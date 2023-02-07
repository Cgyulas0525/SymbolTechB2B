-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1:3306
-- Létrehozás ideje: 2023. Feb 07. 12:56
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
CREATE DEFINER=`b2b`@`localhost` FUNCTION `discountPercentage` (`$customer` INT, `$product` INT, `$quantity` INT, `$quantityUnit` INT, `$currency` INT) RETURNS INT DETERMINISTIC BEGIN
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
