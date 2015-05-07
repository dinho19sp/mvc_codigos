<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application
 *
 * @author dinho19sp
 */
class Application
{
    private       $_url;
    private       $_explode;
    protected     $_controller;
    protected     $_action;
    public        $_parametros;
    protected     $_instance;
    public static $_breadcrumbs;

    public function __construct()
    {
        $this->setUrl();
        $this->setExplode();
        $this->setController();
        $this->setAction();
        $this->setParam();

    }

    public function setUrl()
    {
        $session = Registry::getInstance("SessionStart");

        if(!$session->getVars('USER_ID') && !$session->getVars('USER_TOKEN'))
        {
            $url = "login/";
        }
        else
        {
            $url = "dashboard/";
        }

        $this->_url = (isset($_GET['key']) ? $_GET['key'] : $url);

    }

    private function setExplode()
    {
        $this->_explode = explode("/", $this->_url);
    }

    private function setController()
    {
        $this->_controller = DataFilter::removeAcentos($this->_explode[0]);
    }

    private function setAction()
    {
        if (isset($this->_explode[1])) {
            $this->_action = $this->_explode[1];
        }
    }

    private function setParam()
    {
        unset($this->_explode[0], $this->_explode[1]);
        if (end($this->_explode) == NULL) {
            array_pop($this->_explode);
        }
        $i = 0;
        if (!empty($this->_explode)) {
            foreach ($this->_explode as $val) {
                if ($i % 2 == 0) {
                    $ind[] = $val;
                } else {
                    $value[] = $val;
                }
                $i++;
            }
        } else {
            $ind = array();
            $value = array();
        }

        if (count($ind) == count($value) && !empty($ind) && !empty($value)) {
            $this->_parametros = array_combine($ind, $value);
        } else {
            $this->_parametros = array();
        }

    }

    public function getParam($name = NULL)
    {
        if ($name != NULL) {
            return $this->_parametros[$name];
        } else {

            return $this->_parametros;
        }
    }

    public function Dispatch()
    {

        /* verifica se o controller existe */

        $controller = explode("-", $this->_controller);

        foreach ($controller as $key => $val) {
            $key = $val;
            $cont[] = ucwords(strtolower($key));
            $controlador = implode("", $cont);
        }


        $_controller_file = PATH_CONTROLLER . DataFilter::removeAcentos($controlador) . 'Controller.php';


        if (is_file($_controller_file) && file_exists($_controller_file)) {

            require_once "{$_controller_file}";


        } else {

            //die("Um arquivo não {$_controller_file} foi encontrado");
            $view = new View(PATH_VIEW."404.phtml");
            $view->showContents();

            header("HTTP/1.1 404 Not Found");
        }

        /* verificando se a classe existe */

        $_class = $this->_controller . 'Controller';

        if (class_exists($_class)) {

            $this->_instance = new $_class;

        } else {

            die('A pagina solicitada não foi encontrada ou há a falta de argumentos para acessa-la');
        }

        /* verificando se o metodo Existe */

        $acao = explode("-", $this->_action);

        foreach ($acao as $key => $val) {
            $key = $val;
            $ac[] = ucwords(strtolower($key));
            $action = implode("", $ac);
        }


        $_method = $action . 'Action';

        if (method_exists($this->_instance, $_method)) {

            $this->_instance->$_method();

        } else {

            $this->_instance->index();
        }



    }


    /**
     *   Redireciona a chamada http para outra página
     * @param string $_URL
     */

    static function redirect($_URL)
    {
        header("Location:" . $_URL);
    }


}