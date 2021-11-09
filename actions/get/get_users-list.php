<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';

$action = new Users();
$usersList = $action->getUsersList();

