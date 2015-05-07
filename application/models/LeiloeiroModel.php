<?php
    error_reporting(E_ALL);
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of LeiloeiroModel
     *
     * @author Francisco Nascimento <d19sp.webdeveloper@outlook.com> 
     */
    class LeiloeiroModel extends phpDataReader {
        

        private $_table = 'tb_leiloeiro';

        protected  $_leiloeiro_id;

        public $_sql;                       // Recebe uma sql

        public $_query;                     // retorna uma consulta de sql

        public $_DataForm;                  // pega o campos do formulario

        public $_condicao;

        private $_Filter;

        private $_response_array;

        /* Retorna os elementos da paginação */

        private $_paginator;

        /* Composição */

        private $_DataReader;

        /* DataSet para DataReader */

        private $_DataSet;

        /* comosicao */

        private $_paginacao;


        private $_rows;


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

        private function DataPaginate()
        {
            $this->_paginator = new xjxHelperPagiacao();

            return $this->_paginator;
        }

        /* Monta a tabela de lista de leiloeiros com as opções de busca e paginação */

        public function getListaLeiloeiro(Array $options)
        {
            $this->_DataSet = $this->DataReader();
            $this->_Filter  = $this->DataFilter();
            $this->_paginacao = $this->DataPaginate();

            # Aplica filtro conforme o que for digitado

            $this->_Filter->add(new phpFilter('tbl.is_deleted','=','N'),phpExpression::AND_OPERATOR);

            if($options['txtSearch'] != "")
            {
                $this->_Filter->add(new phpFilter('tbl.nome_leiloeiro','LIKE','%'.$options['txtSearch'].'%'));
                $this->_Filter->add(new phpFilter('tbl.cpf','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);
                $this->_Filter->add(new phpFilter('tbl.n_jucesp','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);

            }

            # Definindo pagina inicial

            if( $options['PaginaInicial'] == 0   ) {  $pg = 1;  }  else  { $pg = $options['PaginaInicial']; }

            $this->_paginacao->setSetPaginaAtual($pg);
            $this->_paginacao->setTotalPorPagina($options['TotalPorPagina']);
            $this->_paginacao->setIncio( $this->_paginacao->_set_pagina_atual, $this->_paginacao->_qtd_por_pagina);



            $this->_query = $this->_DataSet->queryDataSelect($this->_table.' tbl
                    LEFT JOIN tb_leilao tb ON tbl.id_leiloeiro = tb.id_leiloeiro
                    LEFT JOIN tb_status ts ON tbl.id_status=ts.id_status',
                'tbl.id_leiloeiro as IdL,
                tbl.nome_leiloeiro,
                tbl.dt_cadastro_leiloeiro,
                tbl.id_status as Status,
                tbl.cpf,
                tbl.ddd_r,
                tbl.telefone_r,
                tbl.n_jucesp,
                tbl.allow_preposto,
                tb.id_leiloeiro as noDelId,
                tb.nome_leilao,
                tb.codigo_leilao,
                tb.data_prevista,
                ts.id_status,
                ts.status_nome,
                tbl.is_deleted',
                $this->_Filter->dump(), $this->_paginacao->_qtd_inicio.",".$this->_paginacao->_qtd_por_pagina,null);
                $this->_rows = $this->_DataSet->Read($this->_query);


            # Retorna o total de registros afetados conforme condicao

            $this->_paginacao->setTotalRegistros($this->_table .' tbl
                    LEFT JOIN tb_leilao tb ON tbl.id_leiloeiro = tb.id_leiloeiro
                    LEFT JOIN tb_status ts ON tbl.id_status=ts.id_status',$this->_Filter->dump(),'tbl.id_leiloeiro as IdL,
                tbl.nome_leiloeiro,
                tbl.dt_cadastro_leiloeiro,
                tbl.id_status as Status,
                tbl.cpf,
                tbl.ddd_r,
                tbl.telefone_r,
                tbl.n_jucesp,
                tbl.allow_preposto,
                tb.id_leiloeiro as noDelId,
                tb.nome_leilao,
                tb.codigo_leilao,
                tb.data_prevista,
                ts.id_status,
                ts.status_nome,
                tbl.is_deleted');

            $paginator = ceil($this->_paginacao->_total_registros/$this->_paginacao->_qtd_por_pagina);


            for($i=1;$i<=$paginator;$i++)
            {
                if($i==$this->_paginacao->_set_pagina_atual)
                {
                    $numeroPaginacao[] = "<li class='active'><a onclick=''>".$i."</a></li>";
                }
                else
                {
                    if(isset($options['txtSearch']) && $options['txtSearch'] != "")
                    {
                        if(is_int($options['txtSearch']))
                        {
                            $opt = ','.$options['txtSearch'].'';
                        }else{

                            $opt = ',\''.$options['txtSearch'].'\'';
                        }

                    }

                    $numeroPaginacao[] ='<li style="cursor:pointer;" ><a  onclick="fn_Paginacao(\'fn_ajax_DataGrid_Leiloeiro\','.$i.','.$options['TotalPorPagina'].$opt.');$(\'#pgs_leiloeiro\').prop(\'value\','.$i.');">'.$i.'</a></li>';
                }
            }

            $paginate = '<div class="dataTables_paginate paging_bootstrap">';
            $paginate .='<ul class="pagination pagination-sm">';
            $paginate .= implode("",$numeroPaginacao);
            $paginate .= '</ul></div>';

            $this->_response_array = array(

                'DataRow' => $this->_rows,
                'Paginacao'  => $paginate

            );


            return $this->_response_array;

        }

        private function app()
        {
            $app = new onCreateClass();
            return $app;
        }


        public function setLeiloeiroId()
        {
            $this->_leiloeiro_id = $this->app()->getApplication()->getParam('srcid');
        }

        /**
         * @return mixed
         */
        public function getLeiloeiroId()
        {
            return $this->_leiloeiro_id;
        }


        /*
        *  public function Cadastrar();
        *
        *  Cadastra um novo leiloeiro
        *
        **/
    
        public function Cadastrar()
        {

            $this->TablesColumns($this->_table);
            $leiloeiros_fields = $this->arrayTransform($this->_ColumnsName);
            $this->_primaryKey = $this->queryDataInsert($this->_table,$leiloeiros_fields);
            return $this->_primaryKey;
        }


        /*
         *  public function Atualizar();
         *
         *  Atualiza os dados do leiloeiro conform o id passa na url
         *  Parametro { srcid } recebe o valor do id do registro
         *  esse parametro é recuperado na Model
         *
         **/

        public function Atualizar()
        {
            $this->_Filter  = new phpCriteria();

            $this->setLeiloeiroId();
            $this->TablesColumns($this->_table);
            $campos = $this->arrayTransform($this->_ColumnsName);

            $this->_Filter->add(new phpFilter("id_leiloeiro","=" ,$this->_leiloeiro_id));

            $this->_sql = $this->queryDataUpdate($this->_table,$campos,$this->_Filter->dump());


        }

        public function Deletar($leiloeiro_id)
        {
            $this->_Filter = new phpCriteria();
            
            if(is_array($leiloeiro_id))
            {

                foreach ($leiloeiro_id as $key => $value)
                {

                    $this->queryDataUpdate($this->_table,array("is_deleted" => "S"),"(id_leiloeiro = {$value})",true);

                }

            }
            else
            {
                $this->_Filter->add(new phpFilter("id_leiloeiro"," = ", $leiloeiro_id));
                
                $this->queryDataUpdate("tb_leiloeiro",array("is_deleted" => "S"),$this->_Filter->dump());

            }
        }

        public function BuscarLeiloeiro()
        {
            $this->setLeiloeiroId();
            $data = $this->queryDataProcedure('SP_GET_LEILOEIRO',array($this->_leiloeiro_id));
            $response = $data[0];
            return $response;
        }

        public function returnStatus($intStatus)
        {
            switch($intStatus)
            {
                case 1:
                    $class = "badge-success";
                 break;
                case 2:
                    $class = "badge-danger";
                break;
            }
            return $class;

        }

        public function verificarVinculoPreposto($idLeiloeiro)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_preposto','id_leiloeiro','id_leiloeiro ='.$idLeiloeiro);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);



            return $qry;


        }

        public function verificarVinculoLeilao($idLeiloeiro)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_leilao','id_leiloeiro,id_status,codigo_leilao','id_leiloeiro ='.$idLeiloeiro);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;


        }
            
    }