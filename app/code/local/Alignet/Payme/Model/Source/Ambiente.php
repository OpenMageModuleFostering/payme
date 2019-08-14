<?php

class Alignet_Payme_Model_Source_Ambiente
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => 'Testing (Desarrollo)'),
            array('value' => '1', 'label' => 'Producción'),
        );
    }
}