<?php
if (!session_id()){
    session_start();
}

$template = file_get_contents(__DIR__.'/templates/main-template.html');

$output = [
	'{STYLE}' => '<link rel="stylesheet" href="./css/style.css?v='.$_SERVER['REQUEST_TIME'].'" />',
	'{SCRIPTS}' => '',
	'{HEADER_CONTENT}' => file_get_contents(__DIR__.'/templates/header-content.html'),
	'{LEFT_ASIDE}' => '',
	'{MAIN_CONTENT}' => ''
];

require $_SERVER['DOCUMENT_ROOT'].'/engine/auth.php';

$output['{LEFT_ASIDE}'] = file_get_contents(__DIR__.'/templates/left-side-'.$_SESSION['role'].'.html');

$output['{{UserID}}'] = $_SESSION['id'];

$output['{HEADER_CONTENT}'] .= '<div class="header__exit" data-action="user-log-out" title="Logout"><i class="fa fa-sign-out"></i></div>';

$output['{SCRIPTS}'] = "
	<script defer src='./js/main-funcs.js?v=$_SERVER[REQUEST_TIME]'></script>
	<script defer src='./js/modals.js?v=$_SERVER[REQUEST_TIME]'></script>
	<script defer src='./js/script.js?v=$_SERVER[REQUEST_TIME]'></script>
	";

if ($_SERVER['REQUEST_URI'] !== '/'){

	$pattern = '/{view}';
	require_once __DIR__.'/engine/parse-uri.php';
	if (in_array($view,['archive','messages','settings','users'])){
		require "$_SERVER[DOCUMENT_ROOT]/views/list-$view.php";
	}
}
else
	require $_SERVER['DOCUMENT_ROOT'].'/views/list-messages.php';

echo str_replace(array_keys($output),array_values($output),$template);