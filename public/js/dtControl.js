function dtControl(element, thisTable, func) {
    var tr = $(element).closest('tr');
    var row = thisTable.row(tr);

    if (row.child.isShown()) {
        // This row is already open - close it
        row.child.hide();
        tr.removeClass('shown');
    } else {
        // Open this row
        row.child(func(row.data())).show();
        tr.addClass('shown');
    }
}
