<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of DataValidator
*
* @author dinho19sp
*/


class DataValidator {

    /**
    * Verifica se o dado passado esta vazio
    * @param mixed $_MaxValue
    * @return boolean
    */

    public static function vPass($pass)
    {
          $len = strlen($pass);
          $count = 0;
          $array = array("[[:lower:]]+", "[[:upper:]]+", "[[:digit:]]+", "[!#_-]+");

          foreach ($array as $a)
          {
              if (ereg($a, $pass))
              {
                  $count++;
              }
          }

          if ($len > 6)
          {
              $count++;
          }
          return $count;
      }

    public static function CheckThisPassword($password){

      $_check = self::vPass($password);

      switch ($_check)
      {
          case 1:

              $ThisPass = "Senha informada &eacute; de nivel muito fraca.. tente melhora - la";

              break;
          case 2:

              $ThisPass = "Senha informada ainda &eacute; de nivel fraca.. tente ser um pouco mais criativo!";

              break;
          case 3:

               $ThisPass = "Senha informada &eacute; de nivel m&eacute;dia.. mas pode ser melhorada!";

              break;
          case 4:

              $ThisPass = "Senha &eacute; de nivel forte.. para n&atilde;o esquece-la anote-a e a guarde em local seguro!";

              break;
          case 5:

              $ThisPass = "Senha &eacute; de nivel fortissima.. para n&atilde;o esquece-la anote-a e a guarde em local seguro!";

              break;


      }

     return $ThisPass;
    }

    public static function compare($_pass_1,$_pass_2)
    {
      if($_pass_1 != "" && $_pass_2 != "")
      {
        if($_pass_2 != $_pass_1)
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
      }

    }

    public static function isEmpty($_MaxValue){

        if(!strlen($_MaxValue)>0){

           return TRUE;

        }else{

           return FALSE;
        }
    }

    /**
    * Verifica se o dado passado e um numero
    * @param mixed $_MaxValue;
    * @return boolean
    */
    public static function isNumeric($_MaxValue){

       $_MaxValue = str_replace(",",".", $_MaxValue);
       if(!(is_numeric($_MaxValue)))
       {

           return FALSE;

       }
       else
       {

           return TRUE;
       }
    }

    /**
    * Verifica se o dado passado e um numero inteiro
    * @param mixed $_MaxValue;
    * @return boolean
    */
    public static function isInteger( $_MaxValue )
    {
        if(!DataValidator::isNumeric($_MaxValue))
        {

            return false;

        }

        if(preg_match('/[[:punct:]&^-]/', $_MaxValue) > 0){

            return false;

        }
        else
        {

            return true;

        }

    }

    /**
     * nome da função isValidCnpj
     *
     * Esta função testa se um Cnpj é valido ou não.
     *
     * @param	string		$cnpj			Guarda o Cnpj como ele foi digitado pelo cliente
     * @param	array		$num			Guarda apenas os números do Cnpj
     * @param	boolean		$isCnpjValid            Guarda o retorno da função
     * @param	int		$multiplica             Auxilia no Calculo dos Dígitos verificadores
     * @param	int		$soma			Auxilia no Calculo dos Dígitos verificadores
     * @param	int		$resto			Auxilia no Calculo dos Dígitos verificadores
     * @param	int		$dg			Dígito verificador
     * @return	boolean		"true" se o Cnpj é válido ou "false" caso o contrário
     *
     */

    public static function isValidCnpj($cnpj)
    {
        # Etapa 1:
        # Cria um array com apenas os digitos numéricos, isso permite receber o cnpj em
        # diferentes formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00" etc...

        $j = 0;
        for ($i = 0; $i < (strlen($cnpj)); $i++) {

            if (is_numeric($cnpj[$i])) {

                $num[$j] = $cnpj[$i];
                $j++;
            }
        }

        # Etapa 2:
        # Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.

        if (count($num) != 14) {

            $isValidCnpj = false;
        }

        # Etapa 3:
        # O número 00000000000 embora não seja um cnpj real resultaria
        # um cnpj válido após o calculo dos dígitos verificares e por
        # isso precisa ser filtradas nesta etapa.

        if ($num[0] == 0 && $num[1] == 0
            && $num[2] == 0 && $num[3] == 0
            && $num[4] == 0 && $num[5] == 0
            && $num[6] == 0 && $num[7] == 0
            && $num[8] == 0 && $num[9] == 0
            && $num[10] == 0 && $num[11] == 0
        ) {

            $isValidCnpj = false;
        }

        # Etapa 4:
        # Calcula e compara o primeiro dígito verificador.

        else {
            $j = 5;
            for ($i = 0; $i < 4; $i++) {
                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }

            $soma = array_sum($multiplica);
            $j = 9;

            for ($i = 4; $i < 12; $i++) {

                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }

            $soma = array_sum($multiplica);
            $resto = $soma % 11;

            if ($resto < 2) {

                $dg = 0;

            } else {

                $dg = 11 - $resto;

            }
            if ($dg != $num[12]) {

                $isValidCnpj = false;
            }
        }
        # Etapa 5:
        # Calcula e compara o segundo dígito verificador.

        if (!isset($isValidCnpj)) {
            $j = 6;
            for ($i = 0; $i < 5; $i++) {

                $multiplica[$i] = $num[$i] * $j;
                $j--;

            }

            $soma = array_sum($multiplica);
            $j = 9;

            for ($i = 5; $i < 13; $i++) {

                $multiplica[$i] = $num[$i] * $j;
                $j--;
            }

            $soma = array_sum($multiplica);
            $resto = $soma % 11;

            if ($resto < 2) {

                $dg = 0;

            } else {
                $dg = 11 - $resto;
            }

            if ($dg != $num[13]) {

                $isValidCnpj = false;

            } else {
                $isValidCnpj = true;
            }
        }
        //Trecho usado para depurar erros.
        /*
        if($isValidCnpj==true)
                {
                        echo "<p><font color=\"GREEN\">Cnpj é Válido</font></p>";
                }
        if($isValidCnpj==false)
                {
                        echo "<p><font color=\"RED\">Cnpj Inválido</font></p>";
                }
        */
        //Etapa 6: Retorna o Resultado em um valor booleano.
        return $isValidCnpj;
    }

    public static function isValidCpf($cpf = null)
    {

        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = ereg_replace('[^0-9]', '', $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        else if ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999'
        ) {
            return false;
            // Calcula os digitos verificadores para verificar se o
            // CPF é válido
        } else {

            for ($t = 9; $t < 11; $t++) {

                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }

    public static function isValidEmail($email)
    {

        $conta = "^[a-zA-Z0-9\._-]+@";
        $domino = "[a-zA-Z0-9\ ._-]+(\.)";
        $extensao = "([a-zA-Z]{2,4})$";
        $pattern = $conta . $domino . $extensao;
        if (ereg($pattern, $email))

            return true;

        else

            return false;

    }

    public static function CreateDirectory(Array $inputName=NULL)
    {

        if(is_array($inputName))
        {
            foreach($inputName as $key => $val)
            {
                $directory = explode(" ", $val);
                $directory_name[] = implode("_", $directory);
            }
        }

        $dir = implode("_",$directory_name);

        $dir = preg_replace("/[^a-zA-Z0-9]/", "", $dir);

        if (is_dir(PATH_INCLUDE . 'Files')) {
            if (!is_dir(PATH_INCLUDE . 'Files/' . $dir)) {
                chmod(PATH_INCLUDE . 'Files/', 0777);
                mkdir(PATH_INCLUDE . 'Files/' . $dir);

            }
        }
        $string = PATH_INCLUDE . 'Files/' . $dir;
        return $string;

    }


}