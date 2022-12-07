<script>

function RCB( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
    if ( aData.feldolgozott == 1 )
    {
        $('td', nRow).css('background-color', 'lightgray' );
    }
    else if ( aData.feldolgozott == 0 )
    {
        $('td', nRow).css('background-color', 'lightgreen');
    }
}
</script>
