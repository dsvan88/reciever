<?
if (!defined('SQL_HOST'))
{
	define('PASS_SALT', 'VeRyHaRdSaLt_');
	define('TABLE_MAIN', 'requests');
	define('TABLE_USERS', 'users');
	if (isset($_ENV["DATABASE_URL"])){
		preg_match('/\/\/(.*?)\:(.*?)\@(.*?)\:(\d{1,5})\/(.*)/',$_ENV["DATABASE_URL"], $match);
		define('SQL_HOST', 	$match[3]);
		define('SQL_PORT', 	$match[4]);
		define('SQL_USER', 	$match[1]);
		define('SQL_PASS', 	$match[2]);
		define('SQL_DB', 	$match[5]);
	}
	else {
		define('SQL_HOST', 	'127.0.0.1');
		define('SQL_PORT', 	'5432');
		define('SQL_USER', 	'postgres');
		define('SQL_PASS', 	'');
		define('SQL_DB', 	'dsvtester');
	}
}

// if (CFG_DEBUG){
	// ini_set('error_reporting', E_ALL);
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
// }
