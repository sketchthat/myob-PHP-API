<?php
    require_once('../authorize.php');

    $currentDate = new \DateTime('NOW', new \DateTimeZone('Australia/Melbourne'));

    $json = array(
        'Date' => $currentDate->format('Y-m-d\TH:i:s'),
        'Customer' => array(
            'UID' => 'd4c1dca1-3257-4870-8e99-22eb3fac8388'
        ),
        'Lines' => array(
            array(
                'ShipQuantity' => 1,
                'Total' => 12345.65,
                'Item' => array(
                    'UID' => 'c85ef471-9630-4e2e-b8e5-79698b4d176b'
                ),
                'TaxCode' => array(
                    'UID' => 'de12fa93-362b-4f40-bb32-88293f2ea6a2'
                )
            )
        )        
    );

    $invoice = $accountRight->PostSaleInvoiceItem($json);

    echo '<pre>';
    echo '<h1>Invoice UID: '.$accountRight->getLocation().'</h1>';
    print_r($invoice);