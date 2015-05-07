<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 30/03/15
 * Time: 10:14
 */

class SystemLogHelper{

    private $_query;
    private $_DataReader;
    private $_DataSet;
    private $_Filter;
    private $_DataFilter;
    private $_table = 'tb_sys_log';

    private function DataReader()
    {
        $this->_DataReader = new phpDataReader();

        return $this->_DataReader;
    }

    private function DataFilter()
    {
        $this->_Filter = new phpCriteria();

        return $this->_Filter;

    }

    public function getSystemLogUser($idUser=NULL)
    {
        # Retorna o id do usuario e o nome do usuario que efetuaou a ação da tabela log

        $this->_DataSet = $this->DataReader();
        $this->_DataFilter = $this->DataFilter();

        $this->_DataFilter->add(new phpFilter('id_usuario','=',$idUser));


        $this->_query = $this->_DataSet->queryDataSelect('tb_usuarios','id_usuario,nome_usuario',$this->_DataFilter->dump(),null,null);

        $this->_DataRow = $this->_DataSet->queryDataRow($this->_query,phpDataReader::OBJ);


        return $this->_DataRow;


    }

    public function getSystemLog(Array $DataSet)
    {
        $this->_DataSet = $this->DataReader();
        $this->_DataFilter = $this->DataFilter();


        $ord = array('lg.data_access');
        /*
         *  Verifica se existe as opçoes de filtro
         *
         *
         * */
        $rows = '';



            if($DataSet['txtSearch'] != "")
            {
                $this->_DataFilter->add(new phpFilter('tbu.nome_usuario','LIKE','%'.$DataSet['txtSearch'].'%'),phpExpression::OR_OPERATOR);
                $this->_DataFilter->add(new phpFilter('lg.action','LIKE','%'.$DataSet['txtSearch'].'%'),phpExpression::OR_OPERATOR);
                $this->_DataFilter->add(new phpFilter('lg.data_access','LIKE','%'.Functions::fncDataInversa($DataSet['txtSearch']).'%'),phpExpression::OR_OPERATOR);
            }


        if($DataSet['limiteInicio'] == 0)
        {
            $pg = 1;
        }
        else
        {
            $pg = $DataSet['limiteInicio'];
        }


        /* Pega as opçoes para criar paginação */

        $paginacao = new xjxHelperPagiacao();

        $paginacao->setSetPaginaAtual($pg);
        $paginacao->setTotalPorPagina($DataSet['limiteFim']);
        $paginacao->setIncio($paginacao->_set_pagina_atual,$paginacao->_qtd_por_pagina);
        //var_dump($paginacao->_set_pagina_atual);
        $this->_query = $this->_DataSet->queryDataSelect(
            $this->_table .' lg LEFT JOIN tb_usuarios tbu ON (lg.user_logged = tbu.id_usuario)',
            'lg.*,tbu.nome_usuario,tbu.id_usuario',
            $this->_DataFilter->dump(),
            $paginacao->_qtd_inicio.",".$paginacao->_qtd_por_pagina,
            $this->_DataFilter->orderBy($ord,'DESC')
        );

        $this->rows = $this->_DataSet->Read($this->_query);



        /* Retorna o total de registros afetados conforme condicao */




        $paginacao->setTotalRegistros($this->_table .' lg LEFT JOIN tb_usuarios tbu ON (lg.user_logged = tbu.id_usuario)',$this->_DataFilter->dump(),'lg.*,tbu.nome_usuario,tbu.id_usuario');
        $paginator = ceil($paginacao->_total_registros/$paginacao->_qtd_por_pagina);


        for($i=1;$i<=$paginator;$i++)
        {
            if($i==$paginacao->_set_pagina_atual)
            {
                $numeroPaginacao[] = "<li class='active'><a onclick=''>".$i."</a></li>";
            }
            else
            {
                $numeroPaginacao[] ='<li style="cursor:pointer;" ><a  onclick="fn_Paginacao(\'fn_xjx_get_log\','.$i.','.$DataSet['limiteFim'].',$(\'#txtSearch\').val());$(\'#pgs\').prop(\'value\','.$i.');">'.$i.'</a></li>';
            }
        }

        $paginate = '<div class="dataTables_paginate paging_bootstrap">';
        $paginate .='<ul class="pagination pagination-sm">';
        $paginate .= implode("",$numeroPaginacao);
        $paginate .= '</ul></div>';

        $multiArray = array(
          'Dados' => $this->rows,
          'Numeros'  => $paginate
        );


        return $multiArray;
    }


    public function responseSistemaLog($arrayDados)
    {



        $tabela = '<table class="table  Hover Rows" id="tb_log">';
        $tabela .= '<thead>';
        $tabela .= '<tr>';
        $tabela .= '<th style="width: 8%;">#Codigo</th>';
        $tabela .= '<th style="width: 20%;">Quem Fez</th>';
        $tabela .= '<th >Onde Fez</th>';
        $tabela .= '<th>O que fez</th>';
        $tabela .= '<th>Registro afetado</th>';
        $tabela .= '<th>Data</th>';
        $tabela .= '<th>Detalhes</th>';
        $tabela .= '</tr>';
        $tabela .= '</thead>';
        $tabela .= '<tbody>';

        foreach($arrayDados as $k => $v)
        {


            $user = $this->getSystemLogUser($v->user_logged);

            if($user->nome_usuario == "")
            {
                $usuario = "Usuario não existe";
            }
            else
            {
                $usuario = $user->nome_usuario;
            }

            $tbRegistro = explode("_",$v->tabela_of_action);

            $tbs = implode(" ",$tbRegistro);

            $tb = strtoupper($tbs);



            $tabela .= '<tr data-id="'.$v->id_log.'">';
            $tabela .= '    <td>'.str_pad($v->id_log,4,"0",STR_PAD_LEFT).'</td>';
            $tabela .= '    <td>'.$usuario.'</td>';
            $tabela .= '    <td>'.$tb.'</td>';
            $tabela .= '    <td>'.$v->action.'</td>';
            $tabela .= '    <td >'.$v->id_of_registry.'</td>';
            $tabela .= '    <td>'.Functions::fncDataPadrao($v->data_access).'</td>';
            $tabela .= '    <td style="text-align: center;"><a class=""><i class="fa fa-search" style="font-size: 16px; cursor: pointer;"></i> </a> </td>';
            $tabela .= ' </tr>';

        }

        $tabela .= '</tbody>';
        $tabela .= '</table>';

        return array("TAB" =>$tabela);

    }

    public function setCampos()
    {
        $campos = array(
            'query_odl',
            'query_new',
            'id_log',
            'user_ip',
            'url',
            'browser',
            'data_access',
            'hora,action'
        );

        return $campos;
    }

    public function detalhesLogSistema($idLog)
    {
        $this->_Filter = new phpCriteria();
        $this->_Filter->add(new phpFilter('id_log','=',$idLog));

        $campos = $this->setCampos();

        $this->_query = $this->DataReader()->queryDataSelect($this->_table,$campos,$this->_Filter->dump());
        $rows = $this->DataReader()->queryDataRow($this->_query,phpDataReader::OBJ);

        $_Data = array(
            'OLD' => $this->_unserialize($rows->query_odl),
            'NEW' => $this->_unserialize($rows->query_new),
            'ID' => str_pad($rows->id_log,4,"0",STR_PAD_LEFT),
            'IP' => $rows->user_ip,
            'PRT' => $rows->url,
            'NAV' => $rows->browser,
            'DT' => Functions::fncDataPadrao($rows->data_access),
            'HR' => $rows->hora,
            'AC' => $rows->action
        );

        return $_Data;

    }

    public  function getQueryLogOld($idLog)
    {
        $data = $this->detalhesLogSistema($idLog);

        if($data['OLD'] != "" && $data['NEW']=="")
        {
            $titulo = "Dados removidos";
            $size = 12;

        }
        elseif($data['OLD'] != "" && $data['NEW']!="")
        {
            $titulo = "Dados anteriores";
            $size = 6;
        }

        if($data['OLD'] != "")
        {


            $data_old = '<div class="col-md-'.$size.'">';
            $data_old .='   <div class="panel panel-warning" data-collapsed="0">';
            $data_old .='       <div class="panel-heading">';
            $data_old .='           <div class="panel-title">'.$titulo.'</div>';
            $data_old .='               <div class="panel-options">';
            $data_old .='               </div>';
            $data_old .='          </div>';
            $data_old .='          <div class="panel-body">';
            $data_old .='               <table class="table table-condensed">';
            $data_old .='                   <thead>';
            $data_old .='                       <tr>';

            $data_old .='                       <th>Item</th>';
            $data_old .='                       <th>Valor</th>';
            $data_old .='                       </tr>';
            $data_old .='                   </thead>';
            $data_old .='                   <tbody>';

            foreach($data['OLD'] as $Campo => $valor)
            {

                $Campo = explode("_",$Campo);

                $Ca = array_shift($Campo);

                $Campo = implode(" ",$Campo);

                $data_old .='<tr>';

                $data_old .='   <td>'.strtoupper($Campo." ".$Ca).'</td>';
                $data_old .='   <td>'.$valor.'</td>';
                $data_old .='</tr>';

            }
            $data_old .='                   </tbody>';
            $data_old .='               </table>';

            $data_old .='        </div>';
            $data_old .='    </div>';
            $data_old .='</div>';

        }
        else
        {
            $data_old = '';
        }

        return $data_old;
    }

    public function getDadosHeadLog($idLog)
    {
        $head = $this->detalhesLogSistema($idLog);
        $browser =explode("::",$head['NAV']);
        $nav = explode(":",$browser[0]);
        $disp = explode(":",$browser[1]);

            $dados = '<div class="col-md-12">';
            $dados .= '<div class="panel panel-success" data-collapsed="0"> <!-- panel head -->';
            $dados .= '        <div class="panel-heading">';
            $dados .= '            <div class="panel-title">Dados geral do log</div>';
            $dados .= '        </div> <!-- panel body -->';
            $dados .= '        <div class="panel-body">';
            $dados .= '           <p><label>Data e hora:</label> <code class="badge badge-info">'.$head['DT'].' - '.$head['HR'].'</code></p>';
            $dados .= '           <p><label>Navegador:</label> <code class="badge badge-info">'.$nav[1].'</code> </p>';
            $dados .= '           <p><label>Dispositivo:</label> <code class="badge badge-info">'.$disp[1].'</code></p>';
            $dados .= '           <p><label>IP do Usuário:</label> <code class="badge badge-info">'.$head['IP'].'</code></p>';
            $dados .= '          <p><label>Ação executada:</label> <code class="badge badge-info">'.$head['AC'].'</code></p>';
            $dados .= '       </div>';
            $dados .= '  </div>';
            $dados .= '  </div>';



        return $dados;
    }

    public function getQueryLogNew($idLog)
    {
        $data = $this->detalhesLogSistema($idLog);

        if($data['OLD'] == "" && $data['NEW']!="")
        {
            $titulo = "Novos Dados adicionado";
            $size = 12;

        }
        elseif($data['OLD'] != "" && $data['NEW']!="")
        {
            $titulo = "Dados atualizados";
            $size = 6;
        }

        if($data['NEW'] != "")
        {


            $data_new = '<div class="col-md-'. $size.'">';
            $data_new .='   <div class="panel panel-success" data-collapsed="0">';
            $data_new .='       <div class="panel-heading">';
            $data_new .='           <div class="panel-title">'.$titulo.'</div>';
            $data_new .='               <div class="panel-options">';
            $data_new .='               </div>';
            $data_new .='          </div>';
            $data_new .='          <div class="panel-body">';
            $data_new .='               <table class="table table-condensed">';
            $data_new .='                   <thead>';
            $data_new .='                       <tr>';

            $data_new .='                       <th>Item</th>';
            $data_new .='                       <th>Valor</th>';
            $data_new .='                       </tr>';
            $data_new .='                   </thead>';
            $data_new .='                   <tbody>';

                foreach($data['NEW'] as $Campo => $valor)
                {

                    $Campo = explode("_",$Campo);

                    $Ca = array_shift($Campo);

                    $Campo = implode(" ",$Campo);

                    $data_new .='<tr>';

                    $data_new .='   <td>'.strtoupper($Campo." ".$Ca).'</td>';
                    $data_new .='   <td>'.$valor.'</td>';
                    $data_new .='</tr>';

                }
            $data_new .='                   </tbody>';
            $data_new .='               </table>';

            $data_new .='        </div>';
            $data_new .='    </div>';
            $data_new .='</div>';

        }
        else
        {
            $data_new = '';
        }


        return $data_new;
    }


    public  function _unserialize($serial)
    {
        if(!is_null($serial))
        {
            $data = unserialize($serial);
        }else
        {
            $data = FALSE;
        }

        return $data;
    }

} 