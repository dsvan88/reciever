<?php
$output['html'] = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/form_user-password-change.html');
$output['title'] = 'Change User password';

$replaceData = [
    '{{UserId}}' => $_POST['uid'],
];

$output['html'] = str_replace(array_keys($replaceData), array_values($replaceData), $output['html']);