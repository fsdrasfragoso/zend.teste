<?php
class HomesiteModel extends Zend_Db_Table_Abstract 
{
	protected $_name = 'sa_home_site';
		
        public function getHomeSite()
	{    
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('h' => $this->_name), array('*'))
                    ->joinInner(array('m' => 'sa_menu_contexto'),"h.id_menu_contexto = m.id_menu_contexto",array("pai"=>'nm_item'))
                    ->joinLeft(array('mc' => 'sa_menu_contexto'),"h.id_menu_contexto = mc.id_menu_contexto",array("nm_item"=>'nm_item'))
                    ->join(array('n' => 'sa_noticia'),'h.id_noticia = n.id_noticia',array('n.ds_url_imagem'))
                    ->join(array('hs' => 'sa_home_site_posicao'),'hs.id_home_site_posicao = h.id_home_site_posicao',array('hs.ds_div'))
                    ->where('h.id_situacao_cadastro = 1')
                    ->order("h.id_home_site_posicao")->order("h.nr_ordem"); 	

		return $this->fetchAll($select)->toArray();
	}	
        
        public function getDadosHomeSite($id, $posicao, $ordem)
	{    
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('h' => $this->_name), array('*'))
                    ->join(array('n' => 'sa_noticia'),'h.id_noticia = n.id_noticia',array('n.ds_url_imagem'))
                    ->where("h.id_noticia= ".$id." AND h.id_home_site_posicao= ".$posicao." AND h.nr_ordem= ".$ordem); 	

		return $this->fetchRow($select)->toArray();
	}  
                
        public function getbyPosicao($ds_div)
	{    
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('h' => $this->_name), array('*'))
                    ->joinInner(array('m' => 'sa_menu_contexto'),"h.id_menu_contexto = m.id_menu_contexto",array("pai"=>'nm_item'))
                    ->joinLeft(array('mc' => 'sa_menu_contexto'),"h.id_menu_contexto = mc.id_menu_contexto",array("nm_item"=>'nm_item'))
                    ->join(array('n' => 'sa_noticia'),'h.id_noticia = n.id_noticia',array('n.ds_url_imagem'))
                    ->join(array('hs' => 'sa_home_site_posicao'),'hs.id_home_site_posicao = h.id_home_site_posicao',array('hs.ds_div'))
                    ->where("h.id_situacao_cadastro = 1 AND hs.ds_div LIKE '".$ds_div."'")
                    ->order("h.id_home_site_posicao")->order("h.nr_ordem"); 	
//echo $select;die;
		return $this->fetchAll($select)->toArray();
	}

}
?>
