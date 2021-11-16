<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$_POST['mid'] = trim($_POST['mid']);

$action = new Messages();
$criteria = ['id'=>$_POST['mid']];

if($_SESSION['role'] !== 'admin')
    $criteria['uid'] = $_SESSION['id'];

if ($action->recordExists(['id'=>$_POST['mid']],TABLE_MAIN,'AND')){
    $action->setMessageArchive($_POST['mid']);
    $output['text'] = 'Done!';
}
else{
    $output['error'] = '1';
    $output['text'] = 'Cann’t find message with id: '.$_POST['mid'];
}
