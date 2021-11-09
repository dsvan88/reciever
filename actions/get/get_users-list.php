<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.crypt.php';

$action = new Users();
$usersCount = $action->getUsersCount();
if ($usersCount === 0){
    $output['html'] = '<h1>Nothing to show!</h1>';
}
else{
    $page = 0;
    if (isset($_GET['page']))
        $page = (int) $_GET['page'];
    $usersList = $action->getUsersList($page);
    $output['html'] = '';
    $cryptKey = $action->getCryptKey();
    $crypt = new Crypt(['key'=>$cryptKey]);
    for($x=0;$x<count($usersList);$x++){
        $usersContacts = $action->getUsersContacts(['conditions' => ['uid' => $usersList[$x]['id']] ]);
        $websites = $contacts = $tg_uids = '';
        for($i=0;$i<count($usersContacts);$i++){
            if ($usersContacts[$i]['type'] ==='website'){
                $websites .= "{$usersContacts[$i]['value']} <i class='fa fa-times' data-action='delete-contact' data-contact-id='{$usersContacts[$i]['id']}'></i><br>";
            }
            elseif ($usersContacts[$i]['type'] ==='email'){
                $contacts .= $crypt->decrypt($usersContacts[$i]['value'])." <i class='fa fa-times' data-action='delete-contact' data-contact-id='{$usersContacts[$i]['id']}'></i><br>";
            }
            elseif ($usersContacts[$i]['type'] ==='tg_uid'){
                $tg_uids .= $crypt->decrypt($usersContacts[$i]['value'])." <i class='fa fa-times' data-action='delete-contact' data-contact-id='{$usersContacts[$i]['id']}'></i><br>";
            }
        }

        $output['html'] .= "
        <tr class='users__item' data-uid='{$usersList[$x]['id']}'>
            <td class='users__checkbox'>
                <input type='checkbox' name='check-user' value='$x' data-action-change='check-user-change'/>
                ".($x+1).".
            </td>
            <td class='users__login'>
                {$usersList[$x]['login']}
            </td>
            <td class='users__role'>
               {$usersList[$x]['role']}
            </td>
            <td class='users__sites'>
                $websites
            </td>
            <td class='users__emails'>
                $contacts
            </td>
            <td class='users__tg-uids'>
                $tg_uids
            </td>
            <td class='users__dashboard'>
                <i class='fa fa-pencil-square-o'></i>
                <i class='fa fa-user-times'></i>
            </td>
        </tr>
        ";
    };
};
$output['html'] = "
    <main class='main users main-section'>
        <table class='users__item'>
            <thead>
                <tr>
                    <th class='users__common-checkbox'>
                        <input type='checkbox' name='check-user' value='all' data-action-change='check-user-change'/>
                        #
                    </th>
                    <th class='users__common-login'>
                        Login
                    </th>
                    <th class='users__common-role'>
                        System role
                    </th>
                    <th class='users__common-sites'>
                        Web-sites
                    </th>
                    <th class='users__common-emails'>
                        E-mails
                    </th>
                    <th class='users__common-tg-uids'>
                        Telegram IDs
                    </th>
                    <th class='users__common-dashboard'>
                        <i class='fa fa-user-times' data-action='delete-checked-users'></i>
                        <i class='fa fa-user-plus' data-action='add-user-form'></i>
                    </th>
                </tr>
            </thead>
            <tbody>
            $output[html]
            </tbody>
        </table>
    </main>";