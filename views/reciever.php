<?php
header('Access-Control-Allow-Origin: *');//$_SERVER[HTTP_ORIGIN]
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit();

require $_SERVER['DOCUMENT_ROOT'].'/engine/action.class.php';

$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if (strpos($contentType,'application/json') !==  false) {
	$_POST = trim(file_get_contents('php://input'));
	$_POST = json_decode($_POST, true);

	if(!is_array($_POST)){
		error_log(json_encode($_POST,JSON_UNESCAPED_UNICODE));
        die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
    }
}


$action = new Action();
$array=['name'=>'','email'=>'','contact'=>'','message'=>''];
foreach($array as $key=>$value){
    if (isset($_POST["customer-$key"]))
        $array[$key] = $_POST["customer-$key"];
}
if ($array['name'] === '' && $array['email'] === '' && $array['contact'] === '' && $array['message'] === ''){
    die('{"error":"1","title":"Error!","text":"Error: Nothing to send."}');
}
$array['time'] = $_SERVER['REQUEST_TIME'];
if ($action->rowInsert($array) > 0){
    $result=[
        'error' => '0',
        'title' => 'Request recieved',
        'text'  => 'Thanks for your request! I will contact you as soon as possible!'
    ];
    echo json_encode($result,JSON_UNESCAPED_UNICODE);
}
$action->SQLClose();
unset($action);