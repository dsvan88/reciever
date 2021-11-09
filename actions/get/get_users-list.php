<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

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
    for($x=0;$x<count($usersList);$x++){
        // $usersContacts = $action->getUsersContacts($page);
        $output['html'] .= "
        <div class='users__item' data-uid='$x'>
            <div class='users__item-num'>".($x+1).".</div>
            <div class='users__login'>
                {$usersList[$x]['login']}
            </div>
            <div class='users__role'>
               {$usersList[$x]['role']}
            </div>
            <div class='users__sites'>
                sites
            </div>
            <div class='users__emails'>
                contacts
            </div>
            <div class='users__tg-uids'>
                tg_uids
            </div>
            <div class='users__dashboard'>
                <button><i class='fa fa-pencil-square-o'></i></button>
                <button><i class='fa fa-user-times'></i></button>
            </div>
        </div>
        ";
    };
};
$output['html'] = "
    <main class='main users main-section'>
        <div class='users__common-dashboard'>
            <i class='fa fa-user-plus' data-action='add-user-form'></i>
        </div>
        $output[html]
    </main>";