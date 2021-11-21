<?

$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
if (strpos($contentType,'application/json') !==  false) {
	$_POST = trim(file_get_contents('php://input'));
	$_POST = json_decode($_POST, true);

	if(!is_array($_POST)){
		error_log(json_encode($_POST,JSON_UNESCAPED_UNICODE));
        die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
    }
}

file_put_contents($_SERVER['DOCUMENT_ROOT'].'/tg_bot-message.txt',print_r($_POST, true));
$array=[
    'name'=>"{$_POST['message']['from']['first_name']} {$_POST['message']['from']['last_name']} (@{$_POST['message']['from']['username']}, id:{$_POST['message']['from']['id']})",
    'email'=> '-',
    'contact'=> ($_POST['message']['chat']['type'] === 'private') ? "{$_POST['message']['chat']['first_name']} {$_POST['message']['chat']['last_name']} (@{$_POST['message']['chat']['username']}, id:{$_POST['message']['chat']['id']})" : "{$_POST['message']['chat']['title']} (id:{$_POST['message']['chat']['id']})",
    'message'=> $_POST['message']['text'],
    'uid' => '1',
    'source' => 'TelegramBot',
    'time' => $_POST['message']['date'],
];

require $_SERVER['DOCUMENT_ROOT'].'/engine/class.action.php';

$action = new Action();

$action->rowInsert($array);

require $_SERVER['DOCUMENT_ROOT'].'/engine/class.bots.php';

if (count($tg_uIDs) > 0){
    $bot = new MessageBot();
    $bot->message = "Yes, I hear you! Mr. $array[name]";    

    try {
        $messageSend = json_decode($bot->sendToTelegramBot($_POST['message']['chat']['id']), true);
        $result['text'] .=  $messageSend['ok'] ? '<div>Message to telegram - send.</div>' : "<div>Message to telegram - not send ($messageSend[description])!</div>";
    }
    catch (Exception $e) {
        $result['text'] .= "<div>Message not send!</div>";
    }
}