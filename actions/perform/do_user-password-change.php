<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$_POST['uid'] = trim($_POST['uid']);
if ($_SESSION['role'] !== 'admin' && $_POST['uid'] !== $_SESSION['id']){
    die('{"error":"1","title":"Error!","html":"You cann’t modify other user’s passwords."}');
}
$array = [
    'uid' => $_POST['uid'],
    'old_password' => sha1(trim($_POST['old_password'])),
    'password' => sha1(trim($_POST['new_password']))
];
if ($array['password'] !== sha1(trim($_POST['new_password_check']))){
    die('{"error":"1","title":"Error!","text":"New passwords must match!"}');
}
$result = $action->changeUserPassword($array);

if ($result === true){
    $output['text'] = 'Done!';
}
else {
    $output['error'] = 1;
    $output['text'] = $result;
}

