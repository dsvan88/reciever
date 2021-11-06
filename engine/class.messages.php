<?php
require_once __DIR__.'/class.action.php';

class Messages extends Action {
    private $uid = 0;
    function __construct($data){
        parent::__construct();
        if (isset($data['uid']))
            $this->uid = $data['uid'];
    }
    public function getMessagesCount(){
        if ($this->uid === 0)
            return $this->getColumn($this->query('SELECT COUNT(id) FROM '.TABLE_MAIN));
        return $this->getColumn($this->prepQuery('SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE uid = ? ', [$this->uid]));
    }
    public function getMessages($page = 0){
        if ($page === 0)
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        if ($this->uid === 0){
            return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_MAIN.' ORDER BY id DESC'.$limit));
        }
        return $this->getAssocArray($this->prepQuery('SELECT * FROM '.TABLE_MAIN.'  WHERE uid = ? ORDER BY id DESC'.$limit, [$this->uid]));
    }
    public function archiveMessages(){
        return $this->rowUpdate();
    }
}