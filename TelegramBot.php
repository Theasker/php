<?php
// https://www.xibel-it.eu/debug-telegram-bot-sdk-with-webhook-in-laravel/

class TelegramBot {

    public $url;

    function __construct() {
        $this->url = "https://api.telegram.org/bot";
        // $this->url = "http://172.22.0.2:8081/bot";
        // echo "clase padre\n";
    }

    public function getUpdates($token) {
        $url = $this->url.$token.'/getUpdates';

        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function setWebhook($token, $webhookUrl) {
        $url = $this->url.$token.'/setWebhook?url='.$webhookUrl;

        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function deleteWebhook($token) {
        $url = $this->url.$token.'/deleteWebhook';
        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function getWebhookInfo($token) {
        $url = $this->url.$token.'/getWebhookInfo';
        
        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function getMe($token) {
        $url = $this->url.$token.'/getMe';
        
        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    function sendText($token, $msg, $chatid, $silent = false) {
        // $url = "https://api.telegram.org/bot".$token."/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($response);
        $data = array(
            'chat_id' => $chatid,
            'text' => $msg,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'disable_notification' => $silent
        );
        $url = $this->url.$token.'/sendMessage';
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    function sendMedia($token, $chat_id, $filename, $type = '', $caption = '') {
        $api_url = $this->url.$token."/";
        
        if (strpos($type, 'image') !== false) {
          $method = 'sendPhoto';
          $field = 'photo';
        } elseif (strpos($type, 'audio') !== false) {
          $method = 'sendAudio';
          $field = 'audio';
        } elseif (strpos($type, 'video') !== false) {
          $method = 'sendVideo';
          $field = 'video';
        } else {
          $method = 'sendDocument';
          $field = 'document';
        }
        
        $file = curl_file_create($filename, $type);
        $data = array(
          'chat_id' => $chat_id,
          'caption' => $caption,
          $field => $file
        );
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url.$method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:multipart/form-data'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
         
    // Envía foto de una URL
    function sendPhoto($token, $chatid, $urlphoto, $caption = ""){
        $file = $this->saveFile($urlphoto);
        $photo = new CURLFile(realpath($file));
        // echo "$chatid\n$urlphoto\n$caption";
        $data = array(
            'chat_id' => $chatid,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'html',
        );
        $options = array(
            CURLOPT_URL => "https://api.telegram.org/bot$token/sendPhoto",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data
        );
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        $this->deleteFile($file);
        return $response;
    }

    /*
    function sendMedia($token, $urlphoto, $type = '', $chatid = '-797062014', $caption = "") {
        try {
            $name = $this->saveFile($urlphoto);
            $file = fopen("./tmp/$name", 'rb') or die("Unable to open file!");
            $data = array(
                'chat_id' => $chatid,
                'caption' => $caption
            );
            if ($type == "photo") {
                $method = "sendPhoto";
                $files = array('photo' => $file);
            } elseif ($type == "audio") {
                $method = "sendAudio";
                $files = array('audio' => $file);
            } elseif ($type == "video") {
                $method = "sendVideo";
                $files = array('video' => $file);
            } else {
                $data = array(
                    'chat_id' => $chat_id,
                    'caption' => $caption,
                    'disable_content_type_detection' => false
                );
                $method = "sendDocument";
                $files = array('document' => $file);
            }
            $curl = curl_init($this->url.$token."/".$method);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            fclose($file);
            // return json_decode($response, true);
            return $response;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    */

    /*
    function send_media($token, $chat_id, $filename, $type = '', $caption = '') {
        // intenta abrir el archivo para verificar si existe
        try {
            $file = fopen($filename, 'rb');
            $method = '';
            $data = array(
                'chat_id' => $chat_id,
                'caption' => $caption
            );
            if ($type == "photo") {
                $method = "sendPhoto";
                $files = array('photo' => $file);
            } elseif ($type == "audio") {
                $method = "sendAudio";
                $files = array('audio' => $file);
            } elseif ($type == "video") {
                $method = "sendVideo";
                $files = array('video' => $file);
            } else {
                $data = array(
                    'chat_id' => $chat_id,
                    'caption' => $caption,
                    'disable_content_type_detection' => False
                );
                $method = "sendDocument";
                $files = array('document' => $file);
            }
            echo "method: $method\n";
            echo "url: $this->url\n";
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->url . $method);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
            curl_setopt($curl, CURLOPT_POSTFIELDS, $files);
            $response = curl_exec($curl);
            curl_close($curl);
            //return json_decode($response, true);
            return $response;
        } catch (Exception $e) {
            echo 'Excepción capturada: ', $e->getMessage(), "\n";
        }
    }
    */

    // Graba un fichero de una URL
    private function saveFile($url){
        $file_contents = file_get_contents($url);
        $array_url = explode('/',$url);
        $name = $array_url[count($array_url)-1];
        file_put_contents("/tmp/$name", $file_contents);
        return "/tmp/$name";
    }

    // borra un fichero del sistema
    private function deleteFile($file){
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

// $bot = new TelegramBot();
// $token = "5877626268:AAH20O9i4qYjz_0988SPCvmw0xGgVKy3ZPA";

// echo $bot->sendText($token, "https://apod.nasa.gov/apod/image/2301/TripleCometZTF_Caldera_3574.jpg", "-797062014");
// var_dump ($bot->sendPhoto($token, '-797062014', 'https://apod.nasa.gov/apod/image/2301/TripleCometZTF_Caldera_3574.jpg', "caption"));
// $bot->saveFile('https://apod.nasa.gov/apod/image/2301/TripleCometZTF_Caldera_3574.jpg');


// $public_url = "https://php.theasker.ovh/nasa/";
//echo $bot->setWebhook($token, $public_url);
// echo $bot->deleteWebhook($token);
// echo $bot->getWebhookInfo($token);
// echo $bot->getMe($token);
//echo $bot->deleteWebhook($token)."\n"."\n";
//echo $bot->sendText($token, "<u>esto</u> es una prueba");
?>