<?php
    $result = $output;
    require $_SERVER['DOCUMENT_ROOT'].'/views/messages.php';
    $result['html'] = $output['{MAIN_CONTENT}'];
    $output = $result;
