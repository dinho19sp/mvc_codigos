<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 29/08/14
 * Time: 16:17
 *
 * Classe da Fonte: https://github.com/flaviomicheletti/code-books/tree/master/pablo_php_oop/cap3/app.ado
 * Adaptada por Francisco Nascimento
 * em 29/08/2014 16:23;
 *
 *      # aqui vemos um exemplo de critério utilizando o operador lógico OR
 *     # a idade deve ser menor que 16 OU maior que 60
 *
 *      $criteria = new TCriteria;
 *      $criteria->add(new TFilter('idade', '<', 16), TExpression::OR_OPERATOR);
 *      $criteria->add(new TFilter('idade', '>', 60), TExpression::OR_OPERATOR);
 *      echo $criteria->dump();
 *      echo " <br>\n";
 *
 *       # aqui vemos um exemplo de critério utilizando o operador lógico AND
 *       # juntamente com os operadores de conjunto IN (dentro do conjunto) e NOT IN (fora do conjunto)
 *       # a idade deve estar dentro do conjunto (24,25,26) e deve estar fora do conjunto (10)
 *
 *       $criteria = new TCriteria;
 *       $criteria->add(new TFilter('idade','IN',array(24,25,26)));
 *       $criteria->add(new TFilter('idade','NOT IN', array(10)));
 *       echo $criteria->dump();
 *       echo " <br>\n";
 */
/*
 * classe phpCriteria
 * Esta classe provê uma interface utilizada para definição de critérios
 */
class phpCriteria extends phpExpression
{
    private $expressions; // armazena a lista de expressões
    private $operators;     // armazena a lista de operadores
    private $properties;    // propriedades do critério

    /*
     * Método Construtor
     */
    function __construct()
    {
        $this->expressions = array();
        $this->operators = array();
    }

    /*
     * método add()
     * adiciona uma expressão ao critério
     * @param $expression = expressão (objeto phpExpression)
     * @param $operator     = operador lógico de comparação
     */
    public function add(phpExpression $expression, $operator = self::AND_OPERATOR)
    {
        // na primeira vez, não precisamos de operador lógico para concatenar
        if (empty($this->expressions))
        {
            $operator = NULL;
        }

        // agrega o resultado da expressão à lista de expressões
        $this->expressions[] = $expression;
        $this->operators[]    = $operator;
    }

    /*
     * método dump()
     * retorna a expressão final
     */
    public function dump()
    {
        // concatena a lista de expressões
        if (is_array($this->expressions))
        {
            if (count($this->expressions) > 0)
            {
                $result = '';
                foreach ($this->expressions as $i=> $expression)
                {
                    $operator = $this->operators[$i];
                    // concatena o operador com a respectiva expressão
                    $result .= $operator. $expression->dump() . ' ';
                }

                $result = trim($result);
                return "({$result})";
            }
        }
    }

    /*
     * método setProperty()
     * define o valor de uma propriedade
     * @param $property = propriedade
     * @param $value      = valor
     */
    public function setProperty($property, $value)
    {
        if (isset($value))
        {
            $this->properties[$property] = $value;
        }
        else
        {
            $this->properties[$property] = NULL;
        }
    }


    public function groubBy($byGorup)
    {
        if(is_array($byGorup))
        {
            foreach($byGorup as $key => $group)
            {
                $groups[] = $group;
            }

            $arr = " GROUP BY ".implode(",",$groups);
        }
        else
        {
            $arr = " GROUP BY ".$byGorup;
        }

        return $arr;
    }


    /**
     * @param null $order Recebe uma string com o nome do campoa a ser ordenado ou um array com os campos a serem ordenados
     * @param string $type espera o tipo de ordenação por padrão [ASC] ou pode ser [DESC]
     */

    public function orderBy($order,$type='ASC')
    {
        if(isset($order))
        {
            if(is_array($order))
            {
                foreach($order as $key => $value)
                {
                    $ord[] = $value;
                }

                $this->_order_by = implode(",",$ord) . " " . $type;
            }
            else
            {
                $this->_order_by = $order . " ". $type;
            }
        }

        return $this->_order_by;
    }

    /*
         * método getProperty()
         * retorna o valor de uma propriedade
         * @param $property = propriedade
         */
    public function getProperty($property)
    {
        if (isset($this->properties[$property]))
        {
            return $this->properties[$property];
        }
    }
}