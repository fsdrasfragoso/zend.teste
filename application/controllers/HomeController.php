<?php

class HomeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageClass = 'home';
        $this->view->pageJS = array('dashboard');
        $this->view->pageCSS = array('dashboard' );

        
		//$this->view->menu();
    }

    public function indexAction()
    {
		
    }

	



}