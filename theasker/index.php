<?php
// docker run --rm -it -v $(pwd):/music nicolaspotier/spotdl:latest https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT

require_once('/config/www/TelegramBot.php');

class TheaskerBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file("/config/www/theasker/.env"); 
        parent::__construct();
        //echo "clase hija\n";
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' '.$text."\n", FILE_APPEND);
    }

    private function cleanMessage($message, $search){
        $cleanMessage = trim(str_replace($search, "", $message));
        return $cleanMessage;       
    }

    private function help(){
        $str = <<<'EOD'
        <u><b>Comandos a ejecutar con este bot</b></u>
            <code>!help</code>: Muestra esta ayuda
            <code>!comando <comando></code>: Ejecuta comando en el sistema operativo
            <code>!wol</code>: Inicia el ordenador de casa
            <code>!traducir (es|en) <texto></code>: Traduce un texto de Inglés-español o español-inglés.
            <code>!clonewars</code>: Nos escribe una frase de Clone Wars aleatoria.
        EOD;
        return $str;
    }
 
    public function dispatcher($message, $chatId) {
        $out = '';
        if (str_starts_with($message, '!comando')) {
            $command = $this->cleanMessage($message, '!comando');
            if (!empty($command)) {
                $out = shell_exec($command);
            }else $out = "Comando vacío\n";
        }else if (str_starts_with($message, '!wol')) {
            // $command = "ssh root@casa.theasker.ovh etherwake -i eth0 00:23:7D:07:64:DD";
            $command = "ssh pi@casa.theasker.ovh sudo /mnt/datos/scripts/bin/wol.sh";
            $out = shell_exec($command);
            $out = "Se ha enviado la solicitud de WOL ...\n<code>sudo etherwake -i eth0 00:23:7D:07:64:DD</code>\n<code>$out</code>\n";
        }else if (str_starts_with($message, '!spotify')) {
            $out = shell_exec($command);
            // TODO
        }else if (str_starts_with($message, '!help')) {
            $out = $this->help();
        }else if (str_starts_with($message, '!traducir')) {
            $command = $this->cleanMessage($message, '!traducir');
            // Ver a qué idioma (es,en) tenemos que traducir
            $language = explode(" ",$command);
            if ($language[0] == 'en' or $language[0] == 'es') {
                require_once('/config/www/translate/index.php');
                $botTranslate = new LibreTranslate();
                // Quito el idioma del texto
                $text = $this->cleanMessage($command, $language[0]);
                $out = $botTranslate->translate($text, $language[0]);
            }else {
                $out = "Idioma seleccionado no es correcto";
            }
        }else if (str_starts_with($message, '!clonewars')) {
            $out = $this->cloneWarsPhrase();           
        }
        else $out = "Comando \"<code>$message</code>\" => No hago nada";
        return $out;
    }

    public function cloneWarsPhrase() {
        $lines = file($this->ini_array['CLONEWARSFILE']); // Lee el archivo y guarda cada línea en un array
        $phrase = explode(' - ', $lines[rand(0,count($lines))]);
        //var_dump("$phrase[0] - $phrase[1]:\n $phrase[2]");
        $text = str_replace("\n","",$phrase[2]);
        return "<b><i>\"$text\"</i></b>";
    }

    public function youtubeCheckChannels() {
        // Replace with your own API key
        $api_key = $this->ini_array['YOUTUBEAPIKEY'];
        
        foreach ($this->ini_array['YOUTUBECHANNELS'] as $key => $value) {
            // Crea la URL de la API de YouTube Data para buscar el canal
            
            // Channel id from youtube video url
            //$array_url = explode("=",$value);
            //$video_id = $array_url[1];
            //$api_url = "https://youtube.googleapis.com/youtube/v3/videos?part=snippet&id={$video_id}&key={$api_key}";
            ///////////////////////////////////

            // Channel id from youtube custom channel url
            $array_url = explode("=",$value);
            $channel_name = $array_url[count($array_url)-1];
            $api_url = "https://www.googleapis.com/youtube/v3/search?part=id%2Csnippet&q=$channel_name&type=channel&key={$api_key}";
            

            // Haz una llamada a la API y obtén la respuesta JSON           
            $response = file_get_contents($api_url);           
            $json = json_decode($response);
                 
            // Extrae el ID del canal de la respuesta JSON
            $channel_id = $json->items[0]->snippet->channelId;

            // Call the YouTube Data API to get the latest video from the channel
            $api_url = "https://www.googleapis.com/youtube/v3/search?key={$api_key}&channelId={$channel_id}&part=snippet,id&order=date&maxResults=1";
            $response = file_get_contents($api_url);
            $json = json_decode($response);

            // Extract the video ID and title from the response
            $video_id = $json->items[0]->id->videoId;
            $video_title = $json->items[0]->snippet->title;

            // Check if the video ID has changed since the last check
            $last_video_id = file_get_contents('./last_video_id.txt');
            if ($video_id != $last_video_id) {
                // Download the video using the ID and title
                $video_url = "https://www.youtube.com/watch?v={$video_id}";
                $video_path = "./videos/{$video_title}.mp4";
                file_put_contents($video_path, file_get_contents($video_url));
                
                // Save the new video ID for the next check
                file_put_contents('./last_video_id.txt', $video_id);
                echo 'New video downloaded: ' . $video_title . " ( $video_url )";
            } else {
                echo 'No new videos found';
            }
            

        }
        



    }

    public function getYoutubeVideo($youtubeURL) {
        // Load and initialize downloader class 
        include_once 'YouTubeDownloader.class.php'; 
        $handler = new YouTubeDownloader(); 
        
        // Youtube video url 
        //$youtubeURL = 'https://www.youtube.com/watch?v=f7wcKoEbUSA'; 
        
        // Check whether the url is valid 
        if(!empty($youtubeURL) && !filter_var($youtubeURL, FILTER_VALIDATE_URL) === false){ 
            // Get the downloader object 
            $downloader = $handler->getDownloader($youtubeURL); 
            
            // Set the url 
            $downloader->setUrl($youtubeURL); 
            
            // Validate the youtube video url 
            if($downloader->hasVideo()){ 
                // Get the video download link info 
                $videoDownloadLink = $downloader->getVideoDownloadLink(); 
                
                $videoTitle = $videoDownloadLink[0]['title']; 
                $videoQuality = $videoDownloadLink[0]['qualityLabel']; 
                $videoFormat = $videoDownloadLink[0]['format']; 
                $videoFileName = strtolower(str_replace(' ', '_', $videoTitle)).'.'.$videoFormat; 
                $downloadURL = $videoDownloadLink[0]['url']; 
                $fileName = preg_replace('/[^A-Za-z0-9.\_\-]/', '', basename($videoFileName)); 
                
                if(!empty($downloadURL)){ 
                    // Define header for force download 
                    header("Cache-Control: public"); 
                    header("Content-Description: File Transfer"); 
                    header("Content-Disposition: attachment; filename=$fileName"); 
                    header("Content-Type: application/zip"); 
                    header("Content-Transfer-Encoding: binary"); 
                    
                    // Read the file 
                    readfile($downloadURL); 
                } 
            }else{ 
                echo "The video is not found, please check YouTube URL."; 
            } 
        }else{ 
            echo "Please provide valid YouTube URL."; 
        } 
    }
}

function pipedream($update) {
    // pipedream -----------------------------------    
    $handle = curl_init('https://eolmlyay4to9iia.m.pipedream.net');

    $data = [
        'key' => $update
    ];

    $encodedData = json_encode($data);

    curl_setopt($handle, CURLOPT_POST, 1);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $encodedData);
    curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $result = curl_exec($handle);
    /////////////////////////////////////////////////////////
}

function theasker() {
    $bot = new TheaskerBot();
    $out = $bot->dispatcher("!clonewars", "-797062014");
    echo $out;
    // $bot->youtubeCheckChannels();
        
    // $path_file = './videos/Cómo hacer preguntas EFICIENTES en IT.mp4';
    
    // sendMedia($token, $chat_id, $filename, $type, $caption = '')
    // echo $bot->sendMedia($bot->ini_array['TOKEN'], "-797062014", $path_file, 'video');

    // $youtube_video = 'https://www.youtube.com/watch?v=NtKw_jEbfKY';

    // $bot->getYoutubeVideo($youtube_video);
}

theasker();


// Recibir el update de Telegram cuando un usuario introduce algo
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
    pipedream($update);
    $bot = new TheaskerBot();
    $message = $update['message']['text'];
    $chatId = $update['message']['chat']['id'];
    $username = $update['message']['from']['username'];
    $userFirstName = $update['message']['from']['first_name'];
    $userid = $update['message']['from']['id'];
    $out = $bot->dispatcher($message, $chatId);
    
    $bot->sendText($bot->ini_array['TOKEN'], $out, $chatId);
    // Grabo el log
    $msg = "(".$chatId."/".$update['message']['chat']['title'].")";
    $msg = $msg."(".$userFirstName."/".$username."/".$userid."): ".$message;
    //$bot->saveLog($msg);
}
?>