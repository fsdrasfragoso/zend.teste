<?php
class App_Mail_Base extends Dba_Mail_Standard
{
	//// MEMBERS ////
	protected $_template = 'file';
	protected $_mail_from = array('email'=>'no-reply@ativoi.com', 'name'=>'Ativo.com');
	protected $_mail_subject = 'E-mail automático';
	protected $_mail_bcc = array('diogo.abdalla@gmail.com');
	protected $_write_email = true;

	//// METHODS ////

	public function makeMail($data)
	{
		$data['frontUrl'] = Zend_Registry::get('config')->frontURL;
		parent::makeMail($data);
	}//makeMail

	/**
	 * Cria e configura "meio de transporte" de nossa cartinha eletrônica
	 *
	 * @return Zend_Mail_Transport_Abstract $transport
	 */
	/*
	protected function getTransport()
	{
		return null;
	}//getTransport
	*/

}//App_Mail_Base