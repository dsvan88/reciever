<?php
$output['{HEADER_TITLE}'] = 'Welcome to simple personal mail system!';
$output['{MAIN_CONTENT}'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/signin_form.html');