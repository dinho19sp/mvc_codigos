<?php
$environment=new ElementosRoot();
$apps = new onCreateClass();
$urlvirtual=$apps->getElementosRoot()->getHost();
$session = Registry::getInstance('SessionStart');

$menu = new Menu();
$menu->GetSideBarMenuAdmin();
?>

