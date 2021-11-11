<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$user = new Users();

if ($user->deleteUser($_POST['uid'])){
    $output['html'] = '<div class="modal-container">Done!</div>';
    $output['buttons'] = [ 
        0=> [
            'text'=>'Okay',
            'className'=>'modal-close'
        ]
    ];
}
else{
    $output['html'] = '<div class="modal-container">Not done!:(</div>';
    $output['buttons'] = [ 
        0 => [
            'text'=>'Okay',
            'className'=>'modal-close'
        ]
    ];
}


