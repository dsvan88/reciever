<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();

$action->logout();

$output['text'] = 'Done!';