CREATE DEFINER=`root`@`localhost` FUNCTION `getContractPrice`($customer INT, $product INT, $quantityUnit INT, $currency INT) RETURNS decimal(18,4)
    DETERMINISTIC
BEGIN
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
END