<?php

require_once("postmark.php");
require_once("barcode.inc.php");

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initConfig()
	{	
		$se = new Zend_Session_Namespace('redir_adm');
		if(substr($_SERVER['REQUEST_URI'], 0, 6) != '/login' or substr($_SERVER['REQUEST_URI'], 0, 6) != '/xml'){
			$_SESSION["redir_adm"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		
		$config = new Zend_Config($this->getOptions(), true);
		$sessao = new Zend_Session_Namespace('autenticacao');
		#if($sessao->id_usuario==826570){
		#echo "<pre>";print_r($config);echo "</pre>";
		#}
		Zend_Registry::set('config', $config);
		return $config;
	}//initConfig

	public function _initLayout(){
		
		Zend_Layout::startMvc(array(
			'layout'=> 'index',
			'layoutPath' => '../application/views/scripts/layout'
		));
		
		
	}
	
	public function _initTimezone()
	{
		date_default_timezone_set('Brazil/East');
	}
	
		/**
 * Função faz a conexão com o banco de dados e registra a variável $db para
 * que ela esteja disponível em toda a aplicação.
	 */
	protected function _initConnection()
	{
		
	    /**
	     * Obtém os resources(recursos).
	     */
	    $options    = $this->getOption('resources');
	    $db_adapter = $options['db']['adapter'];
	    $params     = $options['db']['params'];
		
		
		#BANCO REPLICA
		$options2    = $this->getOption('resources2');            
		$db_adapter2 = $options2['db']['adapter'];
		$params2     = $options2['db']['params'];	
	 
	    try{
	 
	        /**
	         * Este método carrega dinamicamente a classe adptadora
	         * usando Zend_Loader::loadClass().
	         */
	        $db = Zend_Db::factory($db_adapter, $params);
			#BANCO REPLICA
			$db2 = Zend_Db::factory($db_adapter2, $params2);

	 
	        /**
	         * Este método retorna um objeto para a conexão representada por uma
	         * respectiva extensão de banco de dados.
	         */
	        $db->getConnection();
	 
	        // Registra a $db para que se torne acessível em toda app
	        $registry = Zend_Registry::getInstance();
	        $registry->set('db', $db);
			#BANCO REPLICA
			$registry->set('db2', $db2);
			
			
	 		Zend_Db_Table::setDefaultAdapter($db);
			
			$loader = Zend_Loader_Autoloader::getInstance();
			$loader->setFallbackAutoloader(true);
			
	    }catch( Zend_Exception $e){
	        echo "Estamos sem conexão ao banco de dados neste momento. Tente mais tarde por favor.";
	 
	        exit;
	    }
	 
	}  
}

