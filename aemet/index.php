<?php
require_once('/config/www/TelegramBot.php');

class AEMET_Bot extends TelegramBot{

    public $ini_array = [];

    function __construct() {
        $this->ini_array = parse_ini_file("/config/www/aemet/.env"); 
        parent::__construct();
    }

    public function saveLog($text) {
        file_put_contents("messages.log", date('Y-m-d H:i:s').' - '.$text."\n", FILE_APPEND);
    }

    public function municipios(){
        $url_municipios = '/valores/climatologicos/inventarioestaciones/todasestaciones';
        $json = $this->llamada($url_municipios);
        $res = json_decode($json, true);
        $this->datos($res['datos']);
    }

    private function llamada($endpoint) {
        $curl = curl_init();
        $apikey = $this->ini_array['AEMET_APIKEY'];
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->ini_array['AEMET_URL']."$endpoint/?api_key=$apikey",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache"
            ),
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    private function datos($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status == 200) {
            $data = json_decode($response, true);
            print_r($data);
        } else {
            echo 'Error en la llamada a la API';
        }
    }
}
 
function run() {
    // -1001507585258 => chatid de familia
    // '-797062014' => chatid de pruebas
    $bot = new AEMET_Bot();
    // echo $bot->deleteWebhook($bot->ini_array['TOKEN']);
    //echo $bot->getUpdates($bot->ini_array['TOKEN']);
    echo($bot->ini_array['AEMET_APIKEY'])."\n";
    echo $bot->municipios();
}
run();
?>