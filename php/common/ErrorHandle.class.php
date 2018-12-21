<?php 
/**
 * 错误控制类
 *
 * @copyright   Copyright (c) 2017-2018
 * @author      whx
 * @version     ver 1.0
 */

class ErrorHandle
{
    /**
     * 错误控制方法注册
     * @param null
     * @return null
     */
    public static function register ()
    {
        set_error_handler(array('ErrorHandle', 'allHandle'));
        set_exception_handler(array('ErrorHandle', 'exceptionHandle'));
        register_shutdown_function(array('ErrorHandle', 'shutdownHandle'));
    }
    
    /**
     * 通用报错处理
     * @param 类名
     * @return null
     */
    public static function allHandle ($errno, $errstr, $errfile, $errline, $errcontext=[], $errtrace='')
    {
        if (!defined('ERROR_LEVEL') || $errno & ERROR_LEVEL) {
            $errormsg =  "Error: {$errstr} in $errfile:$errline";
            if ($errtrace) {
                $errormsg .= PHP_EOL . 'Stack trace:' . PHP_EOL . $errtrace;
            }
            LogFile::addLog($errno, array(), 'php_error', $errormsg);
        }
        OutPut::fail($errstr);
    }
    
    /**
     * Fatal Error（致命错误）捕获
     */
    public static function shutdownHandle ()
    {
        $e = error_get_last();
        //交给通用报错处理
        if ($e) {//正常退出也会执行到这里，如果没有错误，不需要进行处理
            self::allHandle($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    public static function exceptionHandle ($exception)
    {
        $errno = $exception->getCode() ?: 2;
        $errstr = $exception->getMessage();
        $errfile = $exception->getFile();
        $errline = $exception->getLine();
        $errtrace = $exception->getTraceAsString();

        self::allHandle($errno, $errstr, $errfile, $errline, [], $errtrace);
    }
}


?>