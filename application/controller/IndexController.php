<?php
/**
 * Created by PhpStorm.
 * User: Francisco
 * Date: 02/09/14
 * Time: 21:55
 */

class DashboardController {

    public $_view;

    public function index()
    {
        $this->_view = new View(PATH_VIEW."dashboard.phtml");
        $this->_view->showContents();
    }

} 