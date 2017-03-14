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
        die("test");
    }
}
