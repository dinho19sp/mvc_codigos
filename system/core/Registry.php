<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 26/05/14
 * Time: 17:03
 */



class Registry {

    protected static $instance;
    protected function __construct(){}

    public static function getInstance($className)
    {
        if(class_exists($className))
        {
             return self::$instance = new $className;

        }else{

            die("error");
        }
    }

    public static function sendMessageLog(phpDataReader $arrException,$ExceptionVar)
    {

        $msg = $arrException->phpException($ExceptionVar,$ExceptionVar->getMessage());
        $log = new LoggerTXT("error_log");
        $log->message("CODE: ".$ExceptionVar->getCode() ." ".$msg[0]." Eexception : EM ".$msg[1]." -> :: Error msg -> ".$ExceptionVar->getMessage()." ]")->write();
        $msgLog =  " <span>Error: ".$ExceptionVar->getCode()." :: Ops!! algo inesperado ocorreu, por favor informe ao Administrador do sistema o Código do erro e a hora em que ocorreu -> ".date("H:i:s")." </span>";
        die($msgLog);

    }

} 