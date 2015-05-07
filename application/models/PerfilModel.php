<?php
/**
 * Created by PhpStorm.
 * User: Isaias-
 * Date: 18/09/14
 * Time: 17:37
 */
error_reporting(E_ALL);

class PerfilModel extends phpDataReader{

    public function getPerfilUsuarios()
    {
        $sql=$this->queryDataSelect('tb_perfil',NULL);
        $da = $this->Read($sql);
        return $da;

    }

    public function getCategoriasPerfilArquivos()
    {
        $sql = $this->queryDataSelect('tb_categorias');
        $qry = $this->Read($sql);
        return $qry;
    }

    public function addCategoriasArquivos($idPerfil)
    {
        $idCategoria = $this->__post('id_categoria');
        $_Filter = new phpCriteria();

        if(is_array($idCategoria))
        {
            foreach($idCategoria as $key => $value)
            {
                $_Filter->add(new phpFilter('id_categoria','=',$value));
                $_Filter->add(new phpFilter('id_perfil','=',$idPerfil));
                $sql = $this->queryDataInsert('tb_perfil_categoria',null,$_Filter->dump());
            }

        }
        else
        {
            $_Filter->add(new phpFilter('id_categoria','=',$idCategoria));
            $_Filter->add(new phpFilter('id_perfil','=',$idPerfil));
            $sql = $this->queryDataInsert('tb_perfil_categoria',null,$_Filter->dump());
        }

    }

    public function delCategoriaArquivos($idPerfil)
    {

        $idCategoria = $this->__post('id_categoria');
        $_Filter = new phpCriteria();

        if(is_array($idCategoria))
        {
            foreach($idCategoria as $key => $value)
            {
                $_Filter->add(new phpFilter('id_categoria','=',$value));
                $_Filter->add(new phpFilter('id_perfil','=',$idPerfil));
                $sql = $this->queryDataDelete('tb_perfil_categoria',null,$_Filter->dump());
            }

        }
        else
        {
            $_Filter->add(new phpFilter('id_categoria','=',$idCategoria));
            $_Filter->add(new phpFilter('id_perfil','=',$idPerfil));
            $sql = $this->queryDataDelete('tb_perfil_categoria',null,$_Filter->dump());
        }

    }


} 