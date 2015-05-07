<?php

/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 28/05/14
 * Time: 18:08
 */
class Functions extends phpDataReader
{

    private static $_setController;
    private static $_setAction;
    private static $_args;
    private static $_setUrl;
    private $_url;
    private $_allowed_functions = array();
    private $_parameter;

    private function app()
    {
        $app = new onCreateClass();

        return $app;
    }

    public static function getUserId()
    {
        $session = Registry::getInstance('SessionStart');
        $enc = new Encryption();
        $id = $session->getVars('USER_ID');
        $idUsuario = $enc->decrypt($id);
        return (int)$idUsuario;
    }

    public static function getOpusKey()
    {
        $session = Registry::getInstance('SessionStart');
        $enc = new Encryption();
        $id = $session->getVars('OPUS_KEY');
        $idObra = $enc->decrypt($id);
        return (int)$idObra;
    }

    public static function getUserGroup()
    {
        $session = Registry::getInstance('SessionStart');
        $enc = new Encryption();
        $id = $session->getVars('USER_GROUP');
        $idGroup = $enc->decrypt($id);;
        return (int)$idGroup;
    }

    public static function getUserPerfil()
    {
        $session = Registry::getInstance('SessionStart');
        $enc = new Encryption();
        $id = $session->getVars('USER_PERF');
        $idGroup = $enc->decrypt($id);;
        return (int)$idGroup;
    }

    private function getActionsUser()
    {
        $_Filter = new phpCriteria();
        $_Filter->add(new phpFilter('Pr.id_usuario', '=', Functions::getUserId()));

        $sql = $this->queryDataSelect('tb_actions_permissao Pr LEFT JOIN tb_actions Ac USING(id_acao)', 'Ac.prefixo_acao', $_Filter->dump(), null, null);
        $actions = $this->Read($sql);

        return $actions;

    }

    public function is_action($action)
    {
        $actions = $this->getActionsUser();

        if ($actions != "") {
            foreach ($actions as $ac_index => $ac_value) {
                $ac_ret[] = $ac_value->prefixo_acao;
            }
        }
        if (in_array($action, $ac_ret, true)) {
            return true;
        } else {
            return false;
        }
    }

    private function setPermitido()
    {
        $idUser = self::getUserId();
        $_filter = new phpCriteria();


        $url = $_GET['key'];
        $url_permitido = explode("/", $url);
        $controller = $url_permitido[0];
        $action = $url_permitido[1];

        if ($action) {
            $this->_url = $controller . "/" . $action . "/";
        } else {
            $this->_url = $controller . "/";
        }

        $_filter->add(new phpFilter('pm.id_usuario', "=", $idUser));
        $_filter->add(new phpFilter('t.link_sub_menu', "=", $this->_url));

        $sql_get_pagina = $this->queryDataSelect('tb_menu_permissao pm LEFT JOIN tb_menu_sub t ON pm.id_sub_menu=t.id_sub_menu', 't.id_sub_menu,t.link_sub_menu', $_filter->dump(), NULL, NULL);
        $qry = $this->queryDataRow($sql_get_pagina, phpDataReader::OBJ);

        $idPage = "";

        if ($qry) {
            $idPage = (int)$qry->id_sub_menu;

        }


        $sql = self::queryDataProcedure('SP_MT_GET_FUNCOES_PAG_PERMITIDA', array($idUser, $idPage));

        $this->_allowed_functions = $sql;

        return $this->_allowed_functions;

    }

    public function permitido($_args)
    {
        $alloweds = $this->setPermitido();

        if ($alloweds != "") {
            foreach ($alloweds as $k => $v) {
                $return[] = $v->prefixo_acao;
            }


            $this->_parameter = $_args;

            if ($this->_parameter == NULL || $this->_parameter == "") {
                $error = "E401 - O parametro nao foi informado para setar a permissao na pagina";

                die("Um algo de errado aconteceu por favor se isto persistir contacte o administrado do sistema e informe o codigo :: E401");


                exit;
            } else {
                if (in_array($this->_parameter, $return, true)) {
                    return true;

                } else {

                    return false;
                }
            }
        }

    }

    public function getCategoriasPermitidas()
    {
        $sql_files_categorias = $this->queryDataSelect('tb_perfil_categoria pc LEFT JOIN tb_perfil pf ON (pc.id_perfil = pf.id_perfil)', 'pc.id_perfil,pf.user_grupo,pc.id_categoria', 'pf.user_grupo =' . self::getUserGroup() . ' AND pf.id_perfil =' . self::getUserPerfil() . '', null, null);
        $qry_files_categorias = $this->Read($sql_files_categorias);

        foreach ($qry_files_categorias as $f => $vf) {
            $perfil_categ[] = $vf->id_categoria;
        }

        $categorias = implode(",", $perfil_categ);

        return $categorias;

    }

    public function getNomeUsuarioId($id)
    {
        $sql = $this->queryDataSelect('tb_usuarios', 'nome_usuario', 'id_usuario =' . $id);
        $query = $this->queryDataRow($sql, phpDataReader::OBJ);

        return $query->nome_usuario;
    }

    public function blockPage()
    {
        $session = Registry::getInstance('SessionStart');
        $group = $session->getVars('USER_GROUP');

        $this->_filter = new phpCriteria();

        $this->_filter->add(new phpFilter('pm.id_usuario', "=", self::getUserId()));
        //$this->_filter->add(new phpFilter('t.tipo_pagina',"=","P"));
        $this->_filter->add(new phpFilter('t.id_status', "=", 1), phpExpression::AND_OPERATOR);

        $sql = $this->queryDataSelect('tb_menu_permissao pm
                LEFT JOIN
                tb_menu_sub t ON pm.id_sub_menu=t.id_sub_menu',
            NULL, $this->_filter->dump(),
            NULL, null);

        $qry = $this->Read($sql);

        $controllers = "";
        $pages = "";
        if ($qry) {
            foreach ($qry as $key => $pg) {
                $explode = explode("/", $pg->link_sub_menu);
                $controllers[] = $explode[0];
                $pages[] = $explode[1];

            }
        }


        $this->addFieldArray($pages, 'meus-dados');
        $this->addFieldArray($pages, 'midias');
        $this->addFieldArray($pages, 'midia-detalhes');
        $this->addFieldArray($pages, 'upload');
        $this->addFieldArray($pages, 'minhas-notificacoes');

        //$this->addFieldArray($controllers,'usuarios');


        $host = new ElementosRoot();
        $url = $_GET['key'];
        $url_permitido = explode("/", $url);
        $controller = $url_permitido[0];
        $action = $url_permitido[1];


        if (in_array($controller, $controllers) && in_array($action, $pages)) {


        } else {

            $session = Registry::getInstance('SessionStart');

            $session->setVars('return', 1);

            Application::redirect($host->getHost() . "sistema/error/");

        }


    }

    public static function is_logado()
    {
        $session = Registry::getInstance("SessionStart");
        $host = new ElementosRoot();

        if (!$session->getVars("USER_ID") OR $session->getVars("USER_ID") == "") {
            Application::redirect($host->getHost());
            exit;
        }
    }

    public static function transformValor($value, $type)
    {
        try {
            $ponto = ".";
            $virgula = ",";

            if (!is_int($type)) {
                throw new Exception("O tipo informado em " . __FUNCTION__ . " n&atildeo &eacute; um tipo valido");
            } else {
                switch ($type) {
                    case 1:
                        $source = array('.', ',');
                        $replace = array('', '.');
                        $noVirgula = str_replace($source, $replace, $value);
                        break;

                    case 2:

                        $noVirgula = number_format($value, 2, ",", ".");
                        break;
                }

                return $noVirgula;
            }
        } catch (Exception $e) {

            $e->getMessage();
        }

    }

    public static function breadcrumb()
    {

        $phpClass = new phpDataReader();
        $urlHome = new ElementosRoot();

        $url = $_GET['key'];

        $explode = explode("/", $url);

        $controlle = $explode[0];
        $action = $explode[1];

        if ($action != "")
        {
            $thisUrl = $controlle . "/" . $action . "/";
        }
        else
        {
            $thisUrl = $controlle . "/";
        }


        $sql = $phpClass->queryDataSelect('tb_menu_sub sub LEFT JOIN tb_menu mn USING(id_menu)', 'sub.link_sub_menu,sub.id_menu,sub.id_sub_menu,sub.titulo_sub_menu,mn.titulo_menu,mn.link_menu', 'link_sub_menu ="' . $thisUrl . '"', null, null);
        $qry = $phpClass->queryDataRow($sql, phpDataReader::OBJ);


        if ($qry->titulo_sub_menu == $qry->titulo_menu)
        {
            $link = '<li><a href="' . $urlHome->getHost() . '/' . $qry->link_menu . '">' . $qry->titulo_menu . '</a></li>';
        }
        else
        {
            if (isset($qry->link_menu))
            {
                $linkMenu = $qry->link_menu;
            }
            else
            {
                $linkMenu = $qry->link_sub_menu;
            }
            $link = '<li><a href="' . $urlHome->getHost() . $linkMenu . '">' . $qry->titulo_menu . '</a></li><li><a href="' . $urlHome->getHost() . $qry->link_sub_menu . '">' . $qry->titulo_sub_menu . '</a></li>';
        }

        $bread = '<ol class="breadcrumb">
                  <li>
                  <a href="' . $urlHome->getHost() . '"> <i class="entypo-folder"></i> Home </a> </li>
                      ' . $link . '
                  </ol>';

        echo $bread;


    }

    public function __go($url)
    {
        echo "<script type='text/javascript'>
                window.location='{$url}';
            </script>";
    }

    public function get_web_page($url, $curl_data = '')
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_POST => 1, // i am sending post data
            CURLOPT_POSTFIELDS => $curl_data, // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0, // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false, //
            CURLOPT_VERBOSE => 1 //
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        //  $header['errno']   = $err;
        //  $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }



    public function getMessageScript($msg)
    {
        $ms = "<script type='text/javascript'>fn_xjx_MessageBoxShow('" . $msg . "',2,2);</script>";
        echo $ms;

    }

    public function returnMessageScript($msg)
    {
        $ms = "<script type='text/javascript'>fn_xjx_MessageBoxShow('" . $msg . "',6,6,'','fn_backPage()');</script>";

        echo $ms;

    }

    public static function goToUrl($route, $action, array $param = NULL)
    {
        $url_param = Registry::getInstance('ElementosRoot');

        //Recebe o nome do Controlador
        if ($route != "") {

            self::$_setController = $route . "/";

        } else {
            self::$_setController = "";
        }

        // Recebe o nome do action
        if ($action != "") {

            self::$_setAction = $action . "/";

        } else {
            self::$_setAction = "";
        }

        // Outros argumentos passados como array('item1' => valor1,...)

        if ($param != "") {

            if (is_array($param)) {
                foreach ($param as $key => $value) {

                    $arg[] = $key . "/" . $value;

                }
            }

            self::$_args = implode("/", $arg);

        } else {

            self::$_args = "";

        }

        self::$_setUrl = $url_param->getHost() . self::$_setController . self::$_setAction . self::$_args;


        return self::$_setUrl;
    }

    public static function gravaSession(array $data)
    {

        $session = Registry::getInstance('SessionStart');

        foreach ($data as $ind => $val) {

            $session->setVars($ind, $val);

        }

    }

    //Limita o tamanho do texto, colocando reticências no final
    public static function limit_text($txt, $limit)
    {

        $size = strlen($txt);

        if ($size <= $limit) {
            $novo_txt = $txt;
        } else {
            $novo_txt = trim(substr($txt, 0, $limit)) . "...";

        }

        return $novo_txt;

    }

    public static function fncDataPadrao($data)
    {
        $data_formatada = "";

        if ($data == "" || $data == '0000-00-00 00:00:00' || $data == '0000-00-00') {
            $data_formatada = "";
        } else {
            $data = str_replace(array("/", ".", "-"), "/", $data);
            $array_data = explode('/', $data);

            (int)$dia = $array_data[1];
            (int)$mes = $array_data[2];
            (int)$ano = $array_data[0];

            $data_formatada = date("d/m/Y", mktime(0, 0, 0, $dia, $mes, $ano));
        }
        return $data_formatada;
    }

    public static function fncDataInversa($data)
    {
        if ($data == "") {
            $data_formatada2 = "00/00/00";
        } else {
            $data = str_replace(array("/", ".", "-"), "/", $data);
            $array_data = explode('/', $data);
            $data_formatada2 = date("Y-m-d", mktime(0, 0, 0, $array_data[1], $array_data[0], $array_data[2]));
        }
        return $data_formatada2;
    }

    public static function fncDataformat($str)
    {
        $str = preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/i", "$3-$2-$1", $str);
        return $str;
    }

    public static function fncDataformatPadrao($str)
    {
        $str = preg_replace("/(\d{2,4})-(\d{2})-(\d{2})/i", "$3/$2/$1", $str);
        return $str;
    }

    public static function hora($hora)
    {
        $arrayH = explode(':', $hora);
        return $arrayH[0] . ':' . $arrayH[1];
    }

    //calcula a distancia em KM entre a origem e o destino pela latitude e a longitude
    public static function calcDistancia($lat_origem, $lng_origem, $lat_destino, $lng_destino)
    {

        if ($lat_origem != '' && $lng_origem != '' && $lat_destino != '' && $lng_destino != '') {
            //Formula
            $distancia = 6371 * acos(cos(M_PI * (90 - $lat_destino) / 180) * cos((90 - $lat_origem) * M_PI / 180) + sin((90 - $lat_destino) * M_PI / 180) * sin((90 - $lat_origem) * M_PI / 180) * cos(($lng_origem - $lng_destino) * M_PI / 180));

            return round($distancia, 4);
        }

    }

    public function TotalArquivosObras($idObra)
    {
        $_Filter = new phpCriteria();

        $tb = "tb_midia_obra";
        $_Filter->add(new phpFilter("id_obra", "=", $idObra));


        $sql = $this->queryDataSelect($tb, " COUNT(id_midia) as Total", $_Filter->dump());
        $query = $this->queryDataRow($sql, phpDataReader::OBJ);

        if ($query->Total > 0) {
            $TotalArquivos = $query->Total;
        } else {
            $TotalArquivos = 0;
        }

        return $TotalArquivos;

    }

    public function getGroupImage($idGroup)
    {

        switch ($idGroup) {
            case 1:
                $_Grupo = "Administrador";
                $image = "administrador.png";
                break;
            case 2:
                $_Grupo = "Gerente";
                $image = "gerente.png";
                break;
            case 3:
                $_Grupo = "Assistente";
                $image = "assistente.png";
                break;
            case 4:
                $_Grupo = "Cliente";
                $image = "cliente.png";
                break;
            case 5:
                $_Grupo = "Fornecedor";
                $image = "fornecedor.png";
                break;
            case 6:
                $_Grupo = "Projetista";
                $image = "projetista.png";

                break;

        }
        $img = "<img src='" . $this->app()->getElementosRoot()->getHost() . "includes/images/" . $image . "' width='60' height='60' class='tooltip-secondary'
                 data-toggle='tooltip' data-placement='top' title='" . $_Grupo . "' data-original-title='" . $_Grupo . "'>";

        return $img;
    }

    public function getImg($tabela, $idProp = NULL)
    {
        $srcid = NULL;

        if ($srcid != NULL) {

            $srcid = $this->app()->getApplication()->getParam('srcid');
        }

        if ($idProp == NULL || $idProp == "") {
            $id = $srcid;
        } else {
            $id = $idProp;
        }

        $_Filter = new phpCriteria();
        $_Filter->add(new phpFilter('id_proprietario', "=", $id));
        $_Filter->add(new phpFilter('proprietario', "=", $tabela), phpExpression::AND_OPERATOR);

        $sql = $this->queryDataSelect('tb_images', NULL, $_Filter->dump());
        $query = $this->queryDataRow($sql, phpDataReader::OBJ);


        if ($tabela == 'tb_obras') {
            $no_image = $this->app()->getElementosRoot()->getHost() . 'includes/images/obra-sem-imagem-bg.jpg';
        } else {
            $no_image = $this->app()->getElementosRoot()->getHost() . 'includes/images/noavatar.gif';
        }


        if (!$query) {
            return $no_image;
        } else {
            return $this->app()->getElementosRoot()->getHost() . $query->path_image . $query->nome_image;
        }

    }

    public static function getFileExt($file)
    {

        $midia = end(explode(".", $file));
        $f = strtolower($midia);
        return $f;

    }

    public static function checkMidia($midia)
    {

        $header = @get_headers($midia);

        if (!$midia || $header[0] == 'HTTP/1.1 404 Not Found' || $header === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $tabela recebe nome da tabela que ira retornar os dados antes de qualquer ação
     * @param $idAffected recebe o id do registro da tabela que tera a ação {Cadastrar, Editar, Deletar}
     * @return string retorna os dados do registro serializados
     *
     */
    public function xTriggerBefore($tabela, $idAffected)
    {
        $id = $this->getPrimaryKey($tabela);

        $sql = $this->queryDataSelect($tabela, null, $id . " = " . $idAffected, null, null);
        $query = $this->queryDataRow($sql, phpDataReader::OBJ);

        $return = utf8_decode(serialize($query));

        return $return;
    }

    public function xTriggerAfter($tabela, $LastId)
    {
        $id = $this->getPrimaryKey($tabela);
        $sql = $this->queryDataSelect($tabela, null, $id . " = " . $LastId, null, null);
        $query = $this->queryDataRow($sql, phpDataReader::OBJ);

        $return = utf8_decode(serialize($query));

        return $return;
    }

    /*
     * Pega as acoes a serem gravadas no log do sistema
     *
     *
     */

    public static function _action($stringAction)
    {
        try {
            $actions = unserialize(LOAD_ACTIONS_LOG);

            if (array_key_exists($stringAction, $actions)) {
                return $actions[$stringAction];

            } else {

                ErrorCode::setError(E_1001, 'O parametro ' . $stringAction . ' passado não foi localizado');
                exit;
            }

        } catch (Exception $e) {
            $var = new phpDataReader();
            $log = Registry::sendMessageLog($var, $e);

        }


    }

    /**
     * Metodo para gravar acoes do usuario como um log.
     * @param $_action String <br /> Recebe uma string formatada (ex: 'minha_acao_cadastro') conforme definido em <a href="c:/wamp/www/mutual-construcoes/system/config.php">system/config.php</a><br />
     * deve ser definido como:<br /><br />    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> define('Minha outra acao' => '_DEL')</strong> em <a href="c:/wamp/www/mutual-construcoes/system/config.php">system/config.php</a><br /><br />
     * e deve ser passsado para a variavel $_action uma string de mesmo nome definido porem separado por ( _ )<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-> ex: 'minha_outra_acao'
     * @param $_tabela String<br /> Recebe uma String com o nome da tabela que sera efetuada a ação.
     * @param $_id_tabela Integer <br /> Recebe ID do registro da tabela que vai sofrer ou sofreu uma ação:<br /><br />
     * Quando a ação definida em <a href="c:/wamp/www/mutual-construcoes/system/config.php">system/config.php</a> retornar <strong>'_CAD'</strong> o ID deve ser uma variavel com o valor de <a href="http://php.net/manual/pt_BR/function.mysql-insert-id.php">mysql_insert_id()</a>
     * <br />para os casos que a ação retornar <strong>'_DEL'</strong> ou <strong>'_ALT'</strong> o ID do registro a ser afetado.
     * @param $func array<br />Recebe um array com parametros ja definidos: <br /><br />&nbsp;&nbsp;&nbsp;Parametros: <br /><br />
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><a href="https://dev.mysql.com/doc/refman/5.0/en/create-procedure.html">procedure</a></strong> &nbsp;-> Nome da procedure a ser chamada
     * <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><a href="">args</a></strong> &nbsp;-> Recebe um array com os parametros da procedure a ser chamada.<br /><br />Se $_action retorna a ação _ALT e a alteração não for invocada por uma procedure,
     * o parametro 'procedure' deve ser informado como string vazia, e <strong><a href="">args</a></strong> deve ser um array com seus devidos campos e e valores que serão alterados.
     * @param $cond  String<br /> Recebe uma condição para os casos de alteração ex: 'Campo = valor';
     * @return int
     * @example  sys_log(Functions::_action('alterar_perfil_de_usuario'),'tb_perfil',$id,array('procedure' => '','args' => array($_Form['nome_perfil' . $id . ''],$_Form['user_grupo'], $id)))
     */

    public function sys_log($_action, $_tabela, $_id_tabela, $func = NULL, $cond = NULL)
    {

        $getDevice = new DeviceSniffer();

        $text = array_keys($_action);
        $acao = array_values($_action);

        $_acao = $acao[0];
        $texto = $text[0];


        $UserId = self::getUserId();

        # Verifica  qual a acao esta sendo executada e retorna os dados afetado para gravar na tabela de log

        switch ($_acao) {
            case "_CAD":

                $query_old = "";
                $campos = self::xTriggerAfter($_tabela, $_id_tabela);

                break;
            case "_DEL":

                $query_old = self::xTriggerBefore($_tabela, $_id_tabela);

                break;
            case "_ALT":

                $query_old = self::xTriggerBefore($_tabela, $_id_tabela);

                if ($func['procedure'] == "" && $cond != "") {

                    $sql = $this->queryDataUpdate($_tabela, $func['args'], $cond);
                    $response = $sql;
                } else {

                    $sql = $this->queryDataProcedure($func['procedure'], $func['args']);
                    $response = $sql[0];
                }

                $campos = self::xTriggerAfter($_tabela, $_id_tabela);

                break;
        }


        $log = $this->queryDataInsert('tb_sys_log', array(
                'user_logged' => $UserId,
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'porta' => $_SERVER['REMOTE_PORT'],
                'url' => $_SERVER['REQUEST_URI'],
                'browser' => $getDevice->getProperty(),
                'query_odl' => $query_old,
                'query_new' => $campos,
                'data_access' => date('Y-m-d H:i:s'),
                'action' => $texto,
                'hora' => date('H:i:s'),
                'tabela_of_action' => $_tabela,
                'id_of_registry' => $_id_tabela)
        );

        switch ($_acao) {
            case "_ALT":
                return $response;
                break;
            default:
                return $log;
                break;
        }
    }

}



