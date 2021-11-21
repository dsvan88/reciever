<?php
if ($_SERVER['REQUEST_URI'] !== '/'){
    if (strpos($_SERVER['REQUEST_URI'],'switcher')){
        require_once $_SERVER['DOCUMENT_ROOT'].'/switcher.php';
    }
    else if (strpos($_SERVER['REQUEST_URI'],'reciever')){
        require_once $_SERVER['DOCUMENT_ROOT'].'/reciever.php';
    }
    else {
        require_once $_SERVER['DOCUMENT_ROOT'].'/index.php';
    }
}
else 
    require_once $_SERVER['DOCUMENT_ROOT'].'/index.php';