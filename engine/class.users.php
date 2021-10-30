<?php
session_start();
require_once __DIR__."/class.action.php";

class Users extends Action{
    public function login($data){
        $authData = $this->getAssoc($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, 'SELECT id,login,password FROM {TABLE_USERS} WHERE login = ? LIMIT 1'),[$data['login']]));
        if (password_verify($data['password'], $authData['password'])){
            unset($authData['password']);
            $_SESSION = $authData;
            return true;
        }
        return false;
    }
}