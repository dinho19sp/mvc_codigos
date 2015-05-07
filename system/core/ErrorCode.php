<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 26/03/15
 * Time: 11:13
 */

final class ErrorCode {

    public static function setError($code,$queryString=NULL)
    {
        if($code)
        {
            $cd = unserialize($code);

            if($queryString != NULL)
            {
                $string = "Query:: ".$queryString;
            }
            else
            {
                $string = NULL;
            }

            throw new Exception("".$cd["MSG"]." ".$string."",(int)$cd["CODE"]);

        }
    }
 }
