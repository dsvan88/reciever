<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();
$usersCount = $action->getUsersCount();
if ($usersCount === 0){
    $output['{MAIN_CONTENT}'] = '<h1>Nothing to show!</h1>';
}
else{
    $page = 0;
    if (isset($_GET['page']))
        $page = (int) $_GET['page'];
    $usersList = $action->getUsersList($page);
    $output['{MAIN_CONTENT}'] = '';
    for($x=0;$x<count($usersList);$x++){
        $usersContacts = $action->getUsersContacts(['conditions' => ['uid' => $usersList[$x]['id']] ]);
        $contacts = [];
        for($i=0;$i<count($usersContacts);$i++)
            $contacts[$usersContacts[$i]['type']] .= "{$usersContacts[$i]['value']} <i class='fa fa-times' data-action='delete-contact' data-contact-id='{$usersContacts[$i]['id']}'></i><br>";

        $output['{MAIN_CONTENT}'] .= "
        <tr class='users__item' data-uid='{$usersList[$x]['id']}'>
            <td class='users__checkbox'>
                <input type='checkbox' name='check-user' value='{$usersList[$x]['id']}' data-action-change='check-user-change'/>
                ".($x+1).".
            </td>
            <td class='users__login'>
                {$usersList[$x]['login']}
            </td>
            <td class='users__role'>
               {$usersList[$x]['role']}
            </td>
            <td class='users__sites'>
                $contacts[website]
            </td>
            <td class='users__emails'>
                $contacts[email]
            </td>
            <td class='users__tg-uids'>
                $contacts[tg_uid]
            </td>
            <td class='users__dashboard'>
                <i class='fa fa-pencil-square-o'  data-action='edit-user-form'></i>
                ".($usersList[$x]['role'] === 'admin' ? '' : '<i class="fa fa-user-times" data-action="delete-user"></i>').'
            </td>
        </tr>
        ';
    };
};
$output['{MAIN_CONTENT}'] = '
    <main class="main users main-section">
        <table class="users__item">
            <thead>
                <tr>
                    <th class="users__common-checkbox">
                        <input type="checkbox" name="check-user" value="all" data-action-change="check-user-change"/>
                        #
                    </th>
                    <th class="users__common-login">
                        Login
                    </th>
                    <th class="users__common-role">
                        System role
                    </th>
                    <th class="users__common-sites">
                        Web-sites
                    </th>
                    <th class="users__common-emails">
                        E-mails
                    </th>
                    <th class="users__common-tg-uids">
                        Telegram IDs
                    </th>
                    <th class="users__common-dashboard">
                        <i class="fa fa-user-times" data-action="delete-users-array"></i>
                        <i class="fa fa-user-plus" data-action="add-user-form"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
            '.$output['{MAIN_CONTENT}'].'
            </tbody>
        </table>
    </main>';