<?php
class App_Mail_PedidoEnviado extends App_Mail_Base
{
	protected $_template = 'pedidos/mail/enviado.phtml';
	protected $_mail_subject = 'Pedido enviado';
	protected $_write_email = true;
}//App_Mail_NovoPedido