<?php  

class Alignet_Payme_Model_Shared extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'payme_shared';

    // protected $_isGateway               = true; //false
    // protected $_canAuthorize            = true; //false
    // protected $_canCapture              = true;
    // protected $_canCapturePartial       = true; //false
    // protected $_canRefund               = true; //false
    // protected $_canVoid                 = true; //false
    // protected $_canUseInternal          = true; //false
    // protected $_canUseCheckout          = true;
    // protected $_canUseForMultishipping  = true; //false

    protected $_formBlockType = 'payme/shared_form';
    protected $_paymentMethod = 'shared';
    protected $_order;

    private $descriptionProducts = 'Prpduct Information';

    public function cleanString($string) {
        
        $string_step1 = strip_tags($string);
        $string_step2 = nl2br($string_step1);
        $string_step3 = str_replace("<br />","<br>",$string_step2);
        $cleaned_string = str_replace("\""," inch",$string_step3);        
        return $cleaned_string;
    }


    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    
    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }

    // public function getCustomerId()
    // {
    //     return Mage::getStoreConfig('payment/' . $this->getCode() . '/customer_id');
    // }
	
    // public function getAccepteCurrency()
    // {
    //     return Mage::getStoreConfig('payment/' . $this->getCode() . '/currency');
    // }
	
    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('payme/shared/redirect');
    }

    public function setDescripcionProducts($descriptionProducts) {
    	$this->descriptionProducts = $descriptionProducts;
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
	
	    $billing = $this->getOrder()->getBillingAddress();
	    $shipping = $this->getOrder()->getShippingAddress();
        $coFields = array();

		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currency_name = Mage::app()->getLocale()->currency($baseCurrencyCode)->getName();
        $purchaseCurrencyCode = '';
		switch ($baseCurrencyCode) {
			case 'PEN':
				$purchaseCurrencyCode = 604;
				break;
			case 'USD':
				$purchaseCurrencyCode = 840;
				break;
			default:
				$purchaseCurrencyCode = 604;
				break;
		}

		// $locale = Mage::getStoreConfig('general/locale/code');
		// $locale = Mage::app()->getStore()->getLocaleCode();
		// $locale = Mage::getStoreConfig('general/locale/code', Mage::app()->getStore()->getId());
		$locale = Mage::app()->getLocale()->getLocaleCode();
		$locale = substr($locale, 0, 2);
		$language = '';
		switch ($locale) {
			case 'es':
				$language = 'ES';
				break;
			case 'en':
				$language = 'EN';
				break;
			default:
				$language = 'ES';
				break;
		}

  //       $countProducts = 0;
		// $descriptionProducts = '';
		// $idProducts = '';
		
		// $session = Mage::getSingleton('checkout/session');
		// foreach ($session->getQuote()->getAllItems() as $item) {
		// 	$idProducts .= $item->getProductId() . ',';
		// 	$descriptionProducts .= $item->getName().",";
		// 	$countProducts++;
		// }

		// $order = Mage::getModel('sales/order')->loadByIncrementId($this->getOrder()->getRealOrderId());
		// $items = $order->getAllItems();
		// foreach ($items as $itemId => $item) {
		// 	$idProducts .= $item->getProductId() . ',';
		// 	$descriptionProducts .= Mage::getModel('catalog/product')->load($item->getProductId()) . ',';
		// 	$countProducts++;
		// }

		// $cart = Mage::getModel('checkout/cart')->getQuote();
		// foreach ($cart->getAllItems() as $item) {
		//     $descriptionProducts .= $item->getProduct()->getName() . ',';
		// }

		// if ($countProducts == 1) {
		// 	$descriptionProducts = substr(trim($descriptionProducts, ','), 0, 30);
		// } else {
		// 	$descriptionProducts = "Varios ($countProducts Productos)";
		// }

		$ALIGNET_DEBUG = $this->getDebug();
		$ALIGNET_IDENTCOMMERCE = Mage::getStoreConfig('payment/payme_shared/ALIGNET_IDENTCOMMERCE');
		$ALIGNET_KEYWALLET = Mage::getStoreConfig('payment/payme_shared/ALIGNET_KEYWALLET');
		$ALIGNET_IDACQUIRER = Mage::getStoreConfig('payment/payme_shared/ALIGNET_IDACQUIRER');
		$ALIGNET_IDCOMMERCE = Mage::getStoreConfig('payment/payme_shared/ALIGNET_IDCOMMERCE');
		$ALIGNET_MCC = Mage::getStoreConfig('payment/payme_shared/ALIGNET_MCC');
		$ALIGNET_KEY = Mage::getStoreConfig('payment/payme_shared/ALIGNET_KEY');
		$ALIGNET_URLTPV = Mage::getStoreConfig('payment/payme_shared/ALIGNET_URLTPV');

		$long = 0;
		$num_ope = number_format($this->getOrder()->getRealOrderId(),0,'','');
		$purchaseOperationNumber = 0;
		if ($ALIGNET_IDACQUIRER == 144 || $ALIGNET_IDACQUIRER == 29) {
			$long = 5;
			$purchaseOperationNumber = str_pad($num_ope, $long, "0", STR_PAD_LEFT);
			$purchaseOperationNumber = substr($purchaseOperationNumber, 4, 5)
		} elseif ($ALIGNET_IDACQUIRER == 84 || $ALIGNET_IDACQUIRER == 10) {
			$long = 9;
			$purchaseOperationNumber = str_pad($num_ope, $long, "0", STR_PAD_LEFT);
		}
		$commerceAssociated = '';
		if ($ALIGNET_IDACQUIRER == 144 || $ALIGNET_IDACQUIRER == 29) {
			switch ($purchaseCurrencyCode) {
				case 604:
					$commerceAssociated = 'MALL ALIGNET-PSP SOLES';
					break;
				case 840:
					$commerceAssociated = 'MALL ALIGNET-PSP DOLARES';
					break;
				default:
					$commerceAssociated = 'MALL ALIGNET-PSP';
					break;
			}
		}

		$purchaseAmount = floatval(str_replace('.','',number_format($this->getOrder()->getBaseGrandTotal(), 2, '.', '')));

		if (phpversion() >= 5.3) {
			$registerVerification = openssl_digest($ALIGNET_IDENTCOMMERCE . $this->getOrder()->getCustomerId() . $this->getOrder()->getCustomerEmail() . $ALIGNET_KEYWALLET, 'sha512');
			
			$purchaseVerification = openssl_digest($ALIGNET_IDACQUIRER . $ALIGNET_IDENTCOMMERCE . $purchaseOperationNumber . $purchaseAmount . $purchaseCurrencyCode . $ALIGNET_KEYWALLET, 'sha512');
		} else {
			$registerVerification = hash('sha512', $ALIGNET_IDENTCOMMERCE . $this->getOrder()->getCustomerId() . $this->getOrder()->getCustomerEmail() . $ALIGNET_KEYWALLET);
			$purchaseVerification = hash('sha512', $ALIGNET_IDACQUIRER . $ALIGNET_IDENTCOMMERCE . $purchaseOperationNumber . $purchaseAmount . $purchaseCurrencyCode . $ALIGNET_KEYWALLET);
		}

		$codAsoCardHolderWallet = '';

		switch($ALIGNET_URLTPV){
			case 0:
				$wsdl = "https://test2.alignetsac.com/WALLETWS/services/WalletCommerce?wsdl";
				break;
			case 1:
				$wsdl = "https://www.pay-me.pe/WALLETWS/services/WalletCommerce?wsdl";
				break;
		}
		$client = new SoapClient($wsdl);
		$params = array(
		    'idEntCommerce'=>(string)$ALIGNET_IDENTCOMMERCE,
		    'codCardHolderCommerce'=>(string)$this->getOrder()->getCustomerId(),
		    'names'=>$billing->getFirstname(),
		    'lastNames'=>$billing->getLastname(),
		    'mail'=>$billing->getEmail(),
		    'reserved1'=>'',
		    'reserved2'=>'',
		    'reserved3'=>'',
		    'registerVerification'=>$registerVerification
		);
		$result = $client->RegisterCardHolder($params);
		$codAsoCardHolderWallet = $result->codAsoCardHolderWallet;
		
        $coFields["codAsoCardHolderWallet"] = $this->getOrder()->getCustomerId();
        $coFields["names"] = $billing->getFirstname();
        $coFields["lastNames"] = $billing->getLastname();
        $coFields["mail"] = $billing->getEmail();

	    $coFields["acquirerId"] = $ALIGNET_IDACQUIRER;
	    $coFields["idCommerce"] = $ALIGNET_IDCOMMERCE;
		$coFields["purchaseOperationNumber"] = $purchaseOperationNumber;
		$coFields["purchaseAmount"] = $purchaseAmount;
		$coFields["purchaseCurrencyCode"] = $purchaseCurrencyCode;
		$coFields["language"] = $language;
		$coFields['billingFirstName'] = $billing->getFirstname();
		$coFields['billingLastName'] = $billing->getLastname();
		$coFields['billingEmail'] = $billing->getEmail();
		$coFields['billingAddress'] = $billing->getStreet()[0];
        $coFields['billingZip'] = $billing->getPostcode();
		$coFields['billingCity'] = $billing->getCity();
        $coFields['billingState'] = $billing->getRegion();
		$coFields['billingCountry'] = $billing->getCountry();
        $coFields['billingPhone'] = $billing->getTelephone();
		$coFields['shippingFirstName'] = $shipping->getFirstname();
		$coFields['shippingLastName'] = $shipping->getLastname();
		$coFields['shippingEmail'] = $shipping->getEmail();
		$coFields['shippingAddress'] = $shipping->getStreet()[0];
        $coFields['shippingZip'] = $shipping->getPostcode();
		$coFields['shippingCity'] = $shipping->getCity();
        $coFields['shippingState'] = $shipping->getRegion();
		$coFields['shippingCountry'] = $shipping->getCountry();
        $coFields['shippingPhone'] = $shipping->getTelephone();
        $coFields["userCommerce"] = $this->getOrder()->getCustomerId();
        $coFields["userCodePayme"] = $codAsoCardHolderWallet;
        if ($ALIGNET_IDACQUIRER == 144 || $ALIGNET_IDACQUIRER == 29) {
	    	$coFields["mcc"] = $ALIGNET_MCC;
        	$coFields['commerceAssociated'] = $commerceAssociated;
        }
        $coFields['descriptionProducts'] = $this->descriptionProducts;
        $coFields['programmingLanguage'] = 'PHP';
        $coFields['reserved1'] = $num_ope;
        $coFields['reserved2'] = '';
        $coFields['reserved3'] = $purchaseAmount;
        $coFields['reserved4'] = $currency_name;
	    $coFields["purchaseVerification"] = $purchaseVerification;
        return $coFields;
    }

    /**
     * Get url of Payu payment
     *
     * @return string
     */
    public function getPaymeSharedUrl()
    {
        $ALIGNET_URLTPV = Mage::getStoreConfig('payment/payme_shared/ALIGNET_URLTPV');
		
		$url = 'https://test2.alignetsac.com/VPOS2/faces/pages/startPayme.xhtml';
		
		if($ALIGNET_URLTPV == 1) {
		  $url = 'https://vpayment.verifika.com/VPOS2/faces/pages/startPayme.xhtml';
		}
		 
        return $url;
    }
       

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/payme_shared/ALIGNET_DEBUG');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * parse response POST array from gateway page and return payment status
     *
     * @return bool
     */
    public function parseResponse()
    {       

            return true;
    
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }
	
	
	public function getResponseOperation($response) {
		$authorizationResult = trim($response['authorizationResult']) == "" ? "-" : $response['authorizationResult'];
		$authorizationCode = trim($response['authorizationCode']) == "" ? "-" : $response['authorizationCode'];
		$errorCode = trim($response['errorCode']) == "" ? "-" : $response['errorCode'];
		$errorMessage = trim($_POST['errorMessage']) == "" ? "-" : $_POST['errorMessage'];
		$bin = trim($response['bin']) == "" ? "-" : $response['bin'];
		$brand = trim($response['brand']) == "" ? "-" : $response['brand'];
		$paymentReferenceCode = trim($response['paymentReferenceCode']) == "" ? "-" : $response['paymentReferenceCode'];
		$reserved1 = trim($response['reserved1']) == "" ? "-" : $response['reserved1'];
		$reserved2 = trim($response['reserved2']) == "" ? "-" : $response['reserved2'];
		$reserved3 = trim($response['reserved3']) == "" ? "-" : $response['reserved3'];
		$reserved4 = trim($response['reserved4']) == "" ? "-" : $response['reserved4'];
		$order = Mage::getModel('sales/order');
		// return $reserved1;
		if ($authorizationResult == '00') {
			$fechaHora = date("d/m/Y H:i:s");
			$message = Mage::helper('payme')->__('Número de Operación de Compra') . ": " . $reserved1 . " | ";
			$message .= Mage::helper('payme')->__('Resultado de la Operación: Operación autorizada y exitosa') . " | ";
			$message .= Mage::helper('payme')->__('Fecha y Hora de la Operación') . ": " . $fechaHora . " | ";
			$message .= Mage::helper('payme')->__('Monto') . ": " . $reserved3 . " | ";
			$message .= Mage::helper('payme')->__('Moneda') . ": " . $reserved4 . " | ";
			$message .= Mage::helper('payme')->__('Marca de la Tarjeta') . ": " . $brand . "";
			
			$order->loadByIncrementId($reserved1);
			// $order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true);
			$order->setData('state', "Complete");
	        $order->setStatus("Complete");
	        $history = $order->addStatusHistoryComment(Mage::helper('payme')->__('El estado del pedido: "Complete"'), false);
	        $history = $order->addStatusHistoryComment($message, false);
	        $history->setIsCustomerNotified(false);
			$order->save();
			$order->sendNewOrderEmail();
			return 'checkout/onepage/success';
		} elseif ($authorizationResult == '01'){
			$order->loadByIncrementId($reserved1);
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
			$this->updateInventory($reserved1);
			$order->cancel()->save();
			return 'checkout/onepage/failure';
		} elseif ($authorizationResult == '05') {
			$order->loadByIncrementId($reserved1);
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
			$this->updateInventory($reserved1);
			$order->cancel()->save();
			return 'checkout/onepage/failure';
			// return 'canceled';
		}
	}
	
    public function updateInventory($orderId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        $items = $order->getAllItems();
		foreach ($items as $itemId => $item) {
		   $orderedQuantity = $item->getQtyToInvoice();
		   $sku = $item->getSku();
		   $product = Mage::getModel('catalog/product')->load($item->getProductId());
		   $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getQty();
		  
		   $updatedInventory=$qtyStock+ $orderedQuantity;
					
		   $stockData = $product->getStockItem();
		   $stockData->setData('qty',$updatedInventory);
		   $stockData->save();
	   } 
    }
	
}