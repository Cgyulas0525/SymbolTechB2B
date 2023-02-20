<?php
require __DIR__ . "/inc/bootstrap.php";
//require PATH_INC . "/utility.php";
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

            $xml->writeElement('id', $shoppingCart['Id']);
            $xml->writeElement('vouchernumber', $shoppingCart['VoucherNumber']);
            $xml->writeElement('customer', $shoppingCart['Customer']);
            $xml->writeElement('customeraddress', $shoppingCart['CustomerAddress']);
            $xml->writeElement('customercontact', $shoppingCart['CustomerContact']);
            $xml->writeElement('voucherdate', $shoppingCart['VoucherDate']);
            $xml->writeElement('deliverydate', $shoppingCart['DeliveryDate']);
            $xml->writeElement('paymentmethod', $shoppingCart['PaymentMethod']);
            $xml->writeElement('currency', $shoppingCart['Currency']);
            $xml->writeElement('currencyrate', $shoppingCart['CurrencyRate']);
            $xml->writeElement('customercontract', $shoppingCart['CustomerContract']);
            $xml->writeElement('transportmode', $shoppingCart['TransportMode']);
            $xml->writeElement('depositvalue', $shoppingCart['DepositValue']);
            $xml->writeElement('depositpercent', $shoppingCart['DepositPercent']);
            $xml->writeElement('netvalue', $shoppingCart['NetValue']);
            $xml->writeElement('grossvalue', $shoppingCart['GrossValue']);
            $xml->writeElement('vatvalue', $shoppingCart['VatValue']);
            $xml->writeElement('comment', $shoppingCart['Comment']);

            $sql = "SELECT * FROM shoppingcartdetail WHERE ShoppingCart = ". $shoppingCart['Id'] . " AND deleted_at IS NULL";
            $smtp = $pdo->executeStatement($sql);
            $shoppingCartDetails = $smtp->fetchAll();
            if (count($shoppingCartDetails) > 0) {
                foreach ($shoppingCartDetails as $shoppingCartDetail) {
                    $xml->startElement('detail');

                    $xml->writeElement('id', $shoppingCartDetail['Id']);
                    $xml->writeElement('shoppingcart', $shoppingCartDetail['ShoppingCart']);
                    $xml->writeElement('currency', $shoppingCartDetail['Currency']);
                    $xml->writeElement('currencyrate', $shoppingCartDetail['CurrencyRate']);
                    $xml->writeElement('product', $shoppingCartDetail['Product']);
                    $xml->writeElement('vat', $shoppingCartDetail['Vat']);
                    $xml->writeElement('quantityunit', $shoppingCartDetail['QuantityUnit']);
                    $xml->writeElement('reverse', $shoppingCartDetail['Reverse']);
                    $xml->writeElement('quantity', $shoppingCartDetail['Quantity']);
                    $xml->writeElement('customerofferdetail', $shoppingCartDetail['CustomerOfferDetail']);
                    $xml->writeElement('customercontractdetail', $shoppingCartDetail['CustomerContractDetail']);
                    $xml->writeElement('unitprice', $shoppingCartDetail['UnitPrice']);
                    $xml->writeElement('discountpercent', $shoppingCartDetail['DiscountPercent']);
                    $xml->writeElement('discountunitprice', $shoppingCartDetail['DiscountUnitPrice']);
                    $xml->writeElement('grossprices', $shoppingCartDetail['GrossPrices']);
                    $xml->writeElement('depositvalue', $shoppingCartDetail['DepositValue']);
                    $xml->writeElement('depositpercent', $shoppingCartDetail['DepositPercent']);
                    $xml->writeElement('netvalue', $shoppingCartDetail['NetValue']);
                    $xml->writeElement('grossvalue', $shoppingCartDetail['GrossValue']);
                    $xml->writeElement('vatvalue', $shoppingCartDetail['VatValue']);
                    $xml->writeElement('comment', $shoppingCartDetail['Comment']);
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

