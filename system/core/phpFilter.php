<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 29/08/14
 * Time: 16:20
 *
 * Classe da Fonte: https://github.com/flaviomicheletti/code-books/tree/master/pablo_php_oop/cap3/app.ado
 * Adaptada por Francisco Nascimento
 * em 29/08/2014 16:23;
 *
 *      Usabilidade:
 *
 *      #// cria um filtro por data (string)
 *
 *      $filter1=new phpFilter('data', '=', '2007-06-02');
 *      echo $filter1->dump();
 *
 *      # cria um filtro por sexo (array)
 *
 *       $filter3=new TFilter('sexo','IN', array('M', 'F'));
 *       echo $filter3->dump();
 */

/*
 * classe TFilter
 * Esta classe provê uma interface para definição de filtros de seleção
 */
class phpFilter extends phpExpression
{
    private $variable; // variável
    private $operator; // operador
    private $value;    // valor

    /*
     * método __construct()
     * instancia um novo filtro
     * @param $variable = variável
     * @param $operator = operador (>,<)
     * @param $value      = valor a ser comparado
     */
    public function __construct($variable,$operator,$value)
    {
        // armazena as propriedades
        $this->variable = $variable;
        $this->operator = $operator;

        // transforma o valor de acordo com certas regras
        // antes de atribuir à propriedade $this->value
        $this->value     = $this->transform($value);
    }

    /*
     * método transform()
     * recebe um valor e faz as modificações necessárias
     * para ele ser interpretado pelo banco de dados
     * podendo ser um integer/string/boolean ou array.
     * @param $value = valor a ser transformado
     */
    private function transform($value)
    {
        // caso seja um array
        if (is_array($value))
        {
            // percorre os valores
            foreach ($value as $x)
            {
                // se for um inteiro
                if (is_integer($x))
                {
                    $foo[]= $x;
                }
                else if (is_string($x))
                {
                    // se for string, adiciona aspas
                    $foo[]= "'$x'";
                }
            }
            // converte o array em string separada por ","
            $result = '(' . implode(',', $foo) . ')';
        }
        // caso seja uma string
        else if (is_string($value))
        {
            // adiciona aspas
            $result = "'$value'";
        }
        // caso seja valor nullo
        else if (is_null($value))
        {
            // armazena NULL
            $result = 'NULL';
        }

        // caso seja booleano
        else if (is_bool($value))
        {
            // armazena TRUE ou FALSE
            $result = $value ? 'TRUE' : 'FALSE';
        }
        else
        {
            $result = $value;
        }
        // retorna o valor
        return $result;
    }


    /*
     * método dump()
     * retorna o filtro em forma de expressão
     */
    public function dump()
    {
        // concatena a expressão
        return "{$this->variable} {$this->operator} {$this->value}";

    }
}