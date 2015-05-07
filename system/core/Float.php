<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 16/04/15
 * Time: 15:01
 */

class Float {


    protected static  $_float;


    public static function isFloat($float)
    {
        try
        {
            if(is_float($float))
            {
                self::$_float = $float;
                return self::$_float;
            }
            else
            {
                throw new Exception("Valor passado não é um float ou double");
            }

        }catch (Exception $e)
        {
            echo($e->getMessage());
        }

    }

} 
