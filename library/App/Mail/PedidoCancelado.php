<?php
class App_Mail_PedidoCancelado extends App_Mail_Base
{
	protected $_template = 'pedidos/mail/cancelado.phtml';
	protected $_mail_subject = 'Pedido cancelado';
	protected $_write_email = true;
}//App_Mail_NovoPedido