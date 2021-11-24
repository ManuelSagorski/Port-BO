<?php
namespace components;

use components\classes\dbConnect;
use components\classes\logger;
use components\classes\user;
use components\classes\port;

session_start();

/*
 * Errorhandling
 */
error_reporting(-1);
ini_set('display_errors', 'On');

/*
 * Pfadangaben
 */
define("FOLDER",'boNew');
define("PATH",$_SERVER["DOCUMENT_ROOT"] . FOLDER);

$hostname = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['PHP_SELF']);

/*
 * Laden der Credentials
 */
require_once(PATH . '/components/configCredentials.php');

/*
 * Autoload der benötigten Klassen
 */
spl_autoload_register(function($class) {
    $class_name = explode('\\', $class);
    $classFolders = array("classes", "classes/PHPMailer", "types", "controller");
    
    foreach($classFolders as $folder) {
        $file = PATH . '/components/' . $folder . '/' . str_replace('\\', '/', $class_name[count($class_name)-1]) . '.php';
        if(file_exists($file)) {
            require_once($file);
            break;
        }
    }
});

/*
 * Aufbau der DB Verbindung
 */
dbConnect::initDB();

/*
 * Instanz der Logging Klasse
 */
$logger = new logger();

if(isset($_GET['logout'])) {
    $_SESSION = array();
    header('Location: http://'.$hostname.($path == '/' ? '' : $path).'/index.php');
}

if(basename($_SERVER[ 'SCRIPT_NAME' ]) != "index.php" && !isset($independent)) {
    if(!isset($_SESSION['user'])) {
        header('Location: http://'.$hostname.'/' . FOLDER . '/index.php');
    }
    else {
        $user = dbConnect::fetchSingle("select * from port_bo_user where id = ?", user::class, array($_SESSION['user']));
    }
}

$ports = dbConnect::fetchAll('select * from port_bo_port', port::class, array());

?>