<?php

$output = [
	'{STYLE}' => "<link rel='stylesheet' href='../css/style.css?v=$_SERVER[REQUEST_TIME]' />",
	'{SCRIPTS}' => '',
	'{HEADER_CONTENT}' => '<h1>Init form</h1>',
	'{LEFT_ASIDE}' => '',
	'{MAIN_CONTENT}' => file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/form_init.html')
];
