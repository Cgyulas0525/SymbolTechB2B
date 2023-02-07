-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3306
-- Létrehozás ideje: 2023. Feb 07. 12:29
-- Kiszolgáló verziója: 8.0.27
-- PHP verzió: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `ujb2b`
--

DELIMITER $$
--
-- Függvények
--
DROP FUNCTION IF EXISTS `discountPercentage`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `discountPercentage` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS INT DETERMINISTIC BEGIN
	DECLARE mPercent INT;
	DECLARE mLastPrice decimal(18,4);
	DECLARE mProductPrice decimal(18,4);
    
    /* Az utolsó végfelhasználói ár */
    SET mLastPrice = getLastProductPrice($customer, $product, $quantityUnit, $currency);
    /* A kedvezményes ár */
    SET mProductPrice = getProductPrice($customer, $product, $quantity, $quantityUnit, $currency);
    /* A százalék */
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
CREATE DEFINER=`root`@`localhost` FUNCTION `getContractPrice` (`$customer` INT, `$product` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
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
CREATE DEFINER=`root`@`localhost` FUNCTION `getLastProductPrice` (`$customer` INT, `$product` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
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
CREATE DEFINER=`root`@`localhost` FUNCTION `getLastProductPriceId` (`$Product` INT, `$QuantityUnit` INT, `$PriceCategory` INT, `$Currency` INT) RETURNS INT DETERMINISTIC BEGIN
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
CREATE DEFINER=`root`@`localhost` FUNCTION `getOfferPrice` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
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
           (t3.Customer = $customer OR
            t3.CustomerCategory = (SELECT t4.CustomerCategory FROM Customer AS t4 WHERE t4.Id = $customer LIMIT 1)) AND
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
CREATE DEFINER=`root`@`localhost` FUNCTION `getProductCustomerCode` (`$customer` INT, `$product` INT) RETURNS VARCHAR(40) CHARSET utf32 DETERMINISTIC BEGIN
	DECLARE mCode VARCHAR(40);
    
    SELECT Code INTO mCode FROM productcustomercode WHERE Product = $product AND Customer = $customer;
    IF mCode IS NULL THEN
		SET mCode = "";
    END IF;
	RETURN mCode;
END$$

DROP FUNCTION IF EXISTS `getProductPrice`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `getProductPrice` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS DECIMAL(18,4) DETERMINISTIC BEGIN
	DECLARE mPrice decimal(18,4);
	DECLARE mOfferPrice decimal(18,4);
	DECLARE mLastPrice decimal(18,4);
	DECLARE mContractPrice decimal(18,4);
    
    /* Az utolsó végfelhasználói ár */
    SET mLastPrice = getLastProductPrice($customer, $product, $quantityUnit, $currency);
    IF mLastPrice > 0 THEN
		SET mPrice = mLastPrice;
    END IF;
    
    /* Az akciós ár */
    SET mOfferPrice = getOfferPrice($customer, $product, $quantity, $quantityUnit, $currency);
    IF mOfferPrice != 0 THEN
		IF mOfferPrice < mPrice THEN
			SET mPrice = mOfferPrice;
        END IF;
    END IF;
    
    /* A szerződéses ár */
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
-- Tábla szerkezet ehhez a táblához `api`
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
-- Tábla szerkezet ehhez a táblához `apimodel`
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
-- Tábla szerkezet ehhez a táblához `apimodelerror`
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
-- Tábla szerkezet ehhez a táblához `currency`
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
-- Tábla szerkezet ehhez a táblához `currencyrate`
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
-- Tábla szerkezet ehhez a táblához `customer`
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
-- Tábla szerkezet ehhez a táblához `customeraddress`
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
-- Tábla szerkezet ehhez a táblához `customercategory`
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
-- Tábla szerkezet ehhez a táblához `customercontact`
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
-- Tábla szerkezet ehhez a táblához `customercontactfavoriteproduct`
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
-- Tábla szerkezet ehhez a táblához `customercontract`
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
-- Tábla szerkezet ehhez a táblához `customercontractdetail`
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
-- Tábla szerkezet ehhez a táblához `customeroffer`
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
-- Tábla szerkezet ehhez a táblához `customeroffercustomer`
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
-- Tábla szerkezet ehhez a táblához `customerofferdetail`
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
-- Tábla szerkezet ehhez a táblához `customerorder`
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
-- Tábla szerkezet ehhez a táblához `customerorderdetail`
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
-- Tábla szerkezet ehhez a táblához `customerorderdetailstatus`
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
-- Tábla szerkezet ehhez a táblához `customerorderstatus`
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
-- Tábla szerkezet ehhez a táblához `datatables_states`
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
-- Tábla szerkezet ehhez a táblához `dictionaries`
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
-- A tábla adatainak kiíratása `dictionaries`
--

INSERT INTO `dictionaries` (`id`, `tipus`, `nev`, `leiras`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 1, 'Rendszergazda', 'Rendszergazda', NULL, NULL, NULL),
(2, 1, 'Belső felhasználó', 'Belső felhasználó', NULL, NULL, NULL),
(1, 1, 'B2B felhasználó', 'B2B felhasználó', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `employee`
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
-- Tábla szerkezet ehhez a táblához `excelimport`
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
-- Tábla szerkezet ehhez a táblához `failed_jobs`
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
-- Tábla szerkezet ehhez a táblához `guaranteemode`
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
-- Tábla szerkezet ehhez a táblához `languages`
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
-- A tábla adatainak kiíratása `languages`
--

INSERT INTO `languages` (`id`, `shortname`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(0, 'hu', 'Magyar', NULL, NULL, NULL),
(3, 'en', 'English', NULL, NULL, NULL),
(4, 'de', 'Deutch', NULL, NULL, NULL),
(5, 'bg', 'Bългарин', NULL, NULL, NULL),
(6, 'cz', 'Češké', NULL, NULL, NULL),
(7, 'dk', 'Dansk', NULL, NULL, NULL),
(8, 'ee', 'Eesti', NULL, NULL, NULL),
(9, 'fi', 'Suomi', NULL, NULL, NULL),
(10, 'fr', 'France', NULL, NULL, NULL),
(11, 'gr', 'Ελληνικά', NULL, NULL, NULL),
(12, 'nl', 'Nederlands', NULL, NULL, NULL),
(13, 'hr', 'Hrvatska', NULL, NULL, NULL),
(14, 'ie', 'Ireland', NULL, NULL, NULL),
(15, 'pl', 'Polski', NULL, NULL, NULL),
(16, 'lv', 'Kļuva', NULL, NULL, NULL),
(17, 'lt', 'Lietuvių', NULL, NULL, NULL),
(18, 'mt', 'Malti', NULL, NULL, NULL),
(19, 'no', 'Norsk', NULL, NULL, NULL),
(20, 'it', 'Italiano', NULL, NULL, NULL),
(21, 'ru', 'Русский', NULL, NULL, NULL),
(22, 'pt', 'Portugal', NULL, NULL, NULL),
(23, 'ro', 'Română', NULL, NULL, NULL),
(24, 'es', 'Español', NULL, NULL, NULL),
(25, 'se', 'Svenska', NULL, NULL, NULL),
(26, 'rs', 'Српски', NULL, NULL, NULL),
(27, 'sk', 'Slovenský', NULL, NULL, NULL),
(28, 'si', 'Slovenščina', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `logitem`
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
-- A tábla adatainak kiíratása `logitem`
--

INSERT INTO `logitem` (`id`, `customer_id`, `user_id`, `eventtype`, `eventdatetime`, `remoteaddress`, `created_at`, `updated_at`, `deleted_at`) VALUES
(994, -9999, 0, 1, '2023-02-07 09:35:23', '127.0.0.1', '2023-02-07 09:35:23', '2023-02-07 09:35:23', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `logitemtable`
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
-- Tábla szerkezet ehhez a táblához `logitemtabledetail`
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
-- Tábla szerkezet ehhez a táblához `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

--
-- A tábla adatainak kiíratása `migrations`
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
-- Tábla szerkezet ehhez a táblához `password_resets`
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
-- Tábla szerkezet ehhez a táblához `paymentmethod`
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
-- Tábla szerkezet ehhez a táblához `paymentmethodlang`
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
-- Tábla szerkezet ehhez a táblához `personal_access_tokens`
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
-- Tábla szerkezet ehhez a táblához `pricecategory`
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
-- Tábla szerkezet ehhez a táblához `product`
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
-- Tábla szerkezet ehhez a táblához `productassociation`
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
-- Tábla szerkezet ehhez a táblához `productassociationtype`
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
-- Tábla szerkezet ehhez a táblához `productattribute`
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
-- Tábla szerkezet ehhez a táblához `productattributelang`
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
-- Tábla szerkezet ehhez a táblához `productattributes`
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
-- Tábla szerkezet ehhez a táblához `productcategory`
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
-- Tábla szerkezet ehhez a táblához `productcategorydiscount`
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
-- Tábla szerkezet ehhez a táblához `productcustomercode`
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
-- Tábla szerkezet ehhez a táblához `productcustomerdiscount`
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
-- Tábla szerkezet ehhez a táblához `productlang`
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
-- Tábla szerkezet ehhez a táblához `productprice`
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
-- Tábla szerkezet ehhez a táblához `quantityunit`
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
-- Tábla szerkezet ehhez a táblához `quantityunitlang`
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
-- Tábla szerkezet ehhez a táblához `shoppingcart`
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
-- Tábla szerkezet ehhez a táblához `shoppingcartdetail`
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
-- Tábla szerkezet ehhez a táblához `systemsetting`
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
-- Tábla szerkezet ehhez a táblához `systemsettingvalue`
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
-- Tábla szerkezet ehhez a táblához `translations`
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
-- A tábla adatainak kiíratása `translations`
--

INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Profil', 'hu', 'Profil', NULL, NULL, NULL),
(2, 'Profil', 'en', 'Profil', NULL, NULL, NULL),
(3, 'Kilépés', 'hu', 'Kilépés', NULL, NULL, NULL),
(4, 'Kilépés', 'en', 'Logout', NULL, NULL, NULL),
(5, 'Kosár', 'hu', 'Kosár', NULL, NULL, NULL),
(6, 'Kosár', 'en', 'Shopping cart', NULL, NULL, NULL),
(9, 'Van már nyitott kosara!', 'hu', 'Van már nyitott kosara!', NULL, NULL, NULL),
(10, 'Van már nyitott kosara!', 'en', 'Van már nyitott kosara!', NULL, NULL, NULL),
(11, 'Vezérlő', 'hu', 'Vezérlő', NULL, NULL, NULL),
(12, 'Vezérlő', 'en', 'Dashboard', NULL, NULL, NULL),
(13, 'Kedvenc termékek', 'hu', 'Kedvenc termékek', NULL, NULL, NULL),
(14, 'Kedvenc termékek', 'en', 'Favorite products', NULL, NULL, NULL),
(15, 'Új Kosár', 'hu', 'Új Kosár', NULL, NULL, NULL),
(16, 'Új Kosár', 'en', 'New shopping cart', NULL, NULL, NULL),
(17, 'Megrendelések', 'hu', 'Megrendelések', NULL, NULL, NULL),
(18, 'Megrendelések', 'en', 'Orders', NULL, NULL, NULL),
(19, 'Termék', 'hu', 'Termék', NULL, NULL, NULL),
(20, 'Termék', 'en', 'Product', NULL, NULL, NULL),
(21, 'Kedvenc termék kiválasztás', 'hu', 'Kedvenc termék kiválasztás', NULL, NULL, NULL),
(22, 'Kedvenc termék kiválasztás', 'en', 'Favorite product selection', NULL, NULL, NULL),
(23, 'Termék kategória', 'hu', 'Termék kategória', NULL, NULL, NULL),
(24, 'Termék kategória', 'en', 'Product category', NULL, NULL, NULL),
(25, 'Minden termék', 'hu', 'Minden termék', NULL, NULL, NULL),
(26, 'Minden termék', 'en', 'All products', NULL, NULL, NULL),
(27, 'Kedvenc', 'hu', 'Kedvenc', NULL, NULL, NULL),
(28, 'Kedvenc', 'en', 'Favorite', NULL, NULL, NULL),
(31, 'Kilép', 'hu', 'Kilép', NULL, NULL, NULL),
(32, 'Kilép', 'en', 'Cancel', NULL, NULL, NULL),
(41, 'Nem jelölt ki sort', 'hu', 'Nem jelölt ki sort', NULL, NULL, NULL),
(42, 'Nem jelölt ki sort', 'en', 'You have not selected a row', NULL, NULL, NULL),
(43, 'Nettó', 'hu', 'Nettó', NULL, NULL, NULL),
(44, 'Nettó', 'en', 'Net', NULL, NULL, NULL),
(45, 'ÁFA', 'hu', 'ÁFA', NULL, NULL, NULL),
(46, 'ÁFA', 'en', 'VAT', NULL, NULL, NULL),
(47, 'Bruttó', 'hu', 'Bruttó', NULL, NULL, NULL),
(48, 'Bruttó', 'en', 'Gross', NULL, NULL, NULL),
(49, 'Kosárba', 'hu', 'Kosárba', NULL, NULL, NULL),
(50, 'Kosárba', 'en', 'To cart', NULL, NULL, NULL),
(51, 'Tételek', 'hu', 'Tételek', NULL, NULL, NULL),
(52, 'Tételek', 'en', 'Tételek', NULL, NULL, NULL),
(53, 'Mennyiség', 'hu', 'Mennyiség', NULL, NULL, NULL),
(54, 'Mennyiség', 'en', 'Mennyiség', NULL, NULL, NULL),
(55, 'Me.egys', 'hu', 'Me.egys', NULL, NULL, NULL),
(56, 'Me.egys', 'en', 'Me.egys', NULL, NULL, NULL),
(57, 'Egys.ár', 'hu', 'Egys.ár', NULL, NULL, NULL),
(58, 'Egys.ár', 'en', 'Egys.ár', NULL, NULL, NULL),
(59, 'Pénznem', 'hu', 'Pénznem', NULL, NULL, NULL),
(60, 'Pénznem', 'en', 'Pénznem', NULL, NULL, NULL),
(61, 'Id', 'hu', 'Id', NULL, NULL, NULL),
(62, 'Id', 'en', 'Id', NULL, NULL, NULL),
(65, 'Product', 'hu', 'Product', NULL, NULL, NULL),
(67, 'Tétetek kosárba másolás!', 'hu', 'Tétetek kosárba másolás!', NULL, NULL, NULL),
(68, 'Tétetek kosárba másolás!', 'en', 'Tétetek kosárba másolás!', NULL, NULL, NULL),
(69, 'Biztosan kosárba másolja a tételeket?', 'hu', 'Biztosan kosárba másolja a tételeket?', NULL, NULL, NULL),
(70, 'Biztosan kosárba másolja a tételeket?', 'en', 'Are you sure you want to copy the items to cart?', NULL, NULL, NULL),
(71, 'Nincs kijelölt tétel!', 'hu', 'Nincs kijelölt tétel!', NULL, NULL, NULL),
(72, 'Nincs kijelölt tétel!', 'en', 'Nincs kijelölt tétel!', NULL, NULL, NULL),
(73, 'Összes megrendelés', 'hu', 'Összes megrendelés', NULL, NULL, NULL),
(74, 'Összes megrendelés', 'en', 'Összes megrendelés', NULL, NULL, NULL),
(75, 'Másolás', 'hu', 'Másolás', NULL, NULL, NULL),
(76, 'Másolás', 'en', 'Másolás', NULL, NULL, NULL),
(77, 'Megrendelés szám', 'hu', 'Megrendelés szám', NULL, NULL, NULL),
(78, 'Megrendelés szám', 'en', 'Megrendelés szám', NULL, NULL, NULL),
(79, 'Dátum', 'hu', 'Dátum', NULL, NULL, NULL),
(80, 'Dátum', 'en', 'Dátum', NULL, NULL, NULL),
(81, 'Tétel', 'hu', 'Tétel', NULL, NULL, NULL),
(82, 'Tétel', 'en', 'Tétel', NULL, NULL, NULL),
(83, 'Idei megrendelések', 'hu', 'Idei megrendelések', NULL, NULL, NULL),
(84, 'Idei megrendelések', 'en', 'This year\'s orders', NULL, NULL, NULL),
(85, 'Saját megrendelés', 'hu', 'Saját megrendelés', NULL, NULL, NULL),
(86, 'Saját megrendelés', 'en', 'Saját megrendelés', NULL, NULL, NULL),
(87, 'Idei saját megrendelések', 'hu', 'Idei saját megrendelések', NULL, NULL, NULL),
(88, 'Idei saját megrendelések', 'en', 'Idei saját megrendelések', NULL, NULL, NULL),
(89, 'Összes kosár', 'hu', 'Összes kosár', NULL, NULL, NULL),
(90, 'Összes kosár', 'en', 'Összes kosár', NULL, NULL, NULL),
(91, 'Idei kosár', 'hu', 'Idei kosár', NULL, NULL, NULL),
(92, 'Idei kosár', 'en', 'Idei kosár', NULL, NULL, NULL),
(93, 'Megrendelés kosárba másolás!', 'hu', 'Megrendelés kosárba másolás!', NULL, NULL, NULL),
(94, 'Megrendelés kosárba másolás!', 'en', 'Megrendelés kosárba másolás!', NULL, NULL, NULL),
(95, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'hu', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, NULL, NULL),
(96, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'en', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, NULL, NULL),
(97, 'Idei megrendelés', 'hu', 'Idei megrendelés', NULL, NULL, NULL),
(98, 'Idei megrendelés', 'en', 'Idei megrendelés', NULL, NULL, NULL),
(103, 'Összes', 'hu', 'Összes', NULL, NULL, NULL),
(104, 'Összes', 'en', 'Összes', NULL, NULL, NULL),
(105, 'Idei', 'hu', 'Idei', NULL, NULL, NULL),
(106, 'Idei', 'en', 'Idei', NULL, NULL, NULL),
(107, 'Idei saját megrendelés', 'hu', 'Idei saját megrendelés', NULL, NULL, NULL),
(108, 'Idei saját megrendelés', 'en', 'Idei saját megrendelés', NULL, NULL, NULL),
(109, 'Felhasználók összesen', 'hu', 'Felhasználók összesen', NULL, NULL, NULL),
(110, 'Felhasználók összesen', 'en', 'Felhasználók összesen', NULL, NULL, NULL),
(111, 'felhasználók', 'hu', 'felhasználók', NULL, NULL, NULL),
(112, 'felhasználók', 'en', 'felhasználók', NULL, NULL, NULL),
(113, 'B2B partnerek', 'hu', 'B2B partnerek', NULL, NULL, NULL),
(114, 'B2B partnerek', 'en', 'B2B partnerek', NULL, NULL, NULL),
(115, 'Partner felhasználók', 'hu', 'Partner felhasználók', NULL, NULL, NULL),
(116, 'Partner felhasználók', 'en', 'Partner felhasználók', NULL, NULL, NULL),
(117, 'Tovább', 'hu', 'Tovább', NULL, NULL, NULL),
(118, 'Tovább', 'en', 'Tovább', NULL, NULL, NULL),
(119, 'Belépés 3 hónap', 'hu', 'Belépés 3 hónap', NULL, NULL, NULL),
(120, 'Belépés 3 hónap', 'en', 'Belépés 3 hónap', NULL, NULL, NULL),
(121, 'Beállítások', 'hu', 'Beállítások', NULL, NULL, NULL),
(122, 'Beállítások', 'en', 'Beállítások', NULL, NULL, NULL),
(123, 'Belépés 3 hónap<', 'hu', 'Belépés 3 hónap<', NULL, NULL, NULL),
(124, 'Belépés 3 hónap<', 'en', 'Belépés 3 hónap<', NULL, NULL, NULL),
(125, 'Név', 'hu', 'Név', NULL, NULL, NULL),
(126, 'Név', 'en', 'Név', NULL, NULL, NULL),
(127, 'Email', 'hu', 'Email', NULL, NULL, NULL),
(128, 'Email', 'en', 'Email', NULL, NULL, NULL),
(129, 'Kép', 'hu', 'Kép', NULL, NULL, NULL),
(130, 'Kép', 'en', 'Kép', NULL, NULL, NULL),
(131, 'Beosztás', 'hu', 'Beosztás', NULL, NULL, NULL),
(132, 'Beosztás', 'en', 'Beosztás', NULL, NULL, NULL),
(133, 'Belépett', 'hu', 'Belépett', NULL, NULL, NULL),
(134, 'Belépett', 'en', 'Belépett', NULL, NULL, NULL),
(135, 'B2B felhasználók', 'hu', 'B2B felhasználók', NULL, NULL, NULL),
(136, 'B2B felhasználók', 'en', 'B2B felhasználók', NULL, NULL, NULL),
(137, 'Belső felhasználók', 'hu', 'Belső felhasználók', NULL, NULL, NULL),
(138, 'Belső felhasználók', 'en', 'Belső felhasználók', NULL, NULL, NULL),
(139, 'Log adatok', 'hu', 'Log adatok', NULL, NULL, NULL),
(140, 'Log adatok', 'en', 'Log adatok', NULL, NULL, NULL),
(141, 'XML Import', 'hu', 'XML Import', NULL, NULL, NULL),
(142, 'XML Import', 'en', 'XML Import', NULL, NULL, NULL),
(143, 'rendszergazdák', 'hu', 'rendszergazdák', NULL, NULL, NULL),
(144, 'rendszergazdák', 'en', 'rendszergazdák', NULL, NULL, NULL),
(145, 'Felhasználói belépések', 'hu', 'Felhasználói belépések', NULL, NULL, NULL),
(146, 'Felhasználói belépések', 'en', 'Felhasználói belépések', NULL, NULL, NULL),
(147, 'Felhasználónként', 'hu', 'Felhasználónként', NULL, NULL, NULL),
(148, 'Felhasználónként', 'en', 'Felhasználónként', NULL, NULL, NULL),
(149, 'db', 'hu', 'db', NULL, NULL, NULL),
(150, 'db', 'en', 'db', NULL, NULL, NULL),
(151, 'Nyitott', 'hu', 'Nyitott', NULL, NULL, NULL),
(152, 'Nyitott', 'en', 'Nyitott', NULL, NULL, NULL),
(153, 'Érték', 'hu', 'Érték', NULL, NULL, NULL),
(154, 'Érték', 'en', 'Érték', NULL, NULL, NULL),
(155, 'Hitel keret', 'hu', 'Hitel keret', NULL, NULL, NULL),
(156, 'Hitel keret', 'en', 'Hitel keret', NULL, NULL, NULL),
(157, 'Felhasznált', 'hu', 'Felhasznált', NULL, NULL, NULL),
(158, 'Felhasznált', 'en', 'Felhasznált', NULL, NULL, NULL),
(159, 'Szabad', 'hu', 'Szabad', NULL, NULL, NULL),
(160, 'Szabad', 'en', 'Szabad', NULL, NULL, NULL),
(161, 'Megrendelés értékek az elmúlt 12 hónapban', 'hu', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(162, 'Megrendelés értékek az elmúlt 12 hónapban', 'en', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(163, 'havi bontás', 'hu', 'havi bontás', NULL, NULL, NULL),
(164, 'havi bontás', 'en', 'havi bontás', NULL, NULL, NULL),
(165, 'forint', 'hu', 'forint', NULL, NULL, NULL),
(166, 'forint', 'en', 'forint', NULL, NULL, NULL),
(167, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'hu', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(168, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'en', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(169, 'Megrendelés darab az elmúlt 12 hónapban', 'hu', 'Megrendelés darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(170, 'Megrendelés darab az elmúlt 12 hónapban', 'en', 'Megrendelés darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(171, 'darab', 'hu', 'darab', NULL, NULL, NULL),
(172, 'darab', 'en', 'darab', NULL, NULL, NULL),
(173, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'hu', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(174, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'en', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(175, 'Keresés:', 'hu', 'Keresés:', NULL, NULL, NULL),
(176, 'Keresés:', 'en', 'Search:', NULL, NULL, NULL),
(177, 'Nincs rendelkezésre álló adat', 'hu', 'Nincs rendelkezésre álló adat', NULL, NULL, NULL),
(178, 'Nincs rendelkezésre álló adat', 'en', 'Nincs rendelkezésre álló adat', NULL, NULL, NULL),
(179, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'hu', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, NULL, NULL),
(180, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'en', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, NULL, NULL),
(181, 'Nulla találat', 'hu', 'Nulla találat', NULL, NULL, NULL),
(182, 'Nulla találat', 'en', 'Nulla találat', NULL, NULL, NULL),
(183, '(_MAX_ összes rekord közül szűrve)', 'hu', '(_MAX_ összes rekord közül szűrve)', NULL, NULL, NULL),
(184, '(_MAX_ összes rekord közül szűrve)', 'en', '(_MAX_ összes rekord közül szűrve)', NULL, NULL, NULL),
(185, '_MENU_ találat oldalanként', 'hu', '_MENU_ találat oldalanként', NULL, NULL, NULL),
(186, '_MENU_ találat oldalanként', 'en', '_MENU_ találat oldalanként', NULL, NULL, NULL),
(187, 'Betöltés...', 'hu', 'Betöltés...', NULL, NULL, NULL),
(188, 'Betöltés...', 'en', 'Betöltés...', NULL, NULL, NULL),
(189, 'Feldolgozás...', 'hu', 'Feldolgozás...', NULL, NULL, NULL),
(190, 'Feldolgozás...', 'en', 'Feldolgozás...', NULL, NULL, NULL),
(191, 'Nincs a keresésnek megfelelő találat', 'hu', 'Nincs a keresésnek megfelelő találat', NULL, NULL, NULL),
(192, 'Nincs a keresésnek megfelelő találat', 'en', 'Nincs a keresésnek megfelelő találat', NULL, NULL, NULL),
(193, 'Első', 'hu', 'Első', NULL, NULL, NULL),
(194, 'Első', 'en', 'Első', NULL, NULL, NULL),
(195, 'Előző', 'hu', 'Előző', NULL, NULL, NULL),
(196, 'Előző', 'en', 'Előző', NULL, NULL, NULL),
(197, 'Következő', 'hu', 'Következő', NULL, NULL, NULL),
(198, 'Következő', 'en', 'Következő', NULL, NULL, NULL),
(199, 'Utolsó', 'hu', 'Utolsó', NULL, NULL, NULL),
(200, 'Utolsó', 'en', 'Utolsó', NULL, NULL, NULL),
(201, ': aktiválja a növekvő rendezéshez', 'hu', ': aktiválja a növekvő rendezéshez', NULL, NULL, NULL),
(202, ': aktiválja a növekvő rendezéshez', 'en', ': aktiválja a növekvő rendezéshez', NULL, NULL, NULL),
(203, ': aktiválja a csökkenő rendezéshez', 'hu', ': aktiválja a csökkenő rendezéshez', NULL, NULL, NULL),
(204, ': aktiválja a csökkenő rendezéshez', 'en', ': aktiválja a csökkenő rendezéshez', NULL, NULL, NULL),
(205, '%d sor kiválasztva', 'hu', '%d sor kiválasztva', NULL, NULL, NULL),
(206, '%d sor kiválasztva', 'en', '%d sor kiválasztva', NULL, NULL, NULL),
(207, '1 sor kiválasztva', 'hu', '1 sor kiválasztva', NULL, NULL, NULL),
(208, '1 sor kiválasztva', 'en', '1 sor kiválasztva', NULL, NULL, NULL),
(209, '1 cella kiválasztva', 'hu', '1 cella kiválasztva', NULL, NULL, NULL),
(210, '1 cella kiválasztva', 'en', '1 cella kiválasztva', NULL, NULL, NULL),
(211, '%d cella kiválasztva', 'hu', '%d cella kiválasztva', NULL, NULL, NULL),
(212, '%d cella kiválasztva', 'en', '%d cella kiválasztva', NULL, NULL, NULL),
(213, '1 oszlop kiválasztva', 'hu', '1 oszlop kiválasztva', NULL, NULL, NULL),
(214, '1 oszlop kiválasztva', 'en', '1 oszlop kiválasztva', NULL, NULL, NULL),
(215, '%d oszlop kiválasztva', 'hu', '%d oszlop kiválasztva', NULL, NULL, NULL),
(216, '%d oszlop kiválasztva', 'en', '%d oszlop kiválasztva', NULL, NULL, NULL),
(217, 'Oszlopok', 'hu', 'Oszlopok', NULL, NULL, NULL),
(218, 'Oszlopok', 'en', 'Oszlopok', NULL, NULL, NULL),
(219, 'Vágólapra másolás', 'hu', 'Vágólapra másolás', NULL, NULL, NULL),
(220, 'Vágólapra másolás', 'en', 'Vágólapra másolás', NULL, NULL, NULL),
(221, '%d sor másolva', 'hu', '%d sor másolva', NULL, NULL, NULL),
(222, '%d sor másolva', 'en', '%d sor másolva', NULL, NULL, NULL),
(223, '1 sor másolva', 'hu', '1 sor másolva', NULL, NULL, NULL),
(224, '1 sor másolva', 'en', '1 sor másolva', NULL, NULL, NULL),
(225, 'Oszlopok visszaállítása', 'hu', 'Oszlopok visszaállítása', NULL, NULL, NULL),
(226, 'Oszlopok visszaállítása', 'en', 'Oszlopok visszaállítása', NULL, NULL, NULL),
(227, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'hu', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, NULL, NULL),
(228, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'en', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, NULL, NULL),
(229, 'Összes sor megjelenítése', 'hu', 'Összes sor megjelenítése', NULL, NULL, NULL),
(230, 'Összes sor megjelenítése', 'en', 'Összes sor megjelenítése', NULL, NULL, NULL),
(231, '%d sor megjelenítése', 'hu', '%d sor megjelenítése', NULL, NULL, NULL),
(232, '%d sor megjelenítése', 'en', '%d sor megjelenítése', NULL, NULL, NULL),
(233, 'Nyomtat', 'hu', 'Nyomtat', NULL, NULL, NULL),
(234, 'Nyomtat', 'en', 'Nyomtat', NULL, NULL, NULL),
(235, 'Megszakítás', 'hu', 'Megszakítás', NULL, NULL, NULL),
(236, 'Megszakítás', 'en', 'Megszakítás', NULL, NULL, NULL),
(237, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'hu', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, NULL, NULL),
(238, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'en', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, NULL, NULL),
(239, 'Cellák vízszintes kitöltése', 'hu', 'Cellák vízszintes kitöltése', NULL, NULL, NULL),
(240, 'Cellák vízszintes kitöltése', 'en', 'Cellák vízszintes kitöltése', NULL, NULL, NULL),
(241, 'Cellák függőleges kitöltése', 'hu', 'Cellák függőleges kitöltése', NULL, NULL, NULL),
(242, 'Cellák függőleges kitöltése', 'en', 'Cellák függőleges kitöltése', NULL, NULL, NULL),
(243, 'Feltétel hozzáadása', 'hu', 'Feltétel hozzáadása', NULL, NULL, NULL),
(244, 'Feltétel hozzáadása', 'en', 'Feltétel hozzáadása', NULL, NULL, NULL),
(245, 'Keresés konfigurátor', 'hu', 'Keresés konfigurátor', NULL, NULL, NULL),
(246, 'Keresés konfigurátor', 'en', 'Keresés konfigurátor', NULL, NULL, NULL),
(247, 'Keresés konfigurátor (%d)', 'hu', 'Keresés konfigurátor (%d)', NULL, NULL, NULL),
(248, 'Keresés konfigurátor (%d)', 'en', 'Keresés konfigurátor (%d)', NULL, NULL, NULL),
(249, 'Összes feltétel törlése', 'hu', 'Összes feltétel törlése', NULL, NULL, NULL),
(250, 'Összes feltétel törlése', 'en', 'Összes feltétel törlése', NULL, NULL, NULL),
(251, 'Feltétel', 'hu', 'Feltétel', NULL, NULL, NULL),
(252, 'Feltétel', 'en', 'Feltétel', NULL, NULL, NULL),
(253, 'Után', 'hu', 'Után', NULL, NULL, NULL),
(254, 'Után', 'en', 'Után', NULL, NULL, NULL),
(255, 'Előtt', 'hu', 'Előtt', NULL, NULL, NULL),
(256, 'Előtt', 'en', 'Előtt', NULL, NULL, NULL),
(257, 'Között', 'hu', 'Között', NULL, NULL, NULL),
(258, 'Között', 'en', 'Között', NULL, NULL, NULL),
(259, 'Üres', 'hu', 'Üres', NULL, NULL, NULL),
(260, 'Üres', 'en', 'Üres', NULL, NULL, NULL),
(261, 'Egyenlő', 'hu', 'Egyenlő', NULL, NULL, NULL),
(262, 'Egyenlő', 'en', 'Egyenlő', NULL, NULL, NULL),
(263, 'Nem', 'hu', 'Nem', NULL, NULL, NULL),
(264, 'Nem', 'en', 'Nem', NULL, NULL, NULL),
(265, 'Kívül eső', 'hu', 'Kívül eső', NULL, NULL, NULL),
(266, 'Kívül eső', 'en', 'Kívül eső', NULL, NULL, NULL),
(267, 'Nem üres', 'hu', 'Nem üres', NULL, NULL, NULL),
(268, 'Nem üres', 'en', 'Nem üres', NULL, NULL, NULL),
(269, 'Nagyobb mint', 'hu', 'Nagyobb mint', NULL, NULL, NULL),
(270, 'Nagyobb mint', 'en', 'Nagyobb mint', NULL, NULL, NULL),
(271, 'Nagyobb vagy egyenlő mint', 'hu', 'Nagyobb vagy egyenlő mint', NULL, NULL, NULL),
(272, 'Nagyobb vagy egyenlő mint', 'en', 'Nagyobb vagy egyenlő mint', NULL, NULL, NULL),
(273, 'Kissebb mint', 'hu', 'Kissebb mint', NULL, NULL, NULL),
(274, 'Kissebb mint', 'en', 'Kissebb mint', NULL, NULL, NULL),
(275, 'Kissebb vagy egyenlő mint', 'hu', 'Kissebb vagy egyenlő mint', NULL, NULL, NULL),
(276, 'Kissebb vagy egyenlő mint', 'en', 'Kissebb vagy egyenlő mint', NULL, NULL, NULL),
(277, 'Tartalmazza', 'hu', 'Tartalmazza', NULL, NULL, NULL),
(278, 'Tartalmazza', 'en', 'Tartalmazza', NULL, NULL, NULL),
(279, 'Végződik', 'hu', 'Végződik', NULL, NULL, NULL),
(280, 'Végződik', 'en', 'Végződik', NULL, NULL, NULL),
(281, 'Kezdődik', 'hu', 'Kezdődik', NULL, NULL, NULL),
(282, 'Kezdődik', 'en', 'Kezdődik', NULL, NULL, NULL),
(283, 'Adat', 'hu', 'Adat', NULL, NULL, NULL),
(284, 'Adat', 'en', 'Adat', NULL, NULL, NULL),
(285, 'Feltétel törlése', 'hu', 'Feltétel törlése', NULL, NULL, NULL),
(286, 'Feltétel törlése', 'en', 'Feltétel törlése', NULL, NULL, NULL),
(287, 'És', 'hu', 'És', NULL, NULL, NULL),
(288, 'És', 'en', 'És', NULL, NULL, NULL),
(289, 'Vagy', 'hu', 'Vagy', NULL, NULL, NULL),
(290, 'Vagy', 'en', 'Vagy', NULL, NULL, NULL),
(291, 'Szűrők törlése', 'hu', 'Szűrők törlése', NULL, NULL, NULL),
(292, 'Szűrők törlése', 'en', 'Szűrők törlése', NULL, NULL, NULL),
(293, 'Szűrőpanelek', 'hu', 'Szűrőpanelek', NULL, NULL, NULL),
(294, 'Szűrőpanelek', 'en', 'Szűrőpanelek', NULL, NULL, NULL),
(295, 'Szűrőpanelek (%d)', 'hu', 'Szűrőpanelek (%d)', NULL, NULL, NULL),
(296, 'Szűrőpanelek (%d)', 'en', 'Szűrőpanelek (%d)', NULL, NULL, NULL),
(297, 'Nincsenek szűrőpanelek', 'hu', 'Nincsenek szűrőpanelek', NULL, NULL, NULL),
(298, 'Nincsenek szűrőpanelek', 'en', 'Nincsenek szűrőpanelek', NULL, NULL, NULL),
(299, 'Szűrőpanelek betöltése', 'hu', 'Szűrőpanelek betöltése', NULL, NULL, NULL),
(300, 'Szűrőpanelek betöltése', 'en', 'Szűrőpanelek betöltése', NULL, NULL, NULL),
(301, 'Aktív szűrőpanelek: %d', 'hu', 'Aktív szűrőpanelek: %d', NULL, NULL, NULL),
(302, 'Aktív szűrőpanelek: %d', 'en', 'Aktív szűrőpanelek: %d', NULL, NULL, NULL),
(303, 'Óra', 'hu', 'Óra', NULL, NULL, NULL),
(304, 'Óra', 'en', 'Óra', NULL, NULL, NULL),
(305, 'Perc', 'hu', 'Perc', NULL, NULL, NULL),
(306, 'Perc', 'en', 'Perc', NULL, NULL, NULL),
(307, 'Másodperc', 'hu', 'Másodperc', NULL, NULL, NULL),
(308, 'Másodperc', 'en', 'Másodperc', NULL, NULL, NULL),
(309, 'de.', 'hu', 'de.', NULL, NULL, NULL),
(310, 'de.', 'en', 'de.', NULL, NULL, NULL),
(311, 'du.', 'hu', 'du.', NULL, NULL, NULL),
(312, 'du.', 'en', 'du.', NULL, NULL, NULL),
(313, 'Bezárás', 'hu', 'Bezárás', NULL, NULL, NULL),
(314, 'Bezárás', 'en', 'Bezárás', NULL, NULL, NULL),
(315, 'Új', 'hu', 'Új', NULL, NULL, NULL),
(316, 'Új', 'en', 'Új', NULL, NULL, NULL),
(317, 'Létrehozás', 'hu', 'Létrehozás', NULL, NULL, NULL),
(318, 'Létrehozás', 'en', 'Létrehozás', NULL, NULL, NULL),
(319, 'Módosítás', 'hu', 'Módosítás', NULL, NULL, NULL),
(320, 'Módosítás', 'en', 'Módosítás', NULL, NULL, NULL),
(321, 'Törlés', 'hu', 'Törlés', NULL, NULL, NULL),
(322, 'Törlés', 'en', 'Törlés', NULL, NULL, NULL),
(323, 'Teljes képernyő', 'hu', 'Teljes képernyő', NULL, NULL, NULL),
(324, 'Teljes képernyő', 'en', 'Full screen', NULL, NULL, NULL),
(325, 'Kilépés a teljes képernyőből', 'hu', 'Kilépés a teljes képernyőből', NULL, NULL, NULL),
(326, 'Kilépés a teljes képernyőből', 'en', 'Kilépés a teljes képernyőből', NULL, NULL, NULL),
(327, 'január', 'hu', 'január', NULL, NULL, NULL),
(328, 'január', 'en', 'január', NULL, NULL, NULL),
(329, 'február', 'hu', 'február', NULL, NULL, NULL),
(330, 'február', 'en', 'február', NULL, NULL, NULL),
(331, 'március', 'hu', 'március', NULL, NULL, NULL),
(332, 'március', 'en', 'március', NULL, NULL, NULL),
(333, 'április', 'hu', 'április', NULL, NULL, NULL),
(334, 'április', 'en', 'április', NULL, NULL, NULL),
(335, 'május', 'hu', 'május', NULL, NULL, NULL),
(336, 'május', 'en', 'május', NULL, NULL, NULL),
(337, 'június', 'hu', 'június', NULL, NULL, NULL),
(338, 'június', 'en', 'június', NULL, NULL, NULL),
(339, 'július', 'hu', 'július', NULL, NULL, NULL),
(340, 'július', 'en', 'július', NULL, NULL, NULL),
(341, 'augusztus', 'hu', 'augusztus', NULL, NULL, NULL),
(342, 'augusztus', 'en', 'augusztus', NULL, NULL, NULL),
(343, 'szeptember', 'hu', 'szeptember', NULL, NULL, NULL),
(344, 'szeptember', 'en', 'szeptember', NULL, NULL, NULL),
(345, 'október', 'hu', 'október', NULL, NULL, NULL),
(346, 'október', 'en', 'október', NULL, NULL, NULL),
(347, 'november', 'hu', 'november', NULL, NULL, NULL),
(348, 'november', 'en', 'november', NULL, NULL, NULL),
(349, 'december', 'hu', 'december', NULL, NULL, NULL),
(350, 'december', 'en', 'december', NULL, NULL, NULL),
(351, 'jan', 'hu', 'jan', NULL, NULL, NULL),
(352, 'jan', 'en', 'jan', NULL, NULL, NULL),
(353, 'febr', 'hu', 'febr', NULL, NULL, NULL),
(354, 'febr', 'en', 'febr', NULL, NULL, NULL),
(355, 'márc', 'hu', 'márc', NULL, NULL, NULL),
(356, 'márc', 'en', 'márc', NULL, NULL, NULL),
(357, 'ápr', 'hu', 'ápr', NULL, NULL, NULL),
(358, 'ápr', 'en', 'ápr', NULL, NULL, NULL),
(359, 'máj', 'hu', 'máj', NULL, NULL, NULL),
(360, 'máj', 'en', 'máj', NULL, NULL, NULL),
(361, 'jún', 'hu', 'jún', NULL, NULL, NULL),
(362, 'jún', 'en', 'jún', NULL, NULL, NULL),
(363, 'júl', 'hu', 'júl', NULL, NULL, NULL),
(364, 'júl', 'en', 'júl', NULL, NULL, NULL),
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
(375, 'vasárnap', 'hu', 'vasárnap', NULL, NULL, NULL),
(376, 'vasárnap', 'en', 'vasárnap', NULL, NULL, NULL),
(377, 'hétfő', 'hu', 'hétfő', NULL, NULL, NULL),
(378, 'hétfő', 'en', 'hétfő', NULL, NULL, NULL),
(379, 'kedd', 'hu', 'kedd', NULL, NULL, NULL),
(380, 'kedd', 'en', 'kedd', NULL, NULL, NULL),
(381, 'szerda', 'hu', 'szerda', NULL, NULL, NULL),
(382, 'szerda', 'en', 'szerda', NULL, NULL, NULL),
(383, 'csütörtök', 'hu', 'csütörtök', NULL, NULL, NULL),
(384, 'csütörtök', 'en', 'csütörtök', NULL, NULL, NULL),
(385, 'péntek', 'hu', 'péntek', NULL, NULL, NULL),
(386, 'péntek', 'en', 'péntek', NULL, NULL, NULL),
(387, 'szombat', 'hu', 'szombat', NULL, NULL, NULL),
(388, 'szombat', 'en', 'szombat', NULL, NULL, NULL),
(389, 'Exportál', 'hu', 'Exportál', NULL, NULL, NULL),
(390, 'Exportál', 'en', 'Exportál', NULL, NULL, NULL),
(391, 'Importál', 'hu', 'Importál', NULL, NULL, NULL),
(392, 'Importál', 'en', 'Importál', NULL, NULL, NULL),
(393, 'ettől', 'hu', 'ettől', NULL, NULL, NULL),
(394, 'ettől', 'en', 'ettől', NULL, NULL, NULL),
(395, 'eddig', 'hu', 'eddig', NULL, NULL, NULL),
(396, 'eddig', 'en', 'eddig', NULL, NULL, NULL),
(397, 'mutat:', 'hu', 'mutat:', NULL, NULL, NULL),
(398, 'mutat:', 'en', 'mutat:', NULL, NULL, NULL),
(399, 'Letöltés CSV fileként', 'hu', 'Letöltés CSV fileként', NULL, NULL, NULL),
(400, 'Letöltés CSV fileként', 'en', 'Letöltés CSV fileként', NULL, NULL, NULL),
(401, 'Letöltés XLS fileként', 'hu', 'Letöltés XLS fileként', NULL, NULL, NULL),
(402, 'Letöltés XLS fileként', 'en', 'Letöltés XLS fileként', NULL, NULL, NULL),
(403, 'Letöltés PNG képként', 'hu', 'Letöltés PNG képként', NULL, NULL, NULL),
(404, 'Letöltés PNG képként', 'en', 'Letöltés PNG képként', NULL, NULL, NULL),
(405, 'Letöltés JPEG képként', 'hu', 'Letöltés JPEG képként', NULL, NULL, NULL),
(406, 'Letöltés JPEG képként', 'en', 'Letöltés JPEG képként', NULL, NULL, NULL),
(407, 'Letöltés PDF dokumentumként', 'hu', 'Letöltés PDF dokumentumként', NULL, NULL, NULL),
(408, 'Letöltés PDF dokumentumként', 'en', 'Letöltés PDF dokumentumként', NULL, NULL, NULL),
(409, 'Letöltés SVG formátumban', 'hu', 'Letöltés SVG formátumban', NULL, NULL, NULL),
(410, 'Letöltés SVG formátumban', 'en', 'Letöltés SVG formátumban', NULL, NULL, NULL),
(411, 'Visszaállít', 'hu', 'Visszaállít', NULL, NULL, NULL),
(412, 'Visszaállít', 'en', 'Visszaállít', NULL, NULL, NULL),
(413, 'Táblázat', 'hu', 'Táblázat', NULL, NULL, NULL),
(414, 'Táblázat', 'en', 'Táblázat', NULL, NULL, NULL),
(415, 'Nyomtatás', 'hu', 'Nyomtatás', NULL, NULL, NULL),
(416, 'Nyomtatás', 'en', 'Nyomtatás', NULL, NULL, NULL),
(471, 'Bejelentkezés', 'hu', 'Bejelentkezés', NULL, NULL, NULL),
(472, 'Bejelentkezés', 'en', 'Bejelentkezés', NULL, NULL, NULL),
(473, 'Belép', 'hu', 'Belép', NULL, NULL, NULL),
(474, 'Belép', 'en', 'Belép', NULL, NULL, NULL),
(475, 'Nyelvek', 'hu', 'Nyelvek', NULL, NULL, NULL),
(476, 'Nyelvek', 'en', 'Nyelvek', NULL, NULL, NULL),
(477, 'Partner cég', 'hu', 'Partner cég', NULL, NULL, NULL),
(478, 'Partner cég', 'en', 'Partner cég', NULL, NULL, NULL),
(479, 'Telephely', 'hu', 'Telephely', NULL, NULL, NULL),
(480, 'Telephely', 'en', 'Telephely', NULL, NULL, NULL),
(481, 'Szállítási mód', 'hu', 'Szállítási mód', NULL, NULL, NULL),
(482, 'Szállítási mód', 'en', 'Szállítási mód', NULL, NULL, NULL),
(483, 'Nemzetiség', 'hu', 'Nemzetiség', NULL, NULL, NULL),
(484, 'Nemzetiség', 'en', 'Nemzetiség', NULL, NULL, NULL),
(485, '%d cella kiválasztva', 'ee', '%d cella kiválasztva', NULL, '2022-06-14 11:25:45', NULL),
(486, '%d oszlop kiválasztva', 'ee', '%d oszlop kiválasztva', NULL, NULL, NULL),
(487, '%d sor kiválasztva', 'ee', '%d sor kiválasztva', NULL, NULL, NULL),
(488, '%d sor másolva', 'ee', '%d sor másolva', NULL, NULL, NULL),
(489, '%d sor megjelenítése', 'ee', '%d sor megjelenítése', NULL, NULL, NULL),
(490, '(_MAX_ összes rekord közül szűrve)', 'ee', '(_MAX_ összes rekord közül szűrve)', NULL, NULL, NULL),
(491, '1 cella kiválasztva', 'ee', '1 cella kiválasztva', NULL, NULL, NULL),
(492, '1 oszlop kiválasztva', 'ee', '1 oszlop kiválasztva', NULL, NULL, NULL),
(493, '1 sor kiválasztva', 'ee', '1 sor kiválasztva', NULL, NULL, NULL),
(494, '1 sor másolva', 'ee', '1 sor másolva', NULL, NULL, NULL),
(495, ': aktiválja a csökkenő rendezéshez', 'ee', ': aktiválja a csökkenő rendezéshez', NULL, NULL, NULL),
(496, ': aktiválja a növekvő rendezéshez', 'ee', ': aktiválja a növekvő rendezéshez', NULL, NULL, NULL),
(497, 'Adat', 'ee', 'Adat', NULL, NULL, NULL),
(498, 'ÁFA', 'ee', 'ÁFA', NULL, NULL, NULL),
(499, 'Aktív szűrőpanelek: %d', 'ee', 'Aktív szűrőpanelek: %d', NULL, NULL, NULL),
(500, 'ápr', 'ee', 'ápr', NULL, NULL, NULL),
(501, 'április', 'ee', 'április', NULL, NULL, NULL),
(502, 'aug', 'ee', 'aug', NULL, NULL, NULL),
(503, 'augusztus', 'ee', 'augusztus', NULL, NULL, NULL),
(504, 'B2B felhasználók', 'ee', 'B2B felhasználók', NULL, NULL, NULL),
(505, 'B2B partnerek', 'ee', 'B2B partnerek', NULL, NULL, NULL),
(506, 'Beállítások', 'ee', 'Beállítások', NULL, NULL, NULL),
(507, 'Bejelentkezés', 'ee', 'Bejelentkezés', NULL, NULL, NULL),
(508, 'Belép', 'ee', 'Belép', NULL, NULL, NULL),
(509, 'Belépés 3 hónap', 'ee', 'Belépés 3 hónap', NULL, NULL, NULL),
(510, 'Belépés 3 hónap<', 'ee', 'Belépés 3 hónap<', NULL, NULL, NULL),
(511, 'Belépett', 'ee', 'Belépett', NULL, NULL, NULL),
(512, 'Belső felhasználók', 'ee', 'Belső felhasználók', NULL, NULL, NULL),
(513, 'Beosztás', 'ee', 'Beosztás', NULL, NULL, NULL),
(514, 'Betöltés...', 'ee', 'Betöltés...', NULL, NULL, NULL),
(515, 'Bezárás', 'ee', 'Bezárás', NULL, NULL, NULL),
(516, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'ee', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, NULL, NULL),
(517, 'Biztosan kosárba másolja a tételeket?', 'ee', 'Biztosan kosárba másolja a tételeket?', NULL, NULL, NULL),
(518, 'Bruttó', 'ee', 'Bruttó', NULL, NULL, NULL),
(519, 'Cellák függőleges kitöltése', 'ee', 'Cellák függőleges kitöltése', NULL, NULL, NULL),
(520, 'Cellák vízszintes kitöltése', 'ee', 'Cellák vízszintes kitöltése', NULL, NULL, NULL),
(521, 'csütörtök', 'ee', 'csütörtök', NULL, NULL, NULL),
(522, 'darab', 'ee', 'darab', NULL, NULL, NULL),
(523, 'Dátum', 'ee', 'Dátum', NULL, NULL, NULL),
(524, 'db', 'ee', 'db', NULL, NULL, NULL),
(525, 'de.', 'ee', 'de.', NULL, NULL, NULL),
(526, 'dec', 'ee', 'dec', NULL, NULL, NULL),
(527, 'december', 'ee', 'december', NULL, NULL, NULL),
(528, 'du.', 'ee', 'du.', NULL, NULL, NULL),
(529, 'eddig', 'ee', 'eddig', NULL, NULL, NULL),
(530, 'Egyenlő', 'ee', 'Egyenlő', NULL, NULL, NULL),
(531, 'Egys.ár', 'ee', 'Egys.ár', NULL, NULL, NULL),
(532, 'Előtt', 'ee', 'Előtt', NULL, NULL, NULL),
(533, 'Előző', 'ee', 'Előző', NULL, NULL, NULL),
(534, 'Első', 'ee', 'Első', NULL, NULL, NULL),
(535, 'Email', 'ee', 'Email', NULL, NULL, NULL),
(536, 'Érték', 'ee', 'Érték', NULL, NULL, NULL),
(537, 'És', 'ee', 'És', NULL, NULL, NULL),
(538, 'ettől', 'ee', 'ettől', NULL, NULL, NULL),
(539, 'Exportál', 'ee', 'Exportál', NULL, NULL, NULL),
(540, 'febr', 'ee', 'febr', NULL, NULL, NULL),
(541, 'február', 'ee', 'február', NULL, NULL, NULL),
(542, 'Feldolgozás...', 'ee', 'Feldolgozás...', NULL, NULL, NULL),
(543, 'Felhasználói belépések', 'ee', 'Felhasználói belépések', NULL, NULL, NULL),
(544, 'felhasználók', 'ee', 'felhasználók', NULL, NULL, NULL),
(545, 'Felhasználók összesen', 'ee', 'Felhasználók összesen', NULL, NULL, NULL),
(546, 'Felhasználónként', 'ee', 'Felhasználónként', NULL, NULL, NULL),
(547, 'Felhasznált', 'ee', 'Felhasznált', NULL, NULL, NULL),
(548, 'Feltétel', 'ee', 'Feltétel', NULL, NULL, NULL),
(549, 'Feltétel hozzáadása', 'ee', 'Feltétel hozzáadása', NULL, NULL, NULL),
(550, 'Feltétel törlése', 'ee', 'Feltétel törlése', NULL, NULL, NULL),
(551, 'forint', 'ee', 'forint', NULL, NULL, NULL),
(552, 'havi bontás', 'ee', 'havi bontás', NULL, NULL, NULL),
(553, 'hétfő', 'ee', 'hétfő', NULL, NULL, NULL),
(554, 'Hitel keret', 'ee', 'Hitel keret', NULL, NULL, NULL),
(555, 'Id', 'ee', 'Id', NULL, NULL, NULL),
(556, 'Idei', 'ee', 'Idei', NULL, NULL, NULL),
(557, 'Idei kosár', 'ee', 'Idei kosár', NULL, NULL, NULL),
(558, 'Idei megrendelés', 'ee', 'Idei megrendelés', NULL, NULL, NULL),
(559, 'Idei megrendelések', 'ee', 'Idei megrendelések', NULL, NULL, NULL),
(560, 'Idei saját megrendelés', 'ee', 'Idei saját megrendelés', NULL, NULL, NULL),
(561, 'Idei saját megrendelések', 'ee', 'Idei saját megrendelések', NULL, NULL, NULL),
(562, 'Importál', 'ee', 'Importál', NULL, NULL, NULL),
(563, 'jan', 'ee', 'jan', NULL, NULL, NULL),
(564, 'január', 'ee', 'január', NULL, NULL, NULL),
(565, 'júl', 'ee', 'júl', NULL, NULL, NULL),
(566, 'július', 'ee', 'július', NULL, NULL, NULL),
(567, 'jún', 'ee', 'jún', NULL, NULL, NULL),
(568, 'június', 'ee', 'június', NULL, NULL, NULL),
(569, 'kedd', 'ee', 'kedd', NULL, NULL, NULL),
(570, 'Kedvenc', 'ee', 'Kedvenc', NULL, NULL, NULL),
(571, 'Kedvenc termék kiválasztás', 'ee', 'Kedvenc termék kiválasztás', NULL, NULL, NULL),
(572, 'Kedvenc termékek', 'ee', 'Kedvenc termékek', NULL, NULL, NULL),
(573, 'Kép', 'ee', 'Kép', NULL, NULL, NULL),
(574, 'Keresés konfigurátor', 'ee', 'Keresés konfigurátor', NULL, NULL, NULL),
(575, 'Keresés konfigurátor (%d)', 'ee', 'Keresés konfigurátor (%d)', NULL, NULL, NULL),
(576, 'Keresés:', 'ee', 'Keresés:', NULL, NULL, NULL),
(577, 'Kezdődik', 'ee', 'Kezdődik', NULL, NULL, NULL),
(578, 'Kilép', 'ee', 'Kilép', NULL, NULL, NULL),
(579, 'Kilépés', 'ee', 'Kilépés', NULL, NULL, NULL),
(580, 'Kilépés a teljes képernyőből', 'ee', 'Kilépés a teljes képernyőből', NULL, NULL, NULL),
(581, 'Kissebb mint', 'ee', 'Kissebb mint', NULL, NULL, NULL),
(582, 'Kissebb vagy egyenlő mint', 'ee', 'Kissebb vagy egyenlő mint', NULL, NULL, NULL),
(583, 'Kívül eső', 'ee', 'Kívül eső', NULL, NULL, NULL),
(584, 'Kosár', 'ee', 'Kosár', NULL, NULL, NULL),
(585, 'Kosárba', 'ee', 'Kosárba', NULL, NULL, NULL),
(586, 'Következő', 'ee', 'Következő', NULL, NULL, NULL),
(587, 'Között', 'ee', 'Között', NULL, NULL, NULL),
(588, 'Letöltés CSV fileként', 'ee', 'Letöltés CSV fileként', NULL, NULL, NULL),
(589, 'Letöltés JPEG képként', 'ee', 'Letöltés JPEG képként', NULL, NULL, NULL),
(590, 'Letöltés PDF dokumentumként', 'ee', 'Letöltés PDF dokumentumként', NULL, NULL, NULL),
(591, 'Letöltés PNG képként', 'ee', 'Letöltés PNG képként', NULL, NULL, NULL),
(592, 'Letöltés SVG formátumban', 'ee', 'Letöltés SVG formátumban', NULL, NULL, NULL),
(593, 'Letöltés XLS fileként', 'ee', 'Letöltés XLS fileként', NULL, NULL, NULL),
(594, 'Létrehozás', 'ee', 'Létrehozás', NULL, NULL, NULL),
(595, 'Log adatok', 'ee', 'Log adatok', NULL, NULL, NULL),
(596, 'máj', 'ee', 'máj', NULL, NULL, NULL),
(597, 'május', 'ee', 'május', NULL, NULL, NULL),
(598, 'márc', 'ee', 'márc', NULL, NULL, NULL),
(599, 'március', 'ee', 'március', NULL, NULL, NULL),
(600, 'Másodperc', 'ee', 'Másodperc', NULL, NULL, NULL),
(601, 'Másolás', 'ee', 'Másolás', NULL, NULL, NULL),
(602, 'Me.egys', 'ee', 'Me.egys', NULL, NULL, NULL),
(603, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'ee', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(604, 'Megrendelés darab az elmúlt 12 hónapban', 'ee', 'Megrendelés darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(605, 'Megrendelés értékek az elmúlt 12 hónapban', 'ee', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(606, 'Megrendelés kosárba másolás!', 'ee', 'Megrendelés kosárba másolás!', NULL, NULL, NULL),
(607, 'Megrendelés szám', 'ee', 'Megrendelés szám', NULL, NULL, NULL),
(608, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'ee', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(609, 'Megrendelések', 'ee', 'Megrendelések', NULL, NULL, NULL),
(610, 'Megszakítás', 'ee', 'Megszakítás', NULL, NULL, NULL),
(611, 'Mennyiség', 'ee', 'Mennyiség', NULL, NULL, NULL),
(612, 'Minden termék', 'ee', 'Minden termék', NULL, NULL, NULL),
(613, 'Módosítás', 'ee', 'Módosítás', NULL, NULL, NULL),
(614, 'mutat:', 'ee', 'mutat:', NULL, NULL, NULL),
(615, 'Nagyobb mint', 'ee', 'Nagyobb mint', NULL, NULL, NULL),
(616, 'Nagyobb vagy egyenlő mint', 'ee', 'Nagyobb vagy egyenlő mint', NULL, NULL, NULL),
(617, 'Nem', 'ee', 'Nem', NULL, NULL, NULL),
(618, 'Nem jelölt ki sort', 'ee', 'Nem jelölt ki sort', NULL, NULL, NULL),
(619, 'Nem üres', 'ee', 'Nem üres', NULL, NULL, NULL),
(620, 'Nemzetiség', 'ee', 'Nemzetiség', NULL, NULL, NULL),
(621, 'Nettó', 'ee', 'Nettó', NULL, NULL, NULL),
(622, 'Név', 'ee', 'Név', NULL, NULL, NULL),
(623, 'Nincs a keresésnek megfelelő találat', 'ee', 'Nincs a keresésnek megfelelő találat', NULL, NULL, NULL),
(624, 'Nincs kijelölt tétel!', 'ee', 'Nincs kijelölt tétel!', NULL, NULL, NULL),
(625, 'Nincs rendelkezésre álló adat', 'ee', 'Nincs rendelkezésre álló adat', NULL, NULL, NULL),
(626, 'Nincsenek szűrőpanelek', 'ee', 'Nincsenek szűrőpanelek', NULL, NULL, NULL),
(627, 'nov', 'ee', 'nov', NULL, NULL, NULL),
(628, 'november', 'ee', 'november', NULL, NULL, NULL),
(629, 'Nulla találat', 'ee', 'Nulla találat', NULL, NULL, NULL),
(630, 'Nyelvek', 'ee', 'Nyelvek', NULL, NULL, NULL),
(631, 'Nyitott', 'ee', 'Nyitott', NULL, NULL, NULL),
(632, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'ee', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, NULL, NULL),
(633, 'Nyomtat', 'ee', 'Nyomtat', NULL, NULL, NULL),
(634, 'Nyomtatás', 'ee', 'Nyomtatás', NULL, NULL, NULL),
(635, 'okt', 'ee', 'okt', NULL, NULL, NULL),
(636, 'október', 'ee', 'október', NULL, NULL, NULL),
(637, 'Óra', 'ee', 'Óra', NULL, NULL, NULL),
(638, 'Összes', 'ee', 'Összes', NULL, NULL, NULL),
(639, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'ee', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, NULL, NULL),
(640, 'Összes feltétel törlése', 'ee', 'Összes feltétel törlése', NULL, NULL, NULL),
(641, 'Összes kosár', 'ee', 'Összes kosár', NULL, NULL, NULL),
(642, 'Összes megrendelés', 'ee', 'Összes megrendelés', NULL, NULL, NULL),
(643, 'Összes sor megjelenítése', 'ee', 'Összes sor megjelenítése', NULL, NULL, NULL),
(644, 'Oszlopok', 'ee', 'Oszlopok', NULL, NULL, NULL),
(645, 'Oszlopok visszaállítása', 'ee', 'Oszlopok visszaállítása', NULL, NULL, NULL),
(646, 'Partner cég', 'ee', 'Partner cég', NULL, NULL, NULL),
(647, 'Partner felhasználók', 'ee', 'Partner felhasználók', NULL, NULL, NULL),
(648, 'péntek', 'ee', 'péntek', NULL, NULL, NULL),
(649, 'Pénznem', 'ee', 'Pénznem', NULL, NULL, NULL),
(650, 'Perc', 'ee', 'Perc', NULL, NULL, NULL),
(651, 'Product', 'ee', 'Product', NULL, NULL, NULL),
(652, 'Profil', 'ee', 'Profil', NULL, NULL, NULL),
(653, 'rendszergazdák', 'ee', 'rendszergazdák', NULL, NULL, NULL),
(654, 'Saját megrendelés', 'ee', 'Saját megrendelés', NULL, NULL, NULL),
(655, 'Szabad', 'ee', 'Szabad', NULL, NULL, NULL),
(656, 'Szállítási mód', 'ee', 'Szállítási mód', NULL, NULL, NULL),
(657, 'szept', 'ee', 'szept', NULL, NULL, NULL),
(658, 'szeptember', 'ee', 'szeptember', NULL, NULL, NULL),
(659, 'szerda', 'ee', 'szerda', NULL, NULL, NULL),
(660, 'szombat', 'ee', 'szombat', NULL, NULL, NULL),
(661, 'Szűrők törlése', 'ee', 'Szűrők törlése', NULL, NULL, NULL),
(662, 'Szűrőpanelek', 'ee', 'Szűrőpanelek', NULL, NULL, NULL),
(663, 'Szűrőpanelek (%d)', 'ee', 'Szűrőpanelek (%d)', NULL, NULL, NULL),
(664, 'Szűrőpanelek betöltése', 'ee', 'Szűrőpanelek betöltése', NULL, NULL, NULL),
(665, 'Táblázat', 'ee', 'Táblázat', NULL, NULL, NULL),
(666, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'ee', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, NULL, NULL),
(667, 'Tartalmazza', 'ee', 'Tartalmazza', NULL, NULL, NULL),
(668, 'Telephely', 'ee', 'Telephely', NULL, NULL, NULL),
(669, 'Teljes képernyő', 'ee', 'Teljes képernyő', NULL, NULL, NULL),
(670, 'Termék', 'ee', 'Termék', NULL, NULL, NULL),
(671, 'Termék kategória', 'ee', 'Termék kategória', NULL, NULL, NULL),
(672, 'Tétel', 'ee', 'Tétel', NULL, NULL, NULL),
(673, 'Tételek', 'ee', 'Tételek', NULL, NULL, NULL),
(674, 'Tétetek kosárba másolás!', 'ee', 'Tétetek kosárba másolás!', NULL, NULL, NULL),
(675, 'Törlés', 'ee', 'Törlés', NULL, NULL, NULL),
(676, 'Tovább', 'ee', 'Tovább', NULL, NULL, NULL),
(677, 'Új', 'ee', 'Új', NULL, NULL, NULL),
(678, 'Új Kosár', 'ee', 'Új Kosár', NULL, NULL, NULL),
(679, 'Üres', 'ee', 'Üres', NULL, NULL, NULL),
(680, 'Után', 'ee', 'Után', NULL, NULL, NULL),
(681, 'Utolsó', 'ee', 'Utolsó', NULL, NULL, NULL),
(682, 'Vágólapra másolás', 'ee', 'Vágólapra másolás', NULL, NULL, NULL),
(683, 'Vagy', 'ee', 'Vagy', NULL, NULL, NULL),
(684, 'Van már nyitott kosara!', 'ee', 'Van már nyitott kosara!', NULL, NULL, NULL),
(685, 'vasárnap', 'ee', 'vasárnap', NULL, NULL, NULL),
(686, 'Végződik', 'ee', 'Végződik', NULL, NULL, NULL),
(687, 'Vezérlő', 'ee', 'Vezérlő', NULL, NULL, NULL),
(688, 'Visszaállít', 'ee', 'Visszaállít', NULL, NULL, NULL),
(689, 'XML Import', 'ee', 'XML Import', NULL, NULL, NULL),
(690, '_MENU_ találat oldalanként', 'ee', '_MENU_ találat oldalanként', NULL, NULL, NULL),
(691, '%d cella kiválasztva', 'cz', '%d cella kiválasztva', NULL, NULL, NULL),
(692, '%d oszlop kiválasztva', 'cz', '%d oszlop kiválasztva', NULL, NULL, NULL),
(693, '%d sor kiválasztva', 'cz', '%d sor kiválasztva', NULL, '2022-06-21 05:20:40', NULL),
(694, '%d sor másolva', 'cz', '%d sor másolva', NULL, NULL, NULL),
(695, '%d sor megjelenítése', 'cz', '%d sor megjelenítése', NULL, '2022-06-21 05:20:46', NULL),
(696, '(_MAX_ összes rekord közül szűrve)', 'cz', '(_MAX_ összes rekord közül szűrve)', NULL, NULL, NULL),
(697, '1 cella kiválasztva', 'cz', '1 cella kiválasztva', NULL, NULL, NULL),
(698, '1 oszlop kiválasztva', 'cz', '1 oszlop kiválasztva', NULL, NULL, NULL),
(699, '1 sor kiválasztva', 'cz', '1 sor kiválasztva', NULL, NULL, NULL),
(700, '1 sor másolva', 'cz', '1 sor másolva', NULL, NULL, NULL),
(701, ': aktiválja a csökkenő rendezéshez', 'cz', ': aktiválja a csökkenő rendezéshez', NULL, NULL, NULL),
(702, ': aktiválja a növekvő rendezéshez', 'cz', ': aktiválja a növekvő rendezéshez', NULL, NULL, NULL),
(703, 'Adat', 'cz', 'Adat', NULL, NULL, NULL),
(704, 'ÁFA', 'cz', 'ÁFA', NULL, NULL, NULL),
(705, 'Aktív szűrőpanelek: %d', 'cz', 'Aktív szűrőpanelek: %d', NULL, NULL, NULL),
(706, 'ápr', 'cz', 'ápr', NULL, NULL, NULL),
(707, 'április', 'cz', 'április', NULL, NULL, NULL),
(708, 'aug', 'cz', 'aug', NULL, NULL, NULL),
(709, 'augusztus', 'cz', 'augusztus', NULL, NULL, NULL),
(710, 'B2B felhasználók', 'cz', 'B2B felhasználók', NULL, NULL, NULL),
(711, 'B2B partnerek', 'cz', 'B2B partnerek', NULL, NULL, NULL),
(712, 'Beállítások', 'cz', 'Beállítások', NULL, NULL, NULL),
(713, 'Bejelentkezés', 'cz', 'Bejelentkezés', NULL, NULL, NULL),
(714, 'Belép', 'cz', 'Belép', NULL, NULL, NULL),
(715, 'Belépés 3 hónap', 'cz', 'Belépés 3 hónap', NULL, NULL, NULL),
(716, 'Belépés 3 hónap<', 'cz', 'Belépés 3 hónap<', NULL, NULL, NULL),
(717, 'Belépett', 'cz', 'Belépett', NULL, NULL, NULL),
(718, 'Belső felhasználók', 'cz', 'Belső felhasználók', NULL, NULL, NULL),
(719, 'Beosztás', 'cz', 'Beosztás', NULL, NULL, NULL),
(720, 'Betöltés...', 'cz', 'Betöltés...', NULL, NULL, NULL),
(721, 'Bezárás', 'cz', 'Bezárás', NULL, NULL, NULL),
(722, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'cz', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, NULL, NULL),
(723, 'Biztosan kosárba másolja a tételeket?', 'cz', 'Biztosan kosárba másolja a tételeket?', NULL, NULL, NULL),
(724, 'Bruttó', 'cz', 'Bruttó', NULL, NULL, NULL),
(725, 'Cellák függőleges kitöltése', 'cz', 'Cellák függőleges kitöltése', NULL, NULL, NULL),
(726, 'Cellák vízszintes kitöltése', 'cz', 'Cellák vízszintes kitöltése', NULL, NULL, NULL),
(727, 'csütörtök', 'cz', 'csütörtök', NULL, NULL, NULL),
(728, 'darab', 'cz', 'darab', NULL, NULL, NULL),
(729, 'Dátum', 'cz', 'Dátum', NULL, NULL, NULL),
(730, 'db', 'cz', 'db', NULL, NULL, NULL),
(731, 'de.', 'cz', 'de.', NULL, NULL, NULL),
(732, 'dec', 'cz', 'dec', NULL, NULL, NULL),
(733, 'december', 'cz', 'december', NULL, NULL, NULL),
(734, 'du.', 'cz', 'du.', NULL, NULL, NULL),
(735, 'eddig', 'cz', 'eddig', NULL, NULL, NULL),
(736, 'Egyenlő', 'cz', 'Egyenlő', NULL, NULL, NULL),
(737, 'Egys.ár', 'cz', 'Egys.ár', NULL, NULL, NULL),
(738, 'Előtt', 'cz', 'Előtt', NULL, NULL, NULL),
(739, 'Előző', 'cz', 'Előző', NULL, NULL, NULL),
(740, 'Első', 'cz', 'Első', NULL, NULL, NULL),
(741, 'Email', 'cz', 'Email', NULL, NULL, NULL),
(742, 'Érték', 'cz', 'Érték', NULL, NULL, NULL),
(743, 'És', 'cz', 'És', NULL, NULL, NULL),
(744, 'ettől', 'cz', 'ettől', NULL, NULL, NULL),
(745, 'Exportál', 'cz', 'Exportál', NULL, NULL, NULL),
(746, 'febr', 'cz', 'febr', NULL, NULL, NULL),
(747, 'február', 'cz', 'február', NULL, NULL, NULL),
(748, 'Feldolgozás...', 'cz', 'Feldolgozás...', NULL, NULL, NULL),
(749, 'Felhasználói belépések', 'cz', 'Felhasználói belépések', NULL, NULL, NULL),
(750, 'felhasználók', 'cz', 'felhasználók', NULL, NULL, NULL),
(751, 'Felhasználók összesen', 'cz', 'Felhasználók összesen', NULL, NULL, NULL),
(752, 'Felhasználónként', 'cz', 'Felhasználónként', NULL, NULL, NULL),
(753, 'Felhasznált', 'cz', 'Felhasznált', NULL, NULL, NULL),
(754, 'Feltétel', 'cz', 'Feltétel', NULL, NULL, NULL),
(755, 'Feltétel hozzáadása', 'cz', 'Feltétel hozzáadása', NULL, NULL, NULL),
(756, 'Feltétel törlése', 'cz', 'Feltétel törlése', NULL, NULL, NULL),
(757, 'forint', 'cz', 'forint', NULL, NULL, NULL),
(758, 'havi bontás', 'cz', 'havi bontás', NULL, NULL, NULL),
(759, 'hétfő', 'cz', 'hétfő', NULL, NULL, NULL),
(760, 'Hitel keret', 'cz', 'Hitel keret', NULL, NULL, NULL),
(761, 'Id', 'cz', 'Id', NULL, NULL, NULL),
(762, 'Idei', 'cz', 'Idei', NULL, NULL, NULL),
(763, 'Idei kosár', 'cz', 'Idei kosár', NULL, NULL, NULL),
(764, 'Idei megrendelés', 'cz', 'Idei megrendelés', NULL, NULL, NULL),
(765, 'Idei megrendelések', 'cz', 'Idei megrendelések', NULL, NULL, NULL),
(766, 'Idei saját megrendelés', 'cz', 'Idei saját megrendelés', NULL, NULL, NULL),
(767, 'Idei saját megrendelések', 'cz', 'Idei saját megrendelések', NULL, NULL, NULL),
(768, 'Importál', 'cz', 'Importál', NULL, NULL, NULL),
(769, 'jan', 'cz', 'jan', NULL, NULL, NULL),
(770, 'január', 'cz', 'január', NULL, NULL, NULL),
(771, 'júl', 'cz', 'júl', NULL, NULL, NULL),
(772, 'július', 'cz', 'július', NULL, NULL, NULL),
(773, 'jún', 'cz', 'jún', NULL, NULL, NULL),
(774, 'június', 'cz', 'június', NULL, NULL, NULL),
(775, 'kedd', 'cz', 'kedd', NULL, NULL, NULL),
(776, 'Kedvenc', 'cz', 'Kedvenc', NULL, NULL, NULL),
(777, 'Kedvenc termék kiválasztás', 'cz', 'Kedvenc termék kiválasztás', NULL, NULL, NULL),
(778, 'Kedvenc termékek', 'cz', 'Kedvenc termékek', NULL, NULL, NULL),
(779, 'Kép', 'cz', 'Kép', NULL, NULL, NULL),
(780, 'Keresés konfigurátor', 'cz', 'Keresés konfigurátor', NULL, NULL, NULL),
(781, 'Keresés konfigurátor (%d)', 'cz', 'Keresés konfigurátor (%d)', NULL, NULL, NULL),
(782, 'Keresés:', 'cz', 'Keresés:', NULL, NULL, NULL),
(783, 'Kezdődik', 'cz', 'Kezdődik', NULL, NULL, NULL),
(784, 'Kilép', 'cz', 'Kilép', NULL, NULL, NULL),
(785, 'Kilépés', 'cz', 'Kilépés', NULL, NULL, NULL),
(786, 'Kilépés a teljes képernyőből', 'cz', 'Kilépés a teljes képernyőből', NULL, NULL, NULL),
(787, 'Kissebb mint', 'cz', 'Kissebb mint', NULL, NULL, NULL),
(788, 'Kissebb vagy egyenlő mint', 'cz', 'Kissebb vagy egyenlő mint', NULL, NULL, NULL),
(789, 'Kívül eső', 'cz', 'Kívül eső', NULL, NULL, NULL),
(790, 'Kosár', 'cz', 'Kosár', NULL, NULL, NULL),
(791, 'Kosárba', 'cz', 'Kosárba', NULL, NULL, NULL),
(792, 'Következő', 'cz', 'Következő', NULL, NULL, NULL),
(793, 'Között', 'cz', 'Között', NULL, NULL, NULL),
(794, 'Letöltés CSV fileként', 'cz', 'Letöltés CSV fileként', NULL, NULL, NULL),
(795, 'Letöltés JPEG képként', 'cz', 'Letöltés JPEG képként', NULL, NULL, NULL),
(796, 'Letöltés PDF dokumentumként', 'cz', 'Letöltés PDF dokumentumként', NULL, NULL, NULL),
(797, 'Letöltés PNG képként', 'cz', 'Letöltés PNG képként', NULL, NULL, NULL),
(798, 'Letöltés SVG formátumban', 'cz', 'Letöltés SVG formátumban', NULL, NULL, NULL),
(799, 'Letöltés XLS fileként', 'cz', 'Letöltés XLS fileként', NULL, NULL, NULL),
(800, 'Létrehozás', 'cz', 'Létrehozás', NULL, NULL, NULL),
(801, 'Log adatok', 'cz', 'Log adatok', NULL, NULL, NULL),
(802, 'máj', 'cz', 'máj', NULL, NULL, NULL),
(803, 'május', 'cz', 'május', NULL, NULL, NULL),
(804, 'márc', 'cz', 'márc', NULL, NULL, NULL),
(805, 'március', 'cz', 'március', NULL, NULL, NULL),
(806, 'Másodperc', 'cz', 'Másodperc', NULL, NULL, NULL),
(807, 'Másolás', 'cz', 'Másolás', NULL, NULL, NULL),
(808, 'Me.egys', 'cz', 'Me.egys', NULL, NULL, NULL),
(809, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'cz', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(810, 'Megrendelés darab az elmúlt 12 hónapban', 'cz', 'Megrendelés darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(811, 'Megrendelés értékek az elmúlt 12 hónapban', 'cz', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(812, 'Megrendelés kosárba másolás!', 'cz', 'Megrendelés kosárba másolás!', NULL, NULL, NULL),
(813, 'Megrendelés szám', 'cz', 'Megrendelés szám', NULL, NULL, NULL),
(814, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'cz', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(815, 'Megrendelések', 'cz', 'Megrendelések', NULL, NULL, NULL),
(816, 'Megszakítás', 'cz', 'Megszakítás', NULL, NULL, NULL),
(817, 'Mennyiség', 'cz', 'Mennyiség', NULL, NULL, NULL),
(818, 'Minden termék', 'cz', 'Minden termék', NULL, NULL, NULL),
(819, 'Módosítás', 'cz', 'Módosítás', NULL, NULL, NULL),
(820, 'mutat:', 'cz', 'mutat:', NULL, NULL, NULL),
(821, 'Nagyobb mint', 'cz', 'Nagyobb mint', NULL, NULL, NULL),
(822, 'Nagyobb vagy egyenlő mint', 'cz', 'Nagyobb vagy egyenlő mint', NULL, NULL, NULL),
(823, 'Nem', 'cz', 'Nem', NULL, NULL, NULL),
(824, 'Nem jelölt ki sort', 'cz', 'Nem jelölt ki sort', NULL, NULL, NULL),
(825, 'Nem üres', 'cz', 'Nem üres', NULL, NULL, NULL),
(826, 'Nemzetiség', 'cz', 'Nemzetiség', NULL, NULL, NULL),
(827, 'Nettó', 'cz', 'Nettó', NULL, NULL, NULL),
(828, 'Név', 'cz', 'Név', NULL, NULL, NULL),
(829, 'Nincs a keresésnek megfelelő találat', 'cz', 'Nincs a keresésnek megfelelő találat', NULL, NULL, NULL),
(830, 'Nincs kijelölt tétel!', 'cz', 'Nincs kijelölt tétel!', NULL, NULL, NULL),
(831, 'Nincs rendelkezésre álló adat', 'cz', 'Nincs rendelkezésre álló adat', NULL, NULL, NULL),
(832, 'Nincsenek szűrőpanelek', 'cz', 'Nincsenek szűrőpanelek', NULL, NULL, NULL),
(833, 'nov', 'cz', 'nov', NULL, NULL, NULL),
(834, 'november', 'cz', 'november', NULL, NULL, NULL),
(835, 'Nulla találat', 'cz', 'Nulla találat', NULL, NULL, NULL),
(836, 'Nyelvek', 'cz', 'Nyelvek', NULL, NULL, NULL),
(837, 'Nyitott', 'cz', 'Nyitott', NULL, NULL, NULL),
(838, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'cz', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, NULL, NULL),
(839, 'Nyomtat', 'cz', 'Nyomtat', NULL, NULL, NULL),
(840, 'Nyomtatás', 'cz', 'Nyomtatás', NULL, NULL, NULL),
(841, 'okt', 'cz', 'okt', NULL, NULL, NULL),
(842, 'október', 'cz', 'október', NULL, NULL, NULL),
(843, 'Óra', 'cz', 'Óra', NULL, NULL, NULL),
(844, 'Összes', 'cz', 'Összes', NULL, NULL, NULL),
(845, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'cz', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, NULL, NULL);
INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(846, 'Összes feltétel törlése', 'cz', 'Összes feltétel törlése', NULL, NULL, NULL),
(847, 'Összes kosár', 'cz', 'Összes kosár', NULL, NULL, NULL),
(848, 'Összes megrendelés', 'cz', 'Összes megrendelés', NULL, NULL, NULL),
(849, 'Összes sor megjelenítése', 'cz', 'Összes sor megjelenítése', NULL, NULL, NULL),
(850, 'Oszlopok', 'cz', 'Oszlopok', NULL, NULL, NULL),
(851, 'Oszlopok visszaállítása', 'cz', 'Oszlopok visszaállítása', NULL, NULL, NULL),
(852, 'Partner cég', 'cz', 'Partner cég', NULL, NULL, NULL),
(853, 'Partner felhasználók', 'cz', 'Partner felhasználók', NULL, NULL, NULL),
(854, 'péntek', 'cz', 'péntek', NULL, NULL, NULL),
(855, 'Pénznem', 'cz', 'Pénznem', NULL, NULL, NULL),
(856, 'Perc', 'cz', 'Perc', NULL, NULL, NULL),
(857, 'Product', 'cz', 'Product', NULL, NULL, NULL),
(858, 'Profil', 'cz', 'Profil', NULL, NULL, NULL),
(859, 'rendszergazdák', 'cz', 'rendszergazdák', NULL, NULL, NULL),
(860, 'Saját megrendelés', 'cz', 'Saját megrendelés', NULL, NULL, NULL),
(861, 'Szabad', 'cz', 'Szabad', NULL, NULL, NULL),
(862, 'Szállítási mód', 'cz', 'Szállítási mód', NULL, NULL, NULL),
(863, 'szept', 'cz', 'szept', NULL, NULL, NULL),
(864, 'szeptember', 'cz', 'szeptember', NULL, NULL, NULL),
(865, 'szerda', 'cz', 'szerda', NULL, NULL, NULL),
(866, 'szombat', 'cz', 'szombat', NULL, NULL, NULL),
(867, 'Szűrők törlése', 'cz', 'Szűrők törlése', NULL, NULL, NULL),
(868, 'Szűrőpanelek', 'cz', 'Szűrőpanelek', NULL, NULL, NULL),
(869, 'Szűrőpanelek (%d)', 'cz', 'Szűrőpanelek (%d)', NULL, NULL, NULL),
(870, 'Szűrőpanelek betöltése', 'cz', 'Szűrőpanelek betöltése', NULL, NULL, NULL),
(871, 'Táblázat', 'cz', 'Táblázat', NULL, NULL, NULL),
(872, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'cz', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, NULL, NULL),
(873, 'Tartalmazza', 'cz', 'Tartalmazza', NULL, NULL, NULL),
(874, 'Telephely', 'cz', 'Telephely', NULL, NULL, NULL),
(875, 'Teljes képernyő', 'cz', 'Teljes képernyő', NULL, NULL, NULL),
(876, 'Termék', 'cz', 'Termék', NULL, NULL, NULL),
(877, 'Termék kategória', 'cz', 'Termék kategória', NULL, NULL, NULL),
(878, 'Tétel', 'cz', 'Tétel', NULL, NULL, NULL),
(879, 'Tételek', 'cz', 'Tételek', NULL, NULL, NULL),
(880, 'Tétetek kosárba másolás!', 'cz', 'Tétetek kosárba másolás!', NULL, NULL, NULL),
(881, 'Törlés', 'cz', 'Törlés', NULL, NULL, NULL),
(882, 'Tovább', 'cz', 'Tovább', NULL, NULL, NULL),
(883, 'Új', 'cz', 'Új', NULL, NULL, NULL),
(884, 'Új Kosár', 'cz', 'Új Kosár', NULL, NULL, NULL),
(885, 'Üres', 'cz', 'Üres', NULL, NULL, NULL),
(886, 'Után', 'cz', 'Után', NULL, NULL, NULL),
(887, 'Utolsó', 'cz', 'Utolsó', NULL, NULL, NULL),
(888, 'Vágólapra másolás', 'cz', 'Vágólapra másolás', NULL, NULL, NULL),
(889, 'Vagy', 'cz', 'Vagy', NULL, NULL, NULL),
(890, 'Van már nyitott kosara!', 'cz', 'Van már nyitott kosara!', NULL, NULL, NULL),
(891, 'vasárnap', 'cz', 'vasárnap', NULL, NULL, NULL),
(892, 'Végződik', 'cz', 'Végződik', NULL, NULL, NULL),
(893, 'Vezérlő', 'cz', 'Vezérlő', NULL, NULL, NULL),
(894, 'Visszaállít', 'cz', 'Visszaállít', NULL, NULL, NULL),
(895, 'XML Import', 'cz', 'XML Import', NULL, NULL, NULL),
(896, '_MENU_ találat oldalanként', 'cz', '_MENU_ találat oldalanként', NULL, NULL, NULL),
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
(909, 'Fordítás', 'hu', 'Fordítás', NULL, NULL, NULL),
(910, 'Fordítás', 'en', 'Fordítás', NULL, NULL, NULL),
(911, 'Fordítás', 'cz', 'Fordítás', NULL, NULL, NULL),
(912, 'Fordítás', 'ee', 'Fordítás', NULL, NULL, NULL),
(913, 'Magyar', 'hu', 'Magyar', NULL, NULL, NULL),
(914, 'Magyar', 'en', 'Magyar', NULL, NULL, NULL),
(915, 'Magyar', 'cz', 'Magyar', NULL, NULL, NULL),
(916, 'Magyar', 'ee', 'Magyar', NULL, NULL, NULL),
(917, 'B2B partner', 'hu', 'B2B partner', NULL, NULL, NULL),
(918, 'B2B partner', 'en', 'B2B partner', NULL, NULL, NULL),
(919, 'B2B partner', 'cz', 'B2B partner', NULL, NULL, NULL),
(920, 'B2B partner', 'ee', 'B2B partner', NULL, NULL, NULL),
(921, 'Partner felhasználó', 'hu', 'Partner felhasználó', NULL, NULL, NULL),
(922, 'Partner felhasználó', 'en', 'Partner felhasználó', NULL, NULL, NULL),
(923, 'Partner felhasználó', 'cz', 'Partner felhasználó', NULL, NULL, NULL),
(924, 'Partner felhasználó', 'ee', 'Partner felhasználó', NULL, NULL, NULL),
(925, '%d cella kiválasztva', 'bg', '%d cella kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(926, '%d oszlop kiválasztva', 'bg', '%d oszlop kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(927, '%d sor kiválasztva', 'bg', '%d sor kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(928, '%d sor másolva', 'bg', '%d sor másolva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(929, '%d sor megjelenítése', 'bg', '%d sor megjelenítése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(930, '(_MAX_ összes rekord közül szűrve)', 'bg', '(_MAX_ összes rekord közül szűrve)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(931, '1 cella kiválasztva', 'bg', '1 cella kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(932, '1 oszlop kiválasztva', 'bg', '1 oszlop kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(933, '1 sor kiválasztva', 'bg', '1 sor kiválasztva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(934, '1 sor másolva', 'bg', '1 sor másolva', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(935, ': aktiválja a csökkenő rendezéshez', 'bg', ': aktiválja a csökkenő rendezéshez', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(936, ': aktiválja a növekvő rendezéshez', 'bg', ': aktiválja a növekvő rendezéshez', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(937, 'Adat', 'bg', 'Adat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(938, 'ÁFA', 'bg', 'ÁFA', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(939, 'Aktív szűrőpanelek: %d', 'bg', 'Aktív szűrőpanelek: %d', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(940, 'ápr', 'bg', 'ápr', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(941, 'április', 'bg', 'április', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(942, 'aug', 'bg', 'aug', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(943, 'augusztus', 'bg', 'augusztus', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(944, 'B2B felhasználók', 'bg', 'B2B felhasználók', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(945, 'B2B partner', 'bg', 'B2B partner', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(946, 'B2B partnerek', 'bg', 'B2B partnerek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(947, 'Beállítások', 'bg', 'Beállítások', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(948, 'Bejelentkezés', 'bg', 'Bejelentkezés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(949, 'Belép', 'bg', 'Belép', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(950, 'Belépés 3 hónap', 'bg', 'Belépés 3 hónap', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(951, 'Belépés 3 hónap<', 'bg', 'Belépés 3 hónap<', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(952, 'Belépett', 'bg', 'Belépett', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(953, 'Belső felhasználók', 'bg', 'Belső felhasználók', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(954, 'Beosztás', 'bg', 'Beosztás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(955, 'Betöltés...', 'bg', 'Betöltés...', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(956, 'Bezárás', 'bg', 'Bezárás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(957, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'bg', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(958, 'Biztosan kosárba másolja a tételeket?', 'bg', 'Biztosan kosárba másolja a tételeket?', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(959, 'Bruttó', 'bg', 'Bruttó', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(960, 'Cellák függőleges kitöltése', 'bg', 'Cellák függőleges kitöltése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(961, 'Cellák vízszintes kitöltése', 'bg', 'Cellák vízszintes kitöltése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(962, 'csütörtök', 'bg', 'csütörtök', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(963, 'darab', 'bg', 'darab', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(964, 'Dátum', 'bg', 'Dátum', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(965, 'db', 'bg', 'db', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(966, 'de.', 'bg', 'de.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(967, 'dec', 'bg', 'dec', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(968, 'december', 'bg', 'december', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(969, 'du.', 'bg', 'du.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(970, 'eddig', 'bg', 'eddig', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(971, 'Egyenlő', 'bg', 'Egyenlő', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(972, 'Egys.ár', 'bg', 'Egys.ár', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(973, 'Előtt', 'bg', 'Előtt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(974, 'Előző', 'bg', 'Előző', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(975, 'Első', 'bg', 'Első', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(976, 'Email', 'bg', 'Email', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(977, 'Érték', 'bg', 'Érték', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(978, 'És', 'bg', 'És', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(979, 'ettől', 'bg', 'ettől', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(980, 'Exportál', 'bg', 'Exportál', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(981, 'febr', 'bg', 'febr', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(982, 'február', 'bg', 'február', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(983, 'Feldolgozás...', 'bg', 'Feldolgozás...', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(984, 'Felhasználói belépések', 'bg', 'Felhasználói belépések', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(985, 'felhasználók', 'bg', 'felhasználók', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(986, 'Felhasználók összesen', 'bg', 'Felhasználók összesen', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(987, 'Felhasználónként', 'bg', 'Felhasználónként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(988, 'Felhasznált', 'bg', 'Felhasznált', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(989, 'Feltétel', 'bg', 'Feltétel', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(990, 'Feltétel hozzáadása', 'bg', 'Feltétel hozzáadása', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(991, 'Feltétel törlése', 'bg', 'Feltétel törlése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(992, 'Fordítás', 'bg', 'Fordítás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(993, 'forint', 'bg', 'forint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(994, 'havi bontás', 'bg', 'havi bontás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(995, 'hétfő', 'bg', 'hétfő', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(996, 'Hiba', 'bg', 'Hiba', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(997, 'Hitel keret', 'bg', 'Hitel keret', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(998, 'Id', 'bg', 'Id', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(999, 'Idei', 'bg', 'Idei', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1000, 'Idei kosár', 'bg', 'Idei kosár', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1001, 'Idei megrendelés', 'bg', 'Idei megrendelés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1002, 'Idei megrendelések', 'bg', 'Idei megrendelések', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1003, 'Idei saját megrendelés', 'bg', 'Idei saját megrendelés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1004, 'Idei saját megrendelések', 'bg', 'Idei saját megrendelések', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1005, 'Importál', 'bg', 'Importál', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1006, 'jan', 'bg', 'jan', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1007, 'január', 'bg', 'január', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1008, 'júl', 'bg', 'júl', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1009, 'július', 'bg', 'július', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1010, 'jún', 'bg', 'jún', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1011, 'június', 'bg', 'június', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1012, 'kedd', 'bg', 'kedd', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1013, 'Kedvenc', 'bg', 'Kedvenc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1014, 'Kedvenc termék kiválasztás', 'bg', 'Kedvenc termék kiválasztás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1015, 'Kedvenc termékek', 'bg', 'Kedvenc termékek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1016, 'Kép', 'bg', 'Kép', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1017, 'Keresés konfigurátor', 'bg', 'Keresés konfigurátor', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1018, 'Keresés konfigurátor (%d)', 'bg', 'Keresés konfigurátor (%d)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1019, 'Keresés:', 'bg', 'Keresés:', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1020, 'Kezdődik', 'bg', 'Kezdődik', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1021, 'Kilép', 'bg', 'Kilép', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1022, 'Kilépés', 'bg', 'Kilépés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1023, 'Kilépés a teljes képernyőből', 'bg', 'Kilépés a teljes képernyőből', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1024, 'Kissebb mint', 'bg', 'Kissebb mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1025, 'Kissebb vagy egyenlő mint', 'bg', 'Kissebb vagy egyenlő mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1026, 'Kívül eső', 'bg', 'Kívül eső', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1027, 'Kosár', 'bg', 'Kosár', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1028, 'Kosárba', 'bg', 'Kosárba', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1029, 'Következő', 'bg', 'Következő', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1030, 'Között', 'bg', 'Között', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1031, 'Letöltés CSV fileként', 'bg', 'Letöltés CSV fileként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1032, 'Letöltés JPEG képként', 'bg', 'Letöltés JPEG képként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1033, 'Letöltés PDF dokumentumként', 'bg', 'Letöltés PDF dokumentumként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1034, 'Letöltés PNG képként', 'bg', 'Letöltés PNG képként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1035, 'Letöltés SVG formátumban', 'bg', 'Letöltés SVG formátumban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1036, 'Letöltés XLS fileként', 'bg', 'Letöltés XLS fileként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1037, 'Létrehozás', 'bg', 'Létrehozás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1038, 'Log adatok', 'bg', 'Log adatok', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1039, 'Magyar', 'bg', 'Magyar', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1040, 'Magyarul', 'bg', 'Magyarul', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1041, 'máj', 'bg', 'máj', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1042, 'május', 'bg', 'május', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1043, 'márc', 'bg', 'márc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1044, 'március', 'bg', 'március', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1045, 'Másodperc', 'bg', 'Másodperc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1046, 'Másolás', 'bg', 'Másolás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1047, 'Me.egys', 'bg', 'Me.egys', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1048, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'bg', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1049, 'Megrendelés darab az elmúlt 12 hónapban', 'bg', 'Megrendelés darab az elmúlt 12 hónapban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1050, 'Megrendelés értékek az elmúlt 12 hónapban', 'bg', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1051, 'Megrendelés kosárba másolás!', 'bg', 'Megrendelés kosárba másolás!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1052, 'Megrendelés szám', 'bg', 'Megrendelés szám', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1053, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'bg', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1054, 'Megrendelések', 'bg', 'Megrendelések', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1055, 'Megszakítás', 'bg', 'Megszakítás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1056, 'Mennyiség', 'bg', 'Mennyiség', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1057, 'Ment', 'bg', 'Ment', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1058, 'Minden termék', 'bg', 'Minden termék', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1059, 'Módosítás', 'bg', 'Módosítás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1060, 'mutat:', 'bg', 'mutat:', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1061, 'Nagyobb mint', 'bg', 'Nagyobb mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1062, 'Nagyobb vagy egyenlő mint', 'bg', 'Nagyobb vagy egyenlő mint', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1063, 'Nem', 'bg', 'Nem', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1064, 'Nem jelölt ki sort', 'bg', 'Nem jelölt ki sort', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1065, 'Nem üres', 'bg', 'Nem üres', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1066, 'Nemzetiség', 'bg', 'Nemzetiség', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1067, 'Nettó', 'bg', 'Nettó', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1068, 'Név', 'bg', 'Név', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1069, 'Nincs a keresésnek megfelelő találat', 'bg', 'Nincs a keresésnek megfelelő találat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1070, 'Nincs kijelölt tétel!', 'bg', 'Nincs kijelölt tétel!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1071, 'Nincs rendelkezésre álló adat', 'bg', 'Nincs rendelkezésre álló adat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1072, 'Nincsenek szűrőpanelek', 'bg', 'Nincsenek szűrőpanelek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1073, 'nov', 'bg', 'nov', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1074, 'november', 'bg', 'november', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1075, 'Nulla találat', 'bg', 'Nulla találat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1076, 'Nyelvek', 'bg', 'Nyelvek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1077, 'Nyitott', 'bg', 'Nyitott', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1078, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'bg', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1079, 'Nyomtat', 'bg', 'Nyomtat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1080, 'Nyomtatás', 'bg', 'Nyomtatás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1081, 'okt', 'bg', 'okt', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1082, 'október', 'bg', 'október', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1083, 'Óra', 'bg', 'Óra', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1084, 'Összes', 'bg', 'Összes', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1085, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'bg', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1086, 'Összes feltétel törlése', 'bg', 'Összes feltétel törlése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1087, 'Összes kosár', 'bg', 'Összes kosár', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1088, 'Összes megrendelés', 'bg', 'Összes megrendelés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1089, 'Összes sor megjelenítése', 'bg', 'Összes sor megjelenítése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1090, 'Oszlopok', 'bg', 'Oszlopok', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1091, 'Oszlopok visszaállítása', 'bg', 'Oszlopok visszaállítása', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1092, 'Partner cég', 'bg', 'Partner cég', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1093, 'Partner felhasználó', 'bg', 'Partner felhasználó', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1094, 'Partner felhasználók', 'bg', 'Partner felhasználók', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1095, 'péntek', 'bg', 'péntek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1096, 'Pénznem', 'bg', 'Pénznem', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1097, 'Perc', 'bg', 'Perc', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1098, 'Product', 'bg', 'Product', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1099, 'Profil', 'bg', 'Profil', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1100, 'rendszergazdák', 'bg', 'rendszergazdák', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1101, 'Saját megrendelés', 'bg', 'Saját megrendelés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1102, 'Szabad', 'bg', 'Szabad', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1103, 'Szállítási mód', 'bg', 'Szállítási mód', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1104, 'szept', 'bg', 'szept', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1105, 'szeptember', 'bg', 'szeptember', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1106, 'szerda', 'bg', 'szerda', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1107, 'szombat', 'bg', 'szombat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1108, 'Szűrők törlése', 'bg', 'Szűrők törlése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1109, 'Szűrőpanelek', 'bg', 'Szűrőpanelek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1110, 'Szűrőpanelek (%d)', 'bg', 'Szűrőpanelek (%d)', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1111, 'Szűrőpanelek betöltése', 'bg', 'Szűrőpanelek betöltése', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1112, 'Táblázat', 'bg', 'Táblázat', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1113, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'bg', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1114, 'Tartalmazza', 'bg', 'Tartalmazza', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1115, 'Telephely', 'bg', 'Telephely', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1116, 'Teljes képernyő', 'bg', 'Teljes képernyő', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1117, 'Termék', 'bg', 'Termék', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1118, 'Termék kategória', 'bg', 'Termék kategória', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1119, 'Tétel', 'bg', 'Tétel', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1120, 'Tételek', 'bg', 'Tételek', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1121, 'Tétetek kosárba másolás!', 'bg', 'Tétetek kosárba másolás!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1122, 'Törlés', 'bg', 'Törlés', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1123, 'Tovább', 'bg', 'Tovább', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1124, 'Új', 'bg', 'Új', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1125, 'Új Kosár', 'bg', 'Új Kosár', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1126, 'Üres', 'bg', 'Üres', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1127, 'Után', 'bg', 'Után', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1128, 'Utolsó', 'bg', 'Utolsó', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1129, 'Vágólapra másolás', 'bg', 'Vágólapra másolás', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1130, 'Vagy', 'bg', 'Vagy', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1131, 'Van már nyitott kosara!', 'bg', 'Van már nyitott kosara!', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1132, 'vasárnap', 'bg', 'vasárnap', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1133, 'Végződik', 'bg', 'Végződik', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1134, 'Vezérlő', 'bg', 'Vezérlő', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1135, 'Visszaállít', 'bg', 'Visszaállít', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1136, 'XML Import', 'bg', 'XML Import', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
(1137, '_MENU_ találat oldalanként', 'bg', '_MENU_ találat oldalanként', NULL, '2022-06-15 07:12:35', '2022-06-15 07:12:35'),
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
(1158, '%d cella kiválasztva', 'de', '%d cella kiválasztva', NULL, NULL, NULL),
(1159, '%d oszlop kiválasztva', 'de', '%d oszlop kiválasztva', NULL, NULL, NULL),
(1160, '%d sor kiválasztva', 'de', '%d sor kiválasztva', NULL, NULL, NULL),
(1161, '%d sor másolva', 'de', '%d sor másolva', NULL, NULL, NULL),
(1162, '%d sor megjelenítése', 'de', '%d sor megjelenítése', NULL, NULL, NULL),
(1163, '(_MAX_ összes rekord közül szűrve)', 'de', '(_MAX_ összes rekord közül szűrve)', NULL, NULL, NULL),
(1164, '1 cella kiválasztva', 'de', '1 cella kiválasztva', NULL, NULL, NULL),
(1165, '1 oszlop kiválasztva', 'de', '1 oszlop kiválasztva', NULL, NULL, NULL),
(1166, '1 sor kiválasztva', 'de', '1 sor kiválasztva', NULL, NULL, NULL),
(1167, '1 sor másolva', 'de', '1 sor másolva', NULL, NULL, NULL),
(1168, ': aktiválja a csökkenő rendezéshez', 'de', ': aktiválja a csökkenő rendezéshez', NULL, NULL, NULL),
(1169, ': aktiválja a növekvő rendezéshez', 'de', ': aktiválja a növekvő rendezéshez', NULL, NULL, NULL),
(1170, 'Adat', 'de', 'Adat', NULL, NULL, NULL),
(1171, 'ÁFA', 'de', 'ÁFA', NULL, NULL, NULL),
(1172, 'Aktív szűrőpanelek: %d', 'de', 'Aktív szűrőpanelek: %d', NULL, NULL, NULL),
(1173, 'ápr', 'de', 'ápr', NULL, NULL, NULL),
(1174, 'április', 'de', 'április', NULL, NULL, NULL),
(1175, 'aug', 'de', 'aug', NULL, NULL, NULL),
(1176, 'augusztus', 'de', 'augusztus', NULL, NULL, NULL),
(1177, 'B2B felhasználók', 'de', 'B2B felhasználók', NULL, NULL, NULL),
(1178, 'B2B partner', 'de', 'B2B partner', NULL, NULL, NULL),
(1179, 'B2B partnerek', 'de', 'B2B partnerek', NULL, NULL, NULL),
(1180, 'Beállítások', 'de', 'Beállítások', NULL, NULL, NULL),
(1181, 'Bejelentkezés', 'de', 'Bejelentkezés', NULL, NULL, NULL),
(1182, 'Bekapcsol', 'de', 'Bekapcsol', NULL, NULL, NULL),
(1183, 'Belép', 'de', 'Belép', NULL, NULL, NULL),
(1184, 'Belépés 3 hónap', 'de', 'Belépés 3 hónap', NULL, NULL, NULL),
(1185, 'Belépés 3 hónap<', 'de', 'Belépés 3 hónap<', NULL, NULL, NULL),
(1186, 'Belépett', 'de', 'Belépett', NULL, NULL, NULL),
(1187, 'Belső felhasználók', 'de', 'Belső felhasználók', NULL, NULL, NULL),
(1188, 'Beosztás', 'de', 'Beosztás', NULL, NULL, NULL),
(1189, 'Betöltés...', 'de', 'Betöltés...', NULL, NULL, NULL),
(1190, 'Bezárás', 'de', 'Bezárás', NULL, NULL, NULL),
(1191, 'Biztos, hogy be akarja kapcsolni a nyelvet?', 'de', 'Biztos, hogy be akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1192, 'Biztos, hogy ki akarja kapcsolni a nyelvet?', 'de', 'Biztos, hogy ki akarja kapcsolni a nyelvet?', NULL, NULL, NULL),
(1193, 'Biztosan kosárba másolja a megrendelés összes tételét?', 'de', 'Biztosan kosárba másolja a megrendelés összes tételét?', NULL, NULL, NULL),
(1194, 'Biztosan kosárba másolja a tételeket?', 'de', 'Biztosan kosárba másolja a tételeket?', NULL, NULL, NULL),
(1195, 'Bruttó', 'de', 'Bruttó', NULL, NULL, NULL),
(1196, 'Cellák függőleges kitöltése', 'de', 'Cellák függőleges kitöltése', NULL, NULL, NULL),
(1197, 'Cellák vízszintes kitöltése', 'de', 'Cellák vízszintes kitöltése', NULL, NULL, NULL),
(1198, 'csütörtök', 'de', 'csütörtök', NULL, NULL, NULL),
(1199, 'darab', 'de', 'darab', NULL, NULL, NULL),
(1200, 'Dátum', 'de', 'Dátum', NULL, NULL, NULL),
(1201, 'db', 'de', 'db', NULL, NULL, NULL),
(1202, 'de.', 'de', 'de.', NULL, NULL, NULL),
(1203, 'dec', 'de', 'dec', NULL, NULL, NULL),
(1204, 'december', 'de', 'december', NULL, NULL, NULL),
(1205, 'du.', 'de', 'du.', NULL, NULL, NULL),
(1206, 'eddig', 'de', 'eddig', NULL, NULL, NULL),
(1207, 'Egyenlő', 'de', 'Egyenlő', NULL, NULL, NULL),
(1208, 'Egys.ár', 'de', 'Egys.ár', NULL, NULL, NULL),
(1209, 'Előtt', 'de', 'Előtt', NULL, NULL, NULL),
(1210, 'Előző', 'de', 'Előző', NULL, NULL, NULL),
(1211, 'Első', 'de', 'Első', NULL, NULL, NULL),
(1212, 'Email', 'de', 'Email', NULL, NULL, NULL),
(1213, 'Érték', 'de', 'Érték', NULL, NULL, NULL),
(1214, 'És', 'de', 'És', NULL, NULL, NULL),
(1215, 'ettől', 'de', 'ettől', NULL, NULL, NULL),
(1216, 'Exportál', 'de', 'Exportál', NULL, NULL, NULL),
(1217, 'febr', 'de', 'febr', NULL, NULL, NULL),
(1218, 'február', 'de', 'február', NULL, NULL, NULL),
(1219, 'Feldolgozás...', 'de', 'Feldolgozás...', NULL, NULL, NULL),
(1220, 'Felhasználói belépések', 'de', 'Felhasználói belépések', NULL, NULL, NULL),
(1221, 'felhasználók', 'de', 'felhasználók', NULL, NULL, NULL),
(1222, 'Felhasználók összesen', 'de', 'Felhasználók összesen', NULL, NULL, NULL),
(1223, 'Felhasználónként', 'de', 'Felhasználónként', NULL, NULL, NULL),
(1224, 'Felhasznált', 'de', 'Felhasznált', NULL, NULL, NULL),
(1225, 'Feltétel', 'de', 'Feltétel', NULL, NULL, NULL),
(1226, 'Feltétel hozzáadása', 'de', 'Feltétel hozzáadása', NULL, NULL, NULL),
(1227, 'Feltétel törlése', 'de', 'Feltétel törlése', NULL, NULL, NULL),
(1228, 'Fordítás', 'de', 'Fordítás', NULL, NULL, NULL),
(1229, 'forint', 'de', 'forint', NULL, NULL, NULL),
(1230, 'havi bontás', 'de', 'havi bontás', NULL, NULL, NULL),
(1231, 'hétfő', 'de', 'hétfő', NULL, NULL, NULL),
(1232, 'Hiba', 'de', 'Hiba', NULL, NULL, NULL),
(1233, 'Hitel keret', 'de', 'Hitel keret', NULL, NULL, NULL),
(1234, 'Id', 'de', 'Id', NULL, NULL, NULL),
(1235, 'Idei', 'de', 'Idei', NULL, NULL, NULL),
(1236, 'Idei kosár', 'de', 'Idei kosár', NULL, NULL, NULL),
(1237, 'Idei megrendelés', 'de', 'Idei megrendelés', NULL, NULL, NULL),
(1238, 'Idei megrendelések', 'de', 'Idei megrendelések', NULL, NULL, NULL),
(1239, 'Idei saját megrendelés', 'de', 'Idei saját megrendelés', NULL, NULL, NULL),
(1240, 'Idei saját megrendelések', 'de', 'Idei saját megrendelések', NULL, NULL, NULL),
(1241, 'Importál', 'de', 'Importál', NULL, NULL, NULL),
(1242, 'jan', 'de', 'jan', NULL, NULL, NULL),
(1243, 'január', 'de', 'január', NULL, NULL, NULL),
(1244, 'júl', 'de', 'júl', NULL, NULL, NULL),
(1245, 'július', 'de', 'július', NULL, NULL, NULL),
(1246, 'jún', 'de', 'jún', NULL, NULL, NULL),
(1247, 'június', 'de', 'június', NULL, NULL, NULL),
(1248, 'kedd', 'de', 'kedd', NULL, NULL, NULL),
(1249, 'Kedvenc', 'de', 'Kedvenc', NULL, NULL, NULL),
(1250, 'Kedvenc termék kiválasztás', 'de', 'Kedvenc termék kiválasztás', NULL, NULL, NULL),
(1251, 'Kedvenc termékek', 'de', 'Kedvenc termékek', NULL, NULL, NULL),
(1252, 'Kép', 'de', 'Kép', NULL, NULL, NULL),
(1253, 'Keresés konfigurátor', 'de', 'Keresés konfigurátor', NULL, NULL, NULL),
(1254, 'Keresés konfigurátor (%d)', 'de', 'Keresés konfigurátor (%d)', NULL, NULL, NULL),
(1255, 'Keresés:', 'de', 'Keresés:', NULL, NULL, NULL),
(1256, 'Kezdődik', 'de', 'Kezdődik', NULL, NULL, NULL),
(1257, 'Kikapcsol', 'de', 'Kikapcsol', NULL, NULL, NULL),
(1258, 'Kilép', 'de', 'Kilép', NULL, NULL, NULL),
(1259, 'Kilépés', 'de', 'Kilépés', NULL, NULL, NULL),
(1260, 'Kilépés a teljes képernyőből', 'de', 'Kilépés a teljes képernyőből', NULL, NULL, NULL),
(1261, 'Kissebb mint', 'de', 'Kissebb mint', NULL, NULL, NULL),
(1262, 'Kissebb vagy egyenlő mint', 'de', 'Kissebb vagy egyenlő mint', NULL, NULL, NULL),
(1263, 'Kívül eső', 'de', 'Kívül eső', NULL, NULL, NULL),
(1264, 'Kosár', 'de', 'Kosár', NULL, NULL, NULL),
(1265, 'Kosárba', 'de', 'Kosárba', NULL, NULL, NULL),
(1266, 'Következő', 'de', 'Következő', NULL, NULL, NULL),
(1267, 'Között', 'de', 'Között', NULL, NULL, NULL),
(1268, 'Letöltés CSV fileként', 'de', 'Letöltés CSV fileként', NULL, NULL, NULL),
(1269, 'Letöltés JPEG képként', 'de', 'Letöltés JPEG képként', NULL, NULL, NULL),
(1270, 'Letöltés PDF dokumentumként', 'de', 'Letöltés PDF dokumentumként', NULL, NULL, NULL),
(1271, 'Letöltés PNG képként', 'de', 'Letöltés PNG képként', NULL, NULL, NULL),
(1272, 'Letöltés SVG formátumban', 'de', 'Letöltés SVG formátumban', NULL, NULL, NULL),
(1273, 'Letöltés XLS fileként', 'de', 'Letöltés XLS fileként', NULL, NULL, NULL),
(1274, 'Létrehozás', 'de', 'Létrehozás', NULL, NULL, NULL),
(1275, 'Log adatok', 'de', 'Log adatok', NULL, NULL, NULL),
(1276, 'Magyar', 'de', 'Magyar', NULL, NULL, NULL),
(1277, 'Magyarul', 'de', 'Magyarul', NULL, NULL, NULL),
(1278, 'máj', 'de', 'máj', NULL, NULL, NULL),
(1279, 'május', 'de', 'május', NULL, NULL, NULL),
(1280, 'márc', 'de', 'márc', NULL, NULL, NULL),
(1281, 'március', 'de', 'március', NULL, NULL, NULL),
(1282, 'Másodperc', 'de', 'Másodperc', NULL, NULL, NULL),
(1283, 'Másolás', 'de', 'Másolás', NULL, NULL, NULL),
(1284, 'Me.egys', 'de', 'Me.egys', NULL, NULL, NULL),
(1285, 'Megrendelés átlag értékek az elmúlt 12 hónapban', 'de', 'Megrendelés átlag értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(1286, 'Megrendelés darab az elmúlt 12 hónapban', 'de', 'Megrendelés darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(1287, 'Megrendelés értékek az elmúlt 12 hónapban', 'de', 'Megrendelés értékek az elmúlt 12 hónapban', NULL, NULL, NULL),
(1288, 'Megrendelés kosárba másolás!', 'de', 'Megrendelés kosárba másolás!', NULL, NULL, NULL),
(1289, 'Megrendelés szám', 'de', 'Megrendelés szám', NULL, NULL, NULL),
(1290, 'Megrendelés tétel darab az elmúlt 12 hónapban', 'de', 'Megrendelés tétel darab az elmúlt 12 hónapban', NULL, NULL, NULL),
(1291, 'Megrendelések', 'de', 'Megrendelések', NULL, NULL, NULL),
(1292, 'Megszakítás', 'de', 'Megszakítás', NULL, NULL, NULL),
(1293, 'Mennyiség', 'de', 'Mennyiség', NULL, NULL, NULL),
(1294, 'Ment', 'de', 'Ment', NULL, NULL, NULL),
(1295, 'Minden termék', 'de', 'Minden termék', NULL, NULL, NULL),
(1296, 'Módosítás', 'de', 'Módosítás', NULL, NULL, NULL),
(1297, 'mutat:', 'de', 'mutat:', NULL, NULL, NULL),
(1298, 'Nagyobb mint', 'de', 'Nagyobb mint', NULL, NULL, NULL),
(1299, 'Nagyobb vagy egyenlő mint', 'de', 'Nagyobb vagy egyenlő mint', NULL, NULL, NULL),
(1300, 'Nem', 'de', 'Nem', NULL, NULL, NULL),
(1301, 'Nem jelölt ki sort', 'de', 'Nem jelölt ki sort', NULL, NULL, NULL),
(1302, 'Nem üres', 'de', 'Nem üres', NULL, NULL, NULL),
(1303, 'Nemzetiség', 'de', 'Nemzetiség', NULL, NULL, NULL),
(1304, 'Nettó', 'de', 'Nettó', NULL, NULL, NULL),
(1305, 'Név', 'de', 'Név', NULL, NULL, NULL),
(1306, 'Nincs a keresésnek megfelelő találat', 'de', 'Nincs a keresésnek megfelelő találat', NULL, NULL, NULL),
(1307, 'Nincs kijelölt tétel!', 'de', 'Nincs kijelölt tétel!', NULL, NULL, NULL),
(1308, 'Nincs rendelkezésre álló adat', 'de', 'Nincs rendelkezésre álló adat', NULL, NULL, NULL),
(1309, 'Nincsenek szűrőpanelek', 'de', 'Nincsenek szűrőpanelek', NULL, NULL, NULL),
(1310, 'nov', 'de', 'nov', NULL, NULL, NULL),
(1311, 'november', 'de', 'november', NULL, NULL, NULL),
(1312, 'Nulla találat', 'de', 'Nulla találat', NULL, NULL, NULL),
(1313, 'Nyelvek', 'de', 'Nyelvek', NULL, NULL, NULL),
(1314, 'Nyitott', 'de', 'Nyitott', NULL, NULL, NULL),
(1315, 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', 'de', 'Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \\/><br \\/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.', NULL, NULL, NULL),
(1316, 'Nyomtat', 'de', 'Nyomtat', NULL, NULL, NULL),
(1317, 'Nyomtatás', 'de', 'Nyomtatás', NULL, NULL, NULL),
(1318, 'okt', 'de', 'okt', NULL, NULL, NULL),
(1319, 'október', 'de', 'október', NULL, NULL, NULL),
(1320, 'Óra', 'de', 'Óra', NULL, NULL, NULL),
(1321, 'Összes', 'de', 'Összes', NULL, NULL, NULL),
(1322, 'Összes cella kitöltése a következővel: <i>%d<\\/i>', 'de', 'Összes cella kitöltése a következővel: <i>%d<\\/i>', NULL, NULL, NULL),
(1323, 'Összes feltétel törlése', 'de', 'Összes feltétel törlése', NULL, NULL, NULL),
(1324, 'Összes kosár', 'de', 'Összes kosár', NULL, NULL, NULL),
(1325, 'Összes megrendelés', 'de', 'Összes megrendelés', NULL, NULL, NULL),
(1326, 'Összes sor megjelenítése', 'de', 'Összes sor megjelenítése', NULL, NULL, NULL),
(1327, 'Oszlopok', 'de', 'Oszlopok', NULL, NULL, NULL),
(1328, 'Oszlopok visszaállítása', 'de', 'Oszlopok visszaállítása', NULL, NULL, NULL),
(1329, 'Partner cég', 'de', 'Partner cég', NULL, NULL, NULL),
(1330, 'Partner felhasználó', 'de', 'Partner felhasználó', NULL, NULL, NULL),
(1331, 'Partner felhasználók', 'de', 'Partner felhasználók', NULL, NULL, NULL),
(1332, 'péntek', 'de', 'péntek', NULL, NULL, NULL),
(1333, 'Pénznem', 'de', 'Pénznem', NULL, NULL, NULL),
(1334, 'Perc', 'de', 'Perc', NULL, NULL, NULL),
(1335, 'Product', 'de', 'Product', NULL, NULL, NULL),
(1336, 'Profil', 'de', 'Profil', NULL, NULL, NULL),
(1337, 'rendszergazdák', 'de', 'rendszergazdák', NULL, NULL, NULL),
(1338, 'Saját megrendelés', 'de', 'Saját megrendelés', NULL, NULL, NULL),
(1339, 'Szabad', 'de', 'Szabad', NULL, NULL, NULL),
(1340, 'Szállítási mód', 'de', 'Szállítási mód', NULL, NULL, NULL),
(1341, 'szept', 'de', 'szept', NULL, NULL, NULL),
(1342, 'szeptember', 'de', 'szeptember', NULL, NULL, NULL),
(1343, 'szerda', 'de', 'szerda', NULL, NULL, NULL),
(1344, 'szombat', 'de', 'szombat', NULL, NULL, NULL),
(1345, 'Szűrők törlése', 'de', 'Szűrők törlése', NULL, NULL, NULL),
(1346, 'Szűrőpanelek', 'de', 'Szűrőpanelek', NULL, NULL, NULL),
(1347, 'Szűrőpanelek (%d)', 'de', 'Szűrőpanelek (%d)', NULL, NULL, NULL),
(1348, 'Szűrőpanelek betöltése', 'de', 'Szűrőpanelek betöltése', NULL, NULL, NULL),
(1349, 'Táblázat', 'de', 'Táblázat', NULL, NULL, NULL),
(1350, 'Találatok: _START_ - _END_ Összesen: _TOTAL_', 'de', 'Találatok: _START_ - _END_ Összesen: _TOTAL_', NULL, NULL, NULL),
(1351, 'Tartalmazza', 'de', 'Tartalmazza', NULL, NULL, NULL),
(1352, 'Telephely', 'de', 'Telephely', NULL, NULL, NULL),
(1353, 'Teljes képernyő', 'de', 'Teljes képernyő', NULL, NULL, NULL),
(1354, 'Termék', 'de', 'Termék', NULL, NULL, NULL),
(1355, 'Termék kategória', 'de', 'Termék kategória', NULL, NULL, NULL),
(1356, 'Tétel', 'de', 'Tétel', NULL, NULL, NULL),
(1357, 'Tételek', 'de', 'Tételek', NULL, NULL, NULL),
(1358, 'Tétetek kosárba másolás!', 'de', 'Tétetek kosárba másolás!', NULL, NULL, NULL),
(1359, 'Törlés', 'de', 'Törlés', NULL, NULL, NULL),
(1360, 'Tovább', 'de', 'Tovább', NULL, NULL, NULL),
(1361, 'Új', 'de', 'Új', NULL, NULL, NULL),
(1362, 'Új Kosár', 'de', 'Új Kosár', NULL, NULL, NULL),
(1363, 'Üres', 'de', 'Üres', NULL, NULL, NULL),
(1364, 'Után', 'de', 'Után', NULL, NULL, NULL),
(1365, 'Utolsó', 'de', 'Utolsó', NULL, NULL, NULL),
(1366, 'Vágólapra másolás', 'de', 'Vágólapra másolás', NULL, NULL, NULL),
(1367, 'Vagy', 'de', 'Vagy', NULL, NULL, NULL),
(1368, 'Van már nyitott kosara!', 'de', 'Van már nyitott kosara!', NULL, NULL, NULL),
(1369, 'vasárnap', 'de', 'vasárnap', NULL, NULL, NULL),
(1370, 'Végződik', 'de', 'Végződik', NULL, NULL, NULL),
(1371, 'Vezérlő', 'de', 'Vezérlő', NULL, NULL, NULL),
(1372, 'Visszaállít', 'de', 'Visszaállít', NULL, NULL, NULL),
(1373, 'XML Import', 'de', 'XML Import', NULL, NULL, NULL),
(1374, '_MENU_ találat oldalanként', 'de', '_MENU_ találat oldalanként', NULL, NULL, NULL),
(1375, 'Fordított', 'hu', 'Fordított', NULL, NULL, NULL),
(1376, 'Fordított', 'en', 'Fordított', NULL, NULL, NULL),
(1377, 'Fordított', 'de', 'Fordított', NULL, NULL, NULL),
(1378, 'Fordított', 'bg', 'Fordított', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1379, 'Fordított', 'cz', 'Fordított', NULL, NULL, NULL),
(1380, 'Fordított', 'ee', 'Fordított', NULL, NULL, NULL),
(1381, 'Fordítatlan', 'hu', 'Fordítatlan', NULL, NULL, NULL),
(1382, 'Fordítatlan', 'en', 'Fordítatlan', NULL, NULL, NULL),
(1383, 'Fordítatlan', 'de', 'Fordítatlan', NULL, NULL, NULL),
(1384, 'Fordítatlan', 'bg', 'Fordítatlan', NULL, '2022-06-16 12:17:59', '2022-06-16 12:17:59'),
(1385, 'Fordítatlan', 'cz', 'Fordítatlan', NULL, NULL, NULL),
(1386, 'Fordítatlan', 'ee', 'Fordítatlan', NULL, NULL, NULL),
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
(1399, 'Felhasználó:', 'hu', 'Felhasználó:', NULL, NULL, NULL),
(1400, 'Felhasználó:', 'en', 'Felhasználó:', NULL, NULL, NULL),
(1401, 'Felhasználó:', 'de', 'Felhasználó:', NULL, NULL, NULL),
(1402, 'Felhasználó:', 'bg', 'Felhasználó:', NULL, NULL, NULL),
(1403, 'Felhasználó:', 'cz', 'Felhasználó:', NULL, NULL, NULL),
(1404, 'Felhasználó:', 'ee', 'Felhasználó:', NULL, NULL, NULL),
(1405, 'Időszak tól:', 'hu', 'Időszak tól:', NULL, NULL, NULL),
(1406, 'Időszak tól:', 'en', 'Időszak tól:', NULL, NULL, NULL),
(1407, 'Időszak tól:', 'de', 'Időszak tól:', NULL, NULL, NULL),
(1408, 'Időszak tól:', 'bg', 'Időszak tól:', NULL, NULL, NULL),
(1409, 'Időszak tól:', 'cz', 'Időszak tól:', NULL, NULL, NULL),
(1410, 'Időszak tól:', 'ee', 'Időszak tól:', NULL, NULL, NULL),
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
(1423, 'Felhasználó', 'hu', 'Felhasználó', NULL, NULL, NULL),
(1424, 'Felhasználó', 'en', 'Felhasználó', NULL, NULL, NULL),
(1425, 'Felhasználó', 'de', 'Felhasználó', NULL, NULL, NULL),
(1426, 'Felhasználó', 'bg', 'Felhasználó', NULL, NULL, NULL),
(1427, 'Felhasználó', 'cz', 'Felhasználó', NULL, NULL, NULL),
(1428, 'Felhasználó', 'ee', 'Felhasználó', NULL, NULL, NULL),
(1429, 'Esemény', 'hu', 'Esemény', NULL, NULL, NULL),
(1430, 'Esemény', 'en', 'Esemény', NULL, NULL, NULL),
(1431, 'Esemény', 'de', 'Esemény', NULL, NULL, NULL),
(1432, 'Esemény', 'bg', 'Esemény', NULL, NULL, NULL),
(1433, 'Esemény', 'cz', 'Esemény', NULL, NULL, NULL),
(1434, 'Esemény', 'ee', 'Esemény', NULL, NULL, NULL),
(1435, 'Log adatok elmúlt 24 óra', 'hu', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1436, 'Log adatok elmúlt 24 óra', 'en', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1437, 'Log adatok elmúlt 24 óra', 'de', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1438, 'Log adatok elmúlt 24 óra', 'bg', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1439, 'Log adatok elmúlt 24 óra', 'cz', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1440, 'Log adatok elmúlt 24 óra', 'ee', 'Log adatok elmúlt 24 óra', NULL, NULL, NULL),
(1441, 'Bizonylatszám:', 'hu', 'Bizonylatszám:', NULL, NULL, NULL),
(1442, 'Bizonylatszám:', 'en', 'Bizonylatszám:', NULL, NULL, NULL),
(1443, 'Bizonylatszám:', 'de', 'Bizonylatszám:', NULL, NULL, NULL),
(1444, 'Bizonylatszám:', 'bg', 'Bizonylatszám:', NULL, NULL, NULL),
(1445, 'Bizonylatszám:', 'cz', 'Bizonylatszám:', NULL, NULL, NULL),
(1446, 'Bizonylatszám:', 'ee', 'Bizonylatszám:', NULL, NULL, NULL),
(1447, 'Fizetési mód:', 'hu', 'Fizetési mód:', NULL, NULL, NULL),
(1448, 'Fizetési mód:', 'en', 'Fizetési mód:', NULL, NULL, NULL),
(1449, 'Fizetési mód:', 'de', 'Fizetési mód:', NULL, NULL, NULL),
(1450, 'Fizetési mód:', 'bg', 'Fizetési mód:', NULL, NULL, NULL),
(1451, 'Fizetési mód:', 'cz', 'Fizetési mód:', NULL, NULL, NULL),
(1452, 'Fizetési mód:', 'ee', 'Fizetési mód:', NULL, NULL, NULL),
(1453, 'Pénznem:', 'hu', 'Pénznem:', NULL, NULL, NULL),
(1454, 'Pénznem:', 'en', 'Pénznem:', NULL, NULL, NULL),
(1455, 'Pénznem:', 'de', 'Pénznem:', NULL, NULL, NULL),
(1456, 'Pénznem:', 'bg', 'Pénznem:', NULL, NULL, NULL),
(1457, 'Pénznem:', 'cz', 'Pénznem:', NULL, NULL, NULL),
(1458, 'Pénznem:', 'ee', 'Pénznem:', NULL, NULL, NULL),
(1459, 'Telephely:', 'hu', 'Telephely:', NULL, NULL, NULL),
(1460, 'Telephely:', 'en', 'Telephely:', NULL, NULL, NULL),
(1461, 'Telephely:', 'de', 'Telephely:', NULL, NULL, NULL),
(1462, 'Telephely:', 'bg', 'Telephely:', NULL, NULL, NULL),
(1463, 'Telephely:', 'cz', 'Telephely:', NULL, NULL, NULL),
(1464, 'Telephely:', 'ee', 'Telephely:', NULL, NULL, NULL),
(1465, 'Szállítási mód:', 'hu', 'Szállítási mód:', NULL, NULL, NULL),
(1466, 'Szállítási mód:', 'en', 'Szállítási mód:', NULL, NULL, NULL),
(1467, 'Szállítási mód:', 'de', 'Szállítási mód:', NULL, NULL, NULL),
(1468, 'Szállítási mód:', 'bg', 'Szállítási mód:', NULL, NULL, NULL),
(1469, 'Szállítási mód:', 'cz', 'Szállítási mód:', NULL, NULL, NULL),
(1470, 'Szállítási mód:', 'ee', 'Szállítási mód:', NULL, NULL, NULL),
(1471, 'Előleg:', 'hu', 'Előleg:', NULL, NULL, NULL),
(1472, 'Előleg:', 'en', 'Előleg:', NULL, NULL, NULL),
(1473, 'Előleg:', 'de', 'Előleg:', NULL, NULL, NULL),
(1474, 'Előleg:', 'bg', 'Előleg:', NULL, NULL, NULL),
(1475, 'Előleg:', 'cz', 'Előleg:', NULL, NULL, NULL),
(1476, 'Előleg:', 'ee', 'Előleg:', NULL, NULL, NULL),
(1477, 'Előleg %:', 'hu', 'Előleg %:', NULL, NULL, NULL),
(1478, 'Előleg %:', 'en', 'Előleg %:', NULL, NULL, NULL),
(1479, 'Előleg %:', 'de', 'Előleg %:', NULL, NULL, NULL),
(1480, 'Előleg %:', 'bg', 'Előleg %:', NULL, NULL, NULL),
(1481, 'Előleg %:', 'cz', 'Előleg %:', NULL, NULL, NULL),
(1482, 'Előleg %:', 'ee', 'Előleg %:', NULL, NULL, NULL),
(1483, 'Nettó érték:', 'hu', 'Nettó érték:', NULL, NULL, NULL),
(1484, 'Nettó érték:', 'en', 'Nettó érték:', NULL, NULL, NULL),
(1485, 'Nettó érték:', 'de', 'Nettó érték:', NULL, NULL, NULL),
(1486, 'Nettó érték:', 'bg', 'Nettó érték:', NULL, NULL, NULL),
(1487, 'Nettó érték:', 'cz', 'Nettó érték:', NULL, NULL, NULL),
(1488, 'Nettó érték:', 'ee', 'Nettó érték:', NULL, NULL, NULL),
(1489, 'Áfa:', 'hu', 'Áfa:', NULL, NULL, NULL),
(1490, 'Áfa:', 'en', 'Áfa:', NULL, NULL, NULL),
(1491, 'Áfa:', 'de', 'Áfa:', NULL, NULL, NULL),
(1492, 'Áfa:', 'bg', 'Áfa:', NULL, NULL, NULL),
(1493, 'Áfa:', 'cz', 'Áfa:', NULL, NULL, NULL),
(1494, 'Áfa:', 'ee', 'Áfa:', NULL, NULL, NULL),
(1495, 'Bruttó érték:', 'hu', 'Bruttó érték:', NULL, NULL, NULL),
(1496, 'Bruttó érték:', 'en', 'Bruttó érték:', NULL, NULL, NULL),
(1497, 'Bruttó érték:', 'de', 'Bruttó érték:', NULL, NULL, NULL),
(1498, 'Bruttó érték:', 'bg', 'Bruttó érték:', NULL, NULL, NULL),
(1499, 'Bruttó érték:', 'cz', 'Bruttó érték:', NULL, NULL, NULL),
(1500, 'Bruttó érték:', 'ee', 'Bruttó érték:', NULL, NULL, NULL),
(1501, 'Megjegyzés:', 'hu', 'Megjegyzés:', NULL, NULL, NULL),
(1502, 'Megjegyzés:', 'en', 'Megjegyzés:', NULL, NULL, NULL),
(1503, 'Megjegyzés:', 'de', 'Megjegyzés:', NULL, NULL, NULL);
INSERT INTO `translations` (`id`, `huname`, `language`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1504, 'Megjegyzés:', 'bg', 'Megjegyzés:', NULL, NULL, NULL),
(1505, 'Megjegyzés:', 'cz', 'Megjegyzés:', NULL, NULL, NULL),
(1506, 'Megjegyzés:', 'ee', 'Megjegyzés:', NULL, NULL, NULL),
(1507, 'B2B felhasználó', 'hu', 'B2B felhasználó', NULL, NULL, NULL),
(1508, 'B2B felhasználó', 'en', 'B2B felhasználó', NULL, NULL, NULL),
(1509, 'B2B felhasználó', 'de', 'B2B felhasználó', NULL, NULL, NULL),
(1510, 'B2B felhasználó', 'bg', 'B2B felhasználó', NULL, NULL, NULL),
(1511, 'B2B felhasználó', 'cz', 'B2B felhasználó', NULL, NULL, NULL),
(1512, 'B2B felhasználó', 'ee', 'B2B felhasználó', NULL, NULL, NULL),
(1513, 'Partner cég:', 'hu', 'Partner cég:', NULL, NULL, NULL),
(1514, 'Partner cég:', 'en', 'Partner cég:', NULL, NULL, NULL),
(1515, 'Partner cég:', 'de', 'Partner cég:', NULL, NULL, NULL),
(1516, 'Partner cég:', 'bg', 'Partner cég:', NULL, NULL, NULL),
(1517, 'Partner cég:', 'cz', 'Partner cég:', NULL, NULL, NULL),
(1518, 'Partner cég:', 'ee', 'Partner cég:', NULL, NULL, NULL),
(1519, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'hu', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1520, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'en', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1521, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'de', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1522, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'bg', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1523, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'cz', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1524, 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', 'ee', 'Kérem a Symbol Ügyviteli rendszerben rendeljen a felhasználóhoz email címet!', NULL, NULL, NULL),
(1525, 'Hibás név vagy jelszó!', 'hu', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1526, 'Hibás név vagy jelszó!', 'en', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1527, 'Hibás név vagy jelszó!', 'de', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1528, 'Hibás név vagy jelszó!', 'bg', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1529, 'Hibás név vagy jelszó!', 'cz', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1530, 'Hibás név vagy jelszó!', 'ee', 'Hibás név vagy jelszó!', NULL, NULL, NULL),
(1531, 'Bizonylatszám', 'hu', 'Bizonylatszám', NULL, NULL, NULL),
(1532, 'Bizonylatszám', 'en', 'Bizonylatszám', NULL, NULL, NULL),
(1533, 'Bizonylatszám', 'de', 'Bizonylatszám', NULL, NULL, NULL),
(1534, 'Bizonylatszám', 'bg', 'Bizonylatszám', NULL, NULL, NULL),
(1535, 'Bizonylatszám', 'cz', 'Bizonylatszám', NULL, NULL, NULL),
(1536, 'Bizonylatszám', 'ee', 'Bizonylatszám', NULL, NULL, NULL),
(1537, 'Kelt', 'hu', 'Kelt', NULL, NULL, NULL),
(1538, 'Kelt', 'en', 'Kelt', NULL, NULL, NULL),
(1539, 'Kelt', 'de', 'Kelt', NULL, NULL, NULL),
(1540, 'Kelt', 'bg', 'Kelt', NULL, NULL, NULL),
(1541, 'Kelt', 'cz', 'Kelt', NULL, NULL, NULL),
(1542, 'Kelt', 'ee', 'Kelt', NULL, NULL, NULL),
(1543, 'Száll.hat.', 'hu', 'Száll.hat.', NULL, NULL, NULL),
(1544, 'Száll.hat.', 'en', 'Száll.hat.', NULL, NULL, NULL),
(1545, 'Száll.hat.', 'de', 'Száll.hat.', NULL, NULL, NULL),
(1546, 'Száll.hat.', 'bg', 'Száll.hat.', NULL, NULL, NULL),
(1547, 'Száll.hat.', 'cz', 'Száll.hat.', NULL, NULL, NULL),
(1548, 'Száll.hat.', 'ee', 'Száll.hat.', NULL, NULL, NULL),
(1549, 'Fizetési mód', 'hu', 'Fizetési mód', NULL, NULL, NULL),
(1550, 'Fizetési mód', 'en', 'Fizetési mód', NULL, NULL, NULL),
(1551, 'Fizetési mód', 'de', 'Fizetési mód', NULL, NULL, NULL),
(1552, 'Fizetési mód', 'bg', 'Fizetési mód', NULL, NULL, NULL),
(1553, 'Fizetési mód', 'cz', 'Fizetési mód', NULL, NULL, NULL),
(1554, 'Fizetési mód', 'ee', 'Fizetési mód', NULL, NULL, NULL),
(1555, 'Rendelésszám', 'hu', 'Rendelésszám', NULL, NULL, NULL),
(1556, 'Rendelésszám', 'en', 'Rendelésszám', NULL, NULL, NULL),
(1557, 'Rendelésszám', 'de', 'Rendelésszám', NULL, NULL, NULL),
(1558, 'Rendelésszám', 'bg', 'Rendelésszám', NULL, NULL, NULL),
(1559, 'Rendelésszám', 'cz', 'Rendelésszám', NULL, NULL, NULL),
(1560, 'Rendelésszám', 'ee', 'Rendelésszám', NULL, NULL, NULL),
(1561, 'Akciós', 'hu', 'Akciós', NULL, NULL, NULL),
(1562, 'Akciós', 'en', 'Akciós', NULL, NULL, NULL),
(1563, 'Akciós', 'de', 'Akciós', NULL, NULL, NULL),
(1564, 'Akciós', 'bg', 'Akciós', NULL, NULL, NULL),
(1565, 'Akciós', 'cz', 'Akciós', NULL, NULL, NULL),
(1566, 'Akciós', 'ee', 'Akciós', NULL, NULL, NULL),
(1567, 'Szerződéses', 'hu', 'Szerződéses', NULL, NULL, NULL),
(1568, 'Szerződéses', 'en', 'Szerződéses', NULL, NULL, NULL),
(1569, 'Szerződéses', 'de', 'Szerződéses', NULL, NULL, NULL),
(1570, 'Szerződéses', 'bg', 'Szerződéses', NULL, NULL, NULL),
(1571, 'Szerződéses', 'cz', 'Szerződéses', NULL, NULL, NULL),
(1572, 'Szerződéses', 'ee', 'Szerződéses', NULL, NULL, NULL),
(1573, 'Kedvencek', 'hu', 'Kedvencek', NULL, NULL, NULL),
(1574, 'Kedvencek', 'en', 'Kedvencek', NULL, NULL, NULL),
(1575, 'Kedvencek', 'de', 'Kedvencek', NULL, NULL, NULL),
(1576, 'Kedvencek', 'bg', 'Kedvencek', NULL, NULL, NULL),
(1577, 'Kedvencek', 'cz', 'Kedvencek', NULL, NULL, NULL),
(1578, 'Kedvencek', 'ee', 'Kedvencek', NULL, NULL, NULL),
(1579, 'Minden tétel', 'hu', 'Minden tétel', NULL, NULL, NULL),
(1580, 'Minden tétel', 'en', 'Minden tétel', NULL, NULL, NULL),
(1581, 'Minden tétel', 'de', 'Minden tétel', NULL, NULL, NULL),
(1582, 'Minden tétel', 'bg', 'Minden tétel', NULL, NULL, NULL),
(1583, 'Minden tétel', 'cz', 'Minden tétel', NULL, NULL, NULL),
(1584, 'Minden tétel', 'ee', 'Minden tétel', NULL, NULL, NULL),
(1585, 'Kód', 'hu', 'Kód', NULL, NULL, NULL),
(1586, 'Kód', 'en', 'Kód', NULL, NULL, NULL),
(1587, 'Kód', 'de', 'Kód', NULL, NULL, NULL),
(1588, 'Kód', 'bg', 'Kód', NULL, NULL, NULL),
(1589, 'Kód', 'cz', 'Kód', NULL, NULL, NULL),
(1590, 'Kód', 'ee', 'Kód', NULL, NULL, NULL),
(1591, 'Termék csoport', 'hu', 'Termék csoport', NULL, NULL, NULL),
(1592, 'Termék csoport', 'en', 'Termék csoport', NULL, NULL, NULL),
(1593, 'Termék csoport', 'de', 'Termék csoport', NULL, NULL, NULL),
(1594, 'Termék csoport', 'bg', 'Termék csoport', NULL, NULL, NULL),
(1595, 'Termék csoport', 'cz', 'Termék csoport', NULL, NULL, NULL),
(1596, 'Termék csoport', 'ee', 'Termék csoport', NULL, NULL, NULL),
(1597, 'Vonalkód', 'hu', 'Vonalkód', NULL, NULL, NULL),
(1598, 'Vonalkód', 'en', 'Vonalkód', NULL, NULL, NULL),
(1599, 'Vonalkód', 'de', 'Vonalkód', NULL, NULL, NULL),
(1600, 'Vonalkód', 'bg', 'Vonalkód', NULL, NULL, NULL),
(1601, 'Vonalkód', 'cz', 'Vonalkód', NULL, NULL, NULL),
(1602, 'Vonalkód', 'ee', 'Vonalkód', NULL, NULL, NULL),
(1603, 'Szerződéses termékek', 'hu', 'Szerződéses termékek', NULL, NULL, NULL),
(1604, 'Szerződéses termékek', 'en', 'Szerződéses termékek', NULL, NULL, NULL),
(1605, 'Szerződéses termékek', 'de', 'Szerződéses termékek', NULL, NULL, NULL),
(1606, 'Szerződéses termékek', 'bg', 'Szerződéses termékek', NULL, NULL, NULL),
(1607, 'Szerződéses termékek', 'cz', 'Szerződéses termékek', NULL, NULL, NULL),
(1608, 'Szerződéses termékek', 'ee', 'Szerződéses termékek', NULL, NULL, NULL),
(1609, 'Akciós termékek', 'hu', 'Akciós termékek', NULL, NULL, NULL),
(1610, 'Akciós termékek', 'en', 'Akciós termékek', NULL, NULL, NULL),
(1611, 'Akciós termékek', 'de', 'Akciós termékek', NULL, NULL, NULL),
(1612, 'Akciós termékek', 'bg', 'Akciós termékek', NULL, NULL, NULL),
(1613, 'Akciós termékek', 'cz', 'Akciós termékek', NULL, NULL, NULL),
(1614, 'Akciós termékek', 'ee', 'Akciós termékek', NULL, NULL, NULL),
(1615, 'Ebben a kosárban már van ilyen termék!', 'hu', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1616, 'Ebben a kosárban már van ilyen termék!', 'en', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1617, 'Ebben a kosárban már van ilyen termék!', 'de', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1618, 'Ebben a kosárban már van ilyen termék!', 'bg', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1619, 'Ebben a kosárban már van ilyen termék!', 'cz', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1620, 'Ebben a kosárban már van ilyen termék!', 'ee', 'Ebben a kosárban már van ilyen termék!', NULL, NULL, NULL),
(1621, 'Biztosan hozzáadja ezt a mennyiséget?', 'hu', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1622, 'Biztosan hozzáadja ezt a mennyiséget?', 'en', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1623, 'Biztosan hozzáadja ezt a mennyiséget?', 'de', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1624, 'Biztosan hozzáadja ezt a mennyiséget?', 'bg', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1625, 'Biztosan hozzáadja ezt a mennyiséget?', 'cz', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1626, 'Biztosan hozzáadja ezt a mennyiséget?', 'ee', 'Biztosan hozzáadja ezt a mennyiséget?', NULL, NULL, NULL),
(1627, 'Kosár módosítás', 'hu', 'Kosár módosítás', NULL, NULL, NULL),
(1628, 'Kosár módosítás', 'en', 'Kosár módosítás', NULL, NULL, NULL),
(1629, 'Kosár módosítás', 'de', 'Kosár módosítás', NULL, NULL, NULL),
(1630, 'Kosár módosítás', 'bg', 'Kosár módosítás', NULL, NULL, NULL),
(1631, 'Kosár módosítás', 'cz', 'Kosár módosítás', NULL, NULL, NULL),
(1632, 'Kosár módosítás', 'ee', 'Kosár módosítás', NULL, NULL, NULL),
(1633, 'Mind', 'hu', 'Mind', NULL, NULL, NULL),
(1634, 'Mind', 'en', 'Mind', NULL, NULL, NULL),
(1635, 'Mind', 'de', 'Mind', NULL, NULL, NULL),
(1636, 'Mind', 'bg', 'Mind', NULL, NULL, NULL),
(1637, 'Mind', 'cz', 'Mind', NULL, NULL, NULL),
(1638, 'Mind', 'ee', 'Mind', NULL, NULL, NULL),
(1639, 'Minden céges', 'hu', 'Minden céges', NULL, NULL, NULL),
(1640, 'Minden céges', 'en', 'Minden céges', NULL, NULL, NULL),
(1641, 'Minden céges', 'de', 'Minden céges', NULL, NULL, NULL),
(1642, 'Minden céges', 'bg', 'Minden céges', NULL, NULL, NULL),
(1643, 'Minden céges', 'cz', 'Minden céges', NULL, NULL, NULL),
(1644, 'Minden céges', 'ee', 'Minden céges', NULL, NULL, NULL),
(1645, 'Idei céges', 'hu', 'Idei céges', NULL, NULL, NULL),
(1646, 'Idei céges', 'en', 'Idei céges', NULL, NULL, NULL),
(1647, 'Idei céges', 'de', 'Idei céges', NULL, NULL, NULL),
(1648, 'Idei céges', 'bg', 'Idei céges', NULL, NULL, NULL),
(1649, 'Idei céges', 'cz', 'Idei céges', NULL, NULL, NULL),
(1650, 'Idei céges', 'ee', 'Idei céges', NULL, NULL, NULL),
(1651, 'Saját', 'hu', 'Saját', NULL, NULL, NULL),
(1652, 'Saját', 'en', 'Saját', NULL, NULL, NULL),
(1653, 'Saját', 'de', 'Saját', NULL, NULL, NULL),
(1654, 'Saját', 'bg', 'Saját', NULL, NULL, NULL),
(1655, 'Saját', 'cz', 'Saját', NULL, NULL, NULL),
(1656, 'Saját', 'ee', 'Saját', NULL, NULL, NULL),
(1657, 'Idei saját', 'hu', 'Idei saját', NULL, NULL, NULL),
(1658, 'Idei saját', 'en', 'Idei saját', NULL, NULL, NULL),
(1659, 'Idei saját', 'de', 'Idei saját', NULL, NULL, NULL),
(1660, 'Idei saját', 'bg', 'Idei saját', NULL, NULL, NULL),
(1661, 'Idei saját', 'cz', 'Idei saját', NULL, NULL, NULL),
(1662, 'Idei saját', 'ee', 'Idei saját', NULL, NULL, NULL),
(1663, 'Cég név:', 'hu', 'Cég név:', NULL, NULL, NULL),
(1664, 'Cég név:', 'en', 'Cég név:', NULL, NULL, NULL),
(1665, 'Cég név:', 'de', 'Cég név:', NULL, NULL, NULL),
(1666, 'Cég név:', 'bg', 'Cég név:', NULL, NULL, NULL),
(1667, 'Cég név:', 'cz', 'Cég név:', NULL, NULL, NULL),
(1668, 'Cég név:', 'ee', 'Cég név:', NULL, NULL, NULL),
(1669, 'Lista ár', 'hu', 'Lista ár', NULL, NULL, NULL),
(1670, 'Lista ár', 'en', 'Lista ár', NULL, NULL, NULL),
(1671, 'Lista ár', 'de', 'Lista ár', NULL, NULL, NULL),
(1672, 'Lista ár', 'bg', 'Lista ár', NULL, NULL, NULL),
(1673, 'Lista ár', 'cz', 'Lista ár', NULL, NULL, NULL),
(1674, 'Lista ár', 'ee', 'Lista ár', NULL, NULL, NULL),
(1675, 'Kedvezményes ár', 'hu', 'Kedvezményes ár', NULL, NULL, NULL),
(1676, 'Kedvezményes ár', 'en', 'Kedvezményes ár', NULL, NULL, NULL),
(1677, 'Kedvezményes ár', 'de', 'Kedvezményes ár', NULL, NULL, NULL),
(1678, 'Kedvezményes ár', 'bg', 'Kedvezményes ár', NULL, NULL, NULL),
(1679, 'Kedvezményes ár', 'cz', 'Kedvezményes ár', NULL, NULL, NULL),
(1680, 'Kedvezményes ár', 'ee', 'Kedvezményes ár', NULL, NULL, NULL),
(1681, 'Kedvezmény', 'hu', 'Kedvezmény', NULL, NULL, NULL),
(1682, 'Kedvezmény', 'en', 'Kedvezmény', NULL, NULL, NULL),
(1683, 'Kedvezmény', 'de', 'Kedvezmény', NULL, NULL, NULL),
(1684, 'Kedvezmény', 'bg', 'Kedvezmény', NULL, NULL, NULL),
(1685, 'Kedvezmény', 'cz', 'Kedvezmény', NULL, NULL, NULL),
(1686, 'Kedvezmény', 'ee', 'Kedvezmény', NULL, NULL, NULL),
(1687, 'Kedv.%', 'hu', 'Kedv.%', NULL, NULL, NULL),
(1688, 'Kedv.%', 'en', 'Kedv.%', NULL, NULL, NULL),
(1689, 'Kedv.%', 'de', 'Kedv.%', NULL, NULL, NULL),
(1690, 'Kedv.%', 'bg', 'Kedv.%', NULL, NULL, NULL),
(1691, 'Kedv.%', 'cz', 'Kedv.%', NULL, NULL, NULL),
(1692, 'Kedv.%', 'ee', 'Kedv.%', NULL, NULL, NULL),
(1693, 'Kedv.ár', 'hu', 'Kedv.ár', NULL, NULL, NULL),
(1694, 'Kedv.ár', 'en', 'Kedv.ár', NULL, NULL, NULL),
(1695, 'Kedv.ár', 'de', 'Kedv.ár', NULL, NULL, NULL),
(1696, 'Kedv.ár', 'bg', 'Kedv.ár', NULL, NULL, NULL),
(1697, 'Kedv.ár', 'cz', 'Kedv.ár', NULL, NULL, NULL),
(1698, 'Kedv.ár', 'ee', 'Kedv.ár', NULL, NULL, NULL),
(1699, 'Vezélő pult', 'hu', 'Vezélő pult', NULL, NULL, NULL),
(1700, 'Vezélő pult', 'en', 'Vezélő pult', NULL, NULL, NULL),
(1701, 'Vezélő pult', 'de', 'Vezélő pult', NULL, NULL, NULL),
(1702, 'Vezélő pult', 'bg', 'Vezélő pult', NULL, NULL, NULL),
(1703, 'Vezélő pult', 'cz', 'Vezélő pult', NULL, NULL, NULL),
(1704, 'Vezélő pult', 'ee', 'Vezélő pult', NULL, NULL, NULL),
(1705, 'Megys', 'hu', 'Megys', NULL, NULL, NULL),
(1706, 'Megys', 'en', 'Megys', NULL, NULL, NULL),
(1707, 'Megys', 'de', 'Megys', NULL, NULL, NULL),
(1708, 'Megys', 'bg', 'Megys', NULL, NULL, NULL),
(1709, 'Megys', 'cz', 'Megys', NULL, NULL, NULL),
(1710, 'Megys', 'ee', 'Megys', NULL, NULL, NULL),
(1711, 'Ár', 'hu', 'Ár', NULL, NULL, NULL),
(1712, 'Ár', 'en', 'Ár', NULL, NULL, NULL),
(1713, 'Ár', 'de', 'Ár', NULL, NULL, NULL),
(1714, 'Ár', 'bg', 'Ár', NULL, NULL, NULL),
(1715, 'Ár', 'cz', 'Ár', NULL, NULL, NULL),
(1716, 'Ár', 'ee', 'Ár', NULL, NULL, NULL),
(1717, 'Árfolyam', 'hu', 'Árfolyam', NULL, NULL, NULL),
(1718, 'Árfolyam', 'en', 'Árfolyam', NULL, NULL, NULL),
(1719, 'Árfolyam', 'de', 'Árfolyam', NULL, NULL, NULL),
(1720, 'Árfolyam', 'bg', 'Árfolyam', NULL, NULL, NULL),
(1721, 'Árfolyam', 'cz', 'Árfolyam', NULL, NULL, NULL),
(1722, 'Árfolyam', 'ee', 'Árfolyam', NULL, NULL, NULL),
(1723, 'Partner ár', 'hu', 'Partner ár', NULL, NULL, NULL),
(1724, 'Partner ár', 'en', 'Partner ár', NULL, NULL, NULL),
(1725, 'Partner ár', 'de', 'Partner ár', NULL, NULL, NULL),
(1726, 'Partner ár', 'bg', 'Partner ár', NULL, NULL, NULL),
(1727, 'Partner ár', 'cz', 'Partner ár', NULL, NULL, NULL),
(1728, 'Partner ár', 'ee', 'Partner ár', NULL, NULL, NULL),
(1729, 'Email beállítások', 'hu', 'Email beállítások', NULL, NULL, NULL),
(1730, 'Email beállítások', 'en', 'Email beállítások', NULL, NULL, NULL),
(1731, 'Email beállítások', 'de', 'Email beállítások', NULL, NULL, NULL),
(1732, 'Email beállítások', 'bg', 'Email beállítások', NULL, NULL, NULL),
(1733, 'Email beállítások', 'cz', 'Email beállítások', NULL, NULL, NULL),
(1734, 'Email beállítások', 'ee', 'Email beállítások', NULL, NULL, NULL),
(1735, 'Kommunikáció', 'hu', 'Kommunikáció', NULL, NULL, NULL),
(1736, 'Kommunikáció', 'en', 'Kommunikáció', NULL, NULL, NULL),
(1737, 'Kommunikáció', 'de', 'Kommunikáció', NULL, NULL, NULL),
(1738, 'Kommunikáció', 'bg', 'Kommunikáció', NULL, NULL, NULL),
(1739, 'Kommunikáció', 'cz', 'Kommunikáció', NULL, NULL, NULL),
(1740, 'Kommunikáció', 'ee', 'Kommunikáció', NULL, NULL, NULL),
(1741, 'Kommunikáció beállítások', 'hu', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1742, 'Kommunikáció beállítások', 'en', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1743, 'Kommunikáció beállítások', 'de', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1744, 'Kommunikáció beállítások', 'bg', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1745, 'Kommunikáció beállítások', 'cz', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1746, 'Kommunikáció beállítások', 'ee', 'Kommunikáció beállítások', NULL, NULL, NULL),
(1747, 'Cég:', 'hu', 'Cég:', NULL, NULL, NULL),
(1748, 'Cég:', 'en', 'Cég:', NULL, NULL, NULL),
(1749, 'Cég:', 'de', 'Cég:', NULL, NULL, NULL),
(1750, 'Cég:', 'bg', 'Cég:', NULL, NULL, NULL),
(1751, 'Cég:', 'cz', 'Cég:', NULL, NULL, NULL),
(1752, 'Cég:', 'ee', 'Cég:', NULL, NULL, NULL),
(1753, 'Státusz', 'hu', 'Státusz', NULL, NULL, NULL),
(1754, 'Státusz', 'en', 'Státusz', NULL, NULL, NULL),
(1755, 'Státusz', 'de', 'Státusz', NULL, NULL, NULL),
(1756, 'Státusz', 'bg', 'Státusz', NULL, NULL, NULL),
(1757, 'Státusz', 'cz', 'Státusz', NULL, NULL, NULL),
(1758, 'Státusz', 'ee', 'Státusz', NULL, NULL, NULL),
(1759, 'Válasszon', 'hu', 'Válasszon', NULL, NULL, NULL),
(1760, 'Válasszon', 'en', 'Válasszon', NULL, NULL, NULL),
(1761, 'Válasszon', 'de', 'Válasszon', NULL, NULL, NULL),
(1762, 'Válasszon', 'bg', 'Válasszon', NULL, NULL, NULL),
(1763, 'Válasszon', 'cz', 'Válasszon', NULL, NULL, NULL),
(1764, 'Válasszon', 'ee', 'Válasszon', NULL, NULL, NULL),
(1765, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'hu', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1766, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'en', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1767, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'de', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1768, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'bg', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1769, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'cz', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1770, 'Kérem rakja az oszlopokat a következő sorrrendbe', 'ee', 'Kérem rakja az oszlopokat a következő sorrrendbe', NULL, NULL, NULL),
(1771, 'Feldolgozott', 'hu', 'Feldolgozott', NULL, NULL, NULL),
(1772, 'Feldolgozott', 'en', 'Feldolgozott', NULL, NULL, NULL),
(1773, 'Feldolgozott', 'de', 'Feldolgozott', NULL, NULL, NULL),
(1774, 'Feldolgozott', 'bg', 'Feldolgozott', NULL, NULL, NULL),
(1775, 'Feldolgozott', 'cz', 'Feldolgozott', NULL, NULL, NULL),
(1776, 'Feldolgozott', 'ee', 'Feldolgozott', NULL, NULL, NULL),
(1777, 'Minden termékek', 'hu', 'Minden termékek', NULL, NULL, NULL),
(1778, 'Minden termékek', 'en', 'Minden termékek', NULL, NULL, NULL),
(1779, 'Minden termékek', 'de', 'Minden termékek', NULL, NULL, NULL),
(1780, 'Minden termékek', 'bg', 'Minden termékek', NULL, NULL, NULL),
(1781, 'Minden termékek', 'cz', 'Minden termékek', NULL, NULL, NULL),
(1782, 'Minden termékek', 'ee', 'Minden termékek', NULL, NULL, NULL),
(1783, 'Termék:', 'hu', 'Termék:', NULL, NULL, NULL),
(1784, 'Termék:', 'en', 'Termék:', NULL, NULL, NULL),
(1785, 'Termék:', 'de', 'Termék:', NULL, NULL, NULL),
(1786, 'Termék:', 'bg', 'Termék:', NULL, NULL, NULL),
(1787, 'Termék:', 'cz', 'Termék:', NULL, NULL, NULL),
(1788, 'Termék:', 'ee', 'Termék:', NULL, NULL, NULL),
(1789, 'Termékcsoport:', 'hu', 'Termékcsoport:', NULL, NULL, NULL),
(1790, 'Termékcsoport:', 'en', 'Termékcsoport:', NULL, NULL, NULL),
(1791, 'Termékcsoport:', 'de', 'Termékcsoport:', NULL, NULL, NULL),
(1792, 'Termékcsoport:', 'bg', 'Termékcsoport:', NULL, NULL, NULL),
(1793, 'Termékcsoport:', 'cz', 'Termékcsoport:', NULL, NULL, NULL),
(1794, 'Termékcsoport:', 'ee', 'Termékcsoport:', NULL, NULL, NULL),
(1795, 'Státusz:', 'hu', 'Státusz:', NULL, NULL, NULL),
(1796, 'Státusz:', 'en', 'Státusz:', NULL, NULL, NULL),
(1797, 'Státusz:', 'de', 'Státusz:', NULL, NULL, NULL),
(1798, 'Státusz:', 'bg', 'Státusz:', NULL, NULL, NULL),
(1799, 'Státusz:', 'cz', 'Státusz:', NULL, NULL, NULL),
(1800, 'Státusz:', 'ee', 'Státusz:', NULL, NULL, NULL),
(1801, 'Biztos törli a tételt?', 'hu', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1802, 'Biztos törli a tételt?', 'en', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1803, 'Biztos törli a tételt?', 'de', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1804, 'Biztos törli a tételt?', 'bg', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1805, 'Biztos törli a tételt?', 'cz', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1806, 'Biztos törli a tételt?', 'ee', 'Biztos törli a tételt?', NULL, NULL, NULL),
(1807, 'Ez a bejegyzés véglegesen törlődik!', 'hu', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1808, 'Ez a bejegyzés véglegesen törlődik!', 'en', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1809, 'Ez a bejegyzés véglegesen törlődik!', 'de', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1810, 'Ez a bejegyzés véglegesen törlődik!', 'bg', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1811, 'Ez a bejegyzés véglegesen törlődik!', 'cz', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1812, 'Ez a bejegyzés véglegesen törlődik!', 'ee', 'Ez a bejegyzés véglegesen törlődik!', NULL, NULL, NULL),
(1813, 'Vissza', 'hu', 'Vissza', NULL, NULL, NULL),
(1814, 'Vissza', 'en', 'Vissza', NULL, NULL, NULL),
(1815, 'Vissza', 'de', 'Vissza', NULL, NULL, NULL),
(1816, 'Vissza', 'bg', 'Vissza', NULL, NULL, NULL),
(1817, 'Vissza', 'cz', 'Vissza', NULL, NULL, NULL),
(1818, 'Vissza', 'ee', 'Vissza', NULL, NULL, NULL),
(1819, 'File', 'hu', 'File', NULL, NULL, NULL),
(1820, 'File', 'en', 'File', NULL, NULL, NULL),
(1821, 'File', 'de', 'File', NULL, NULL, NULL),
(1822, 'File', 'bg', 'File', NULL, NULL, NULL),
(1823, 'File', 'cz', 'File', NULL, NULL, NULL),
(1824, 'File', 'ee', 'File', NULL, NULL, NULL),
(1825, 'Tábla', 'hu', 'Tábla', NULL, NULL, NULL),
(1826, 'Tábla', 'en', 'Tábla', NULL, NULL, NULL),
(1827, 'Tábla', 'de', 'Tábla', NULL, NULL, NULL),
(1828, 'Tábla', 'bg', 'Tábla', NULL, NULL, NULL),
(1829, 'Tábla', 'cz', 'Tábla', NULL, NULL, NULL),
(1830, 'Tábla', 'ee', 'Tábla', NULL, NULL, NULL),
(1831, 'Rekord', 'hu', 'Rekord', NULL, NULL, NULL),
(1832, 'Rekord', 'en', 'Rekord', NULL, NULL, NULL),
(1833, 'Rekord', 'de', 'Rekord', NULL, NULL, NULL),
(1834, 'Rekord', 'bg', 'Rekord', NULL, NULL, NULL),
(1835, 'Rekord', 'cz', 'Rekord', NULL, NULL, NULL),
(1836, 'Rekord', 'ee', 'Rekord', NULL, NULL, NULL),
(1837, 'Insert', 'hu', 'Insert', NULL, NULL, NULL),
(1838, 'Insert', 'en', 'Insert', NULL, NULL, NULL),
(1839, 'Insert', 'de', 'Insert', NULL, NULL, NULL),
(1840, 'Insert', 'bg', 'Insert', NULL, NULL, NULL),
(1841, 'Insert', 'cz', 'Insert', NULL, NULL, NULL),
(1842, 'Insert', 'ee', 'Insert', NULL, NULL, NULL),
(1843, 'Update', 'hu', 'Update', NULL, NULL, NULL),
(1844, 'Update', 'en', 'Update', NULL, NULL, NULL),
(1845, 'Update', 'de', 'Update', NULL, NULL, NULL),
(1846, 'Update', 'bg', 'Update', NULL, NULL, NULL),
(1847, 'Update', 'cz', 'Update', NULL, NULL, NULL),
(1848, 'Update', 'ee', 'Update', NULL, NULL, NULL),
(1849, 'Error', 'hu', 'Error', NULL, NULL, NULL),
(1850, 'Error', 'en', 'Error', NULL, NULL, NULL),
(1851, 'Error', 'de', 'Error', NULL, NULL, NULL),
(1852, 'Error', 'bg', 'Error', NULL, NULL, NULL),
(1853, 'Error', 'cz', 'Error', NULL, NULL, NULL),
(1854, 'Error', 'ee', 'Error', NULL, NULL, NULL),
(1855, 'SÜ Adatok', 'hu', 'SÜ Adatok', NULL, NULL, NULL),
(1856, 'SÜ Adatok', 'en', 'SÜ Adatok', NULL, NULL, NULL),
(1857, 'SÜ Adatok', 'de', 'SÜ Adatok', NULL, NULL, NULL),
(1858, 'SÜ Adatok', 'bg', 'SÜ Adatok', NULL, NULL, NULL),
(1859, 'SÜ Adatok', 'cz', 'SÜ Adatok', NULL, NULL, NULL),
(1860, 'SÜ Adatok', 'ee', 'SÜ Adatok', NULL, NULL, NULL),
(1861, 'Kosár törlés', 'hu', 'Kosár törlés', NULL, NULL, NULL),
(1862, 'Kosár törlés', 'en', 'Kosár törlés', NULL, NULL, NULL),
(1863, 'Kosár törlés', 'de', 'Kosár törlés', NULL, NULL, NULL),
(1864, 'Kosár törlés', 'bg', 'Kosár törlés', NULL, NULL, NULL),
(1865, 'Kosár törlés', 'cz', 'Kosár törlés', NULL, NULL, NULL),
(1866, 'Kosár törlés', 'ee', 'Kosár törlés', NULL, NULL, NULL),
(1867, 'A tétel nem törölhető', 'hu', 'A tétel nem törölhető', NULL, NULL, NULL),
(1868, 'A tétel nem törölhető', 'en', 'A tétel nem törölhető', NULL, NULL, NULL),
(1869, 'A tétel nem törölhető', 'de', 'A tétel nem törölhető', NULL, NULL, NULL),
(1870, 'A tétel nem törölhető', 'bg', 'A tétel nem törölhető', NULL, NULL, NULL),
(1871, 'A tétel nem törölhető', 'cz', 'A tétel nem törölhető', NULL, NULL, NULL),
(1872, 'A tétel nem törölhető', 'ee', 'A tétel nem törölhető', NULL, NULL, NULL),
(1873, 'Töröl', 'hu', 'Töröl', NULL, NULL, NULL),
(1874, 'Töröl', 'en', 'Töröl', NULL, NULL, NULL),
(1875, 'Töröl', 'de', 'Töröl', NULL, NULL, NULL),
(1876, 'Töröl', 'bg', 'Töröl', NULL, NULL, NULL),
(1877, 'Töröl', 'cz', 'Töröl', NULL, NULL, NULL),
(1878, 'Töröl', 'ee', 'Töröl', NULL, NULL, NULL),
(1879, 'Tétel: ', 'hu', 'Tétel: ', NULL, NULL, NULL),
(1880, 'Tétel: ', 'en', 'Tétel: ', NULL, NULL, NULL),
(1881, 'Tétel: ', 'de', 'Tétel: ', NULL, NULL, NULL),
(1882, 'Tétel: ', 'bg', 'Tétel: ', NULL, NULL, NULL),
(1883, 'Tétel: ', 'cz', 'Tétel: ', NULL, NULL, NULL),
(1884, 'Tétel: ', 'ee', 'Tétel: ', NULL, NULL, NULL),
(1885, 'Kosár SÜ-be', 'hu', 'Kosár SÜ-be', NULL, NULL, NULL),
(1886, 'Kosár SÜ-be', 'en', 'Kosár SÜ-be', NULL, NULL, NULL),
(1887, 'Kosár SÜ-be', 'de', 'Kosár SÜ-be', NULL, NULL, NULL),
(1888, 'Kosár SÜ-be', 'bg', 'Kosár SÜ-be', NULL, NULL, NULL),
(1889, 'Kosár SÜ-be', 'cz', 'Kosár SÜ-be', NULL, NULL, NULL),
(1890, 'Kosár SÜ-be', 'ee', 'Kosár SÜ-be', NULL, NULL, NULL),
(1891, 'Kosár adatok SÜ ERP-be!', 'hu', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1892, 'Kosár adatok SÜ ERP-be!', 'en', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1893, 'Kosár adatok SÜ ERP-be!', 'de', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1894, 'Kosár adatok SÜ ERP-be!', 'bg', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1895, 'Kosár adatok SÜ ERP-be!', 'cz', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1896, 'Kosár adatok SÜ ERP-be!', 'ee', 'Kosár adatok SÜ ERP-be!', NULL, NULL, NULL),
(1897, 'Biztosan átadja a tételeket?', 'hu', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1898, 'Biztosan átadja a tételeket?', 'en', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1899, 'Biztosan átadja a tételeket?', 'de', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1900, 'Biztosan átadja a tételeket?', 'bg', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1901, 'Biztosan átadja a tételeket?', 'cz', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1902, 'Biztosan átadja a tételeket?', 'ee', 'Biztosan átadja a tételeket?', NULL, NULL, NULL),
(1903, 'SÜ ERP-be', 'hu', 'SÜ ERP-be', NULL, NULL, NULL),
(1904, 'SÜ ERP-be', 'en', 'SÜ ERP-be', NULL, NULL, NULL),
(1905, 'SÜ ERP-be', 'de', 'SÜ ERP-be', NULL, NULL, NULL),
(1906, 'SÜ ERP-be', 'bg', 'SÜ ERP-be', NULL, NULL, NULL),
(1907, 'SÜ ERP-be', 'cz', 'SÜ ERP-be', NULL, NULL, NULL),
(1908, 'SÜ ERP-be', 'ee', 'SÜ ERP-be', NULL, NULL, NULL),
(1909, 'Üdvözlettel', 'hu', 'Üdvözlettel', NULL, NULL, NULL),
(1910, 'Üdvözlettel', 'en', 'Üdvözlettel', NULL, NULL, NULL),
(1911, 'Üdvözlettel', 'de', 'Üdvözlettel', NULL, NULL, NULL),
(1912, 'Üdvözlettel', 'bg', 'Üdvözlettel', NULL, NULL, NULL),
(1913, 'Üdvözlettel', 'cz', 'Üdvözlettel', NULL, NULL, NULL),
(1914, 'Üdvözlettel', 'ee', 'Üdvözlettel', NULL, NULL, NULL),
(1915, 'Üresen hagyta a jelszó mezőt!', 'hu', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1916, 'Üresen hagyta a jelszó mezőt!', 'en', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1917, 'Üresen hagyta a jelszó mezőt!', 'de', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1918, 'Üresen hagyta a jelszó mezőt!', 'bg', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1919, 'Üresen hagyta a jelszó mezőt!', 'cz', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1920, 'Üresen hagyta a jelszó mezőt!', 'ee', 'Üresen hagyta a jelszó mezőt!', NULL, NULL, NULL),
(1921, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'hu', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1922, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'en', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1923, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'de', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1924, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'bg', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1925, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'cz', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1926, 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', 'ee', 'Jelszónak minimum 8 karakter hosszúnak kell lennie!', NULL, NULL, NULL),
(1927, 'Üresen hagyta a jelszó újra mezőt!', 'hu', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1928, 'Üresen hagyta a jelszó újra mezőt!', 'en', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1929, 'Üresen hagyta a jelszó újra mezőt!', 'de', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1930, 'Üresen hagyta a jelszó újra mezőt!', 'bg', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1931, 'Üresen hagyta a jelszó újra mezőt!', 'cz', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1932, 'Üresen hagyta a jelszó újra mezőt!', 'ee', 'Üresen hagyta a jelszó újra mezőt!', NULL, NULL, NULL),
(1933, 'Nem egyezik a két jelszó!', 'hu', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1934, 'Nem egyezik a két jelszó!', 'en', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1935, 'Nem egyezik a két jelszó!', 'de', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1936, 'Nem egyezik a két jelszó!', 'bg', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1937, 'Nem egyezik a két jelszó!', 'cz', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1938, 'Nem egyezik a két jelszó!', 'ee', 'Nem egyezik a két jelszó!', NULL, NULL, NULL),
(1939, 'A név kötelező!', 'hu', 'A név kötelező!', NULL, NULL, NULL),
(1940, 'A név kötelező!', 'en', 'A név kötelező!', NULL, NULL, NULL),
(1941, 'A név kötelező!', 'de', 'A név kötelező!', NULL, NULL, NULL),
(1942, 'A név kötelező!', 'bg', 'A név kötelező!', NULL, NULL, NULL),
(1943, 'A név kötelező!', 'cz', 'A név kötelező!', NULL, NULL, NULL),
(1944, 'A név kötelező!', 'ee', 'A név kötelező!', NULL, NULL, NULL),
(1945, 'Nem adott meg státuszt!', 'hu', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1946, 'Nem adott meg státuszt!', 'en', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1947, 'Nem adott meg státuszt!', 'de', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1948, 'Nem adott meg státuszt!', 'bg', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1949, 'Nem adott meg státuszt!', 'cz', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1950, 'Nem adott meg státuszt!', 'ee', 'Nem adott meg státuszt!', NULL, NULL, NULL),
(1951, 'Belső felhasználó hozzáadás', 'hu', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1952, 'Belső felhasználó hozzáadás', 'en', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1953, 'Belső felhasználó hozzáadás', 'de', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1954, 'Belső felhasználó hozzáadás', 'bg', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1955, 'Belső felhasználó hozzáadás', 'cz', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1956, 'Belső felhasználó hozzáadás', 'ee', 'Belső felhasználó hozzáadás', NULL, NULL, NULL),
(1957, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'hu', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1958, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'en', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1959, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'de', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1960, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'bg', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1961, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'cz', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1962, 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', 'ee', 'Ennek a felhasználónak már van hozzáférése a rendszerhez!', NULL, NULL, NULL),
(1963, 'Nem adott meg felhasználót!', 'hu', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1964, 'Nem adott meg felhasználót!', 'en', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1965, 'Nem adott meg felhasználót!', 'de', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1966, 'Nem adott meg felhasználót!', 'bg', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1967, 'Nem adott meg felhasználót!', 'cz', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1968, 'Nem adott meg felhasználót!', 'ee', 'Nem adott meg felhasználót!', NULL, NULL, NULL),
(1969, 'Nem adott meg email címet!', 'hu', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1970, 'Nem adott meg email címet!', 'en', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1971, 'Nem adott meg email címet!', 'de', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1972, 'Nem adott meg email címet!', 'bg', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1973, 'Nem adott meg email címet!', 'cz', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1974, 'Nem adott meg email címet!', 'ee', 'Nem adott meg email címet!', NULL, NULL, NULL),
(1975, 'Belépés', 'hu', 'Belépés', NULL, NULL, NULL),
(1976, 'Belépés', 'en', 'Belépés', NULL, NULL, NULL),
(1977, 'Belépés', 'de', 'Belépés', NULL, NULL, NULL),
(1978, 'Belépés', 'bg', 'Belépés', NULL, NULL, NULL),
(1979, 'Belépés', 'cz', 'Belépés', NULL, NULL, NULL),
(1980, 'Belépés', 'ee', 'Belépés', NULL, NULL, NULL),
(1981, 'Nincs a tételhez kapcsolódó tábla', 'hu', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1982, 'Nincs a tételhez kapcsolódó tábla', 'en', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1983, 'Nincs a tételhez kapcsolódó tábla', 'de', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1984, 'Nincs a tételhez kapcsolódó tábla', 'bg', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1985, 'Nincs a tételhez kapcsolódó tábla', 'cz', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1986, 'Nincs a tételhez kapcsolódó tábla', 'ee', 'Nincs a tételhez kapcsolódó tábla', NULL, NULL, NULL),
(1987, 'Mező', 'hu', 'Mező', NULL, NULL, NULL),
(1988, 'Mező', 'en', 'Mező', NULL, NULL, NULL),
(1989, 'Mező', 'de', 'Mező', NULL, NULL, NULL),
(1990, 'Mező', 'bg', 'Mező', NULL, NULL, NULL),
(1991, 'Mező', 'cz', 'Mező', NULL, NULL, NULL),
(1992, 'Mező', 'ee', 'Mező', NULL, NULL, NULL),
(1993, 'Régi', 'hu', 'Régi', NULL, NULL, NULL),
(1994, 'Régi', 'en', 'Régi', NULL, NULL, NULL),
(1995, 'Régi', 'de', 'Régi', NULL, NULL, NULL),
(1996, 'Régi', 'bg', 'Régi', NULL, NULL, NULL),
(1997, 'Régi', 'cz', 'Régi', NULL, NULL, NULL),
(1998, 'Régi', 'ee', 'Régi', NULL, NULL, NULL),
(1999, 'A jelszó kötelező!', 'hu', 'A jelszó kötelező!', NULL, NULL, NULL),
(2000, 'A jelszó kötelező!', 'en', 'A jelszó kötelező!', NULL, NULL, NULL),
(2001, 'A jelszó kötelező!', 'de', 'A jelszó kötelező!', NULL, NULL, NULL),
(2002, 'A jelszó kötelező!', 'bg', 'A jelszó kötelező!', NULL, NULL, NULL),
(2003, 'A jelszó kötelező!', 'cz', 'A jelszó kötelező!', NULL, NULL, NULL),
(2004, 'A jelszó kötelező!', 'ee', 'A jelszó kötelező!', NULL, NULL, NULL),
(2005, 'Belső felhasználó hozzáadás asasasa', 'hu', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2006, 'Belső felhasználó hozzáadás asasasa', 'en', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2007, 'Belső felhasználó hozzáadás asasasa', 'de', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2008, 'Belső felhasználó hozzáadás asasasa', 'bg', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2009, 'Belső felhasználó hozzáadás asasasa', 'cz', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2010, 'Belső felhasználó hozzáadás asasasa', 'ee', 'Belső felhasználó hozzáadás asasasa', NULL, NULL, NULL),
(2011, 'Kezdeti adatbetöltés', 'hu', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2012, 'Kezdeti adatbetöltés', 'en', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2013, 'Kezdeti adatbetöltés', 'de', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2014, 'Kezdeti adatbetöltés', 'bg', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2015, 'Kezdeti adatbetöltés', 'cz', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2016, 'Kezdeti adatbetöltés', 'ee', 'Kezdeti adatbetöltés', NULL, NULL, NULL),
(2017, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'hu', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2018, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'en', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2019, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'de', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2020, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'bg', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2021, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'cz', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2022, 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', 'ee', 'Kérem exportálja ki az adatokat a Symbol Tech Ügyviteli rendszeréből!', NULL, NULL, NULL),
(2023, 'Azután a fenti gombbal importálja!', 'hu', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2024, 'Azután a fenti gombbal importálja!', 'en', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2025, 'Azután a fenti gombbal importálja!', 'de', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2026, 'Azután a fenti gombbal importálja!', 'bg', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2027, 'Azután a fenti gombbal importálja!', 'cz', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2028, 'Azután a fenti gombbal importálja!', 'ee', 'Azután a fenti gombbal importálja!', NULL, NULL, NULL),
(2029, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'hu', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL),
(2030, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'en', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL),
(2031, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'de', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL),
(2032, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'bg', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL),
(2033, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'cz', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL),
(2034, 'Ha sikerült, a fenti gombbal importálja az adatokat!', 'ee', 'Ha sikerült, a fenti gombbal importálja az adatokat!', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `transportmode`
--

DROP TABLE IF EXISTS `transportmode`;
CREATE TABLE IF NOT EXISTS `transportmode` (
  `Id` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `DiscountPercent` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `VoucherComment` varchar(100) DEFAULT NULL,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `Code` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `IDX_TransportMode_Name` (`Name`),
  KEY `IRC_TransportMode` (`RowCreate`),
  KEY `IRM_TransportMode` (`RowModify`),
  KEY `transportmode_Id_index` (`Id`),
  KEY `IDX_TransportMode_Delete` (`Deleted`,`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `transportmodelang`
--

DROP TABLE IF EXISTS `transportmodelang`;
CREATE TABLE IF NOT EXISTS `transportmodelang` (
  `Id` bigint NOT NULL,
  `Lang` int NOT NULL DEFAULT '0',
  `TransportMode` bigint NOT NULL,
  `Name` varchar(100) NOT NULL,
  `VoucherComment` blob,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_TransportModeLang_TM` (`TransportMode`),
  KEY `IDX_TransportModeLang_LTM` (`Lang`,`TransportMode`),
  KEY `IRC_TransportModeLang` (`RowCreate`),
  KEY `IRM_TransportModeLang` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` int DEFAULT NULL,
  `customercontact_id` int DEFAULT NULL,
  `rendszergazda` int DEFAULT '0',
  `megjegyzes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CustomerAddress` bigint DEFAULT NULL,
  `TransportMode` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id_uindex` (`id`),
  UNIQUE KEY `users_costumercontact_id_uindex` (`customercontact_id`,`deleted_at`),
  UNIQUE KEY `users_employee_id_deleted_at_uindex` (`employee_id`,`deleted_at`),
  UNIQUE KEY `users_rendszergazda_id_uindex` (`rendszergazda`,`id`),
  KEY `users_email_index` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `employee_id`, `customercontact_id`, `rendszergazda`, `megjegyzes`, `CustomerAddress`, `TransportMode`, `created_at`, `updated_at`, `deleted_at`, `remember_token`, `image_url`) VALUES
(20, 'Cseszneki Gyula', 'cgyulas@gmail.com', NULL, '7367cc4cee061a476290d18978830414', 2, NULL, 2, NULL, NULL, NULL, '2021-10-23 12:08:25', '2021-10-23 12:08:25', NULL, 'A1234567', 'public/img/Gyula_cv.png'),
(0, 'administrator', NULL, NULL, '200ceb26807d6bf99fd6f4f0d1ca54d4', NULL, NULL, 2, 'administrator', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `vat`
--

DROP TABLE IF EXISTS `vat`;
CREATE TABLE IF NOT EXISTS `vat` (
  `Id` bigint NOT NULL,
  `DirectionBuy` smallint NOT NULL DEFAULT '0',
  `Name` varchar(100) NOT NULL,
  `Rate` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `ExpenseRate` decimal(18,4) DEFAULT NULL,
  `Converse` smallint NOT NULL DEFAULT '0',
  `ConverseText` varchar(100) DEFAULT NULL,
  `Eu` smallint NOT NULL DEFAULT '0',
  `CashRegIndex` int NOT NULL DEFAULT '0',
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `ShowDetailName` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UNQ_Vat` (`DirectionBuy`,`Name`,`Rate`),
  KEY `IRC_Vat` (`RowCreate`),
  KEY `IRM_Vat` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `warehouse`
--

DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE IF NOT EXISTS `warehouse` (
  `Id` bigint NOT NULL,
  `Site` bigint NOT NULL DEFAULT '0',
  `Name` varchar(100) NOT NULL,
  `AllowNegativeBalance` smallint NOT NULL DEFAULT '0',
  `PermissionProtected` smallint NOT NULL DEFAULT '0',
  `Trust` smallint NOT NULL DEFAULT '0',
  `TrustCustomer` bigint DEFAULT NULL,
  `TrustCustomerAddress` bigint DEFAULT NULL,
  `OwnerEmployee` bigint DEFAULT NULL,
  `OwnerInvestment` bigint DEFAULT NULL,
  `SellBanned` smallint NOT NULL DEFAULT '0',
  `Foreignn` smallint NOT NULL DEFAULT '0',
  `Zip` varchar(10) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Street` varchar(100) DEFAULT NULL,
  `HouseNumber` varchar(20) DEFAULT NULL,
  `ContactName` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Comment` blob,
  `Deleted` smallint NOT NULL DEFAULT '0',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  `GLN` varchar(40) DEFAULT NULL,
  `IsConsigner` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `FK_Warehouse_TrustCustomer` (`TrustCustomer`),
  KEY `FK_Warehouse_TrustAddress` (`TrustCustomerAddress`),
  KEY `FK_Warehouse_Employee` (`OwnerEmployee`),
  KEY `IDX_Warehouse_Name` (`Name`),
  KEY `IRC_Warehouse` (`RowCreate`),
  KEY `IRM_Warehouse` (`RowModify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `warehousebalance`
--

DROP TABLE IF EXISTS `warehousebalance`;
CREATE TABLE IF NOT EXISTS `warehousebalance` (
  `Id` bigint NOT NULL AUTO_INCREMENT,
  `Product` bigint NOT NULL,
  `Warehouse` bigint NOT NULL,
  `Balance` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `AllocatedBalance` decimal(18,4) DEFAULT NULL,
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_WarehouseBalance_Warehouse` (`Warehouse`),
  KEY `IDX_WarehouseBalance_Balance` (`Balance`),
  KEY `IRC_WarehouseBalance` (`RowCreate`),
  KEY `IRM_WarehouseBalance` (`RowModify`),
  KEY `UNQ_WarehouseBalance` (`Product`,`Warehouse`)
) ENGINE=InnoDB AUTO_INCREMENT=7086 DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `warehousedailybalance`
--

DROP TABLE IF EXISTS `warehousedailybalance`;
CREATE TABLE IF NOT EXISTS `warehousedailybalance` (
  `Id` bigint NOT NULL,
  `Product` bigint NOT NULL,
  `Warehouse` bigint NOT NULL,
  `Date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Balance` decimal(18,4) NOT NULL DEFAULT '0.0000',
  `RowCreate` timestamp NULL DEFAULT NULL,
  `RowModify` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_WarehouseDailyBalance_Wareh` (`Warehouse`),
  KEY `IDX_WarehouseDailyBalance_Blc` (`Balance`),
  KEY `IRC_WarehouseDailyBalance` (`RowCreate`),
  KEY `IRM_WarehouseDailyBalance` (`RowModify`),
  KEY `UNQ_WarehouseDailyBalance` (`Product`,`Warehouse`,`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `xmlimport`
--

DROP TABLE IF EXISTS `xmlimport`;
CREATE TABLE IF NOT EXISTS `xmlimport` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ok` int NOT NULL DEFAULT '0',
  `error` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `xmlimportdetail`
--

DROP TABLE IF EXISTS `xmlimportdetail`;
CREATE TABLE IF NOT EXISTS `xmlimportdetail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `xmlimport_id` int NOT NULL,
  `tablename` varchar(100) NOT NULL,
  `recordnumber` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
