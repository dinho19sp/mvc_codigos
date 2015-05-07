<?php
/**
 * Created by PhpStorm.
 * User: Kelly
 * Date: 18/12/14
 * Time: 17:49
 */

class xjxHelperPagiacao extends phpDataReader
{

    /*
     * @var $_qtd_por_pagina
     *
     * Recebe a quantidade de registros por pagina
     *
    */

    public $_qtd_por_pagina;


    /*
     * @var $_qtd_inicio
     *
     * Recebe o valor inicial dos registros
     *
    */

    public $_qtd_inicio;


    /*
     * @var $_total_registros
     *
     * Recebe o Total de registros encontardo na tabela
     *
    */

    public $_total_registros;



    /*
     * @var $_set_pagina_atual
     *
     * Seta o valor da pagina atual para geraçao correta dos links
     * Esse valor deve ser colocado em um input hidden da tabela
     * gerada com os registros
     *
     */

    public $_set_pagina_atual;



    /**
    *
    * @param mixed $set_pagina_atual
    */


    public function setSetPaginaAtual($set_pagina_atual)
    {
        $this->_set_pagina_atual = $set_pagina_atual;
    }


    /**
     * @return mixed
     */

    public function getSetPaginaAtual()
    {
        return $this->_set_pagina_atual;
    }


    /**
     *
     * @param $CurrentPage  Recebe o valor da pagina atual
     * @param $PageOfTotal  Recebe o valor da quantidade de registros por pagina;
     *
     */

    public function setIncio($CurrentPage,$PageOfTotal)
    {

        $this->_qtd_inicio = ($CurrentPage * $PageOfTotal) - $PageOfTotal;

    }


    /**
     *
     * @return O valor inicial;
     *
     */

    public function getInicio()
    {
        return $this->_qtd_inicio;
    }

    /**
     * @param $PageOfTotal Recebe o valor de registros por pagina
     *
     */

    public function setTotalPorPagina($PageOfTotal)
    {
        $this->_qtd_por_pagina = $PageOfTotal;
    }

    /**
     * @return valor de registros por pagina
     *
     */

    public function getTotalPorPagina()
    {
       return  $this->_qtd_por_pagina;
    }





    public function setTotalRegistros($tabela,$condicao=NULL,$campos=NULL)
    {


         $sql = $this->queryDataSelect($tabela,$campos,$condicao,null,null);
         $this->_total_registros = $sql->RowCount();

         return $this->_total_registros;

    }


    public function getTotalRegistros()
    {
        return $this->_total_registros;
    }

    /* Recebe um integer : retorna o valor inteiro da pagina selecionada na paginacao */
    public function setPage($intPage)
    {
        if($intPage == 0)
        {
            $page = 1;

        }
        else
        {
            $page = $intPage;

        }

        return $page;
    }


} 