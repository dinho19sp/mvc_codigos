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

class LeiloeiroHelper {

    private $_table = 'tb_leiloeiro';

    private $_response_table;

    private $_response;

    private $_response_array;

    private $_paginator;

    private $_DataReader;

    private $_DataSet;

    private $_Filter;

    private $_Options;

    private $_query;

    private $_rows;

    private $_sql;



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



    /* Monta a tabela de lista de leiloeiros com as opções de busca e paginação */

    public function MontaListaLeiloeiro($DataSet,LeiloeiroModel $Model)
    {


        $this->_response_table = '     <form id="form-2" name="form-2" method="post">';
        $this->_response_table .= '         <input type="hidden" name="deletar" id="deletar" value="">';
        $this->_response_table .= '         <table class="table table-bordered table-striped table-hover" id="table_leiloeiro">';
        $this->_response_table .= '             <thead>';
        $this->_response_table .= '                <th>Opções</th>';
        $this->_response_table .= '                 <th># ID</th>';
        $this->_response_table .= '                 <th>Nome Leiloeiro</th>';
        $this->_response_table .= '                 <th>Telefone Res</th>';
        $this->_response_table .= '                 <th>Numero Jucesp</th>';
        $this->_response_table .= '                 <th colspan="3" style="text-align: center;">Situa&ccedil;&atilde;o</th>';
        $this->_response_table .= '        </thead>';
        $this->_response_table .= '        <tbody>';

        if($DataSet != "")
        {

            foreach($DataSet as $Indice => $data )
            {

                $status = $Model->returnStatus($data->id_status);
                $leiloeiroVinculado = $Model->verificarVinculoLeilao($data->IdL);
                $prepostoVinculado = $Model->verificarVinculoPreposto($data->IdL);

                if($prepostoVinculado->id_leiloeiro == $data->IdL)
                {
                    $preposto ='&nbsp;<span class="badge badge-info tooltip-primary" data-toggle="tooltip" data-placement="top" title="Vinculado ao Preposto" data-original-title="Vinculado ao Preposto"><i class="fa fa-flag-o"></i></span>';
                }
                else
                {
                    $preposto ='&nbsp;';
                }

                if($leiloeiroVinculado->id_leiloeiro == $data->IdL)
                {
                    $leiloeiro ='&nbsp;<span class="badge badge-secondary tooltip-secondary" data-toggle="tooltip" data-placement="top" title="Vinculado ao leilão : '.$leiloeiroVinculado->codigo_leilao.'" data-original-title="Vinculado ao leilão : '.$leiloeiroVinculado->codigo_leilao.'"><i class="fa fa-flag"></i></span>';
                }
                else
                {
                    $leiloeiro ='&nbsp;';
                }



                $this->_response_table .= '             <tr   id="LT_'.$data->IdL.'" data-id="'.$data->IdL.'" data-edit="'.$this->app()->getFunctions()->goToUrl('leiloeiro','editar-leiloeiro',array('srcid' =>  $data->IdL ) ).'" data-placement="top" data-original-title="" title="Clique para editar">';
                $this->_response_table .= '                <td class="novalue" style="width: 5%; text-align: center;" >';
                $this->_response_table .= '                     <div class="checkbox checkbox-replace color-red">';
				$this->_response_table .= '				            <input type="checkbox" id="'.$data->IdL.'" name="id_leiloeiro[]">';
                $this->_response_table .= '				            <label></label>';
                $this->_response_table .= '			             </div>';
                $this->_response_table .= '                </td>';
                $this->_response_table .= '                <td># '.str_pad($data->IdL, 3, "0", STR_PAD_LEFT).'</td>';
                $this->_response_table .= '                <td>'.$data->nome_leiloeiro.'</td>';
                $this->_response_table .= '                <td hidden-tablet">'.$data->ddd_r . '&nbsp;-&nbsp;' . $data->telefone_r.'</td>';
                $this->_response_table .= '                <td>'.$data->n_jucesp.'</td>';
                $this->_response_table .= '                <td id="TD_'.$data->IdL.'" style="width:10%; text-align: center;"><span class="badge '.$status.'">'.$data->status_nome.'</span></td>';
                if($leiloeiro != '' || $preposto != '')
                {
                    $this->_response_table .= '                <td style="width:8%; text-align: center;">'.$preposto.$leiloeiro.'</td>';
                }
                if($this->app()->getElementosRoot()->getCookie('GET_LEILOEIRO') && $data->allow_preposto == 1)
                {
                    $this->_response_table .= '                <td class="novalue" style="width:5%; text-align: center;" onclick="fn_getClipboardLeiloeiro('.$data->IdL.');"><span class="fa fa-copy popover-default"  data-toggle="popover" data-trigger="hover" data-placement="left" data-content="Clique para selecionar o leiloeiro a ser vinculado ao preposto" data-original-title="Selecionar Leiloeiro" style="font-size:18px; color:green; cursor:pointer;"></span> </td>';
                }
                else
                {
                    $this->_response_table .= '                <td class="novalue" style="width:5%; text-align: center;" >&nbsp; </td>';

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

    public function ajax_DataGrid_Leiloeiro($PaginaIni,$TotalPorPagina = 0, $Data=NULL)
    {

        $this->_response = new xajaxResponse();

        $this->_Options = array(
            'PaginaInicial'     => $PaginaIni,
            'TotalPorPagina'    => $TotalPorPagina,
            'txtSearch'         => $Data
        );


        $Model = new LeiloeiroModel();
        $DataTable  =  $Model->getListaLeiloeiro($this->_Options);
        $DataSet    =  $DataTable['DataRow'];
        $Paginate   =  $DataTable['Paginacao'];
        $_tabela = $this->MontaListaLeiloeiro($DataSet,$Model);


        $this->_response->assign("ListaLeiloeiro", "innerHTML", $_tabela);

        $this->_response->assign("paginacao-form", "innerHTML", $Paginate);
        $this->_response->script(' $(\'[data-toggle=\"tooltip\"]\').tooltip();');
        $this->_response->script("$('[data-toggle=\"popover\"]').popover();");
        $this->_response->script("replaceCheckboxes();");
        $this->_response->script("

                $('#table_leiloeiro tr td:not(.novalue)').click(function(){

                    window.location.href = $(this).parent().data(\"edit\");

                });


        ");

        $this->_response->script("

                $('#btn_send').bind('click',function(){

                    if($('div.neon-cb-replacement').hasClass('checked'))
                    {
                            fn_xjx_MessageBoxShow('Deseja realmente deleta estes registros',2,3,'',\"fn_delSelected('div.neon-cb-replacement.checked','fn_ajax_DeletaLeiloeiro','fn_ajax_DataGrid_Leiloeiro','leiloeiro')\");


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

    public function ajax_DeletaLeiloeiro($id,$PaginaIni=null,$TotalPorPagina=null)
    {
        $this->_response= new xajaxResponse();

        $model = new LeiloeiroModel();

        $vincPreposto = $model->verificarVinculoPreposto($id);
        $vincLeilao = $model->verificarVinculoLeilao($id);


        if($vincLeilao->id_leiloeiro == $id)
        {
            $this->_response->script(" fn_xjx_MessageNotification('alert-danger','Não é possivel deletar um Leiloeiro vinculado a um Leilão');");
        }
        elseif($vincPreposto->id_leiloeiro != $id)
        {
            $this->_query = $model->Deletar($id);
            $this->_response->script('
            $("#table_leiloeiro #LT_'.$id.'").animate(
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
        else
        {
            $this->_response->script("fn_xjx_MessageNotification('alert-danger','Não é possivel deletar um Leiloeiro vinculado ao Preposto');");
        }



        return $this->_response;
    }



} 