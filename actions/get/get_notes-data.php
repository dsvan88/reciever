<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.notes.php';

$_POST['mid'] = trim($_POST['mid']);

$action = new Notes();
$output['html'] = '';
$notes = $action->getNotes($_POST['mid']);

if (count($notes) > 0){
    $users = [];
    for ($i=0; $i < count($notes); $i++) {
        $notes[$i]['time'] = date('d.m.Y H:i:s', $notes[$i]['time']);
        $output['html'] .= "
            <div class='messages__notes-item'>
                <div class='messages__notes-tech-data'>
                    <div class='messages__notes-author'>
                        Note:
                    </div>
                    <div class='messages__notes-time'>
                        {$notes[$i]['time']}
                    </div>
                </div>
                <div class='messages__notes-content'>
                    {$notes[$i]['text']}
                </div>
            </div>";
    }
}
else{
    $output['error'] = '1';
    $output['text'] = 'Cann’t find message with id: '.$_POST['mid'];
}
