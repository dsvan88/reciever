<?php
require_once __DIR__.'/class.action.php';

class Messages extends Action {
    private $uid = 0;
    function __construct($data = []){
        parent::__construct();
        if (isset($data['uid']))
            $this->uid = $data['uid'];
    }
    public function getMessagesCount(){
        if ($this->uid === 0)
            return $this->getColumn($this->prepQuery('SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? ', ['new']));
        return $this->getColumn($this->prepQuery('SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? AND uid = ? ', ['new', $this->uid]));
    }
    public function getMessages($page = 0){
        if ($page === 0)
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        if ($this->uid === 0){
            return $this->getAssocArray($this->prepQuery('SELECT * FROM '.TABLE_MAIN.' WHERE status = ? ORDER BY id DESC'.$limit,['new']));
        }
        return $this->getAssocArray($this->prepQuery('SELECT * FROM '.TABLE_MAIN.'  WHERE status = ? AND uid = ? ORDER BY id DESC'.$limit, ['new',$this->uid]));
    }
    public function getArchivedMessagesCount(){
        if ($this->uid === 0)
            return $this->getColumn($this->prepQuery('SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? ', ['archive']));
        return $this->getColumn($this->prepQuery('SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? AND uid = ? ', ['archive', $this->uid]));
    }
    public function getArchivedMessages($page){
        if ($page === 0)
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        if ($this->uid === 0){
            return $this->getAssocArray($this->prepQuery('SELECT * FROM '.TABLE_MAIN.' WHERE status = ? ORDER BY id DESC'.$limit, ['archive']));
        }
        return $this->getAssocArray($this->prepQuery('SELECT * FROM '.TABLE_MAIN.'  WHERE status = ? AND uid = ? ORDER BY id DESC'.$limit, ['archive', $this->uid]));
    }
    public function setMessageArchive($mId){
        return $this->rowUpdate(['status' => 'archive'], ['id' => $mId]);
    }
    public function deleteMessage($mId){
        return $this->rowDelete($mId);
    }
}