<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        // action body
        $this->_helper->redirector->gotoSimple("index", "home");
    }

    public function acaoAction()
    {

    }
    public function barcodeAction()
    {
        $params = $this->getRequest()->getParams();
//        print_r($params);
//        echo $params['codigo'];
//        die();
        $this->_helper->layout->disableLayout();
        $this->view->codigo = $params['codigo'];
    }

}

