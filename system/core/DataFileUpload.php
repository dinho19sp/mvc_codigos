<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 10/10/14
 * Time: 17:14
 */

class DataFileUpload {

    public  $image_nome;
    public $image_type;
    public $image_size;
    public $image_temp;
    public $image_rename;
    public $image_tabela;
    public $image_path;
    public $image_extensao;
    public $image_count;


    public function __construct($ImageName,$ImageSavePath)
    {
        $upload = new Uploads();

        $this->setImageNome($ImageName);
        $this->setImageCount($ImageName);
        $this->setImageType($ImageName);
        $this->setImageTemp($ImageName);
        $this->setPathImage($ImageSavePath);

    }



    protected function addImage($postImage)
    {
        $upload = new Uploads();

        for($i=0;$i < $this->image_count;$i++)
        {
            $this->setImageExtensao($i);

            switch($this->image_extensao)
            {
                case 'jpeg':
                case 'jpg' :
                    $ext = '.jpg';
                    break;
                case 'gif':
                    $ext = '.gif';
                    break;
                case 'png':
                    $ext = '.png';
                    break;
            }

            # Separa as imagens da Home e detalhes
            # colocando um identificador para distinguir as imagens principais

            $rename_image_explode = explode(" ",$this->objSqlPost('produto_nome'));
            $rename_image_implode = implode("-",$rename_image_explode);
            $this->setImageRename($rename_image_implode."-284".$secundary.$ext);
            $home = $rename_image_implode."-494".$secundary.$ext;

            if(is_file($this->image_temp[$i]))
            {
                $upload->img_resize( $this->image_temp[$i] , 494 , CM_PATH_IMAGE_SITE ."zoom/",strtolower($home) );
                $upload->img_resize( $this->image_temp[$i] , 284 , CM_PATH_IMAGE_SITE , strtolower($this->image_rename));



                $campos_media = array(
                    'media_tipo'        => 'imagem',
                    'media_descricao'   => ucwords($this->objSqlPost('produto_nome')),
                    'media_titulo'      => strtolower($this->objSqlPost('produto_nome')),
                    'media_thumb_small' => $this->image_rename,
                    'media_thumb_large' => $home,
                    'media_ordem'       => $ordem,
                    'status'            => 1

                );




                $campos_media_objeto = array(
                    'tabela_nome' => 'tb_produtos',
                    'id_objeto' => $id_produto,
                    'id_media' => "",
                    'media_side'  => 'F'
                );

                $db = $this->setConnection();
                $sql = "INSERT INTO tb_media(media_tipo,media_descricao,media_titulo,media_thumb_small,media_thumb_large,media_ordem,status) VALUES ('imagem','".strtolower($this->objSqlPost('produto_nome'))."','".strtoupper($this->objSqlPost('produto_nome'))."','".strtolower($this->image_rename)."','".strtolower($home)."',
                 '{$ordem}',
                 '1')";
                $qry = $db->query($sql);
                $id = $db->lastInsertId();

                $campos_media_objeto = array(
                    'tabela_nome' => 'tb_produtos',
                    'id_objeto' => $id_produto,
                    'id_media' => $id,
                    'media_side'  => 'F'
                );

                $this->objSqlInsertDataRow('tb_media_objeto',$campos_media_objeto);
            }



        }


    }

    /**
     * Tratamento de Imagens
     */


    public function setImageNome($nome)
    {
        $this->image_nome = $_FILES[$nome]['name'];
    }

    protected function getImageNome()
    {
        return $this->image_nome;
    }

    public function setImageCount($nome)
    {

        $this->image_count = count($_FILES[$nome]['name']);

    }

    protected function getImageCount()
    {

        return $this->image_count;

    }

    public function setImageExtensao($part)
    {

        $ex = explode(".",$this->image_nome[$part]);
        $this->image_extensao = strtolower($ex[count($ex)-1]);

    }

    protected function getImageExtensao()
    {

        return $this->image_extensao;

    }

    protected function setImageTemp($nome)
    {

        $this->image_temp = $_FILES[$nome]['tmp_name'];

    }

    protected function getImageTemp()
    {

        return  $this->image_temp;

    }

    protected function setImageType($nome)
    {

        $this->image_type = $_FILES[$nome]['type'];

    }

    protected function getImageType()
    {

        return  $this->image_type;

    }

    public function setImageSize($nome)
    {

        $this->image_temp = $_FILES[$nome]['size'];

    }

    protected function getImageSize()
    {

        return  $this->image_size;

    }

    public function setImageRename($nome)
    {

        $this->image_rename = $nome;
    }

    protected function getImageRename()
    {

        return $this->image_rename;

    }

    public function setTabelaImage($tabela)
    {

        $this->image_tabela = $tabela;

    }

    protected function getTabelaImage()
    {
        return $this->image_tabela;
    }

    public function setPathImage($path)
    {

        $this->image_path = $path;

    }


    protected function getPathImage()
    {
        return $this->image_path;
    }


} 