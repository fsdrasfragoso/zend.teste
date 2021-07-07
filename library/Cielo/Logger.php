<?php
class Cielo_Logger
{
	private $log_file;
	private $fp = null;
	
	public function logOpen()
	{
		$this->log_file = APPLICATION_PATH.'/logs/cielo/xml.log';
		$this->fp = fopen($this->log_file, 'a');
	}//logOpen
	 
	public function logWrite($strMessage, $transacao)
	{
		if(!$this->fp)$this->logOpen();
		
		$path = $_SERVER["REQUEST_URI"];
		$data = date("Y-m-d H:i:s:u (T)");
		
		$log = "***********************************************" . "\n";
		$log .= $data . "\n";
		$log .= "DO ARQUIVO: " . $path . "\n"; 
		$log .= "OPERAÇÃO: " . $transacao . "\n";
		$log .= $strMessage . "\n\n"; 

		fwrite($this->fp, $log);
	}//logWrite
}//Cielo_Logger