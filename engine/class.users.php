<?php
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
    public function logout(){
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return true;
    }
    public function getCryptKey(){
        return $this->getColumn($this->query(str_replace('{TABLE_AUTH}', TABLE_AUTH, 'SELECT key FROM {TABLE_AUTH} WHERE id = 1 LIMIT 1')));
    }
    public function addNewUser($data){

        if ($this->recordExists(['login' => $data['login']],TABLE_USERS))
            return false;

        $userData = [
            'login' => strtolower($data['login']),
            'password' => password_hash(sha1($data['password']), PASSWORD_DEFAULT)
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
    public function changeUserPassword($data){
        $oldPassword = $this->getColumn($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, 'SELECT password,role FROM {TABLE_USERS} WHERE id = ? LIMIT 1'),[$data['uid']]));
        if (password_verify($data['old_password'], $oldPassword)){
            $this->rowUpdate(['password'=>password_hash($data['password'],PASSWORD_DEFAULT)], ['id' => $data['uid']], TABLE_USERS);
            return true;
        }
        return 'Old password incorrect!';
    }
    public function resetUserPassword($uid){
        $tempPassword = $this->generateRandomString(mt_rand(7, 13));
        $this->rowUpdate(['password'=>password_hash(sha1($tempPassword),PASSWORD_DEFAULT)], ['id' => $uid], TABLE_USERS);
        return $tempPassword;
    }
    public function getUserData($conditions = [], $columns = '*'){
        if (!is_array($columns))
            $keys = $columns;
        else
            $keys = implode(',', $columns);

        $where = '';
        if (count($conditions) !== 0){
            $where = ' WHERE ';
            foreach($conditions as $k=>$v){
				$where .= $k." = :$k OR ";
            }
            $where = substr($where, 0, -4);
        }
        return $this->getAssoc($this->prepQuery(str_replace('{TABLE_USERS}', TABLE_USERS, "SELECT $keys FROM {TABLE_USERS} $where"), $conditions));
    }
    public function getUsersContacts($conditions = [], $clue = 'OR'){

        $where = '';
        if (count($conditions) !== 0){
            $where = ' WHERE ';
            foreach($conditions as $k=>$v){
				$where .= $k." = :$k $clue ";
            }
            $where = substr($where, 0, -(strlen($clue)+2));
        }
        return $this->decryptContacts($this->getAssocArray($this->prepQuery(str_replace('{TABLE_CONTACTS}', TABLE_CONTACTS, "SELECT * FROM {TABLE_CONTACTS} $where ORDER BY id ASC"), $conditions)));
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
        return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_USERS.' ORDER BY id ASC'));
    }
    public function getUsersCount(){
        return $this->getColumn($this->query('SELECT COUNT(id) FROM '.TABLE_USERS));
    }
    public function deleteUser($uid){
        $this->rowDelete($uid, TABLE_USERS);
        $this->prepQuery(str_replace('{TABLE_CONTACTS}', TABLE_CONTACTS, 'DELETE FROM {TABLE_CONTACTS} WHERE uid = ? '), [$uid]);
        return true;
    }
    public function deleteContact($cid){
        return $this->rowDelete($cid, TABLE_CONTACTS);
    }
}