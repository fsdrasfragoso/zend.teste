<?php
class App_Mail_PedidoPago extends App_Mail_Base
{
	protected $_template = 'pedidos/mail/pago.phtml';
	protected $_mail_subject = 'Pedido pago';
	protected $_write_email = true;
}//App_Mail_NovoPedido