<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 26/08/14
 * Time: 16:44
 */

class phpDataControl {

    public $_order_by   = NULL;
    public $_limit_by    = NULL;
    public $_where       = NULL;
    public $_StringQuery = NULL;
    public $_Fields_by_query = NULL;

    # Gera condicoes para filtors na sql

    public function __Filter($campo,$value=NULL,$alias=NULL,$operador,$operando = "")
    {

        if(isset($campo))
        {
            if($value != NULL)
            {
                if(is_numeric($value))
                {
                    $valor = $value;
                }
                else
                {

                    $valor = "{$value}";
                }

            }
            else
            {
                if($operador == " LIKE ")
                {
                    $valor = "'%{$this->__post($campo)}%'";
                }
                else
                {
                    $valor = "'{$this->__post($campo)}'";
                }

            }

            $filtro = $operando. " ".(($alias != "")? $alias.".".$campo : $campo). " ". $operador." ".$valor;

            if($this->objSqlPost($campo) != "")
            {
                return $filtro;
            }

        }

    }

    protected function __select($campos)
    {
        if(is_array($campos))
        {
            foreach($campos as $cmp => $field)
            {
                $_F[] = $field;
            }

            $_Fields = implode(",",$_F);

            $sel = " SELECT ".$_Fields. " FROM ";
        }
        else
        {
            $sel = " SELECT ".$campos. " FROM ";
        }


        return $sel;
    }

    protected function __insert($campos=NULL,$tabela)
    {
        $_FieldsCampos = implode(",", array_keys($campos));
        $_FieldsValues = "'".implode("','", array_values($campos))."'";

        $sel = "INSERT INTO `{$tabela}`({$_FieldsCampos}) VALUES ({$_FieldsValues})";

        return $sel;
    }

    protected function __update($_campos=NULL,$tabela)
    {
        foreach ($_campos as $inds => $val)
        {

            if(!is_int($val))
            {
                $v =  "'$val'";
            }
            else
            {
                $v = $val;
            }

            $campos_arr[] = "{$inds} = {$v}";
        }

        $campos = implode(", ",$campos_arr);

        $sel = "UPDATE ".$tabela." SET ". $campos;

        return $sel;
    }

    protected function __delete($tabela,$where=NULL)
    {
        if($tabela == "" || $tabela==NULL )
        {
             user_error('[ A tabela &eacute; obrigatoria para usar metodo queryDataDelete() ] em '.__FUNCTION__."()");
        }
        else
        {
            $sel = " DELETE FROM `{$tabela}`{$where}";
            return $sel;
        }



    }

    public  function __post($value)
    {
        $value = str_ireplace(" or ", "", $value);
        $value = str_ireplace("select ", "", $value);
        $value = str_ireplace("delete ", "", $value);
        $value = str_ireplace("create ", "", $value);
        $value = str_replace("#", "", $value);
        $value = str_replace("=", "", $value);
        $value = str_replace("--", "", $value);
        $value = str_replace(";", "", $value);
        $value = str_replace("*", "", $value);
        $value = trim($value);
        $value = strip_tags($value);
        $value = addslashes($value);

        if(isset($_POST[$value])){

            $value =  $_POST[$value];

        }else{

            $value = "";
        }

        return ($value);
    }

    public function __request($value)
    {
        $value = str_ireplace(" or ", "", $value);
        $value = str_ireplace("select ", "", $value);
        $value = str_ireplace("delete ", "", $value);
        $value = str_ireplace("create ", "", $value);
        $value = str_replace("#", "", $value);
        $value = str_replace("=", "", $value);
        $value = str_replace("--", "", $value);
        $value = str_replace(";", "", $value);
        $value = str_replace("*", "", $value);
        $value = trim($value);
        $value = strip_tags($value);
        $value = addslashes($value);

        if(isset($_REQUEST[$value])){

            $value =  $_REQUEST[$value];

        }else{

            $value = "";
        }

        return($value);
    }

    public function __postFile($value)
    {
        $value = str_ireplace(" or ", "", $value);
        $value = str_ireplace("select ", "", $value);
        $value = str_ireplace("delete ", "", $value);
        $value = str_ireplace("create ", "", $value);
        $value = str_replace("#", "", $value);
        $value = str_replace("=", "", $value);
        $value = str_replace("--", "", $value);
        $value = str_replace(";", "", $value);
        $value = str_replace("*", "", $value);
        $value = trim($value);
        $value = strip_tags($value);
        $value = addslashes($value);

        if(isset($_FILES[$value])){

            $value =  $_FILES[$value];

        }else{

            $value = "";
        }

        return($value);
    }

    public function setOrderBy($order=NULL)
    {
        if(isset($order))
        {
            if(is_array($order))
            {
                foreach($order as $key => $value)
                {
                    $ord[] = $value;
                }

                $this->_order_by = " ORDER BY ".implode(",",$ord);
            }
            else
            {
                $this->_order_by = " ORDER BY ".$order;
            }
        }

    }

    private function getOrderBy()
    {
        if(!is_null($this->_order_by))
        {
            return $this->_order_by;
        }
    }

    public function setLimit($limite=NULL)
    {
        if(isset($limite))
        {
            if(is_array($limite))
            {
                foreach($limite as $key => $value)
                {
                    if(!is_int($value))
                    {
                        die("Os valores passados para LIMIT na query são invalidos");
                    }
                    else
                    {
                        $limits[] = $value;
                    }


                }

                $this->_limit_by = " LIMIT ".implode(",",$limits);

            }
            else
            {
                $this->_limit_by = " LIMIT ".$limite;
            }
        }

    }

    private function getLimit()
    {
        if(!is_null($this->_limit_by))
        {
            return $this->_limit_by;
        }
    }

    public function setWhere($condition = NULL)
    {
        if(isset($condition))
        {
            $this->_where = " WHERE ".$condition;
        }
        else
        {
            $this->_where = NULL;
        }

    }

    private function getWhere()
    {
        if(!is_null($this->_where))
        {
            return $this->_where;
        }
    }

    public function setFieldQuery($campos=NULL)
    {
        if(isset($campos))
        {
            if(is_array($campos))
            {
                foreach($campos as $key => $value)
                {
                        $camp[] = $value;
                }

                $this->_Fields_by_query = implode(",",$camp);

            }
            else
            {
                $this->_Fields_by_query = $campos;
            }
        }
        else
        {
            if(is_null($campos) or $campos == "")
            {
                $this->_Fields_by_query = "*";
            }
        }

        return $this->_Fields_by_query;

    }

    private function getFieldQuery()
    {
        if(!is_null($this->_Fields_by_query))
        {
            return $this->_Fields_by_query;
        }
    }

    public function debug($resultQuery,$type="D")
    {
        if(!empty($resultQuery))
        {
            switch($type)
            {
                case "D":
                    print "<pre>";
                    var_dump($resultQuery);
                    break;
                case "P":
                    print "<pre>";
                    print_r($resultQuery);
                    break;
            }

        }
    }



} 