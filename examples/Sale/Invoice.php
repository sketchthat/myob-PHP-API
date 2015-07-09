<?php
    require_once('../authorize.php');

    $invoice = $accountRight->SaleInvoice();

    echo '<pre>';
    print_r($invoice);