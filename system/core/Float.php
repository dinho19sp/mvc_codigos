<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 16/04/15
 * Time: 15:01
 */

class Float {


    protected static  $_float;


    public static function Int($float)
    {
        try
        {
            if(is_int($float))
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