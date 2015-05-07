<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 05/06/14
 * Time: 09:40
 */

class FacebookWebConfig extends Facebook{

    private $_app;
    private $_secret;



    public function registerAppId()
    {
        $this->_app = new Helpers();

        if(is_object($this->_app)){

            $appid = get_object_vars($this->_app->xmlData("app.host.config"));

        }

          return (string) $appid['facebook']->app_id;


    }



    public  function registerAppSecret()
    {
        $this->_secret = new Helpers();

        if(is_object($this->_secret)){

            $secret = get_object_vars($this->_secret->xmlData("app.host.config"));

        }

        return (string)$secret['facebook']->app_secret;
    }



} 