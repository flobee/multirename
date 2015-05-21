<?php

/**
 * Multirename bootstrap for tests
 */
ini_set('memory_limit', '64M');
date_default_timezone_set('Europe/Berlin');

setlocale(LC_ALL, 'POSIX');// "C" style

ini_set('include_path', '../externals/mumsys-library/src/' . PATH_SEPARATOR . get_include_path());

//require_once '../build/multirename.phar';
//ini_set('include_path', 'phar://multirename.phar' . PATH_SEPARATOR . get_include_path());

require_once '../externals/mumsys-library/src/Mumsys_Loader.php';
spl_autoload_register(array('Mumsys_Loader', 'autoload'));


class MumsysTestHelper extends PHPUnit_Framework_TestSuite
{
    private static $_config;

    private static $_params;


    public static function getConfig()
    {
        if ( !isset(self::$_config) ) {
            self::$_config = Mumsys_Config::getInstance();
        }

        return self::$_config;
    }

    public static function getTestsBaseDir()
    {
        if (isset(self::$_params['testsBaseDir'])) {
            return self::$_params['testsBaseDir'];

        } else {
            self::$_params['testsBaseDir'] = realpath(dirname(__FILE__) .'/');
            return self::$_params['testsBaseDir'];
        }
    }

}
