<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

error_log('Log out!');

error_log(json_encode($_SESSION));

$action = new Users();

$action->logout();

error_log(json_encode($_SESSION));

$output['text'] = 'Done!';