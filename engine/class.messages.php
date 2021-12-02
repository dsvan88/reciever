<?php
require_once __DIR__.'/class.action.php';

class Messages extends Action {
    private $uid = 0;
    function __construct($data = []){
        parent::__construct();
        if (isset($data['uid']))
            $this->uid = $data['uid'];
    }
    public function getMessagesCount($searchString=''){
        $values = ['new'];
        $searchQuery = 'SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? ';
        if ($this->uid !== 0){
            $searchQuery .= ' AND uid = ?';
            $values[] = $this->uid;
        }
        if ($searchString !== ''){
            $searchQuery .= ' AND ( name ~~* ? OR contact ~~* ? OR email ~~* ? OR message ~~* ? )'; // ~~* - аналог ILIKE - регистронезависимый поиск подстроки
            $values = array_pad($values, count($values)+4, "%$searchString%");
        }
        return $this->getColumn($this->prepQuery($searchQuery, $values));
    }
    public function getMessages($page = 0, $searchString=''){
        $values = ['new'];
        $searchQuery = 'SELECT * FROM '.TABLE_MAIN.' WHERE status = ? ';
        if ($this->uid !== 0){
            $searchQuery .= ' AND uid = ?';
            $values[] = $this->uid;
        }
        if ($searchString !== ''){
            $searchQuery .= ' AND ( name ~~* ? OR contact ~~* ? OR email ~~* ? OR message ~~* ? )'; // ~~* - аналог ILIKE - регистронезависимый поиск подстроки
            $values = array_pad($values, count($values)+4, "%$searchString%");
        }

        $searchQuery .= ' ORDER BY id DESC';

        if ($page === 0)
            $searchQuery .= ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $searchQuery .= ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        
        return $this->getAssocArray($this->prepQuery($searchQuery, $values));
    }
    public function getMessageData($conditions = [], $columns = '*'){
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
        return $this->getAssoc($this->prepQuery(str_replace('{TABLE_MAIN}', TABLE_MAIN, "SELECT $keys FROM {TABLE_MAIN} $where"), $conditions));
    }
    public function getArchivedMessagesCount($searchString){
        $values = ['archive'];
        $searchQuery = 'SELECT COUNT(id) FROM '.TABLE_MAIN.' WHERE status = ? ';
        if ($this->uid !== 0){
            $searchQuery .= ' AND uid = ?';
            $values[] = $this->uid;
        }
        if ($searchString !== ''){
            $searchQuery .= ' AND ( name ~~* ? OR contact ~~* ? OR email ~~* ? OR message ~~* ? )'; // ~~* - аналог ILIKE - регистронезависимый поиск подстроки
            $values = array_pad($values, count($values)+4, "%$searchString%");
        }
        return $this->getColumn($this->prepQuery($searchQuery, $values));
    }
    public function getArchivedMessages($page,$searchString=''){
        
        $values = ['archive'];
        $searchQuery = 'SELECT * FROM '.TABLE_MAIN.' WHERE status = ? ';
        if ($this->uid !== 0){
            $searchQuery .= ' AND uid = ?';
            $values[] = $this->uid;
        }
        if ($searchString !== ''){
            $searchQuery .= ' AND ( name ~~* ? OR contact ~~* ? OR email ~~* ? OR message ~~* ? )'; // ~~* - аналог ILIKE - регистронезависимый поиск подстроки
            $values = array_pad($values, count($values)+4, "%$searchString%");
        }

        $searchQuery .= ' ORDER BY id DESC';

        if ($page === 0)
            $searchQuery .= ' LIMIT '.CFG_MESSAGE_PER_PAGE;
        else
            $searchQuery .= ' LIMIT '.CFG_MESSAGE_PER_PAGE.' OFFSET '.(CFG_MESSAGE_PER_PAGE*$page);
        
        return $this->getAssocArray($this->prepQuery($searchQuery, $values));
    }
    public function setMessageArchive($mId){
        return $this->rowUpdate(['status' => 'archive'], ['id' => $mId]);
    }
    public function deleteMessage($mId){
        return $this->rowDelete($mId);
    }
}