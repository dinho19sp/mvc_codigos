<?php
/**
 * Created by PhpStorm.
 * User: Isaias-
 * Date: 16/07/14
 * Time: 14:18
 */

class MoipTransaction {

    private $_key;
    private $_token;

    public function registerKey() {

        $this->_key = new Helpers();

        if(is_object($this->_key)) {
            $chave = get_object_vars($this->_key->xmlData("app.host.config"));
        }

        return (string) $chave['moip']->moip_key;

    }

    public function registerToken() {

        $this->_token = new Helpers();

        if(is_object($this->_token)) {
            $tokenreg = get_object_vars($this->_key->xmlData("app.host.config"));
        }

        return (string) $tokenreg['moip']->moip_token;

    }

    public function enviaPedidoInfo($usuario, $num_pedido, $_FORM, $exibe = FALSE) {

        $moip = new Moip();

        $host = new ElementosHtml();

        $moip->setNotificationURL($host->getHost().'moip_transacao.php');

        $moip->setCredential(array(
            'key' => $this->registerKey(),
            'token' => $this->registerToken()
        ));

        if($usuario != '') {

            if(count($_FORM) > 0) {

            $estado = Functions::getUF($_FORM['id_estado']);

            //envia os dados do pedido para o Moip
            $moip->setEnvironment('teste');
            $moip->setUniqueID($num_pedido);
            $moip->setValue($_FORM['total_pedido_parcela']);
            $moip->setReason("Valor do Consorcio");
            $moip->setPayer(array(
                                'name' => $_FORM['nome_usuario'],
                                'email' => $_FORM['email_usuario'],
                                'payerId' => $usuario,
                                'identity' => $_FORM['cpf'],
                                'phone' => $_FORM['celular'],
                                'billingAddress' => array(
                                    'address' => $_FORM['logradouro_nome'],
                                    'number' => $_FORM['numero'],
                                    'complement' => $_FORM['complemento'],
                                    'city' => $_FORM['cidade'],
                                    'neighborhood' => $_FORM['bairro'],
                                    'state' => $estado->estado_sigla,
                                    'country' => 'BRA',
                                    'zipCode' => $_FORM['cep'],
                                    'phone' => $_FORM['telefone'])));

            //forma de pagamento
            $moip->addPaymentWay('creditCard');
            $moip->validate('Identification');

            }

            $moip->send();

            $resposta = $moip->getAnswer();

            if($exibe === TRUE) {
                var_dump($resposta);
            }

            return $resposta;

        }

    }

    public function atualizaStatus(array $dados, $num_pedido) {

        //Captura o id do pedido
        $sql_pedido = objSql::objSqlSelectDataRow("tb_pedidos", "id_pedido, numero_pedido", "numero_pedido = '".$num_pedido."'");
        $qry_pedido = objSql::objSqlDataList($sql_pedido, objSql::OBJ);

        if($qry_pedido->id_pedido) {
        $update = objSql::objSqlUpdateDataRow("tb_pedidos_pagto", $dados, "id_pedido = ".$qry_pedido->id_pedido);

        }

    }

}