<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$user = new Users();

$ids = explode(',', $_POST['ids']);
for ($i=0; $i < count($ids); $i++) { 
    $user->deleteUser($ids[$i]);
}

$output['html'] = '<div class="modal-container">Done!</div>';
        $output['buttons'] = [ 
            0=> [
                'text'=>'Okay',
                'className'=>'modal-close'
            ]
        ];