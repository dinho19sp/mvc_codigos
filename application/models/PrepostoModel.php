<?php
    error_reporting(E_ALL);
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of PrepostoModel
     *
     * @author Francisco Nascimento <d19sp.webdeveloper@outlook.com> 
     */
    class PrepostoModel extends phpDataReader {
        

        private $_table = 'tb_preposto';

        protected  $_preposto_id;

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

        private function app()
        {
            $app = new onCreateClass();
            return $app;
        }


        /* Monta a tabela de lista de prepostos com as opções de busca e paginação */

        public function getListaPreposto(Array $options)
        {
            $this->_DataSet = $this->DataReader();
            $this->_Filter  = $this->DataFilter();
            $this->_paginacao = $this->DataPaginate();

            # Aplica filtro conforme o que for digitado

            $this->_Filter->add(new phpFilter('tbp.is_deleted','=','N'),phpExpression::AND_OPERATOR);

            if($options['txtSearch'] != "")
            {
                $this->_Filter->add(new phpFilter('tbp.nome_preposto','LIKE','%'.$options['txtSearch'].'%'));
                $this->_Filter->add(new phpFilter('tbp.cpf','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);


            }

            # Definindo pagina inicial

            if( $options['PaginaInicial'] == 0   ) {  $pg = 1;  }  else  { $pg = $options['PaginaInicial']; }

            $this->_paginacao->setSetPaginaAtual($pg);
            $this->_paginacao->setTotalPorPagina($options['TotalPorPagina']);
            $this->_paginacao->setIncio( $this->_paginacao->_set_pagina_atual, $this->_paginacao->_qtd_por_pagina);



            $this->_query = $this->_DataSet->queryDataSelect($this->_table.' tbp
                LEFT JOIN tb_status ts ON tbp.id_status=ts.id_status',
                'tbp.id_preposto as IdL,
                tbp.nome_preposto,
                tbp.dt_cadastro_preposto,
                tbp.id_status as Status,
                tbp.cpf,
                tbp.ddd_r,
                tbp.telefone_r,
                tbp.id_leiloeiro,
                ts.id_status,
                ts.status_nome,
                tbp.is_deleted',
                $this->_Filter->dump(), $this->_paginacao->_qtd_inicio.",".$this->_paginacao->_qtd_por_pagina,null);
                $this->_rows = $this->_DataSet->Read($this->_query);


            # Retorna o total de registros afetados conforme condicao

            $this->_paginacao->setTotalRegistros($this->_table.' tbp
                LEFT JOIN tb_status ts ON tbp.id_status=ts.id_status',$this->_Filter->dump(),'tbp.id_preposto as IdL,
                tbp.nome_preposto,
                tbp.dt_cadastro_preposto,
                tbp.id_status as Status,
                tbp.cpf,
                tbp.ddd_r,
                tbp.telefone_r,
                tbp.id_leiloeiro,
                ts.id_status,
                ts.status_nome,
                tbp.is_deleted');

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

                    $numeroPaginacao[] ='<li style="cursor:pointer;" ><a  onclick="fn_Paginacao(\'fn_ajax_DataGrid_Preposto\','.$i.','.$options['TotalPorPagina'].$opt.');$(\'#pgs_preposto\').prop(\'value\','.$i.');">'.$i.'</a></li>';
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

        public function setPrepostoId()
        {
            $this->_preposto_id = $this->app()->getApplication()->getParam('srcid');
        }

        /**
         * @return mixed
         */
        public function getPrepostoId()
        {
            return $this->_preposto_id;
        }


        /*
        *  public function Cadastrar();
        *
        *  Cadastra um novo preposto
        *
        **/

        public function getLeiloeiroPreposto($id)
        {
            $sql = $this->queryDataSelect('tb_leiloeiro','id_leiloeiro,nome_leiloeiro','id_leiloeiro = '.$id.'',null,null);
            $qry = $this->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;
        }

        public function Cadastrar()
        {

            $this->TablesColumns($this->_table);
            $prepostos_fields = $this->arrayTransform($this->_ColumnsName);
            $this->_primaryKey = $this->queryDataInsert($this->_table,$prepostos_fields);
            return $this->_primaryKey;
        }


        /*
         *  public function Atualizar();
         *
         *  Atualiza os dados do preposto conform o id passa na url
         *  Parametro { srcid } recebe o valor do id do registro
         *  esse parametro é recuperado na Model
         *
         **/

        public function Atualizar()
        {
            $this->_Filter  = new phpCriteria();

            $this->setPrepostoId();
            $this->TablesColumns($this->_table);
            $campos = $this->arrayTransform($this->_ColumnsName);

            $this->_Filter->add(new phpFilter("id_preposto","=" ,$this->_preposto_id));

            $this->_sql = $this->queryDataUpdate($this->_table,$campos,$this->_Filter->dump());


        }

        public function Deletar($preposto_id)
        {
            $this->_Filter = new phpCriteria();
            
            if(is_array($preposto_id))
            {

                foreach ($preposto_id as $key => $value)
                {

                    $this->queryDataUpdate($this->_table,array("is_deleted" => "S"),"(id_preposto = {$value})");

                }

            }
            else
            {
                $this->_Filter->add(new phpFilter("id_preposto"," = ", $preposto_id));
                
                $this->queryDataUpdate("tb_preposto",array("is_deleted" => "S"),$this->_Filter->dump());

            }
        }

        public function BuscarPreposto()
        {
            $this->setPrepostoId();
            $data = $this->queryDataSelect($this->_table . ' tbp LEFT JOIN tb_leiloeiro tbl USING(id_leiloeiro)','tbp.*,tbl.nome_leiloeiro,tbp.id_leiloeiro',"id_preposto = ".$this->_preposto_id."",null,null);
            $response = $this->queryDataRow($data,phpDataReader::OBJ);

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


        public function verificarVinculoPrepostoLeilao($idPreposto)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_leilao','id_preposto,id_status,codigo_leilao','id_preposto ='.$idPreposto.'',null,null);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;


        }

        public function getLeiloeiroTransfere($id)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_leiloeiro','id_leiloeiro,nome_leiloeiro','id_leiloeiro ='.$id.'',null,null);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;
        }
            
    }