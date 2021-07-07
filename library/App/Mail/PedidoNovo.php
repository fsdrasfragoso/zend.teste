<?php
class App_Mail_PedidoNovo extends App_Mail_Base
{
	protected $_template = 'pedidos/mail/novo.phtml';
	protected $_mail_subject = 'Novo pedido';
	protected $_write_email = true;
}//App_Mail_PedidoNovo