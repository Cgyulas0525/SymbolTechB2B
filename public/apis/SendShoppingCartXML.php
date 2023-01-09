<?php
require __DIR__ . "/inc/bootstrap.php";
require PATH_INC . "/utility.php";
require PATH_INC . "/ModelChange.php";
require PATH_MODEL . "/mySQLDatabase.php";

$pdo = new mySQLDatabase();
$utility = new Utility();
$modelChange = new ModelChange();

$sql = "SELECT * FROM shoppingcart WHERE Opened = 1 AND CustomerOrder IS NULL AND deleted_at IS NULL";
$smtp = $pdo->executeStatement($sql);

if ($smtp) {
    $shoppingCarts = $smtp->fetchAll();
    if (count($shoppingCarts) > 0) {
        $xml = new XMLWriter();
        $xml->openURI(PATH_OUTPUT.'customerorder.xml');
        $xml->setIndent(true);
        $xml->setIndentString('    ');
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('customerorders');
        foreach ($shoppingCarts as $shoppingCart) {
            echo $shoppingCart['VoucherNumber'] . "\n";
            $xml->startElement('customerorder');
            $xml->writeElement('date', $shoppingCart['VoucherDate']);
            $xml->writeElement('expirationdays', '');
            $xml->writeElement('orderid', $shoppingCart['Id']);
            $xml->writeElement('customer', '');
            $xml->writeElement('customerid', $shoppingCart['Customer']);
            $xml->writeElement('customercode', '');
            $xml->writeElement('customeremail', '');
            $xml->writeElement('country', '');
            $xml->writeElement('region', '');
            $xml->writeElement('zip', '');
            $xml->writeElement('city', '');
            $xml->writeElement('street', '');
            $xml->writeElement('housenumber', '');
            $xml->writeElement('transportid', '');
            $xml->writeElement('transportname', '');
            $xml->writeElement('transportcountry', '');
            $xml->writeElement('transportregion', '');
            $xml->writeElement('transportzip', '');
            $xml->writeElement('transportcity', '');
            $xml->writeElement('transportstreet', '');
            $xml->writeElement('transporthousenumber', '');
            $xml->writeElement('transportcontactname', '');
            $xml->writeElement('currency', '');
		    $xml->writeElement('currencyrate', '');
		    $xml->writeElement('transportmode', '');
            $xml->writeElement('transporttargetid', '');
            $xml->writeElement('transportdate', '');
            $xml->writeElement('vouchersequencecode', '');
            $xml->writeElement('paymentmethod', '');
            $xml->writeElement('paymentmethodtolerance', '');
            $xml->writeElement('warehouse', '');
		    $xml->writeElement('notifyphone', '');
            $xml->writeElement('notifysms', '');
            $xml->writeElement('notifyemail', '');
            $xml->writeElement('splitforbid', '');
            $xml->writeElement('banktrid', '');
            $xml->writeElement('comment', '');
            $xml->writeElement('closedmanually', '');
            $xml->writeElement('strexa', '');
            $xml->writeElement('strexb', '');
            $xml->writeElement('strexc', '');
            $xml->writeElement('strexd', '');
            $xml->writeElement('dateexa', '');
            $xml->writeElement('dateexb', '');
            $xml->writeElement('dateexc', '');
            $xml->writeElement('dateexd', '');
            $xml->writeElement('numexa', '');
            $xml->writeElement('numexb', '');
            $xml->writeElement('numexc', '');
            $xml->writeElement('numexd', '');
            $xml->writeElement('boolexa', '');
            $xml->writeElement('boolexb', '');
            $xml->writeElement('boolexc', '');
            $xml->writeElement('boolexd', '');
            $xml->writeElement('lookupexa', '');
            $xml->writeElement('lookupexb', '');
            $xml->writeElement('lookupexc', '');
            $xml->writeElement('lookupexd', '');
            $xml->writeElement('feedbackurl', '');
 		    $xml->writeElement('errorurl', '');

            $sql = "SELECT * FROM shoppingcartdetail WHERE ShoppingCart = ". $shoppingCart['Id'] . " AND deleted_at IS NULL";
            $smtp = $pdo->executeStatement($sql);
            $shoppingCartDetails = $smtp->fetchAll();
            if (count($shoppingCartDetails) > 0) {
                foreach ($shoppingCartDetails as $shoppingCartDetail) {
                    $xml->startElement('deatil');
                        $xml->writeElement('productid', $shoppingCartDetail['Product']);
                        $xml->writeElement('productname', '');
                        $xml->writeElement('quantity', '');
                        $xml->writeElement('vat', '');
                        $xml->writeElement('vatname', '');
                        $xml->writeElement('unipricenet', '');
                        $xml->writeElement('uniprice', '');
                        $xml->writeElement('netvalue', '');
                        $xml->writeElement('grossvalue', '');
                        $xml->writeElement('discountpercent', '');
                        $xml->writeElement('mustmanufacturing', '');
                        $xml->writeElement('allocate', '');
                        $xml->writeElement('detailstatus', '');
                        $xml->writeElement('comment', '');
                        $xml->writeElement('strexa', '');
                        $xml->writeElement('strexb', '');
                        $xml->writeElement('strexc', '');
                        $xml->writeElement('strexd', '');
                        $xml->writeElement('dateexa', '');
                        $xml->writeElement('dateexb', '');
                        $xml->writeElement('dateexc', '');
                        $xml->writeElement('dateexd', '');
                        $xml->writeElement('numexa', '');
                        $xml->writeElement('numexb', '');
                        $xml->writeElement('numexc', '');
                        $xml->writeElement('numexd', '');
                        $xml->writeElement('boolexa', '');
                        $xml->writeElement('boolexb', '');
                        $xml->writeElement('boolexc', '');
                        $xml->writeElement('boolexd', '');
                        $xml->writeElement('lookupexa', '');
                        $xml->writeElement('lookupexb', '');
                        $xml->writeElement('lookupexc', '');
                        $xml->writeElement('lookupexd', '');
                    $xml->endElement();
                }
            }
            $xml->endElement();
        }
        $xml->endDocument();
        $xml->flush();
        unset($xml);
    }
} else {
    echo "Nincs új kosár!";
}

