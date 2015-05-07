<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 29/08/14
 * Time: 16:19
 *
 * Classe da Fonte: https://github.com/flaviomicheletti/code-books/tree/master/pablo_php_oop/cap3/app.ado
 * Adaptada por Francisco Nascimento
 * em 29/08/2014 16:23;
 */

/*
 * classe TExpression
 * classe abstrata para gerenciar expressões
 */
abstract class phpExpression
{
    // operadores lógicos
    const AND_OPERATOR = 'AND ';
    const OR_OPERATOR = 'OR ';

    // marca método dump como obrigatório
    abstract public function dump();
}