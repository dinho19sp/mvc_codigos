<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 15/04/15
 * Time: 14:12
 */

class Integer {

    protected static  $_int;


    public static function Int($int)
    {

        try
        {
            if(is_int($int))
            {
                self::$_int = $int;
                return self::$_int;
            }
            else
            {
                throw new Exception("Valor passado não é um inteiro");
            }


        }catch (Exception $e)
        {
            echo($e->getMessage());
        }

    }
} 