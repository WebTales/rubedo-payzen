<?php

namespace RubedoPayzen\Rest\V1\Ecommerce\Payments;

use RubedoAPI\Rest\V1\AbstractResource;
use Rubedo\Services\Manager;
use RubedoAPI\Entities\API\Definition\VerbDefinitionEntity;
use RubedoPayzen\Payment\Toolbox\Toolbox;
use WebTales\MongoFilters\Filter;

class PayzenResource extends AbstractResource
{

    /**
     * native config for this payment means
     *
     * @var array
     */
    protected $nativePMConfig;

    public function __construct()
    {
        parent::__construct();
        $pmConfig=Manager::getService("PaymentConfigs")->getConfigForPM("payzen");
        $this->nativePMConfig=$pmConfig['data']['nativePMConfig'];
        $this
            ->definition
            ->setName('Payzen')
            ->setDescription('Deal with Payzen IPN')
            ->editVerb('post', function (VerbDefinitionEntity &$entity) {
                $entity
                    ->setDescription('Process IPN');
            });
    }

    public function postAction($params)
    {
        $toolbox=new Toolbox($this->nativePMConfig);
        $control = $toolbox->checkSignature($_POST);
        if(!isset($_POST['vads_hash']) || !$control){
            return array("success"=>false,"message"=>"Auth fail");
        }
        $postParams = $toolbox->getIpn();
        Manager::getService("PaypalIPN")->create(array(
            "postData"=>$postParams,
            "source"=>"payzen",
            "verified"=>true
        ));
        if (($postParams['vads_url_check_src']=="PAY")&&($postParams['vads_result']=="00")){
            //handle successful payment
            $orderNumber=$postParams['vads_order_id'];
            $filter=Filter::factory()->addFilter(Filter::factory('Value')->setName('orderNumber')->setValue($orderNumber));
            $order=Manager::getService("Orders")->findOne($filter);
            if (!$order){
                return array(
                    "succes"=>false,
                    "message"=>"order not found"
                );
            }
            if ($order['status']=="pendingPayment"){
                    $finalPrice = ((float)$order['finalPrice']) * 100;
                    $receivedPrice = (float)$postParams['vads_amount'];
                    if (round($finalPrice, 2) != round($receivedPrice, 2)){
                        Manager::getService("PaypalIPN")->create(array(
                            "type"=>"amountError",
                            "receivedAmount"=> $receivedPrice,
                            "expectedAmount"=> $finalPrice,
                            "origin"=>"payzen"
                        ));
                        return array(
                            "succes"=>false,
                            "message"=>"amounts do not match"
                        );
                    }
                $order['status']="payed";
                $updatedOrder=Manager::getService("Orders")->update($order);
            }
        }
        return array("success"=>true);

    }
}