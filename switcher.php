<?php
$contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';

if (strpos($contentType,'application/json') !==  false) {
	$_POST = trim(file_get_contents('php://input'));
	$_POST = json_decode($_POST, true);

	if(!is_array($_POST)){
		error_log(json_encode($_POST,JSON_UNESCAPED_UNICODE));
        die('{"error":"1","title":"Error!","html":"Error: Nothing to send."}');
    }
}

$need = trim(isset($_GET['need']) ? $_GET['need'] : $_POST['need']);

if ($need==='') 
    die('{"error":"1","title":"Error!","html":"Wrong `need` type."}');

$output['error'] = 0;

try{
    if (strpos($need,'get_') !== false){
        require "$_SERVER[DOCUMENT_ROOT]/actions/get/$need.php";
    }
    elseif (strpos($need,'do_') !== false){
        require "$_SERVER[DOCUMENT_ROOT]/actions/perform/$need.php";
    }
    elseif (strpos($need,'form_') !== false){
        require "$_SERVER[DOCUMENT_ROOT]/actions/forms/$need.php";
    }
} catch (Throwable $th) {
    $output['error'] = 1;
    $output['html'] = "Error with '$need': ".$th->getFile().':'.$th->getLine().";\r\nMessage: ".$th->getMessage()."\r\nTrace: ".$th->getTraceAsString();
}
exit(json_encode($output,JSON_UNESCAPED_UNICODE));