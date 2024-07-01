<?php
class ErrorManager
{
    public function __construct(){}
    public function getExceptionLog(Exception|Error $exception, string $className): string
    {
        $message = '(Error Log | '.date('Y-m-d H:i:s').'):'.PHP_EOL;
        switch ($exception)
        {
            case ($exception instanceof mysqli_sql_exception):
                $message .= '[MySQLi | '.$className.' | Error] => Error in '.$exception->getFile().PHP_EOL;
                $message .= '[MySQLi | '.$className.' | Error] => Line: '.$exception->getLine().PHP_EOL;
                $message .= '[MySQLi | '.$className.' | Error] => Message: '.$exception->getMessage().PHP_EOL;
                $message .= '[MySQLi | '.$className.' | Error] => Code: '.$exception->getCode().PHP_EOL;
                $message .= '[MySQLi | '.$className.' | Error] => SQL State: '.$exception->getSqlState().PHP_EOL;
                break;
            case ($exception instanceof DivisionByZeroError):
                $message .= '[Math | '.$className.' | Error] => Error in '.$exception->getFile().PHP_EOL;
                $message .= '[Math | '.$className.' | Error] => Line: '.$exception->getLine().PHP_EOL;
                $message .= '[Math | '.$className.' | Error] => Message: '.$exception->getMessage().PHP_EOL;
                $message .= '[Math | '.$className.' | Error] => Code: '.$exception->getCode().PHP_EOL;
                break;
            default:
                $message .= '[Undefined | '.$className.' | Error] => Error in '.$exception->getFile().PHP_EOL;
                $message .= '[Undefined | '.$className.' | Error] => Line: '.$exception->getLine().PHP_EOL;
                $message .= '[Undefined | '.$className.' | Error] => Message: '.$exception->getMessage().PHP_EOL;
                $message .= '[Undefined | '.$className.' | Error] => Code: '.$exception->getCode().PHP_EOL;
        }
        if ($message !== '')
        {
            file_put_contents('error_log.log', $message.PHP_EOL, FILE_APPEND);
        }
        return $message;
    }
}