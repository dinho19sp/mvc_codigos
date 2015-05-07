<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 30/05/14
 * Time: 15:51
 */


class FacebookWeb extends FacebookWebConfig{

    /* Guardar o id do aplicativo do facebbok que esta em uso*/

    private $_app_id;

    /* Guardar o app_secret do aplicativo em uso */

    private $_app_secret;

    /* Guardar o numero do token do facebook */

    private $_app_token;

    /* Guardar o id do usuario loga pelo o facebook */

    public  $_user_id;

    /* Retorna a lista dos amigos do usuario logado*/

    public  $_user_friends;


    public function __construct(){

        $this->setAppId($this->registerAppId());
        $this->setAppSecret($this->registerAppSecret());

    }




    private function setAppToken($token)
    {
        $this->_app_token = $token;
    }

    private function getAppToken()
    {
        return $this->_app_token;
    }


    public function setAppSecret($secret)
    {
        $this->_app_secret= $secret;
    }

    public function getAppSecret()
    {
        return $this->_app_secret;
    }

    public function setAppId($id)
    {
        $this->_app_id = $id;
    }

    public function getAppId()
    {
        return $this->_app_id;
    }



}