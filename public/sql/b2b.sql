-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- G�p: 127.0.0.1:3306
-- L�trehoz�s ideje: 2023. Feb 07. 12:29
-- Kiszolg�l� verzi�ja: 8.0.27
-- PHP verzi�: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatb�zis: `b2b`
--

DELIMITER $$
--
-- F�ggv�nyek
--
DROP FUNCTION IF EXISTS `discountPercentage`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `discountPercentage` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS INT DETERMINISTIC BEGIN
	DECLARE mPercent INT;
	DECLARE mLastPrice decimal(18,4);
	DECLARE mProductPrice decimal(18,4);

    /* Az utols� v�gfelhaszn�l�i �r */
    SET mLastPrice = getLastProductPrice($customer, $product, $quantityUnit, $currency);
    /* A kedvezm�nyes �r */
    SET mProductPrice = getProductPrice($customer, $product, $quantity, $quantityUnit, $currency);
    /* A sz�zal�k */
    IF mLastPrice > 0 THEN
		SET mPercent = ROUND((100 - (mProductPrice / (mLastPrice / 100))), 0);
		IF mPercent IS NULL THEN
			RETURN 0;
		END IF;
	ELSE
		RETURN 0;
	END IF;
	RETURN mPercent;
END$$

DROP FUNCTION IF EXISTS `getContractPrice`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getContractPrice` (`$customer` INT, `$product` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
	DECLARE mPrice DECIMAL(18,4);

    SELECT t2.Price INTO mPrice
      FROM CustomerContract as t1, CustomerContractDetail as t2
	 WHERE t2.CustomerContract = t1.Id AND
           t2.ValidFrom <= now() and
           ( t2.ValidTo >= now() OR t2.ValidTo IS NULL) AND
           t1.Customer = $customer AND
           t2.Product = $product AND
           t2.QuantityUnit = $quantityUnit AND
           t2.Currency = $currency;

	IF mPrice IS NOT NULL THEN
		RETURN mPrice;
	ELSE
		RETURN 0;
	END IF;
END$$

DROP FUNCTION IF EXISTS `getLastProductPrice`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getLastProductPrice` (`$customer` INT, `$product` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
	DECLARE mPrice DECIMAL(18,4);

    SELECT Price INTO mPrice FROM ProductPrice
     WHERE Product = $product AND
		   PriceCategory = (SELECT PriceCategory FROM Customer WHERE Id = $customer LIMIT 1) AND
           Currency = $currency AND
           QuantityUnit = $quantityUnit AND
           ValidFrom <= now()
	ORDER BY ValidFrom DESC
    LIMIT 1;

    IF mPrice IS NOT NULL THEN
		RETURN mPrice;
	ELSE
		RETURN 0;
    END IF;
END$$

DROP FUNCTION IF EXISTS `getLastProductPriceId`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getLastProductPriceId` (`$Product` INT, `$QuantityUnit` INT, `$PriceCategory` INT, `$Currency` INT) RETURNS INT DETERMINISTIC BEGIN
	DECLARE mId INT;

    SELECT MAX(t5.Id) INTO mId FROM ProductPrice AS t5
	 WHERE t5.QuantityUnit = $QuantityUnit
	   AND t5.PriceCategory = $PriceCategory
	   AND t5.Currency = $Currency
	   AND t5.Product = $Product
	GROUP BY t5.Product, t5.QuantityUnit, t5.Currency, t5.PriceCategory;

	RETURN mId;
END$$

DROP FUNCTION IF EXISTS `getOfferPrice`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getOfferPrice` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
	declare mPrice decimal(18,4);

    SELECT t1.SalesPrice INTO mPrice FROM CustomerOfferDetail as t1, CustomerOffer as t2, CustomerOfferCustomer as t3
     WHERE t2.Id = t1.CustomerOffer AND
		   t3.CustomerOffer = t2.Id AND
           t1.Product = $product AND
           t2.ValidFrom <= now() AND
           t2.ValidTo >= now() AND
           t3.Forbid = 0 AND
           t1.Currency = $currency AND
           t1.QuantityUnit = $quantityUnit AND
           (t3.Customer = $customer OR t3.CustomerCategory = (SELECT t4.CustomerCategory FROM Customer AS t4 WHERE t4.Id = $customer LIMIT 1)) AND
           ((t1.QuantityMinimum <= 1) OR t1.QuantityMinimum IS NULL)
	ORDER BY t1.QuantityMinimum DESC, t1.QuantityMaximum DESC
    LIMIT 1;

    IF mPrice IS NOT NULL THEN
		RETURN mPrice;
    ELSE
        RETURN 0;
	END IF;
END$$

DROP FUNCTION IF EXISTS `getProductCustomerCode`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getProductCustomerCode` (`$customer` INT, `$product` INT) RETURNS VARCHAR(40) CHARSET utf32 DETERMINISTIC BEGIN
	DECLARE mCode VARCHAR(40);

    SELECT Code INTO mCode FROM productcustomercode WHERE Product = $product AND Customer = $customer;
    IF mCode IS NULL THEN
		SET mCode = "";
    END IF;
	RETURN mCode;
END$$

DROP FUNCTION IF EXISTS `getProductPrice`$$
CREATE DEFINER=`b2b`@`localhost` FUNCTION `getProductPrice` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
	DECLARE mPrice decimal(18,4);
	DECLARE mOfferPrice decimal(18,4);
	DECLARE mLastPrice decimal(18,4);
	DECLARE mContractPrice decimal(18,4);

    /* Az utols� v�gfelhaszn�l�i �r */
    SET mLastPrice = getLastProductPrice($customer, $product, $quantityUnit, $currency);
    IF mLastPrice > 0 THEN
		SET mPrice = mLastPrice;
    END IF;

    /* Az akci�s �r */
    SET mOfferPrice = getOfferPrice($customer, $product, $quantity, $quantityUnit, $currency);
    IF mOfferPrice != 0 THEN
		IF mOfferPrice < mPrice THEN
			SET mPrice = mOfferPrice;
        END IF;
    END IF;

    /* A szerz�d�ses �r */
    SET mContractPrice = getContractPrice($customer, $product, $quantityUnit, $currency);
    IF mContractPrice != 0 THEN
		IF mContractPrice < mPrice THEN
			SET mPrice = mContractPrice;
        END IF;
    END IF;

    IF mPrice IS NULL THEN
        SET mPrice = 0;
    END IF;
	RETURN mPrice;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `api`
--

DROP TABLE IF EXISTS `api`;
CREATE TABLE IF NOT EXISTS `api` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `apimodel`
--

DROP TABLE IF EXISTS `apimodel`;
CREATE TABLE IF NOT EXISTS `apimodel` (
  `id` int NOT NULL AUTO_INCREMENT,
  `api_id` int NOT NULL,
  `model` varchar(100) NOT NULL,
  `recordnumber` int NOT NULL DEFAULT '0',
  `insertednumber` int NOT NULL DEFAULT '0',
  `updatednumber` int NOT NULL DEFAULT '0',
  `errornumber` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `apimodel_api_id_id_uindex` (`api_id`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=402 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `apimodelerror`
--

DROP TABLE IF EXISTS `apimodelerror`;
CREATE TABLE IF NOT EXISTS `apimodelerror` (
  `id` int NOT NULL AUTO_INCREMENT,
  `apimodel_id` int NOT NULL,
  `smtp` varchar(2000) NOT NULL,
  `error` varchar(2000) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `apimodelerror_apimodel_id_id_uindex` (`apimodel_id`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1953 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `Id` bigint NOT NULL,
  `Name` varchar(8) NOT NULL,
  `Sign` varchar(4) NOT NULL,
  `RoundDigits` int NOT NULL DEFAULT '0',
  `DetailRoundDigits` int NOT NULL DEFAULT '0',
  `GrossRound` smallint NOT NULL DEFAULT '0',
  `Denomination` blob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_Currency_Name` (`Name`),
  KEY `IRC_Currency` (`RowCreate`),
  KEY `IRM_Currency` (`RowModify`),
  KEY `currency_Id_index` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `currencyrate`
--

DROP TABLE IF EXISTS `currencyrate`;
CREATE TABLE IF NOT EXISTS `currencyrate` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `Currency` bigint NOT NULL,
  `ValidFrom` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Rate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `RateBuy` decimal(18,4) DEFAULT NULL,
  `RateSell` decimal(18,4) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_CurrencyRate` (`Currency`,`ValidFrom`),
  KEY `IRC_CurrencyRate` (`RowCreate`),
  KEY `IRM_CurrencyRate` (`RowModify`)
) ENGINE=InnoDB AUTO_INCREMENT=9223 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customer`
--

DROP TABLE IF EXISTS `customer`;
CREATE TABLE IF NOT EXISTS `customer` (
  `Id` bigint NOT NULL,
  `Code` varchar(40) NOT NULL,
  `CustomerStatus` smallint NOT NULL DEFAULT '0',
  `SupplierStatus` smallint NOT NULL DEFAULT '0',
  `Name` varchar(100) NOT NULL,
  `SearchName` varchar(100) DEFAULT NULL,
  `CreateDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CustomerCategory` bigint DEFAULT NULL,
  `SupplierCategory` bigint DEFAULT NULL,
  `DisplayCountry` smallint NOT NULL DEFAULT '0',
  `InvoiceCountry` varchar(100) DEFAULT NULL,
  `InvoiceRegion` varchar(100) DEFAULT NULL,
  `InvoiceZip` varchar(10) DEFAULT NULL,
  `InvoiceCity` varchar(100) DEFAULT NULL,
  `InvoiceStreet` varchar(100) DEFAULT NULL,
  `InvoiceHouseNumber` varchar(20) DEFAULT NULL,
  `MailBanned` smallint NOT NULL DEFAULT '0',
  `MailCountry` varchar(100) DEFAULT NULL,
  `MailRegion` varchar(100) DEFAULT NULL,
  `MailName` varchar(100) DEFAULT NULL,
  `MailOriginalName` smallint NOT NULL DEFAULT '0',
  `MailZip` varchar(10) DEFAULT NULL,
  `MailCity` varchar(100) DEFAULT NULL,
  `MailStreet` varchar(100) DEFAULT NULL,
  `MailHouseNumber` varchar(20) DEFAULT NULL,
  `PaymentMethod` bigint DEFAULT NULL,
  `PaymentMethodStrict` smallint NOT NULL DEFAULT '0',
  `PaymentMethodToleranceDay` int DEFAULT NULL,
  `PriceCategory` bigint DEFAULT NULL,
  `CustomerIstatTemplate` bigint DEFAULT NULL,
  `SupplierIstatTemplate` bigint DEFAULT NULL,
  `Currency` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `TradeRegNumber` varchar(20) DEFAULT NULL,
  `TaxNumber` varchar(20) DEFAULT NULL,
  `EUTaxNumber` varchar(20) DEFAULT NULL,
  `GroupTaxNUmber` varchar(20) DEFAULT NULL,
  `EUMembership` int NOT NULL DEFAULT '0',
  `BankAccount` varchar(100) DEFAULT NULL,
  `BankAccountIBAN` varchar(100) DEFAULT NULL,
  `ContactName` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Sms` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `RobinsonMode` smallint NOT NULL DEFAULT '0',
  `AllowEmailVouchers` smallint NOT NULL DEFAULT '0',
  `SpecVoucherEmails` smallint NOT NULL DEFAULT '0',
  `WebUsername` varchar(100) DEFAULT NULL,
  `WebPassword` varchar(100) DEFAULT NULL,
  `DeliveryInfo` blob,
  `Comment` blob,
  `VoucherRules` blob,
  `DiscountPercent` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `DebitQuota` blob,
  `EInvoice` blob,
  `BuyCompanyCode` varchar(40) DEFAULT NULL,
  `SellCompanyCode` varchar(40) DEFAULT NULL,
  `Agent` bigint DEFAULT NULL,
  `AgentStrict` smallint NOT NULL DEFAULT '0',
  `StrExA` varchar(100) DEFAULT NULL,
  `StrExB` varchar(100) DEFAULT NULL,
  `StrExC` varchar(100) DEFAULT NULL,
  `StrExD` varchar(100) DEFAULT NULL,
  `DateExA` timestamp NULL DEFAULT NULL,
  `DateExB` timestamp NULL DEFAULT NULL,
  `NumExA` decimal(18,4) DEFAULT NULL,
  `NumExB` decimal(18,4) DEFAULT NULL,
  `NumExC` decimal(18,4) DEFAULT NULL,
  `BoolExA` smallint NOT NULL DEFAULT '0',
  `BoolExB` smallint NOT NULL DEFAULT '0',
  `LookupExA` bigint DEFAULT NULL,
  `LookupExB` bigint DEFAULT NULL,
  `LookupExC` bigint DEFAULT NULL,
  `LookupExD` bigint DEFAULT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DeliveryCDay` int DEFAULT NULL,
  `DeliverySDay` int DEFAULT NULL,
  `SelfSupplierInvoice` smallint NOT NULL DEFAULT '0',
  `Url` varchar(100) DEFAULT NULL,
  `MemoExA` blob,
  `MemoExB` blob,
  `DateExC` timestamp NULL DEFAULT NULL,
  `DateExD` timestamp NULL DEFAULT NULL,
  `NumExD` decimal(18,4) DEFAULT NULL,
  `BoolExC` smallint NOT NULL DEFAULT '0',
  `BoolExD` smallint NOT NULL DEFAULT '0',
  `MemoExC` blob,
  `MemoExD` blob,
  `SupplierDebitQuota` blob,
  `DebitQIgnoreOnce` smallint NOT NULL DEFAULT '0',
  `BankName` varchar(100) DEFAULT NULL,
  `BankSwiftCode` varchar(100) DEFAULT NULL,
  `SupplierDiscountPercent` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `IsCompany` smallint NOT NULL DEFAULT '0',
  `InvoiceTownship` varchar(100) DEFAULT NULL,
  `MailTownship` varchar(100) DEFAULT NULL,
  `GLN` varchar(40) DEFAULT NULL,
  `PaymentMethodLimitSkip` smallint NOT NULL DEFAULT '0',
  `SupplierPaymentMethod` bigint DEFAULT NULL,
  `SupplierPMStrict` smallint NOT NULL DEFAULT '0',
  `SupplierPMToleranceDay` int DEFAULT NULL,
  `NAVOnlineInvoiceUsername` varchar(100) DEFAULT NULL,
  `NAVOnlineInvoicePassword` varchar(100) DEFAULT NULL,
  `NAVOnlineInvoiceSignature` varchar(100) DEFAULT NULL,
  `NAVOnlineInvoiceDecode` varchar(100) DEFAULT NULL,
  `NAVOnlineInvoiceInactive` smallint NOT NULL DEFAULT '0',
  `InvoiceCustomer` bigint DEFAULT NULL,
  `BuyLimit` decimal(18,4) DEFAULT NULL,
  `ParcelInfo` bigint DEFAULT NULL,
  `DiscountPercentDateTime` timestamp NULL DEFAULT NULL,
  `StrExE` varchar(100) DEFAULT NULL,
  `StrExF` varchar(100) DEFAULT NULL,
  `StrExG` varchar(100) DEFAULT NULL,
  `StrExH` varchar(100) DEFAULT NULL,
  `StrExI` varchar(100) DEFAULT NULL,
  `StrExJ` varchar(100) DEFAULT NULL,
  `DateExE` timestamp NULL DEFAULT NULL,
  `DateExF` timestamp NULL DEFAULT NULL,
  `DateExG` timestamp NULL DEFAULT NULL,
  `DateExH` timestamp NULL DEFAULT NULL,
  `DateExI` timestamp NULL DEFAULT NULL,
  `DateExJ` timestamp NULL DEFAULT NULL,
  `NumExE` decimal(18,4) DEFAULT NULL,
  `NumExF` decimal(18,4) DEFAULT NULL,
  `NumExG` decimal(18,4) DEFAULT NULL,
  `NumExH` decimal(18,4) DEFAULT NULL,
  `NumExI` decimal(18,4) DEFAULT NULL,
  `NumExJ` decimal(18,4) DEFAULT NULL,
  `BoolExE` smallint DEFAULT NULL,
  `BoolExF` smallint DEFAULT NULL,
  `BoolExG` smallint DEFAULT NULL,
  `BoolExH` smallint DEFAULT NULL,
  `BoolExI` smallint DEFAULT NULL,
  `BoolExJ` smallint DEFAULT NULL,
  `LookupExE` bigint DEFAULT NULL,
  `LookupExF` bigint DEFAULT NULL,
  `LookupExG` bigint DEFAULT NULL,
  `LookupExH` bigint DEFAULT NULL,
  `LookupExI` bigint DEFAULT NULL,
  `LookupExJ` bigint DEFAULT NULL,
  `MemoExE` blob,
  `MemoExF` blob,
  `MemoExG` blob,
  `MemoExH` blob,
  `MemoExI` blob,
  `MemoExJ` blob,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `customer_Id_uindex` (`Id`),
  KEY `FK_Customer_InvoiceCustomer` (`InvoiceCustomer`),
  KEY `FK_Customer_SupplierCategory` (`SupplierCategory`),
  KEY `FK_Customer_PaymentMethod` (`PaymentMethod`),
  KEY `FK_Customer_PriceCategory` (`PriceCategory`),
  KEY `FK_Customer_TransportMode` (`TransportMode`),
  KEY `FK_Customer_Agent` (`Agent`),
  KEY `FK_Customer_ExA` (`LookupExA`),
  KEY `FK_Customer_ExB` (`LookupExB`),
  KEY `FK_Customer_ExC` (`LookupExC`),
  KEY `FK_Customer_ExD` (`LookupExD`),
  KEY `FK_Customer_SupplierPM` (`SupplierPaymentMethod`),
  KEY `IDX_Customer_Name` (`Name`) USING BTREE,
  KEY `IDX_Customer_SearchName` (`SearchName`) USING BTREE,
  KEY `IDX_Customer_BankName` (`BankName`),
  KEY `IDX_Customer_BankSwiftCode` (`BankSwiftCode`),
  KEY `IDX_Customer_BuyLimit` (`BuyLimit`),
  KEY `IDX_Customer_Email` (`Email`),
  KEY `IDX_Customer_WebUsername` (`WebUsername`),
  KEY `IRC_Customer` (`RowCreate`),
  KEY `IRM_Customer` (`RowModify`),
  KEY `UNQ_Customer_Code` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customeraddress`
--

DROP TABLE IF EXISTS `customeraddress`;
CREATE TABLE IF NOT EXISTS `customeraddress` (
  `Id` bigint NOT NULL,
  `Customer` bigint NOT NULL,
  `Preferred` smallint NOT NULL DEFAULT '0',
  `Code` varchar(40) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `DisplayCountry` smallint NOT NULL DEFAULT '0',
  `Country` varchar(100) DEFAULT NULL,
  `Region` varchar(100) DEFAULT NULL,
  `Zip` varchar(10) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Street` varchar(100) DEFAULT NULL,
  `HouseNumber` varchar(20) DEFAULT NULL,
  `ContactName` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `IsCompany` smallint NOT NULL DEFAULT '0',
  `CompanyTaxNumber` varchar(20) DEFAULT NULL,
  `DeliveryInfo` blob,
  `Comment` blob,
  `VoucherComment` blob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PaymentMethod` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `DeliveryCDay` int DEFAULT NULL,
  `Agent` bigint DEFAULT NULL,
  `AgentStrict` smallint NOT NULL DEFAULT '0',
  `Sms` varchar(20) DEFAULT NULL,
  `CompanyEUTaxNumber` varchar(20) DEFAULT NULL,
  `CompanyGroupTaxNumber` varchar(20) DEFAULT NULL,
  `CompanyTradeRegNumber` varchar(20) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `Township` varchar(100) DEFAULT NULL,
  `GLN` varchar(40) DEFAULT NULL,
  `IsPerson` smallint NOT NULL DEFAULT '0',
  `ParcelInfo` bigint DEFAULT NULL,
  `EUMembership` int DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CustomerAddress_Customer` (`Customer`),
  KEY `FK_CustomerAddress_Agent` (`Agent`),
  KEY `FK_CustomerAddress_PaymentMet` (`PaymentMethod`),
  KEY `FK_CustomerAddress_TransportM` (`TransportMode`),
  KEY `IDX_CustomerAddress_Code` (`Code`),
  KEY `IDX_CustomerAddress_Email` (`Email`),
  KEY `IDX_CustomerAddress_Name` (`Name`),
  KEY `IRC_CustomerAddress` (`RowCreate`),
  KEY `IRM_CustomerAddress` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customercategory`
--

DROP TABLE IF EXISTS `customercategory`;
CREATE TABLE IF NOT EXISTS `customercategory` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Parent` bigint DEFAULT NULL,
  `LeftValue` bigint NOT NULL DEFAULT '0',
  `RightValue` bigint NOT NULL DEFAULT '0',
  `DiscountPercent` decimal(18,4) DEFAULT NULL,
  `PaymentMethod` bigint DEFAULT NULL,
  `PaymentMethodStrict` smallint NOT NULL DEFAULT '0',
  `PriceCategory` bigint DEFAULT NULL,
  `Currency` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `VoucherRules` blob,
  `DebitQuota` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `IsCompany` smallint DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CustomerCategory_Parent` (`Parent`),
  KEY `FK_CustomerCategory_Curr` (`Currency`),
  KEY `FK_CustomerCategory_PM` (`PaymentMethod`),
  KEY `FK_CustomerCategory_PC` (`PriceCategory`),
  KEY `FK_CustomerCategory_TrMode` (`TransportMode`),
  KEY `IDX_CustomerCategory_Name` (`Name`),
  KEY `IDX_CustomerCategory_Value` (`LeftValue`,`RightValue`),
  KEY `IRC_CustomerCategory` (`RowCreate`),
  KEY `IRM_CustomerCategory` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customercontact`
--

DROP TABLE IF EXISTS `customercontact`;
CREATE TABLE IF NOT EXISTS `customercontact` (
  `Id` bigint NOT NULL,
  `Customer` bigint NOT NULL,
  `CustomerAddress` bigint DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Theeing` smallint NOT NULL DEFAULT '0',
  `Responsibility` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Sms` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Url` varchar(100) DEFAULT NULL,
  `Skype` varchar(10) DEFAULT NULL,
  `FacebookUrl` varchar(100) DEFAULT NULL,
  `Msn` varchar(100) DEFAULT NULL,
  `Comment` blob,
  `VoucherComment` blob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CustomerContact_Customer` (`Customer`),
  KEY `FK_CustomerContact_CustAddress` (`CustomerAddress`),
  KEY `IDX_CustomerContact_Email` (`Email`),
  KEY `IDX_CustomerContact_Name` (`Name`),
  KEY `IRC_CustomerContact` (`RowCreate`),
  KEY `IRM_CustomerContact` (`RowModify`),
  KEY `customercontact_Id_index` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customercontactfavoriteproduct`
--

DROP TABLE IF EXISTS `customercontactfavoriteproduct`;
CREATE TABLE IF NOT EXISTS `customercontactfavoriteproduct` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customercontact_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ccfp_cc_id_p_id_index` (`customercontact_id`,`product_id`),
  KEY `ccfp_p_id_cct_id_index` (`product_id`,`customercontact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customercontract`
--

DROP TABLE IF EXISTS `customercontract`;
CREATE TABLE IF NOT EXISTS `customercontract` (
  `Id` bigint NOT NULL,
  `VoucherSequence` bigint NOT NULL,
  `VoucherNumber` varchar(100) NOT NULL,
  `PrimeVoucherNumber` varchar(100) DEFAULT NULL,
  `Customer` bigint NOT NULL,
  `AddressDepends` smallint NOT NULL DEFAULT '0',
  `CustomerAddress` bigint DEFAULT NULL,
  `Subject` varchar(100) DEFAULT NULL,
  `ValidFrom` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ValidTo` timestamp NULL DEFAULT NULL,
  `InvoiceOccurence` longblob,
  `AlertGenerated` timestamp NULL DEFAULT NULL,
  `PaymentMethod` bigint DEFAULT NULL,
  `SuppressPriceAffect` smallint NOT NULL DEFAULT '0',
  `OfferOverride` smallint NOT NULL DEFAULT '0',
  `ManualAdapt` smallint NOT NULL DEFAULT '0',
  `Comment` longblob,
  `CopyCommentToInvoice` smallint NOT NULL DEFAULT '0',
  `Cancelled` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `InvoiceModeSeason` smallint NOT NULL DEFAULT '0',
  `Investment` bigint DEFAULT NULL,
  UNIQUE KEY `PK_CustomerContract` (`Id`),
  KEY `IDX_CustomerContract_Prime` (`PrimeVoucherNumber`),
  KEY `IDX_CustomerContract_Valids` (`ValidFrom`,`ValidTo`),
  KEY `UNQ_CustomerContract_VoucherNum` (`VoucherNumber`),
  KEY `FK_CustomerContract_Sequence` (`VoucherSequence`),
  KEY `FK_CustomerContract_Customer` (`Customer`),
  KEY `FK_CustomerContract_CAddress` (`CustomerAddress`),
  KEY `FK_CustomerContract_PaymentMet` (`PaymentMethod`),
  KEY `FK_CustomerContract_Inv` (`Investment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customercontractdetail`
--

DROP TABLE IF EXISTS `customercontractdetail`;
CREATE TABLE IF NOT EXISTS `customercontractdetail` (
  `Id` bigint NOT NULL,
  `CustomerContract` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `ShareQuantity` decimal(18,4) DEFAULT NULL,
  `Price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Currency` bigint NOT NULL,
  `QuantityUnit` bigint DEFAULT NULL,
  `InvoiceQty` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Vat` bigint NOT NULL,
  `ValidFrom` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ValidTo` timestamp NULL DEFAULT NULL,
  `InvoiceOccurence` longblob,
  `SuppressPriceAffect` smallint NOT NULL DEFAULT '0',
  `OfferOverride` smallint NOT NULL DEFAULT '0',
  `Comment` longblob,
  `CopyCommentToInvoice` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowOrder` int NOT NULL DEFAULT '0',
  `Investment` bigint DEFAULT NULL,
  UNIQUE KEY `PK_CustomerContractDetail` (`Id`),
  KEY `IDX_CustomerContractDetail_InvQ` (`InvoiceQty`),
  KEY `IDX_CustomerContractDetail_PCQ` (`ValidFrom`,`ValidTo`),
  KEY `IDX_CustomerContractDetail_SQ` (`ShareQuantity`),
  KEY `FK_CustomerContractDetail_Inv` (`Investment`),
  KEY `FK_CustomerContractDetail_CustC` (`CustomerContract`),
  KEY `FK_CustomerContractDetail_Prod` (`Product`),
  KEY `FK_CustomerContractDetail_Curr` (`Currency`),
  KEY `FK_CustomerContractDetail_QUnit` (`QuantityUnit`),
  KEY `FK_CustomerContractDetail_Vat` (`Vat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customeroffer`
--

DROP TABLE IF EXISTS `customeroffer`;
CREATE TABLE IF NOT EXISTS `customeroffer` (
  `Id` bigint NOT NULL,
  `VoucherSequence` bigint NOT NULL,
  `VoucherNumber` varchar(100) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `ValidFrom` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ValidTo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `OrderDlvFrom` smallint NOT NULL DEFAULT '0',
  `OrderDlvTo` smallint NOT NULL DEFAULT '0',
  `Campaign` bigint DEFAULT NULL,
  `CustomerDepend` int NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`),
  KEY `IDX_CustomerOffer_Name` (`Name`),
  KEY `IDX_CustomerOffer_Valids` (`ValidFrom`,`ValidTo`),
  KEY `IDX_CustomerOffer_VoucherNumber` (`VoucherNumber`),
  KEY `IDX_CustomerOffer_VoucherSequenc` (`VoucherSequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customeroffercustomer`
--

DROP TABLE IF EXISTS `customeroffercustomer`;
CREATE TABLE IF NOT EXISTS `customeroffercustomer` (
  `Id` bigint NOT NULL,
  `CustomerOffer` bigint NOT NULL,
  `Customer` bigint DEFAULT NULL,
  `CustomerCategory` bigint DEFAULT NULL,
  `Forbid` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `IDX_CustomerOfferCustomer_1` (`CustomerCategory`),
  KEY `IDX_CustomerOfferCustomer_Cust` (`Customer`),
  KEY `IDX_CustomerOfferCustomer_Offer` (`CustomerOffer`),
  KEY `IDX_CustomerOfferCustomer_Customer` (`Customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customerofferdetail`
--

DROP TABLE IF EXISTS `customerofferdetail`;
CREATE TABLE IF NOT EXISTS `customerofferdetail` (
  `Id` bigint NOT NULL,
  `CustomerOffer` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `ShareQuantity` decimal(18,4) DEFAULT NULL,
  `Currency` bigint NOT NULL,
  `QuantityUnit` bigint DEFAULT NULL,
  `SalesPrice` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `QuantityMinimum` decimal(18,4) DEFAULT NULL,
  `QuantityMaximum` decimal(18,4) DEFAULT NULL,
  `SupplierOfferDetail` bigint DEFAULT NULL,
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RowOrder` int NOT NULL DEFAULT '0',
  `PriceCategory` bigint DEFAULT NULL,
  `BasePrice` decimal(18,4) DEFAULT NULL,
  `BasePriceDate` timestamp NULL DEFAULT NULL,
  `SalesPercent` decimal(18,4) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_CustomerOfferDetail_Currency` (`Currency`),
  KEY `IDX_CustomerOfferDetail_CustOffe` (`CustomerOffer`),
  KEY `IDX_CustomerOfferDetail_PCQ` (`Product`,`Currency`,`QuantityUnit`),
  KEY `IDX_CustomerOfferDetail_Product` (`Product`),
  KEY `IDX_CustomerOfferDetail_QUnit` (`QuantityUnit`),
  KEY `IDX_CustomerOfferDetail_SQ` (`ShareQuantity`),
  KEY `IDX_CustomerOfferDetail_SuppOff` (`SupplierOfferDetail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customerorder`
--

DROP TABLE IF EXISTS `customerorder`;
CREATE TABLE IF NOT EXISTS `customerorder` (
  `Id` bigint NOT NULL,
  `VoucherType` int NOT NULL DEFAULT '0',
  `VoucherSequence` bigint NOT NULL,
  `VoucherNumber` varchar(100) NOT NULL,
  `PrimeVoucherNumber` varchar(100) DEFAULT NULL,
  `CancelledVoucher` bigint DEFAULT NULL,
  `MaintenanceProduct` bigint DEFAULT NULL,
  `Customer` bigint NOT NULL,
  `CustomerAddress` bigint DEFAULT NULL,
  `CustomerContact` bigint DEFAULT NULL,
  `VoucherDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DeliveryDate` timestamp NULL DEFAULT NULL,
  `DeliveryFrom` timestamp NULL DEFAULT NULL,
  `DeliveryTo` timestamp NULL DEFAULT NULL,
  `PaymentMethod` bigint DEFAULT NULL,
  `Currency` bigint NOT NULL,
  `CurrencyRate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Investment` bigint DEFAULT NULL,
  `Division` bigint DEFAULT NULL,
  `Agent` bigint DEFAULT NULL,
  `ContactEmployee` bigint DEFAULT NULL,
  `Campaign` bigint DEFAULT NULL,
  `CustomerContract` bigint DEFAULT NULL,
  `Warehouse` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `DepositValue` decimal(18,4) DEFAULT NULL,
  `DepositPercent` decimal(18,4) DEFAULT NULL,
  `NetValue` decimal(18,4) DEFAULT NULL,
  `GrossValue` decimal(18,4) DEFAULT NULL,
  `VatValue` decimal(18,4) DEFAULT NULL,
  `AmountAsk` bigint DEFAULT NULL,
  `Maintenance` bigint DEFAULT NULL,
  `SplitForbid` smallint NOT NULL DEFAULT '0',
  `PrimePostage` decimal(18,4) DEFAULT NULL,
  `OrderHidePrice` smallint NOT NULL DEFAULT '0',
  `Closed` smallint NOT NULL DEFAULT '0',
  `ClosedManually` smallint NOT NULL DEFAULT '0',
  `Comment` blob,
  `Cancelled` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `MaintOrderSrcCustOrder` bigint DEFAULT NULL,
  `ExpirationDate` timestamp NULL DEFAULT NULL,
  `InternalComment` blob,
  `FinalizedDate` timestamp NULL DEFAULT NULL,
  `ParcelShop` bigint DEFAULT NULL,
  `StrExA` varchar(100) DEFAULT NULL,
  `StrExB` varchar(100) DEFAULT NULL,
  `StrExC` varchar(100) DEFAULT NULL,
  `StrExD` varchar(100) DEFAULT NULL,
  `DateExA` timestamp NULL DEFAULT NULL,
  `DateExB` timestamp NULL DEFAULT NULL,
  `DateExC` timestamp NULL DEFAULT NULL,
  `DateExD` timestamp NULL DEFAULT NULL,
  `NumExA` decimal(18,4) DEFAULT NULL,
  `NumExB` decimal(18,4) DEFAULT NULL,
  `NumExC` decimal(18,4) DEFAULT NULL,
  `NumExD` decimal(18,4) DEFAULT NULL,
  `BoolExA` smallint NOT NULL DEFAULT '0',
  `BoolExB` smallint NOT NULL DEFAULT '0',
  `BoolExC` smallint NOT NULL DEFAULT '0',
  `BoolExD` smallint NOT NULL DEFAULT '0',
  `LookupExA` bigint DEFAULT NULL,
  `LookupExB` bigint DEFAULT NULL,
  `LookupExC` bigint DEFAULT NULL,
  `LookupExD` bigint DEFAULT NULL,
  `MemoExA` blob,
  `MemoExB` blob,
  `MemoExC` blob,
  `MemoExD` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `NotifyPhone` smallint NOT NULL DEFAULT '0',
  `NotifySms` smallint NOT NULL DEFAULT '0',
  `NotifyEmail` smallint NOT NULL DEFAULT '0',
  `PublicHealthPTFree` smallint NOT NULL DEFAULT '0',
  `FabricDeadLine` timestamp NULL DEFAULT NULL,
  `CheckoutBankAccount` bigint DEFAULT NULL,
  `OriginalVoucher` bigint DEFAULT NULL,
  `DepositGross` smallint NOT NULL DEFAULT '0',
  `ExchangePackage` smallint NOT NULL DEFAULT '0',
  `ChainTransaction` smallint NOT NULL DEFAULT '0',
  `ValidityDate` timestamp NULL DEFAULT NULL,
  `CurrRateDate` timestamp NULL DEFAULT NULL,
  `CancelReason` varchar(100) DEFAULT NULL,
  `CustomerOrderStatus` bigint DEFAULT NULL,
  `BankTRID` varchar(100) DEFAULT NULL,
  `CloseReason` varchar(100) DEFAULT NULL,
  `StrExE` varchar(100) DEFAULT NULL,
  `StrExF` varchar(100) DEFAULT NULL,
  `StrExG` varchar(100) DEFAULT NULL,
  `StrExH` varchar(100) DEFAULT NULL,
  `StrExI` varchar(100) DEFAULT NULL,
  `StrExJ` varchar(100) DEFAULT NULL,
  `DateExE` timestamp NULL DEFAULT NULL,
  `DateExF` timestamp NULL DEFAULT NULL,
  `DateExG` timestamp NULL DEFAULT NULL,
  `DateExH` timestamp NULL DEFAULT NULL,
  `DateExI` timestamp NULL DEFAULT NULL,
  `DateExJ` timestamp NULL DEFAULT NULL,
  `NumExE` decimal(18,4) DEFAULT NULL,
  `NumExF` decimal(18,4) DEFAULT NULL,
  `NumExG` decimal(18,4) DEFAULT NULL,
  `NumExH` decimal(18,4) DEFAULT NULL,
  `NumExI` decimal(18,4) DEFAULT NULL,
  `NumExJ` decimal(18,4) DEFAULT NULL,
  `BoolExE` smallint DEFAULT NULL,
  `BoolExF` smallint DEFAULT NULL,
  `BoolExG` smallint DEFAULT NULL,
  `BoolExH` smallint DEFAULT NULL,
  `BoolExI` smallint DEFAULT NULL,
  `BoolExJ` smallint DEFAULT NULL,
  `LookupExE` bigint DEFAULT NULL,
  `LookupExF` bigint DEFAULT NULL,
  `LookupExG` bigint DEFAULT NULL,
  `LookupExH` bigint DEFAULT NULL,
  `LookupExI` bigint DEFAULT NULL,
  `LookupExJ` bigint DEFAULT NULL,
  `MemoExE` blob,
  `MemoExF` blob,
  `MemoExG` blob,
  `MemoExH` blob,
  `MemoExI` blob,
  `MemoExJ` blob,
  PRIMARY KEY (`Id`),
  KEY `FK_CustomerOrder_CancelledVch` (`CancelledVoucher`),
  KEY `FK_CustomerOrder_Customer` (`Customer`),
  KEY `FK_CustomerOrder_CustomerAddres` (`CustomerAddress`),
  KEY `FK_CustomerOrder_CustCont` (`CustomerContact`),
  KEY `FK_CustomerOrder_Currency` (`Currency`),
  KEY `FK_CustomerOrder_Division` (`Division`),
  KEY `FK_CustomerOrder_Agent` (`Agent`),
  KEY `FK_CustomerOrder_ContactE` (`ContactEmployee`),
  KEY `FK_CustomerOrder_Contract` (`CustomerContract`),
  KEY `FK_CustomerOrder_OriginalV` (`OriginalVoucher`),
  KEY `FK_CustomerOrder_VoucherSeq` (`VoucherSequence`),
  KEY `FK_CustomerOrder_PaymentMethod` (`PaymentMethod`),
  KEY `FK_CustomerOrder_Warehouse` (`Warehouse`),
  KEY `FK_CustomerOrder_TransportMode` (`TransportMode`),
  KEY `FK_CustomerOrder_MOSCO` (`MaintOrderSrcCustOrder`),
  KEY `IDX_CustomerOrder_CRDat2` (`CurrRateDate`),
  KEY `IDX_CustomerOrder_CRDate` (`CurrRateDate`),
  KEY `IDX_CustomerOrder_ChainTransact` (`ChainTransaction`),
  KEY `IDX_CustomerOrder_DeliveryDat2` (`DeliveryDate`),
  KEY `IDX_CustomerOrder_DeliveryDate` (`DeliveryDate`),
  KEY `IDX_CustomerOrder_DeliveryInter` (`DeliveryFrom`,`DeliveryTo`),
  KEY `IDX_CustomerOrder_DepPercent` (`DepositPercent`),
  KEY `IDX_CustomerOrder_DepValue` (`DepositValue`),
  KEY `IDX_CustomerOrder_Expiratio2` (`ExpirationDate`),
  KEY `IDX_CustomerOrder_Expiration` (`ExpirationDate`),
  KEY `IDX_CustomerOrder_Prime` (`PrimeVoucherNumber`),
  KEY `IDX_CustomerOrder_ValidityDat2` (`ValidityDate`),
  KEY `IDX_CustomerOrder_ValidityDate` (`ValidityDate`),
  KEY `IDX_CustomerOrder_VoucherDat2` (`VoucherDate`),
  KEY `IDX_CustomerOrder_VoucherDate` (`VoucherDate`),
  KEY `IDX_CustomerOrder_VoucherType` (`VoucherType`),
  KEY `IRC_CustomerOrder` (`RowCreate`),
  KEY `IRM_CustomerOrder` (`RowModify`),
  KEY `customerorder_Id_index` (`Id`),
  KEY `UNQ_CustomerOrder_VoucherNumber` (`VoucherNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customerorderdetail`
--

DROP TABLE IF EXISTS `customerorderdetail`;
CREATE TABLE IF NOT EXISTS `customerorderdetail` (
  `Id` bigint NOT NULL,
  `CustomerOrder` bigint NOT NULL,
  `CancelledDetail` bigint DEFAULT NULL,
  `DeliveryDate` timestamp NULL DEFAULT NULL,
  `DeliveryFrom` timestamp NULL DEFAULT NULL,
  `DeliveryTo` timestamp NULL DEFAULT NULL,
  `Currency` bigint NOT NULL,
  `CurrencyRate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Investment` bigint DEFAULT NULL,
  `Division` bigint DEFAULT NULL,
  `Agent` bigint DEFAULT NULL,
  `Campaign` bigint DEFAULT NULL,
  `Product` bigint DEFAULT NULL,
  `ProductAlias` varchar(100) DEFAULT NULL,
  `MaintenanceProduct` bigint DEFAULT NULL,
  `Keywords` varchar(100) DEFAULT NULL,
  `Vat` bigint DEFAULT NULL,
  `QuantityUnit` bigint DEFAULT NULL,
  `QURate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Quantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `FulfilledQuantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `CancelledQuantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `CompleteQuantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `DetailStatus` bigint DEFAULT NULL,
  `CustomerOfferDetail` bigint DEFAULT NULL,
  `CustomerContractDetail` bigint DEFAULT NULL,
  `AllocateWarehouse` smallint NOT NULL DEFAULT '0',
  `MustMunufacturing` smallint NOT NULL DEFAULT '0',
  `ManufacQuantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `UnitPrice` decimal(18,4) DEFAULT NULL,
  `DiscountPercent` decimal(18,4) DEFAULT NULL,
  `DiscountUnitPrice` decimal(18,4) DEFAULT NULL,
  `GrossPrices` smallint NOT NULL DEFAULT '0',
  `DepositValue` decimal(18,4) DEFAULT NULL,
  `DepositPercent` decimal(18,4) DEFAULT NULL,
  `NetValue` decimal(18,4) DEFAULT NULL,
  `GrossValue` decimal(18,4) DEFAULT NULL,
  `VatValue` decimal(18,4) DEFAULT NULL,
  `Comment` blob,
  `CopyCommentToInvoice` smallint NOT NULL DEFAULT '0',
  `RowOrder` int NOT NULL DEFAULT '0',
  `RowVersion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Reverse` smallint NOT NULL DEFAULT '0',
  `InternalComment` blob,
  `StrExA` varchar(100) DEFAULT NULL,
  `StrExB` varchar(100) DEFAULT NULL,
  `StrExC` varchar(100) DEFAULT NULL,
  `StrExD` varchar(100) DEFAULT NULL,
  `DateExA` timestamp NULL DEFAULT NULL,
  `DateExB` timestamp NULL DEFAULT NULL,
  `DateExC` timestamp NULL DEFAULT NULL,
  `DateExD` timestamp NULL DEFAULT NULL,
  `NumExA` decimal(18,4) DEFAULT NULL,
  `NumExB` decimal(18,4) DEFAULT NULL,
  `NumExC` decimal(18,4) DEFAULT NULL,
  `NumExD` decimal(18,4) DEFAULT NULL,
  `BoolExA` smallint NOT NULL DEFAULT '0',
  `BoolExB` smallint NOT NULL DEFAULT '0',
  `BoolExC` smallint NOT NULL DEFAULT '0',
  `BoolExD` smallint NOT NULL DEFAULT '0',
  `LookupExA` bigint DEFAULT NULL,
  `LookupExB` bigint DEFAULT NULL,
  `LookupExC` bigint DEFAULT NULL,
  `LookupExD` bigint DEFAULT NULL,
  `MemoExA` blob,
  `MemoExB` blob,
  `MemoExC` blob,
  `MemoExD` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `FabricSchema` bigint DEFAULT NULL,
  `PublicHealthPTUPrice` decimal(18,4) DEFAULT NULL,
  `FabricDeadLine` timestamp NULL DEFAULT NULL,
  `PriceCategory` bigint DEFAULT NULL,
  `CurrRateDate` timestamp NULL DEFAULT NULL,
  `StrExE` varchar(100) DEFAULT NULL,
  `StrExF` varchar(100) DEFAULT NULL,
  `StrExG` varchar(100) DEFAULT NULL,
  `StrExH` varchar(100) DEFAULT NULL,
  `StrExI` varchar(100) DEFAULT NULL,
  `StrExJ` varchar(100) DEFAULT NULL,
  `DateExE` timestamp NULL DEFAULT NULL,
  `DateExF` timestamp NULL DEFAULT NULL,
  `DateExG` timestamp NULL DEFAULT NULL,
  `DateExH` timestamp NULL DEFAULT NULL,
  `DateExI` timestamp NULL DEFAULT NULL,
  `DateExJ` timestamp NULL DEFAULT NULL,
  `NumExE` decimal(18,4) DEFAULT NULL,
  `NumExF` decimal(18,4) DEFAULT NULL,
  `NumExG` decimal(18,4) DEFAULT NULL,
  `NumExH` decimal(18,4) DEFAULT NULL,
  `NumExI` decimal(18,4) DEFAULT NULL,
  `NumExJ` decimal(18,4) DEFAULT NULL,
  `BoolExE` smallint DEFAULT NULL,
  `BoolExF` smallint DEFAULT NULL,
  `BoolExG` smallint DEFAULT NULL,
  `BoolExH` smallint DEFAULT NULL,
  `BoolExI` smallint DEFAULT NULL,
  `BoolExJ` smallint DEFAULT NULL,
  `LookupExE` bigint DEFAULT NULL,
  `LookupExF` bigint DEFAULT NULL,
  `LookupExG` bigint DEFAULT NULL,
  `LookupExH` bigint DEFAULT NULL,
  `LookupExI` bigint DEFAULT NULL,
  `LookupExJ` bigint DEFAULT NULL,
  `MemoExE` blob,
  `MemoExF` blob,
  `MemoExG` blob,
  `MemoExH` blob,
  `MemoExI` blob,
  `MemoExJ` blob,
  PRIMARY KEY (`Id`),
  KEY `FK_CustomerOrderDetail_Order` (`CustomerOrder`),
  KEY `FK_CustomerOrderDetail_Cancel` (`CancelledDetail`),
  KEY `FK_CustomerOrderDetail_Currency` (`Currency`),
  KEY `FK_CustomerOrderDetail_Division` (`Division`),
  KEY `FK_CustomerOrderDetail_Agent` (`Agent`),
  KEY `FK_CustomerOrderDetail_Product` (`Product`),
  KEY `FK_CustomerOrderDetail_Vat` (`Vat`),
  KEY `FK_CustomerOrderDetail_QUnit` (`QuantityUnit`),
  KEY `FK_CustomerOrderDetail_DetailSt` (`DetailStatus`),
  KEY `FK_CustomerOrderDetail_PC` (`PriceCategory`),
  KEY `IDX_CustomerOrderDetail_CRDat2` (`CurrRateDate`),
  KEY `IDX_CustomerOrderDetail_CRDate` (`CurrRateDate`),
  KEY `IDX_CustomerOrderDetail_DelDat2` (`DeliveryDate`),
  KEY `IDX_CustomerOrderDetail_DelDate` (`DeliveryDate`),
  KEY `IDX_CustomerOrderDetail_DelInt` (`DeliveryFrom`,`DeliveryTo`),
  KEY `IDX_CustomerOrderDetail_DepPerc` (`DepositPercent`),
  KEY `IDX_CustomerOrderDetail_DepVal` (`DepositValue`),
  KEY `IDX_CustomerOrderDetail_Keyword` (`Keywords`),
  KEY `IDX_CustomerOrderDetail_RowOrd` (`RowOrder`),
  KEY `IRC_CustomerOrderDetail` (`RowCreate`),
  KEY `IRM_CustomerOrderDetail` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customerorderdetailstatus`
--

DROP TABLE IF EXISTS `customerorderdetailstatus`;
CREATE TABLE IF NOT EXISTS `customerorderdetailstatus` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `StrictAllocate` smallint NOT NULL DEFAULT '0',
  `Deleted` smallint NOT NULL DEFAULT '0',
  `EditMode` int NOT NULL DEFAULT '0',
  `ForeColor` int DEFAULT NULL,
  `BackColor` int DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_CustomerOrderDetailStatus_N` (`Name`),
  KEY `IRC_CustomerOrderDetailStatus` (`RowCreate`),
  KEY `IRM_CustomerOrderDetailStatus` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `customerorderstatus`
--

DROP TABLE IF EXISTS `customerorderstatus`;
CREATE TABLE IF NOT EXISTS `customerorderstatus` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `ForeColor` int DEFAULT NULL,
  `BackColor` int DEFAULT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IDX_CustomerOrderStatus_N` (`Name`),
  UNIQUE KEY `IRC_CustomerOrderStatus` (`RowCreate`),
  UNIQUE KEY `IRM_CustomerOrderStatus` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `datatables_states`
--

DROP TABLE IF EXISTS `datatables_states`;
CREATE TABLE IF NOT EXISTS `datatables_states` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(20) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `state` text NOT NULL,
  `array` text,
  PRIMARY KEY (`id`),
  KEY `ds_name_user` (`name`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `dictionaries`
--

DROP TABLE IF EXISTS `dictionaries`;
CREATE TABLE IF NOT EXISTS `dictionaries` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipus` int UNSIGNED NOT NULL,
  `nev` varchar(191) NOT NULL,
  `leiras` longtext,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dictionaries_tipus_id_index` (`tipus`,`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2116 DEFAULT CHARSET=utf8mb3;

--
-- A t�bla adatainak ki�rat�sa `dictionaries`
--

INSERT INTO `dictionaries` (`id`, `tipus`, `nev`, `leiras`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 'Rendszergazda', 'Rendszergazda', NULL, NULL, NULL),
(2, 1, 'Bels� felhaszn�l�', 'Bels� felhaszn�l�', NULL, NULL, NULL),
(1, 1, 'B2B felhaszn�l�', 'B2B felhaszn�l�', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `Site` bigint DEFAULT NULL,
  `IsAdmin` smallint NOT NULL DEFAULT '0',
  `IsEmployee` smallint NOT NULL DEFAULT '0',
  `IsPermission` smallint NOT NULL DEFAULT '0',
  `IsAgent` smallint NOT NULL DEFAULT '0',
  `Code` varchar(40) DEFAULT NULL,
  `Titular` varchar(10) DEFAULT NULL,
  `Name` varchar(80) NOT NULL,
  `BirthName` varchar(100) DEFAULT NULL,
  `BirthPlace` varchar(100) DEFAULT NULL,
  `BirthDate` datetime DEFAULT NULL,
  `GenderMale` smallint NOT NULL DEFAULT '0',
  `Nationality` varchar(100) DEFAULT NULL,
  `MotherName` varchar(100) DEFAULT NULL,
  `TaxId` varchar(20) DEFAULT NULL,
  `InsuranceId` varchar(20) DEFAULT NULL,
  `IdentifiyNumber` varchar(20) DEFAULT NULL,
  `PassportNumber` varchar(20) DEFAULT NULL,
  `BankName` varchar(100) DEFAULT NULL,
  `BankAccount` varchar(100) DEFAULT NULL,
  `Phone` varchar(100) DEFAULT NULL,
  `Sms` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhonePrivate` varchar(100) DEFAULT NULL,
  `SmsPrivate` varchar(20) DEFAULT NULL,
  `EmailPrivate` varchar(100) DEFAULT NULL,
  `Picture` longblob,
  `DefaultDivision` bigint DEFAULT NULL,
  `Leader` bigint DEFAULT NULL,
  `LoginDisabled` smallint NOT NULL DEFAULT '0',
  `Username` varchar(32) DEFAULT NULL,
  `Password` varchar(32) DEFAULT NULL,
  `PINCode` varchar(10) DEFAULT NULL,
  `SidSddl` varchar(128) DEFAULT NULL,
  `SidSddlMachine` varchar(128) DEFAULT NULL,
  `TwoFactorAuthSms` varchar(20) DEFAULT NULL,
  `TwoFactorAuthEmail` varchar(100) DEFAULT NULL,
  `EmailSignature` longblob,
  `UILanguage` varchar(10) DEFAULT NULL,
  `CallCardInfo` longblob,
  `Setting` longblob,
  `FabricExpense` varchar(100) DEFAULT NULL,
  `Comment` longblob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `FK_Employee_DefaultDivision` (`DefaultDivision`),
  KEY `FK_Employee_Leader` (`Leader`),
  KEY `IDX_Employee_IsEmployee` (`IsEmployee`) USING BTREE,
  KEY `IDX_Employee_Code` (`Code`),
  KEY `IDX_Employee_IdentifiyNumber` (`IdentifiyNumber`),
  KEY `IDX_Employee_InsId` (`InsuranceId`),
  KEY `IDX_Employee_Name` (`Name`),
  KEY `IDX_Employee_PINCode` (`PINCode`),
  KEY `IDX_Employee_PassportNumber` (`PassportNumber`),
  KEY `IDX_Employee_Site` (`Site`),
  KEY `IDX_Employee_TaxId` (`TaxId`),
  KEY `IDX_Employee_UserPass` (`Username`,`Password`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `excelimport`
--

DROP TABLE IF EXISTS `excelimport`;
CREATE TABLE IF NOT EXISTS `excelimport` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Field0` varchar(255) DEFAULT NULL,
  `Field1` varchar(255) DEFAULT NULL,
  `Field2` varchar(255) DEFAULT NULL,
  `Field3` varchar(255) DEFAULT NULL,
  `Field4` varchar(255) DEFAULT NULL,
  `Field5` varchar(255) DEFAULT NULL,
  `Field6` varchar(255) DEFAULT NULL,
  `Field7` varchar(255) DEFAULT NULL,
  `Field8` varchar(255) DEFAULT NULL,
  `Field9` varchar(255) DEFAULT NULL,
  `Field10` varchar(255) DEFAULT NULL,
  `Field11` varchar(255) DEFAULT NULL,
  `Field12` varchar(255) DEFAULT NULL,
  `Field13` varchar(255) DEFAULT NULL,
  `Field14` varchar(255) DEFAULT NULL,
  `Field15` varchar(255) DEFAULT NULL,
  `Field16` varchar(255) DEFAULT NULL,
  `Field17` varchar(255) DEFAULT NULL,
  `Field18` varchar(255) DEFAULT NULL,
  `Field19` varchar(255) DEFAULT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `guaranteemode`
--

DROP TABLE IF EXISTS `guaranteemode`;
CREATE TABLE IF NOT EXISTS `guaranteemode` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_GuaranteeMode_Name` (`Name`),
  KEY `IRC_GuaranteeMode` (`RowCreate`),
  KEY `IRM_GuaranteeMode` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shortname` char(2) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf32;

--
-- A t�bla adatainak ki�rat�sa `languages`
--

INSERT INTO `languages` (`id`, `shortname`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(0, 'hu', 'Magyar', NULL, NULL, NULL),
(3, 'en', 'English', NULL, NULL, NULL),
(4, 'de', 'Deutch', NULL, NULL, NULL),
(5, 'bg', 'B???????', NULL, NULL, NULL),
(6, 'cz', '�e�k�', NULL, NULL, NULL),
(7, 'dk', 'Dansk', NULL, NULL, NULL),
(8, 'ee', 'Eesti', NULL, NULL, NULL),
(9, 'fi', 'Suomi', NULL, NULL, NULL),
(10, 'fr', 'France', NULL, NULL, NULL),
(11, 'gr', '????????', NULL, NULL, NULL),
(12, 'nl', 'Nederlands', NULL, NULL, NULL),
(13, 'hr', 'Hrvatska', NULL, NULL, NULL),
(14, 'ie', 'Ireland', NULL, NULL, NULL),
(15, 'pl', 'Polski', NULL, NULL, NULL),
(16, 'lv', 'Kluva', NULL, NULL, NULL),
(17, 'lt', 'Lietuviu', NULL, NULL, NULL),
(18, 'mt', 'Malti', NULL, NULL, NULL),
(19, 'no', 'Norsk', NULL, NULL, NULL),
(20, 'it', 'Italiano', NULL, NULL, NULL),
(21, 'ru', '???????', NULL, NULL, NULL),
(22, 'pt', 'Portugal', NULL, NULL, NULL),
(23, 'ro', 'Rom�n�', NULL, NULL, NULL),
(24, 'es', 'Espanol', NULL, NULL, NULL),
(25, 'se', 'Svenska', NULL, NULL, NULL),
(26, 'rs', '??????', NULL, NULL, NULL),
(27, 'sk', 'Slovensk�', NULL, NULL, NULL),
(28, 'si', 'Sloven��ina', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `logitem`
--

DROP TABLE IF EXISTS `logitem`;
CREATE TABLE IF NOT EXISTS `logitem` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `eventtype` int NOT NULL,
  `eventdatetime` timestamp NOT NULL,
  `remoteaddress` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logitem_id_uindex` (`id`),
  KEY `logitem__customeruser_index` (`customer_id`,`user_id`,`eventdatetime`),
  KEY `logitem__eventdatetime_index` (`eventdatetime`),
  KEY `logitem__user_index` (`user_id`,`eventdatetime`)
) ENGINE=InnoDB AUTO_INCREMENT=995 DEFAULT CHARSET=utf32;

--
-- A t�bla adatainak ki�rat�sa `logitem`
--

INSERT INTO `logitem` (`id`, `customer_id`, `user_id`, `eventtype`, `eventdatetime`, `remoteaddress`, `created_at`, `updated_at`, `deleted_at`) VALUES
(994, -9999, 0, 1, '2023-02-07 09:35:23', '127.0.0.1', '2023-02-07 09:35:23', '2023-02-07 09:35:23', NULL);

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `logitemtable`
--

DROP TABLE IF EXISTS `logitemtable`;
CREATE TABLE IF NOT EXISTS `logitemtable` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logitem_id` int NOT NULL,
  `tablename` varchar(100) NOT NULL,
  `recordid` int DEFAULT NULL,
  `targetrecordid` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logitemtable_id_uindex` (`id`),
  KEY `logitemtable__logitem_index` (`logitem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `logitemtabledetail`
--

DROP TABLE IF EXISTS `logitemtabledetail`;
CREATE TABLE IF NOT EXISTS `logitemtabledetail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logitemtable_id` int NOT NULL,
  `changedfield` varchar(100) NOT NULL,
  `oldinteger` int DEFAULT NULL,
  `oldstring` varchar(250) DEFAULT NULL,
  `olddatetime` datetime DEFAULT NULL,
  `olddecimal` decimal(18,4) DEFAULT NULL,
  `newinteger` int DEFAULT NULL,
  `newstring` varchar(250) DEFAULT NULL,
  `newdatetime` datetime DEFAULT NULL,
  `newdecimal` decimal(18,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `logitemtabledetail_id_uindex` (`id`),
  KEY `logitemtabledetail__litid_index` (`logitemtable_id`,`changedfield`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

--
-- A t�bla adatainak ki�rat�sa `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2014_10_12_200000_add_two_factor_columns_to_users_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2020_05_21_100000_create_teams_table', 1),
(7, '2020_05_21_200000_create_team_user_table', 1),
(8, '2020_05_21_300000_create_team_invitations_table', 1),
(9, '2021_03_04_165629_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `paymentmethod`
--

DROP TABLE IF EXISTS `paymentmethod`;
CREATE TABLE IF NOT EXISTS `paymentmethod` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Cash` smallint NOT NULL DEFAULT '0',
  `UseAlways` smallint NOT NULL DEFAULT '0',
  `Immediately` smallint NOT NULL DEFAULT '0',
  `PettyCashCreation` smallint NOT NULL DEFAULT '0',
  `ToleranceDay` int DEFAULT NULL,
  `FulfillmentTolerance` smallint NOT NULL DEFAULT '0',
  `DiscountPercent` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `VoucherComment` blob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_PaymentMethod_Name` (`Name`),
  KEY `IRC_PaymentMethod` (`RowCreate`),
  KEY `IRM_PaymentMethod` (`RowModify`),
  KEY `paymentmethod_Id_index` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `paymentmethodlang`
--

DROP TABLE IF EXISTS `paymentmethodlang`;
CREATE TABLE IF NOT EXISTS `paymentmethodlang` (
  `Id` bigint NOT NULL,
  `Lang` int NOT NULL DEFAULT '0',
  `PaymentMethod` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `VoucherComment` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_PaymentMethodLang_PM` (`PaymentMethod`),
  KEY `IDX_PaymentMethodLang_LPM` (`Lang`,`PaymentMethod`),
  KEY `IRC_PaymentMethodLang` (`RowCreate`),
  KEY `IRM_PaymentMethodLang` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `pricecategory`
--

DROP TABLE IF EXISTS `pricecategory`;
CREATE TABLE IF NOT EXISTS `pricecategory` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `IncomingPrice` smallint NOT NULL DEFAULT '0',
  `BasePrice` smallint NOT NULL DEFAULT '0',
  `RateRelativeToBasePrice` smallint NOT NULL DEFAULT '0',
  `Rate` decimal(18,4) DEFAULT NULL,
  `NineRule` smallint NOT NULL DEFAULT '0',
  `DisableAutoCalc` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `GrossPrices` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `IDX_PriceCategory_Name` (`Name`),
  KEY `IRC_PriceCategory` (`RowCreate`),
  KEY `IRM_PriceCategory` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `Id` bigint NOT NULL,
  `Code` varchar(40) NOT NULL,
  `CodeHidden` smallint NOT NULL DEFAULT '0',
  `Barcode` varchar(100) DEFAULT NULL,
  `Name` varchar(100) NOT NULL,
  `Inactive` smallint NOT NULL DEFAULT '0',
  `CreateDateTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `PrimeSupplier` bigint DEFAULT NULL,
  `Manufacturer` bigint DEFAULT NULL,
  `ProductCategory` bigint DEFAULT NULL,
  `Vat` bigint DEFAULT NULL,
  `VatBuy` bigint DEFAULT NULL,
  `SellBanned` smallint NOT NULL DEFAULT '0',
  `BuyBanned` smallint NOT NULL DEFAULT '0',
  `RunOut` smallint NOT NULL DEFAULT '0',
  `Service` smallint NOT NULL DEFAULT '0',
  `MediateService` smallint NOT NULL DEFAULT '0',
  `ZeroPriceAllowed` smallint NOT NULL DEFAULT '0',
  `Accumulator` smallint NOT NULL DEFAULT '0',
  `AccProduct` bigint DEFAULT NULL,
  `VisibleInPriceList` smallint NOT NULL DEFAULT '0',
  `QuantityUnit` bigint DEFAULT NULL,
  `QuantityDigits` int NOT NULL DEFAULT '0',
  `PriceDigits` int NOT NULL DEFAULT '0',
  `PriceDigitsExt` varchar(100) DEFAULT NULL,
  `GrossPrices` smallint NOT NULL DEFAULT '0',
  `SupplierPriceAffected` smallint NOT NULL DEFAULT '0',
  `SupplierPriceTolerance` int NOT NULL DEFAULT '0',
  `SupplierInPriceOnly` smallint NOT NULL DEFAULT '0',
  `SupplierToSysCurrency` smallint DEFAULT NULL,
  `SupplierToBaseQU` smallint NOT NULL DEFAULT '0',
  `WeightControll` smallint NOT NULL DEFAULT '0',
  `AttachmentRoll` smallint NOT NULL DEFAULT '0',
  `CustomsTariffNumber` varchar(100) DEFAULT NULL,
  `Weight` decimal(18,4) DEFAULT NULL,
  `DimensionWidth` decimal(18,4) DEFAULT NULL,
  `DimensionHeight` decimal(18,4) DEFAULT NULL,
  `DimensionDepth` decimal(18,4) DEFAULT NULL,
  `QuantityMin` decimal(18,4) DEFAULT NULL,
  `QuantityMax` decimal(18,4) DEFAULT NULL,
  `QuantityOpt` decimal(18,4) DEFAULT NULL,
  `QtyPackage` decimal(18,4) DEFAULT NULL,
  `QtyLevel` decimal(18,4) DEFAULT NULL,
  `QtyPallet` decimal(18,4) DEFAULT NULL,
  `IstatKN` bigint DEFAULT NULL,
  `IstatCountryOrigin` bigint DEFAULT NULL,
  `IncidentExpense` decimal(18,4) DEFAULT NULL,
  `IncidentExpensePerc` decimal(18,4) DEFAULT NULL,
  `GuaranteeMonths` int DEFAULT NULL,
  `GuaranteeMode` bigint DEFAULT NULL,
  `GuaranteeMinUnitPrice` decimal(18,4) DEFAULT NULL,
  `BestBeforeValue` int DEFAULT NULL,
  `BestBeforeIsDay` smallint NOT NULL DEFAULT '0',
  `PriceCategoryRule` blob,
  `MustMunufacturing` smallint NOT NULL DEFAULT '0',
  `StrictManufacturing` smallint NOT NULL DEFAULT '0',
  `SerialMode` int NOT NULL DEFAULT '0',
  `SerialSetting` blob,
  `ShelfMode` int NOT NULL DEFAULT '0',
  `ClearAllocation` smallint NOT NULL DEFAULT '0',
  `DefaultAlias` varchar(100) DEFAULT NULL,
  `DepositPercent` decimal(18,4) DEFAULT NULL,
  `Pictogram` varchar(100) DEFAULT NULL,
  `Comment` blob,
  `WebName` varchar(100) DEFAULT NULL,
  `WebDescription` blob,
  `WebUrl` varchar(100) DEFAULT NULL,
  `Picture` longblob,
  `StrExA` varchar(100) DEFAULT NULL,
  `StrExB` varchar(100) DEFAULT NULL,
  `StrExC` varchar(100) DEFAULT NULL,
  `StrExD` varchar(100) DEFAULT NULL,
  `DateExA` timestamp NULL DEFAULT NULL,
  `DateExB` timestamp NULL DEFAULT NULL,
  `NumExA` decimal(18,4) DEFAULT NULL,
  `NumExB` decimal(18,4) DEFAULT NULL,
  `NumExC` decimal(18,4) DEFAULT NULL,
  `BoolExA` smallint NOT NULL DEFAULT '0',
  `BoolExB` smallint NOT NULL DEFAULT '0',
  `LookupExA` bigint DEFAULT NULL,
  `LookupExB` bigint DEFAULT NULL,
  `LookupExC` bigint DEFAULT NULL,
  `LookupExD` bigint DEFAULT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowVersion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `MinProfitPercent` decimal(18,4) DEFAULT NULL,
  `ManufacturingCost` decimal(18,4) DEFAULT NULL,
  `SerialAutoMaintenance` smallint NOT NULL DEFAULT '0',
  `AdrMaterial` bigint DEFAULT NULL,
  `AdrPackage` bigint DEFAULT NULL,
  `WeightNet` decimal(18,4) DEFAULT NULL,
  `MemoExA` blob,
  `MemoExB` blob,
  `DateExC` timestamp NULL DEFAULT NULL,
  `DateExD` timestamp NULL DEFAULT NULL,
  `NumExD` decimal(18,4) DEFAULT NULL,
  `BoolExC` smallint NOT NULL DEFAULT '0',
  `BoolExD` smallint NOT NULL DEFAULT '0',
  `MemoExC` blob,
  `MemoExD` blob,
  `WebMetaDescription` blob,
  `WebKeywords` varchar(100) DEFAULT NULL,
  `WebDisplay` smallint NOT NULL DEFAULT '0',
  `LookupExE` bigint DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `FillingVolume` decimal(18,4) DEFAULT NULL,
  `PublicHealthPT` bigint DEFAULT NULL,
  `VoucherRules` blob,
  `IsLarge` smallint NOT NULL DEFAULT '0',
  `UseWarrantyRule` smallint NOT NULL DEFAULT '0',
  `AdrCalcBasis` int NOT NULL DEFAULT '0',
  `EuVat` bigint DEFAULT NULL,
  `EuVatBuy` bigint DEFAULT NULL,
  `NonEuVat` bigint DEFAULT NULL,
  `NonEuVatBuy` bigint DEFAULT NULL,
  `BidAllowed` smallint NOT NULL DEFAULT '0',
  `IsPallet` smallint NOT NULL DEFAULT '0',
  `IsFragile` smallint DEFAULT '0',
  `PictureDateTime` timestamp NULL DEFAULT NULL,
  `MinSellQuantity` decimal(18,4) DEFAULT NULL,
  `StrExE` varchar(100) DEFAULT NULL,
  `StrExF` varchar(100) DEFAULT NULL,
  `StrExG` varchar(100) DEFAULT NULL,
  `StrExH` varchar(100) DEFAULT NULL,
  `StrExI` varchar(100) DEFAULT NULL,
  `StrExJ` varchar(100) DEFAULT NULL,
  `DateExE` timestamp NULL DEFAULT NULL,
  `DateExF` timestamp NULL DEFAULT NULL,
  `DateExG` timestamp NULL DEFAULT NULL,
  `DateExH` timestamp NULL DEFAULT NULL,
  `DateExI` timestamp NULL DEFAULT NULL,
  `DateExJ` timestamp NULL DEFAULT NULL,
  `NumExE` decimal(18,4) DEFAULT NULL,
  `NumExF` decimal(18,4) DEFAULT NULL,
  `NumExG` decimal(18,4) DEFAULT NULL,
  `NumExH` decimal(18,4) DEFAULT NULL,
  `NumExI` decimal(18,4) DEFAULT NULL,
  `NumExJ` decimal(18,4) DEFAULT NULL,
  `BoolExE` smallint DEFAULT NULL,
  `BoolExF` smallint DEFAULT NULL,
  `BoolExG` smallint DEFAULT NULL,
  `BoolExH` smallint DEFAULT NULL,
  `BoolExI` smallint DEFAULT NULL,
  `BoolExJ` smallint DEFAULT NULL,
  `LookupExF` bigint DEFAULT NULL,
  `LookupExG` bigint DEFAULT NULL,
  `LookupExH` bigint DEFAULT NULL,
  `LookupExI` bigint DEFAULT NULL,
  `LookupExJ` bigint DEFAULT NULL,
  `MemoExE` blob,
  `MemoExF` blob,
  `MemoExG` blob,
  `MemoExH` blob,
  `MemoExI` blob,
  `MemoExJ` blob,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_Product_Code` (`Code`),
  UNIQUE KEY `product_Id_uindex` (`Id`),
  KEY `FK_Product_PrimeSupplier` (`PrimeSupplier`),
  KEY `FK_Product_AccProduct` (`AccProduct`),
  KEY `FK_Product_GuaranteeMode` (`GuaranteeMode`),
  KEY `FK_Product_ProductCategory` (`ProductCategory`),
  KEY `FK_Product_Vat` (`Vat`),
  KEY `FK_Product_QuantityUnit` (`QuantityUnit`),
  KEY `FK_Product_ExA` (`LookupExA`),
  KEY `FK_Product_ExB` (`LookupExB`),
  KEY `FK_Product_ExC` (`LookupExC`),
  KEY `FK_Product_ExD` (`LookupExD`),
  KEY `FK_Product_ExE` (`LookupExE`),
  KEY `FK_Product_EuVatBuy` (`EuVatBuy`),
  KEY `FK_Product_NonEuVat` (`NonEuVat`),
  KEY `FK_Product_NonEuVatBuy` (`NonEuVatBuy`),
  KEY `IDX_Product_Barcode` (`Barcode`),
  KEY `IDX_Product_CustomTariffNumber` (`CustomsTariffNumber`),
  KEY `IDX_Product_DepPercent` (`DepositPercent`),
  KEY `IDX_Product_Name` (`Name`),
  KEY `IDX_Product_UseWarrantyRule` (`UseWarrantyRule`),
  KEY `IRC_Product` (`RowCreate`),
  KEY `IRM_Product` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productassociation`
--

DROP TABLE IF EXISTS `productassociation`;
CREATE TABLE IF NOT EXISTS `productassociation` (
  `Id` bigint NOT NULL,
  `OriginalProduct` bigint NOT NULL,
  `AssociatedProduct` bigint NOT NULL,
  `ProductAssociationType` bigint NOT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductAssociation_Original` (`OriginalProduct`),
  KEY `FK_ProductAssociation_Associate` (`AssociatedProduct`),
  KEY `FK_ProductAssociation_AssocType` (`ProductAssociationType`),
  KEY `IRC_ProductAssociation` (`RowCreate`),
  KEY `IRM_ProductAssociation` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productassociationtype`
--

DROP TABLE IF EXISTS `productassociationtype`;
CREATE TABLE IF NOT EXISTS `productassociationtype` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_ProductAssociationType_Name` (`Name`),
  KEY `IRC_ProductAssociationType` (`RowCreate`),
  KEY `IRM_ProductAssociationType` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productattribute`
--

DROP TABLE IF EXISTS `productattribute`;
CREATE TABLE IF NOT EXISTS `productattribute` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `AttributeTypeValue` int NOT NULL DEFAULT '0',
  `Postfix` varchar(8) DEFAULT NULL,
  `Filter` smallint NOT NULL DEFAULT '0',
  `HideFromVoucher` smallint NOT NULL DEFAULT '0',
  `Priority` int NOT NULL DEFAULT '0',
  `HideFromWeb` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_ProductAttribute_Name` (`Name`),
  KEY `IRC_ProductAttribute` (`RowCreate`),
  KEY `IRM_ProductAttribute` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productattributelang`
--

DROP TABLE IF EXISTS `productattributelang`;
CREATE TABLE IF NOT EXISTS `productattributelang` (
  `Id` bigint NOT NULL,
  `Lang` int NOT NULL DEFAULT '0',
  `ProductAttribute` bigint NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Postfix` varchar(8) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductAttributeLang_PAttr` (`ProductAttribute`),
  KEY `IDX_ProductAttributeLang_LP` (`Lang`,`ProductAttribute`),
  KEY `IRC_ProductAttributeLang` (`RowCreate`),
  KEY `IRM_ProductAttributeLang` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productattributes`
--

DROP TABLE IF EXISTS `productattributes`;
CREATE TABLE IF NOT EXISTS `productattributes` (
  `Id` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `ProductAttribute` bigint NOT NULL,
  `ValueString` varchar(100) NOT NULL,
  `ValueDecimal` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `ValueDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ValueBool` smallint NOT NULL DEFAULT '0',
  `ValueLookup` bigint DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductAttributes_Product` (`Product`),
  KEY `FK_ProductAttributes_ProdAttr` (`ProductAttribute`),
  KEY `FK_ProductAttributes_PAValue` (`ValueLookup`),
  KEY `IDX_ProductAttributes_VDat2` (`ValueDate`),
  KEY `IDX_ProductAttributes_VDate` (`ValueDate`),
  KEY `IDX_ProductAttributes_VDec` (`ValueDecimal`),
  KEY `IDX_ProductAttributes_VStr` (`ValueString`),
  KEY `IRC_ProductAttributes` (`RowCreate`),
  KEY `IRM_ProductAttributes` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productcategory`
--

DROP TABLE IF EXISTS `productcategory`;
CREATE TABLE IF NOT EXISTS `productcategory` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Parent` bigint DEFAULT NULL,
  `LeftValue` bigint NOT NULL DEFAULT '0',
  `RightValue` bigint NOT NULL DEFAULT '0',
  `ProfitPercent` decimal(18,4) DEFAULT NULL,
  `PriceDigits` int DEFAULT NULL,
  `PriceDigitsExt` varchar(100) DEFAULT NULL,
  `Vat` bigint DEFAULT NULL,
  `VatBuy` bigint DEFAULT NULL,
  `Service` smallint DEFAULT NULL,
  `QuantityUnit` bigint DEFAULT NULL,
  `QuantityDigits` int DEFAULT NULL,
  `CustomsTariffNumber` varchar(100) DEFAULT NULL,
  `GuaranteeMonths` int DEFAULT NULL,
  `GuaranteeMode` bigint DEFAULT NULL,
  `GuaranteeMinUnitPrice` decimal(18,4) DEFAULT NULL,
  `GuaranteeDescription` blob,
  `BarcodeMask` varchar(100) DEFAULT NULL,
  `MinProfitPercent` decimal(18,4) DEFAULT NULL,
  `PriceCategoryRule` blob,
  `VoucherRules` blob,
  `UseWarrantyRule` smallint DEFAULT NULL,
  `EuVat` bigint DEFAULT NULL,
  `EuVatBuy` bigint DEFAULT NULL,
  `NonEuVat` bigint DEFAULT NULL,
  `NonEuVatBuy` bigint DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductCategory_Category` (`Parent`),
  KEY `FK_ProductCategory_Vat` (`Vat`),
  KEY `FK_ProductCategory_VatBuy` (`VatBuy`),
  KEY `FK_ProductCategory_QuantityUnit` (`QuantityUnit`),
  KEY `FK_ProductCategory_GuaranteeMod` (`GuaranteeMode`),
  KEY `FK_ProductCategory_EuVat` (`EuVat`),
  KEY `FK_ProductCategory_EuVatBuy` (`EuVatBuy`),
  KEY `FK_ProductCategory_NonEuVat` (`NonEuVat`),
  KEY `FK_ProductCategory_NonEuVatBuy` (`NonEuVatBuy`),
  KEY `IDX_ProductCategory_Name` (`Name`),
  KEY `IDX_ProductCategory_Values` (`LeftValue`,`RightValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productcategorydiscount`
--

DROP TABLE IF EXISTS `productcategorydiscount`;
CREATE TABLE IF NOT EXISTS `productcategorydiscount` (
  `Id` bigint NOT NULL,
  `ProductCategory` bigint NOT NULL,
  `Customer` bigint DEFAULT NULL,
  `CustCategory` bigint DEFAULT NULL,
  `Inherit` smallint NOT NULL DEFAULT '0',
  `Discount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `ValidFrom` timestamp NULL DEFAULT NULL,
  `ValidTo` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductCategoryDiscount_PrCa` (`ProductCategory`),
  KEY `FK_Product_EuVat` (`Customer`),
  KEY `FK_ProductCategoryDiscount_CCat` (`CustCategory`),
  KEY `IDX_ProductCategoryDiscount_Vs` (`ValidFrom`,`ValidTo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productcustomercode`
--

DROP TABLE IF EXISTS `productcustomercode`;
CREATE TABLE IF NOT EXISTS `productcustomercode` (
  `Id` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `Customer` bigint NOT NULL,
  `Code` varchar(40) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_ProductCustomerCode_ProdCus` (`Product`,`Customer`),
  KEY `FK_ProductCustomerCode_Customer` (`Customer`),
  KEY `IDX_ProductCustomerCode_Code` (`Code`),
  KEY `IDX_ProductCustomerCode_Name` (`Name`),
  KEY `IRC_ProductCustomerCode` (`RowCreate`),
  KEY `IRM_ProductCustomerCode` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productcustomerdiscount`
--

DROP TABLE IF EXISTS `productcustomerdiscount`;
CREATE TABLE IF NOT EXISTS `productcustomerdiscount` (
  `Id` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `Customer` bigint NOT NULL,
  `Discount` decimal(18,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_ProductCustomerDiscount` (`Product`,`Customer`),
  KEY `FK_ProductCustomerDiscount_Cust` (`Customer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productlang`
--

DROP TABLE IF EXISTS `productlang`;
CREATE TABLE IF NOT EXISTS `productlang` (
  `Id` bigint NOT NULL,
  `Lang` int NOT NULL DEFAULT '0',
  `Product` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Comment` blob,
  `WebName` varchar(100) DEFAULT NULL,
  `WebDescription` blob,
  `WebUrl` varchar(100) DEFAULT NULL,
  `WebMetaDescription` blob,
  `WebKeywords` varchar(100) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductLang_Product` (`Product`),
  KEY `IDX_ProductLang_PL` (`Lang`,`Product`),
  KEY `IRC_ProductLang` (`RowCreate`),
  KEY `IRM_ProductLang` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `productprice`
--

DROP TABLE IF EXISTS `productprice`;
CREATE TABLE IF NOT EXISTS `productprice` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `Product` bigint NOT NULL,
  `Currency` bigint NOT NULL,
  `ValidFrom` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PriceCategory` bigint NOT NULL,
  `QuantityUnit` bigint NOT NULL,
  `Price` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProductPrice_Currency` (`Currency`),
  KEY `FK_ProductPrice_PriceCategory` (`PriceCategory`),
  KEY `FK_ProductPrice_QuantityUnit` (`QuantityUnit`),
  KEY `IDX_ProductPrice_PCPV` (`Product`,`Currency`,`PriceCategory`,`ValidFrom`,`QuantityUnit`),
  KEY `productprice_Product_RowCreate_index` (`Product`,`RowCreate`)
) ENGINE=InnoDB AUTO_INCREMENT=186226 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `quantityunit`
--

DROP TABLE IF EXISTS `quantityunit`;
CREATE TABLE IF NOT EXISTS `quantityunit` (
  `Id` bigint NOT NULL,
  `Name` varchar(10) NOT NULL,
  `CashRegIndex` int NOT NULL DEFAULT '0',
  `QuantityDigits` int NOT NULL DEFAULT '0',
  `Standard` smallint NOT NULL DEFAULT '0',
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_QuantityUnit_Name` (`Name`),
  KEY `IRC_QuantityUnit` (`RowCreate`),
  KEY `IRM_QuantityUnit` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `quantityunitlang`
--

DROP TABLE IF EXISTS `quantityunitlang`;
CREATE TABLE IF NOT EXISTS `quantityunitlang` (
  `Id` bigint NOT NULL,
  `Lang` int NOT NULL DEFAULT '0',
  `QuantityUnit` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_QuantityUnitLang_QU` (`QuantityUnit`),
  KEY `IDX_QuantityUnitLang_LQU` (`Lang`,`QuantityUnit`),
  KEY `IRC_QuantityUnitLang` (`RowCreate`),
  KEY `IRM_QuantityUnitLang` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `shoppingcart`
--

DROP TABLE IF EXISTS `shoppingcart`;
CREATE TABLE IF NOT EXISTS `shoppingcart` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `VoucherNumber` varchar(100) DEFAULT NULL,
  `Customer` bigint NOT NULL,
  `CustomerAddress` bigint DEFAULT NULL,
  `CustomerContact` bigint DEFAULT NULL,
  `VoucherDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `DeliveryDate` timestamp NULL DEFAULT NULL,
  `PaymentMethod` bigint DEFAULT NULL,
  `Currency` bigint NOT NULL,
  `CurrencyRate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `CustomerContract` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `DepositValue` decimal(18,4) DEFAULT NULL,
  `DepositPercent` decimal(18,4) DEFAULT NULL,
  `NetValue` decimal(18,4) DEFAULT NULL,
  `GrossValue` decimal(18,4) DEFAULT NULL,
  `VatValue` decimal(18,4) DEFAULT NULL,
  `Comment` blob,
  `Opened` smallint NOT NULL DEFAULT '0',
  `CustomerOrder` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IDX_ShoppingCart_Closed` (`Id`),
  UNIQUE KEY `UNQ_ShoppingCart_VoucherNumber` (`VoucherNumber`),
  KEY `FK_ShoppingCart_Customer` (`Customer`),
  KEY `FK_ShoppingCart_CustomerAddres` (`CustomerAddress`),
  KEY `FK_ShoppingCart_PaymentMethod` (`PaymentMethod`),
  KEY `FK_ShoppingCart_Currency` (`Currency`),
  KEY `FK_ShoppingCart_Contract` (`CustomerContract`),
  KEY `FK_ShoppingCart_TransportMode` (`TransportMode`),
  KEY `shoppingcart_Opened_CustomerContact_index` (`Opened`,`CustomerContact`),
  KEY `IDX_ShoppingCart_DeliveryDate` (`DeliveryDate`),
  KEY `IDX_ShoppingCart_DepPercent` (`DepositPercent`),
  KEY `IDX_ShoppingCart_DepValue` (`DepositValue`),
  KEY `IDX_ShoppingCart_VoucherDate` (`VoucherDate`),
  KEY `shoppingcart_pk` (`CustomerContact`),
  KEY `shoppingcart_VoucherNumber_index` (`VoucherNumber` DESC)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `shoppingcartdetail`
--

DROP TABLE IF EXISTS `shoppingcartdetail`;
CREATE TABLE IF NOT EXISTS `shoppingcartdetail` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `ShoppingCart` bigint NOT NULL,
  `Currency` bigint NOT NULL,
  `CurrencyRate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `Product` bigint DEFAULT NULL,
  `Vat` bigint DEFAULT NULL,
  `QuantityUnit` bigint DEFAULT NULL,
  `Reverse` smallint NOT NULL DEFAULT '0',
  `Quantity` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `CustomerOfferDetail` bigint DEFAULT NULL,
  `CustomerContractDetail` bigint DEFAULT NULL,
  `UnitPrice` decimal(18,4) DEFAULT NULL,
  `DiscountPercent` decimal(18,4) DEFAULT NULL,
  `DiscountUnitPrice` decimal(18,4) DEFAULT NULL,
  `GrossPrices` smallint NOT NULL DEFAULT '0',
  `DepositValue` decimal(18,4) DEFAULT NULL,
  `DepositPercent` decimal(18,4) DEFAULT NULL,
  `NetValue` decimal(18,4) DEFAULT NULL,
  `GrossValue` decimal(18,4) DEFAULT NULL,
  `VatValue` decimal(18,4) DEFAULT NULL,
  `Comment` blob,
  `CustomerOrderDetail` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `shoppingcartdetail_ShoppingCart_Id_uindex` (`ShoppingCart`,`Id`),
  UNIQUE KEY `IDX_ShoppingCartDetail_DepVal` (`DepositValue`),
  UNIQUE KEY `IDX_ShoppingCartDetail_DepPerc` (`DepositPercent`),
  KEY `FK_ShoppingCartDetail_Currency` (`Currency`),
  KEY `FK_ShoppingCartDetail_Product` (`Product`),
  KEY `FK_ShoppingCartDetail_Vat` (`Vat`),
  KEY `FK_ShoppingCartDetail_QUnit` (`QuantityUnit`),
  KEY `FK_ShoppingCartDetail_CustOffD` (`CustomerOfferDetail`),
  KEY `FK_ShoppingCartDetail_CustConD` (`CustomerContractDetail`),
  KEY `shoppingcartdetail_ShoppingCart_Id_index` (`ShoppingCart`,`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `systemsetting`
--

DROP TABLE IF EXISTS `systemsetting`;
CREATE TABLE IF NOT EXISTS `systemsetting` (
  `Id` bigint NOT NULL,
  `ProductKey` blob,
  `Company` blob,
  `Setting` longblob,
  `RowVersion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `systemsettingvalue`
--

DROP TABLE IF EXISTS `systemsettingvalue`;
CREATE TABLE IF NOT EXISTS `systemsettingvalue` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `ValueType` int NOT NULL DEFAULT '0',
  `ValueBool` smallint DEFAULT NULL,
  `ValueInt` int DEFAULT NULL,
  `ValueDecimal` decimal(18,4) DEFAULT NULL,
  `ValueDate` timestamp NULL DEFAULT NULL,
  `ValueBigInt` bigint DEFAULT NULL,
  `ValueString` varchar(100) DEFAULT NULL,
  `ValueText` blob,
  `ValueBinary` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `IDX_SystemSettingValue_N` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- T�bla szerkezet ehhez a t�bl�hoz `translations`
--

DROP TABLE IF EXISTS `translations`;
CREATE TABLE IF NOT EXISTS `translations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `huname` varchar(500) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `language` char(2) NOT NULL,
  `name` varchar(500) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translation_hu_lang` (`huname`,`language`),
  UNIQUE KEY `translation_lang_name` (`language`,`name`),
  KEY `translations_name_language_index` (`name`,`language`),
  KEY `translations_language_huname_index` (`language`,`huname`)
) ENGINE=InnoDB AUTO_INCREMENT=2035 DEFAULT CHARSET=utf32;

--
-- A t�bla adatainak ki�rat�sa `translations`
--

INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Profil', 'hu', 'Profil', NULL, NULL, NULL),
(2, 'Profil', 'en', 'Profil', NULL, NULL, NULL),
(3, 'Kil�p�s', 'hu', 'Kil�p�s', NULL, NULL, NULL),
(4, 'Kil�p�s', 'en', 'Logout', NULL, NULL, NULL),
(5, 'Kos�r', 'hu', 'Kos�r', NULL, NULL, NULL),
(6, 'Kos�r', 'en', 'Shopping cart', NULL, NULL, NULL),
(9, 'Van m�r nyitott kosara!', 'hu', 'Van m�r nyitott kosara!', NULL, NULL, NULL),
(10, 'Van m�r nyitott kosara!', 'en', 'Van m�r nyitott kosara!', NULL, NULL, NULL),
(11, 'Vez�rl�', 'hu', 'Vez�rl�', NULL, NULL, NULL),
(12, 'Vez�rl�', 'en', 'Dashboard', NULL, NULL, NULL),
(13, 'Kedvenc term�kek', 'hu', 'Kedvenc term�kek', NULL, NULL, NULL),
(14, 'Kedvenc term�kek', 'en', 'Favorite products', NULL, NULL, NULL),
(15, '�j Kos�r', 'hu', '�j Kos�r', NULL, NULL, NULL),
(16, '�j Kos�r', 'en', 'New shopping cart', NULL, NULL, NULL),
(17, 'Megrendel�sek', 'hu', 'Megrendel�sek', NULL, NULL, NULL),
(18, 'Megrendel�sek', 'en', 'Orders', NULL, NULL, NULL),
(19, 'Term�k', 'hu', 'Term�k', NULL, NULL, NULL),
(20, 'Term�k', 'en', 'Product', NULL, NULL, NULL),
(21, 'Kedvenc term�k kiv�laszt�s', 'hu', 'Kedvenc term�k kiv�laszt�s', NULL, NULL, NULL),
(22, 'Kedvenc term�k kiv�laszt�s', 'en', 'Favorite product selection', NULL, NULL, NULL),
(23, 'Term�k kateg�ria', 'hu', 'Term�k kateg�ria', NULL, NULL, NULL),
(24, 'Term�k kateg�ria', 'en', 'Product category', NULL, NULL, NULL),
(25, 'Minden term�k', 'hu', 'Minden term�k', NULL, NULL, NULL),
(26, 'Minden term�k', 'en', 'All products', NULL, NULL, NULL),
(27, 'Kedvenc', 'hu', 'Kedvenc', NULL, NULL, NULL),
(28, 'Kedvenc', 'en', 'Favorite', NULL, NULL, NULL),
(31, 'Kil�p', 'hu', 'Kil�p', NULL, NULL, NULL),
(32, 'Kil�p', 'en', 'Cancel', NULL, NULL, NULL),
(41, 'Nem jel�lt ki sort', 'hu', 'Nem jel�lt ki sort', NULL, NULL, NULL),
(42, 'Nem jel�lt ki sort', 'en', 'You have not selected a row', NULL, NULL, NULL),
(43, 'Nett�', 'hu', 'Nett�', NULL, NULL, NULL),
(44, 'Nett�', 'en', 'Net', NULL, NULL, NULL),
(45, '�FA', 'hu', '�FA', NULL, NULL, NULL),
(46, '�FA', 'en', 'VAT', NULL, NULL, NULL),
(47, 'Brutt�', 'hu', 'Brutt�', NULL, NULL, NULL),
(48, 'Brutt�', 'en', 'Gross', NULL, NULL, NULL),
(49, 'Kos�rba', 'hu', 'Kos�rba', NULL, NULL, NULL),
(50, 'Kos�rba', 'en', 'To cart', NULL, NULL, NULL),
(51, 'T�telek', 'hu', 'T�telek', NULL, NULL, NULL),
(52, 'T�telek', 'en', 'T�telek', NULL, NULL, NULL),
(53, 'Mennyis�g', 'hu', 'Mennyis�g', NULL, NULL, NULL),
(54, 'Mennyis�g', 'en', 'Mennyis�g', NULL, NULL, NULL),
(55, 'Me.egys', 'hu', 'Me.egys', NULL, NULL, NULL),
(56, 'Me.egys', 'en', 'Me.egys', NULL, NULL, NULL),
(57, 'Egys.�r', 'hu', 'Egys.�r', NULL, NULL, NULL),
(58, 'Egys.�r', 'en', 'Egys.�r', NULL, NULL, NULL),
(59, 'P�nznem', 'hu', 'P�nznem', NULL, NULL, NULL),
(60, 'P�nznem', 'en', 'P�nznem', NULL, NULL, NULL),
(61, 'Id', 'hu', 'Id', NULL, NULL, NULL),
(62, 'Id', 'en', 'Id', NULL, NULL, NULL),
(65, 'Product', 'hu', 'Product', NULL, NULL, NULL),
(67, 'T�tetek kos�rba m�sol�s!', 'hu', 'T�tetek kos�rba m�sol�s!', NULL, NULL, NULL),
(68, 'T�tetek kos�rba m�sol�s!', 'en', 'T�tetek kos�rba m�sol�s!', NULL, NULL, NULL),
(69, 'Biztosan kos�rba m�solja a t�teleket?', 'hu', 'Biztosan kos�rba m�solja a t�teleket?', NULL, NULL, NULL),
(70, 'Biztosan kos�rba m�solja a t�teleket?', 'en', 'Are you sure you want to copy the items to cart?', NULL, NULL, NULL),
(71, 'Nincs kijel�lt t�tel!', 'hu', 'Nincs kijel�lt t�tel!', NULL, NULL, NULL),
(72, 'Nincs kijel�lt t�tel!', 'en', 'Nincs kijel�lt t�tel!', NULL, NULL, NULL),
(73, '�sszes megrendel�s', 'hu', '�sszes megrendel�s', NULL, NULL, NULL),
(74, '�sszes megrendel�s', 'en', '�sszes megrendel�s', NULL, NULL, NULL),
(75, 'M�sol�s', 'hu', 'M�sol�s', NULL, NULL, NULL),
(76, 'M�sol�s', 'en', 'M�sol�s', NULL, NULL, NULL),
(77, 'Megrendel�s sz�m', 'hu', 'Megrendel�s sz�m', NULL, NULL, NULL),
(78, 'Megrendel�s sz�m', 'en', 'Megrendel�s sz�m', NULL, NULL, NULL),
(79, 'D�tum', 'hu', 'D�tum', NULL, NULL, NULL),
(80, 'D�tum', 'en', 'D�tum', NULL, NULL, NULL),
(81, 'T�tel', 'hu', 'T�tel', NULL, NULL, NULL),
(82, 'T�tel', 'en', 'T�tel', NULL, NULL, NULL),
(83, 'Idei megrendel�sek', 'hu', 'Idei megrendel�sek', NULL, NULL, NULL),
(84, 'Idei megrendel�sek', 'en', 'This year\'s orders', NULL, NULL, NULL),
(85, 'Saj�t megrendel�s', 'hu', 'Saj�t megrendel�s', NULL, NULL, NULL),
(86, 'Saj�t megrendel�s', 'en', 'Saj�t megrendel�s', NULL, NULL, NULL),
(87, 'Idei saj�t megrendel�sek', 'hu', 'Idei saj�t megrendel�sek', NULL, NULL, NULL),
(88, 'Idei saj�t megrendel�sek', 'en', 'Idei saj�t megrendel�sek', NULL, NULL, NULL),
(89, '�sszes kos�r', 'hu', '�sszes kos�r', NULL, NULL, NULL),
(90, '�sszes kos�r', 'en', '�sszes kos�r', NULL, NULL, NULL),
(91, 'Idei kos�r', 'hu', 'Idei kos�r', NULL, NULL, NULL),
(92, 'Idei kos�r', 'en', 'Idei kos�r', NULL, NULL, NULL),
(93, 'Megrendel�s kos�rba m�sol�s!', 'hu', 'Megrendel�s kos�rba m�sol�s!', NULL, NULL, NULL),
(94, 'Megrendel�s kos�rba m�sol�s!', 'en', 'Megrendel�s kos�rba m�sol�s!', NULL, NULL, NULL),
(95, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'hu', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, NULL, NULL),
(96, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'en', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, NULL, NULL),
(97, 'Idei megrendel�s', 'hu', 'Idei megrendel�s', NULL, NULL, NULL),
(98, 'Idei megrendel�s', 'en', 'Idei megrendel�s', NULL, NULL, NULL),
(103, '�sszes', 'hu', '�sszes', NULL, NULL, NULL),
(104, '�sszes', 'en', '�sszes', NULL, NULL, NULL),
(105, 'Idei', 'hu', 'Idei', NULL, NULL, NULL),
(106, 'Idei', 'en', 'Idei', NULL, NULL, NULL),
(107, 'Idei saj�t megrendel�s', 'hu', 'Idei saj�t megrendel�s', NULL, NULL, NULL),
(108, 'Idei saj�t megrendel�s', 'en', 'Idei saj�t megrendel�s', NULL, NULL, NULL),
(109, 'Felhaszn�l�k �sszesen', 'hu', 'Felhaszn�l�k �sszesen', NULL, NULL, NULL),
(110, 'Felhaszn�l�k �sszesen', 'en', 'Felhaszn�l�k �sszesen', NULL, NULL, NULL),
(111, 'felhaszn�l�k', 'hu', 'felhaszn�l�k', NULL, NULL, NULL),
(112, 'felhaszn�l�k', 'en', 'felhaszn�l�k', NULL, NULL, NULL),
(113, 'B2B partnerek', 'hu', 'B2B partnerek', NULL, NULL, NULL),
(114, 'B2B partnerek', 'en', 'B2B partnerek', NULL, NULL, NULL),
(115, 'Partner felhaszn�l�k', 'hu', 'Partner felhaszn�l�k', NULL, NULL, NULL),
(116, 'Partner felhaszn�l�k', 'en', 'Partner felhaszn�l�k', NULL, NULL, NULL),
(117, 'Tov�bb', 'hu', 'Tov�bb', NULL, NULL, NULL),
(118, 'Tov�bb', 'en', 'Tov�bb', NULL, NULL, NULL),
(119, 'Bel�p�s 3 h�nap', 'hu', 'Bel�p�s 3 h�nap', NULL, NULL, NULL),
(120, 'Bel�p�s 3 h�nap', 'en', 'Bel�p�s 3 h�nap', NULL, NULL, NULL),
(121, 'Be�ll�t�sok', 'hu', 'Be�ll�t�sok', NULL, NULL, NULL),
(122, 'Be�ll�t�sok', 'en', 'Be�ll�t�sok', NULL, NULL, NULL),
(123, 'Bel�p�s 3 h�nap<', 'hu', 'Bel�p�s 3 h�nap<', NULL, NULL, NULL),
(124, 'Bel�p�s 3 h�nap<', 'en', 'Bel�p�s 3 h�nap<', NULL, NULL, NULL),
(125, 'N�v', 'hu', 'N�v', NULL, NULL, NULL),
(126, 'N�v', 'en', 'N�v', NULL, NULL, NULL),
(127, 'Email', 'hu', 'Email', NULL, NULL, NULL),
(128, 'Email', 'en', 'Email', NULL, NULL, NULL),
(129, 'K�p', 'hu', 'K�p', NULL, NULL, NULL),
(130, 'K�p', 'en', 'K�p', NULL, NULL, NULL),
(131, 'Beoszt�s', 'hu', 'Beoszt�s', NULL, NULL, NULL),
(132, 'Beoszt�s', 'en', 'Beoszt�s', NULL, NULL, NULL),
(133, 'Bel�pett', 'hu', 'Bel�pett', NULL, NULL, NULL),
(134, 'Bel�pett', 'en', 'Bel�pett', NULL, NULL, NULL),
(135, 'B2B felhaszn�l�k', 'hu', 'B2B felhaszn�l�k', NULL, NULL, NULL),
(136, 'B2B felhaszn�l�k', 'en', 'B2B felhaszn�l�k', NULL, NULL, NULL),
(137, 'Bels� felhaszn�l�k', 'hu', 'Bels� felhaszn�l�k', NULL, NULL, NULL),
(138, 'Bels� felhaszn�l�k', 'en', 'Bels� felhaszn�l�k', NULL, NULL, NULL),
(139, 'Log adatok', 'hu', 'Log adatok', NULL, NULL, NULL),
(140, 'Log adatok', 'en', 'Log adatok', NULL, NULL, NULL),
(141, 'XML Import', 'hu', 'XML Import', NULL, NULL, NULL),
(142, 'XML Import', 'en', 'XML Import', NULL, NULL, NULL),
(143, 'rendszergazd�k', 'hu', 'rendszergazd�k', NULL, NULL, NULL),
(144, 'rendszergazd�k', 'en', 'rendszergazd�k', NULL, NULL, NULL),
(145, 'Felhaszn�l�i bel�p�sek', 'hu', 'Felhaszn�l�i bel�p�sek', NULL, NULL, NULL),
(146, 'Felhaszn�l�i bel�p�sek', 'en', 'Felhaszn�l�i bel�p�sek', NULL, NULL, NULL),
(147, 'Felhaszn�l�nk�nt', 'hu', 'Felhaszn�l�nk�nt', NULL, NULL, NULL),
(148, 'Felhaszn�l�nk�nt', 'en', 'Felhaszn�l�nk�nt', NULL, NULL, NULL),
(149, 'db', 'hu', 'db', NULL, NULL, NULL),
(150, 'db', 'en', 'db', NULL, NULL, NULL),
(151, 'Nyitott', 'hu', 'Nyitott', NULL, NULL, NULL),
(152, 'Nyitott', 'en', 'Nyitott', NULL, NULL, NULL),
(153, '�rt�k', 'hu', '�rt�k', NULL, NULL, NULL),
(154, '�rt�k', 'en', '�rt�k', NULL, NULL, NULL),
(155, 'Hitel keret', 'hu', 'Hitel keret', NULL, NULL, NULL),
(156, 'Hitel keret', 'en', 'Hitel keret', NULL, NULL, NULL),
(157, 'Felhaszn�lt', 'hu', 'Felhaszn�lt', NULL, NULL, NULL),
(158, 'Felhaszn�lt', 'en', 'Felhaszn�lt', NULL, NULL, NULL),
(159, 'Szabad', 'hu', 'Szabad', NULL, NULL, NULL),
(160, 'Szabad', 'en', 'Szabad', NULL, NULL, NULL),
(161, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'hu', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(162, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'en', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(163, 'havi bont�s', 'hu', 'havi bont�s', NULL, NULL, NULL),
(164, 'havi bont�s', 'en', 'havi bont�s', NULL, NULL, NULL),
(165, 'forint', 'hu', 'forint', NULL, NULL, NULL),
(166, 'forint', 'en', 'forint', NULL, NULL, NULL),
(167, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'hu', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(168, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'en', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(169, 'Megrendel�s darab az elm�lt 12 h�napban', 'hu', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(170, 'Megrendel�s darab az elm�lt 12 h�napban', 'en', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(171, 'darab', 'hu', 'darab', NULL, NULL, NULL),
(172, 'darab', 'en', 'darab', NULL, NULL, NULL),
(173, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'hu', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(174, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'en', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(175, 'Keres�s:', 'hu', 'Keres�s:', NULL, NULL, NULL),
(176, 'Keres�s:', 'en', 'Search:', NULL, NULL, NULL),
(177, 'Nincs rendelkez�sre �ll� adat', 'hu', 'Nincs rendelkez�sre �ll� adat', NULL, NULL, NULL),
(178, 'Nincs rendelkez�sre �ll� adat', 'en', 'Nincs rendelkez�sre �ll� adat', NULL, NULL, NULL),
(179, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'hu', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, NULL, NULL),
(180, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'en', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, NULL, NULL),
(181, 'Nulla tal�lat', 'hu', 'Nulla tal�lat', NULL, NULL, NULL),
(182, 'Nulla tal�lat', 'en', 'Nulla tal�lat', NULL, NULL, NULL),
(183, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'hu', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, NULL, NULL),
(184, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'en', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, NULL, NULL),
(185, '_MENU_ tal�lat oldalank�nt', 'hu', '_MENU_ tal�lat oldalank�nt', NULL, NULL, NULL),
(186, '_MENU_ tal�lat oldalank�nt', 'en', '_MENU_ tal�lat oldalank�nt', NULL, NULL, NULL),
(187, 'Bet�lt�s...', 'hu', 'Bet�lt�s...', NULL, NULL, NULL),
(188, 'Bet�lt�s...', 'en', 'Bet�lt�s...', NULL, NULL, NULL),
(189, 'Feldolgoz�s...', 'hu', 'Feldolgoz�s...', NULL, NULL, NULL),
(190, 'Feldolgoz�s...', 'en', 'Feldolgoz�s...', NULL, NULL, NULL),
(191, 'Nincs a keres�snek megfelel� tal�lat', 'hu', 'Nincs a keres�snek megfelel� tal�lat', NULL, NULL, NULL),
(192, 'Nincs a keres�snek megfelel� tal�lat', 'en', 'Nincs a keres�snek megfelel� tal�lat', NULL, NULL, NULL),
(193, 'Els�', 'hu', 'Els�', NULL, NULL, NULL),
(194, 'Els�', 'en', 'Els�', NULL, NULL, NULL),
(195, 'El�z�', 'hu', 'El�z�', NULL, NULL, NULL),
(196, 'El�z�', 'en', 'El�z�', NULL, NULL, NULL),
(197, 'K�vetkez�', 'hu', 'K�vetkez�', NULL, NULL, NULL),
(198, 'K�vetkez�', 'en', 'K�vetkez�', NULL, NULL, NULL),
(199, 'Utols�', 'hu', 'Utols�', NULL, NULL, NULL),
(200, 'Utols�', 'en', 'Utols�', NULL, NULL, NULL),
(201, ': aktiv�lja a n�vekv� rendez�shez', 'hu', ': aktiv�lja a n�vekv� rendez�shez', NULL, NULL, NULL),
(202, ': aktiv�lja a n�vekv� rendez�shez', 'en', ': aktiv�lja a n�vekv� rendez�shez', NULL, NULL, NULL),
(203, ': aktiv�lja a cs�kken� rendez�shez', 'hu', ': aktiv�lja a cs�kken� rendez�shez', NULL, NULL, NULL),
(204, ': aktiv�lja a cs�kken� rendez�shez', 'en', ': aktiv�lja a cs�kken� rendez�shez', NULL, NULL, NULL),
(205, '%d sor kiv�lasztva', 'hu', '%d sor kiv�lasztva', NULL, NULL, NULL),
(206, '%d sor kiv�lasztva', 'en', '%d sor kiv�lasztva', NULL, NULL, NULL),
(207, '1 sor kiv�lasztva', 'hu', '1 sor kiv�lasztva', NULL, NULL, NULL),
(208, '1 sor kiv�lasztva', 'en', '1 sor kiv�lasztva', NULL, NULL, NULL),
(209, '1 cella kiv�lasztva', 'hu', '1 cella kiv�lasztva', NULL, NULL, NULL),
(210, '1 cella kiv�lasztva', 'en', '1 cella kiv�lasztva', NULL, NULL, NULL),
(211, '%d cella kiv�lasztva', 'hu', '%d cella kiv�lasztva', NULL, NULL, NULL),
(212, '%d cella kiv�lasztva', 'en', '%d cella kiv�lasztva', NULL, NULL, NULL),
(213, '1 oszlop kiv�lasztva', 'hu', '1 oszlop kiv�lasztva', NULL, NULL, NULL),
(214, '1 oszlop kiv�lasztva', 'en', '1 oszlop kiv�lasztva', NULL, NULL, NULL),
(215, '%d oszlop kiv�lasztva', 'hu', '%d oszlop kiv�lasztva', NULL, NULL, NULL),
(216, '%d oszlop kiv�lasztva', 'en', '%d oszlop kiv�lasztva', NULL, NULL, NULL),
(217, 'Oszlopok', 'hu', 'Oszlopok', NULL, NULL, NULL),
(218, 'Oszlopok', 'en', 'Oszlopok', NULL, NULL, NULL),
(219, 'V�g�lapra m�sol�s', 'hu', 'V�g�lapra m�sol�s', NULL, NULL, NULL),
(220, 'V�g�lapra m�sol�s', 'en', 'V�g�lapra m�sol�s', NULL, NULL, NULL),
(221, '%d sor m�solva', 'hu', '%d sor m�solva', NULL, NULL, NULL),
(222, '%d sor m�solva', 'en', '%d sor m�solva', NULL, NULL, NULL),
(223, '1 sor m�solva', 'hu', '1 sor m�solva', NULL, NULL, NULL),
(224, '1 sor m�solva', 'en', '1 sor m�solva', NULL, NULL, NULL),
(225, 'Oszlopok vissza�ll�t�sa', 'hu', 'Oszlopok vissza�ll�t�sa', NULL, NULL, NULL),
(226, 'Oszlopok vissza�ll�t�sa', 'en', 'Oszlopok vissza�ll�t�sa', NULL, NULL, NULL),
(227, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'hu', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, NULL, NULL),
(228, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'en', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, NULL, NULL),
(229, '�sszes sor megjelen�t�se', 'hu', '�sszes sor megjelen�t�se', NULL, NULL, NULL),
(230, '�sszes sor megjelen�t�se', 'en', '�sszes sor megjelen�t�se', NULL, NULL, NULL),
(231, '%d sor megjelen�t�se', 'hu', '%d sor megjelen�t�se', NULL, NULL, NULL),
(232, '%d sor megjelen�t�se', 'en', '%d sor megjelen�t�se', NULL, NULL, NULL),
(233, 'Nyomtat', 'hu', 'Nyomtat', NULL, NULL, NULL),
(234, 'Nyomtat', 'en', 'Nyomtat', NULL, NULL, NULL),
(235, 'Megszak�t�s', 'hu', 'Megszak�t�s', NULL, NULL, NULL),
(236, 'Megszak�t�s', 'en', 'Megszak�t�s', NULL, NULL, NULL),
(237, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'hu', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, NULL, NULL),
(238, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'en', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, NULL, NULL),
(239, 'Cell�k v�zszintes kit�lt�se', 'hu', 'Cell�k v�zszintes kit�lt�se', NULL, NULL, NULL),
(240, 'Cell�k v�zszintes kit�lt�se', 'en', 'Cell�k v�zszintes kit�lt�se', NULL, NULL, NULL),
(241, 'Cell�k f�gg�leges kit�lt�se', 'hu', 'Cell�k f�gg�leges kit�lt�se', NULL, NULL, NULL),
(242, 'Cell�k f�gg�leges kit�lt�se', 'en', 'Cell�k f�gg�leges kit�lt�se', NULL, NULL, NULL),
(243, 'Felt�tel hozz�ad�sa', 'hu', 'Felt�tel hozz�ad�sa', NULL, NULL, NULL),
(244, 'Felt�tel hozz�ad�sa', 'en', 'Felt�tel hozz�ad�sa', NULL, NULL, NULL),
(245, 'Keres�s konfigur�tor', 'hu', 'Keres�s konfigur�tor', NULL, NULL, NULL),
(246, 'Keres�s konfigur�tor', 'en', 'Keres�s konfigur�tor', NULL, NULL, NULL),
(247, 'Keres�s konfigur�tor (%d)', 'hu', 'Keres�s konfigur�tor (%d)', NULL, NULL, NULL),
(248, 'Keres�s konfigur�tor (%d)', 'en', 'Keres�s konfigur�tor (%d)', NULL, NULL, NULL),
(249, '�sszes felt�tel t�rl�se', 'hu', '�sszes felt�tel t�rl�se', NULL, NULL, NULL),
(250, '�sszes felt�tel t�rl�se', 'en', '�sszes felt�tel t�rl�se', NULL, NULL, NULL),
(251, 'Felt�tel', 'hu', 'Felt�tel', NULL, NULL, NULL),
(252, 'Felt�tel', 'en', 'Felt�tel', NULL, NULL, NULL),
(253, 'Ut�n', 'hu', 'Ut�n', NULL, NULL, NULL),
(254, 'Ut�n', 'en', 'Ut�n', NULL, NULL, NULL),
(255, 'El�tt', 'hu', 'El�tt', NULL, NULL, NULL),
(256, 'El�tt', 'en', 'El�tt', NULL, NULL, NULL),
(257, 'K�z�tt', 'hu', 'K�z�tt', NULL, NULL, NULL),
(258, 'K�z�tt', 'en', 'K�z�tt', NULL, NULL, NULL),
(259, '�res', 'hu', '�res', NULL, NULL, NULL),
(260, '�res', 'en', '�res', NULL, NULL, NULL),
(261, 'Egyenl�', 'hu', 'Egyenl�', NULL, NULL, NULL),
(262, 'Egyenl�', 'en', 'Egyenl�', NULL, NULL, NULL),
(263, 'Nem', 'hu', 'Nem', NULL, NULL, NULL),
(264, 'Nem', 'en', 'Nem', NULL, NULL, NULL),
(265, 'K�v�l es�', 'hu', 'K�v�l es�', NULL, NULL, NULL),
(266, 'K�v�l es�', 'en', 'K�v�l es�', NULL, NULL, NULL),
(267, 'Nem �res', 'hu', 'Nem �res', NULL, NULL, NULL),
(268, 'Nem �res', 'en', 'Nem �res', NULL, NULL, NULL),
(269, 'Nagyobb mint', 'hu', 'Nagyobb mint', NULL, NULL, NULL),
(270, 'Nagyobb mint', 'en', 'Nagyobb mint', NULL, NULL, NULL),
(271, 'Nagyobb vagy egyenl� mint', 'hu', 'Nagyobb vagy egyenl� mint', NULL, NULL, NULL),
(272, 'Nagyobb vagy egyenl� mint', 'en', 'Nagyobb vagy egyenl� mint', NULL, NULL, NULL),
(273, 'Kissebb mint', 'hu', 'Kissebb mint', NULL, NULL, NULL),
(274, 'Kissebb mint', 'en', 'Kissebb mint', NULL, NULL, NULL),
(275, 'Kissebb vagy egyenl� mint', 'hu', 'Kissebb vagy egyenl� mint', NULL, NULL, NULL),
(276, 'Kissebb vagy egyenl� mint', 'en', 'Kissebb vagy egyenl� mint', NULL, NULL, NULL),
(277, 'Tartalmazza', 'hu', 'Tartalmazza', NULL, NULL, NULL),
(278, 'Tartalmazza', 'en', 'Tartalmazza', NULL, NULL, NULL),
(279, 'V�gz�dik', 'hu', 'V�gz�dik', NULL, NULL, NULL),
(280, 'V�gz�dik', 'en', 'V�gz�dik', NULL, NULL, NULL),
(281, 'Kezd�dik', 'hu', 'Kezd�dik', NULL, NULL, NULL),
(282, 'Kezd�dik', 'en', 'Kezd�dik', NULL, NULL, NULL),
(283, 'Adat', 'hu', 'Adat', NULL, NULL, NULL),
(284, 'Adat', 'en', 'Adat', NULL, NULL, NULL),
(285, 'Felt�tel t�rl�se', 'hu', 'Felt�tel t�rl�se', NULL, NULL, NULL),
(286, 'Felt�tel t�rl�se', 'en', 'Felt�tel t�rl�se', NULL, NULL, NULL),
(287, '�s', 'hu', '�s', NULL, NULL, NULL),
(288, '�s', 'en', '�s', NULL, NULL, NULL),
(289, 'Vagy', 'hu', 'Vagy', NULL, NULL, NULL),
(290, 'Vagy', 'en', 'Vagy', NULL, NULL, NULL),
(291, 'Sz�r�k t�rl�se', 'hu', 'Sz�r�k t�rl�se', NULL, NULL, NULL),
(292, 'Sz�r�k t�rl�se', 'en', 'Sz�r�k t�rl�se', NULL, NULL, NULL),
(293, 'Sz�r�panelek', 'hu', 'Sz�r�panelek', NULL, NULL, NULL),
(294, 'Sz�r�panelek', 'en', 'Sz�r�panelek', NULL, NULL, NULL),
(295, 'Sz�r�panelek (%d)', 'hu', 'Sz�r�panelek (%d)', NULL, NULL, NULL),
(296, 'Sz�r�panelek (%d)', 'en', 'Sz�r�panelek (%d)', NULL, NULL, NULL),
(297, 'Nincsenek sz�r�panelek', 'hu', 'Nincsenek sz�r�panelek', NULL, NULL, NULL),
(298, 'Nincsenek sz�r�panelek', 'en', 'Nincsenek sz�r�panelek', NULL, NULL, NULL),
(299, 'Sz�r�panelek bet�lt�se', 'hu', 'Sz�r�panelek bet�lt�se', NULL, NULL, NULL),
(300, 'Sz�r�panelek bet�lt�se', 'en', 'Sz�r�panelek bet�lt�se', NULL, NULL, NULL),
(301, 'Akt�v sz�r�panelek: %d', 'hu', 'Akt�v sz�r�panelek: %d', NULL, NULL, NULL),
(302, 'Akt�v sz�r�panelek: %d', 'en', 'Akt�v sz�r�panelek: %d', NULL, NULL, NULL),
(303, '�ra', 'hu', '�ra', NULL, NULL, NULL),
(304, '�ra', 'en', '�ra', NULL, NULL, NULL),
(305, 'Perc', 'hu', 'Perc', NULL, NULL, NULL),
(306, 'Perc', 'en', 'Perc', NULL, NULL, NULL),
(307, 'M�sodperc', 'hu', 'M�sodperc', NULL, NULL, NULL),
(308, 'M�sodperc', 'en', 'M�sodperc', NULL, NULL, NULL),
(309, 'de.', 'hu', 'de.', NULL, NULL, NULL),
(310, 'de.', 'en', 'de.', NULL, NULL, NULL),
(311, 'du.', 'hu', 'du.', NULL, NULL, NULL),
(312, 'du.', 'en', 'du.', NULL, NULL, NULL),
(313, 'Bez�r�s', 'hu', 'Bez�r�s', NULL, NULL, NULL),
(314, 'Bez�r�s', 'en', 'Bez�r�s', NULL, NULL, NULL),
(315, '�j', 'hu', '�j', NULL, NULL, NULL),
(316, '�j', 'en', '�j', NULL, NULL, NULL),
(317, 'L�trehoz�s', 'hu', 'L�trehoz�s', NULL, NULL, NULL),
(318, 'L�trehoz�s', 'en', 'L�trehoz�s', NULL, NULL, NULL),
(319, 'M�dos�t�s', 'hu', 'M�dos�t�s', NULL, NULL, NULL),
(320, 'M�dos�t�s', 'en', 'M�dos�t�s', NULL, NULL, NULL),
(321, 'T�rl�s', 'hu', 'T�rl�s', NULL, NULL, NULL),
(322, 'T�rl�s', 'en', 'T�rl�s', NULL, NULL, NULL),
(323, 'Teljes k�perny�', 'hu', 'Teljes k�perny�', NULL, NULL, NULL),
(324, 'Teljes k�perny�', 'en', 'Full screen', NULL, NULL, NULL),
(325, 'Kil�p�s a teljes k�perny�b�l', 'hu', 'Kil�p�s a teljes k�perny�b�l', NULL, NULL, NULL),
(326, 'Kil�p�s a teljes k�perny�b�l', 'en', 'Kil�p�s a teljes k�perny�b�l', NULL, NULL, NULL),
(327, 'janu�r', 'hu', 'janu�r', NULL, NULL, NULL),
(328, 'janu�r', 'en', 'janu�r', NULL, NULL, NULL),
(329, 'febru�r', 'hu', 'febru�r', NULL, NULL, NULL),
(330, 'febru�r', 'en', 'febru�r', NULL, NULL, NULL),
(331, 'm�rcius', 'hu', 'm�rcius', NULL, NULL, NULL),
(332, 'm�rcius', 'en', 'm�rcius', NULL, NULL, NULL),
(333, '�prilis', 'hu', '�prilis', NULL, NULL, NULL),
(334, '�prilis', 'en', '�prilis', NULL, NULL, NULL),
(335, 'm�jus', 'hu', 'm�jus', NULL, NULL, NULL),
(336, 'm�jus', 'en', 'm�jus', NULL, NULL, NULL),
(337, 'j�nius', 'hu', 'j�nius', NULL, NULL, NULL),
(338, 'j�nius', 'en', 'j�nius', NULL, NULL, NULL),
(339, 'j�lius', 'hu', 'j�lius', NULL, NULL, NULL),
(340, 'j�lius', 'en', 'j�lius', NULL, NULL, NULL),
(341, 'augusztus', 'hu', 'augusztus', NULL, NULL, NULL),
(342, 'augusztus', 'en', 'augusztus', NULL, NULL, NULL),
(343, 'szeptember', 'hu', 'szeptember', NULL, NULL, NULL),
(344, 'szeptember', 'en', 'szeptember', NULL, NULL, NULL),
(345, 'okt�ber', 'hu', 'okt�ber', NULL, NULL, NULL),
(346, 'okt�ber', 'en', 'okt�ber', NULL, NULL, NULL),
(347, 'november', 'hu', 'november', NULL, NULL, NULL),
(348, 'november', 'en', 'november', NULL, NULL, NULL),
(349, 'december', 'hu', 'december', NULL, NULL, NULL),
(350, 'december', 'en', 'december', NULL, NULL, NULL),
(351, 'jan', 'hu', 'jan', NULL, NULL, NULL),
(352, 'jan', 'en', 'jan', NULL, NULL, NULL),
(353, 'febr', 'hu', 'febr', NULL, NULL, NULL),
(354, 'febr', 'en', 'febr', NULL, NULL, NULL),
(355, 'm�rc', 'hu', 'm�rc', NULL, NULL, NULL),
(356, 'm�rc', 'en', 'm�rc', NULL, NULL, NULL),
(357, '�pr', 'hu', '�pr', NULL, NULL, NULL),
(358, '�pr', 'en', '�pr', NULL, NULL, NULL),
(359, 'm�j', 'hu', 'm�j', NULL, NULL, NULL),
(360, 'm�j', 'en', 'm�j', NULL, NULL, NULL),
(361, 'j�n', 'hu', 'j�n', NULL, NULL, NULL),
(362, 'j�n', 'en', 'j�n', NULL, NULL, NULL),
(363, 'j�l', 'hu', 'j�l', NULL, NULL, NULL),
(364, 'j�l', 'en', 'j�l', NULL, NULL, NULL),
(365, 'aug', 'hu', 'aug', NULL, NULL, NULL),
(366, 'aug', 'en', 'aug', NULL, NULL, NULL),
(367, 'szept', 'hu', 'szept', NULL, NULL, NULL),
(368, 'szept', 'en', 'szept', NULL, NULL, NULL),
(369, 'okt', 'hu', 'okt', NULL, NULL, NULL),
(370, 'okt', 'en', 'okt', NULL, NULL, NULL),
(371, 'nov', 'hu', 'nov', NULL, NULL, NULL),
(372, 'nov', 'en', 'nov', NULL, NULL, NULL),
(373, 'dec', 'hu', 'dec', NULL, NULL, NULL),
(374, 'dec', 'en', 'dec', NULL, NULL, NULL),
(375, 'vas�rnap', 'hu', 'vas�rnap', NULL, NULL, NULL),
(376, 'vas�rnap', 'en', 'vas�rnap', NULL, NULL, NULL),
(377, 'h�tf�', 'hu', 'h�tf�', NULL, NULL, NULL),
(378, 'h�tf�', 'en', 'h�tf�', NULL, NULL, NULL),
(379, 'kedd', 'hu', 'kedd', NULL, NULL, NULL),
(380, 'kedd', 'en', 'kedd', NULL, NULL, NULL),
(381, 'szerda', 'hu', 'szerda', NULL, NULL, NULL),
(382, 'szerda', 'en', 'szerda', NULL, NULL, NULL),
(383, 'cs�t�rt�k', 'hu', 'cs�t�rt�k', NULL, NULL, NULL),
(384, 'cs�t�rt�k', 'en', 'cs�t�rt�k', NULL, NULL, NULL),
(385, 'p�ntek', 'hu', 'p�ntek', NULL, NULL, NULL),
(386, 'p�ntek', 'en', 'p�ntek', NULL, NULL, NULL),
(387, 'szombat', 'hu', 'szombat', NULL, NULL, NULL),
(388, 'szombat', 'en', 'szombat', NULL, NULL, NULL),
(389, 'Export�l', 'hu', 'Export�l', NULL, NULL, NULL),
(390, 'Export�l', 'en', 'Export�l', NULL, NULL, NULL),
(391, 'Import�l', 'hu', 'Import�l', NULL, NULL, NULL),
(392, 'Import�l', 'en', 'Import�l', NULL, NULL, NULL),
(393, 'ett�l', 'hu', 'ett�l', NULL, NULL, NULL),
(394, 'ett�l', 'en', 'ett�l', NULL, NULL, NULL),
(395, 'eddig', 'hu', 'eddig', NULL, NULL, NULL),
(396, 'eddig', 'en', 'eddig', NULL, NULL, NULL),
(397, 'mutat:', 'hu', 'mutat:', NULL, NULL, NULL),
(398, 'mutat:', 'en', 'mutat:', NULL, NULL, NULL),
(399, 'Let�lt�s CSV filek�nt', 'hu', 'Let�lt�s CSV filek�nt', NULL, NULL, NULL),
(400, 'Let�lt�s CSV filek�nt', 'en', 'Let�lt�s CSV filek�nt', NULL, NULL, NULL),
(401, 'Let�lt�s XLS filek�nt', 'hu', 'Let�lt�s XLS filek�nt', NULL, NULL, NULL),
(402, 'Let�lt�s XLS filek�nt', 'en', 'Let�lt�s XLS filek�nt', NULL, NULL, NULL),
(403, 'Let�lt�s PNG k�pk�nt', 'hu', 'Let�lt�s PNG k�pk�nt', NULL, NULL, NULL),
(404, 'Let�lt�s PNG k�pk�nt', 'en', 'Let�lt�s PNG k�pk�nt', NULL, NULL, NULL),
(405, 'Let�lt�s JPEG k�pk�nt', 'hu', 'Let�lt�s JPEG k�pk�nt', NULL, NULL, NULL),
(406, 'Let�lt�s JPEG k�pk�nt', 'en', 'Let�lt�s JPEG k�pk�nt', NULL, NULL, NULL),
(407, 'Let�lt�s PDF dokumentumk�nt', 'hu', 'Let�lt�s PDF dokumentumk�nt', NULL, NULL, NULL),
(408, 'Let�lt�s PDF dokumentumk�nt', 'en', 'Let�lt�s PDF dokumentumk�nt', NULL, NULL, NULL),
(409, 'Let�lt�s SVG form�tumban', 'hu', 'Let�lt�s SVG form�tumban', NULL, NULL, NULL),
(410, 'Let�lt�s SVG form�tumban', 'en', 'Let�lt�s SVG form�tumban', NULL, NULL, NULL),
(411, 'Vissza�ll�t', 'hu', 'Vissza�ll�t', NULL, NULL, NULL),
(412, 'Vissza�ll�t', 'en', 'Vissza�ll�t', NULL, NULL, NULL),
(413, 'T�bl�zat', 'hu', 'T�bl�zat', NULL, NULL, NULL),
(414, 'T�bl�zat', 'en', 'T�bl�zat', NULL, NULL, NULL),
(415, 'Nyomtat�s', 'hu', 'Nyomtat�s', NULL, NULL, NULL),
(416, 'Nyomtat�s', 'en', 'Nyomtat�s', NULL, NULL, NULL),
(471, 'Bejelentkez�s', 'hu', 'Bejelentkez�s', NULL, NULL, NULL),
(472, 'Bejelentkez�s', 'en', 'Bejelentkez�s', NULL, NULL, NULL),
(473, 'Bel�p', 'hu', 'Bel�p', NULL, NULL, NULL),
(474, 'Bel�p', 'en', 'Bel�p', NULL, NULL, NULL),
(475, 'Nyelvek', 'hu', 'Nyelvek', NULL, NULL, NULL),
(476, 'Nyelvek', 'en', 'Nyelvek', NULL, NULL, NULL),
(477, 'Partner c�g', 'hu', 'Partner c�g', NULL, NULL, NULL),
(478, 'Partner c�g', 'en', 'Partner c�g', NULL, NULL, NULL),
(479, 'Telephely', 'hu', 'Telephely', NULL, NULL, NULL),
(480, 'Telephely', 'en', 'Telephely', NULL, NULL, NULL),
(481, 'Sz�ll�t�si m�d', 'hu', 'Sz�ll�t�si m�d', NULL, NULL, NULL),
(482, 'Sz�ll�t�si m�d', 'en', 'Sz�ll�t�si m�d', NULL, NULL, NULL),
(483, 'Nemzetis�g', 'hu', 'Nemzetis�g', NULL, NULL, NULL),
(484, 'Nemzetis�g', 'en', 'Nemzetis�g', NULL, NULL, NULL),
(485, '%d cella kiv�lasztva', 'ee', '%d cella kiv�lasztva', NULL, '2022-06-14 11:25:45', NULL),
(486, '%d oszlop kiv�lasztva', 'ee', '%d oszlop kiv�lasztva', NULL, NULL, NULL),
(487, '%d sor kiv�lasztva', 'ee', '%d sor kiv�lasztva', NULL, NULL, NULL),
(488, '%d sor m�solva', 'ee', '%d sor m�solva', NULL, NULL, NULL),
(489, '%d sor megjelen�t�se', 'ee', '%d sor megjelen�t�se', NULL, NULL, NULL),
(490, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'ee', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, NULL, NULL),
(491, '1 cella kiv�lasztva', 'ee', '1 cella kiv�lasztva', NULL, NULL, NULL),
(492, '1 oszlop kiv�lasztva', 'ee', '1 oszlop kiv�lasztva', NULL, NULL, NULL),
(493, '1 sor kiv�lasztva', 'ee', '1 sor kiv�lasztva', NULL, NULL, NULL),
(494, '1 sor m�solva', 'ee', '1 sor m�solva', NULL, NULL, NULL),
(495, ': aktiv�lja a cs�kken� rendez�shez', 'ee', ': aktiv�lja a cs�kken� rendez�shez', NULL, NULL, NULL),
(496, ': aktiv�lja a n�vekv� rendez�shez', 'ee', ': aktiv�lja a n�vekv� rendez�shez', NULL, NULL, NULL),
(497, 'Adat', 'ee', 'Adat', NULL, NULL, NULL),
(498, '�FA', 'ee', '�FA', NULL, NULL, NULL),
(499, 'Akt�v sz�r�panelek: %d', 'ee', 'Akt�v sz�r�panelek: %d', NULL, NULL, NULL),
(500, '�pr', 'ee', '�pr', NULL, NULL, NULL),
(501, '�prilis', 'ee', '�prilis', NULL, NULL, NULL),
(502, 'aug', 'ee', 'aug', NULL, NULL, NULL),
(503, 'augusztus', 'ee', 'augusztus', NULL, NULL, NULL),
(504, 'B2B felhaszn�l�k', 'ee', 'B2B felhaszn�l�k', NULL, NULL, NULL),
(505, 'B2B partnerek', 'ee', 'B2B partnerek', NULL, NULL, NULL),
(506, 'Be�ll�t�sok', 'ee', 'Be�ll�t�sok', NULL, NULL, NULL),
(507, 'Bejelentkez�s', 'ee', 'Bejelentkez�s', NULL, NULL, NULL),
(508, 'Bel�p', 'ee', 'Bel�p', NULL, NULL, NULL),
(509, 'Bel�p�s 3 h�nap', 'ee', 'Bel�p�s 3 h�nap', NULL, NULL, NULL),
(510, 'Bel�p�s 3 h�nap<', 'ee', 'Bel�p�s 3 h�nap<', NULL, NULL, NULL),
(511, 'Bel�pett', 'ee', 'Bel�pett', NULL, NULL, NULL),
(512, 'Bels� felhaszn�l�k', 'ee', 'Bels� felhaszn�l�k', NULL, NULL, NULL),
(513, 'Beoszt�s', 'ee', 'Beoszt�s', NULL, NULL, NULL),
(514, 'Bet�lt�s...', 'ee', 'Bet�lt�s...', NULL, NULL, NULL),
(515, 'Bez�r�s', 'ee', 'Bez�r�s', NULL, NULL, NULL),
(516, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'ee', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, NULL, NULL),
(517, 'Biztosan kos�rba m�solja a t�teleket?', 'ee', 'Biztosan kos�rba m�solja a t�teleket?', NULL, NULL, NULL),
(518, 'Brutt�', 'ee', 'Brutt�', NULL, NULL, NULL),
(519, 'Cell�k f�gg�leges kit�lt�se', 'ee', 'Cell�k f�gg�leges kit�lt�se', NULL, NULL, NULL),
(520, 'Cell�k v�zszintes kit�lt�se', 'ee', 'Cell�k v�zszintes kit�lt�se', NULL, NULL, NULL),
(521, 'cs�t�rt�k', 'ee', 'cs�t�rt�k', NULL, NULL, NULL),
(522, 'darab', 'ee', 'darab', NULL, NULL, NULL),
(523, 'D�tum', 'ee', 'D�tum', NULL, NULL, NULL),
(524, 'db', 'ee', 'db', NULL, NULL, NULL),
(525, 'de.', 'ee', 'de.', NULL, NULL, NULL),
(526, 'dec', 'ee', 'dec', NULL, NULL, NULL),
(527, 'december', 'ee', 'december', NULL, NULL, NULL),
(528, 'du.', 'ee', 'du.', NULL, NULL, NULL),
(529, 'eddig', 'ee', 'eddig', NULL, NULL, NULL),
(530, 'Egyenl�', 'ee', 'Egyenl�', NULL, NULL, NULL),
(531, 'Egys.�r', 'ee', 'Egys.�r', NULL, NULL, NULL),
(532, 'El�tt', 'ee', 'El�tt', NULL, NULL, NULL),
(533, 'El�z�', 'ee', 'El�z�', NULL, NULL, NULL),
(534, 'Els�', 'ee', 'Els�', NULL, NULL, NULL),
(535, 'Email', 'ee', 'Email', NULL, NULL, NULL),
(536, '�rt�k', 'ee', '�rt�k', NULL, NULL, NULL),
(537, '�s', 'ee', '�s', NULL, NULL, NULL),
(538, 'ett�l', 'ee', 'ett�l', NULL, NULL, NULL),
(539, 'Export�l', 'ee', 'Export�l', NULL, NULL, NULL),
(540, 'febr', 'ee', 'febr', NULL, NULL, NULL),
(541, 'febru�r', 'ee', 'febru�r', NULL, NULL, NULL),
(542, 'Feldolgoz�s...', 'ee', 'Feldolgoz�s...', NULL, NULL, NULL),
(543, 'Felhaszn�l�i bel�p�sek', 'ee', 'Felhaszn�l�i bel�p�sek', NULL, NULL, NULL),
(544, 'felhaszn�l�k', 'ee', 'felhaszn�l�k', NULL, NULL, NULL),
(545, 'Felhaszn�l�k �sszesen', 'ee', 'Felhaszn�l�k �sszesen', NULL, NULL, NULL),
(546, 'Felhaszn�l�nk�nt', 'ee', 'Felhaszn�l�nk�nt', NULL, NULL, NULL),
(547, 'Felhaszn�lt', 'ee', 'Felhaszn�lt', NULL, NULL, NULL),
(548, 'Felt�tel', 'ee', 'Felt�tel', NULL, NULL, NULL),
(549, 'Felt�tel hozz�ad�sa', 'ee', 'Felt�tel hozz�ad�sa', NULL, NULL, NULL),
(550, 'Felt�tel t�rl�se', 'ee', 'Felt�tel t�rl�se', NULL, NULL, NULL),
(551, 'forint', 'ee', 'forint', NULL, NULL, NULL),
(552, 'havi bont�s', 'ee', 'havi bont�s', NULL, NULL, NULL),
(553, 'h�tf�', 'ee', 'h�tf�', NULL, NULL, NULL),
(554, 'Hitel keret', 'ee', 'Hitel keret', NULL, NULL, NULL),
(555, 'Id', 'ee', 'Id', NULL, NULL, NULL),
(556, 'Idei', 'ee', 'Idei', NULL, NULL, NULL),
(557, 'Idei kos�r', 'ee', 'Idei kos�r', NULL, NULL, NULL),
(558, 'Idei megrendel�s', 'ee', 'Idei megrendel�s', NULL, NULL, NULL),
(559, 'Idei megrendel�sek', 'ee', 'Idei megrendel�sek', NULL, NULL, NULL),
(560, 'Idei saj�t megrendel�s', 'ee', 'Idei saj�t megrendel�s', NULL, NULL, NULL),
(561, 'Idei saj�t megrendel�sek', 'ee', 'Idei saj�t megrendel�sek', NULL, NULL, NULL),
(562, 'Import�l', 'ee', 'Import�l', NULL, NULL, NULL),
(563, 'jan', 'ee', 'jan', NULL, NULL, NULL),
(564, 'janu�r', 'ee', 'janu�r', NULL, NULL, NULL),
(565, 'j�l', 'ee', 'j�l', NULL, NULL, NULL),
(566, 'j�lius', 'ee', 'j�lius', NULL, NULL, NULL),
(567, 'j�n', 'ee', 'j�n', NULL, NULL, NULL),
(568, 'j�nius', 'ee', 'j�nius', NULL, NULL, NULL),
(569, 'kedd', 'ee', 'kedd', NULL, NULL, NULL),
(570, 'Kedvenc', 'ee', 'Kedvenc', NULL, NULL, NULL),
(571, 'Kedvenc term�k kiv�laszt�s', 'ee', 'Kedvenc term�k kiv�laszt�s', NULL, NULL, NULL),
(572, 'Kedvenc term�kek', 'ee', 'Kedvenc term�kek', NULL, NULL, NULL),
(573, 'K�p', 'ee', 'K�p', NULL, NULL, NULL),
(574, 'Keres�s konfigur�tor', 'ee', 'Keres�s konfigur�tor', NULL, NULL, NULL),
(575, 'Keres�s konfigur�tor (%d)', 'ee', 'Keres�s konfigur�tor (%d)', NULL, NULL, NULL),
(576, 'Keres�s:', 'ee', 'Keres�s:', NULL, NULL, NULL),
(577, 'Kezd�dik', 'ee', 'Kezd�dik', NULL, NULL, NULL),
(578, 'Kil�p', 'ee', 'Kil�p', NULL, NULL, NULL),
(579, 'Kil�p�s', 'ee', 'Kil�p�s', NULL, NULL, NULL),
(580, 'Kil�p�s a teljes k�perny�b�l', 'ee', 'Kil�p�s a teljes k�perny�b�l', NULL, NULL, NULL),
(581, 'Kissebb mint', 'ee', 'Kissebb mint', NULL, NULL, NULL),
(582, 'Kissebb vagy egyenl� mint', 'ee', 'Kissebb vagy egyenl� mint', NULL, NULL, NULL),
(583, 'K�v�l es�', 'ee', 'K�v�l es�', NULL, NULL, NULL),
(584, 'Kos�r', 'ee', 'Kos�r', NULL, NULL, NULL),
(585, 'Kos�rba', 'ee', 'Kos�rba', NULL, NULL, NULL),
(586, 'K�vetkez�', 'ee', 'K�vetkez�', NULL, NULL, NULL),
(587, 'K�z�tt', 'ee', 'K�z�tt', NULL, NULL, NULL),
(588, 'Let�lt�s CSV filek�nt', 'ee', 'Let�lt�s CSV filek�nt', NULL, NULL, NULL),
(589, 'Let�lt�s JPEG k�pk�nt', 'ee', 'Let�lt�s JPEG k�pk�nt', NULL, NULL, NULL),
(590, 'Let�lt�s PDF dokumentumk�nt', 'ee', 'Let�lt�s PDF dokumentumk�nt', NULL, NULL, NULL),
(591, 'Let�lt�s PNG k�pk�nt', 'ee', 'Let�lt�s PNG k�pk�nt', NULL, NULL, NULL),
(592, 'Let�lt�s SVG form�tumban', 'ee', 'Let�lt�s SVG form�tumban', NULL, NULL, NULL),
(593, 'Let�lt�s XLS filek�nt', 'ee', 'Let�lt�s XLS filek�nt', NULL, NULL, NULL),
(594, 'L�trehoz�s', 'ee', 'L�trehoz�s', NULL, NULL, NULL),
(595, 'Log adatok', 'ee', 'Log adatok', NULL, NULL, NULL),
(596, 'm�j', 'ee', 'm�j', NULL, NULL, NULL),
(597, 'm�jus', 'ee', 'm�jus', NULL, NULL, NULL),
(598, 'm�rc', 'ee', 'm�rc', NULL, NULL, NULL),
(599, 'm�rcius', 'ee', 'm�rcius', NULL, NULL, NULL),
(600, 'M�sodperc', 'ee', 'M�sodperc', NULL, NULL, NULL),
(601, 'M�sol�s', 'ee', 'M�sol�s', NULL, NULL, NULL),
(602, 'Me.egys', 'ee', 'Me.egys', NULL, NULL, NULL),
(603, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'ee', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(604, 'Megrendel�s darab az elm�lt 12 h�napban', 'ee', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(605, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'ee', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(606, 'Megrendel�s kos�rba m�sol�s!', 'ee', 'Megrendel�s kos�rba m�sol�s!', NULL, NULL, NULL),
(607, 'Megrendel�s sz�m', 'ee', 'Megrendel�s sz�m', NULL, NULL, NULL),
(608, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'ee', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(609, 'Megrendel�sek', 'ee', 'Megrendel�sek', NULL, NULL, NULL),
(610, 'Megszak�t�s', 'ee', 'Megszak�t�s', NULL, NULL, NULL),
(611, 'Mennyis�g', 'ee', 'Mennyis�g', NULL, NULL, NULL),
(612, 'Minden term�k', 'ee', 'Minden term�k', NULL, NULL, NULL),
(613, 'M�dos�t�s', 'ee', 'M�dos�t�s', NULL, NULL, NULL),
(614, 'mutat:', 'ee', 'mutat:', NULL, NULL, NULL),
(615, 'Nagyobb mint', 'ee', 'Nagyobb mint', NULL, NULL, NULL),
(616, 'Nagyobb vagy egyenl� mint', 'ee', 'Nagyobb vagy egyenl� mint', NULL, NULL, NULL),
(617, 'Nem', 'ee', 'Nem', NULL, NULL, NULL),
(618, 'Nem jel�lt ki sort', 'ee', 'Nem jel�lt ki sort', NULL, NULL, NULL),
(619, 'Nem �res', 'ee', 'Nem �res', NULL, NULL, NULL),
(620, 'Nemzetis�g', 'ee', 'Nemzetis�g', NULL, NULL, NULL),
(621, 'Nett�', 'ee', 'Nett�', NULL, NULL, NULL),
(622, 'N�v', 'ee', 'N�v', NULL, NULL, NULL),
(623, 'Nincs a keres�snek megfelel� tal�lat', 'ee', 'Nincs a keres�snek megfelel� tal�lat', NULL, NULL, NULL),
(624, 'Nincs kijel�lt t�tel!', 'ee', 'Nincs kijel�lt t�tel!', NULL, NULL, NULL),
(625, 'Nincs rendelkez�sre �ll� adat', 'ee', 'Nincs rendelkez�sre �ll� adat', NULL, NULL, NULL),
(626, 'Nincsenek sz�r�panelek', 'ee', 'Nincsenek sz�r�panelek', NULL, NULL, NULL),
(627, 'nov', 'ee', 'nov', NULL, NULL, NULL),
(628, 'november', 'ee', 'november', NULL, NULL, NULL),
(629, 'Nulla tal�lat', 'ee', 'Nulla tal�lat', NULL, NULL, NULL),
(630, 'Nyelvek', 'ee', 'Nyelvek', NULL, NULL, NULL),
(631, 'Nyitott', 'ee', 'Nyitott', NULL, NULL, NULL),
(632, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'ee', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, NULL, NULL),
(633, 'Nyomtat', 'ee', 'Nyomtat', NULL, NULL, NULL),
(634, 'Nyomtat�s', 'ee', 'Nyomtat�s', NULL, NULL, NULL),
(635, 'okt', 'ee', 'okt', NULL, NULL, NULL),
(636, 'okt�ber', 'ee', 'okt�ber', NULL, NULL, NULL),
(637, '�ra', 'ee', '�ra', NULL, NULL, NULL),
(638, '�sszes', 'ee', '�sszes', NULL, NULL, NULL),
(639, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'ee', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, NULL, NULL),
(640, '�sszes felt�tel t�rl�se', 'ee', '�sszes felt�tel t�rl�se', NULL, NULL, NULL),
(641, '�sszes kos�r', 'ee', '�sszes kos�r', NULL, NULL, NULL),
(642, '�sszes megrendel�s', 'ee', '�sszes megrendel�s', NULL, NULL, NULL),
(643, '�sszes sor megjelen�t�se', 'ee', '�sszes sor megjelen�t�se', NULL, NULL, NULL),
(644, 'Oszlopok', 'ee', 'Oszlopok', NULL, NULL, NULL),
(645, 'Oszlopok vissza�ll�t�sa', 'ee', 'Oszlopok vissza�ll�t�sa', NULL, NULL, NULL),
(646, 'Partner c�g', 'ee', 'Partner c�g', NULL, NULL, NULL),
(647, 'Partner felhaszn�l�k', 'ee', 'Partner felhaszn�l�k', NULL, NULL, NULL),
(648, 'p�ntek', 'ee', 'p�ntek', NULL, NULL, NULL),
(649, 'P�nznem', 'ee', 'P�nznem', NULL, NULL, NULL),
(650, 'Perc', 'ee', 'Perc', NULL, NULL, NULL),
(651, 'Product', 'ee', 'Product', NULL, NULL, NULL),
(652, 'Profil', 'ee', 'Profil', NULL, NULL, NULL),
(653, 'rendszergazd�k', 'ee', 'rendszergazd�k', NULL, NULL, NULL),
(654, 'Saj�t megrendel�s', 'ee', 'Saj�t megrendel�s', NULL, NULL, NULL),
(655, 'Szabad', 'ee', 'Szabad', NULL, NULL, NULL),
(656, 'Sz�ll�t�si m�d', 'ee', 'Sz�ll�t�si m�d', NULL, NULL, NULL),
(657, 'szept', 'ee', 'szept', NULL, NULL, NULL),
(658, 'szeptember', 'ee', 'szeptember', NULL, NULL, NULL),
(659, 'szerda', 'ee', 'szerda', NULL, NULL, NULL),
(660, 'szombat', 'ee', 'szombat', NULL, NULL, NULL),
(661, 'Sz�r�k t�rl�se', 'ee', 'Sz�r�k t�rl�se', NULL, NULL, NULL),
(662, 'Sz�r�panelek', 'ee', 'Sz�r�panelek', NULL, NULL, NULL),
(663, 'Sz�r�panelek (%d)', 'ee', 'Sz�r�panelek (%d)', NULL, NULL, NULL),
(664, 'Sz�r�panelek bet�lt�se', 'ee', 'Sz�r�panelek bet�lt�se', NULL, NULL, NULL),
(665, 'T�bl�zat', 'ee', 'T�bl�zat', NULL, NULL, NULL),
(666, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'ee', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, NULL, NULL),
(667, 'Tartalmazza', 'ee', 'Tartalmazza', NULL, NULL, NULL),
(668, 'Telephely', 'ee', 'Telephely', NULL, NULL, NULL),
(669, 'Teljes k�perny�', 'ee', 'Teljes k�perny�', NULL, NULL, NULL),
(670, 'Term�k', 'ee', 'Term�k', NULL, NULL, NULL),
(671, 'Term�k kateg�ria', 'ee', 'Term�k kateg�ria', NULL, NULL, NULL),
(672, 'T�tel', 'ee', 'T�tel', NULL, NULL, NULL),
(673, 'T�telek', 'ee', 'T�telek', NULL, NULL, NULL),
(674, 'T�tetek kos�rba m�sol�s!', 'ee', 'T�tetek kos�rba m�sol�s!', NULL, NULL, NULL),
(675, 'T�rl�s', 'ee', 'T�rl�s', NULL, NULL, NULL),
(676, 'Tov�bb', 'ee', 'Tov�bb', NULL, NULL, NULL),
(677, '�j', 'ee', '�j', NULL, NULL, NULL),
(678, '�j Kos�r', 'ee', '�j Kos�r', NULL, NULL, NULL),
(679, '�res', 'ee', '�res', NULL, NULL, NULL),
(680, 'Ut�n', 'ee', 'Ut�n', NULL, NULL, NULL),
(681, 'Utols�', 'ee', 'Utols�', NULL, NULL, NULL),
(682, 'V�g�lapra m�sol�s', 'ee', 'V�g�lapra m�sol�s', NULL, NULL, NULL),
(683, 'Vagy', 'ee', 'Vagy', NULL, NULL, NULL),
(684, 'Van m�r nyitott kosara!', 'ee', 'Van m�r nyitott kosara!', NULL, NULL, NULL),
(685, 'vas�rnap', 'ee', 'vas�rnap', NULL, NULL, NULL),
(686, 'V�gz�dik', 'ee', 'V�gz�dik', NULL, NULL, NULL),
(687, 'Vez�rl�', 'ee', 'Vez�rl�', NULL, NULL, NULL),
(688, 'Vissza�ll�t', 'ee', 'Vissza�ll�t', NULL, NULL, NULL),
(689, 'XML Import', 'ee', 'XML Import', NULL, NULL, NULL),
(690, '_MENU_ tal�lat oldalank�nt', 'ee', '_MENU_ tal�lat oldalank�nt', NULL, NULL, NULL),
(691, '%d cella kiv�lasztva', 'cz', '%d cella kiv�lasztva', NULL, NULL, NULL),
(692, '%d oszlop kiv�lasztva', 'cz', '%d oszlop kiv�lasztva', NULL, NULL, NULL),
(693, '%d sor kiv�lasztva', 'cz', '%d sor kiv�lasztva', NULL, '2022-06-21 05:20:40', NULL),
(694, '%d sor m�solva', 'cz', '%d sor m�solva', NULL, NULL, NULL),
(695, '%d sor megjelen�t�se', 'cz', '%d sor megjelen�t�se', NULL, '2022-06-21 05:20:46', NULL),
(696, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'cz', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, NULL, NULL),
(697, '1 cella kiv�lasztva', 'cz', '1 cella kiv�lasztva', NULL, NULL, NULL),
(698, '1 oszlop kiv�lasztva', 'cz', '1 oszlop kiv�lasztva', NULL, NULL, NULL),
(699, '1 sor kiv�lasztva', 'cz', '1 sor kiv�lasztva', NULL, NULL, NULL),
(700, '1 sor m�solva', 'cz', '1 sor m�solva', NULL, NULL, NULL),
(701, ': aktiv�lja a cs�kken� rendez�shez', 'cz', ': aktiv�lja a cs�kken� rendez�shez', NULL, NULL, NULL),
(702, ': aktiv�lja a n�vekv� rendez�shez', 'cz', ': aktiv�lja a n�vekv� rendez�shez', NULL, NULL, NULL),
(703, 'Adat', 'cz', 'Adat', NULL, NULL, NULL),
(704, '�FA', 'cz', '�FA', NULL, NULL, NULL),
(705, 'Akt�v sz�r�panelek: %d', 'cz', 'Akt�v sz�r�panelek: %d', NULL, NULL, NULL),
(706, '�pr', 'cz', '�pr', NULL, NULL, NULL),
(707, '�prilis', 'cz', '�prilis', NULL, NULL, NULL),
(708, 'aug', 'cz', 'aug', NULL, NULL, NULL),
(709, 'augusztus', 'cz', 'augusztus', NULL, NULL, NULL),
(710, 'B2B felhaszn�l�k', 'cz', 'B2B felhaszn�l�k', NULL, NULL, NULL),
(711, 'B2B partnerek', 'cz', 'B2B partnerek', NULL, NULL, NULL),
(712, 'Be�ll�t�sok', 'cz', 'Be�ll�t�sok', NULL, NULL, NULL),
(713, 'Bejelentkez�s', 'cz', 'Bejelentkez�s', NULL, NULL, NULL),
(714, 'Bel�p', 'cz', 'Bel�p', NULL, NULL, NULL),
(715, 'Bel�p�s 3 h�nap', 'cz', 'Bel�p�s 3 h�nap', NULL, NULL, NULL),
(716, 'Bel�p�s 3 h�nap<', 'cz', 'Bel�p�s 3 h�nap<', NULL, NULL, NULL),
(717, 'Bel�pett', 'cz', 'Bel�pett', NULL, NULL, NULL),
(718, 'Bels� felhaszn�l�k', 'cz', 'Bels� felhaszn�l�k', NULL, NULL, NULL),
(719, 'Beoszt�s', 'cz', 'Beoszt�s', NULL, NULL, NULL),
(720, 'Bet�lt�s...', 'cz', 'Bet�lt�s...', NULL, NULL, NULL),
(721, 'Bez�r�s', 'cz', 'Bez�r�s', NULL, NULL, NULL),
(722, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'cz', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, NULL, NULL),
(723, 'Biztosan kos�rba m�solja a t�teleket?', 'cz', 'Biztosan kos�rba m�solja a t�teleket?', NULL, NULL, NULL),
(724, 'Brutt�', 'cz', 'Brutt�', NULL, NULL, NULL),
(725, 'Cell�k f�gg�leges kit�lt�se', 'cz', 'Cell�k f�gg�leges kit�lt�se', NULL, NULL, NULL),
(726, 'Cell�k v�zszintes kit�lt�se', 'cz', 'Cell�k v�zszintes kit�lt�se', NULL, NULL, NULL),
(727, 'cs�t�rt�k', 'cz', 'cs�t�rt�k', NULL, NULL, NULL),
(728, 'darab', 'cz', 'darab', NULL, NULL, NULL),
(729, 'D�tum', 'cz', 'D�tum', NULL, NULL, NULL),
(730, 'db', 'cz', 'db', NULL, NULL, NULL),
(731, 'de.', 'cz', 'de.', NULL, NULL, NULL),
(732, 'dec', 'cz', 'dec', NULL, NULL, NULL),
(733, 'december', 'cz', 'december', NULL, NULL, NULL),
(734, 'du.', 'cz', 'du.', NULL, NULL, NULL),
(735, 'eddig', 'cz', 'eddig', NULL, NULL, NULL),
(736, 'Egyenl�', 'cz', 'Egyenl�', NULL, NULL, NULL),
(737, 'Egys.�r', 'cz', 'Egys.�r', NULL, NULL, NULL),
(738, 'El�tt', 'cz', 'El�tt', NULL, NULL, NULL),
(739, 'El�z�', 'cz', 'El�z�', NULL, NULL, NULL),
(740, 'Els�', 'cz', 'Els�', NULL, NULL, NULL),
(741, 'Email', 'cz', 'Email', NULL, NULL, NULL),
(742, '�rt�k', 'cz', '�rt�k', NULL, NULL, NULL),
(743, '�s', 'cz', '�s', NULL, NULL, NULL),
(744, 'ett�l', 'cz', 'ett�l', NULL, NULL, NULL),
(745, 'Export�l', 'cz', 'Export�l', NULL, NULL, NULL),
(746, 'febr', 'cz', 'febr', NULL, NULL, NULL),
(747, 'febru�r', 'cz', 'febru�r', NULL, NULL, NULL),
(748, 'Feldolgoz�s...', 'cz', 'Feldolgoz�s...', NULL, NULL, NULL),
(749, 'Felhaszn�l�i bel�p�sek', 'cz', 'Felhaszn�l�i bel�p�sek', NULL, NULL, NULL),
(750, 'felhaszn�l�k', 'cz', 'felhaszn�l�k', NULL, NULL, NULL),
(751, 'Felhaszn�l�k �sszesen', 'cz', 'Felhaszn�l�k �sszesen', NULL, NULL, NULL),
(752, 'Felhaszn�l�nk�nt', 'cz', 'Felhaszn�l�nk�nt', NULL, NULL, NULL),
(753, 'Felhaszn�lt', 'cz', 'Felhaszn�lt', NULL, NULL, NULL),
(754, 'Felt�tel', 'cz', 'Felt�tel', NULL, NULL, NULL),
(755, 'Felt�tel hozz�ad�sa', 'cz', 'Felt�tel hozz�ad�sa', NULL, NULL, NULL),
(756, 'Felt�tel t�rl�se', 'cz', 'Felt�tel t�rl�se', NULL, NULL, NULL),
(757, 'forint', 'cz', 'forint', NULL, NULL, NULL),
(758, 'havi bont�s', 'cz', 'havi bont�s', NULL, NULL, NULL),
(759, 'h�tf�', 'cz', 'h�tf�', NULL, NULL, NULL),
(760, 'Hitel keret', 'cz', 'Hitel keret', NULL, NULL, NULL),
(761, 'Id', 'cz', 'Id', NULL, NULL, NULL),
(762, 'Idei', 'cz', 'Idei', NULL, NULL, NULL),
(763, 'Idei kos�r', 'cz', 'Idei kos�r', NULL, NULL, NULL),
(764, 'Idei megrendel�s', 'cz', 'Idei megrendel�s', NULL, NULL, NULL),
(765, 'Idei megrendel�sek', 'cz', 'Idei megrendel�sek', NULL, NULL, NULL),
(766, 'Idei saj�t megrendel�s', 'cz', 'Idei saj�t megrendel�s', NULL, NULL, NULL),
(767, 'Idei saj�t megrendel�sek', 'cz', 'Idei saj�t megrendel�sek', NULL, NULL, NULL),
(768, 'Import�l', 'cz', 'Import�l', NULL, NULL, NULL),
(769, 'jan', 'cz', 'jan', NULL, NULL, NULL),
(770, 'janu�r', 'cz', 'janu�r', NULL, NULL, NULL),
(771, 'j�l', 'cz', 'j�l', NULL, NULL, NULL),
(772, 'j�lius', 'cz', 'j�lius', NULL, NULL, NULL),
(773, 'j�n', 'cz', 'j�n', NULL, NULL, NULL),
(774, 'j�nius', 'cz', 'j�nius', NULL, NULL, NULL),
(775, 'kedd', 'cz', 'kedd', NULL, NULL, NULL),
(776, 'Kedvenc', 'cz', 'Kedvenc', NULL, NULL, NULL),
(777, 'Kedvenc term�k kiv�laszt�s', 'cz', 'Kedvenc term�k kiv�laszt�s', NULL, NULL, NULL),
(778, 'Kedvenc term�kek', 'cz', 'Kedvenc term�kek', NULL, NULL, NULL),
(779, 'K�p', 'cz', 'K�p', NULL, NULL, NULL),
(780, 'Keres�s konfigur�tor', 'cz', 'Keres�s konfigur�tor', NULL, NULL, NULL),
(781, 'Keres�s konfigur�tor (%d)', 'cz', 'Keres�s konfigur�tor (%d)', NULL, NULL, NULL),
(782, 'Keres�s:', 'cz', 'Keres�s:', NULL, NULL, NULL),
(783, 'Kezd�dik', 'cz', 'Kezd�dik', NULL, NULL, NULL),
(784, 'Kil�p', 'cz', 'Kil�p', NULL, NULL, NULL),
(785, 'Kil�p�s', 'cz', 'Kil�p�s', NULL, NULL, NULL),
(786, 'Kil�p�s a teljes k�perny�b�l', 'cz', 'Kil�p�s a teljes k�perny�b�l', NULL, NULL, NULL),
(787, 'Kissebb mint', 'cz', 'Kissebb mint', NULL, NULL, NULL),
(788, 'Kissebb vagy egyenl� mint', 'cz', 'Kissebb vagy egyenl� mint', NULL, NULL, NULL),
(789, 'K�v�l es�', 'cz', 'K�v�l es�', NULL, NULL, NULL),
(790, 'Kos�r', 'cz', 'Kos�r', NULL, NULL, NULL),
(791, 'Kos�rba', 'cz', 'Kos�rba', NULL, NULL, NULL),
(792, 'K�vetkez�', 'cz', 'K�vetkez�', NULL, NULL, NULL),
(793, 'K�z�tt', 'cz', 'K�z�tt', NULL, NULL, NULL),
(794, 'Let�lt�s CSV filek�nt', 'cz', 'Let�lt�s CSV filek�nt', NULL, NULL, NULL),
(795, 'Let�lt�s JPEG k�pk�nt', 'cz', 'Let�lt�s JPEG k�pk�nt', NULL, NULL, NULL),
(796, 'Let�lt�s PDF dokumentumk�nt', 'cz', 'Let�lt�s PDF dokumentumk�nt', NULL, NULL, NULL),
(797, 'Let�lt�s PNG k�pk�nt', 'cz', 'Let�lt�s PNG k�pk�nt', NULL, NULL, NULL),
(798, 'Let�lt�s SVG form�tumban', 'cz', 'Let�lt�s SVG form�tumban', NULL, NULL, NULL),
(799, 'Let�lt�s XLS filek�nt', 'cz', 'Let�lt�s XLS filek�nt', NULL, NULL, NULL),
(800, 'L�trehoz�s', 'cz', 'L�trehoz�s', NULL, NULL, NULL),
(801, 'Log adatok', 'cz', 'Log adatok', NULL, NULL, NULL),
(802, 'm�j', 'cz', 'm�j', NULL, NULL, NULL),
(803, 'm�jus', 'cz', 'm�jus', NULL, NULL, NULL),
(804, 'm�rc', 'cz', 'm�rc', NULL, NULL, NULL),
(805, 'm�rcius', 'cz', 'm�rcius', NULL, NULL, NULL),
(806, 'M�sodperc', 'cz', 'M�sodperc', NULL, NULL, NULL),
(807, 'M�sol�s', 'cz', 'M�sol�s', NULL, NULL, NULL),
(808, 'Me.egys', 'cz', 'Me.egys', NULL, NULL, NULL),
(809, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'cz', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(810, 'Megrendel�s darab az elm�lt 12 h�napban', 'cz', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(811, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'cz', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(812, 'Megrendel�s kos�rba m�sol�s!', 'cz', 'Megrendel�s kos�rba m�sol�s!', NULL, NULL, NULL),
(813, 'Megrendel�s sz�m', 'cz', 'Megrendel�s sz�m', NULL, NULL, NULL),
(814, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'cz', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(815, 'Megrendel�sek', 'cz', 'Megrendel�sek', NULL, NULL, NULL),
(816, 'Megszak�t�s', 'cz', 'Megszak�t�s', NULL, NULL, NULL),
(817, 'Mennyis�g', 'cz', 'Mennyis�g', NULL, NULL, NULL),
(818, 'Minden term�k', 'cz', 'Minden term�k', NULL, NULL, NULL),
(819, 'M�dos�t�s', 'cz', 'M�dos�t�s', NULL, NULL, NULL),
(820, 'mutat:', 'cz', 'mutat:', NULL, NULL, NULL),
(821, 'Nagyobb mint', 'cz', 'Nagyobb mint', NULL, NULL, NULL),
(822, 'Nagyobb vagy egyenl� mint', 'cz', 'Nagyobb vagy egyenl� mint', NULL, NULL, NULL),
(823, 'Nem', 'cz', 'Nem', NULL, NULL, NULL),
(824, 'Nem jel�lt ki sort', 'cz', 'Nem jel�lt ki sort', NULL, NULL, NULL),
(825, 'Nem �res', 'cz', 'Nem �res', NULL, NULL, NULL),
(826, 'Nemzetis�g', 'cz', 'Nemzetis�g', NULL, NULL, NULL),
(827, 'Nett�', 'cz', 'Nett�', NULL, NULL, NULL),
(828, 'N�v', 'cz', 'N�v', NULL, NULL, NULL),
(829, 'Nincs a keres�snek megfelel� tal�lat', 'cz', 'Nincs a keres�snek megfelel� tal�lat', NULL, NULL, NULL),
(830, 'Nincs kijel�lt t�tel!', 'cz', 'Nincs kijel�lt t�tel!', NULL, NULL, NULL),
(831, 'Nincs rendelkez�sre �ll� adat', 'cz', 'Nincs rendelkez�sre �ll� adat', NULL, NULL, NULL),
(832, 'Nincsenek sz�r�panelek', 'cz', 'Nincsenek sz�r�panelek', NULL, NULL, NULL),
(833, 'nov', 'cz', 'nov', NULL, NULL, NULL),
(834, 'november', 'cz', 'november', NULL, NULL, NULL),
(835, 'Nulla tal�lat', 'cz', 'Nulla tal�lat', NULL, NULL, NULL),
(836, 'Nyelvek', 'cz', 'Nyelvek', NULL, NULL, NULL),
(837, 'Nyitott', 'cz', 'Nyitott', NULL, NULL, NULL),
(838, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'cz', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, NULL, NULL),
(839, 'Nyomtat', 'cz', 'Nyomtat', NULL, NULL, NULL),
(840, 'Nyomtat�s', 'cz', 'Nyomtat�s', NULL, NULL, NULL),
(841, 'okt', 'cz', 'okt', NULL, NULL, NULL),
(842, 'okt�ber', 'cz', 'okt�ber', NULL, NULL, NULL),
(843, '�ra', 'cz', '�ra', NULL, NULL, NULL),
(844, '�sszes', 'cz', '�sszes', NULL, NULL, NULL),
(845, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'cz', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, NULL, NULL);
INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(846, '�sszes felt�tel t�rl�se', 'cz', '�sszes felt�tel t�rl�se', NULL, NULL, NULL),
(847, '�sszes kos�r', 'cz', '�sszes kos�r', NULL, NULL, NULL),
(848, '�sszes megrendel�s', 'cz', '�sszes megrendel�s', NULL, NULL, NULL),
(849, '�sszes sor megjelen�t�se', 'cz', '�sszes sor megjelen�t�se', NULL, NULL, NULL),
(850, 'Oszlopok', 'cz', 'Oszlopok', NULL, NULL, NULL),
(851, 'Oszlopok vissza�ll�t�sa', 'cz', 'Oszlopok vissza�ll�t�sa', NULL, NULL, NULL),
(852, 'Partner c�g', 'cz', 'Partner c�g', NULL, NULL, NULL),
(853, 'Partner felhaszn�l�k', 'cz', 'Partner felhaszn�l�k', NULL, NULL, NULL),
(854, 'p�ntek', 'cz', 'p�ntek', NULL, NULL, NULL),
(855, 'P�nznem', 'cz', 'P�nznem', NULL, NULL, NULL),
(856, 'Perc', 'cz', 'Perc', NULL, NULL, NULL),
(857, 'Product', 'cz', 'Product', NULL, NULL, NULL),
(858, 'Profil', 'cz', 'Profil', NULL, NULL, NULL),
(859, 'rendszergazd�k', 'cz', 'rendszergazd�k', NULL, NULL, NULL),
(860, 'Saj�t megrendel�s', 'cz', 'Saj�t megrendel�s', NULL, NULL, NULL),
(861, 'Szabad', 'cz', 'Szabad', NULL, NULL, NULL),
(862, 'Sz�ll�t�si m�d', 'cz', 'Sz�ll�t�si m�d', NULL, NULL, NULL),
(863, 'szept', 'cz', 'szept', NULL, NULL, NULL),
(864, 'szeptember', 'cz', 'szeptember', NULL, NULL, NULL),
(865, 'szerda', 'cz', 'szerda', NULL, NULL, NULL),
(866, 'szombat', 'cz', 'szombat', NULL, NULL, NULL),
(867, 'Sz�r�k t�rl�se', 'cz', 'Sz�r�k t�rl�se', NULL, NULL, NULL),
(868, 'Sz�r�panelek', 'cz', 'Sz�r�panelek', NULL, NULL, NULL),
(869, 'Sz�r�panelek (%d)', 'cz', 'Sz�r�panelek (%d)', NULL, NULL, NULL),
(870, 'Sz�r�panelek bet�lt�se', 'cz', 'Sz�r�panelek bet�lt�se', NULL, NULL, NULL),
(871, 'T�bl�zat', 'cz', 'T�bl�zat', NULL, NULL, NULL),
(872, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'cz', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, NULL, NULL),
(873, 'Tartalmazza', 'cz', 'Tartalmazza', NULL, NULL, NULL),
(874, 'Telephely', 'cz', 'Telephely', NULL, NULL, NULL),
(875, 'Teljes k�perny�', 'cz', 'Teljes k�perny�', NULL, NULL, NULL),
(876, 'Term�k', 'cz', 'Term�k', NULL, NULL, NULL),
(877, 'Term�k kateg�ria', 'cz', 'Term�k kateg�ria', NULL, NULL, NULL),
(878, 'T�tel', 'cz', 'T�tel', NULL, NULL, NULL),
(879, 'T�telek', 'cz', 'T�telek', NULL, NULL, NULL),
(880, 'T�tetek kos�rba m�sol�s!', 'cz', 'T�tetek kos�rba m�sol�s!', NULL, NULL, NULL),
(881, 'T�rl�s', 'cz', 'T�rl�s', NULL, NULL, NULL),
(882, 'Tov�bb', 'cz', 'Tov�bb', NULL, NULL, NULL),
(883, '�j', 'cz', '�j', NULL, NULL, NULL),
(884, '�j Kos�r', 'cz', '�j Kos�r', NULL, NULL, NULL),
(885, '�res', 'cz', '�res', NULL, NULL, NULL),
(886, 'Ut�n', 'cz', 'Ut�n', NULL, NULL, NULL),
(887, 'Utols�', 'cz', 'Utols�', NULL, NULL, NULL),
(888, 'V�g�lapra m�sol�s', 'cz', 'V�g�lapra m�sol�s', NULL, NULL, NULL),
(889, 'Vagy', 'cz', 'Vagy', NULL, NULL, NULL),
(890, 'Van m�r nyitott kosara!', 'cz', 'Van m�r nyitott kosara!', NULL, NULL, NULL),
(891, 'vas�rnap', 'cz', 'vas�rnap', NULL, NULL, NULL),
(892, 'V�gz�dik', 'cz', 'V�gz�dik', NULL, NULL, NULL),
(893, 'Vez�rl�', 'cz', 'Vez�rl�', NULL, NULL, NULL),
(894, 'Vissza�ll�t', 'cz', 'Vissza�ll�t', NULL, NULL, NULL),
(895, 'XML Import', 'cz', 'XML Import', NULL, NULL, NULL),
(896, '_MENU_ tal�lat oldalank�nt', 'cz', '_MENU_ tal�lat oldalank�nt', NULL, NULL, NULL),
(897, 'Ment', 'hu', 'Ment', NULL, NULL, NULL),
(898, 'Ment', 'en', 'Ment', NULL, NULL, NULL),
(899, 'Ment', 'cz', 'Ment', NULL, NULL, NULL),
(900, 'Ment', 'ee', 'Ment', NULL, NULL, NULL),
(901, 'Hiba', 'hu', 'Hiba', NULL, NULL, NULL),
(902, 'Hiba', 'en', 'Hiba', NULL, NULL, NULL),
(903, 'Hiba', 'cz', 'Hiba', NULL, NULL, NULL),
(904, 'Hiba', 'ee', 'Hiba', NULL, NULL, NULL),
(905, 'Magyarul', 'hu', 'Magyarul', NULL, NULL, NULL),
(906, 'Magyarul', 'en', 'Magyarul', NULL, NULL, NULL),
(907, 'Magyarul', 'cz', 'Magyarul', NULL, NULL, NULL),
(908, 'Magyarul', 'ee', 'Magyarul', NULL, NULL, NULL),
(909, 'Ford�t�s', 'hu', 'Ford�t�s', NULL, NULL, NULL),
(910, 'Ford�t�s', 'en', 'Ford�t�s', NULL, NULL, NULL),
(911, 'Ford�t�s', 'cz', 'Ford�t�s', NULL, NULL, NULL),
(912, 'Ford�t�s', 'ee', 'Ford�t�s', NULL, NULL, NULL),
(913, 'Magyar', 'hu', 'Magyar', NULL, NULL, NULL),
(914, 'Magyar', 'en', 'Magyar', NULL, NULL, NULL),
(915, 'Magyar', 'cz', 'Magyar', NULL, NULL, NULL),
(916, 'Magyar', 'ee', 'Magyar', NULL, NULL, NULL),
(917, 'B2B partner', 'hu', 'B2B partner', NULL, NULL, NULL),
(918, 'B2B partner', 'en', 'B2B partner', NULL, NULL, NULL),
(919, 'B2B partner', 'cz', 'B2B partner', NULL, NULL, NULL),
(920, 'B2B partner', 'ee', 'B2B partner', NULL, NULL, NULL),
(921, 'Partner felhaszn�l�', 'hu', 'Partner felhaszn�l�', NULL, NULL, NULL),
(922, 'Partner felhaszn�l�', 'en', 'Partner felhaszn�l�', NULL, NULL, NULL),
(923, 'Partner felhaszn�l�', 'cz', 'Partner felhaszn�l�', NULL, NULL, NULL),
(924, 'Partner felhaszn�l�', 'ee', 'Partner felhaszn�l�', NULL, NULL, NULL),
(925, '%d cella kiv�lasztva', 'bg', '%d cella kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(926, '%d oszlop kiv�lasztva', 'bg', '%d oszlop kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(927, '%d sor kiv�lasztva', 'bg', '%d sor kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(928, '%d sor m�solva', 'bg', '%d sor m�solva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(929, '%d sor megjelen�t�se', 'bg', '%d sor megjelen�t�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(930, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'bg', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(931, '1 cella kiv�lasztva', 'bg', '1 cella kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(932, '1 oszlop kiv�lasztva', 'bg', '1 oszlop kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(933, '1 sor kiv�lasztva', 'bg', '1 sor kiv�lasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(934, '1 sor m�solva', 'bg', '1 sor m�solva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(935, ': aktiv�lja a cs�kken� rendez�shez', 'bg', ': aktiv�lja a cs�kken� rendez�shez', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(936, ': aktiv�lja a n�vekv� rendez�shez', 'bg', ': aktiv�lja a n�vekv� rendez�shez', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(937, 'Adat', 'bg', 'Adat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(938, '�FA', 'bg', '�FA', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(939, 'Akt�v sz�r�panelek: %d', 'bg', 'Akt�v sz�r�panelek: %d', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(940, '�pr', 'bg', '�pr', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(941, '�prilis', 'bg', '�prilis', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(942, 'aug', 'bg', 'aug', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(943, 'augusztus', 'bg', 'augusztus', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(944, 'B2B felhaszn�l�k', 'bg', 'B2B felhaszn�l�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(945, 'B2B partner', 'bg', 'B2B partner', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(946, 'B2B partnerek', 'bg', 'B2B partnerek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(947, 'Be�ll�t�sok', 'bg', 'Be�ll�t�sok', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(948, 'Bejelentkez�s', 'bg', 'Bejelentkez�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(949, 'Bel�p', 'bg', 'Bel�p', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(950, 'Bel�p�s 3 h�nap', 'bg', 'Bel�p�s 3 h�nap', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(951, 'Bel�p�s 3 h�nap<', 'bg', 'Bel�p�s 3 h�nap<', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(952, 'Bel�pett', 'bg', 'Bel�pett', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(953, 'Bels� felhaszn�l�k', 'bg', 'Bels� felhaszn�l�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(954, 'Beoszt�s', 'bg', 'Beoszt�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(955, 'Bet�lt�s...', 'bg', 'Bet�lt�s...', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(956, 'Bez�r�s', 'bg', 'Bez�r�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(957, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'bg', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(958, 'Biztosan kos�rba m�solja a t�teleket?', 'bg', 'Biztosan kos�rba m�solja a t�teleket?', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(959, 'Brutt�', 'bg', 'Brutt�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(960, 'Cell�k f�gg�leges kit�lt�se', 'bg', 'Cell�k f�gg�leges kit�lt�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(961, 'Cell�k v�zszintes kit�lt�se', 'bg', 'Cell�k v�zszintes kit�lt�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(962, 'cs�t�rt�k', 'bg', 'cs�t�rt�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(963, 'darab', 'bg', 'darab', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(964, 'D�tum', 'bg', 'D�tum', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(965, 'db', 'bg', 'db', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(966, 'de.', 'bg', 'de.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(967, 'dec', 'bg', 'dec', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(968, 'december', 'bg', 'december', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(969, 'du.', 'bg', 'du.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(970, 'eddig', 'bg', 'eddig', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(971, 'Egyenl�', 'bg', 'Egyenl�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(972, 'Egys.�r', 'bg', 'Egys.�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(973, 'El�tt', 'bg', 'El�tt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(974, 'El�z�', 'bg', 'El�z�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(975, 'Els�', 'bg', 'Els�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(976, 'Email', 'bg', 'Email', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(977, '�rt�k', 'bg', '�rt�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(978, '�s', 'bg', '�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(979, 'ett�l', 'bg', 'ett�l', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(980, 'Export�l', 'bg', 'Export�l', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(981, 'febr', 'bg', 'febr', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(982, 'febru�r', 'bg', 'febru�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(983, 'Feldolgoz�s...', 'bg', 'Feldolgoz�s...', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(984, 'Felhaszn�l�i bel�p�sek', 'bg', 'Felhaszn�l�i bel�p�sek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(985, 'felhaszn�l�k', 'bg', 'felhaszn�l�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(986, 'Felhaszn�l�k �sszesen', 'bg', 'Felhaszn�l�k �sszesen', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(987, 'Felhaszn�l�nk�nt', 'bg', 'Felhaszn�l�nk�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(988, 'Felhaszn�lt', 'bg', 'Felhaszn�lt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(989, 'Felt�tel', 'bg', 'Felt�tel', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(990, 'Felt�tel hozz�ad�sa', 'bg', 'Felt�tel hozz�ad�sa', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(991, 'Felt�tel t�rl�se', 'bg', 'Felt�tel t�rl�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(992, 'Ford�t�s', 'bg', 'Ford�t�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(993, 'forint', 'bg', 'forint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(994, 'havi bont�s', 'bg', 'havi bont�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(995, 'h�tf�', 'bg', 'h�tf�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(996, 'Hiba', 'bg', 'Hiba', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(997, 'Hitel keret', 'bg', 'Hitel keret', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(998, 'Id', 'bg', 'Id', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(999, 'Idei', 'bg', 'Idei', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1000, 'Idei kos�r', 'bg', 'Idei kos�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1001, 'Idei megrendel�s', 'bg', 'Idei megrendel�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1002, 'Idei megrendel�sek', 'bg', 'Idei megrendel�sek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1003, 'Idei saj�t megrendel�s', 'bg', 'Idei saj�t megrendel�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1004, 'Idei saj�t megrendel�sek', 'bg', 'Idei saj�t megrendel�sek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1005, 'Import�l', 'bg', 'Import�l', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1006, 'jan', 'bg', 'jan', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1007, 'janu�r', 'bg', 'janu�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1008, 'j�l', 'bg', 'j�l', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1009, 'j�lius', 'bg', 'j�lius', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1010, 'j�n', 'bg', 'j�n', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1011, 'j�nius', 'bg', 'j�nius', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1012, 'kedd', 'bg', 'kedd', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1013, 'Kedvenc', 'bg', 'Kedvenc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1014, 'Kedvenc term�k kiv�laszt�s', 'bg', 'Kedvenc term�k kiv�laszt�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1015, 'Kedvenc term�kek', 'bg', 'Kedvenc term�kek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1016, 'K�p', 'bg', 'K�p', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1017, 'Keres�s konfigur�tor', 'bg', 'Keres�s konfigur�tor', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1018, 'Keres�s konfigur�tor (%d)', 'bg', 'Keres�s konfigur�tor (%d)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1019, 'Keres�s:', 'bg', 'Keres�s:', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1020, 'Kezd�dik', 'bg', 'Kezd�dik', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1021, 'Kil�p', 'bg', 'Kil�p', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1022, 'Kil�p�s', 'bg', 'Kil�p�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1023, 'Kil�p�s a teljes k�perny�b�l', 'bg', 'Kil�p�s a teljes k�perny�b�l', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1024, 'Kissebb mint', 'bg', 'Kissebb mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1025, 'Kissebb vagy egyenl� mint', 'bg', 'Kissebb vagy egyenl� mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1026, 'K�v�l es�', 'bg', 'K�v�l es�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1027, 'Kos�r', 'bg', 'Kos�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1028, 'Kos�rba', 'bg', 'Kos�rba', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1029, 'K�vetkez�', 'bg', 'K�vetkez�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1030, 'K�z�tt', 'bg', 'K�z�tt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1031, 'Let�lt�s CSV filek�nt', 'bg', 'Let�lt�s CSV filek�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1032, 'Let�lt�s JPEG k�pk�nt', 'bg', 'Let�lt�s JPEG k�pk�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1033, 'Let�lt�s PDF dokumentumk�nt', 'bg', 'Let�lt�s PDF dokumentumk�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1034, 'Let�lt�s PNG k�pk�nt', 'bg', 'Let�lt�s PNG k�pk�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1035, 'Let�lt�s SVG form�tumban', 'bg', 'Let�lt�s SVG form�tumban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1036, 'Let�lt�s XLS filek�nt', 'bg', 'Let�lt�s XLS filek�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1037, 'L�trehoz�s', 'bg', 'L�trehoz�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1038, 'Log adatok', 'bg', 'Log adatok', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1039, 'Magyar', 'bg', 'Magyar', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1040, 'Magyarul', 'bg', 'Magyarul', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1041, 'm�j', 'bg', 'm�j', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1042, 'm�jus', 'bg', 'm�jus', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1043, 'm�rc', 'bg', 'm�rc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1044, 'm�rcius', 'bg', 'm�rcius', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1045, 'M�sodperc', 'bg', 'M�sodperc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1046, 'M�sol�s', 'bg', 'M�sol�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1047, 'Me.egys', 'bg', 'Me.egys', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1048, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'bg', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1049, 'Megrendel�s darab az elm�lt 12 h�napban', 'bg', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1050, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'bg', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1051, 'Megrendel�s kos�rba m�sol�s!', 'bg', 'Megrendel�s kos�rba m�sol�s!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1052, 'Megrendel�s sz�m', 'bg', 'Megrendel�s sz�m', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1053, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'bg', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1054, 'Megrendel�sek', 'bg', 'Megrendel�sek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1055, 'Megszak�t�s', 'bg', 'Megszak�t�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1056, 'Mennyis�g', 'bg', 'Mennyis�g', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1057, 'Ment', 'bg', 'Ment', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1058, 'Minden term�k', 'bg', 'Minden term�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1059, 'M�dos�t�s', 'bg', 'M�dos�t�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1060, 'mutat:', 'bg', 'mutat:', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1061, 'Nagyobb mint', 'bg', 'Nagyobb mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1062, 'Nagyobb vagy egyenl� mint', 'bg', 'Nagyobb vagy egyenl� mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1063, 'Nem', 'bg', 'Nem', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1064, 'Nem jel�lt ki sort', 'bg', 'Nem jel�lt ki sort', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1065, 'Nem �res', 'bg', 'Nem �res', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1066, 'Nemzetis�g', 'bg', 'Nemzetis�g', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1067, 'Nett�', 'bg', 'Nett�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1068, 'N�v', 'bg', 'N�v', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1069, 'Nincs a keres�snek megfelel� tal�lat', 'bg', 'Nincs a keres�snek megfelel� tal�lat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1070, 'Nincs kijel�lt t�tel!', 'bg', 'Nincs kijel�lt t�tel!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1071, 'Nincs rendelkez�sre �ll� adat', 'bg', 'Nincs rendelkez�sre �ll� adat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1072, 'Nincsenek sz�r�panelek', 'bg', 'Nincsenek sz�r�panelek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1073, 'nov', 'bg', 'nov', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1074, 'november', 'bg', 'november', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1075, 'Nulla tal�lat', 'bg', 'Nulla tal�lat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1076, 'Nyelvek', 'bg', 'Nyelvek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1077, 'Nyitott', 'bg', 'Nyitott', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1078, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'bg', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1079, 'Nyomtat', 'bg', 'Nyomtat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1080, 'Nyomtat�s', 'bg', 'Nyomtat�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1081, 'okt', 'bg', 'okt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1082, 'okt�ber', 'bg', 'okt�ber', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1083, '�ra', 'bg', '�ra', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1084, '�sszes', 'bg', '�sszes', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1085, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'bg', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1086, '�sszes felt�tel t�rl�se', 'bg', '�sszes felt�tel t�rl�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1087, '�sszes kos�r', 'bg', '�sszes kos�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1088, '�sszes megrendel�s', 'bg', '�sszes megrendel�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1089, '�sszes sor megjelen�t�se', 'bg', '�sszes sor megjelen�t�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1090, 'Oszlopok', 'bg', 'Oszlopok', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1091, 'Oszlopok vissza�ll�t�sa', 'bg', 'Oszlopok vissza�ll�t�sa', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1092, 'Partner c�g', 'bg', 'Partner c�g', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1093, 'Partner felhaszn�l�', 'bg', 'Partner felhaszn�l�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1094, 'Partner felhaszn�l�k', 'bg', 'Partner felhaszn�l�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1095, 'p�ntek', 'bg', 'p�ntek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1096, 'P�nznem', 'bg', 'P�nznem', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1097, 'Perc', 'bg', 'Perc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1098, 'Product', 'bg', 'Product', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1099, 'Profil', 'bg', 'Profil', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1100, 'rendszergazd�k', 'bg', 'rendszergazd�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1101, 'Saj�t megrendel�s', 'bg', 'Saj�t megrendel�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1102, 'Szabad', 'bg', 'Szabad', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1103, 'Sz�ll�t�si m�d', 'bg', 'Sz�ll�t�si m�d', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1104, 'szept', 'bg', 'szept', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1105, 'szeptember', 'bg', 'szeptember', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1106, 'szerda', 'bg', 'szerda', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1107, 'szombat', 'bg', 'szombat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1108, 'Sz�r�k t�rl�se', 'bg', 'Sz�r�k t�rl�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1109, 'Sz�r�panelek', 'bg', 'Sz�r�panelek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1110, 'Sz�r�panelek (%d)', 'bg', 'Sz�r�panelek (%d)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1111, 'Sz�r�panelek bet�lt�se', 'bg', 'Sz�r�panelek bet�lt�se', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1112, 'T�bl�zat', 'bg', 'T�bl�zat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1113, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'bg', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1114, 'Tartalmazza', 'bg', 'Tartalmazza', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1115, 'Telephely', 'bg', 'Telephely', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1116, 'Teljes k�perny�', 'bg', 'Teljes k�perny�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1117, 'Term�k', 'bg', 'Term�k', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1118, 'Term�k kateg�ria', 'bg', 'Term�k kateg�ria', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1119, 'T�tel', 'bg', 'T�tel', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1120, 'T�telek', 'bg', 'T�telek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1121, 'T�tetek kos�rba m�sol�s!', 'bg', 'T�tetek kos�rba m�sol�s!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1122, 'T�rl�s', 'bg', 'T�rl�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1123, 'Tov�bb', 'bg', 'Tov�bb', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1124, '�j', 'bg', '�j', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1125, '�j Kos�r', 'bg', '�j Kos�r', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1126, '�res', 'bg', '�res', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1127, 'Ut�n', 'bg', 'Ut�n', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1128, 'Utols�', 'bg', 'Utols�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1129, 'V�g�lapra m�sol�s', 'bg', 'V�g�lapra m�sol�s', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1130, 'Vagy', 'bg', 'Vagy', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1131, 'Van m�r nyitott kosara!', 'bg', 'Van m�r nyitott kosara!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1132, 'vas�rnap', 'bg', 'vas�rnap', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1133, 'V�gz�dik', 'bg', 'V�gz�dik', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1134, 'Vez�rl�', 'bg', 'Vez�rl�', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1135, 'Vissza�ll�t', 'bg', 'Vissza�ll�t', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1136, 'XML Import', 'bg', 'XML Import', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1137, '_MENU_ tal�lat oldalank�nt', 'bg', '_MENU_ tal�lat oldalank�nt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1138, 'Kikapcsol', 'hu', 'Kikapcsol', NULL, NULL, NULL),
(1139, 'Kikapcsol', 'en', 'Kikapcsol', NULL, NULL, NULL),
(1140, 'Kikapcsol', 'bg', 'Kikapcsol', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1141, 'Kikapcsol', 'cz', 'Kikapcsol', NULL, NULL, NULL),
(1142, 'Kikapcsol', 'ee', 'Kikapcsol', NULL, NULL, NULL),
(1143, 'Bekapcsol', 'hu', 'Bekapcsol', NULL, NULL, NULL),
(1144, 'Bekapcsol', 'en', 'Bekapcsol', NULL, NULL, NULL),
(1145, 'Bekapcsol', 'bg', 'Bekapcsol', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1146, 'Bekapcsol', 'cz', 'Bekapcsol', NULL, NULL, NULL),
(1147, 'Bekapcsol', 'ee', 'Bekapcsol', NULL, NULL, NULL),
(1148, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'hu', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1149, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'en', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1150, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'bg', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1151, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'cz', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1152, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'ee', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1153, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'hu', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1154, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'en', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1155, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'bg', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1156, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'cz', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1157, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'ee', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1158, '%d cella kiv�lasztva', 'de', '%d cella kiv�lasztva', NULL, NULL, NULL),
(1159, '%d oszlop kiv�lasztva', 'de', '%d oszlop kiv�lasztva', NULL, NULL, NULL),
(1160, '%d sor kiv�lasztva', 'de', '%d sor kiv�lasztva', NULL, NULL, NULL),
(1161, '%d sor m�solva', 'de', '%d sor m�solva', NULL, NULL, NULL),
(1162, '%d sor megjelen�t�se', 'de', '%d sor megjelen�t�se', NULL, NULL, NULL),
(1163, '(_MAX_ �sszes rekord k�z�l sz�rve)', 'de', '(_MAX_ �sszes rekord k�z�l sz�rve)', NULL, NULL, NULL),
(1164, '1 cella kiv�lasztva', 'de', '1 cella kiv�lasztva', NULL, NULL, NULL),
(1165, '1 oszlop kiv�lasztva', 'de', '1 oszlop kiv�lasztva', NULL, NULL, NULL),
(1166, '1 sor kiv�lasztva', 'de', '1 sor kiv�lasztva', NULL, NULL, NULL),
(1167, '1 sor m�solva', 'de', '1 sor m�solva', NULL, NULL, NULL),
(1168, ': aktiv�lja a cs�kken� rendez�shez', 'de', ': aktiv�lja a cs�kken� rendez�shez', NULL, NULL, NULL),
(1169, ': aktiv�lja a n�vekv� rendez�shez', 'de', ': aktiv�lja a n�vekv� rendez�shez', NULL, NULL, NULL),
(1170, 'Adat', 'de', 'Adat', NULL, NULL, NULL),
(1171, '�FA', 'de', '�FA', NULL, NULL, NULL),
(1172, 'Akt�v sz�r�panelek: %d', 'de', 'Akt�v sz�r�panelek: %d', NULL, NULL, NULL),
(1173, '�pr', 'de', '�pr', NULL, NULL, NULL),
(1174, '�prilis', 'de', '�prilis', NULL, NULL, NULL),
(1175, 'aug', 'de', 'aug', NULL, NULL, NULL),
(1176, 'augusztus', 'de', 'augusztus', NULL, NULL, NULL),
(1177, 'B2B felhaszn�l�k', 'de', 'B2B felhaszn�l�k', NULL, NULL, NULL),
(1178, 'B2B partner', 'de', 'B2B partner', NULL, NULL, NULL),
(1179, 'B2B partnerek', 'de', 'B2B partnerek', NULL, NULL, NULL),
(1180, 'Be�ll�t�sok', 'de', 'Be�ll�t�sok', NULL, NULL, NULL),
(1181, 'Bejelentkez�s', 'de', 'Bejelentkez�s', NULL, NULL, NULL),
(1182, 'Bekapcsol', 'de', 'Bekapcsol', NULL, NULL, NULL),
(1183, 'Bel�p', 'de', 'Bel�p', NULL, NULL, NULL),
(1184, 'Bel�p�s 3 h�nap', 'de', 'Bel�p�s 3 h�nap', NULL, NULL, NULL),
(1185, 'Bel�p�s 3 h�nap<', 'de', 'Bel�p�s 3 h�nap<', NULL, NULL, NULL),
(1186, 'Bel�pett', 'de', 'Bel�pett', NULL, NULL, NULL),
(1187, 'Bels� felhaszn�l�k', 'de', 'Bels� felhaszn�l�k', NULL, NULL, NULL),
(1188, 'Beoszt�s', 'de', 'Beoszt�s', NULL, NULL, NULL),
(1189, 'Bet�lt�s...', 'de', 'Bet�lt�s...', NULL, NULL, NULL),
(1190, 'Bez�r�s', 'de', 'Bez�r�s', NULL, NULL, NULL),
(1191, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'de', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1192, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'de', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1193, 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', 'de', 'Biztosan kos�rba m�solja a megrendel�s �sszes t�tel�t?', NULL, NULL, NULL),
(1194, 'Biztosan kos�rba m�solja a t�teleket?', 'de', 'Biztosan kos�rba m�solja a t�teleket?', NULL, NULL, NULL),
(1195, 'Brutt�', 'de', 'Brutt�', NULL, NULL, NULL),
(1196, 'Cell�k f�gg�leges kit�lt�se', 'de', 'Cell�k f�gg�leges kit�lt�se', NULL, NULL, NULL),
(1197, 'Cell�k v�zszintes kit�lt�se', 'de', 'Cell�k v�zszintes kit�lt�se', NULL, NULL, NULL),
(1198, 'cs�t�rt�k', 'de', 'cs�t�rt�k', NULL, NULL, NULL),
(1199, 'darab', 'de', 'darab', NULL, NULL, NULL),
(1200, 'D�tum', 'de', 'D�tum', NULL, NULL, NULL),
(1201, 'db', 'de', 'db', NULL, NULL, NULL),
(1202, 'de.', 'de', 'de.', NULL, NULL, NULL),
(1203, 'dec', 'de', 'dec', NULL, NULL, NULL),
(1204, 'december', 'de', 'december', NULL, NULL, NULL),
(1205, 'du.', 'de', 'du.', NULL, NULL, NULL),
(1206, 'eddig', 'de', 'eddig', NULL, NULL, NULL),
(1207, 'Egyenl�', 'de', 'Egyenl�', NULL, NULL, NULL),
(1208, 'Egys.�r', 'de', 'Egys.�r', NULL, NULL, NULL),
(1209, 'El�tt', 'de', 'El�tt', NULL, NULL, NULL),
(1210, 'El�z�', 'de', 'El�z�', NULL, NULL, NULL),
(1211, 'Els�', 'de', 'Els�', NULL, NULL, NULL),
(1212, 'Email', 'de', 'Email', NULL, NULL, NULL),
(1213, '�rt�k', 'de', '�rt�k', NULL, NULL, NULL),
(1214, '�s', 'de', '�s', NULL, NULL, NULL),
(1215, 'ett�l', 'de', 'ett�l', NULL, NULL, NULL),
(1216, 'Export�l', 'de', 'Export�l', NULL, NULL, NULL),
(1217, 'febr', 'de', 'febr', NULL, NULL, NULL),
(1218, 'febru�r', 'de', 'febru�r', NULL, NULL, NULL),
(1219, 'Feldolgoz�s...', 'de', 'Feldolgoz�s...', NULL, NULL, NULL),
(1220, 'Felhaszn�l�i bel�p�sek', 'de', 'Felhaszn�l�i bel�p�sek', NULL, NULL, NULL),
(1221, 'felhaszn�l�k', 'de', 'felhaszn�l�k', NULL, NULL, NULL),
(1222, 'Felhaszn�l�k �sszesen', 'de', 'Felhaszn�l�k �sszesen', NULL, NULL, NULL),
(1223, 'Felhaszn�l�nk�nt', 'de', 'Felhaszn�l�nk�nt', NULL, NULL, NULL),
(1224, 'Felhaszn�lt', 'de', 'Felhaszn�lt', NULL, NULL, NULL),
(1225, 'Felt�tel', 'de', 'Felt�tel', NULL, NULL, NULL),
(1226, 'Felt�tel hozz�ad�sa', 'de', 'Felt�tel hozz�ad�sa', NULL, NULL, NULL),
(1227, 'Felt�tel t�rl�se', 'de', 'Felt�tel t�rl�se', NULL, NULL, NULL),
(1228, 'Ford�t�s', 'de', 'Ford�t�s', NULL, NULL, NULL),
(1229, 'forint', 'de', 'forint', NULL, NULL, NULL),
(1230, 'havi bont�s', 'de', 'havi bont�s', NULL, NULL, NULL),
(1231, 'h�tf�', 'de', 'h�tf�', NULL, NULL, NULL),
(1232, 'Hiba', 'de', 'Hiba', NULL, NULL, NULL),
(1233, 'Hitel keret', 'de', 'Hitel keret', NULL, NULL, NULL),
(1234, 'Id', 'de', 'Id', NULL, NULL, NULL),
(1235, 'Idei', 'de', 'Idei', NULL, NULL, NULL),
(1236, 'Idei kos�r', 'de', 'Idei kos�r', NULL, NULL, NULL),
(1237, 'Idei megrendel�s', 'de', 'Idei megrendel�s', NULL, NULL, NULL),
(1238, 'Idei megrendel�sek', 'de', 'Idei megrendel�sek', NULL, NULL, NULL),
(1239, 'Idei saj�t megrendel�s', 'de', 'Idei saj�t megrendel�s', NULL, NULL, NULL),
(1240, 'Idei saj�t megrendel�sek', 'de', 'Idei saj�t megrendel�sek', NULL, NULL, NULL),
(1241, 'Import�l', 'de', 'Import�l', NULL, NULL, NULL),
(1242, 'jan', 'de', 'jan', NULL, NULL, NULL),
(1243, 'janu�r', 'de', 'janu�r', NULL, NULL, NULL),
(1244, 'j�l', 'de', 'j�l', NULL, NULL, NULL),
(1245, 'j�lius', 'de', 'j�lius', NULL, NULL, NULL),
(1246, 'j�n', 'de', 'j�n', NULL, NULL, NULL),
(1247, 'j�nius', 'de', 'j�nius', NULL, NULL, NULL),
(1248, 'kedd', 'de', 'kedd', NULL, NULL, NULL),
(1249, 'Kedvenc', 'de', 'Kedvenc', NULL, NULL, NULL),
(1250, 'Kedvenc term�k kiv�laszt�s', 'de', 'Kedvenc term�k kiv�laszt�s', NULL, NULL, NULL),
(1251, 'Kedvenc term�kek', 'de', 'Kedvenc term�kek', NULL, NULL, NULL),
(1252, 'K�p', 'de', 'K�p', NULL, NULL, NULL),
(1253, 'Keres�s konfigur�tor', 'de', 'Keres�s konfigur�tor', NULL, NULL, NULL),
(1254, 'Keres�s konfigur�tor (%d)', 'de', 'Keres�s konfigur�tor (%d)', NULL, NULL, NULL),
(1255, 'Keres�s:', 'de', 'Keres�s:', NULL, NULL, NULL),
(1256, 'Kezd�dik', 'de', 'Kezd�dik', NULL, NULL, NULL),
(1257, 'Kikapcsol', 'de', 'Kikapcsol', NULL, NULL, NULL),
(1258, 'Kil�p', 'de', 'Kil�p', NULL, NULL, NULL),
(1259, 'Kil�p�s', 'de', 'Kil�p�s', NULL, NULL, NULL),
(1260, 'Kil�p�s a teljes k�perny�b�l', 'de', 'Kil�p�s a teljes k�perny�b�l', NULL, NULL, NULL),
(1261, 'Kissebb mint', 'de', 'Kissebb mint', NULL, NULL, NULL),
(1262, 'Kissebb vagy egyenl� mint', 'de', 'Kissebb vagy egyenl� mint', NULL, NULL, NULL),
(1263, 'K�v�l es�', 'de', 'K�v�l es�', NULL, NULL, NULL),
(1264, 'Kos�r', 'de', 'Kos�r', NULL, NULL, NULL),
(1265, 'Kos�rba', 'de', 'Kos�rba', NULL, NULL, NULL),
(1266, 'K�vetkez�', 'de', 'K�vetkez�', NULL, NULL, NULL),
(1267, 'K�z�tt', 'de', 'K�z�tt', NULL, NULL, NULL),
(1268, 'Let�lt�s CSV filek�nt', 'de', 'Let�lt�s CSV filek�nt', NULL, NULL, NULL),
(1269, 'Let�lt�s JPEG k�pk�nt', 'de', 'Let�lt�s JPEG k�pk�nt', NULL, NULL, NULL),
(1270, 'Let�lt�s PDF dokumentumk�nt', 'de', 'Let�lt�s PDF dokumentumk�nt', NULL, NULL, NULL),
(1271, 'Let�lt�s PNG k�pk�nt', 'de', 'Let�lt�s PNG k�pk�nt', NULL, NULL, NULL),
(1272, 'Let�lt�s SVG form�tumban', 'de', 'Let�lt�s SVG form�tumban', NULL, NULL, NULL),
(1273, 'Let�lt�s XLS filek�nt', 'de', 'Let�lt�s XLS filek�nt', NULL, NULL, NULL),
(1274, 'L�trehoz�s', 'de', 'L�trehoz�s', NULL, NULL, NULL),
(1275, 'Log adatok', 'de', 'Log adatok', NULL, NULL, NULL),
(1276, 'Magyar', 'de', 'Magyar', NULL, NULL, NULL),
(1277, 'Magyarul', 'de', 'Magyarul', NULL, NULL, NULL),
(1278, 'm�j', 'de', 'm�j', NULL, NULL, NULL),
(1279, 'm�jus', 'de', 'm�jus', NULL, NULL, NULL),
(1280, 'm�rc', 'de', 'm�rc', NULL, NULL, NULL),
(1281, 'm�rcius', 'de', 'm�rcius', NULL, NULL, NULL),
(1282, 'M�sodperc', 'de', 'M�sodperc', NULL, NULL, NULL),
(1283, 'M�sol�s', 'de', 'M�sol�s', NULL, NULL, NULL),
(1284, 'Me.egys', 'de', 'Me.egys', NULL, NULL, NULL),
(1285, 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', 'de', 'Megrendel�s �tlag �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(1286, 'Megrendel�s darab az elm�lt 12 h�napban', 'de', 'Megrendel�s darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(1287, 'Megrendel�s �rt�kek az elm�lt 12 h�napban', 'de', 'Megrendel�s �rt�kek az elm�lt 12 h�napban', NULL, NULL, NULL),
(1288, 'Megrendel�s kos�rba m�sol�s!', 'de', 'Megrendel�s kos�rba m�sol�s!', NULL, NULL, NULL),
(1289, 'Megrendel�s sz�m', 'de', 'Megrendel�s sz�m', NULL, NULL, NULL),
(1290, 'Megrendel�s t�tel darab az elm�lt 12 h�napban', 'de', 'Megrendel�s t�tel darab az elm�lt 12 h�napban', NULL, NULL, NULL),
(1291, 'Megrendel�sek', 'de', 'Megrendel�sek', NULL, NULL, NULL),
(1292, 'Megszak�t�s', 'de', 'Megszak�t�s', NULL, NULL, NULL),
(1293, 'Mennyis�g', 'de', 'Mennyis�g', NULL, NULL, NULL),
(1294, 'Ment', 'de', 'Ment', NULL, NULL, NULL),
(1295, 'Minden term�k', 'de', 'Minden term�k', NULL, NULL, NULL),
(1296, 'M�dos�t�s', 'de', 'M�dos�t�s', NULL, NULL, NULL),
(1297, 'mutat:', 'de', 'mutat:', NULL, NULL, NULL),
(1298, 'Nagyobb mint', 'de', 'Nagyobb mint', NULL, NULL, NULL),
(1299, 'Nagyobb vagy egyenl� mint', 'de', 'Nagyobb vagy egyenl� mint', NULL, NULL, NULL),
(1300, 'Nem', 'de', 'Nem', NULL, NULL, NULL),
(1301, 'Nem jel�lt ki sort', 'de', 'Nem jel�lt ki sort', NULL, NULL, NULL),
(1302, 'Nem �res', 'de', 'Nem �res', NULL, NULL, NULL),
(1303, 'Nemzetis�g', 'de', 'Nemzetis�g', NULL, NULL, NULL),
(1304, 'Nett�', 'de', 'Nett�', NULL, NULL, NULL),
(1305, 'N�v', 'de', 'N�v', NULL, NULL, NULL),
(1306, 'Nincs a keres�snek megfelel� tal�lat', 'de', 'Nincs a keres�snek megfelel� tal�lat', NULL, NULL, NULL),
(1307, 'Nincs kijel�lt t�tel!', 'de', 'Nincs kijel�lt t�tel!', NULL, NULL, NULL),
(1308, 'Nincs rendelkez�sre �ll� adat', 'de', 'Nincs rendelkez�sre �ll� adat', NULL, NULL, NULL),
(1309, 'Nincsenek sz�r�panelek', 'de', 'Nincsenek sz�r�panelek', NULL, NULL, NULL),
(1310, 'nov', 'de', 'nov', NULL, NULL, NULL),
(1311, 'november', 'de', 'november', NULL, NULL, NULL),
(1312, 'Nulla tal�lat', 'de', 'Nulla tal�lat', NULL, NULL, NULL),
(1313, 'Nyelvek', 'de', 'Nyelvek', NULL, NULL, NULL),
(1314, 'Nyitott', 'de', 'Nyitott', NULL, NULL, NULL),
(1315, 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', 'de', 'Nyomja meg a CTRL vagy u2318 + C gombokat a t�bl�zat adatainak a v�g�lapra m�sol�s�hoz.<br \\/><br \\/>A megszak�t�shoz kattintson az �zenetre vagy nyomja meg az ESC billenty�t.', NULL, NULL, NULL),
(1316, 'Nyomtat', 'de', 'Nyomtat', NULL, NULL, NULL),
(1317, 'Nyomtat�s', 'de', 'Nyomtat�s', NULL, NULL, NULL),
(1318, 'okt', 'de', 'okt', NULL, NULL, NULL),
(1319, 'okt�ber', 'de', 'okt�ber', NULL, NULL, NULL),
(1320, '�ra', 'de', '�ra', NULL, NULL, NULL),
(1321, '�sszes', 'de', '�sszes', NULL, NULL, NULL),
(1322, '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', 'de', '�sszes cella kit�lt�se a k�vetkez�vel: <i>%d<\\/i>', NULL, NULL, NULL),
(1323, '�sszes felt�tel t�rl�se', 'de', '�sszes felt�tel t�rl�se', NULL, NULL, NULL),
(1324, '�sszes kos�r', 'de', '�sszes kos�r', NULL, NULL, NULL),
(1325, '�sszes megrendel�s', 'de', '�sszes megrendel�s', NULL, NULL, NULL),
(1326, '�sszes sor megjelen�t�se', 'de', '�sszes sor megjelen�t�se', NULL, NULL, NULL),
(1327, 'Oszlopok', 'de', 'Oszlopok', NULL, NULL, NULL),
(1328, 'Oszlopok vissza�ll�t�sa', 'de', 'Oszlopok vissza�ll�t�sa', NULL, NULL, NULL),
(1329, 'Partner c�g', 'de', 'Partner c�g', NULL, NULL, NULL),
(1330, 'Partner felhaszn�l�', 'de', 'Partner felhaszn�l�', NULL, NULL, NULL),
(1331, 'Partner felhaszn�l�k', 'de', 'Partner felhaszn�l�k', NULL, NULL, NULL),
(1332, 'p�ntek', 'de', 'p�ntek', NULL, NULL, NULL),
(1333, 'P�nznem', 'de', 'P�nznem', NULL, NULL, NULL),
(1334, 'Perc', 'de', 'Perc', NULL, NULL, NULL),
(1335, 'Product', 'de', 'Product', NULL, NULL, NULL),
(1336, 'Profil', 'de', 'Profil', NULL, NULL, NULL),
(1337, 'rendszergazd�k', 'de', 'rendszergazd�k', NULL, NULL, NULL),
(1338, 'Saj�t megrendel�s', 'de', 'Saj�t megrendel�s', NULL, NULL, NULL),
(1339, 'Szabad', 'de', 'Szabad', NULL, NULL, NULL),
(1340, 'Sz�ll�t�si m�d', 'de', 'Sz�ll�t�si m�d', NULL, NULL, NULL),
(1341, 'szept', 'de', 'szept', NULL, NULL, NULL),
(1342, 'szeptember', 'de', 'szeptember', NULL, NULL, NULL),
(1343, 'szerda', 'de', 'szerda', NULL, NULL, NULL),
(1344, 'szombat', 'de', 'szombat', NULL, NULL, NULL),
(1345, 'Sz�r�k t�rl�se', 'de', 'Sz�r�k t�rl�se', NULL, NULL, NULL),
(1346, 'Sz�r�panelek', 'de', 'Sz�r�panelek', NULL, NULL, NULL),
(1347, 'Sz�r�panelek (%d)', 'de', 'Sz�r�panelek (%d)', NULL, NULL, NULL),
(1348, 'Sz�r�panelek bet�lt�se', 'de', 'Sz�r�panelek bet�lt�se', NULL, NULL, NULL),
(1349, 'T�bl�zat', 'de', 'T�bl�zat', NULL, NULL, NULL),
(1350, 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', 'de', 'Tal�latok: _START_ - _END_ �sszesen: _TOTAL_', NULL, NULL, NULL),
(1351, 'Tartalmazza', 'de', 'Tartalmazza', NULL, NULL, NULL),
(1352, 'Telephely', 'de', 'Telephely', NULL, NULL, NULL),
(1353, 'Teljes k�perny�', 'de', 'Teljes k�perny�', NULL, NULL, NULL),
(1354, 'Term�k', 'de', 'Term�k', NULL, NULL, NULL),
(1355, 'Term�k kateg�ria', 'de', 'Term�k kateg�ria', NULL, NULL, NULL),
(1356, 'T�tel', 'de', 'T�tel', NULL, NULL, NULL),
(1357, 'T�telek', 'de', 'T�telek', NULL, NULL, NULL),
(1358, 'T�tetek kos�rba m�sol�s!', 'de', 'T�tetek kos�rba m�sol�s!', NULL, NULL, NULL),
(1359, 'T�rl�s', 'de', 'T�rl�s', NULL, NULL, NULL),
(1360, 'Tov�bb', 'de', 'Tov�bb', NULL, NULL, NULL),
(1361, '�j', 'de', '�j', NULL, NULL, NULL),
(1362, '�j Kos�r', 'de', '�j Kos�r', NULL, NULL, NULL),
(1363, '�res', 'de', '�res', NULL, NULL, NULL),
(1364, 'Ut�n', 'de', 'Ut�n', NULL, NULL, NULL),
(1365, 'Utols�', 'de', 'Utols�', NULL, NULL, NULL),
(1366, 'V�g�lapra m�sol�s', 'de', 'V�g�lapra m�sol�s', NULL, NULL, NULL),
(1367, 'Vagy', 'de', 'Vagy', NULL, NULL, NULL),
(1368, 'Van m�r nyitott kosara!', 'de', 'Van m�r nyitott kosara!', NULL, NULL, NULL),
(1369, 'vas�rnap', 'de', 'vas�rnap', NULL, NULL, NULL),
(1370, 'V�gz�dik', 'de', 'V�gz�dik', NULL, NULL, NULL),
(1371, 'Vez�rl�', 'de', 'Vez�rl�', NULL, NULL, NULL),
(1372, 'Vissza�ll�t', 'de', 'Vissza�ll�t', NULL, NULL, NULL),
(1373, 'XML Import', 'de', 'XML Import', NULL, NULL, NULL),
(1374, '_MENU_ tal�lat oldalank�nt', 'de', '_MENU_ tal�lat oldalank�nt', NULL, NULL, NULL),
(1375, 'Ford�tott', 'hu', 'Ford�tott', NULL, NULL, NULL),
(1376, 'Ford�tott', 'en', 'Ford�tott', NULL, NULL, NULL),
(1377, 'Ford�tott', 'de', 'Ford�tott', NULL, NULL, NULL),
(1378, 'Ford�tott', 'bg', 'Ford�tott', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1379, 'Ford�tott', 'cz', 'Ford�tott', NULL, NULL, NULL),
(1380, 'Ford�tott', 'ee', 'Ford�tott', NULL, NULL, NULL),
(1381, 'Ford�tatlan', 'hu', 'Ford�tatlan', NULL, NULL, NULL),
(1382, 'Ford�tatlan', 'en', 'Ford�tatlan', NULL, NULL, NULL),
(1383, 'Ford�tatlan', 'de', 'Ford�tatlan', NULL, NULL, NULL),
(1384, 'Ford�tatlan', 'bg', 'Ford�tatlan', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1385, 'Ford�tatlan', 'cz', 'Ford�tatlan', NULL, NULL, NULL),
(1386, 'Ford�tatlan', 'ee', 'Ford�tatlan', NULL, NULL, NULL),
(1387, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'hu', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1388, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'en', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1389, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'de', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1390, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'bg', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1391, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'cz', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1392, 'Biztos, hogy ke akarja kapcsolni a nyelvet?', 'ee', 'Biztos, hogy ke akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1393, 'Partner:', 'hu', 'Partner:', NULL, NULL, NULL),
(1394, 'Partner:', 'en', 'Partner:', NULL, NULL, NULL),
(1395, 'Partner:', 'de', 'Partner:', NULL, NULL, NULL),
(1396, 'Partner:', 'bg', 'Partner:', NULL, NULL, NULL),
(1397, 'Partner:', 'cz', 'Partner:', NULL, NULL, NULL),
(1398, 'Partner:', 'ee', 'Partner:', NULL, NULL, NULL),
(1399, 'Felhaszn�l�:', 'hu', 'Felhaszn�l�:', NULL, NULL, NULL),
(1400, 'Felhaszn�l�:', 'en', 'Felhaszn�l�:', NULL, NULL, NULL),
(1401, 'Felhaszn�l�:', 'de', 'Felhaszn�l�:', NULL, NULL, NULL),
(1402, 'Felhaszn�l�:', 'bg', 'Felhaszn�l�:', NULL, NULL, NULL),
(1403, 'Felhaszn�l�:', 'cz', 'Felhaszn�l�:', NULL, NULL, NULL),
(1404, 'Felhaszn�l�:', 'ee', 'Felhaszn�l�:', NULL, NULL, NULL),
(1405, 'Id�szak t�l:', 'hu', 'Id�szak t�l:', NULL, NULL, NULL),
(1406, 'Id�szak t�l:', 'en', 'Id�szak t�l:', NULL, NULL, NULL),
(1407, 'Id�szak t�l:', 'de', 'Id�szak t�l:', NULL, NULL, NULL),
(1408, 'Id�szak t�l:', 'bg', 'Id�szak t�l:', NULL, NULL, NULL),
(1409, 'Id�szak t�l:', 'cz', 'Id�szak t�l:', NULL, NULL, NULL),
(1410, 'Id�szak t�l:', 'ee', 'Id�szak t�l:', NULL, NULL, NULL),
(1411, 'ig:', 'hu', 'ig:', NULL, NULL, NULL),
(1412, 'ig:', 'en', 'ig:', NULL, NULL, NULL),
(1413, 'ig:', 'de', 'ig:', NULL, NULL, NULL),
(1414, 'ig:', 'bg', 'ig:', NULL, NULL, NULL),
(1415, 'ig:', 'cz', 'ig:', NULL, NULL, NULL),
(1416, 'ig:', 'ee', 'ig:', NULL, NULL, NULL),
(1417, 'Feladat', 'hu', 'Feladat', NULL, NULL, NULL),
(1418, 'Feladat', 'en', 'Feladat', NULL, NULL, NULL),
(1419, 'Feladat', 'de', 'Feladat', NULL, NULL, NULL),
(1420, 'Feladat', 'bg', 'Feladat', NULL, NULL, NULL),
(1421, 'Feladat', 'cz', 'Feladat', NULL, NULL, NULL),
(1422, 'Feladat', 'ee', 'Feladat', NULL, NULL, NULL),
(1423, 'Felhaszn�l�', 'hu', 'Felhaszn�l�', NULL, NULL, NULL),
(1424, 'Felhaszn�l�', 'en', 'Felhaszn�l�', NULL, NULL, NULL),
(1425, 'Felhaszn�l�', 'de', 'Felhaszn�l�', NULL, NULL, NULL),
(1426, 'Felhaszn�l�', 'bg', 'Felhaszn�l�', NULL, NULL, NULL),
(1427, 'Felhaszn�l�', 'cz', 'Felhaszn�l�', NULL, NULL, NULL),
(1428, 'Felhaszn�l�', 'ee', 'Felhaszn�l�', NULL, NULL, NULL),
(1429, 'Esem�ny', 'hu', 'Esem�ny', NULL, NULL, NULL),
(1430, 'Esem�ny', 'en', 'Esem�ny', NULL, NULL, NULL),
(1431, 'Esem�ny', 'de', 'Esem�ny', NULL, NULL, NULL),
(1432, 'Esem�ny', 'bg', 'Esem�ny', NULL, NULL, NULL),
(1433, 'Esem�ny', 'cz', 'Esem�ny', NULL, NULL, NULL),
(1434, 'Esem�ny', 'ee', 'Esem�ny', NULL, NULL, NULL),
(1435, 'Log adatok elm�lt 24 �ra', 'hu', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1436, 'Log adatok elm�lt 24 �ra', 'en', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1437, 'Log adatok elm�lt 24 �ra', 'de', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1438, 'Log adatok elm�lt 24 �ra', 'bg', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1439, 'Log adatok elm�lt 24 �ra', 'cz', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1440, 'Log adatok elm�lt 24 �ra', 'ee', 'Log adatok elm�lt 24 �ra', NULL, NULL, NULL),
(1441, 'Bizonylatsz�m:', 'hu', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1442, 'Bizonylatsz�m:', 'en', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1443, 'Bizonylatsz�m:', 'de', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1444, 'Bizonylatsz�m:', 'bg', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1445, 'Bizonylatsz�m:', 'cz', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1446, 'Bizonylatsz�m:', 'ee', 'Bizonylatsz�m:', NULL, NULL, NULL),
(1447, 'Fizet�si m�d:', 'hu', 'Fizet�si m�d:', NULL, NULL, NULL),
(1448, 'Fizet�si m�d:', 'en', 'Fizet�si m�d:', NULL, NULL, NULL),
(1449, 'Fizet�si m�d:', 'de', 'Fizet�si m�d:', NULL, NULL, NULL),
(1450, 'Fizet�si m�d:', 'bg', 'Fizet�si m�d:', NULL, NULL, NULL),
(1451, 'Fizet�si m�d:', 'cz', 'Fizet�si m�d:', NULL, NULL, NULL),
(1452, 'Fizet�si m�d:', 'ee', 'Fizet�si m�d:', NULL, NULL, NULL),
(1453, 'P�nznem:', 'hu', 'P�nznem:', NULL, NULL, NULL),
(1454, 'P�nznem:', 'en', 'P�nznem:', NULL, NULL, NULL),
(1455, 'P�nznem:', 'de', 'P�nznem:', NULL, NULL, NULL),
(1456, 'P�nznem:', 'bg', 'P�nznem:', NULL, NULL, NULL),
(1457, 'P�nznem:', 'cz', 'P�nznem:', NULL, NULL, NULL),
(1458, 'P�nznem:', 'ee', 'P�nznem:', NULL, NULL, NULL),
(1459, 'Telephely:', 'hu', 'Telephely:', NULL, NULL, NULL),
(1460, 'Telephely:', 'en', 'Telephely:', NULL, NULL, NULL),
(1461, 'Telephely:', 'de', 'Telephely:', NULL, NULL, NULL),
(1462, 'Telephely:', 'bg', 'Telephely:', NULL, NULL, NULL),
(1463, 'Telephely:', 'cz', 'Telephely:', NULL, NULL, NULL),
(1464, 'Telephely:', 'ee', 'Telephely:', NULL, NULL, NULL),
(1465, 'Sz�ll�t�si m�d:', 'hu', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1466, 'Sz�ll�t�si m�d:', 'en', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1467, 'Sz�ll�t�si m�d:', 'de', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1468, 'Sz�ll�t�si m�d:', 'bg', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1469, 'Sz�ll�t�si m�d:', 'cz', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1470, 'Sz�ll�t�si m�d:', 'ee', 'Sz�ll�t�si m�d:', NULL, NULL, NULL),
(1471, 'El�leg:', 'hu', 'El�leg:', NULL, NULL, NULL),
(1472, 'El�leg:', 'en', 'El�leg:', NULL, NULL, NULL),
(1473, 'El�leg:', 'de', 'El�leg:', NULL, NULL, NULL),
(1474, 'El�leg:', 'bg', 'El�leg:', NULL, NULL, NULL),
(1475, 'El�leg:', 'cz', 'El�leg:', NULL, NULL, NULL),
(1476, 'El�leg:', 'ee', 'El�leg:', NULL, NULL, NULL),
(1477, 'El�leg %:', 'hu', 'El�leg %:', NULL, NULL, NULL),
(1478, 'El�leg %:', 'en', 'El�leg %:', NULL, NULL, NULL),
(1479, 'El�leg %:', 'de', 'El�leg %:', NULL, NULL, NULL),
(1480, 'El�leg %:', 'bg', 'El�leg %:', NULL, NULL, NULL),
(1481, 'El�leg %:', 'cz', 'El�leg %:', NULL, NULL, NULL),
(1482, 'El�leg %:', 'ee', 'El�leg %:', NULL, NULL, NULL),
(1483, 'Nett� �rt�k:', 'hu', 'Nett� �rt�k:', NULL, NULL, NULL),
(1484, 'Nett� �rt�k:', 'en', 'Nett� �rt�k:', NULL, NULL, NULL),
(1485, 'Nett� �rt�k:', 'de', 'Nett� �rt�k:', NULL, NULL, NULL),
(1486, 'Nett� �rt�k:', 'bg', 'Nett� �rt�k:', NULL, NULL, NULL),
(1487, 'Nett� �rt�k:', 'cz', 'Nett� �rt�k:', NULL, NULL, NULL),
(1488, 'Nett� �rt�k:', 'ee', 'Nett� �rt�k:', NULL, NULL, NULL),
(1489, '�fa:', 'hu', '�fa:', NULL, NULL, NULL),
(1490, '�fa:', 'en', '�fa:', NULL, NULL, NULL),
(1491, '�fa:', 'de', '�fa:', NULL, NULL, NULL),
(1492, '�fa:', 'bg', '�fa:', NULL, NULL, NULL),
(1493, '�fa:', 'cz', '�fa:', NULL, NULL, NULL),
(1494, '�fa:', 'ee', '�fa:', NULL, NULL, NULL),
(1495, 'Brutt� �rt�k:', 'hu', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1496, 'Brutt� �rt�k:', 'en', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1497, 'Brutt� �rt�k:', 'de', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1498, 'Brutt� �rt�k:', 'bg', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1499, 'Brutt� �rt�k:', 'cz', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1500, 'Brutt� �rt�k:', 'ee', 'Brutt� �rt�k:', NULL, NULL, NULL),
(1501, 'Megjegyz�s:', 'hu', 'Megjegyz�s:', NULL, NULL, NULL),
(1502, 'Megjegyz�s:', 'en', 'Megjegyz�s:', NULL, NULL, NULL),
(1503, 'Megjegyz�s:', 'de', 'Megjegyz�s:', NULL, NULL, NULL);
INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1504, 'Megjegyz�s:', 'bg', 'Megjegyz�s:', NULL, NULL, NULL),
(1505, 'Megjegyz�s:', 'cz', 'Megjegyz�s:', NULL, NULL, NULL),
(1506, 'Megjegyz�s:', 'ee', 'Megjegyz�s:', NULL, NULL, NULL),
(1507, 'B2B felhaszn�l�', 'hu', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1508, 'B2B felhaszn�l�', 'en', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1509, 'B2B felhaszn�l�', 'de', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1510, 'B2B felhaszn�l�', 'bg', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1511, 'B2B felhaszn�l�', 'cz', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1512, 'B2B felhaszn�l�', 'ee', 'B2B felhaszn�l�', NULL, NULL, NULL),
(1513, 'Partner c�g:', 'hu', 'Partner c�g:', NULL, NULL, NULL),
(1514, 'Partner c�g:', 'en', 'Partner c�g:', NULL, NULL, NULL),
(1515, 'Partner c�g:', 'de', 'Partner c�g:', NULL, NULL, NULL),
(1516, 'Partner c�g:', 'bg', 'Partner c�g:', NULL, NULL, NULL),
(1517, 'Partner c�g:', 'cz', 'Partner c�g:', NULL, NULL, NULL),
(1518, 'Partner c�g:', 'ee', 'Partner c�g:', NULL, NULL, NULL),
(1519, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'hu', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1520, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'en', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1521, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'de', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1522, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'bg', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1523, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'cz', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1524, 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', 'ee', 'K�rem a Symbol �gyviteli rendszerben rendeljen a felhaszn�l�hoz email c�met!', NULL, NULL, NULL),
(1525, 'Hib�s n�v vagy jelsz�!', 'hu', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1526, 'Hib�s n�v vagy jelsz�!', 'en', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1527, 'Hib�s n�v vagy jelsz�!', 'de', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1528, 'Hib�s n�v vagy jelsz�!', 'bg', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1529, 'Hib�s n�v vagy jelsz�!', 'cz', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1530, 'Hib�s n�v vagy jelsz�!', 'ee', 'Hib�s n�v vagy jelsz�!', NULL, NULL, NULL),
(1531, 'Bizonylatsz�m', 'hu', 'Bizonylatsz�m', NULL, NULL, NULL),
(1532, 'Bizonylatsz�m', 'en', 'Bizonylatsz�m', NULL, NULL, NULL),
(1533, 'Bizonylatsz�m', 'de', 'Bizonylatsz�m', NULL, NULL, NULL),
(1534, 'Bizonylatsz�m', 'bg', 'Bizonylatsz�m', NULL, NULL, NULL),
(1535, 'Bizonylatsz�m', 'cz', 'Bizonylatsz�m', NULL, NULL, NULL),
(1536, 'Bizonylatsz�m', 'ee', 'Bizonylatsz�m', NULL, NULL, NULL),
(1537, 'Kelt', 'hu', 'Kelt', NULL, NULL, NULL),
(1538, 'Kelt', 'en', 'Kelt', NULL, NULL, NULL),
(1539, 'Kelt', 'de', 'Kelt', NULL, NULL, NULL),
(1540, 'Kelt', 'bg', 'Kelt', NULL, NULL, NULL),
(1541, 'Kelt', 'cz', 'Kelt', NULL, NULL, NULL),
(1542, 'Kelt', 'ee', 'Kelt', NULL, NULL, NULL),
(1543, 'Sz�ll.hat.', 'hu', 'Sz�ll.hat.', NULL, NULL, NULL),
(1544, 'Sz�ll.hat.', 'en', 'Sz�ll.hat.', NULL, NULL, NULL),
(1545, 'Sz�ll.hat.', 'de', 'Sz�ll.hat.', NULL, NULL, NULL),
(1546, 'Sz�ll.hat.', 'bg', 'Sz�ll.hat.', NULL, NULL, NULL),
(1547, 'Sz�ll.hat.', 'cz', 'Sz�ll.hat.', NULL, NULL, NULL),
(1548, 'Sz�ll.hat.', 'ee', 'Sz�ll.hat.', NULL, NULL, NULL),
(1549, 'Fizet�si m�d', 'hu', 'Fizet�si m�d', NULL, NULL, NULL),
(1550, 'Fizet�si m�d', 'en', 'Fizet�si m�d', NULL, NULL, NULL),
(1551, 'Fizet�si m�d', 'de', 'Fizet�si m�d', NULL, NULL, NULL),
(1552, 'Fizet�si m�d', 'bg', 'Fizet�si m�d', NULL, NULL, NULL),
(1553, 'Fizet�si m�d', 'cz', 'Fizet�si m�d', NULL, NULL, NULL),
(1554, 'Fizet�si m�d', 'ee', 'Fizet�si m�d', NULL, NULL, NULL),
(1555, 'Rendel�ssz�m', 'hu', 'Rendel�ssz�m', NULL, NULL, NULL),
(1556, 'Rendel�ssz�m', 'en', 'Rendel�ssz�m', NULL, NULL, NULL),
(1557, 'Rendel�ssz�m', 'de', 'Rendel�ssz�m', NULL, NULL, NULL),
(1558, 'Rendel�ssz�m', 'bg', 'Rendel�ssz�m', NULL, NULL, NULL),
(1559, 'Rendel�ssz�m', 'cz', 'Rendel�ssz�m', NULL, NULL, NULL),
(1560, 'Rendel�ssz�m', 'ee', 'Rendel�ssz�m', NULL, NULL, NULL),
(1561, 'Akci�s', 'hu', 'Akci�s', NULL, NULL, NULL),
(1562, 'Akci�s', 'en', 'Akci�s', NULL, NULL, NULL),
(1563, 'Akci�s', 'de', 'Akci�s', NULL, NULL, NULL),
(1564, 'Akci�s', 'bg', 'Akci�s', NULL, NULL, NULL),
(1565, 'Akci�s', 'cz', 'Akci�s', NULL, NULL, NULL),
(1566, 'Akci�s', 'ee', 'Akci�s', NULL, NULL, NULL),
(1567, 'Szerz�d�ses', 'hu', 'Szerz�d�ses', NULL, NULL, NULL),
(1568, 'Szerz�d�ses', 'en', 'Szerz�d�ses', NULL, NULL, NULL),
(1569, 'Szerz�d�ses', 'de', 'Szerz�d�ses', NULL, NULL, NULL),
(1570, 'Szerz�d�ses', 'bg', 'Szerz�d�ses', NULL, NULL, NULL),
(1571, 'Szerz�d�ses', 'cz', 'Szerz�d�ses', NULL, NULL, NULL),
(1572, 'Szerz�d�ses', 'ee', 'Szerz�d�ses', NULL, NULL, NULL),
(1573, 'Kedvencek', 'hu', 'Kedvencek', NULL, NULL, NULL),
(1574, 'Kedvencek', 'en', 'Kedvencek', NULL, NULL, NULL),
(1575, 'Kedvencek', 'de', 'Kedvencek', NULL, NULL, NULL),
(1576, 'Kedvencek', 'bg', 'Kedvencek', NULL, NULL, NULL),
(1577, 'Kedvencek', 'cz', 'Kedvencek', NULL, NULL, NULL),
(1578, 'Kedvencek', 'ee', 'Kedvencek', NULL, NULL, NULL),
(1579, 'Minden t�tel', 'hu', 'Minden t�tel', NULL, NULL, NULL),
(1580, 'Minden t�tel', 'en', 'Minden t�tel', NULL, NULL, NULL),
(1581, 'Minden t�tel', 'de', 'Minden t�tel', NULL, NULL, NULL),
(1582, 'Minden t�tel', 'bg', 'Minden t�tel', NULL, NULL, NULL),
(1583, 'Minden t�tel', 'cz', 'Minden t�tel', NULL, NULL, NULL),
(1584, 'Minden t�tel', 'ee', 'Minden t�tel', NULL, NULL, NULL),
(1585, 'K�d', 'hu', 'K�d', NULL, NULL, NULL),
(1586, 'K�d', 'en', 'K�d', NULL, NULL, NULL),
(1587, 'K�d', 'de', 'K�d', NULL, NULL, NULL),
(1588, 'K�d', 'bg', 'K�d', NULL, NULL, NULL),
(1589, 'K�d', 'cz', 'K�d', NULL, NULL, NULL),
(1590, 'K�d', 'ee', 'K�d', NULL, NULL, NULL),
(1591, 'Term�k csoport', 'hu', 'Term�k csoport', NULL, NULL, NULL),
(1592, 'Term�k csoport', 'en', 'Term�k csoport', NULL, NULL, NULL),
(1593, 'Term�k csoport', 'de', 'Term�k csoport', NULL, NULL, NULL),
(1594, 'Term�k csoport', 'bg', 'Term�k csoport', NULL, NULL, NULL),
(1595, 'Term�k csoport', 'cz', 'Term�k csoport', NULL, NULL, NULL),
(1596, 'Term�k csoport', 'ee', 'Term�k csoport', NULL, NULL, NULL),
(1597, 'Vonalk�d', 'hu', 'Vonalk�d', NULL, NULL, NULL),
(1598, 'Vonalk�d', 'en', 'Vonalk�d', NULL, NULL, NULL),
(1599, 'Vonalk�d', 'de', 'Vonalk�d', NULL, NULL, NULL),
(1600, 'Vonalk�d', 'bg', 'Vonalk�d', NULL, NULL, NULL),
(1601, 'Vonalk�d', 'cz', 'Vonalk�d', NULL, NULL, NULL),
(1602, 'Vonalk�d', 'ee', 'Vonalk�d', NULL, NULL, NULL),
(1603, 'Szerz�d�ses term�kek', 'hu', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1604, 'Szerz�d�ses term�kek', 'en', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1605, 'Szerz�d�ses term�kek', 'de', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1606, 'Szerz�d�ses term�kek', 'bg', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1607, 'Szerz�d�ses term�kek', 'cz', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1608, 'Szerz�d�ses term�kek', 'ee', 'Szerz�d�ses term�kek', NULL, NULL, NULL),
(1609, 'Akci�s term�kek', 'hu', 'Akci�s term�kek', NULL, NULL, NULL),
(1610, 'Akci�s term�kek', 'en', 'Akci�s term�kek', NULL, NULL, NULL),
(1611, 'Akci�s term�kek', 'de', 'Akci�s term�kek', NULL, NULL, NULL),
(1612, 'Akci�s term�kek', 'bg', 'Akci�s term�kek', NULL, NULL, NULL),
(1613, 'Akci�s term�kek', 'cz', 'Akci�s term�kek', NULL, NULL, NULL),
(1614, 'Akci�s term�kek', 'ee', 'Akci�s term�kek', NULL, NULL, NULL),
(1615, 'Ebben a kos�rban m�r van ilyen term�k!', 'hu', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1616, 'Ebben a kos�rban m�r van ilyen term�k!', 'en', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1617, 'Ebben a kos�rban m�r van ilyen term�k!', 'de', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1618, 'Ebben a kos�rban m�r van ilyen term�k!', 'bg', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1619, 'Ebben a kos�rban m�r van ilyen term�k!', 'cz', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1620, 'Ebben a kos�rban m�r van ilyen term�k!', 'ee', 'Ebben a kos�rban m�r van ilyen term�k!', NULL, NULL, NULL),
(1621, 'Biztosan hozz�adja ezt a mennyis�get?', 'hu', 'Biztosan hozz�adja ezt a mennyis�get?', NULL, NULL, NULL),
