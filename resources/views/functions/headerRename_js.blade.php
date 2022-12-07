<script type="text/javascript">

    function headerRename(table, data) {
        for ( i = 0; i < data.length; i++) {
            $( table.column( i ).header() ).text( data[i + 1] );
        }
    }

</script>
