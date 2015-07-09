<?php
    require_once('../authorize.php');

    $contact = $accountRight->Contact();

    echo '<pre>';
    print_r($contact);