<?php
/**
 * Created by PhpStorm.
 * User: Francisco Nascimento
 * Date: 16/04/15
 * Time: 22:05
 *
 * Helper para operações com cadastro de leiloeiro
 *
 *
 */

class ComitenteHelper {

    private $_table = 'tb_comitente';
    private $_response_table;
    private $_response;
    private $_paginator;
    private $_DataReader;
    private $_Filter;
    private $_Options;
    private $_query;

    /* Retorna objetos das calasses phpDataReader */

    private function DataReader()
    {
        $this->_DataReader = new phpDataReader();

        return $this->_DataReader;
    }

    /* Retorna objetos das calasses phpCriteria */

    private function DataFilter()
    {
        $this->_Filter = new phpCriteria();

        return $this->_Filter;

    }

    /* Retorna objetos das calasses xjxHelperPagiacao */

    private function DataPaginate()
    {
        $this->_paginator = new xjxHelperPagiacao();

        return $this->_paginator;
    }

   /* Retorna objetos das calasses ElementosRoot , Applcation, e Funcions */

    private function app()
    {
        $app = new onCreateClass();

        return $app;
    }

    public function setDadosLeiloeiro()
    {
        $this->_response = new xajaxResponse();

        $model = new ComitenteModel();
        if($this->app()->getElementosRoot()->getCookie('ID_LEILOEIRO'))
        {
            $id = $this->app()->getElementosRoot()->getCookie('ID_LEILOEIRO');
            $resource = $model->getLeiloeiroTransfere($id);

            $this->_response->assign('id_leiloeiro','value',$resource->id_leiloeiro);
            $this->_response->assign('nome_leiloeiro','value',$resource->nome_leiloeiro);

            if($resource > 0)
            {
                $this->app()->getElementosRoot()->removeCookieSite(array('ID_LEILOEIRO','GET_LEILOEIRO','args_id'));

                $this->_response->script("fn_xjx_MessageNotification('alert-success','Leiloeiro adicionado com sucesso')");
            }


        }

        return $this->_response;

    }

    public function removeDadosLeiloeiro()
    {
        $this->_response = new xajaxResponse();

            $this->_response->script("

            if($('#id_leiloeiro').val() == '')
            {

                 fn_xjx_MessageNotification('alert-danger','não há dados para remover');

            }
            else
            {
                fn_xjx_set_value('id_leiloeiro','','value');
                fn_xjx_set_value('nome_leiloeiro','','value');

                fn_xjx_MessageNotification('alert-success','Leiloeiro removido com sucesso');
            }
            ");

        return $this->_response;

    }

    /* Monta a tabela de lista de leiloeiros com as opções de busca e paginação */

    public function MontaListaComitente($DataSet,ComitenteModel $Model)
    {


        $this->_response_table = '     <form id="form-2" name="form-2" method="post">';
        $this->_response_table .= '         <input type="hidden" name="deletar" id="deletar" value="">';
        $this->_response_table .= '         <table class="table table-bordered table-striped table-hover" id="table_comitente">';
        $this->_response_table .= '             <thead>';
        $this->_response_table .= '                <th>Opções</th>';
        $this->_response_table .= '                 <th># ID</th>';
        $this->_response_table .= '                 <th>Nome Comitente</th>';
        $this->_response_table .= '                 <th>Telefone</th>';
        $this->_response_table .= '                 <th colspan="2" style="text-align: center;">Situa&ccedil;&atilde;o</th>';
        $this->_response_table .= '        </thead>';
        $this->_response_table .= '        <tbody>';

        if($DataSet != "")
        {

            foreach($DataSet as $Indice => $data )
            {

                $status = $Model->returnStatus($data->id_status);


                $vincLeilao = $Model->verificarVinculoComitenteLeilao($data->IdL);

                if($vincLeilao->id_comitente == $data->IdL)
                {
                    $comitente =' <span class="badge badge-info tooltip-primary" data-toggle="tooltip" data-placement="top" title="Vinc. Leilão n°&nbsp;:'.$vincLeilao->codigo_leilao.'" data-original-title="Vinc. Leilão n°&nbsp;:'.$vincLeilao->codigo_leilao.'"><i class="fa fa-flag-o"></i></span>';


                }
                else
                {
                    $comitente ='&nbsp;';
                }

                $this->_response_table .= '             <tr  id="LT_'.$data->IdL.'" data-id="'.$data->IdL.'" data-edit="'.$this->app()->getFunctions()->goToUrl('comitente','editar-comitente',array('srcid' =>  $data->IdL ) ).'" data-placement="top" data-original-title="" title="Clique para editar">';
                $this->_response_table .= '                <td class="novalue" style="width: 5%; text-align: center;" >';
                $this->_response_table .= '                     <div class="checkbox checkbox-replace color-red">';
				$this->_response_table .= '				            <input type="checkbox" id="'.$data->IdL.'" name="id_comitente[]">';
                $this->_response_table .= '				            <label></label>';
                $this->_response_table .= '			             </div>';
                $this->_response_table .= '                </td>';
                $this->_response_table .= '                <td># '.str_pad($data->IdL, 3, "0", STR_PAD_LEFT).'</td>';
                $this->_response_table .= '                <td>'.$data->nome_comitente.'</td>';
                $this->_response_table .= '                <td>'.$data->ddd_r . '&nbsp;-&nbsp;' . $data->telefone_r.'</td>';
                $this->_response_table .= '                <td  style="width: 5%; text-align: center;"><span class="badge '.$status.'">'.$data->status_nome.'</span></td>';
                if($comitente != '')
                {
                    $this->_response_table .= '                <td style="width:8%; text-align: center;">'.$comitente.'</td>';
                }
                $this->_response_table .= '            </tr>';
            }


        }

        $this->_response_table .= '         </tbody>';
        $this->_response_table .= '    </table>';
        $this->_response_table .= ' </form>';

        return $this->_response_table;
    }

    /*
     * Monta a pagina de lista de leiloeiro
     *
     */

    public function ajax_DataGrid_Comitente($PaginaIni,$TotalPorPagina = 0, $Data=NULL)
    {

        $this->_response = new xajaxResponse();

        $this->_Options = array(
            'PaginaInicial'     => $PaginaIni,
            'TotalPorPagina'    => $TotalPorPagina,
            'txtSearch'         => $Data
        );


        $Model = new ComitenteModel();
        $DataTable  =  $Model->getListaComitente($this->_Options);
        $DataSet    =  $DataTable['DataRow'];
        $Paginate   =  $DataTable['Paginacao'];
        $_tabela = $this->MontaListaComitente($DataSet,$Model);


        $this->_response->assign("ListaComitente", "innerHTML", $_tabela);

        $this->_response->assign("paginacao-form", "innerHTML", $Paginate);
        $this->_response->script(' $(\'[data-toggle=\"tooltip\"]\').tooltip();');
        $this->_response->script("$('[data-toggle=\"popover\"]').popover();");
        $this->_response->script("replaceCheckboxes();");
        $this->_response->script("

                $('#table_comitente tr td:not(.novalue)').click(function(){

                    window.location.href = $(this).parent().data(\"edit\");

                });

        ");

        $this->_response->script("

                $('#btn_send').bind('click',function(){

                    if($('div.neon-cb-replacement').hasClass('checked'))
                    {
                            fn_xjx_MessageBoxShow('Deseja realmente deleta estes registros',2,3,'',\"fn_delSelected('div.neon-cb-replacement.checked','fn_ajax_DeletaComitente','fn_ajax_DataGrid_Comitente','comitente')\");


                    }
                    else
                    {
                            fn_xjx_MessageNotification('alert-danger','Selecione pelo menos um registro para deletar');
                    }

                });

        ");


        return $this->_response;

    }

    /* Deleta o registro do leiloeiro */

    public function ajax_DeletaComitente($id,$PaginaIni=null,$TotalPorPagina=null)
    {
        $this->_response= new xajaxResponse();

        $model = new ComitenteModel();

        $vincLeilao = $model->verificarVinculoComitenteLeilao($id);


        if($vincLeilao->id_comitente == $id)
        {
            $this->_response->script(" fn_xjx_MessageNotification('alert-danger','Não é possivel deletar um comitente vinculado a um Leilão');");
        }
        else
        {
            $this->_query = $model->Deletar($id);
            $this->_response->script('
            $("#table_comitente #LT_'.$id.'").animate(
            {
               width: [ "toggle", "swing" ],
               height: [ "toggle", "swing" ],
               opacity: "toggle"
              }, 800, "linear", function() {
                $( this ).remove();
                fn_xjx_MessageNotification(\'alert-success\',\'Registros deletados com sucesso\');
            });

        ');

        }

        return $this->_response;
    }

} 