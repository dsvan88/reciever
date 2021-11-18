<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$_POST['uid'] = trim($_POST['uid']);
if ($_SESSION['role'] !== 'admin' && $_POST['uid'] != $_SESSION['id']){
    die('{"error":"1","title":"Error!","html":"You cann’t modify other user’s data."}');
}

$data['login'] = strtolower(htmlspecialchars($_POST['login']));

if (!$action->recordExists(['login' => $data['login']],TABLE_USERS))
    $action->rowUpdate(['login' => $data['login']], ['id'=>$_POST['uid']], TABLE_USERS);

$userContacts = $action->getUsersContacts(['uid'=>$_POST['uid']]);

$contacts = $contactsDelete = $contactsAdd = [];

for ($i=0; $i < count($userContacts); $i++) { 
    if (array_search($userContacts[$i]['value'], $_POST[$userContacts[$i]['type']], true) === false){
        $contactsDelete[] = $userContacts[$i]['id'];
    }
    $contacts[$userContacts[$i]['id']] = $userContacts[$i]['value'];
}

$key = $action->getCryptKey();
$crypt = new Crypt(['key'=>$key]);

for ($i=0; $i < count($_POST['website']); $i++) { 
    if (trim($_POST['website'][$i]) == '') continue;
    if (array_search($_POST['website'][$i], $contacts, true) === false){
        $contactsAdd[] = [
            'uid' => $_POST['uid'],
            'type' => 'website',
            'value' => $_POST['website'][$i]
        ];
    }
}
for ($i=0; $i < count($_POST['email']); $i++) {
    if (trim($_POST['email'][$i]) == '') continue;
    if (array_search($_POST['email'][$i], $contacts, true) === false){
        $contactsAdd[] = [
            'uid' => $_POST['uid'],
            'type' => 'email',
            'value' => $crypt->encrypt($_POST['email'][$i])
        ];
    }
}
for ($i=0; $i < count($_POST['tg_uid']); $i++) {
    if (trim($_POST['tg_uid'][$i]) == '') continue;
    if (array_search($_POST['tg_uid'][$i], $contacts, true) === false){
        $contactsAdd[] = [
            'uid' => $_POST['uid'],
            'type' => 'tg_uid',
            'value' => $crypt->encrypt($_POST['tg_uid'][$i])
        ];
    }
}

if (count($contactsAdd) > 0)
    $action->rowInsert($contactsAdd,TABLE_CONTACTS);

for ($i=0; $i < count($contactsDelete); $i++) { 
    $action->deleteContact($contactsDelete[$i]);
}

$output['text'] = 'Done!';