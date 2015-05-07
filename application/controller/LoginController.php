<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 04/09/14
 * Time: 16:16
 */

class LoginController extends LoginModel{

    private $_view;
    private $_message;
    public $_response = array();

    public function index()
    {

        $this->_view = new View(PATH_VIEW."login.phtml");

        $this->_view->showContents();

    }


    public function LogarAction()
    {
        $session = Registry::getInstance("SessionStart");

        $data = new phpDataReader();
        $host = new ElementosRoot();
        $_encrypt = new Encryption();

        $response = array();
        // Fields Submitted
        $username = $data->__post("username");
        $password = $data->__post("password");

        $usuario = $this->getLoginUser($username);


        if(Bcrypt::check($password,$usuario->user_pass)  == true)
        {
            $_Sql = $this->getLoggin($username,$usuario->user_pass);




        $response = $_Sql[0];

        // This array of data is returned for demo purpose, see assets/js/neon-forgotpassword.js
        $this->_response['submitted_data'] = $_POST;


        $login_status = 'invalid';

        if(isset($response->user_login) && isset($response->user_pass))
        {

            if($response->user_login == $username && Bcrypt::check($password,$response->user_pass) == true)
            {
                $login_status = 'success';

                $host->setCookieSite(
                    array(
                        "CM_USE_DI"=> $_encrypt->encrypt($response->id_usuario),
                        "CM_USE_NM" => $_encrypt->encrypt($response->user_login),
                        "REM" => 1
                    )
                );

            }
        }


        $this->_response['login_status'] = $login_status;
        // Login Success URL
        if($login_status == 'success')
        {


            $session->setVars("USER_ID",$_encrypt->encrypt($response->id_usuario));
            $session->setVars("USER_TOKEN",$_encrypt->encrypt($response->id_usuario));
            $session->setVars("USER_GROUP",$_encrypt->encrypt($response->user_grupo));
            $session->setVars("USER_PERF",$_encrypt->encrypt($response->id_perfil));



            // Set the redirect url after successful login
            $this->_response['redirect_url'] = $host->getHost();
        }


        echo json_encode($this->_response);
        }
    }

    public function EncerrarSistemaAction()
    {

        $session = Registry::getInstance('SessionStart');
        $session->SessionDestroy(True);

        ob_clean();

        Application::redirect($_COOKIE['data_url']);

    }

} 