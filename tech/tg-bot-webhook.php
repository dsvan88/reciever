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
/* $array=[
    'name'=>$_POST['message']['from']['first_name']."(id:${$_POST['message']['from']['id']})",
    'email'=> '-',
    'contact'=>$_POST['message']['chat']['first_name']."(id:${$_POST['chat']['from']['id']})",
    'message'=> $_POST['message']['text'],
    'uid' => '',
    'source' => 'TelegramBot',
    'time' => $_POST['message']['date'],
];

require $_SERVER['DOCUMENT_ROOT'].'/engine/class.action.php';

$action = new Action();

$action->rowInsert($array); */