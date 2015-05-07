<?php
/**
 * Created by PhpStorm.
 * User: francisco
 * Date: 03/09/14
 * Time: 13:34
 */

class Menu {

    /*
        *
        *   @private $_menu armazena o primeiro nivel do menu:
        *
        *      <ul>
        *          <li> Nome menu </li>     <--- 1º Nivel
        *      </ul>
        *
    */
    private $_menu;


    /*
        *
        *   @private $_sub_menu armazena o segundo nivel do menu:
        *
        *      <ul>
        *         <li> Nome menu
        *            <ul>
        *                <li>Nome Sub Menu</li>   <--- 2º Nivel
        *            </ul>
        *         </li>
        *      </ul>
        *
    */

    private $_sub_menu;


     /*
        *
        *   @private $_this_menu armazena toda a estrutura do menu:
        *
        *      $_this_menu = "<li> Nome menu
        *          <ul>
        *              <li>Nome Sub Menu</li>
        *              <li> Nome sub nivel
        *                  <ul>
        *                   <li> link_sub_nivel_1</li>
        *                   <li> link_sub_nivel_2</li>
        *                  </ul>
        *              </li>
        *          </ul>
        *      </li>";
        *
    */

    private $_this_menu;


    /*
       *
       *   @private $_icon_menu armazena o icone do menu 1° nivel:
       *
       *      $_this_menu = "<li> Nome menu
       *          <ul>
       *              <li>Nome Sub Menu</li>
       *              <li> Nome sub nivel
       *                  <ul>
       *                   <li> link_sub_nivel_1</li>
       *                   <li> link_sub_nivel_2</li>
       *                  </ul>
       *              </li>
       *          </ul>
       *      </li>";
       *
   */

    private $_icon_menu;

    /*
      *
      *   @private $_link_root_menu armazena o link a ser definido:
      *
      *      $_this_menu = "<li> Nome menu
      *          <ul>
      *              <li>Nome Sub Menu</li>
      *              <li> Nome sub nivel
      *                  <ul>
      *                   <li> link_sub_nivel_1</li>
      *                   <li> link_sub_nivel_2</li>
      *                  </ul>
      *              </li>
      *          </ul>
      *      </li>";
      *
  */

    private $_link_root_menu;

    private $_class;

    private $_sql;
    private $_query;
    private $_filter;

    private function DataReader()
    {
        $DataReader = new phpDataReader();

        return $DataReader;
    }

    public function setActive()
    {
        $url = $_GET['key'];

        $explode = explode("/",$url);

        $controlle= $explode[0];
        $action = $explode[1];

        if($action != "")
        {
            $thisUrl = $controlle."/".$action."/";
        }
        else
        {
            $thisUrl = $controlle."/";
        }


        $sql = $this->DataReader()->queryDataSelect('tb_menu_sub','link_sub_menu,id_menu,id_sub_menu','link_sub_menu ="'.$thisUrl.'"',null,null);
        $qry = $this->DataReader()->queryDataRow($sql,phpDataReader::OBJ);

        $array = array("SETSUB" => $qry->id_sub_menu,"SETID"=>$qry->id_menu);

        return $array;

    }
    private function DataFilter()
    {
        $DataFilter = new phpCriteria();

        return $DataFilter;
    }

    /**
     *
     *  private setStructMenu();
     *
     */

    /**
     *
     *  private SqlGetMenu();
     *
     */

        private function SqlGetMenu()
        {
                $this->_sql = $this->DataReader()->queryDataSelect('tb_menu');

                return $this->_sql;
        }


        private function SqlGetSubMenu($idMenu)
        {
            /*
             *
             * Adiciona condicao para filtros
             *
             *
             */

            $this->_filter = $this->DataFilter();
            $this->_filter->add(new phpFilter('id_menu',"=",$idMenu));

            $this->_sql = $this->DataReader()->queryDataSelect('tb_menu_sub',NULL,$this->_filter->dump(),NULL,NULL);


        }



    /**
     *
     *  private GetMenuPermissao;
     *
     */

    public function GetMenuPermissao()
    {
        $session = Registry::getInstance('SessionStart');

        $this->_filter = $this->DataFilter();

        $this->_filter->add(new phpFilter('perm.id_usuario',"=",Functions::getUserId()));
        $this->_filter->add(new phpFilter('perm.permitido',"=","S"),phpExpression::AND_OPERATOR);
        $this->_filter->add(new phpFilter('menu.id_status',"=",1),phpExpression::AND_OPERATOR);


        $this->_sql = $this->DataReader()
            ->queryDataSelect('tb_menu_permissao perm
                                        LEFT JOIN tb_menu menu ON perm.id_menu=menu.id_menu',
            NULL,$this->_filter->dump().$this->_filter->groubBy(array('perm.id_menu')),
            NULL,"menu.ordem_menu");

            return $this->_sql;


    }


    public function GetSubMenuPermissao($idMenu)
    {
        $session = Registry::getInstance('SessionStart');

        $this->_filter = $this->DataFilter();

        $this->_filter->add(new phpFilter('pm.id_usuario',"=",Functions::getUserId()));
        $this->_filter->add(new phpFilter('t.tipo_pagina',"=","P"));
        $this->_filter->add(new phpFilter('pm.permitido',"=","S"));
        $this->_filter->add(new phpFilter('t.id_menu',"=",$idMenu));
        $this->_filter->add(new phpFilter('t.id_status',"=",1));

        $this->_sql = $this->DataReader()->queryDataSelect('tb_menu_permissao pm
                LEFT JOIN
                tb_menu_sub t ON pm.id_sub_menu=t.id_sub_menu',
            NULL,$this->_filter->dump(),
            NULL,"t.sub_menu_order");

        return $this->_sql;
    }
    /**
     *
     *  public GetSideBarMenuAdmin;
     *
     */


     public function GetSideBarMenuAdmin()
     {
         $_data_url = new ElementosRoot();
        $_sql = $this->GetMenuPermissao();

         $setClass = $this->setActive();

        $this->_this_menu = '
        <div class="sidebar-menu">
            <header class="logo-env">

                <!-- logo -->
                <div class="logo">
                    <a href="'.$_data_url->getHost().'">
                        <img src="'.$_data_url->getImg().'logo@2x.png" width="90" alt="" />
                    </a>
                </div>

                <!-- logo collapse icon -->
                <div class="sidebar-collapse">
                    <a href="#" class="sidebar-collapse-icon with-animation"><!-- add class "with-animation" if you want sidebar to have animation during expanding/collapsing transition -->
                        <i class="entypo-menu"></i>
                    </a>
                </div>

                <!-- open/close menu icon (do not remove if you want to enable menu on mobile devices) -->
                <div class="sidebar-mobile-menu visible-xs">
                    <a href="#" class="with-animation"><!-- add class "with-animation" to support animation -->
                        <i class="entypo-menu"></i>
                    </a>
                </div>

            </header>
            <ul id="main-menu" class="">
            <!-- add class "multiple-expanded" to allow multiple submenus to open -->
            <!-- class "auto-inherit-active-class" will automatically add "active" class for parent elements who are marked already with class "active" -->
            <!-- Search Bar -->
            ';
            $link = null;


         while($menu = $this->DataReader()->queryDataRow($_sql,phpDataReader::OBJ))
            {

                if($menu->tipo_menu == 1)
                {
                    $link =  'onclick="window.location=\''.$_data_url->getHost().($menu->link_menu).'\'"';

                }else
                {
                    $link = null;
                }




                $this->_this_menu .= '<li class="'.(($setClass['SETID'] == $menu->id_menu)? "active opened active":"").'" >

                                        <a href="javascript:void(0);" '.$link.' >
                                            <i class="'.$menu->icon_menu.'"></i>
                                            <span>'.($menu->titulo_menu).'</span>
                                        </a>';
                if($menu->tipo_menu == 1)
                {
                    $this->_this_menu .='</li>';
                }
                else
                {
                    $this->_this_menu .= '<ul>';

                    $subMenu = $this->GetSubMenuPermissao($menu->id_menu);

                    while ($menuSub = $this->DataReader()->queryDataRow($subMenu, phpDataReader::OBJ))

                        $this->_this_menu .= '<li class="'.(($setClass['SETSUB'] == $menuSub->id_sub_menu)? "active":"").'">
                                                <a href="' . $_data_url->getHost() . ($menuSub->link_sub_menu) . '">
                                                    <span>' . $menuSub->titulo_sub_menu . '</span>
                                                </a>
                                            </li>';

                        $this->_this_menu .= '</ul>';
                }
                $this->_this_menu .='</li>';
            }

         $this->_this_menu .= '</ul>';
         $this->_this_menu .= '</div>';

         echo($this->_this_menu);

     }

    /**
     *
     *  public GetSideBarMenuCustomer;
     *
     */


    public function GetSideBarMenuCustomer()
    {

    }

    /**
     *
     *  GETTERS AND SETTERS
     *
     */






} 