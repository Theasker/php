<?php
// https://www.xibel-it.eu/debug-telegram-bot-sdk-with-webhook-in-laravel/
class TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file(".env");
    }

    public function setWebhook() {
        $url = $this->ini_array['URL'].$this->ini_array['TOKEN'].'/setWebhook?url='.$this->ini_array['PUBLIC_URL'];

        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function deleteWebhook() {
        $url = $this->ini_array['URL'].$this->ini_array['TOKEN'].'/deleteWebhook';
        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function getWebhookInfo() {
        $url = $this->ini_array['URL'].$this->ini_array['TOKEN'].'/getWebhookInfo';
        
        // Initialize a CURL session.
        $curl = curl_init();
        
        // Return Page contents.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        // Grab URL and pass it to the variable
        curl_setopt($curl, CURLOPT_URL, $url);
        
        $result = curl_exec($curl);
        return $result;
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' - '.$text."\n", FILE_APPEND);
    }

    function sendText($msg, $chatid = '-690607908', $silent = false) {
        // $url = "https://api.telegram.org/bot".$bot->ini_array['TOKEN']."/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($response);
        $data = array(
            'chat_id' => $chatid,
            'text' => $msg,
            'parse_mode' => 'html',
            'disable_web_page_preview' => true,
            'disable_notification' => $silent
        );
        $url = 'https://api.telegram.org/bot'.$this->ini_array['TOKEN'].'/sendMessage';
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

    public function get_nasa_pictureday() {
        $url = $this->ini_array['NASA_URL'].$this->ini_array['NASA_APIKEY'];
    }

    public function pruebas() {
        //var_dump($this->ini_array);

    }
}

$bot = new TelegramBot();
// $bot->setWebhook();
// echo $bot->getWebhookInfo();

// Recibir el update de Telegram
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
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


    // Obtener el mensaje recibido
    $message = $update['message']['text'];
    // Obtener el ID del chat
    $chatId = $update['message']['chat']['id'];
    
    // Grabo el log
    $url = "https://api.telegram.org/bot".$bot->ini_array['TOKEN']."/sendMessage?chat_id=".$chatId."&text=".urlencode($response);
    $bot->saveLog("(".$chatId." - ".$update['message']['chat']['title'].") - ".$message);
    $bot->sendText(date('Y-m-d H:i:s')." - ".$message, $chatId);
}
?>