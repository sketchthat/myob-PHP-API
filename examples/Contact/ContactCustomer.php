<?php
    require_once('../authorize.php');

    $json = array(
        'LastName' => 'Doe',
        'FirstName' => 'John',
        'IsIndividual' => 'True',
        'SellingDetails' => array(
            'TaxCode' => array(
                'UID' => 'de12fa93-362b-4f40-bb32-88293f2ea6a2'
            ),
            'FreightTaxCode' => array(
                'UID' => 'de12fa93-362b-4f40-bb32-88293f2ea6a2'
            )
        )
    );

    $contactCustomer = $accountRight->ContactCustomer($json);

    echo '<pre>';
    echo '<h1>Contact UID: '.$accountRight->getLocation().'</h1>';
    print_r($contactCustomer);
