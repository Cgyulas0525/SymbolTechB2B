B2B telep�t�s �s kezdeti adatfelt�lt�s.

git pull
mysql �res adatb�zis import ( ebben n�h�ny t�bl�ban van csak �rt�k �s a USERS t�bl�ban van 2 rekord, egy administrator/administrator, �s egy Cseszneki Gyula rendszergazdai joggal)

mysql -u username -p database_name < file.sql
--
composer update
programba az administrator/administrator felhasz�l�val kell bel�pni, els� l�p�s a Symbol �gyvitel al�bbi t�bl�inak strukt�r�j�r�l kellene egy XSD file, hogy az esetleges v�ltoz�sokat �t lehessen vezetni.

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

ezeknek a t�bl�knak az adattartalm�t ki kell export�lni a S�-b�l �s beimport�lni a B2B rendszerbe.

majd kil�pni �s �jra bel�pni mint administrator/administrator �s a bels� felhaszn�l� felviteli ablakon felvenni az els� felhaszn�l�t, ideiglenes jelsz�val, amit elk�d a rendszer a felhaszn�l�nak email-ba
az els� bel�p�sekor tudja v�ltoztatni a jelszav�t.

