<?php
return array(
    "paymentMeans"=>[
        'payzen' => array(
            'name' => "Payzen",
            'service' => 'PayzenPayment',
            'definitionFile' => realpath(__DIR__ . '/paymentMeans')  . '/payzen.json'
        )
    ],
    'service_manager' => array(
        'invokables' => array(
            'PayzenPayment' => 'RubedoPayzen\\Payment\\PayzenPayment',
        ),
    )
);
