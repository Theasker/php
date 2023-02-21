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

class LibreTranslate extends TelegramBot {

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file("/config/www/translate/.env"); 
        parent::__construct();
    }

    function translate ($text, $target) {
        $url = $this->ini_array['LIBRETRANSLATE_URL'];
        $data = array(
            'q' => $text,
            'source' => 'auto',
            'target' => $target,
            'format' => 'text',
            'api_key' => ''
        );
        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ),
        );

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $res = json_decode($response);
        //var_dump($res);
        return $res->translatedText;
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
function translate() {
    // $bot = new DeeplBot();
    // $bot->saveLog("prueba");
    // ($token, $msg, $chatid)
    $bot = new LibreTranslate();
    $bot->translate("Hello world", "es");
    //echo $bot->sendText($bot->ini_array['TOKEN'], date('Y-m-d H:i:s')." - prueba", "-797062014");
}

//translate();


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