<?php
    require_once('../authorize.php');

    $company = $accountRight->Company();

    echo '<pre>';
    print_r($company);