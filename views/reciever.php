<?php
require $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$source = str_replace(['http://','https://', 'www.'],'',$_SERVER['HTTP_ORIGIN']);
if (($pos = strrpos($source,'/')) !== false)
    $source = substr($source, 0, $pos);

$userData = $action->getUsersContacts(['columns' => ['uid','value'], 'conditions' => ['value' => $source] ])[0];

if (isset($userData['value'])){
    header("Access-Control-Allow-Origin: $_SERVER[HTTP_ORIGIN]");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if (strpos($contentType,'application/json') !==  false) {
	$_POST = trim(file_get_contents('php://input'));
	$_POST = json_decode($_POST, true);

	if(!is_array($_POST)){
		error_log(json_encode($_POST,JSON_UNESCAPED_UNICODE));
        die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
    }
}

$array=['name'=>'-','email'=>'-','contact'=>'-','message'=>'-'];
foreach($array as $key=>$value){
    if (isset($_POST["customer-$key"]))
        $array[$key] = urldecode(htmlspecialchars(trim($_POST["customer-$key"])));
}
if ($array['name'] === '-' && $array['email'] === '-' && $array['contact'] === '-' && $array['message'] === '-'){
    die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
}

$array['source']    = $source;
$array['uid']       = $userData['uid'];
$array['time']      = $_SERVER['REQUEST_TIME'];

if ($action->rowInsert($array) > 0){
    $result=[
        'error' => '0',
        'title' => 'Request recieved',
        'text'  => 'Thanks for your request! I will contact you as soon as possible!'
    ];
}
else{
     $result=[
        'error' => '1',
        'title' => 'Reciever not saved',
        'text'  => 'Sorry, something wrong with your request, please, choose other contact method!'
    ];
}

$contacts = $action->getUsersContacts(['columns' => '*', 'conditions' => ['uid' => $userData['uid']]]);
if ($contacts === 0)
    exit(json_encode($result,JSON_UNESCAPED_UNICODE));

require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.crypt.php';

$cryptKey = $action->getColumn($action->query('SELECT key FROM '.TABLE_AUTH.' WHERE id != 0 LIMIT 1'));

$crypt = new Crypt(['key'=>$cryptKey]);

if (CFG_EMAIL){
    
    require $_SERVER['DOCUMENT_ROOT'].'/engine/class.mailer.php';
    $emails = [];

    foreach($contacts as $row=>$column){
        if ($column['type'] !== 'email') continue;
        $emails[] = $crypt->decrypt($column['value']);
    }
    if (count($emails) > 0){
        $mailer = new Mailer($array);
        try {
            $result['text'] .= $mailer->send($emails) ? '<div>Email send.</div>' : '<div>Email not send!</div>';
        }
        catch (Exception $e) {
            $result['text'] .= "<div>Email not send! Error: {$mailer->ErrorInfo}</div>";
            if (isset($GLOBALS['status']))
                error_log(json_encode($GLOBALS['status'],JSON_UNESCAPED_UNICODE));
        }
    }    
}
if (CFG_BOTS){
    require $_SERVER['DOCUMENT_ROOT'].'/engine/class.bots.php';

    $tg_uIDs = [];
    foreach($contacts as $row=>$column){
        if ($column['type'] !== 'tg_uid') continue;
        $tg_uIDs[] = $crypt->decrypt($column['value']);
    }
    if (count($tg_uIDs) > 0){
        $bot = new MessageBot();
        $bot->prepMessage($array);    

        try {
            $messageSend = json_decode($bot->sendToTelegramBot($tg_uIDs), true);
            $result['text'] .=  $messageSend['ok'] ? '<div>Message to telegram - send.</div>' : "<div>Message to telegram - not send ($messageSend[description])!</div>";
        }
        catch (Exception $e) {
            $result['text'] .= "<div>Message not send!</div>";
        }
    }
}
echo json_encode($result,JSON_UNESCAPED_UNICODE);