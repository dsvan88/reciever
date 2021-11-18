<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$user = new Users();

if ($_SESSION['role'] !== 'admin'){
    die('{"error":"1","title":"Error!","html":"You cann’t delete other users."}');
}

$ids = explode(',', $_POST['ids']);
for ($i=0; $i < count($ids); $i++) { 
    $user->deleteUser($ids[$i]);
}

$output['html'] = '<div class="modal-container">Done!</div>';
        $output['buttons'] = [ 
            [ 'text'=>'Okay', 'className'=>'modal-close modal-reload-page']
        ];