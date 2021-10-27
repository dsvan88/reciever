<?php
session_start();
require_once __DIR__."/action.class.php";

class Users extends Action{
    public function login($data){
        $_SESSION = $this->getAssoc($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, 'SELECT id,login FROM {TABLE_USERS} WHERE login = :login AND password = :password LIMIT 1'),$data));
        return $_SESSION;
    }
}