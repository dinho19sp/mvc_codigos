<?php
class Uploads extends phpDataReader
{
    private static $file_name;
	static private $nome_img;
	static private $nome_image;
	static private $tmp_name;
	static function foto($nome,$tmp)
	{
		self::$nome_image = $nome;
		self::$tmp_name = $tmp;
	}

    static function retiraAcento($fileName) {

        //Limpando o nome da imagem(retira acentos e espaÃ§os)=====
        $fileName = str_replace(' ','_',$fileName);
        $fileName = preg_replace("[ÁÀÂÃ]","A",$fileName);
        $fileName = preg_replace("[áàâãª]","a",$fileName);
        $fileName = preg_replace("[ÉÈÊ]","E",$fileName);
        $fileName = preg_replace("[éèê]","e",$fileName);
        $fileName = preg_replace("[ÓÒÔÕ]","O",$fileName);
        $fileName = preg_replace("[óòôõº]","o",$fileName);
        $fileName = preg_replace("[ÚÙÛ]","U",$fileName);
        $fileName = preg_replace("[úùû]","u",$fileName);
        $fileName = str_replace("Ç","C",$fileName);
        $fileName = str_replace("ç","c",$fileName);

        return $fileName;

    }

	static function upload_imagem($dir="")
	{
		 $nome_imagem = self::$nome_image;
		  $tmp_nome = self::$tmp_name;

          $nome_imagem = self::retiraAcento($nome_imagem);
			//==========================================================
		if($nome_imagem<>"")
		{
			$array_nome = explode('.',$nome_imagem);
			$nome_imagem = $array_nome[0];
			$ext_imagem = '.'.$array_nome[1];
			$caminho=$dir.'/'.$nome_imagem.$ext_imagem;
                        
			$nf =1;
			$nI = $nome_imagem;
			while(file_exists($caminho))
			{
				$nome_imagem = $nI.$nf;
				$caminho = $dir.'/'.$nome_imagem.$ext_imagem;
				$nf++;
			}
			if(move_uploaded_file($tmp_nome,$caminho))
			{
				self::$nome_img = $nome_imagem.$ext_imagem;
				return true;
			}
			else
			{
				self::$nome_img = '';
				return false;
			}
		}
		else
		{
			return false;
		}
	}
        
     public function img_resize( $tmpname, $size, $save_dir, $save_name, $maxisheight = 0 )
     {
         $save_dir     .= ( substr($save_dir,-1) != "/") ? "/" : "";
         $gis        = getimagesize($tmpname);
         $type        = $gis[2];
         switch($type)
         {
             case "1": $imorig = imagecreatefromgif($tmpname); break;
             case "2": $imorig = imagecreatefromjpeg($tmpname);break;
             case "3": $imorig = imagecreatefrompng($tmpname); break;
             default:  $imorig = imagecreatefromjpeg($tmpname);
         }

         $x = imagesx($imorig);
         $y = imagesy($imorig);

         $woh = (!$maxisheight)? $gis[0] : $gis[1] ;

         if($woh <= $size)
         {
             $aw = $x;
             $ah = $y;
         }
         else
         {
             if(!$maxisheight){
                 $aw = $size;
                 $ah = ($size) * $y / $x ;

             } else {
                 $aw = $size * $x / $y;
                 $ah = $size;
             }
         }
         $im = imagecreatetruecolor($aw,$ah);
         if (imagecopyresampled($im,$imorig , 0,0,0,0,$aw,$ah,$x,$y))
             if (imagejpeg($im, $save_dir.$save_name))
                 return true;
             else
                 return false;
     }
	/*
  FunÃ§Ã£o uploadJPEG
  Entrada: $file: Variavel de formulario
                  (Ex: $_FILES[nome_do_campo_file])
           $path: Path para salvar a miniatura.
                  (Ex: "imagens/foto.jpg")
                  Obs: De CHMOD 777 na pasta que receberÃ¡ os arquivos
           $maxdim: DimensÃ£o Maxima em pixels, relativa Ã  altura
                     ou largura, o que for maior
                     200 por default
           $maxsize: Tamanho mÃ¡ximo do arquivo original em bytes
                     3072000 por default
  Retorno: formato INT:
           Valores:
             1: se upload bem sucedido.
             2: se o arquivo nÃ£o foi recebido
             3: se o arquivo nao for do tipo JPEG
             4: se o tamanho do arquivo Ã© maior que o tamanho permitido
             5: se ocorrer algum erro durante o redimensionamento
*/
	static function uploadJPEG($tmp_name,$img_tipo,$path,$maxdim=200,$maxsize=3072000){
	  if(is_uploaded_file($tmp_name)){
		$mime = $img_tipo;

		  if($file[size] < $maxsize){
			list($larg_orig, $alt_orig) = getimagesize($tmp_name);
			$razao_orig = $larg_orig/$alt_orig;
			if($razao_orig < 1){
			  $larg = $maxdim*$razao_orig;
			  $alt = $maxdim;
			}
			else{
			  $alt = $maxdim;
			  $larg = $maxdim*$razao_orig;
			}
			$imagem_nova = imagecreatetruecolor($larg, $alt);
			$imagem = imagecreatefromjpeg($tmp_name);
			imagecopyresampled($imagem_nova, $imagem, 0, 0, 0, 0, $larg, $alt, $larg_orig, $alt_orig);
			return (imagejpeg($imagem_nova, $path)) ? 1 : 5;
		  }

		return 3;
	  }
	  return 2;
	}
	static function up_imagem($tmp_name,$img_tipo,$tam,$nome,$dir="")
	{
		  $nome_imagem = $nome;//$img['name'];
		  $tmp_nome = $tmp_name;
			//Limpando o nome da imagem(retira acentos e espaÃ§os)=====

			//==========================================================
		if($nome_imagem<>"")
		{
			$array_nome = explode('.',$nome_imagem);
			$nome_imagem = $array_nome[0];
			//$ext_imagem = $array_nome[1];
			 $ext_imagem = '.jpg';
			$caminho=$dir.'/'.$nome_imagem.$ext_imagem;
			$nf =1;
			while(file_exists($caminho))
			{
				$nome_imagem = $nome_imagem.$nf.$ext_imagem;
				$caminho = $dir.'/'.$nome_imagem;
				$nf++;
			}
			//if(move_uploaded_file($tmp_nome,$caminho))
			if(self::uploadJPEG($tmp_nome,$img_tipo,$caminho,$tam)==1)
			{
				self::$nome_img = $nome_imagem;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	static function alterar_imagem($nome,$img_antiga="",$dir="")//nome=nome do campo imagem, img_antiga= caminho da imagem antiga
	{
		 $nome_imagem = $_FILES[$nome]['name'];
		 $tmp_nome = $_FILES[$nome]['tmp_name'];
			//Limpando o nome da imagem(retira acentos e espaÃ§os)=====
        $nome_imagem = str_replace(' ','_',$nome_imagem);
        $nome_imagem = ereg_replace("[ÁÀÂÃ]","A",$nome_imagem);
        $nome_imagem = ereg_replace("[áàâãª]","a",$nome_imagem);
        $nome_imagem = ereg_replace("[ÉÈÊ]","E",$nome_imagem);
        $nome_imagem = ereg_replace("[éèê]","e",$nome_imagem);
        $nome_imagem = ereg_replace("[ÓÒÔÕ]","O",$nome_imagem);
        $nome_imagem = ereg_replace("[óòôõº]","o",$nome_imagem);
        $nome_imagem = ereg_replace("[ÚÙÛ]","U",$nome_imagem);
        $nome_imagem = ereg_replace("[úùû]","u",$nome_imagem);
        $nome_imagem = str_replace("Ç","C",$nome_imagem);
        $nome_imagem = str_replace("ç","c",$nome_imagem);
			//=========================================================
		if($nome_imagem<>"")
		{
			if($img_antiga<>"")
			{
				unlink($dir.'/'.$img_antiga);//deleta a imagem antiga
			}
			$array_nome = explode('.',$nome_imagem);
			$nome_imagem = $array_nome[0];
			$ext_imagem = '.'.$array_nome[1];
			$caminho=$dir.'/'.$nome_imagem.$ext_imagem;
			$nf =1;
			while(file_exists($caminho))
			{
				$nome_imagem = $nome_imagem.$nf;
				$caminho = $dir.'/'.$nome_imagem.$ext_imagem;
				$nf++;
			}
			$nome_imagem = $nome_imagem.$ext_imagem;
			 if(move_uploaded_file($tmp_nome,$caminho))
			{
				 self::$nome_img = $nome_imagem;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	static function nome_imag()
	{
		echo $imag = self::nome_img;
		return $imag;
	}
}