<?php
// https://www.xibel-it.eu/debug-telegram-bot-sdk-with-webhook-in-laravel/
/*
curl -X POST 'https://api-free.deepl.com/v2/translate' \
	-H 'Authorization: DeepL-Auth-Key 0308710a-e9e4-d203-71f2-61075dff0361:fx' \
	-d 'text=Hello%2C%20world!' \
	-d 'target_lang=DE'
*/

require_once('/config/www/TelegramBot.php');

class DeeplBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file("/config/www/translate/.env"); 
        parent::__construct();
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' '.$text."\n", FILE_APPEND);
    }

    public function translate($text) {
        $curl = curl_init();
        // $curlOptions = 
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ini_array['DEEPL_URL'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            //CURLOPT_POSTFIELDS => "text=Hello%2C%20world!&target_lang=DE",
            CURLOPT_POSTFIELDS => "text=$text&target_lang=ES",
            CURLOPT_HTTPHEADER => array(
                "Authorization: DeepL-Auth-Key 0308710a-e9e4-d203-71f2-61075dff0361:fx"
        ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response);
        return $res->translations[0]->text;
    }
}
/* 
function pipedream($update) {
    // pipedream -----------------------------------    
    $handle = curl_init('https://eolmlyay4to9iia.m.pipedream.net');

    $data = [
        'out' => $update
    ];

    $encodedData = json_encode($data);

    curl_setopt($handle, CURLOPT_POST, 1);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $encodedData);
    curl_setopt($handle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $result = curl_exec($handle);
    /////////////////////////////////////////////////////////
}
 */
function run() {
    $bot = new DeeplBot();
    $bot->saveLog("prueba");
    // ($token, $msg, $chatid)
    //echo $bot->deleteWebhook($bot->ini_array['TOKEN']);
    //echo $bot->getUpdates($bot->ini_array['TOKEN']);
    //echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    
    //echo $bot->setWebhook($bot->ini_array['TOKEN'], $bot->ini_array['PUBLIC_URL'])."\n";
    //echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    echo $bot->translate("Hello world");
    //echo $bot->sendText($bot->ini_array['TOKEN'], date('Y-m-d H:i:s')." - prueba", "-797062014");
}

// run();


// Recibir el update de Telegram cuando un usuario introduce algo
/* 
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
    // pipedream($update);
    try {
        $bot = new DeeplBot();
        // $bot->sendMedia($nasaDatas->title, $nasaDatas->hdurl);

        // Obtener el mensaje recibido
        $message = $update['message']['text'];
        // Obtener el ID del chat
        $chatId = $update['message']['chat']['id'];
        
        // Grabo el log
        //$url = "https://api.telegram.org/bot".$bot->ini_array['TOKEN']."/sendMessage?chat_id=".$chatId."&text=".urlencode($response);
        $bot->saveLog("prueba2");
        $out = $bot->saveLog("(".$chatId." - ".$update['message']['chat']['title'].") - ".$message);
        pipedream($out);
        $bot->sendText(date('Y-m-d H:i:s')." - ".$message, $chatId);
    } catch (Exception $e) {
        $bot->saveLog( $e->getMessage());
    }
}
 */

?>