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

class PatiosHelper {

    private $_table = 'tb_patios';
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




    public function setDadosComitente($id=null,$name=null)
    {
        $this->_response = new xajaxResponse();

        $model = new PatiosModel();

        $resource = $model->getComitenteTransfere(null,$name);

        $this->_response_table = '     <form id="form-2" name="form-2" method="post">';
        $this->_response_table .= '         <table class="table table-bordered table-striped table-hover" id="table_comitente">';
        $this->_response_table .= '             <thead>';
        $this->_response_table .= '                 <th style="width:8%;">#Codigo</th>';
        $this->_response_table .= '                 <th>Comitente</th>';
        $this->_response_table .= '        </thead>';
        $this->_response_table .= '        <tbody>';

        if($resource != "")
        {

            foreach($resource as $Indice => $data )
            {

                $this->_response_table .= '             <tr data-id="'.$data->id_comitente.'" data-name="'.$data->nome_comitente.'" data-dismiss="modal">';
                $this->_response_table .= '                <td ># '.str_pad($data->id_comitente, 3, "0", STR_PAD_LEFT).'</td>';
                $this->_response_table .= '                <td>'.$data->nome_comitente.'</td>';
                $this->_response_table .= '            </tr>';
            }

        }else
        {
            $this->_response_table .= '             <tr>';
            $this->_response_table .= '                <td colspan="2" style="text-align: center; font-size: 16px"><span class="badge-roundless badge badge-info"><i class="fa fa-exclamation-triangle"></i> Nenhum registro encontrado!</span> </td>';
            $this->_response_table .= '            </tr>';

        }

        $this->_response_table .= '         </tbody>';
        $this->_response_table .= '    </table>';
        $this->_response_table .= ' </form>';


        $this->_response->assign('listaComitente','innerHTML',$this->_response_table);
        $this->_response->script("$('#table_comitente tr ').click(function(){

                console.log($(this).data('id'));
                $('#id_comitente').prop('value',$(this).data('id'));
                $('#nome_comitente').prop('value',$(this).data('name'));
                fn_xjx_MessageNotification('alert-success','O registro [ '+$(this).data('name')+' ] foi adicionado com sucesso!!');

        });");
        return $this->_response;

    }

    public function removeDadosComitente()
    {
        $this->_response = new xajaxResponse();

            $this->_response->script("

            var regName = $('#nome_comitente').val();

            if($('#id_comitente').val() == '')
            {

                 fn_xjx_MessageNotification('alert-danger','não há dados para remover');

            }
            else
            {


                fn_xjx_set_value('id_comitente','','value');
                fn_xjx_set_value('nome_comitente','','value');

                fn_xjx_MessageNotification('alert-success','O registro '+regName+' removido com sucesso');
            }
            ");

        return $this->_response;

    }

    /* Monta a tabela de lista de leiloeiros com as opções de busca e paginação */

    public function MontaListaPatios($DataSet,PatiosModel $Model)
    {




        $this->_response_table = '     <form id="form-2" name="form-2" method="post">';
        $this->_response_table .= '         <input type="hidden" name="deletar" id="deletar" value="">';
        $this->_response_table .= '         <table class="table table-bordered table-striped table-hover" id="table_patios">';
        $this->_response_table .= '             <thead>';
        $this->_response_table .= '                <th>Opções</th>';
        $this->_response_table .= '                 <th># ID</th>';
        $this->_response_table .= '                 <th>Nome do Patio</th>';
        $this->_response_table .= '                 <th>Comitente</th>';
        $this->_response_table .= '                 <th>Telefone</th>';
        $this->_response_table .= '                 <th colspan="2" style="text-align: center;">Situa&ccedil;&atilde;o</th>';
        $this->_response_table .= '        </thead>';
        $this->_response_table .= '        <tbody>';

        if($DataSet != "")
        {

            foreach($DataSet as $Indice => $data )
            {

                $status = $Model->returnStatus($data->id_status);
                $comitente_patios = $Model->getComitentePatio($data->id_comitente);


                if($data->id_comitente <= 0)
                {

                    $comitente = '<span class="badge badge-info">Sem leiloeiro vinculado</span>';
                }
                else
                {
                    $comitente = $comitente_patios->nome_comitente;
                }



                $this->_response_table .= '             <tr  for="id_patio[]" id="LT_'.$data->IdL.'" data-id="'.$data->IdL.'" data-edit="'.$this->app()->getFunctions()->goToUrl('patios','editar-patio',array('srcid' =>  $data->IdL ) ).'" data-placement="top" data-original-title="" title="Clique para editar">';
                $this->_response_table .= '                <td class="novalue" style="width: 5%; text-align: center;" >';
                $this->_response_table .= '                     <div class="checkbox checkbox-replace color-red">';
				$this->_response_table .= '				            <input type="checkbox" id="'.$data->IdL.'" name="id_patio[]">';
                $this->_response_table .= '				            <label></label>';
                $this->_response_table .= '			             </div>';
                $this->_response_table .= '                </td>';
                $this->_response_table .= '                <td># '.str_pad($data->IdL, 3, "0", STR_PAD_LEFT).'</td>';
                $this->_response_table .= '                <td>'.$data->nome_local.'</td>';
                $this->_response_table .= '                <td hidden-tablet">'.$comitente.'</td>';
                $this->_response_table .= '                <td>'.$data->ddd_r . '&nbsp;-&nbsp;' . $data->telefone_r.'</td>';
                $this->_response_table .= '                <td  style="width: 5%; text-align: center;"><span class="badge '.$status.'">'.$data->status_nome.'</span></td>';
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

    public function ajax_DataGrid_Patios($PaginaIni,$TotalPorPagina = 0, $Data=NULL)
    {

        $this->_response = new xajaxResponse();

        $this->_Options = array(
            'PaginaInicial'     => $PaginaIni,
            'TotalPorPagina'    => $TotalPorPagina,
            'txtSearch'         => $Data
        );


        $Model = new PatiosModel();
        $DataTable  =  $Model->getListaPatios($this->_Options);

        $DataSet    =  $DataTable['DataRow'];
        $Paginate   =  $DataTable['Paginacao'];
        $_tabela = $this->MontaListaPatios($DataSet,$Model);


        $this->_response->assign("ListaPatios", "innerHTML", $_tabela);
        $this->_response->script("replaceCheckboxes();");
        $this->_response->assign("paginacao-form", "innerHTML", $Paginate);
        $this->_response->script(' $(\'[data-toggle=\"tooltip\"]\').tooltip();');
        $this->_response->script("$('[data-toggle=\"popover\"]').popover();");

        $this->_response->script("
                $('#table_patios tr td:not(.novalue)').click(function(){
                    window.location.href = $(this).parent().data(\"edit\");
                });
        ");

        $this->_response->script("

                $('#btn_send').bind('click',function(){

                    if($('div.neon-cb-replacement').hasClass('checked'))
                    {
                            fn_xjx_MessageBoxShow('Deseja realmente deleta estes registros',2,3,'',\"fn_delSelected('div.neon-cb-replacement.checked','fn_ajax_DeletaPatios','fn_ajax_DataGrid_Patios','patios')\");
                    }
                    else
                    {
                            fn_xjx_MessageNotification('alert-danger','Selecione pelo menos um registro para deletar');
                    }
                });

        ");

        return $this->_response;

    }

    /* Deleta o registro do patio */

    public function ajax_DeletaPatios($id,$PaginaIni=null,$TotalPorPagina=null)
    {
        $this->_response= new xajaxResponse();

        $model = new PatiosModel();

        $vincPatio = $model->verificarVinculoComitenteLeilao($id);


        if($vincPatio->id_patio == $id)
        {

            $this->_response->script(" fn_xjx_MessageNotification('alert-danger','Não é possivel deletar um patios vinculado a um Leilão');");

        }
        else
        {
            $this->_query = $model->Deletar($id);
            $this->_response->script('
            $("#table_patios #LT_'.$id.'").animate(
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