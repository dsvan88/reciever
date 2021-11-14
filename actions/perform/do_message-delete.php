<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$action = new Messages();

if ($action->deleteMessage($_POST['mid'])){
    $output['text'] = 'Done!';
}
else{
   $output['text'] = 'Not done! :(';
}


