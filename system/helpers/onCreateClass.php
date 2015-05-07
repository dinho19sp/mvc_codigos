<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 10/09/14
 * Time: 15:18
 */

class onCreateClass {

    public function getElementosRoot()
    {
        $elem = new ElementosRoot();

        return $elem;
    }

    public function getApplication()
    {
        $app = new Application();

        return $app;
    }

    public function getFunctions()
    {
        $fnc = new Functions();

        return $fnc;
    }

} 