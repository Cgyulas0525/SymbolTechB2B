CREATE DEFINER=`root`@`localhost` FUNCTION `getOfferPrice`($customer INT, $product INT, $quantity INT, $quantityUnit INT, $currency INT) RETURNS decimal(18,4)
    DETERMINISTIC
BEGIN
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
END