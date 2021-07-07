<?php
class App_PedidoLabel
{
	/**
	 *
	 * @return Zend_Pdf
	 */
	public function createBlankLabel()
	{
		return Zend_Pdf::load(APPLICATION_PATH.'/etc/label/blank.pdf');
	}//createBlankLabel

	/**
	 *
	 * @param int|array $ids
	 * @return Zend_Pdf
	 */
	public function renderLabels($ids)
	{
		$pdf = $this->createBlankLabel();
		/* @var $blank Zend_Pdf_Page */
		$blank = $pdf->pages[0];
		unset($pdf->pages[0]);

		foreach($ids as $id)
		{
			$page = $this->renderPage($blank, $id);
			if(!is_null($page))$pdf->pages[] = $page;
		}//for i

		return $pdf;
	}//renderLabels

	/**
	 *
	 * @param Zend_Pdf_Page $blank
	 * @param int $id
	 * @return \Zend_Pdf_Page|null
	 */
	public function renderPage($blank, $id)
	{
		$page = new Zend_Pdf_Page($blank);

		$tb = new PedidoModel();
		$ped = $tb->getPedido($id);

		if($ped->getEndereco() != null)
		{
			$font = Zend_Pdf_Font::fontWithPath(APPLICATION_PATH.'/etc/label/arialbd.ttf');
			$page->setFont($font, 10);

			$page->drawText($ped->getUsuario()->ds_nome, 2, 57, 'UTF-8');
			$page->drawText('Pedido: '.$ped->ds_cod, 2, 44, 'UTF-8');

			$end1 = "{$ped->getEndereco()->ds_endereco}, {$ped->getEndereco()->ds_num}";
			if($ped->getEndereco()->ds_complemento != '')$end1 .= " {$ped->getEndereco()->ds_complemento}";
			$end2 = "{$ped->getEndereco()->ds_bairro} - {$ped->getEndereco()->getCidade()->ds_cidade}-{$ped->getEndereco()->getCidade()->ds_estado}";
			$end3 = "CEP:{$ped->getEndereco()->nr_cep}";

			$font = Zend_Pdf_Font::fontWithPath(APPLICATION_PATH.'/etc/label/arial.ttf');
			$page->setFont($font, 9);
			$page->drawText($end1, 2, 30, 'UTF-8');
			$page->drawText($end2, 2, 19, 'UTF-8');
			$page->drawText($end3, 2, 8, 'UTF-8');

			return $page;
		}//if endereco
		return null;
	}//renderPage

}//App_PedidoLabel