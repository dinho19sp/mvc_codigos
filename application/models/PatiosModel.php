<?php
    error_reporting(E_ALL);
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of PatiosModel
     *
     * @author Francisco Nascimento <d19sp.webdeveloper@outlook.com> 
     */
    class PatiosModel extends phpDataReader {
        

        private $_table = 'tb_patio';

        protected  $_patios_id;

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

        private $_primaryKey;
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


        /* Monta a tabela de lista de patioss com as opções de busca e paginação */

        public function getListaPatios(Array $options)
        {
            $this->_DataSet = $this->DataReader();
            $this->_Filter  = $this->DataFilter();
            $this->_paginacao = $this->DataPaginate();

            # Aplica filtro conforme o que for digitado

            $this->_Filter->add(new phpFilter('tb.is_deleted','=','N'),phpExpression::AND_OPERATOR);

            if($options['txtSearch'] != "")
            {
                $this->_Filter->add(new phpFilter('tb.nome_patio','LIKE','%'.$options['txtSearch'].'%'));
                //$this->_Filter->add(new phpFilter('tbp.cpf','LIKE','%'.$options['txtSearch'].'%'),phpExpression::OR_OPERATOR);


            }

            # Definindo pagina inicial

            if( $options['PaginaInicial'] == 0   ) {  $pg = 1;  }  else  { $pg = $options['PaginaInicial']; }

            $this->_paginacao->setSetPaginaAtual($pg);
            $this->_paginacao->setTotalPorPagina($options['TotalPorPagina']);
            $this->_paginacao->setIncio( $this->_paginacao->_set_pagina_atual, $this->_paginacao->_qtd_por_pagina);



            $this->_query = $this->_DataSet->queryDataSelect($this->_table.' tb
                    LEFT JOIN tb_status ts ON tb.id_status=ts.id_status
                    LEFT JOIN tb_comitente tbc ON tb.id_comitente=tbc.id_comitente
                    LEFT JOIN tb_patio_leilao tbl ON tb.id_patio=tbl.id_patio',
                'tb.id_patio as IdL,
                tbl.id_patio as IdLeilao,
                tb.nome_local,
                tbc.nome_comitente,
                tbc.id_comitente,
                tb.dt_cadastro_patio,
                tb.id_status,
                tb.email_p,
                tb.cep_r,
                tb.ddd_r,
                tb.telefone_r,
                tb.nome_responsavel,
                ts.status_nome',
                $this->_Filter->dump(), $this->_paginacao->_qtd_inicio.",".$this->_paginacao->_qtd_por_pagina,null);
                $this->_rows = $this->_DataSet->Read($this->_query);


            # Retorna o total de registros afetados conforme condicao

            $this->_paginacao->setTotalRegistros($this->_table.' tb
                    LEFT JOIN tb_status ts ON tb.id_status=ts.id_status
                    LEFT JOIN tb_comitente tbc ON tb.id_comitente=tbc.id_comitente
                    LEFT JOIN tb_patio_leilao tbl ON tb.id_patio=tbl.id_patio',$this->_Filter->dump(),'tb.id_patio as Idl,
                tbl.id_patio as IdLeilao,
                tb.nome_local,
                tbc.nome_comitente,
                tbc.id_comitente,
                tb.dt_cadastro_patio,
                tb.id_status,
                tb.email_p,
                tb.cep_r,
                tb.ddd_r,
                tb.telefone_r,
                tb.nome_responsavel,
                ts.status_nome');

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

                    $numeroPaginacao[] ='<li style="cursor:pointer;" ><a  onclick="fn_Paginacao(\'fn_ajax_DataGrid_Patios\','.$i.','.$options['TotalPorPagina'].$opt.');$(\'#pgs_patios\').prop(\'value\','.$i.');">'.$i.'</a></li>';
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

        public function setPatiosId()
        {
            $this->_patios_id = $this->app()->getApplication()->getParam('srcid');
        }

        /**
         * @return mixed
         */
        public function getPatiosId()
        {
            return $this->_patios_id;
        }


        /*
        *  public function Cadastrar();
        *
        *  Cadastra um novo patios
        *
        **/


        public function Cadastrar()
        {

            $this->TablesColumns($this->_table);
            $patioss_fields = $this->arrayTransform($this->_ColumnsName);
            $this->_primaryKey = $this->queryDataInsert($this->_table,$patioss_fields);

           $this->addDadosBancoPatio($this->_primaryKey);


            return $this->_primaryKey;
        }


        /*
         *  public function Atualizar();
         *
         *  Atualiza os dados do patios conform o id passa na url
         *  Parametro { srcid } recebe o valor do id do registro
         *  esse parametro é recuperado na Model
         *
         **/

        public function Atualizar()
        {
            $this->_Filter  = new phpCriteria();

            $this->setPatiosId();

            $this->TablesColumns($this->_table);

            $campos = $this->arrayTransform($this->_ColumnsName);

             $this->_Filter->add(new phpFilter("id_patio","=",$this->_patios_id));

            $this->_sql = $this->queryDataUpdate($this->_table,$campos,$this->_Filter->dump());

            $qry = $this->updDadosBancoPatio($this->_patios_id);

        }


        private function addDadosBancoPatio($idPatio)
        {

            $array = array(
                'id_patio',
                'id_banco',
                'num_conta',
                'num_agencia',
                'nome_titular'
            );

            $campos = $this->arrayTransform($array);
            $this->updateArrayField($campos,'id_patio',$idPatio);
            $this->_sql = $this->queryDataInsert('tb_patio_dados_banco',$campos);

            return $this->_sql;

        }

        private function updDadosBancoPatio($idPatio)
        {

            $this->_Filter  = new phpCriteria();

            $array = array(
                'id_banco',
                'num_conta',
                'num_agencia',
                'nome_titular'
            );

            $campos = $this->arrayTransform($array);
            $this->_Filter->add(new phpFilter("id_patio","=",$idPatio));
            $this->_sql = $this->queryDataUpdate('tb_patio_dados_banco',$campos,$this->_Filter->dump());

            return $this->_sql;

        }

        public function Deletar($patios_id)
        {
            $this->_Filter = new phpCriteria();
            
            if(is_array($patios_id))
            {

                foreach ($patios_id as $key => $value)
                {

                    $this->queryDataUpdate($this->_table,array("is_deleted" => "S"),"(id_patio = {$value})");

                }

            }
            else
            {
                $this->_Filter->add(new phpFilter("id_patio"," = ", $patios_id));
                
                $this->queryDataUpdate("tb_patio",array("is_deleted" => "S"),$this->_Filter->dump());

            }
        }

        public function getComitentePatio($idcomitente)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_comitente','id_comitente,nome_comitente','id_comitente ='.$idcomitente.'',null,null);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;
        }

        private function getBancoPatio($idBancoPatio)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_patio_dados_banco',null,'id_patio ='.$idBancoPatio.'',null,null);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;
        }



        public function BuscarPatios()
        {
            $this->setPatiosId();
            $data = $this->queryDataSelect($this->_table ,null,"id_patio = ".$this->_patios_id."",null,null);
            $response = $this->queryDataRow($data,phpDataReader::OBJ);

            $comitente = $this->getComitenteTransfere($response->id_comitente);
            $dadosBancario = $this->getBancoPatio($response->id_patio);

            $array = array('DadosPatio' => $response,'DadosComitente' => $comitente ,'DadosBanco' => $dadosBancario);

            return $array;
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


        public function verificarVinculoComitenteLeilao($idPatio)
        {
            $sql = $this->DataReader()->queryDataSelect('tb_patio_leilao',null,'id_patio ='.$idPatio.'',null,null);
            $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

            return $qry;


        }

        public function getComitenteTransfere($id=null,$searchData=null)
        {
            $this->_Filter = new phpCriteria();

            $this->_Filter->add(new phpFilter('is_deleted',' =','N'));
            if($id != null)
            {
                $this->_Filter->add(new phpFilter('id_comitente',' =',$id));
            }
            if($searchData != "")
            {
                $this->_Filter->add(new phpFilter('nome_comitente','LIKE','%'.$searchData.'%'));
            }

            $sql = $this->DataReader()->queryDataSelect('tb_comitente USE INDEX(IDX_PART_NAME)','id_comitente,nome_comitente',$this->_Filter->dump(),null,null);

            if($id != null)
            {
                $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);
            }
            else
            {
                $qry = $this->DataReader()->Read($sql);
            }


            return $qry;
        }
            
    }