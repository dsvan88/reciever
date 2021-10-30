<?php
require dirname(__DIR__).'/config/vars.php';

class Action {
    private $SQL;
    function __construct(){
        try {
            $this->SQL = new PDO('pgsql:host='.SQL_HOST.';port='.SQL_PORT.';dbname='.SQL_DB,SQL_USER,SQL_PASS);
        }
        catch (PDOException $exeption){
            error_log("SQL Error!: " . $exeption->getMessage());
            die();
        }
    }
	function SQLClose()
	{
		$this->SQL = null;
	}
    function query($q)
    {
        return $this->SQL->query($q);
    }
    function prepQuery($q,$a)
    {
        try {
            $stmt = $this->SQL->prepare($q);
            $stmt->execute($a);
            return $stmt;
        } catch (Throwable $th) {
            error_log('Error: '.$th->getFile().':'.$th->getLine().";\r\nMessage: ".$th->getMessage()."\r\nTrace:\r\n".$th->getTraceAsString());
            return false;
        }
    }
    // Проверка наличия записей в базе по критериям
    function recordExists($criteria,$table,$criteriaType='OR'){

		$keys = array_keys($criteria);
		$query = "SELECT COUNT(id) FROM $table WHERE ";

		for($x = 0; $x<count($keys); $x++){
			if (trim($criteria[$keys[$x]]) !== ''){
				$query .= $keys[$x]." = :$keys[$x] $criteriaType ";
			}
		}
		return ($this->getColumn($this->prepQuery(substr($query,0,-2-strlen($criteriaType)),$criteria)) > 0) ? true : false;
	}
    // Добавляет запись в MYSQL базу
	// $data - ассоциативный массив со значениями записи: array('column_name'=>'column_value',...)
	// $t - таблица в которую будет добавлена запись
	// возвращает последнюю запись из таблицы (id новой записи, если верно указан $g_id)
	function rowInsert($data,$t='')
	{
		$keys = array_keys($data);
		$preKeys = [];
		for ($x=0;$x<count($keys);$x++)
			$preKeys[$x] = ':'.$keys[$x];
		$t = $t === '' ? TABLE_MAIN : $t;
		$this->prepQuery('INSERT INTO '.$t.' ('.implode(',',$keys).') VALUES ('.implode(',',$preKeys).')', $data);
		return $this->SQL->lastInsertId();
	}
    // Обновляет запись в базе
	// $data - ассоциативный массив со значениями записи: array('column_name'=>'column_value',...)
	// $where - ассоциативный массив со значенниями по которым искать запись для обновления ('key'=>'value') ('id'=>1)
	// $table - таблица в которую будет добавлена запись
    function rowUpdate($data,$where,$table='')
    {
        $query ='UPDATE '.($table==='' ? TABLE_MAIN : $table).' SET ';
        
        foreach ($data as $k=>$v)
            $query .= "$k = :$k,";
        $conditon = ' WHERE ';
        foreach ($where as $k=>$v){
            if (isset($data[$k])){
                error_log(__METHOD__.': UPDATE cann’t work with same keys in UPDATE-array and UPDATE-conditions!');
                die();
            }
            $data[$k] = $v;
            $conditon .= "$k = :$k OR";
        }
        if ($conditon === ' WHERE '){
            error_log(__METHOD__.': There in no conditions for SQL UPDATE');
            die();
        }
        $this->prepQuery(substr($query,0,-1).substr($conditon,0,-3), $data);
    }
    // Вибирает значение лишь одной колонки, быстрее, чем getRow($q)[0]
	function getColumn($q, $n=0)
	{
		return $q->fetchColumn($n);
	}
    // Разбирает результат запроса в простой массив
	function getRow($q)
	{
		return $q->fetch(PDO::FETCH_NUM);
	}
    // Разбирает результат запроса в ассоциативный массив
    function getAssoc($q)
	{
		return $q ? $q->fetch(PDO::FETCH_ASSOC) : $q;
	}
    // Перебирает результат запроса в двухмерный ассоциативный массив
    function getAssocArray($r)
	{
		$a = array();
		$i=0;
		while($row = $this->getAssoc($r))
		{
			foreach($row as $k=>$v)
				$a[$i][$k] = $v;
			++$i;
		}
		$r = null;
		return $a;
	}
}