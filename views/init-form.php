<?php
$output['{STYLE}'] = '<link rel="stylesheet" href="../css/style.css?v='.$_SERVER['REQUEST_TIME'].'" />';
$output['{HEADER_TITLE}'] = 'Init form';
$output['{MAIN_CONTENT}'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/init_form.html');