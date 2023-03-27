<?php
/**
 * Created by PhpStorm.
 * User: danielhemmerich
 * Date: 09.08.18
 * Time: 20:55
 */

use JetBrains\PhpStorm\NoReturn;

error_reporting(E_ALL);
set_error_handler("errorHandler");
set_exception_handler("exceptionHandler");
//ini_set("memory_limit", "-1");

/**
 * @param string $message
 */
function logError(string $message): void
{
    error_log(trim($message));

    file_put_contents(
        __DIR__ . '/../logs/errors.' . date('Y-m-d') . '.log',
        '[' . date('r') . '] ' . trim($message) . "\n",
        FILE_APPEND
    );
}

/**
 * @param int $code
 * @param string $message
 * @param string $file
 * @param int $line
 * @return mixed
 * @throws ErrorException
 */
function errorHandler(
	int $code,
	string $message,
	string $file,
	int $line
): mixed
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
#[NoReturn]
function exceptionHandler(
	Throwable $throwable
): void
{
    logError($throwable);
    exit($throwable->getMessage() . "\n");
}