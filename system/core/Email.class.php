<?php
#####################################################
# Ella Design em Iluminação Ltda                    #
# www.ella.com.br - 15/09/2004                      #
# Alterado em 04/04/2011 de acordo com wiki Locaweb #
# Alterado em 27/11/2013 por Francisco Nascimento
# compatibilidade com PHP V. 5.4 
#####################################################

class Email {
	
	private $de;
	private $para;
	private $assunto;
	private $mensagem;
	private $headers;
	private $tipo; // html ou txt
	private $quebra_linha;
	private $Cc;
	private $Bcc;
	private $responder;
		
		
	public function From($from)
	{
		$this->de = $from;
		return $this->de;
	}
	
	
	public function To($para)
	{
		$this->para = $para;
		return $this->para;
	}
	
		
	public function Subject($assunto)
	{
		$this->assunto = $assunto;
		return $this->assunto;
	}
			
	public function ContentType($tipo)
	{
		$this->tipo = $tipo;
		return $this->tipo;
	}
	
	
	
	public function Message($mensagem)
	{
		$this->mensagem = $mensagem;
		return $this->mensagem;
	}
	
	
	
	public function ReplyTo($responder)
	{
		$this->responder = $responder;
		
		return $this->responder;
	}
	

	public function Cc($Cc)
	{
		$this->Cc = $Cc;
		return $this->Cc;
	}
	
	
	
	public function Bcc($Bcc)
	{
		$this->Bcc = $Bcc;
		return Bcc;
	}
	
		
	private function tipo(){
	
		$this->quebra_linha = "\n";
		
		switch($this->tipo){
			default:
			case "txt":
				$this->headers = "From: ". $this->de ."\nContent-Type: text/plain";
			break;
			case "html":
				/* Montando o cabeçalho da mensagem */
				$this->headers .= "Content-type: text/html; charset=utf-8" .$this->quebra_linha;
				// Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
				$this->headers .= "From: " .  $this->de.$this->quebra_linha;
				$this->headers .= "Cc: " . $this->Cc . $this->quebra_linha;
				$this->headers .= "Bcc: " . $this->Bcc . $this->quebra_linha;
				$this->headers .= "Return-Path: ".$this->de."\r\n"; // return-path
				$this->headers .= "Reply-To: " . $this->responder . $this->quebra_linha;

			break;
		}
	}
	public function envia(){
	
		try{
		
			
			
				$this->tipo();
				
					if(!mail($this->para, $this->assunto, $this->mensagem, $this->headers ,"-r".$this->de)){ // Se for Postfix
						$headers .= "Return-Path: " . $this->de . $this->quebra_linha; // Se "não for Postfix"
						mail($this->para, $this->assunto, $this->mensagem, $this->headers );
					}
					//mail($this->para,$this->assunto,$this->msg,$this->headers);
					return true;
				
			
			
		}catch(Exception $error){
		
			printf("Erro no envio do email : %s",$error->getMessage());

            return false;
		}
	}
	
		
	
		
	
}
?>
