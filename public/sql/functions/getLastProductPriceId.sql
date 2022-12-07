CREATE DEFINER=`root`@`localhost` FUNCTION `getLastProductPriceId`($Product INT, $QuantityUnit INT, $PriceCategory INT, $Currency INT) RETURNS INT
    DETERMINISTIC
BEGIN
	DECLARE mId INT;
    
    SELECT MAX(t5.Id) INTO mId FROM ProductPrice AS t5 
	 WHERE t5.QuantityUnit = $QuantityUnit 
	   AND t5.PriceCategory = $PriceCategory 
	   AND t5.Currency = $Currency 
	   AND t5.Product = $Product
	GROUP BY t5.Product, t5.QuantityUnit, t5.Currency, t5.PriceCategory;
    
	RETURN mId;
END