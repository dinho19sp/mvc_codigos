<?php

/**
 * Classe XajaxController: Validações de campos e criação de componentes de UI
 * @author Francisco Nascimento <d19sp.webdeveloper@outloo.com>
 * E necessario incluir a biblioteca xajax.
 * @uses  prefixo xajax_ para chamar um metodo não é necessario instanciar a classe
 *        a classe ja esta instanciada por padrao, somente use xajax_ antes do nome do metodo
 *        para chamar o metodo
 * @example minhaFunctio($param) use o prfixo xajax_ : xajax_minhaFunction($param);
 */
@require("system/xajax/xajax_core/xajax.inc.php");

class XajaxController extends phpDataReader
{

    public $_response;
    public $_xajax_uri = '/lib/xajax';
    public $_xajax_js;
    public $_sql;
    public $_query;
    public $_DataForm;
    public $_condicao;
    public $_resource;
    public $_Form;
    public $_modal;
    protected $_xajax;
    protected $_app;
    protected $_cep = array();
    protected $_data = array();
    protected $_options_query;

    public function __construct($xajaxDebug = FALSE)
    {

        $host = new ElementosRoot();

        $this->_xajax = new xajax();

        $this->_xajax->configureMany(
            array(
                'debug' => $xajaxDebug,
                'wrapperPrefix' => 'fn_',
                'request URI' => $this->_xajax_uri,
                'characterEncoding' => 'UTF-8',
                "responseType" => "XML"
            ));

        $this->_register($this->_xajax);
        $this->_xajax_js = $this->_xajax->getJavascript($host->getHost() . PATH_AJAX);
        $this->_xajax->processRequest();

    }

    /*
     *  Registra os metodos que seram usado com xajax
    */

    public function _register($ajax)
    {

        /*
         *
         *  Metodos padrao:
         *
         * */

        $ajax->register(XAJAX_FUNCTION, array('xjx_set_value', $this, 'xjx_set_value'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_setTextBox', $this, 'xjx_setTextBox'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_helperValidarDoc', $this, 'xjx_helperValidarDoc'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_MessageBoxShow', $this, 'xjx_MessageBoxShow'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_MessageNotification',$this,'xjx_MessageNotification'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_helperGetCep', $this, 'xjx_helperGetCep'));
        $ajax->register(XAJAX_FUNCTION, array('Paginacao', $this, 'Paginacao'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_recover_password', $this, 'xjx_recover_password'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_recover_new_password', $this, 'xjx_recover_new_password'));
        $ajax->register(XAJAX_FUNCTION, array('delSelected',$this,'delSelected'));
        $ajax->register(XAJAX_FUNCTION, array('xjx_valida_senha', $this, 'xjx_valida_senha'));


        /*
         *  Pertence a LeiloeirosHelpers em system/helpers/
         *
         */

        $hlp_leiloeiro = new LeiloeiroHelper();

        $ajax->register(XAJAX_FUNCTION, array('ajax_DataGrid_Leiloeiro',$hlp_leiloeiro , 'ajax_DataGrid_Leiloeiro'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DeletaLeiloeiro',$hlp_leiloeiro,'ajax_DeletaLeiloeiro'));

        /*
         *  Pertence a PrepostoHelpers em system/helpers/
         *
         */

        $hlp_preposto = new PrepostoHelper();


        $ajax->register(XAJAX_FUNCTION, array('ajax_DataGrid_Preposto',$hlp_preposto , 'ajax_DataGrid_Preposto'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DeletaPreposto',$hlp_preposto,'ajax_DeletaPreposto'));
        $ajax->register(XAJAX_FUNCTION, array('setDadosLeiloeiro',$hlp_preposto,'setDadosLeiloeiro'));
        $ajax->register(XAJAX_FUNCTION, array('removeDadosLeiloeiro',$hlp_preposto,'removeDadosLeiloeiro'));
        $ajax->register(XAJAX_FUNCTION, array('getClipboardLeiloeiro',$hlp_preposto,'getClipboardLeiloeiro'));
        $ajax->register(XAJAX_FUNCTION, array('setGetLeiloeiro',$hlp_preposto,'setGetLeiloeiro'));

        /*
         *  Pertence a ComitenteHelpers em system/helpers/
         *
         * */

        $hlp_comitente = new ComitenteHelper();

        $ajax->register(XAJAX_FUNCTION, array('ajax_DataGrid_Comitente',$hlp_comitente , 'ajax_DataGrid_Comitente'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DeletaComitente',$hlp_comitente,'ajax_DeletaComitente'));

        /*
         *  Pertence a PatiosHelpers em system/helpers/
         *
         * */

        $hlp_patios = new PatiosHelper();

        $ajax->register(XAJAX_FUNCTION, array('ajax_modal',$this , 'ajax_modal'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DataGrid_Patios',$hlp_patios , 'ajax_DataGrid_Patios'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DeletaPatios',$hlp_patios , 'ajax_DeletaPatios'));
        $ajax->register(XAJAX_FUNCTION, array('removeDadosComitente',$hlp_patios , 'removeDadosComitente'));
        $ajax->register(XAJAX_FUNCTION, array('setDadosComitente',$hlp_patios,'setDadosComitente'));


        $hlp_usuarios = new UsuariosHelper();

        $ajax->register(XAJAX_FUNCTION, array('ajax_DataGrid_Usuarios',$hlp_usuarios , 'ajax_DataGrid_Usuarios'));
        $ajax->register(XAJAX_FUNCTION, array('ajax_DeletaUsuarios',$hlp_usuarios , 'ajax_DeletaUsuarios'));

    }


    /*
     *
     * Metodos padrao:

******************************************************************************************************************************************************************************************/

    public function xjx_set_value($index, $val, $propriety, $idForm = NULL)
    {
        $this->_response = new xajaxResponse();
        $session = Registry::getInstance('SessionStart');
        if ($propriety == "value") {
            if ($idForm === NULL || $idForm === "") {
                $this->_response->assign($index, "value", $val);
            } else {
                $this->_response->script("document.forms['" . $idForm . "'].{$index}.value={$val}");
                $session->setVars($index, $val);
            }
        } elseif ($propriety == "innerHTML") {
            $this->_response->assign($index, "innerHTML", $val);
        }

        return $this->_response;
    }

    public function xjx_setTextBox($elemen, $name, $textarea = FALSE, $value = NULL)
    {
        $this->_response = new xajaxResponse();

        if ($textarea != NULL && $textarea == TRUE) {
            $start_multi = "textarea cols='30' rows='5'";
            $end_multi = $value . "</textarea>";
            $inputValue = "";
        } else {
            $start_multi = "input type='text' cols='40' rows='5'";
            $end_multi = "";
            $inputValue = "value='$value'";
        }

        if (isset($name)):

            $this->_response->assign($elemen, "innerHTML", "<" . $start_multi . " id='$name' class='input-small'  name='$name' " . $inputValue . " >" . $end_multi . "");
            $this->_response->script("$('#" . $name . "').focus();");

        endif;

        return $this->_response;
    }

    public function xjx_helperValidarDoc($FormData, $isCmp)
    {

        $this->_response = new xajaxResponse();

        /* retira a pontuação dos documentos de CPF e CNPJ */

        $FormData[$isCmp] = str_replace('.', '', $FormData[$isCmp]);
        $FormData[$isCmp] = str_replace('-', '', $FormData[$isCmp]);
        $FormData[$isCmp] = str_replace('/', '', $FormData[$isCmp]);
        $FormData[$isCmp] = str_replace('_', '', $FormData[$isCmp]);




        switch($FormData['id_natureza'])
        {
            case 1:
                if (strlen($FormData[$isCmp]) >= 1 && strlen($FormData[$isCmp]) <= 11 )
                {

                    $this->_error = 'CPF digitado está Inv&aacute;lido!!';

                    if (DataValidator::isValidCpf($FormData[$isCmp]) == false)
                    {
                        $this->_response->assign('span_' . $isCmp, 'innerHTML', "<code style='font-size: 12px; color: red;'><i style='color: red;' class=\"entypo-cancel-circled icon-red\"></i>&nbsp;{$this->_error}</code>");
                        $this->_response->script("document.getElementById('" . $isCmp . "').focus()");
                        $this->_response->assign($isCmp, "style.borderColor", "#EE0000");
                    }
                    else
                    {

                        $this->_response->assign('span_' . $isCmp, 'innerHTML',"");
                        $this->_response->assign($isCmp, "style.borderColor", "");
                    }

                }
                elseif (strlen($FormData[$isCmp]) <= 0 OR strlen($FormData[$isCmp]) == 11 && DataValidator::isValidCpf($FormData[$isCmp]) == true)
                {


                    $this->_response->assign('span_' . $isCmp, 'innerHTML',"");
                    $this->_response->assign($isCmp, "style.borderColor", "");

                }

                break;
            case 2:

                if(strlen($FormData[$isCmp]) >= 1 && strlen($FormData[$isCmp]) <= 15 )
                {

                    if (DataValidator::isValidCnpj($FormData[$isCmp]) == false )
                    {

                        $this->_error = 'CNPJ digitado está Inv&aacute;lido!!';

                        $this->_response->assign('span_' . $isCmp, 'innerHTML', "<code style='font-size: 12px; color: red;'><i style='color: red;' class=\"entypo-cancel-circled icon-red\"></i>&nbsp;{$this->_error}</code>");
                        $this->_response->script("document.getElementById('" . $isCmp . "').focus()");
                        $this->_response->assign($isCmp, "style.borderColor", "#EE0000");
                    }
                    else
                    {

                        $this->_response->assign('span_' . $isCmp, 'innerHTML', '');
                        $this->_response->assign($isCmp, "style.borderColor", "");
                    }
                }
                else if(strlen($FormData[$isCmp]) == 0 || strlen($FormData[$isCmp]) == 15 && DataValidator::isValidCnpj($FormData[$isCmp]) == true)
                {

                    $this->_response->assign('span_' . $isCmp, 'innerHTML', '');
                    $this->_response->assign($isCmp, "style.borderColor", "");
                }


                break;
        }
        return $this->_response;


    }

    public function xjx_MessageBoxShow($Message, $BoxHeader, $TypeBoxMessage, $id_FormSubmit = "", $xajaxFunction = "", $target = "", $url = NULL)
    {
        $this->_response = new xajaxResponse();
        $host = Registry::getInstance('ElementosRoot');

        if ((int)$target == 1) {
            $attr = ".attr('target','_blank')";
        } else {
            $attr = "";
        }

        switch ($TypeBoxMessage) {
            case 1:
                $button = "<a href=\"#\" style=\"text-decoration:none; color:#FFF;\" class=\"btn btn-mini btn-warning\" onclick=\"$('#" . $id_FormSubmit . "'){$attr}.submit();\">Confirma</a>
      <a href=\"#\" onclick=\"document.getElementById('open_msg').style.display='none';document.getElementById('fade_msg').style.display='none';xajax_xjx_set_value('deletar', 'FALSE')\" style=\"text-decoration:none; color:#FFF;\" class=\"btn btn-mini btn-danger\">Cancelar</a>";
                break;
            case 2:
                $button = "<a onclick=\"document.getElementById('open_msg').style.display='none';document.getElementById('fade_msg').style.display='none';window.reload()\" class=\"btn btn-white btn-sm\" data-dismiss=\"modal\">Ok</a>";
                break;
            case 3:

                $button = "<a href=\"javascript:void(0);\" style=\"text-decoration:none; color:#FFF;\" class=\"btn btn-mini btn-warning\" onclick=\"" . $xajaxFunction . ";document.getElementById('open_msg').style.display='none';document.getElementById('fade_msg').style.display='none'\">Proceder</a>
      <a href=\"#\" onclick=\"document.getElementById('open_msg').style.display='none';document.getElementById('fade_msg').style.display='none';\" style=\"text-decoration:none; color:#FFF;\" id=\"btn-cancelar\" class=\"btn btn-mini btn-danger\">Cancelar</a>";
                break;
            case 4:

                $button = "";
                break;
            case 5:
                $button = "<a class='link-facebook' href='javascript:void(0);' onclick=\"window.location ='" . $url . "'\"><div class='btn-facebook'><div class='btn-facebook-img'><img width='28' height='28' src='" . $host->getHost() . "includes/images/facebook-login.png'></div><p>Login com <img class='facebook-login' src='" . $host->getHost() . "includes/images/facebook-logo.png'></p></div></a>";
                break;
            case 6:

                $button = "<a href=\"javascript:void(0);\" style=\"text-decoration:none; color:#FFF;\" class=\"btn btn-mini btn-warning\" onclick=\"" . $xajaxFunction . ";document.getElementById('open_msg').style.display='none';document.getElementById('fade_msg').style.display='none'\">Fechar e voltar</a>";
                break;
        }

        switch ($BoxHeader) {

            case 1:
                $header = "<i class='fa fa-exclamation-triangle' style='color:orange;'></i>&nbsp; Aten&ccedil;&atilde;o:  n&atilde;o foi possivel executar a opera&ccedil;&atilde;o";
                break;
            case 2:
                $header = "<span class='entypo-alert' style='font-size:32px; color:orange;'></span>&nbsp;<span style='font-size:18px; color:blue;'>Informação</span>";
                break;
            case 3:
                $header = "<i class='entypo-alert' style='font-size:36px;color:red;'></i>&nbsp;<span style='font-size: 18px;color:red;'> Excluir Registro</span>";
                break;
            case 4:
                $header = "<span class='entypo-cancel-circled' style='font-size:32px; color:red;'></span>&nbsp;Opera&ccedil;&atilde;o n&atilde;o realizada!";
                break;
            case 5:
                $header = "<i class='icon-remove-sign icon-large icon-2x' style='color:black;'></i>&nbsp;Aguarde...";
                break;
            case 6:
                $header = "<i class='glyphicon glyphicon-warning-sign' style='color:#ff0000; font-size: 20px;'></i>&nbsp;<span style='font-size: 18px;color:red;'> Atenção </span>";
                break;
        }

        $_ALERT = "<div class=\"modal-dialog\"  id=\"modal-1\" aria-hidden=\"false\" role=\"dialog\" style=\"display: block;\">";
        $_ALERT .= "<div class=\"modal-content\" id=\"sample-modal-dialog-1\" style=\"margin-top:10%;\">";
        $_ALERT .= "	<div class=\"modal-header\">";
        $_ALERT .= "		<h5>" . $header . "</h5>";
        $_ALERT .= "	</div>";
        $_ALERT .= "	<div class=\"modal-body\">";
        $_ALERT .= "		<p style=\"color:red;\"><center><h4 id='msg_alert'>" . $Message . "</h4></center></p>";
        $_ALERT .= "	</div>";
        $_ALERT .= "	<div class=\"modal-footer\">";
        $_ALERT .= "		" . $button . "";
        $_ALERT .= "	</div>";
        $_ALERT .= "</div>	";
        $_ALERT .= "</div>	";

        $this->_response->assign("open_msg", "innerHTML", $_ALERT);
        $this->_response->script("$('#open_msg').fadeIn('slow');");
        $this->_response->script("document.getElementById('open_msg').style.display=\"block\";document.getElementById('fade_msg').style.display=\"block\"");

        return $this->_response;

    }

    public function xjx_MessageNotification($MessageType=null,$Message,$stringJson=null)
    {

        $this->_response = new xajaxResponse();




        switch($MessageType)
        {
            case 'alert-danger';
                $icon = 'fa fa-minus-circle';
                $color= 'red';
                break;
            case 'alert-warning';
                $icon = 'fa fa-exclamation-triangle';
                $color= 'red';
                break;
            case 'alert-info';
                $icon = 'fa fa-exclamation-circle';
                $color= 'blue';
                break;
            case 'alert-success';
                $icon = 'fa fa-check-circle';
                $color= 'green';
                break;
            default:
                $icon = 'fa fa-comments-o';
                $color= 'green';
                break;
        }

        if($stringJson != "")
        {
            $json  = json_decode($stringJson);
            $In = $json->time_in;
            $Out = $json->time_out;

        }
        else
        {
            $In = Integer::Int(400);
            $Out = Integer::Int(4000);
        }


        $this->_response->assign("msg", "innerHTML", "<div class='alert ".$MessageType."' id='msg-alert' style='text-align:left;' ><span class='".$icon."' style='font-size: 24px; color:".$color." ; vertical-align:middle;'> </span>&nbsp;&nbsp;".$Message." <br /> </div>");
        $this->_response->script('setTimeout(function(){ $("#msg").fadeIn(); }, '.$In.');');
        $this->_response->script('setTimeout(function(){ $("#msg").fadeOut(600); }, '.$Out.');');

        return $this->_response;
    }

    public function xjx_helperGetCep($Form, $_cep)
    {

        $this->_response = new xajaxResponse();

        if (isset($Form[$_cep])) {

            $Form[$_cep] = str_replace("-", "", trim($Form[$_cep]));

            $correios = new Correios;
            $correios->retornaInformacoesCep($Form[$_cep]);
            $dados['sucesso'] = "1";
            $dados['endereco'] = $correios->informacoesCorreios->getLogradouro();
            $dados['bairro'] = $correios->informacoesCorreios->getBairro();
            $dados['cidade'] = $correios->informacoesCorreios->getLocalidade();
            $dados['uf'] = $correios->informacoesCorreios->getUF();

            $dadosCep = array_map('utf8_encode',$dados);

            $logradouro = explode(" ",$dadosCep['endereco']);

            $logradouroTipo = array_shift($logradouro);

            $logradouroNome = implode(" ",$logradouro);

            $this->_sql = $this->queryDataSelect('tb_enderecos_logradouro',NULL, "logradouro_nome like '%{$logradouroTipo}%'");
            $this->_condicao = $this->queryDataRow($this->_sql,phpDataReader::ASSOC);


            $_sql = $this->queryDataSelect('tb_enderecos_estados', NULL, "estado_sigla like '%{$dadosCep['uf']}%'");
            $_condicao =  $this->queryDataRow($_sql,phpDataReader::OBJ);

            $DataCep = array(
                'id_logradouro_r'       => (int)$this->_condicao['id_logradouro'],
                'logradouro_nome_r'     =>(string)$logradouroNome,
                'bairro_r'              => (string)$dadosCep['bairro'],
                'cidade_r'              => (string)$dadosCep['cidade'],
                'id_estado_r'           => $_condicao->id_estado
            );


            foreach($DataCep as $key =>$val)
            {
                $this->_response->assign($key,'value',$val);
            }

            $this->_response->script("document.getElementById('numero_r').focus()");

            return $this->_response;
        }
    }

    public function app()
    {
        $this->_app = new onCreateClass();
        return $this->_app;
    }

/***************************************************************************************************************************************************************************************/
    /* Retorna instancias das Classes ElementosRoot, Application, e Functions */

    public function Paginacao($fn, $page, $tPagina,  $arg = NULL)
    {
        $this->_response = new xajaxResponse();

        if($arg != NULL)
        {
            if(is_int($arg))
            {
                $args = ','.$arg;

            }
            else
            {
                $args = ',"'.$arg.'"';
            }

        }
        $this->_response->script("$fn({$page},".$tPagina.$args.");");

        return $this->_response;
    }

    /**
     * @param $fnc_ajax String recebe o nome de um Metodo ajax para busca
     * @return xajaxResponse  retorna o resultado da busca
     */
    public function ajax_modal($fnc_ajax)
    {
        $this->_response = new xajaxResponse();

        $modal ='<form id="formModal" name="formModal" metod="post">';
        $modal .= '<div class="modal fade" id="modal-comitente">';
        $modal .='<div class="modal-dialog">';
        $modal .='<div class="modal-content">';
        $modal .='            <div class="modal-header">';
        $modal .='       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        $modal .='           <h4 class="modal-title">Registros encontrados</h4>';
        $modal .='      </div>';
        $modal .='            <div class="modal-body">';
        $modal .='                <div class="row">';
        $modal .='                  <div class="col-md-12">';
        $modal .='                     <div class="input-group">';
        $modal .='                       <span class="input-group-addon"><i class="fa fa-search"></i></span>';
        $modal .='                          <input type="text" name="searchData" id="searchData" class="form-control" placeholder="Digite o nome para buscar o registro" value="" onkeyup='.$fnc_ajax.'(null,this.value);>';
        $modal .='                     </div>';
        $modal .='                  </div>';
        $modal .='               </div>';
        $modal .='               <p></p>';
        $modal .='                <div class="row">';
        $modal .='                  <div class="col-md-12">';
        $modal .='                     <div id="listaComitente">';
        $modal .='                     </div>';
        $modal .='                  </div>';
        $modal .='               </div>';
        $modal .='            </div>';
        $modal .='            <div class="modal-footer">';
        $modal .='           <button type="button" class="btn btn-info" data-dismiss="modal">Fechar</button>';
        $modal .='        </div>';
        $modal .='    </div>';
        $modal .=' </div>';
        $modal .='</div>';
        $modal .='</form>';

        $this->_response->script("
            $('#modal-comitente').remove();
             $('body').prepend('" . $modal . "');

           ");


        return $this->_response;
    }

    /*
     *  Especifico para listara registros da hammer leiloes
     *  Deleta varios registros  conforme especificado a classe da div e o id a ser deletado
     */

    public function delSelected($args,$metodDelete,$metodMontaTabela,$cmpPaginacao)
    {
        $this->_response= new xajaxResponse();
        $this->_response->script("

                 $('".$args."').each(function()
                  {

                    ".$metodDelete."($('label > input',this).attr('id'));

                  }

                  );
       ");

        $this->_response->script("


        setTimeout(function()
        {

            if($('#table_".$cmpPaginacao." tbody tr').length == 0)
            {
                $('#table_".$cmpPaginacao."').show('slow');
                ".$metodMontaTabela."($('#pgs_".$cmpPaginacao."').val()-1,10);


                 $('#pgs_".$cmpPaginacao."').prop('value',$('#pgs_".$cmpPaginacao."').val()-1);

             }
             else
             {

$('#table_".$cmpPaginacao."').show('slow');
                ".$metodMontaTabela."($('#pgs_".$cmpPaginacao."').val(),10);

                $('#pgs_".$cmpPaginacao."').prop('value',$('#pgs_".$cmpPaginacao."').val());
             }

        }, 2500);");

        return $this->_response;
    }


    public function xjx_valida_senha($Campo1,$Campo2)
    {
        $this->_response = new xajaxResponse();
        if ($Campo1 != $Campo2) {

            $this->_response->script("
            var duration = '{\"time_in\":500,\"time_out\":6000}';
            fn_xjx_MessageNotification('alert-danger','Senhas informadas não confere!',duration)");

        }
        else
        {
            if ($Campo2 != "") {
                $msg = DataValidator::CheckThisPassword($Campo1);
                $this->_response->script("


                var duration = '{\"time_in\":500,\"time_out\":6000}';
                fn_xjx_MessageNotification('alert-info','".$msg."',duration)");
            }
        }
        return $this->_response;
    }
/*
 *  Metodos usados para redefinir a senha na pagina de login
 *
 ****************************************************************************************************************************************************************************************/

    public function xjx_recover_new_password($dataForm)
    {
        $this->_response = new xajaxResponse();
        $code = new Encryption();
        $security_code_user = $code->setSecurityCode();

        $sqlRecover = $this->queryDataSelect('tb_usuarios_acessos',null,'id_usuario =' . $dataForm['id_usuario'],null,null);
        $qry = $this->queryDataRow($sqlRecover,phpDataReader::OBJ);

        $sql = $this->app()->getFunctions()->sys_log(Functions::_action('recover_passoword_usuario'),'tb_usuarios_acessos',$qry->id_acesso,array('procedure'=> "",'args'=>array('usuario_pass' => md5($dataForm['usuario_pass']), 'usuario_security' => $security_code_user)),'id_acesso =' . $qry->id_acesso);

        $this->_response->script("setTimeout(function(){
                    $('#modal-6,.modal-backdrop').removeClass('in');
                        $('#formRecover').each(function(){
                            this.reset();
                            $('#row_security').hide();
                            $('#row_senha').hide();
                            $('#row_actions').hide();
                            $('#span_cpf_cnpj').html('');
                            $('#span_usuario_email').html('');
                            $('#span_senhas').html('');
                            $('#span_usuario_security').html('');
                            $('#enviado').prop('value',0);
                        });
                    }, 100);");
        $this->_response->assign("msg", "innerHTML", "<div class='alert alert-success' id='msg-alert'><span class='entypo-check-circled' style='font-size: 22px; color: green;'> </span>Senha alterada com sucesso</div>");
        $this->_response->script('setTimeout(function(){ $("#msg").fadeIn(); }, 100);');
        $this->_response->script('setTimeout(function(){ $("#msg").fadeOut(600); }, 4000);');

        return $this->_response;
    }

    public function xjx_recover_password($dataForm)
    {

        $this->_response = new xajaxResponse();

        $data = new UsuariosModel();
        $dados = $data->BuscaUsuarioRecoverSenha($dataForm['cpf_cnpj']);
        $messageCodigo = $data->securityCardUsuario($dados);


        if ($dataForm['cpf_cnpj'] <> "" && $dataForm['cpf_cnpj'] <> $dados->cpf_cnpj) {

            $this->_response->assign('span_cpf_cnpj', 'innerHTML', "<code style='color: red;'> <i class='entypo-cancel-circled'></i> &nbsp;O CPF informado não foi encontrado ou esta inválido</code>");

        } elseif ($dataForm['cpf_cnpj'] === $dados->cpf_cnpj) {

            $this->_response->assign('span_cpf_cnpj', 'innerHTML', "<i class='entypo-check' style='color:green;'></i>&nbsp;&nbsp;<span style='color:green'>Válido!</span>");
        } else {
            $this->_response->assign('span_cpf_cnpj', 'innerHTML', "");
        }

        if ($dataForm['usuario_email'] <> "" && $dataForm['usuario_email'] <> $dados->usuario_email) {
            $this->_response->assign('span_usuario_email', 'innerHTML', "<code style='color: red;'> <i class='entypo-cancel-circled'></i> &nbsp;O e-mail informado não foi encontrado ou esta inválido</code>");
            $this->_response->script("$('#row_security,#row_senha').hide();");

        } elseif ($dataForm['usuario_email'] == "") {
            $this->_response->assign('span_usuario_email', 'innerHTML', "");
            $this->_response->script("$('#row_security,#row_senha').hide();");
            $this->_response->script("$('#row_actions').hide();");

        } elseif ($dataForm['usuario_email'] == $dados->usuario_email) {

            $enviado = 0;

            $_email = new Email();
            $_email->To($dados->usuario_email);
            $_email->From("francisco.nascimento@krakenagencia.com.br");
            $_email->ReplyTo("francisco.nascimento@krakenagencia.com.br");
            $_email->Subject("Recuperação de Senha - Mutual Construções area do usuário");
            $_email->ContentType("html");
            $message = $messageCodigo;

            $_email->Message($message);

            if ($dataForm['enviado'] == 0) {

                $_email->envia();
                $this->_response->assign("msg", "innerHTML", "<div class='alert alert-success' id='msg-alert'><span class='entypo-check-circled' style='font-size: 22px; color: green;'> </span>você recebera em seu e-mail o código de segurança, caso não o tenha, para poder completar a transação</div>");
                $this->_response->script('setTimeout(function(){ $("#msg").fadeIn(); }, 100);');
                $this->_response->script('setTimeout(function(){ $("#msg").fadeOut(600); }, 7000);');
            }

            $enviado = 1;

            $this->_response->assign('enviado', 'value', $enviado);
            $this->_response->assign('id_usuario', 'value', $dados->id_usuario);
            $this->_response->assign('span_usuario_email', 'innerHTML', "<i class='entypo-check' style='color:green;'></i>&nbsp;<span style='color:green'>Válido!</span>");
            $this->_response->script("$('#row_security').show();");
            $this->_response->script("$('#row_actions').hide();");
        }

        if ($dataForm['usuario_security'] <> "" && $dataForm['usuario_security'] <> $dados->usuario_security) {

            $this->_response->assign('span_usuario_security', 'innerHTML', "<code style='color: red;'> <i class='entypo-cancel-circled'></i> &nbsp;O c&oacute;digo de seguran&ccedil;a informado não foi encontrado ou esta inválido</code>");
            $this->_response->script("$('#row_senha').hide();");
            $this->_response->script("$('#row_actions').hide();");


        } elseif ($dataForm['usuario_security'] == "") {
            $this->_response->assign('span_usuario_security', 'innerHTML', "");
            $this->_response->script("$('#row_senha').hide();");
            $this->_response->script("$('#row_actions').hide();");

        } elseif ($dataForm['usuario_security'] == $dados->usuario_security && $dataForm['usuario_email'] == $dados->usuario_email) {

            $this->_response->assign('span_usuario_security', 'innerHTML', "<i class='entypo-check' style='color:green;'></i>&nbsp;<span style='color:green'>Válido!</span>");
            $this->_response->script("$('#row_senha').show();");
            $this->_response->script("$('#row_actions').hide();");
        }


        if ($dataForm['usuario_pass'] <> "" && $dataForm['usuario_pass'] <> $dataForm['usuario_pass_2']) {

            $this->_response->assign('span_senhas', 'innerHTML', "<code style='color: red;'> <i class='entypo-cancel-circled'></i> &nbsp;As senhas informadas est&atilde;o diferentes</code>");
            $this->_response->script("$('#row_actions').hide();");
        } elseif ($dataForm['usuario_pass'] == "" && $dataForm['usuario_pass_2'] == "") {

            $this->_response->assign('span_senhas', 'innerHTML', "");
            $this->_response->script("$('#row_actions').hide();");

        } elseif ($dataForm['usuario_pass'] === $dataForm['usuario_pass_2']) {

            $this->_response->assign('span_senhas', 'innerHTML', "<i class='icon-ok-sign icon-large' style='color:green;'></i>&nbsp;<span style='color:green'>" . DataValidator::CheckThisPassword($dataForm['usuario_pass']) . "</span>");
            $this->_response->script("$('#row_actions').show();");
        }

        return $this->_response;
    }



/****************************************************************************************************************************************************************************************/

}

