<?php
    require_once('../authorize.php');

    $contactCustomer = $accountRight->ContactCustomer();

    echo '<pre>';
    print_r($contactCustomer);