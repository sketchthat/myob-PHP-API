<?php
    require_once('../authorize.php');

    $currentDate = new \DateTime('NOW');

    $json = array(
        'UID' => '',
        'Customer' => array(
            'UID'
        )
    );

    $method = AccountRightV2::POST;
    $uid = '';
    $date = $currentDate->format('Y-m-d H:i:s');

    $customer => array(
        'UID' => $customerId
    );

    $lines = 


    SaleInvoiceItem($method = self::POST, $json);


    $invoice = $accountRight->SaleInvoiceItem($method, $guid, );

    echo '<pre>';
    print_r($invoice);