<?php
if (count($_POST)===0){
    require_once $_SERVER['DOCUMENT_ROOT'].'/views/init-form.php';
    $template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/main-template.html');
    die(str_replace(array_keys($output),array_values($output),$template));
}
require $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$array = [];

if (isset($_POST['email']) && trim($_POST['email']) !== ''){
    $array['email'] = trim($_POST['email']);
}
if (isset($_POST['email-password']) && trim($_POST['email-password']) !== ''){
    $array['password'] = trim($_POST['email-password']);
}
if (isset($_POST['tg_bot_token']) && trim($_POST['tg_bot_token']) !== ''){
    $array['tg_bot_token'] = trim($_POST['tg_bot_token']);
}

if (count($array) > 0){
    $cryptKey = $action->getCryptKey();
    if ($cryptKey !== false){
        $crypt = new Crypt(['key'=>$cryptKey]);
        foreach($array as $key=>$value){
            $array[$key] = $crypt->encrypt($value);
        }
        $action->rowUpdate($array, ['id'=> 1], TABLE_AUTH);
    }
    else{
        $crypt = new Crypt([ 'value' => $array['email'] ]);
        $array['key'] = $crypt->key;
        $action->rowInsert($array, TABLE_AUTH);
    }
}
