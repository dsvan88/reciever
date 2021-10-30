<?php
require_once __DIR__.'/engine/users.class.php';

$template = file_get_contents(__DIR__.'/templates/main_template.html');
$user = new Users();

$output['{STYLE}'] = '<link rel="stylesheet" href="./css/style.css?v='.$_SERVER['REQUEST_TIME'].'" />';

if (!isset($_SESSION['id']) && (!isset($_POST['login']) || !isset($_POST['password'])) ){
	require $_SERVER['DOCUMENT_ROOT'].'/views/signin.php';
	echo str_replace(array_keys($output),array_values($output),$template);
	exit();
}
if (!isset($_SESSION['id'])){
	error_log(password_hash(sha1(trim($_POST['password'])), PASSWORD_DEFAULT));
	$data = [
		'login' => strtolower(trim($_POST['login'])),
		'password' => sha1(trim($_POST['password']))
	];
	if (!$user->login($data)){
		require $_SERVER['DOCUMENT_ROOT'].'/views/signin.php';
		echo str_replace(array_keys($output),array_values($output),$template);
		exit();
	}
}
require $_SERVER['DOCUMENT_ROOT'].'/views/messages.php';

echo str_replace(array_keys($output),array_values($output),$template);