<?php
require_once __DIR__.'/class.action.php';

class Messages extends Action {
    public function getMessagesCount(){
        return $this->getColumn($this->query('SELECT COUNT(id) FROM '.TABLE_MAIN));
    }
    public function getMessages($page = 0){
        if ($page === 0)
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_MAIN.' ORDER BY id DESC'.$limit));
    }
    public function archiveMessages(){
        return $this->rowUpdate();
    }
}