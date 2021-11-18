<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$_POST['uid'] = trim($_POST['uid']);
if ($_SESSION['role'] !== 'admin' && $_POST['uid'] !== $_SESSION['id']){
    die('{"error":"1","title":"Error!","html":"You cann’t modify other user’s data."}');
}

$user = new Users();

if ($user->deleteUser($_POST['uid'])){
    $output['html'] = '<div class="modal-container">Done!</div>';
    $output['buttons'] = [ 
        [ 'text'=>'Okay', 'className'=>'modal-close modal-reload-page' ]
    ];
}
else{
    $output['html'] = '<div class="modal-container">Not done!:(</div>';
    $output['buttons'] = [ 
        [ 'text'=>'Okay', 'className'=>'modal-close' ]
    ];
}


