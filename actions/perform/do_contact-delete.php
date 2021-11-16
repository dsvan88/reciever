<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

if ($_SESSION['role'] !== 'admin'){
    die('{"error":"1","title":"Error!","html":"You cann’t delete users contacts."}');
}

$user = new Users();

if ($user->deleteContact($_POST['cid'])){
    $output['html'] = '<div class="modal-container">Done!</div>';
    $output['buttons'] = [ 
        0=> [
            'text'=>'Okay',
            'className'=>'modal-close'
        ]
    ];
}
else{
    $output['html'] = '<div class="modal-container">Done!</div>';
}


