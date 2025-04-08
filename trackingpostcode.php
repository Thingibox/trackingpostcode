<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class TrackingPostcode extends Module
{
    public function __construct()
    {
        $this->name = 'trackingpostcode';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'Minicraft';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Tracking Number Postcode');
        $this->description = $this->l('Afegeix automàticament el codi postal al número de seguiment');
    }

    public function install()
    {
        return parent::install() && 
               $this->registerHook('actionObjectOrderCarrierUpdateAfter');
    }

    public function hookActionObjectOrderCarrierUpdateAfter($params)
    {
        $order_carrier = $params['object'];
        
        if (!Validate::isLoadedObject($order_carrier) || empty($order_carrier->tracking_number)) {
            return;
        }

        if (strpos($order_carrier->tracking_number, '/') !== false) {
            return;
        }

        $order = new Order($order_carrier->id_order);
        if (!Validate::isLoadedObject($order)) {
            return;
        }

        $address = new Address($order->id_address_delivery);
        if (!Validate::isLoadedObject($address)) {
            return;
        }

        $order_carrier->tracking_number = $order_carrier->tracking_number . '/' . $address->postcode;
        $order_carrier->update();
    }
}