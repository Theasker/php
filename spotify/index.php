<?php
// docker run --rm -it -v $(pwd):/music nicolaspotier/spotdl:latest https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT

require_once('../TelegramBot.php');

class SpotifyBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file(".env"); 
        parent::__construct();
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' - '.$text."\n", FILE_APPEND);
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

function spotify() {
    $bot = new spotifyBot();
    // echo $bot->setWebhook($bot->ini_array['TOKEN'], $bot->ini_array['PUBLIC_URL'])."\n";
    // echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    // $bot->sendText($bot->ini_array['TOKEN'], date('Y-m-d H:i:s')." - "."prueba", "-797062014");
    echo shell_exec('ls -lart');
}

spotify();


// Recibir el update de Telegram cuando un usuario introduce algo

$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
    pipedream($update);

    $bot = new SpotifyBot();
    // $bot->sendMedia($nasaDatas->title, $nasaDatas->hdurl);

    // Obtener el mensaje recibido
    $message = $update['message']['text'];
    // Obtener el ID del chat
    $chatId = $update['message']['chat']['id'];
    
    // Grabo el log
    //$url = "https://api.telegram.org/bot".$bot->ini_array['TOKEN']."/sendMessage?chat_id=".$chatId."&text=".urlencode($response);
    // $bot->saveLog("(".$chatId." - ".$update['message']['chat']['title'].") - ".$message);
    // function sendText($token, $msg, $chatid, $silent = false)
    $bot->sendText($bot->ini_array['TOKEN'], date('Y-m-d H:i:s')." - ".$message, $chatId);
}


?>