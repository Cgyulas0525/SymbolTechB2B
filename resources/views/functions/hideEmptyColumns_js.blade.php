<script type="text/javascript">

    function hideEmptyColumns(selector) {
        var emptyColumnsIndexes = []; // store index of empty columns here
        // check each column separately for empty cells
        $(selector).find('th').each(function(i) {
            // get all cells for current column
            var cells = $(this).parents('table').find('tr td:nth-child(' + (i + 1) + ')');
            var emptyCells = 0;

            cells.each(function(cell) {
                // increase emptyCells if current cell is empty, trim string to remove possible spaces in cell
                if ($(this).html().trim() === '') {
                    emptyCells++;
                }
            });

            // if all cells are empty push current column to emptyColumns
            if (emptyCells === $(cells).length) {
                emptyColumnsIndexes.push($(this).index());
            }
        });

        // only make changes if there are columns to hide
        if (emptyColumnsIndexes.length > 0) {
            /* add class never to all empty columns
                never is a special class of the Responsive extension:
                Columns with class never will never be visible, regardless of the browser width, and the data will not be shown in a child row
            */
            $((selector).DataTable().columns(emptyColumnsIndexes).header()).addClass('never');

            $(selector).DataTable().columns(emptyColumnsIndexes).visible(false);
            // Recalculate the column breakpoints based on the class information of the column header cells, class never will now be available to Responsive extension
            $(selector).DataTable().columns.adjust().responsive.rebuild();
            // immediatly call recalc to have Responsive extension updae the display for the cahnge in classes
            $(selector).DataTable().columns.adjust().responsive.recalc();
        }
    }

</script>
