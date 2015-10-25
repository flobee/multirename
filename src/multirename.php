<?php

/**
 * ----------------------------------------------------------------------------
 * Multirename
 * for MUMSYS Library for Multi User Management System
 * ----------------------------------------------------------------------------
 * @author Florian Blasel <flobee.code@gmail.com>
 * ----------------------------------------------------------------------------
 * @copyright (c) 2015 by Florian Blasel
 * ----------------------------------------------------------------------------
 * @license LGPL Version 3 http://www.gnu.org/licenses/lgpl-3.0.txt
 * ----------------------------------------------------------------------------
 * @version 1.2.1
 * Created 28.02.2015
 * ----------------------------------------------------------------------------
 */

 /**
  * Example (you may check INSTALL file first):
  * multirename --test --fileextensions "avi;ts;mpg" --keepcopy --path /tmp/ \
  * -s 'this-goes=that-is;regex:/^(\d{5})$/i'=%path2%_%path1%_$1; =_'
  */


$pathLibrary = 'library/mumsys/'; // tailing slash!
$pathLogfile = '/tmp/';

// --- misc -------------------------------------------------------------------
error_reporting(-1);
ini_set('display_errors', true);
ignore_user_abort(false);

ini_set('max_execution_time', 0);
ini_set('memory_limit', '8M');
date_default_timezone_set('Europe/Berlin');

// --- bootstrap for Mumsys library -------------------------------------------
require_once( $pathLibrary . 'Mumsys_Loader.php');
spl_autoload_extensions('.php');
spl_autoload_register(array('Mumsys_Loader', 'autoload'));


// --- prepare the logger -----------------------------------------------------
if (empty($_SERVER['REMOTE_USER'])) {
    $user = $_SERVER['USER'];
} else {
    $user = $_SERVER['REMOTE_USER'];
}

$logOptions = array(
    // user based or access problems take affect
    'logfile' => $pathLogfile .'multirename.' . $user . '.log',
    'way' => 'a',
    'logLevel' => 7,
    'msglogLevel' => 7, // can be changed in cmd line! as --loglevel
    'msgLineFormat' => '[%3$s] %5$s',
    'msgEcho' => true,
    'msgReturn' => false,
    'maxfilesize' => 1024000 * 3,
);
$logger = new Mumsys_Logger($logOptions);


// --- pipe uncachable errors to the logger -----------------------------------
function myExceptionHandler($ex)
{
    global $logger;

    $logger->log('No catchable exception found:', 0);
    $logger->log($ex->getMessage(), 0);
    $logger->log('Exception trace:' . PHP_EOL . $ex->getTraceAsString(), 0);
}

function myErrorHandler($code, $message, $file, $row)
{
    global $logger;
    $loglevel = 0;
    $codes = array(
        1 => 'E_ERROR',
        2 => 'E_WARNING',
        4 => 'E_PARSE',
        8 => 'E_NOTICE',
        16 => 'E_CORE_ERROR'
    );
    if (isset($codes[$code])) {
        $code = $codes[$code];
    }

    $string = 'PHP-Error "%4$s" (%3$s) in "%1$s:%2$s"';
    $message = sprintf($string, basename($file), $row, $code, $message);
    $logger->log($message, $loglevel);
}

set_exception_handler('myExceptionHandler');
set_error_handler('myErrorHandler');



// --- lets go ----------------------------------------------------------------


try {
    $options = Mumsys_Multirename::getSetup(true);
    // not needed in setup but for shell args
    $options['--help|-h'] = 'Show this help';

    $opts = new Mumsys_GetOpts($options);
    $config = $opts->getResult();

    if (!isset($config['path']) && !isset($config['from-config'])
        && !isset($config['help']) && !isset($config['version'])) {
        $message = 'Parameters found but incomplete.' . PHP_EOL . 'Usage:' .
            PHP_EOL . $opts->getHelp() .PHP_EOL;
        $logger->log($message, 5);
    } else {
        if (isset($config['help'])) {
            $logger->log('Usage:' . PHP_EOL . $opts->getHelp(), 6);
        } elseif (isset($config['version'])) {
            Mumsys_Multirename::showVersion();
        } else {
            $oFiles = new Mumsys_FileSystem();
            $oMultirename = new Mumsys_Multirename($config, $oFiles, $logger);
        }
    }

} catch (Mumsys_Exception $err) {
    $logger->log('Catched mumsys exception: ', 0);
    $logger->log($err->getMessage(), 0);
} catch (Exception $e) {
    $logger->log('Standard exception found: ', 0);
    $logger->log($e->getMessage(), 0);
    $logger->log($e->getTraceAsString(), 0);
}
