<?php

class Zend_View_Helper_SetTranslation extends Zend_View_Helper_Abstract
{
    /**
    *
    * @param string $text
    * @param string $url
    * @param string $domain
    *
    * @return string
    */
    public function setTranslation($text,$url,$domain=null) {

        $localDomain = array("localhost", "com");
        $serverName = $_SERVER['SERVER_NAME'];
        if ($serverName!='localhost' && empty($domain)) {
            $vetorServer = explode(".", $serverName);
            $totalVetor = count($vetorServer);
            $domain=$vetorServer[$totalVetor-1];
        }
        $domain=strtolower($domain);
		if($domain == 'com'){
			$domain = 'br';
		}
        // $PaisOrigemModel = new PaisOrigemModel();
        // $paisOrigem = $PaisOrigemModel->getPaisOrigemBySigla($domain);

        switch ($domain) {
         case 'br':
         $traducao = 'pt_BR';
         break;
         case 'ar':
         $traducao = 'es_AR';
         break;
         case 'cl':
         $traducao = 'es_CL';
         break;
         case 'mx':
         $traducao = 'es_MX';
         break;
         case 'uy':
         $traducao = 'es_UY';
         break;
         case 'co':
         $traducao = 'es_CO';
         break;
         case 'pe':
         $traducao = 'es_PE';
         break;
         default:
         $traducao = 'pt_BR';
        }

        $localeFile = $_SERVER['DOCUMENT_ROOT'].$url.'/locale/'.$traducao.'.mo';
        if (file_exists($localeFile) && !in_array($domain, $localDomain)) {
            $translate = new Zend_Translate(
                array(
                    'adapter' => 'gettext',
                    'content' => $localeFile,
                    'locale'  => $domain
                )
            );
            return $translate->_($text);
        } else {
            return $text;
        }
    }
}	