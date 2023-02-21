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

function theasker() {
    $bot = new TheaskerBot();
    $out = $bot->dispatcher("!traducir en hola que tal estas?", "-797062014");
    echo $out;
    
    $bot->sendText($bot->ini_array['TOKEN'], $out, "-797062014");    
}

theasker();


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