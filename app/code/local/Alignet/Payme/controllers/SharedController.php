<?php 

class Alignet_Payme_SharedController extends Alignet_Payme_Controller_Abstract
{
   
    protected $_redirectBlockType = 'payme/shared_redirect';
    protected $_paymentInst = NULL;
	
	public function successAction() {
        $response = $this->getRequest()->getPost();
		$retorno = Mage::getModel('payme/shared')->getResponseOperation($response);
		// if ($retorno == 'canceled') {
			// $this->getCheckout()->clear();
			// $this->loadLayout();
	        // $this->renderLayout();
	        // Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles());
		// } else {
        	$this->_redirect($retorno);
		// }
		// return $retorno;
    }
	
	
	
	//  public function failureAction()
 //    {
       
	//    $arrParams = $this->getRequest()->getPost();
	//    Mage::getModel('payme/shared')->getResponseOperation($arrParams);
 //       $this->getCheckout()->clear();
	//    $this->_redirect('checkout/onepage/failure');
 //    }


 //    public function canceledAction()
 //    {
	//     $arrParams = $this->getRequest()->getParams();
	
       
	// 	Mage::getModel('payme/shared')->getResponseOperation($arrParams);
		
	// 	$this->getCheckout()->clear();
	// 	$this->loadLayout();
 //        $this->renderLayout();
 //    }


   

    
}
    
    