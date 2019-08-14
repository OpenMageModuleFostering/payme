<?php

class Alignet_Payme_Block_Shared_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('payme/shared/form.phtml');
        parent::_construct();
    }
}