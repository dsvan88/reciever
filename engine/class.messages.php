<?php
require_once __DIR__.'/class.action.php';

class Messages extends Action {
    public function getMessages(){
        return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_MAIN.' ORDER BY id DESC'));
    }
    public function archiveMessages(){
        return $this->rowUpdate();
    }
}