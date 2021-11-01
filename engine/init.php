<?php
if (count($_POST)===0){
    require_once $_SERVER['DOCUMENT_ROOT'].'/views/init-form.php';
    $template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/templates/main_template.html');
    die(str_replace(array_keys($output),array_values($output),$template));
}

require __DIR__.'/class.action.php';

$action = new Action();

$action->query(
    str_replace('{TABLE_MAIN}', TABLE_MAIN,
        "CREATE TABLE IF NOT EXISTS {TABLE_MAIN} (
            id int GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
            name CHARACTER VARYING(100) NOT NULL DEFAULT '',
            contact CHARACTER VARYING(100) NOT NULL DEFAULT '',
            email CHARACTER VARYING(100) NOT NULL DEFAULT '',
            message TEXT NULL DEFAULT NULL,
            time int NOT NULL DEFAULT 0,
            status CHARACTER VARYING(20) NOT NULL DEFAULT 'new'
        );"
    )
);

$action->query(
    str_replace('{TABLE_USERS}', TABLE_USERS,
        "CREATE TABLE IF NOT EXISTS {TABLE_USERS} (
            id int GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
            login CHARACTER VARYING(80) NOT NULL DEFAULT '',
            password CHARACTER VARYING(100) NOT NULL DEFAULT ''
        );"
    )
);

$action->query(
    str_replace('{TABLE_AUTH}', TABLE_AUTH,
        "CREATE TABLE IF NOT EXISTS {TABLE_AUTH} (
            id int GENERATED BY DEFAULT AS IDENTITY PRIMARY KEY,
            login CHARACTER VARYING(150) NOT NULL DEFAULT '',
            password CHARACTER VARYING(150) NOT NULL DEFAULT '',
            tg_uid CHARACTER VARYING(250) NOT NULL DEFAULT '',
            tg_bot_token CHARACTER VARYING NOT NULL DEFAULT '',
            key CHARACTER VARYING(150) NOT NULL DEFAULT ''
        );"
    )
);
echo 'Databases is Ready!</br>';
if (isset($_POST['login']) && trim($_POST['login']) !== ''){
    if (!$action->recordExists(['id'=>1],TABLE_USERS)){
        $action->rowInsert(['login'=>strtolower(trim($_POST['login'])), 'password' => trim($_POST['password']) !=='' ? password_hash(sha1(trim($_POST['password'])),PASSWORD_DEFAULT) : '$2y$10$nrFt674uOjlgALrZBfu4nu6a4f8bX1h/rmoe2A.2kPUNspmObnl4q'],TABLE_USERS);
    }
    echo 'Create first user - Done!</br>';
}
if (isset($_POST['email']) && isset($_POST['email-password'])){
    if (!$action->recordExists(['id'=>1],TABLE_AUTH)){
        require __DIR__.'/class.crypt.php';
        $crypt = new Crypt([ 'value'=> trim($_POST['email']) ]);
        $action->rowInsert([ 
            'login'=>$crypt->encoded,
            'password' => $crypt->encrypt(trim($_POST['email-password'])),
            'tg_uid' => $crypt->encrypt(trim($_POST['tg_uid'])),
            'tg_bot_token' => $crypt->encrypt(trim($_POST['tg_bot_token'])),
            'key'=> $crypt->key ],TABLE_AUTH);
    }
    echo 'Connection to email - Done!</br>';
}