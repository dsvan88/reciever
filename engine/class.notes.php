<?php
require_once __DIR__.'/class.action.php';

class Notes {
    private $action;
    function __construct($data = []){
        $this->action = new Action();
    }
    public function getNotesCount($mid = 0){
        if ($mid === 0)
            return false;
        return $this->action->getColumn($this->action->prepQuery('SELECT COUNT(id) FROM '.TABLE_NOTES.' WHERE mid = ? ', [$mid]));
    }
    public function getNotes($mid=0, $page = 0){
        if ($mid === 0)
            return false;
        if ($page === 0)
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $limit = ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        return $this->action->getAssocArray($this->action->prepQuery('SELECT * FROM '.TABLE_NOTES.' WHERE mid = ? ORDER BY id DESC'.$limit, [$mid]));
    }
    public function getNoteData($conditions = [], $columns = '*'){
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
        return $this->action->getAssoc($this->action->prepQuery(str_replace('{TABLE_NOTES}', TABLE_NOTES, "SELECT $keys FROM {TABLE_NOTES} $where"), $conditions));
    }
    public function addNote($data){
        return $this->action->rowInsert($data,TABLE_NOTES);
    }
    public function deleteNote($nId){
        return $this->action->rowDelete($nId, TABLE_NOTES);
    }
}