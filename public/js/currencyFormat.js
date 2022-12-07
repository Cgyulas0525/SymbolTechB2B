function currencyFormat(num) {
    return (
        num
            .toFixed(0) // always two decimal digits
            .replace('.', ',') // replace decimal point character with ,
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    ) // use . as a separator
}
