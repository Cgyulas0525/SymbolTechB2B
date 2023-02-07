B2B telepítés és kezdeti adatfeltöltés.

git pull
mysql üres adatbázis import ( ebben néhány táblában van csak érték és a USERS táblában van 2 rekord, egy administrator/administrator, és egy cseszneki Gyula)
composer update
programba az administrator/administrator felhaszálóval kell belépni, elsõ lépés a Symbol Ügyvitel alábbi tábláinak struktúrájáról kellene egy XSD file, hogy az esetleges változásokat át lehessen vezetni.

"Currency",
"CurrencyRate",
"Customer",
"CustomerAddress",
"CustomerCategory",
"CustomerContact",
"CustomerOffer",
"CustomerOfferCustomer",
"CustomerOfferDetail",
"CustomerOrder",
"CustomerOrderStatus",
"CustomerOrderDetail",
"CustomerOrderDetailStatus",
"Employee"
"PaymentMethod",
"PaymentMethodLang",
"PriceCategory",
"Product"
"ProductAssociation",
"ProductAssociationType",
"ProductAttributes",
"ProductAttribute",
"ProductAttributeLang",
"ProductCategory",
"ProductCustomerCode",
"ProductCustomerDiscount",
"ProductLang",
"ProductPrice",
"QuantityUnit",
"QuantityUnitLang",
"SystemSetting",
"SystemSettingValue",
"TransportMode",
"TransportModeLang",
"Vat",
"Warehouse",
"WarehouseBalance",
"WarehouseDailyBalance"

ezeknek a tábláknak az adattartalmát ki kell exportálni a SÜ-bõl és beimportálni a B2B rendszerbe.

majd kilépni és újra belépni mint administrator/administrator és a belsõ felhasználó felviteli ablakon felvenni az elsõ felhasználót, ideiglenes jelszóval, amit elküd a rendszer a felhasználónak email-ba
az elsõ belépésekor tudja változtatni a jelszavát.

