<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DataFilter
 *
 * @author dinho19sp
 */

class DataFilter {
    
    /**
    * Retira pontuacao da string 
    * @param string $st_data
    * @return string
    */
   public function alphaNum($_data){
       
       $_data = preg_replace("([[:punct:]] | )","", $_data);
       return $_data;
       
   }
   
   /**
    * Retira caracteres nao numericos da string
    * @param string $st_data
    * @return string
    */
   public function Numeric($_Num){
       
       $_Num = preg_replace("([[:punct:]] | [[:alpha:]] |)","", $_Num);
       return $_Num;
   }
   
   /**
    * 
    * Retira tags HTML / XML e adiciona "\" antes
    * de aspas simples e aspas duplas
    * @param unknown_type $st_string
    */
    static function cleanString( $_String )
    {
        return addslashes(strip_tags($_String));
    }

    // assume $str esteja em UTF-8
    // funciona corretamente
         
       public static function UnicodeRemoverAcentos($str) {

            $unicode  = array('&Aacute;' => 'A',
                              '&aacute;'  => 'a',
                              '&oacute;'  => 'o',
                              '&Oacute;'  => 'O',
                              '&iacute;'  => 'i',
                              '&Iacute;'  => 'I',
                              '&uacute;'  => 'u',
                              '&Uacute;'  => 'U'
                              );


          return str_replace(array_keys($unicode),array_values($unicode),$str);

        }
    
       public static function removeAcentos($string, $slug = false) {

        $string = utf8_decode($string);
        // Código ASCII das vogais

        $ascii['a'] = range(224, 230);
        $ascii['A'] = range(192, 197);
        $ascii['e'] = range(232, 235);
        $ascii['E'] = range(200, 203);
        $ascii['i'] = range(236, 239);
        $ascii['I'] = range(204, 207);
        $ascii['o'] = array_merge(range(242, 246), array(240, 248));
        $ascii['O'] = range(210, 214);
        $ascii['u'] = range(249, 252);
        $ascii['U'] = range(217, 220);
        $ascii['c'] = array(231);
        $ascii['C'] = array(199);

        // Código ASCII dos outros caracteres

        $ascii['b'] = array(223);
        $ascii['c'] = array(231);
        $ascii['d'] = array(208);
        $ascii['n'] = array(241);
        $ascii['y'] = array(253, 255);

        foreach ($ascii as $key => $item) 
        {

            $acentos = '';

            foreach ($item AS $codigo)
                $acentos .= chr($codigo);

            $troca[$key] = '/[' . $acentos . ']/i';
        }

        $string = preg_replace(array_values($troca), array_keys($troca), $string);
        // Slug?

        if ($slug)
        {
            // Troca tudo que não for letra ou número por um caractere ($slug)

            $string = preg_replace('/[^a-z0-9]/i', $slug, $string);

            // Tira os caracteres ($slug) repetidos

            $string = preg_replace('/' . $slug . '{2,}/i', $slug, $string);

            $string = trim($string, $slug);
        }



        return $string;
    }

}