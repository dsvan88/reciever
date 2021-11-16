<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$_POST['mid'] = trim($_POST['mid']);

$action = new Messages();
$messageData = $action->getMessageData([ 'id'=>$_POST['mid'] ]);

if ($messageData !== null){
    
    if ($_SESSION['role'] !== 'admin' && $messageData['uid'] !== $_SESSION['id']){
        die('{"error":"1","title":"Error!","html":"You cann’t edit other user’s messages."}');
    }
    
    $replaceData = [
        '{{MessageId}}' => $_POST['mid'],
        '{{CustomerName}}' => $messageData['name'],
        '{{CustomerEmail}}' => $messageData['email'],
        '{{CustomerContact}}' => $messageData['contact'],
        '{{CustomerMessage}}' => $messageData['message'],
        '{{MasterNotices}}' => '
            <a data-action="add-form-field"><i class="fa fa-plus-circle"></i></a>'
    ];

    require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.notes.php';

    $notesAction = new Notes();
    $notesCount = $notesAction->getNotesCount($_POST['mid']);
    
    if ($notesCount){
        $notes = $notesAction->getNotes($_POST['mid']);
        $notesHtml = '';
        for ($i=0; $i < count($notes); $i++) { 
            $notesHtml .= "<input class='common-form__input' type='text' name='notes[]' placeholder='Note (comments)' value='{$notes[$i]['text']}'>";
        }
        $replaceData['{{MasterNotices}}'] = $notesHtml.$replaceData['{{MasterNotices}}'];
    }
    else{
        $replaceData['{{MasterNotices}}'] = '<input class="common-form__input" type="text" name="notes[]" placeholder="Note (comments)">'.$replaceData['{{MasterNotices}}'];
    }

    $output['title'] = 'Edit message Form';
    $output['html'] = str_replace(array_keys($replaceData), array_values($replaceData), file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/form_message-edit.html'));
}
else {
    $output['error'] = '1';
    $output['html'] = "Message with id: $_POST[mid] - not found!";
}