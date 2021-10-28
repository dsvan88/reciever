<?php
require_once __DIR__.'/action.class.php';

class Messages extends Action {
    public function getMessages(){
        return $this->getAssocArray($this->query('SELECT * FROM '.TABLE_MAIN.' ORDER BY id DESC'));
    }
}