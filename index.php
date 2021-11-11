<?php
require_once __DIR__.'/engine/class.users.php';

$template = file_get_contents(__DIR__.'/templates/main-template.html');
$user = new Users();

$output = [
	'{STYLE}' => '<link rel="stylesheet" href="./css/style.css?v='.$_SERVER['REQUEST_TIME'].'" />',
	'{SCRIPTS}' => '',
	'{HEADER_TITLE}' => 'Simple personal mail system!',
	'{LEFT_ASIDE}' => '',
	'{MAIN_CONTENT}' => ''
];

if (!isset($_SESSION['id'])){
	if (!isset($_POST['login']) || !isset($_POST['password'])){
		require $_SERVER['DOCUMENT_ROOT'].'/views/signin.php';
		die(str_replace(array_keys($output),array_values($output),$template));
	}

	$data = [
		'login' => strtolower(trim($_POST['login'])),
		'password' => sha1(trim($_POST['password']))
	];
	if (!$user->login($data)){
		require $_SERVER['DOCUMENT_ROOT'].'/views/signin.php';
		die(str_replace(array_keys($output),array_values($output),$template));
	}
}
$output['{LEFT_ASIDE}'] = file_get_contents(__DIR__.'/templates/left-side.html');

$output['{SCRIPTS}'] = "
	<script defer src='./js/main-funcs.js?v=$_SERVER[REQUEST_TIME]'></script>
	<script defer src='./js/modals.js?v=$_SERVER[REQUEST_TIME]'></script>
	<script defer src='./js/script.js?v=$_SERVER[REQUEST_TIME]'></script>
	";

if (isset($_GET['view'])){
	require "$_SERVER[DOCUMENT_ROOT]/views/$_GET[view].php";
}
else
	require $_SERVER['DOCUMENT_ROOT'].'/views/messages-list.php';

echo str_replace(array_keys($output),array_values($output),$template);