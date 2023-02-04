<?php
// https://www.xibel-it.eu/debug-telegram-bot-sdk-with-webhook-in-laravel/

require_once('../TelegramBot.php');

class DeeplBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file(".env"); 
        parent::__construct();
    }

    public function getNASApictureday() {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api-free.deepl.com/v2/translate",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => [
                "text" => "Hello, world!",
                "target_lang" => "DE"
            ],
            /*CURLOPT_HTTPHEADER => [
                "Authorization: DeepL-Auth-Key 0308710a-e9e4-d203-71f2-61075dff0361:fx"
            ],*/
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
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

function deepl() {
    $bot = new DeeplBot();
    
}

deepl();


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