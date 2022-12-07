<script type="text/javascript">

    function rowCellText(sor, oszlop) {
        var text = '.partners-table tbody tr:eq(';
        text = text.concat(sor.toString());
        text = text.concat(') td:eq(');
        text = text.concat(oszlop.toString());
        text = text.concat(')');
        return text;
    }

</script>
