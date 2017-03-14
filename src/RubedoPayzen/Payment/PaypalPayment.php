<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2014, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license.
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2014 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace RubedoPayzen\Payment;

use RubedoPayzen\Payment\Toolbox\Toolbox;
use Rubedo\Payment\AbstractPayment;


/**
 *
 * @author adobre
 * @category Rubedo
 * @package Rubedo
 */
class PayzenPayment extends AbstractPayment
{
    public function __construct()
    {
        $this->paymentMeans = 'payzen';
        parent::__construct();
    }


    public function getOrderPaymentData($order,$currentUserUrl)
    {
        $toolbox=new Toolbox($this->nativePMConfig);
        $centPrice=$order['finalPrice']*100;
        $args = array(
            'vads_amount' => array(
                'value' => (string) intval($centPrice),
                'label' => 'Price',
                'type' => 'text',
                'class'  => 'vads-field',
                'wrapper_class' => 'vads-wrapper',
                'readonly' => true,
                'help' =>  'Price'
            ),
            "vads_currency" => "978",
            "vads_order_id" => (string) $order["id"]
        );
        $formData = $toolbox->getFormData($args);
        $form = '<form id="payzenAutoSubmitForm" action="'.$formData['form']['action'].'" method="'.$formData['form']['method'].'" accept-charset="'.$formData['form']['accept-charset'].'" class="form-horizontal">';
        foreach ($formData['fields'] as $name => $value) {
            $display_value = (isset($value['value']) && is_array($value)) ? $value['value'] : $value;
            $label = (isset($value['label']) && is_array($value)) ? $value['label'] : $name;
            $class = (isset($value['class']) && is_array($value)) ? $value['class'] : '';
            $help = (isset($value['help']) && $value['help'] !== '' && is_array($value)) ? ' '.$value['help'] : '';
            $wrapper_class = (isset($value['wrapper_class']) && is_array($value)) ? $value['wrapper_class'] : 'hidden';
            $type = (isset($value['type']) && is_array($value) ) ? $value['type'] : 'text';
            $help_link = '<small id="helpBlock" class="help-block">'.$help.'</small>';
            $addon = '';
            $addon_end = '';
            $hidden_field = '';
            if($name == 'vads_amount'){
                $hidden_field = '<input type="hidden" value="'.$display_value.'" name="vads_amount"/>';
                $cents =  substr($display_value,-2);
                $amount = substr($display_value,0,-2);
                $display_value = $amount.','.$cents;
                $addon = '<div class="input-group">';
                $addon_end = '<span class="glyphicon glyphicon-euro form-control-feedback" aria-hidden="true"></span></div>';
            }
            //$form .= $hidden_field;
            $form .= '<div class="form-group '.$wrapper_class.'">';
            $form .= '<label for="'. $name. '" class="col-sm-2 control-label">'.$label.'</label>';
            $form .= '<div class="col-sm-10">';
            $form .= $addon;
            $form .= $hidden_field;
            $form .= '<input type="'.$type.'" readonly="readonly"  class="form-control '.$class.'"  name="'.$name.'" value="'.$display_value.'" />';
            $form .= $help_link;
            $form .= $addon_end;
            $form .= '</div></div>';
        }
        $form .= '<button type="submit" class="btn btn-default">Payer</button>';
        $form .= '</form><script>document.getElementById("payzenAutoSubmitForm").submit();</script>';
        return [
            'whatToDo'=>'displayRichText',
            'richText'=>$form
        ];
    }
}
