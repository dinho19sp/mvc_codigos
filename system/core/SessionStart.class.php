<?php
/**
*	Class SessionStart
*
*	Autor: Francisco Nascimento
*	Data : 10/02/2013
*	
*	Variaveis:
*
*	_SID:
*		 Guarda o ID da sessao
*
*	_SessionNames:
*		
*		Array que contera as variaveis de sessao registrada
*
*
*	Como usar:
*
*		$sess = new SessionStart();
*		$sess->setNamesInSession('Variavel','ValorDaVariavel');
*
*	Destruindo a sessao:	
*
*		$sessDest = new SessionStart();
*		$sessDest->SessionDestroy(TRUE);
*
*
*/	
	class SessionStart{
		private  $_SID;
		private  $_nvars;
		private  $_route;
        private  $_method;
                
		public static $_msg;
                
		public function __construct($ini=FALSE,$_vars=NULL){
			if($ini == TRUE){
				$this->SessionStart();
				/*if($_var==NULL){
					$this->setVars($_vars);
				}*/
			}
		}
#------- NUMEROS DE VARIAVEIS COM O TAMANHO DA SESSAO ATIVA ----------------------------------------------

		private function setNVars(){
			$this->_nvars = sizeof($_SESSION);
		}
#------- CRIA / ALTERA UMA VARIAVEL DA SESSAO ATIVA ------------------------------------------------------

		private function setVar($var,$values){
			$_SESSION[$var] = $values;
			$this->setNVars();	
		}
#------- ELIMINA UMA VARIAVEL DA SESSAO ATIVA -------------------------------------------------------------

		private function unSetVar($var){
				unset($_SESSION[$var]);
				$this->setNVars();
		}
#------- ELIMINA UMA VARIAVEL DA SESSAO ATIVA -------------------------------------------------------------

		private function getVar($var){
			if(isset($_SESSION[$var])){
				return $_SESSION[$var];
			}else{
				return NULL;
			}
		}
#------- INICIA UMA SESSAO --------------------------------------------------------------------------------

		public function SessionStart(){
			session_start();
			$this->_SID = session_id();
			$this->setNVars();			
		}
#------- CRIA / ALTERA UMA OU MAIS VARIAVEIS DE UMA SESSAO ------------------------------------------------

		public function setVars($var,$values=''){
			if(is_array($var)){
				foreach($var as $_k => $_v){
					$this->setVar($_k,$_v);	
				}
			}else{
				$this->setVar($var,$values);	
			}
		}
#------- CRIA / ALTERA UMA OU MAIS VARIAVEIS DE UMA SESSAO ------------------------------------------------

		public function unSetVars($var){
			if(is_array($var)){
				foreach($var as  $_v){
					$this->unSetVar($_v);	
				}
			}else{
				$this->unSetVar($var);	
			}
		}
#------- RETORNA O VALOR DE UMA OU MAIS VARIAVEIS DE UMA SESSAO ----------------------------------------------

		public function getVars($var){
			if(is_array($var)){
				foreach($var as $_v){
					$_res[] = $this->getVar($_v);
				}
			}else{
				$_res = $this->getVar($var);	
			}
			return $_res;
		}
#------- RETORNA O TAMANHO DE UMA SESSAO ---------------------------------------------------------------

		public function getNVars(){
			return $this->_nvars;	
		}
#------- RETORNA O ID DA SESSAO ATUAL -------------------------------------------------------------------

		public function getSID(){
			return $this->_SID;	
		}
#------- IMPRIME A RELAÇÃO DE VARIVAIES DA SESSAO ATUAL --------------------------------------------------

		public function PrintSessionVars(){
			printf('<div class="well">Variaveis da Sessao ativa:<br>');
			foreach($_SESSION as $_k => $_v){
				printf("Variavel[ %s ] = %s  ",$_k,$_v);
				printf("<br>");
			}
			printf("</div>");
		}
#------- DESTROI A SESSAO ATUAL --------------------------------------------------

		public function SessionDestroy($ini=FALSE){
			session_unset();
			session_destroy();
			$this->setNVars();
			$this->_SID = NULL;
			if($ini==TRUE){
				$this->SessionStart();	
			}
		}
}