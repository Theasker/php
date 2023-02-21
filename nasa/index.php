<?php
// https://www.xibel-it.eu/debug-telegram-bot-sdk-with-webhook-in-laravel/

require_once('/config/www/TelegramBot.php');
require_once('/config/www/translate/index.php');

class NasaBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file("/config/www/nasa/.env"); 
        parent::__construct();
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' - '.$text."\n", FILE_APPEND);
    }

    public function getNASApictureday() {
        $url = $this->ini_array['NASA_URL'].$this->ini_array['NASA_APIKEY'];
        $json = file_get_contents($url);
        //echo $json;
        $data = json_decode($json);
        // object fields: date, explanation, hdurl, media_type, service_version, title, url
        return $data;
    }
}

/* 
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
 */
function nasa() {
    // -1001507585258 => chatid de familia
    // '-797062014' => chatid de pruebas
    $bot = new NasaBot();
    // echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    $token = $bot->ini_array['TOKEN'];
    $nasaDatas = $bot->getNASApictureday();
    // $msg = "<b>$nasaDatas->title ($nasaDatas->date)</b></br>\n$nasaDatas->explanation\n";
    // Traducción del titulo =======================================
    $bottranslate = new LibreTranslate();
    
    $translate = $bottranslate->translate($nasaDatas->title, "es");
    //$msg = "<u><b>$nasaDatas->title ($nasaDatas->date) :</b></u>";
    // Mensaje del título con la fecha
    $msg = "<u><b>$translate</b></u> ($nasaDatas->date):";
    //$msg = "<u><b>$nasaDatas->title</b></u> ($nasaDatas->date):";
   
    $bot->sendPhoto($bot->ini_array['TOKEN'], '-1001507585258', $nasaDatas->hdurl, $msg); 
    // Envío de la traducción de la descripción ===============
    $translate = $bottranslate->translate($nasaDatas->explanation, "es");
    // echo $translate;
    $bot->sendText($bot->ini_array['TOKEN'], $translate, '-1001507585258');
    // =======================================================
}

nasa();


// Recibir el update de Telegram cuando un usuario introduce algo
/*
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
    pipedream($update);

    $bot = new NasaBot();
    // $bot->sendMedia($nasaDatas->title, $nasaDatas->hdurl);

    // Obtener el mensaje recibido
    $message = $update['message']['text'];
    // Obtener el ID del chat
    $chatId = $update['message']['chat']['id'];
    
    // Grabo el log
    //$url = "https://api.telegram.org/bot".$bot->ini_array['TOKEN']."/sendMessage?chat_id=".$chatId."&text=".urlencode($response);
    $bot->saveLog("(".$chatId." - ".$update['message']['chat']['title'].") - ".$message);
    $bot->sendText(date('Y-m-d H:i:s')." - ".$message, $chatId);
}
*/

?>