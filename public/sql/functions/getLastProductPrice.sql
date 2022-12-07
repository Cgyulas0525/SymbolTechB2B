CREATE DEFINER=`root`@`localhost` FUNCTION `getLastProductPrice`($customer INT, $product INT, $quantityUnit INT, $currency INT) RETURNS decimal(18,4)
    DETERMINISTIC
BEGIN
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
END