<?php

namespace App\Classes\Api;

use App\Classes\Api\apiUtilityClass;
use DB;
use XMLWriter;

class sendShoppingCartClass
{

    private $utility = NULL;
    private $outputFile = NULL;

    function __construct() {
        require_once dirname(__DIR__, 2). "/Classes/Api/Inc/config.php";
        $this->utility = new apiUtilityClass();

        $this->outputFile = fopen(PATH_OUTPUT . 'sendShoppingCart-'. uniqid() . '.txt', "w") or die("Unable to open file!");
        $this->utility->fileWrite($this->outputFile, "B2B sendShoppingCart\n");
        $this->utility->fileWrite($this->outputFile, "Start: " . date('Y.m.d H:m:s', strtotime('now')) . "\n");

    }

    public function sendShoppingCart() {
        $datas = DB::table('shoppingcart')->where('Opened', 1)->whereNull('CustomerOrder')->whereNull('deleted_at')->get();
        if (count($datas) == 0) {
            $this->utility->fileWrite($this->outputFile, "Nincs feldolgozandó tétel!\n");
        }
        if (count($datas) > 0) {
            $this->utility->fileWrite($this->outputFile, "Kosarak: " . count($datas) . "\n");
            $xml = new XMLWriter();
            $xml->openURI(PATH_OUTPUT.'customerorder.xml');
            $xml->setIndent(true);
            $xml->setIndentString('    ');
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('customerorders');
            foreach ($datas as $data) {
                $this->utility->fileWrite($this->outputFile, "Kosár: " . $data->VoucherNumber . "\n");
                $xml->startElement('customerorder');

                $xml->writeElement('id', $data->Id);
                $xml->writeElement('vouchernumber', $data->VoucherNumber);
                $xml->writeElement('customer', $data->Customer);
                $xml->writeElement('customeraddress', $data->CustomerAddress);
                $xml->writeElement('customercontact', $data->CustomerContact);
                $xml->writeElement('voucherdate', $data->VoucherDate);
                $xml->writeElement('deliverydate', $data->DeliveryDate);
                $xml->writeElement('paymentmethod', $data->PaymentMethod);
                $xml->writeElement('currency', $data->Currency);
                $xml->writeElement('currencyrate', $data->CurrencyRate);
                $xml->writeElement('customercontract', $data->CustomerContract);
                $xml->writeElement('transportmode', $data->TransportMode);
                $xml->writeElement('depositvalue', $data->DepositValue);
                $xml->writeElement('depositpercent', $data->DepositPercent);
                $xml->writeElement('netvalue', $data->NetValue);
                $xml->writeElement('grossvalue', $data->GrossValue);
                $xml->writeElement('vatvalue', $data->VatValue);
                $xml->writeElement('comment', $data->Comment);

                $items = DB::table('shoppingcartdetail')->where('ShoppingCart', $data->Id)->whereNull('deleted_at')->get();
                if (!empty($items)) {
                    foreach ($items as $item) {
                        $xml->startElement('detail');

                        $xml->writeElement('id', $item->Id);
                        $xml->writeElement('shoppingcart', $item->ShoppingCart);
                        $xml->writeElement('currency', $item->Currency);
                        $xml->writeElement('currencyrate', $item->CurrencyRate);
                        $xml->writeElement('product', $item->Product);
                        $xml->writeElement('vat', $item->Vat);
                        $xml->writeElement('quantityunit', $item->QuantityUnit);
                        $xml->writeElement('reverse', $item->Reverse);
                        $xml->writeElement('quantity', $item->Quantity);
                        $xml->writeElement('customerofferdetail', $item->CustomerOfferDetail);
                        $xml->writeElement('customercontractdetail', $item->CustomerContractDetail);
                        $xml->writeElement('unitprice', $item->UnitPrice);
                        $xml->writeElement('discountpercent', $item->DiscountPercent);
                        $xml->writeElement('discountunitprice', $item->DiscountUnitPrice);
                        $xml->writeElement('grossprices', $item->GrossPrices);
                        $xml->writeElement('depositvalue', $item->DepositValue);
                        $xml->writeElement('depositpercent', $item->DepositPercent);
                        $xml->writeElement('netvalue', $item->NetValue);
                        $xml->writeElement('grossvalue', $item->GrossValue);
                        $xml->writeElement('vatvalue', $item->VatValue);
                        $xml->writeElement('comment', $item->Comment);
                        $xml->endElement();

                    }
                    $xml->endElement();
                }

            }
            $xml->endDocument();
            $xml->flush();
            unset($xml);

            $this->utility->fileWrite($this->outputFile, "OK\n");
        }
        $this->utility->fileWrite($this->outputFile, "End: " . date('Y.m.d H:m:s', strtotime('now')) . "\n");
        fclose($this->outputFile);
    }
}


