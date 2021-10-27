<?
if (!defined('SQL_HOST'))
{
	define('SQL_HOST', 	'127.0.0.1');
	define('SQL_PORT', 	'5432');
	define('SQL_USER', 	'postgres');
	define('SQL_PASS', 	'');
	define('SQL_DB', 	'dsvtester');
	define('PASS_SALT', 'VeRyHaRdSaLt_');
	define('TABLE_MAIN', 'requests');
	define('TABLE_USERS', 'users');
}

// if (CFG_DEBUG){
	// ini_set('error_reporting', E_ALL);
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
// }
