<?php
    require_once('../authorize.php');

    //$contact = $accountRight->Contact('$filter=UID+eq+guid\'5bb643a6-24b1-4f2d-8f06-fdc52d0ab228\'');
    $contact = $accountRight->Contact('$filter=DisplayID+eq+\'CUS000001\'');

    echo '<pre>';
    print_r($contact);