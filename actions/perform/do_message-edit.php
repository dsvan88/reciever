<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.messages.php';

$_POST['mid'] = trim($_POST['mid']);

$action = new Messages();

$criteria = ['id'=>$_POST['mid']];

if($_SESSION['role'] !== 'admin')
    $criteria['uid'] = $_SESSION['id'];

if (!$action->recordExists($criteria,TABLE_MAIN,'AND')){
    die('{"error":"1","title":"Error!","html":"Cann’t find message with id: '.$_POST['mid'].'"}');
}

$array=['name'=>'-','email'=>'-','contact'=>'-','message'=>'-'];
foreach($array as $key=>$value){
    if (isset($_POST["customer-$key"]))
        $array[$key] = htmlspecialchars(trim($_POST["customer-$key"]));
}
$action->rowUpdate($array,['id'=>$_POST['mid']]);

if (count($_POST['notes']) > 0){

    require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.notes.php';
    
    $notesAction = new Notes();
    $notesAll = $notesAction->getNotes($_POST['mid']);
    $notesAdd = [];
    if (count($notesAll) === 0){
        for ($i=0; $i < count($_POST['notes']); $i++) { 
            if (trim($_POST['notes'][$i]) == '') continue;
            $notesAdd[] = [
                'mid' => $_POST['mid'],
                'uid' => $_SESSION['id'],
                'text' => $_POST['notes'][$i],
                'time' => time()
            ];
        }
        $notesAction->addNote($notesAdd);
    }
    else{
        for ($i=0; $i < count($notesAll); $i++) { 
            if (array_search($notesAll[$i]['text'], $_POST['notes'], true) === false){
                $notesDelete[] = $notesAll[$i]['id'];
            }
            $notes[$notesAll[$i]['id']] = $notesAll[$i]['text'];
        }
        for ($i=0; $i < count($_POST['notes']); $i++) { 
            if (trim($_POST['notes'][$i]) == '') continue;
            if (array_search($_POST['notes'][$i], $notes, true) === false){
                $notesAdd[] = [
                    'mid' => $_POST['mid'],
                    'uid' => $_SESSION['id'],
                    'text' => $_POST['notes'][$i],
                    'time' => time()
                ];
            }
        }

        if (count($notesAdd) > 0)
            $notesAction->addNote($notesAdd);

        for ($i=0; $i < count($notesDelete); $i++) { 
            $notesAction->deleteNote($notesDelete[$i]);
        }
    }
}
$output['text'] = 'Done!';