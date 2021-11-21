<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.users.php';
$user = new Users();

if ( isset($_SESSION['id']) && ( $_SESSION['expire'] < $_SERVER['REQUEST_TIME'] || !$user->checkToken() )){
    $user->logout();
}

if (!isset($_SESSION['id'])){
	if (!isset($_POST['login']) || !isset($_POST['password']) || !$user->login($_POST)){
		require $_SERVER['DOCUMENT_ROOT'].'/views/signin.php';
		die(str_replace(array_keys($output),array_values($output),$template));
	}
}