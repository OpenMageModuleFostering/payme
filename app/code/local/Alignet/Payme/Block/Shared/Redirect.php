<?php

class Alignet_Payme_Block_Shared_Redirect extends Mage_Core_Block_Abstract {
    protected function _toHtml() {
        $shared = $this->getOrder()->getPayment()->getMethodInstance();

        $html = '<html><body>';

        if ($shared->getDebug() == 1){

            $labels = array('idEntCommerce' => 'ID Wallet de Comercio', 'names' => 'Nombres', 'lastNames' => 'Apellidos', 'mail' => 'Email', 'codAsoCardHolderWallet' => 'Código del titular de la tarjeta', 'acquirerId' => 'acquirerId', 'idCommerce' => 'idCommerce', 'purchaseOperationNumber' => 'Número de Operación', 'purchaseAmount' => 'Monto', 'purchaseCurrencyCode' => 'Código de moneda', 'language' => 'Idioma', 'billingFirstName' => 'Nombres de Facturación', 'billingLastName' => 'Apellidos de Facturación', 'billingEmail' => 'Email de Facturación', 'billingAddress' => 'Dirección de Facturación', 'billingZIP' => 'ZIP de Facturación', 'billingCity' => 'Ciudad de Facturación', 'billingState' => 'Estado de Facturación', 'billingCountry' => 'País de Facturación', 'billingPhone' => 'Teléfono de Facturación', 'shippingFirstName' => 'Nombres de Envío', 'shippingLastName' => 'Apellidos de Envío', 'shippingEmail' => 'Email de Envío', 'shippingAddress' => 'Dirección de Envío', 'shippingZIP' => 'ZIP de Envío', 'shippingCity' => 'Ciudad de Envío', 'shippingState' => 'Estado de Envío', 'shippingCountry' => 'País de Envío', 'shippingPhone' => 'Teléfono de Envío', 'mcc' => 'MCC', 'commerceAssociated' => 'Comercio Asociado', 'descriptionProducts' => 'Producto(s)', 'userCodePayme' => 'Código de Usuario Payme', 'registerVerification' => 'Cifrado de registro', 'purchaseVerification' => 'Cifrado de compra', 'keyvpos2' => 'KEY VPOS 2');

            $html .= 'WALLET<br>';

            foreach ($shared->getFormFields() as $field=>$value) {
                if ($field == 'userCommerce')
                    $html .= '';
                elseif ($field == 'userCodePayme')
                    $html .= '';
                elseif ($field == 'programmingLanguage')
                    $html .= '';
                elseif ($field == 'purchaseVerification')
                    $html .= '';
                elseif ($field == 'reserved1')
                    $html .= '';
                elseif ($field == 'reserved2')
                    $html .= '';
                elseif ($field == 'reserved3')
                    $html .= '';
                elseif ($field == 'reserved4')
                    $html .= '';
                elseif ($field == 'reserved5')
                    $html .= '';
                elseif ($field == 'reserved6')
                    $html .= '';
                elseif ($field == 'reserved7')
                    $html .= '';
                elseif ($field == 'reserved8')
                    $html .= '';
                else {
                    if ($field == 'acquirerId')
                        // $html .= '<br>VPOS2<br>' . $labels[$field] . ': ' . $value . '<br>';
                        $html .= '<br>VPOS2<br>' . $field . ': ' . $value . '<br>';
                    else
                        // $html .= $labels[$field] . ': ' . $value . '<br>';
                        $html .= $field . ': ' . $value . '<br>';
                }
            }
        } else {
            $form = new Varien_Data_Form();
            $form->setAction($shared->getPaymeSharedUrl())
                ->setId('payme_shared_checkout')
                ->setName('payme_shared_checkout')
                ->setMethod('POST')
                ->setUseContainer(true);
            foreach ($shared->getFormFields() as $field=>$value) {
                $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
            }
            $html.= '<center><img src="https://test2.alignetsac.com/WALLET/resources/correo/logo0.png"/></br>' . $this->__('Usted sera redirigido a Payme en pocos segundos.') . '</center>';
            // Se le redireccionará a la pasarela VPOS de Alignet
            $html.= $form->toHtml();
            $html.= '<script type="text/javascript">document.getElementById("payme_shared_checkout").submit();</script>';
        }
        
        $html.= '</body></html>';

        return $html;
    }
}