<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 16/09/14
 * Time: 00:38
 */

class phpFormComponents extends phpDataReader{

    private $_getTable;
    private $_getListValue;
    private $_getTextList;
    private $_getValueSelected=NULL;
    private $_ListCondicao = NULL;
    private $_addOption = NULL;

    /**
     * @param mixed $getListValue
     */
    public function setGetListValue($getListValue)
    {
        $this->_getListValue = $getListValue;
    }

    /**
     * @return mixed
     */
    public function getGetListValue()
    {
        return $this->_getListValue;
    }

    /**
     * @param mixed $getTable
     */
    public function setGetTable($getTable)
    {
        $this->_getTable = $getTable;
    }

    /**
     * @return mixed
     */
    public function getGetTable()
    {
        return $this->_getTable;
    }

    /**
     * @param mixed $getTextList
     */
    public function setGetTextList($getTextList)
    {
        $this->_getTextList = $getTextList;
    }

    /**
     * @return mixed
     */
    public function getGetTextList()
    {
        return $this->_getTextList;
    }

    /**
     * @param null $getValueSelected
     */
    public function setGetValueSelected($getValueSelected)
    {
        $this->_getValueSelected = $getValueSelected;
    }

    /**
     * @return null
     */
    public function getGetValueSelected()
    {
        return $this->_getValueSelected;
    }

    /**
     * @param null $ListCondicao
     */
    public function setListCondicao($ListCondicao)
    {
        $this->_ListCondicao = $ListCondicao;
    }

    /**
     * @return null
     */
    public function getListCondicao()
    {
        return $this->_ListCondicao;
    }

    private function setEmptyOption($opt=NULL)
    {
        if(isset($opt) && $opt != NULL)
        {
            $this->_addOption = $opt;
        }

    }

    private function getEmptyOption()
    {
        return $this->_addOption;

    }

    public function addOptionValue($OtionName=NULL,$OptionValue=NULL)
    {
        $this->setEmptyOption($OtionName);

        if($OptionValue == NULL)
        {
            $opt = "";
        }
        else
        {
            $opt = $OptionValue;
        }

        if($this->_addOption != NULL)
        {
            echo "<option value='' >{$OtionName}</option>";
        }


    }

    private function __selDataList($valor1, $valor2)
    {
        if ($valor1 == $valor2)
        {
            $sel = "selected";
        }
        else
        {
            $sel = "";
        }

        return $sel;
    }

    /**
     * @param $type String com o tipo de cpmponete de formulario a se criado (ex: Button, text, Hidden, checkbox,etc..)
     * @param array $opt Recebe parametros a ser definido (ex: array("name" => "teste1", "id" => "idteste", "value" => 1);
     * @return string Componente formatado
     */
    public function __formController($type,Array $opt = NULL)
    {
        $bt= "<input  type = '{$type}'";

        if($opt != NULL)
        {
            foreach($opt as $k => $vl)
            {
                $set[] = $k ."='{$vl}'";
            }
        }
        $st = implode(" ",$set);

        $bt .= $st . " />";

        return $bt;
    }

    /*public function CheckBox($setTable,$setListValue,$setTextList, $setNameCheckbox=NULL,$setCondition=NULL) {

        $this->setGetTable($setTable);
        $this->setGetListValue($setListValue);
        $this->setGetTextList($setTextList);
        $this->setListCondicao($setCondition);


        $ck = "";
        $this->_addOption;
        if($this->_getTable =="" OR $this->_getTable==NULL)
        {
            $ck .= "<span> ERRO: A Entidade do banco não foi informada! </span>";
        }
        elseif($this->_getListValue =="" OR $this->_getListValue == NULL)
        {
            $ck .= "<span> ERRO: informe o nome do campo value </span>";
        }
        elseif($this->_getTextList=="" OR $this->_getTextList==NULL)
        {
            $ck .= "<span> ERRO: informe o nome do campo text </span>";
        }
        else
        {
            $seld="";

            $s = $this->queryDataSelect($this->_getTable,NULL,$this->_ListCondicao);
            while($d = $this->queryDataRow($s, phpDataReader::ASSOC))
            {
                $ck .= "<div class='checkbox'><label><input type='checkbox' id='{$d[$this->_getListValue]}' name='{$setNameCheckbox}[]' value='{$d[$this->_getListValue]}' ".$seld." >";
                $ck .= $d[$this->_getTextList]."</label></div>";
            }

            print $ck;
        }
    }*/

    public function DropDownList($setTable,$setListValue,$setTextList,$setValueSelected=NULL,$setCondition=NULL,$orderBy=null)
    {
        $this->setGetTable($setTable);
        $this->setGetListValue($setListValue);
        $this->setGetTextList($setTextList);
        $this->setGetValueSelected($setValueSelected);
        $this->setListCondicao($setCondition);


        $select = "";
        $this->_addOption;
        if($this->_getTable =="" OR $this->_getTable==NULL)
        {
            $select .= "<option value='' > ERRO: A Entidade do banco não foi informada! </option>";
        }
        elseif($this->_getListValue =="" OR $this->_getListValue == NULL)
        {
            $select .= "<option value='' > ERRO: informe o nome do campo value </option>";
        }
        elseif($this->_getTextList=="" OR $this->_getTextList==NULL)
        {
            $select .= "<option value='' > ERRO: informe o nome do campo text </option>";
        }
        else
        {
            $seld="";

            $s = $this->queryDataSelect($this->_getTable,NULL,$this->_ListCondicao,null,$orderBy);
            while($d = $this->queryDataRow($s, phpDataReader::ASSOC))
            {
                if($this->_getValueSelected <> NULL)
                {
                    $seld = $this->__selDataList($d[$this->_getListValue],$this->_getValueSelected);
                }
                $select .= "<option value='{$d[$this->_getListValue]}' ".$seld." >";
                $select .= $d[$this->_getTextList];
                $select .= "</option>";
            }

            print $select;
        }
    }
} 