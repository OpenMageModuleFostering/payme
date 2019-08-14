<?php

abstract class Alignet_Payme_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Redirect Block
     * need to be redeclared
     */
    protected $_redirectBlockType;

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select 2CO payment method
     */
    public function redirectAction()
    {

        $session = $this->getCheckout();
        $session->setPaymeQuoteId($session->getQuoteId());
        $session->setPaymeRealOrderId($session->getLastRealOrderId());
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), Mage::helper('payme')->__('El cliente ha sido redirecionado a Payme.'));
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock($this->_redirectBlockType)
                ->setOrder($order)
                ->toHtml()
        );        
        $order->save();

        $session->unsQuoteId();
        $session->unsLastRealOrderId();
    }

// 400000065
   

}