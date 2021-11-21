<?
preg_match_all('#/{([^/?]*?)}#',$pattern,$varNames);

preg_match_all('#/([^/?]*)#',$_SERVER['REQUEST_URI'],$values);

if (count($varNames[1]) !== count($values[1]))
    echo 'ERROR!';

for ($i=0; $i <count($varNames[1]) ; $i++) { 
    ${$varNames[1][$i]} = $values[1][$i];
}
$getRequestPos = mb_strpos($_SERVER['REQUEST_URI'],'?',0,'UTF-8');

if ($getRequestPos !== false){
    parse_str(mb_substr($_SERVER['REQUEST_URI'],$getRequestPos+1, NULL,'UTF-8'),$_GET);
}