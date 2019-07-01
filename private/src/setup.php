<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 09.08.18
 * Time: 20:55
 */

error_reporting(E_ALL);
set_error_handler("errorHandler");
set_exception_handler("exceptionHandler");
//ini_set("memory_limit", "-1");

/**
 * @param string $message
 */
function logError(string $message) {
    error_log(trim($message));

    file_put_contents(
        __DIR__ . '/../logs/errors.' . date('Y-m-d') . '.log',
        '[' . date('r') . '] ' . trim($message) . "\n",
        FILE_APPEND
    );
}

/**
 * @param $iCode
 * @param $sMessage
 * @param $sFile
 * @param $iLine
 */
function errorHandler(
	$code,
	$message,
	$file,
	$line
)
{
    throw new ErrorException(
        $message,
        $code,
        1,
        $file,
        $line
    );
}

/**
 * @param Throwable $throwable
 */
function exceptionHandler(
	Throwable $throwable
)
{
    logError($throwable);
    exit($throwable->getMessage() . "\n");
}