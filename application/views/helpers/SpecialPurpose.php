<?php


class jean_SpecialPurpose extends Zend_View_Helper_Abstract
{
    protected $_count = 0;
    public function specialPurpose()
    {
        $this->_count++;
        $output = "I have seen 'The Jerk' {$this->_count} time(s).";
        return htmlspecialchars($output);
    }
}

?>