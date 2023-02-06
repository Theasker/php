<?php
// docker run --rm -it -v $(pwd):/music nicolaspotier/spotdl:latest https://open.spotify.com/track/4cOdK2wGLETKBW3PvgPWqT

require_once('../TelegramBot.php');

class TheaskerBot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file(".env"); 
        parent::__construct();
        //echo "clase hija\n";
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' '.$text."\n", FILE_APPEND);
    }

    public function dispatcher($message) {
        if (str_contains($message, 'comando')) {
            $this->saveLog("==prueba==");
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

function run() {
    //$bot = new TheaskerBot();
    echo "\n".strcmp("dadfasd", "a")."\n";
    $message = "!mensaje introducido";
    $buscar = "!prueba";
    // substr(string $string, int $start, int $length = ?)
    if ($su0) {
        echo "Iguales\n";
    }else {
        echo "No iguales\n";
    }
    
    // echo $bot->deleteWebhook($bot->ini_array['TOKEN']);
    //echo $bot->getUpdates($bot->ini_array['TOKEN']);
    
    // echo $bot->setWebhook($bot->ini_array['TOKEN'], $bot->ini_array['PUBLIC_URL'])."\n";
    // echo $bot->getWebhookInfo($bot->ini_array['TOKEN'])."\n";
    // ($token, $msg, $chatid, $silent = false
    // echo $bot->sendText($bot->ini_array['TOKEN'], "prueba", "-797062014");
    /* $token = $bot->ini_array['TOKEN'];
    var_dump('-690607908');
    echo $bot->sendText($token, 'prueba', '-690607908'); */
    // echo shell_exec('ls -lart');
    // grupo_pruebas=-797062014, notificaciones=-690607908, bot_id=8310736
}

run();


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
    $bot->dispatcher($message);
    // Grabo el log
    $msg = "(".$chatId."/".$update['message']['chat']['title'].")";
    $msg = $msg."(".$userFirstName."/".$username."/".$userid."): ".$message;
    $bot->saveLog($msg);
}


?>