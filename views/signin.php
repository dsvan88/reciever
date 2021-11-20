<?php
$output['{HEADER_CONTENT}'] = '<div></div><h1>Welcome to simple personal mail system!</h1><div></div>';
$output['{MAIN_CONTENT}'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/form_signin.html');