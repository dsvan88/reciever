<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$_POST['uid'] = trim($_POST['uid']);
if ($_SESSION['role'] !== 'admin' && $_POST['uid'] !== $_SESSION['id']){
    die('{"error":"1","title":"Error!","html":"You cann’t modify other user’s passwords."}');
}

$result = $action->resetUserPassword($_POST['uid']);

if (is_string($result)){

    $output['text'] = 'Done!';

    $email = '';

    $contacts = $action->getUsersContacts(['uid' => $_POST['uid']]);

    foreach($contacts as $row=>$column){
        if ($column['type'] === 'email'){
            $email = $column['value'];
            break;
        }
    }

    if ($email !== ''){

        require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.mailer.php';

        $mailer = new Mailer();

        $mailer->prepMessage([
            'title' => 'Request for password reset',
            'body'  => "<h2>New request</h2>
                        <b>Your new temp password:</b> $result<br>
                        Please, change it as soon as possible!"
        ]);
        
        try {
            $output['text'] .= $mailer->send($email) ? "\r\nEmail send to $email." : 'Email not send!';
        }
        catch (Exception $e) {
            $output['text'] .= "Email not send! Error: {$mailer->ErrorInfo}";
            if (isset($GLOBALS['status']))
                error_log(json_encode($GLOBALS['status'],JSON_UNESCAPED_UNICODE));
        }
    }
    else{
        $output['text'] .= "Email not found! Temp password: $result";
    }
}
else {
    $output['error'] = 1;
    $output['text'] = $result;
}

