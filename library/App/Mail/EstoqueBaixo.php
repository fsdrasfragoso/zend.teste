<?php
class App_Mail_EstoqueBaixo extends App_Mail_Base
{
	protected $_template = 'estoque/mail/alerta.phtml';
	protected $_mail_subject = 'Alerta de estoque baixo';
	protected $_write_email = true;

	protected $_default_target = array('estoque@ativo.com', 'emeail@ativo.com');

}//App_Mail_EstoqueBaixo