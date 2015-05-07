<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of View
 *
 * @author dinho19sp
 */
class View  extends Atributos{
    #Armazena o Conteudo html da view
    private $_Contents;
    
    #armazena o arquivo html,tpl,phtml
    
    private $_view;
    #Armazena os parametros a serem mostrados na view
    
    private $_parameters;
    
    #PASSA MESSAGE
   

    public function __construct($_view = NULL, $_parameters = NULL) {
        
        if($_view != NULL){
            if(file_exists($_view))
            {
                $this->setView($_view);
            }
            else
            {
                $_view = PATH_INCLUDE."404.php";
                $this->setView($_view);
            }


            $this->_parameters = $_parameters;


        }
    }
        /**
       * Define qual arquivo html deve ser renderizado
       * @param string $st_view
       * @throws Exception
       */
        public function setView($view){
            
            if(file_exists($view)){
                
                $this->_view = $view;
                
            }else{
                
                die("View file {$view} not found");
            }
        }
        
        /**
        * Retorna o nome do arquivo que deve ser renderizado
        * @return string 
        */
        
        public function getView(){
            
            return $this->_view;
        }
        
        /**
        * Define os dados que devem ser repassados é view
        * @param Array $v_params
        */
        public function setParameters(array $_parameters){

            $this->_parameters = $_parameters;

        }
        
        /**
        * Retorna os dados que foram ser repassados ao arquivo de visualização
        * @return Array
        */
        
        public function getParameters(){


            return $this->_parameters;
        }
        
        /**
        * Retorna uma string contendo todo 
        * o conteudo do arquivo de visualização
        * 
        * @return string
        */
        
        public function getContents(){
            
           ob_start();
           
                    
           if(isset($this->_view)){
               
                require_once($this->_view);
                
               
           }
           
           $this->_Contents = ob_get_contents();
           
           ob_end_clean();
           
           return $this->_Contents;
        }
        
         /**
         * Imprime o arquivo de visualização
         */
        
         public function showContents()
                 
         {
             echo $this->getContents();
             
             
         }
         
         //converte a linha atual do conjunto de resultados como um objeto
          public function _fetch_object($dados){
              
            if(is_array($dados)){

                foreach($dados as $ind=>$val){

                    if(!is_numeric($ind)){

                            $this->$ind = $val;
                        }
                    }
                }
          }
          
    
        public function view_EementsForm(){

           $dados = getInstance('_config');
           return  $dados;
           
           
        }
        
        public function view_EementsHelper(){

           $this->_helper = new Functions();
          return $this->_helper;
        
           
           
        }
    
}