<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

if ($_SESSION['role'] !== 'admin'){
    die('{"error":"1","title":"Error!","html":"You cann’t add other users."}');
}

$user = new Users();
$user->addNewUser($_POST);

$output['text'] = 'Done!';