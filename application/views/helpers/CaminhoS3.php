<?php

class Zend_View_Helper_CaminhoS3 extends Zend_View_Helper_Abstract
{
	public function CaminhoS3()
	{
		$s3Path = Zend_Registry::get('config')->s3;
		
		return $s3Path->bucketHttp;
	}
	
}
	