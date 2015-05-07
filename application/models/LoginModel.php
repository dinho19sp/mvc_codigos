<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 04/09/14
 * Time: 16:16
 */
error_reporting(E_ALL);
class LoginModel extends phpDataReader{

    /*
     *  Armazena  instancia de sessao
     */
    private $_session;


    private function getSession()
    {
        $this->_session = new SessionStart(TRUE);
        return $this->_session;
    }

    public function getLoginUser($user)
    {
       $retun = $this->queryDataProcedure("SP_MT_GET_USER_LOGIN",array($user));

        $retorno = $retun[0];

       return $retorno;
    }



    public function getLoggin($user,$pass)
    {
        $this->queryDataProcedure("SP_CHECK_LOGIN",array($user,$pass));

         return $this->_LoadDataSp;
    }


} 