<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 25/08/14
 * Time: 16:57
 */

class phpDataReader extends phpDataTable{

    public $_stringQuery;
    public $_queryExecute;
    public $_lastInsertId;
    public $_connection_sp;
    public $_LoadDataSp;
    private $_sp_nome;
    # Constants

    const OBJ =     0x01;
    const ASSOC =   0x02;
    const NUM =     0x03;



    public function queryDataProcedure($SP_NAME,$param=NULL,$stringQuery=FALSE)
    {
        if(is_array($param))
        {
            foreach($param as $key => $val)
            {
                if(is_string($val))
                {
                    $values[] = "'{$val}'";
                }
                else
                {
                    $values[] = $val;
                }

            }

            $parametros = implode(",",$values);
        }
        else
        {
            if(isset($param))
            {
                $parametros = $param;
            }
            else
            {
                $parametros = "";
            }
        }


        $this->_sp_nome = "CALL  $SP_NAME($parametros);";

        if($stringQuery == TRUE)
        {
            print $this->_sp_nome;

        }

        $this->_connection_sp = DataSource::getConnection("mysql");
        $query = $this->_connection_sp->query($this->_sp_nome);

        $this->_LoadDataSp = $this->Read($query);

        return $this->_LoadDataSp;

    }

    public function queryDataSelect($_tabela,$_campos=NULL,$_condicao=NULL,$_limite=NULL,$_order=NULL,$debug=FALSE)
    {
        $this->setWhere($_condicao);
        $this->setLimit($_limite);
        $this->setOrderBy($_order);
        $this->setFieldQuery($_campos);

        $this->_stringQuery = $this->__select($this->_Fields_by_query).$_tabela.$this->_where.$this->_order_by.$this->_limit_by;

        if($debug === TRUE)
        {

            echo $this->_stringQuery;
        }

        $this->queryExecute = $this->queryDataExecute($this->_stringQuery);

        return $this->queryExecute;
    }

    public function queryDataInsert($_tabela,$_campos=NULL,$debug=FALSE)
    {

        $this->_stringQuery = $this->__insert($_campos,$_tabela);

        $this->_queryExecute = $this->queryDataExecute($this->_stringQuery);

        $this->_lastInsertId = $this->_Connection->lastInsertId();

        if($debug === TRUE)
        {

            $this->debug($this->_stringQuery,"P");

        }

        return $this->_lastInsertId;
    }

    public function queryDataUpdate($_tabela,$_campos=NULL,$_condicao=NULL,$debug=FALSE)
    {

        $this->setWhere($_condicao);

        $this->_stringQuery = $this->__update($_campos,$_tabela) . $this->_where;

        $this->_queryExecute = $this->queryDataExecute($this->_stringQuery);

        $rows = $this->_queryExecute->rowCount();

        if($debug === TRUE)
        {

            $this->debug($this->_stringQuery,"D");

        }

        return $rows;
    }

    public function queryDataDelete($_tabela,$_condicao=NULL,$debug=FALSE)
    {
        $this->setWhere($_condicao);

        $this->_stringQuery = $this->__delete($_tabela,$this->_where);

        if($debug === TRUE){

            $this->debug($this->_stringQuery,"D");

        }

       $this->_queryExecute = $this->queryDataExecute($this->_stringQuery);
        return $this->_queryExecute;
    }

    public function queryDataExecute($StringSql)
    {
        try{
            $this->_Connection = DataSource::getConnection('mysql');

            if($StringSql != "")
            {

                $query = $this->_Connection->query($StringSql);

            }else{

                throw new Exception("Comando Sql: O comando sql nao foi especificado");

            }

            return $query;

        } catch (Exception $qry){

            $qry->getMessage();

            exit;
        }
    }

    public function Read($sql)
    {
        $arr = NULL;

        while($query=$this->queryDataRow( $sql,phpDataReader::OBJ))
        {

                $arr[] = $query;

        }
        return $arr;
    }

    public function queryDataRow($result,$type)
    {
        if(!$result)
        {
            throw new Exception(" Erro ao retornar os dados solicitados ");
        }
        else
        {
            switch($type)
            {
                case self::OBJ :
                    $records = $result->fetch(PDO::FETCH_OBJ);
                    break;
                case self::ASSOC:
                    $records = $result->fetch(PDO::FETCH_ASSOC);
                    break;
                case self::NUM :
                    $records = $result->fetch(PDO::FETCH_NUM);
            }

            return $records;
        }
    }

    public function phpException($sTrace, $Message=NULL)
    {
        $trace  = explode("#",$sTrace);
        $nTrace = explode("\\",$trace[1]);
        $rTrace = array_pop($nTrace);
        $abstract = explode(" ",$rTrace);
        $location = explode("\\",$trace[2]);

        return array($abstract[0],$location[6],$Message);

    }
} 