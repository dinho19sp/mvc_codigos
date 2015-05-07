<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 27/05/14
 * Time: 22:44
 *  
 * 
 *
 */



class ElementosRoot {

    private  $_getCss;
    private  $_path_css_default;
    private  $__path_js_default;
    private  $_getJsc;
    private  $_getImg;
    private  $_getPlugins;
    public   $_getRootSite;
    public   $cookiePath;
    private  $_url = NULL;
    private  $_explode;
   

    /**
     * @param $hostnames recebe uma string que define qual ambiente esta sendo usado para a aplicação<br />
     * Strings:<br />"host_local"  => Ambiente local (localhost)<br />"host_teste" => Ambiente de Homologacao (http://hosts de homolgacao)<br />"host_producao" => Ambiente de producao (host de hospedagem);
     */

    public function __construct($host = NULL)
    {
        
        if($host != "" OR $host != NULL)
        {

             return $this->setHost($host = NULL);

        }
       

    }

   
    public function setHost($host = NULL)
    {
        $session = Registry::getInstance('SessionStart');
        $session->setVars('ROOT_ENVIRONMENT',$host);


        $xmlConfigHost = new Connection();

        if($session->getVars('ROOT_ENVIRONMENT')) {

            switch($host)
            {
                case "host_local":
                    $this->_getRootSite = $xmlConfigHost->getDataLocal();
                    break;
                case "host_developer":
                    $this->_getRootSite = $xmlConfigHost->getDataDevloper();
                    break;
                case "host_producao":
                    $this->_getRootSite = $xmlConfigHost->getDataProduction();
                    break;
                case "host_facebook":
                    $this->_getRootSite = $xmlConfigHost->getDataFacebook();
                    break;
            }

            return $this->_getRootSite;

        }


    }

    public  function getHost()
    {
        $session = Registry::getInstance('SessionStart');
        $this->setHost($session->getVars('ROOT_ENVIRONMENT'));
        return $this->_getRootSite;
    }

    /**
     *  get_css($FileName,$Path=NULL,$externo=FALSE);
     *
     * @param      $FileName  Espera um nome de arquivo css sem sua extensao
     * @param null $Path      Caminho onde o arquivo css esta armazenado
     * @param bool $externo   por padrao e FALSE, caso true o nome do arquivo recebe a url completa do arquivo, $path não altera continua NULL e $externo deve ser TRUE
     */
    public  function get_css($FileName,$Path=NULL,$externo=FALSE)
    {
        if($Path == "")
        {
            $this->_path_css_default = PATH_CSS;
        }
        else
        {
            if(!is_dir($Path))
            {
                user_error("CODE - 404: {$Path} n&atilde;o &eacute um diretorio",E_USER_WARNING);
            }
            else
            {
                $this->_path_css_default = $Path;
            }

        }
        if($externo==TRUE)
        {
            $this->_getCss =    "<link rel=\"stylesheet\" href=\"$FileName\">";
        }
        else
        {
            if($this->getHost().$this->_path_css_default.$FileName.".css")
            {
                $this->_getCss =    "<link rel=\"stylesheet\" href=\"".$this->getHost().$this->_path_css_default.$FileName.".css\">";


            }
            else
            {
                user_error("CODE - 404: ".$this->getHost().$this->_path_css_default.$FileName.".css n&atilde;o encontrado ou incorreto",E_USER_NOTICE);
            }
        }

        printf("%s","\t".$this->_getCss."\n");

    }


    /**
     *  get_jsc($FileName,$Path=NULL,$externo=FALSE);
     *
     * @param      $FileName  Espera um nome de arquivo css sem sua extensao
     * @param null $Path      Caminho onde o arquivo css esta armazenado
     * @param bool $externo   por padrao e FALSE, caso true o nome do arquivo recebe a url completa do arquivo, $path não altera continua NULL e $externo deve ser TRUE
     */
    public  function get_jsc($FileName,$Path=NULL,$externo=FALSE)
    {

        if($Path == "")
        {
            $this->_path_js_default = PATH_JSC;
        }
        else
        {
            if(!is_dir($Path))
            {
                user_error("CODE - 404: {$Path} n&atilde;o &eacute um diretorio",E_USER_WARNING);
            }
            else
            {
                $this->_path_js_default = $Path;
            }

        }
        if($externo==TRUE)
        {
            $this->_getJsc =    "<script src=\"".$FileName."\"></script>";
        }
        else
        {
            if($this->getHost().$this->_path_css_default.$FileName.".js")
            {
                $this->_getJsc =    "<script src=\"".$this->getHost().$this->_path_js_default.$FileName.".js\"></script>";


            }
            else
            {
                user_error("CODE - 404: ".$FileName.".js n&atilde;o encontrado ou incorreto",E_USER_NOTICE);
            }
        }

        printf("%s","\t".$this->_getJsc."\n");

    }

    /** Adiciona o link para a pasta plugin */
    public  function getPlugins()
    {

        $this->_getPlugins = $this->getHost().PATH_PLUGINS;

        return $this->_getPlugins;
    }

    /** Adiciona o link para a pasta imagens */
    public function getImg()
    {

        $this->_getImg = $this->getHost().PATH_IMAGES;

        return $this->_getImg;
    }

    public static function _set($value)
    {
        $global = $GLOBALS[$value];

       return $global;
    }

    private function _setUrl()
    {
        $this->_url = (isset($_GET['key'])? $_GET['key']: NULL);

        $this->_explode = explode("/",$this->_url);


    }

    public function setPathCookie()
    {

        $path = explode("//",$this->getHost());

        $pathCookie = explode("/",$path[1]);

        $this->cookiePath = "/".$pathCookie[1]."/";

    }

    public function getPathCookie()
    {

        return $this->cookiePath;
    }

    public function setCookieSite(array $data){

        $key = array_keys($data);
        $val = array_values($data);
        $this->setPathCookie();
        $s = array_combine($key,$val);

        foreach ($s as $k => $v){

            setcookie($k,$v,strtotime( '+30 days' ),$this->cookiePath);
        }

    }

    public function removeCookieSite(array $data){

        $key = array_keys($data);
        $val = array_values($data);

        $this->setPathCookie();
        $s = array_combine($key,$val);

        foreach ($s as $k => $v){

            unset($_COOKIE[$v]);


          setcookie($v,NULL,time()-3600,$this->cookiePath);

        }

    }
    public function getCookie($data)
    {


            return $_COOKIE[$data];




    }
}