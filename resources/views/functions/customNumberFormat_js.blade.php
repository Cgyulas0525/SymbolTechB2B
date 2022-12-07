<script type="text/javascript">

    function custom_number_format( number_input, decimals, dec_point, thousands_sep ) {

        var number       = ( number_input + '' ).replace( /[^0-9+\-Ee.]/g, '' );
        var finite_number   = !isFinite( +number ) ? 0 : +number;
        var finite_decimals = !isFinite( +decimals ) ? 0 : Math.abs( decimals );
        var seperater     = ( typeof thousands_sep === 'undefined' ) ? '.' : thousands_sep;
        var decimal_pont   = ( typeof dec_point === 'undefined' ) ? ',' : dec_point;

        var number_output   = '';
        var toFixedFix = function ( n, prec ) {
            if( ( '' + n ).indexOf( 'e' ) === -1 ) {
                return +( Math.round( n + 'e+' + prec ) + 'e-' + prec );
            } else {
                var arr = ( '' + n ).split( 'e' );
                let sig = '';
                if ( +arr[1] + prec > 0 ) {
                    sig = '+';
                }
                return ( +(Math.round( +arr[0] + 'e' + sig + ( +arr[1] + prec ) ) + 'e-' + prec ) ).toFixed( prec );
            }
        }
        number_output = ( finite_decimals ? toFixedFix( finite_number, finite_decimals ).toString() : '' + Math.round( finite_number ) ).split( '.' );
        if( number_output[0].length > 3 ) {
            number_output[0] = number_output[0].replace( /\B(?=(?:\d{3})+(?!\d))/g, seperater );
        }
        if( ( number_output[1] || '' ).length < finite_decimals ) {
            number_output[1] = number_output[1] || '';
            number_output[1] += new Array( finite_decimals - number_output[1].length + 1 ).join( '0' );
        }
        return number_output.join( decimal_pont );
    }

</script>
