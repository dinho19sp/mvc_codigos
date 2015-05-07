<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Autoloader
 *
 * @author dinho19sp
 */

function autoLoad($className){
    
    #   Directories added here must be 
    #   relative to the script going to use this file. 
    #   New entries can be added to this list

    $directories = array(
        PATH_CORE,
        PATH_MODEL,
        PATH_CONTROLLER,
        PATH_HELPERS,
        PATH_APIS,
        PATH_APIS."Facebook/",
        PATH_APIS."MoIP/",
        PATH_DATABASE);
   
    #   Add your file naming formats here
    
    $fileNameFormats = array(
      '%s.php',
      '%s.class.php',
      'class.%s.php',
      '%s.inc.php',
      '%s.barcode.php',
      '%s.helper.php'
    );

    #   this is to take care of the PEAR style of naming classes
   
    $path = str_ireplace('_', '/', $className);

    if(@include_once $path.'.php'){
        return;
    }
    
    foreach($directories as $directory){
        
        foreach($fileNameFormats as $fileNameFormat){
            
            $path = $directory.sprintf($fileNameFormat, $className);

            if(file_exists($path)){

                include_once $path;
                
                return;
            }
        }
    }
}

spl_autoload_register('autoLoad');