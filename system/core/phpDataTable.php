<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 25/08/14
 * Time: 17:42
 */

class phpDataTable extends phpDataControl{

    public $_ColumnsIndice;
    public $_ColumnsName;
    public $_Connection;
    public $_Data;
    public $_StringQuery;
    public $_FiedsTable;
    public  $_Primary_key;
    private $_sql;



    # Retorna um array com o nome das colunas da tabela especificada

    public function TablesColumns($tabela)
    {
        $this->_Connection = DataSource::getConnection('mysql');

        $this->_StringQuery = $this->_Connection->query("show columns from ".$tabela);

        $i=0;

        while($this->_Data = $this->queryDataRow($this->_StringQuery,phpDataReader::ASSOC))
        {

            if($this->_Data['Key']<>'PRI')
            {
                $this->_ColumnsIndice[$this->_Data['Field']] = $this->_Data['Type'];
                $this->_ColumnsName[$i] = $this->_Data['Field'];
                $i++;
            }
        }

        return $this->_ColumnsName;
    }


    # Pega os campos da tabela selecionada

    public function setFiedsTable($tabela)
    {
        $this->_FiedsTable = $this->TablesColumns($tabela);
    }


    # Retorna os campos da tabela setada em TablesColumns()

    public function getFieldsTabela()
    {
        return $this->TablesColumns;
    }



    # Cria um array com os campos da tabela para instrucao sql INSERT, UPDATE

    public function arrayTransform($array)
    {
        foreach($array as $key => $value)
        {
            $key = $value;
            $transform_01[] = $key;

            if($this->__post($key) != "")
            {
                if(is_numeric($this->__post($key)))
                {
                    $post = $this->__post($key);
                }
                else
                {
                    $post = ''.$this->__post($key).'';
                }

                $transform_02[] = $post;
            }
            else
            {
                $transform_02[] = "";
            }

        }

        $campos = array_combine($transform_01,$transform_02);

        return  $campos;
    }

    # Altera o valor de um elemento de um array

    public function updateArrayField(&$array,$campos, $valor)
    {
        return $array[$campos] = $valor;
    }

    # Deleta um elemento do array

    public function deleteArrayField(&$array,$campos)
    {
        if(isset($array[$campos]))
        {
            unset($array[$campos]);

        }elseif(array_values($array) == $campos){

            unset($campos);
        }

    }


    public function addFieldArray(&$array,$campo)
    {
        $push = array_push($array,$campo);

        return $push;
    }

    public function getPrimaryKey($tabela)
    {
        $this->_sql = 'show keys from '.$tabela.' where key_name = "PRIMARY"';
        $qColummns = $this->queryDataExecute($this->_sql);
        $this->_Primary_key = $this->queryDataRow($qColummns,phpDataReader::OBJ);

        return $this->_Primary_key->Column_name;
    }


} 