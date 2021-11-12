<?php
session_start();
require_once __DIR__.'/class.action.php';
require_once __DIR__.'/class.crypt.php';

class Users extends Action{
    public function login($data){
        $authData = $this->getAssoc($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, 'SELECT id,login,password,role FROM {TABLE_USERS} WHERE login = ? LIMIT 1'),[$data['login']]));
        if (password_verify($data['password'], $authData['password'])){
            unset($authData['password']);
            $_SESSION = $authData;
            return true;
        }
        return false;
    }
    public function getCryptKey(){
        return $this->getColumn($this->query(str_replace('{TABLE_AUTH}', TABLE_AUTH, 'SELECT key FROM {TABLE_AUTH} WHERE id = 1 LIMIT 1')));
    }
    public function addNewUser($data){

        if ($this->recordExists(['login' => $data['login']],TABLE_USERS))
            return false;

        $userData = [
            'login' => $data['login'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ];
        $userData['id'] = $this->rowInsert($userData,TABLE_USERS);
        if (!$userData['id']) return false;

        $key = $this->getCryptKey();
        $crypt = new Crypt(['key'=>$key]);

        $contacts = [];
        for($x=0; $x<count($data['email']); $x++){
            $contacts[] = [
                'uid' => $userData['id'],
                'type' => 'email',
                'value' => $crypt->encrypt($data['email'][$x])
            ];
        };

        for($x=0; $x<count($data['tg_uid']); $x++){
            $contacts[] = [
                'uid' => $userData['id'],
                'type' => 'tg_uid',
                'value' => $crypt->encrypt($data['tg_uid'][$x])
            ];
        };

        for($x=0; $x<count($data['website']); $x++){
            $contacts[] = [
                'uid' => $userData['id'],
                'type' => 'website',
                'value' => $data['website'][$x]
            ];
        };
        $this->rowInsert($contacts,TABLE_CONTACTS);
    }
    public function getUserData($data = ['columns' => '*', 'conditions' => [] ]){
        if (!is_array($data['columns']))
            $keys = '*';
        else
            $keys = implode(',', $data['columns']);

        $where = '';
        if (count($data['conditions']) !== 0){
            $where = ' WHERE ';
            foreach($data['conditions'] as $k=>$v){
				$where .= $k." = :$k OR ";
            }
            $where = substr($where, 0, -4);
        }
        return $this->getAssoc($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, "SELECT $keys FROM {TABLE_USERS} $where"), $data['conditions']));
    }
    public function getUsersContacts($data = ['columns' => '*', 'conditions' => [] ]){
        if (!is_array($data['columns']))
            $keys = '*';
        else
            $keys = implode(',', $data['columns']);

        $where = '';
        if (count($data['conditions']) !== 0){
            $where = ' WHERE ';
            foreach($data['conditions'] as $k=>$v){
				$where .= $k." = :$k OR ";
            }
            $where = substr($where, 0, -4);
        }
        return $this->decryptContacts($this->getAssocArray($this->prepQuery(str_replace('{TABLE_CONTACTS}', TABLE_CONTACTS, "SELECT $keys FROM {TABLE_CONTACTS} $where"), $data['conditions'])));
    }
    public function decryptContacts($contacts){

        $cryptKey = $this->getCryptKey();
        $crypt = new Crypt(['key'=>$cryptKey]);

        for($i=0;$i<count($contacts);$i++){
            if ($contacts[$i]['type'] === 'website') continue;
            $contacts[$i]['value'] = $crypt->decrypt($contacts[$i]['value']);
        }
        return $contacts;
    }
    public function getUsersList(){
        return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_USERS));
    }
    public function getUsersCount(){
        return $this->getColumn($this->query('SELECT COUNT(id) FROM '.TABLE_USERS));
    }
    public function deleteUser($uid){
        $this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, 'DELETE FROM {TABLE_USERS} WHERE id = ? '), [$uid]);
        $this->prepQuery(str_replace('{TABLE_CONTACTS}', TABLE_CONTACTS, 'DELETE FROM {TABLE_CONTACTS} WHERE uid = ? '), [$uid]);
        return true;
    }
    public function deleteContact($cid){
        return $this->prepQuery(str_replace('{TABLE_CONTACTS}', TABLE_CONTACTS, 'DELETE FROM {TABLE_CONTACTS} WHERE id = ? '), [$cid]);
    }
}