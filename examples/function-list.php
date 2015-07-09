<?php
    require_once('authorize.php');

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MYOB Function List</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body style="margin: 50px;">
        <h5>GUID: <?php echo $accountRight->getGuid(); ?>
        <table class="table table-striped">
            <tr>
                <th><a href="Contact/Contact.php" target="_blank">/Contact</a></th>
            </tr>
            <tr>
                <td><a href="Contact/Customer.php" target="_blank">/Contact/Customer</a></td>
            </tr>
            <tr>
                <th><a href="Company/Company.php" target="_blank">/Company</a></th>
            </tr>
            <tr>
                <th>/Sale</th>
            </tr>
            <tr>
                <td>/Sale/CustomerPayment</td>
            </tr>
            <tr>
                <td>/Sale/CustomerPayment/CalculateDiscountsFees</td>
            </tr>
            <tr>
                <td>/Sale/CustomerPayment/RecordWithDiscountsAndFees</td>
            </tr>
            <tr>
                <td>/Sale/CreditRefund</td>
            </tr>
            <tr>
                <td>/Sale/CreditSettlement</td>
            </tr>
            <tr>
                <td><a href="Sale/Invoice.php" target="_blank">/Sale/Invoice</a></td>
            </tr>
            <tr>
                <td>/Sale/Invoice/Item</td>
            </tr>
            <tr>
                <td>/Sale/Invoice/Service</td>
            </tr>
            <tr>
                <td>/Sale/Invoice/Professional</td>
            </tr>
            <tr>
                <td>/Sale/Invoice/TimeBilling</td>
            </tr>
            <tr>
                <td>/Sale/Invoice/Miscellaneous</td>
            </tr>
            <tr>
                <td>/Sale/Invoice/RenderAsPDF</td>
            </tr>
            <tr>
                <td>/Sale/Order</td>
            </tr>
            <tr>
                <td>/Sale/Order/Item</td>
            </tr>
            <tr>
                <td>/Sale/Order/Service</td>
            </tr>
            <tr>
                <td>/Sale/Order/Professional</td>
            </tr>
            <tr>
                <td>/Sale/Order/TimeBilling</td>
            </tr>
            <tr>
                <td>/Sale/Order/Miscellaneous</td>
            </tr>
            <tr>
                <td>/Sale/Order/RenderAsPDF</td>
            </tr>
        </table>
    </body>
</html>