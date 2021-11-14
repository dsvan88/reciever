<?php
$output['html'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/form_user-edit.html');
$output['title'] = 'Add new User';

require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();
$userData = $action->getUserData(['conditions' => ['id'=>trim($_POST['uid'])]]);

$replaceData = [
    '{{UserId}}' => $_POST['uid'],
    '{{UserName}}' => $userData['login'],
    '{{UserEmails}}' => '',
    '{{UserTelegramIDs}}' => '',
    '{{UserWebsites}}' => ''
];

$userContacts = $action->getUsersContacts(['conditions' => ['uid' => $_POST['uid']] ]);

for($i=0;$i<count($userContacts);$i++){
    if ($userContacts[$i]['type'] ==='website'){
        $replaceData['{{UserWebsites}}'] .= "<input class='new-user-form__input' type='text' name='website[]' value='{$userContacts[$i]['value']}' placeholder='website.com'/>";
    }
    elseif ($userContacts[$i]['type'] ==='email'){
        $replaceData['{{UserEmails}}'] .= "<input class='new-user-form__input' type='email' name='email[]' value='{$userContacts[$i]['value']}' placeholder='E-mail'/>";
    }
    elseif ($userContacts[$i]['type'] ==='tg_uid'){
        $replaceData['{{UserTelegramIDs}}'] .= "<input class='new-user-form__input' type='text' name='tg_uid[]' value='{$userContacts[$i]['value']}' placeholder='Telegram UserID'/>";
    }
}

$replaceData['{{UserWebsites}}'] .= '<a data-action="add-form-field"><i class="fa fa-plus-circle"></i></a>';
$replaceData['{{UserEmails}}'] .= '<a data-action="add-form-field"><i class="fa fa-plus-circle"></i></a>';
$replaceData['{{UserTelegramIDs}}'] .= '<a data-action="add-form-field"><i class="fa fa-plus-circle"></i></a>';

$output['html'] = str_replace(array_keys($replaceData), array_values($replaceData), $output['html']);