<?php
require_once('Zend/Mail.php');
require_once('Zend/Mail/Transport/Smtp.php');

class Dba_Mail_Standard extends Zend_Mail
{
	public function __construct($data = null)
	{
		$this->init();
		if(!is_null($data))$this->makeMail($data);
	}//constructor

	//// MEMBERS ////
	protected $_template = 'file';
	protected $_mail_from = array('email'=>'email@email.com', 'name'=>'Nobody');
	protected $_mail_subject = 'Assunto';
	protected $_mail_bcc = array();
	protected $_write_email = false;

	protected $_content = '';

	protected $_default_target = '';

	protected static $_tpl_path = '/';

	//// METHODS ////

	public function setTemplate($tpl_name)
	{
		$this->_template = $tpl_name;
	} //setTemplate

	public static function setTplPath($path)
	{
		self::$_tpl_path = $path;
	}//setTplPath

	public static function getTplPath()
	{
		return self::$_tpl_path;
	}//getTplPath

    public function setSubject($subject)
    {
		$subject = $this->_filterOther($subject);
        $this->_subject = $this->_encodeHeader($subject);
        $this->_storeHeader('Subject', $this->_subject);
        return $this;
    }//setSubject

	protected function init()
	{
		parent::__construct('UTF-8');
		$trans = $this->getTransport();
		if(!is_null($trans))Zend_Mail::setDefaultTransport($trans);
	}//init

	/**
	 * Cria e configura "meio de transporte" de nossa cartinha eletrônica
	 *
	 * @return Zend_Mail_Transport_Abstract $transport
	 */
	protected function getTransport()
	{
		return null;
	}//getTransport

	protected function addDefaultData(&$data)
	{

	}//addDefaultData

	/**
	 *
	 * @param $data array
	 * @return Dba_Mail_Standard
	 */
	public function makeMail($data)
	{
		$this->addDefaultData($data);

	    $this->setMailAtrr($data);

	    $this->_content = $html = $this->setMailContent($data);

		$this->setBodyHtml($html);
		$this->setFrom($this->_mail_from['email'], $this->_mail_from['name']);
		$this->setSubject($this->_mail_subject);

		return $this;
	}//makeMail

	public function sendDefault()
	{
		if(!empty($this->_default_target))return $this->sendTo($this->_default_target);
			else return false;
	}//sendDefault

	public function sendTo($emails)
	{
		$this->clearRecipients();

		if(!is_array($emails))$emails = array($emails);

		foreach($this->_mail_bcc as $bcc)
		{
			$this->addBcc($bcc);
		}//foreach emails

		foreach($emails as $mail)
		{
			if(is_array($mail))
			{
				$this->addTo($mail['email'], $mail['name']);
			}//if mail array
			else
			{
				$this->addTo($mail);
			}//else bland
		}//foreach emails

		try
		{
			//$this->send();

			$this->writeLog('enviado');

			return true;
		}//try sending
		catch(Zend_Mail_Transport_Exception $error)
		{
			$this->writeLog($error->getMessage());
			return false;
		}//houston, we have a problem!
	}//sendTo

    /**
     * Sets From-header and sender of the message
     *
     * @param  string    $email
     * @param  string    $name
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if called subsequent times
     */
    public function setFrom($email, $name = null)
    {
    	unset($this->_headers['From']);
    	$this->_from = null;
        return parent::setFrom($email, $name);
    }//setFrom

    protected function writeLog($error)
    {
		$fname = date('YmdHis').'-'.get_class($this);

		$html = "$error\n$this->_content";
		$fname = APPLICATION_PATH."/etc/$fname.html";

		file_put_contents($fname, $html);
    }//writeLog

    /**
     *
     * @return Zend_View
     */
    protected function getView()
    {
    	if(is_null($this->_view))
    	{
    		$this->_view = new Zend_View();
   			$this->_view->addScriptPath(APPLICATION_PATH.'/views/scripts/');
    	}//if !view
    	return $this->_view;
    }//getView

	protected function setMailContent($data)
	{
		$this->getView()->assign($data);
		return $this->getView()->render($this->_template);
	}//setMailContent

	protected function setMailAtrr($data)
	{
	    foreach($data as $field=>$value)
	    {
	        $field_var = "#$field#";

	        if(strpos($this->_mail_subject, $field_var) !== false)
	        {
	            $this->_mail_subject = str_replace($field_var, $value, $this->_mail_subject);
	        }//if subject has var

	        if(strpos($this->_mail_from['name'], $field_var) !== false)
	        {
	          $this->_mail_from['name'] = str_replace($field_var, $value, $this->_mail_from['name']);
	        }//if name has var

	        if(strpos($this->_mail_from['email'], $field_var) !== false)
	        {
	          $this->_mail_from['email'] = str_replace($field_var, $value, $this->_mail_from['email']);
	        }//if email has var
	    }//foreach data
	}//setMailAtrr

}//Dba_Mail_Standard
?>