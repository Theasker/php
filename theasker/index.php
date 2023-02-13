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
            <code>!comando</code>: Ejecuta comando en el sistema operativo
            <code>!wol</code>: Inicia el ordenador de casa
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
            $command = "ssh pi@casa.theasker.ovh sudo etherwake -i eth0 00:23:7D:07:64:DD";
            $out = shell_exec($command);
        }else if (str_starts_with($message, '!spotify')) {
            
            $out = shell_exec($command);
        }
        else if (str_starts_with($message, '!help')) {
            $out = $this->help();
        }else $out = "Comando \"<code>$message</code>\" => No hago nada";
        return $out;
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

function run() {
    $bot = new TheaskerBot();
        
    //echo $bot->deleteWebhook($bot->ini_array['TOKEN']);
    //echo $bot->getUpdates($bot->ini_array['TOKEN']);   
    // echo $bot->setWebhook($bot->ini_array['TOKEN'], $bot->ini_array['PUBLIC_URL'])."\n";
    //echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    // ($token, $msg, $chatid, $silent = false
    // echo $bot->sendText($bot->ini_array['TOKEN'], "prueba", "-797062014");
    
}

//run();


// Recibir el update de Telegram cuando un usuario introduce algo
$update = json_decode(file_get_contents('php://input'), true);

if (isset($update['message']['text'])) {
    //pipedream($update);
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