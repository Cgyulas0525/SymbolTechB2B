CREATE DEFINER=`root`@`localhost` FUNCTION `getProductPrice`($customer INT, $product INT, $quantity INT, $quantityUnit INT, $currency INT) RETURNS decimal(18,4)
    DETERMINISTIC
BEGIN
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
	
	RETURN mPrice;
END