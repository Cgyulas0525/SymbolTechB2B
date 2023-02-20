<!-- Datatables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.0/js/buttons.print.min.js"></script>

<script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>

<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/searchpanes/2.0.0/js/dataTables.searchPanes.min.js"></script>

<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/colreorder/1.5.5/js/dataTables.colReorder.min.js"></script>

<script src="https://cdn.datatables.net/plug-ins/1.11.5/api/column().title().js"></script>

<script src="https://cdn.datatables.net/rowgroup/1.2.0/js/dataTables.rowGroup.min.js"></script>

<script>
    $(function () {
        function urlChange(table, url) {
            table.ajax.url(url).load();
        }

        function currencyFormatDE(num) {
            return (
                num
                    .toFixed(0) // always two decimal digits
                    .replace('.', ',') // replace decimal point character with ,
                    .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
            ) // use . as a separator
        }

        $.extend( true, $.fn.dataTable.defaults, {
            language: {
                "emptyTable": <?php echo "'" . langClass::trans("Nincs rendelkezésre álló adat") . "'"; ?>,
                "info": <?php echo "'" . langClass::trans("Találatok: _START_ - _END_ Összesen: _TOTAL_") . "'"; ?>,
                "infoEmpty": <?php echo "'" . langClass::trans("Nulla találat") . "'"; ?>,
                "infoFiltered": <?php echo "'" . langClass::trans("(_MAX_ összes rekord közül szűrve)") . "'"; ?>,
                "infoThousands": " ",
                "lengthMenu": <?php echo "'" . langClass::trans("_MENU_ találat oldalanként") . "'"; ?>,
                "loadingRecords": <?php echo "'" . langClass::trans("Betöltés...") . "'"; ?>,
                "processing": <?php echo "'" . langClass::trans("Feldolgozás...") . "'"; ?>,
                "search": <?php echo "'" . langClass::trans("Keresés:") . "'"; ?>,
                "zeroRecords": <?php echo "'" . langClass::trans("Nincs a keresésnek megfelelő találat") . "'"; ?>,
                "paginate": {
                    "first": <?php echo "'" . langClass::trans("Első") . "'"; ?>,
                    "previous": <?php echo "'" . langClass::trans("Előző") . "'"; ?>,
                    "next": <?php echo "'" . langClass::trans("Következő") . "'"; ?>,
                    "last": <?php echo "'" . langClass::trans("Utolsó") . "'"; ?>
                },
                "aria": {
                    "sortAscending": <?php echo "'" . langClass::trans(": aktiválja a növekvő rendezéshez"). "'"; ?>,
                    "sortDescending": <?php echo "'" . langClass::trans(": aktiválja a csökkenő rendezéshez") . "'"; ?>
                },
                "select": {
                    "rows": {
                        "_": <?php echo "'" . langClass::trans("%d sor kiválasztva") . "'"; ?>,
                        "1": <?php echo "'" . langClass::trans("1 sor kiválasztva") . "'"; ?>
                    },
                    "cells": {
                        "1": <?php echo "'" . langClass::trans("1 cella kiválasztva") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("%d cella kiválasztva") . "'"; ?>
                    },
                    "columns": {
                        "1": <?php echo "'" . langClass::trans("1 oszlop kiválasztva") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("%d oszlop kiválasztva") . "'"; ?>
                    }
                },
                "buttons": {
                    "colvis": <?php echo "'" . langClass::trans("Oszlopok") . "'"; ?>,
                    "copy": <?php echo "'" . langClass::trans("Másolás") . "'"; ?>,
                    "copyTitle": <?php echo "'" . langClass::trans("Vágólapra másolás") . "'"; ?>,
                    "copySuccess": {
                        "_": <?php echo "'" . langClass::trans("%d sor másolva") . "'"; ?>,
                        "1": <?php echo "'" . langClass::trans("1 sor másolva") . "'"; ?>
                    },
                    "collection": "Gyűjtemény",
                    "colvisRestore": <?php echo "'" . langClass::trans("Oszlopok visszaállítása") . "'"; ?>,
                    "copyKeys": <?php echo "'" . langClass::trans("Nyomja meg a CTRL vagy u2318 + C gombokat a táblázat adatainak a vágólapra másolásához.<br \/><br \/>A megszakításhoz kattintson az üzenetre vagy nyomja meg az ESC billentyűt.") . "'"; ?>,
                    "csv": "CSV",
                    "excel": "Excel",
                    "pageLength": {
                        "-1": <?php echo "'" . langClass::trans("Összes sor megjelenítése") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("%d sor megjelenítése") . "'"; ?>
                    },
                    "pdf": "PDF",
                    "print": <?php echo "'" . langClass::trans("Nyomtat") . "'"; ?>
                },
                "autoFill": {
                    "cancel": <?php echo "'" . langClass::trans("Megszakítás") . "'"; ?>,
                    "fill": <?php echo "'" . langClass::trans("Összes cella kitöltése a következővel: <i>%d<\/i>") . "'"; ?>,
                    "fillHorizontal": <?php echo "'" . langClass::trans("Cellák vízszintes kitöltése") . "'"; ?>,
                    "fillVertical": <?php echo "'" . langClass::trans("Cellák függőleges kitöltése") . "'"; ?>
                },
                "searchBuilder": {
                    "add": <?php echo "'" . langClass::trans("Feltétel hozzáadása") . "'"; ?>,
                    "button": {
                        "0": <?php echo "'" . langClass::trans("Keresés konfigurátor") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("Keresés konfigurátor (%d)") . "'"; ?>
                    },
                    "clearAll": <?php echo "'" . langClass::trans("Összes feltétel törlése") . "'"; ?>,
                    "condition": <?php echo "'" . langClass::trans("Feltétel") . "'"; ?>,
                    "conditions": {
                        "date": {
                            "after": <?php echo "'" . langClass::trans("Után") . "'"; ?>,
                            "before": <?php echo "'" . langClass::trans("Előtt") . "'"; ?>,
                            "between": <?php echo "'" . langClass::trans("Között") . "'"; ?>,
                            "empty": <?php echo "'" . langClass::trans("Üres") . "'"; ?>,
                            "equals": <?php echo "'" . langClass::trans("Egyenlő") . "'"; ?>,
                            "not": <?php echo "'" . langClass::trans("Nem") . "'"; ?>,
                            "notBetween": <?php echo "'" . langClass::trans("Kívül eső") . "'"; ?>,
                            "notEmpty": <?php echo "'" . langClass::trans("Nem üres") . "'"; ?>
                        },
                        "number": {
                            "between": <?php echo "'" . langClass::trans("Között") . "'"; ?>,
                            "empty": <?php echo "'" . langClass::trans("Üres") . "'"; ?>,
                            "equals": <?php echo "'" . langClass::trans("Egyenlő") . "'"; ?>,
                            "gt": <?php echo "'" . langClass::trans("Nagyobb mint") . "'"; ?>,
                            "gte": <?php echo "'" . langClass::trans("Nagyobb vagy egyenlő mint") . "'"; ?>,
                            "lt": <?php echo "'" . langClass::trans("Kissebb mint") . "'"; ?>,
                            "lte": <?php echo "'" . langClass::trans("Kissebb vagy egyenlő mint") . "'"; ?>,
                            "not": <?php echo "'" . langClass::trans("Nem") . "'"; ?>,
                            "notBetween": <?php echo "'" . langClass::trans("Kívül eső") . "'"; ?>,
                            "notEmpty": <?php echo "'" . langClass::trans("Nem üres") . "'"; ?>
                        },
                        "string": {
                            "contains": <?php echo "'" . langClass::trans("Tartalmazza") . "'"; ?>,
                            "empty": <?php echo "'" . langClass::trans("Üres") . "'"; ?>,
                            "endsWith": <?php echo "'" . langClass::trans("Végződik") . "'"; ?>,
                            "equals": <?php echo "'" . langClass::trans("Egyenlő") . "'"; ?>,
                            "not": <?php echo "'" . langClass::trans("Nem") . "'"; ?>,
                            "notEmpty": <?php echo "'" . langClass::trans("Nem üres") . "'"; ?>,
                            "startsWith": <?php echo "'" . langClass::trans("Kezdődik") . "'"; ?>
                        }
                    },
                    "data": <?php echo "'" . langClass::trans("Adat") . "'"; ?>,
                    "deleteTitle": <?php echo "'" . langClass::trans("Feltétel törlése") . "'"; ?>,
                    "logicAnd": <?php echo "'" . langClass::trans("És") . "'"; ?>,
                    "logicOr": <?php echo "'" . langClass::trans("Vagy") . "'"; ?>,
                    "title": {
                        "0": <?php echo "'" . langClass::trans("Keresés konfigurátor") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("Keresés konfigurátor (%d)") . "'"; ?>
                    },
                    "value": <?php echo "'" . langClass::trans("Érték") . "'"; ?>
                },
                "searchPanes": {
                    "clearMessage": <?php echo "'" . langClass::trans("Szűrők törlése") . "'"; ?>,
                    "collapse": {
                        "0": <?php echo "'" . langClass::trans("Szűrőpanelek") . "'"; ?>,
                        "_": <?php echo "'" . langClass::trans("Szűrőpanelek (%d)") . "'"; ?>
                    },
                    "count": "{total}",
                    "countFiltered": "{shown} ({total})",
                    "emptyPanes": <?php echo "'" . langClass::trans("Nincsenek szűrőpanelek") . "'"; ?>,
                    "loadMessage": <?php echo "'" . langClass::trans("Szűrőpanelek betöltése") . "'"; ?>,
                    "title": <?php echo "'" . langClass::trans("Aktív szűrőpanelek: %d") . "'"; ?>
                },
                "datetime": {
                    "previous": <?php echo "'" . langClass::trans("Előző") . "'"; ?>,
                    "next": <?php echo "'" . langClass::trans("Következő") . "'"; ?>,
                    "hours": <?php echo "'" . langClass::trans("Óra") . "'"; ?>,
                    "minutes": <?php echo "'" . langClass::trans("Perc") . "'"; ?>,
                    "seconds": <?php echo "'" . langClass::trans("Másodperc") . "'"; ?>,
                    "amPm": [
                        <?php echo "'" . langClass::trans("de.") . "'"; ?>,
                        <?php echo "'" . langClass::trans("du.") . "'"; ?>
                    ]
                },
                "editor": {
                    "close": <?php echo "'" . langClass::trans("Bezárás") . "'"; ?>,
                    "create": {
                        "button": <?php echo "'" . langClass::trans("Új") . "'"; ?>,
                        "title": <?php echo "'" . langClass::trans("Új") . "'"; ?>,
                        "submit": <?php echo "'" . langClass::trans("Létrehozás") . "'"; ?>
                    },
                    "edit": {
                        "button": <?php echo "'" . langClass::trans("Módosítás") . "'"; ?>,
                        "title": <?php echo "'" . langClass::trans("Módosítás") . "'"; ?>,
                        "submit": <?php echo "'" . langClass::trans("Módosítás") . "'"; ?>
                    },
                    "remove": {
                        "button": <?php echo "'" . langClass::trans("Törlés") . "'"; ?>,
                        "title": <?php echo "'" . langClass::trans("Törlés") . "'"; ?>,
                        "submit": <?php echo "'" . langClass::trans("Törlés") . "'"; ?>
                    }
                }
            },
            processing: true,
            pagingType: 'full_numbers',
            select: true,
            scrollY: 500,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Mind"]],
            dom: 'B<"clear">lfrtip',
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      '<i class="far fa-copy"></i>',
                    titleAttr: 'Másolás',
                    exportOptions: {
                        columns: [ ':visible' ]
                    },
                },

                {
                    extend: 'csvHtml5',
                    text: '<i class="far fa-file-code"></i>',
                    titleAttr: 'CSV',
                    exportOptions: {
                        columns: [ ':visible' ]
                    },
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="far fa-file-excel"></i>',
                    titleAttr: 'Excel',
                    exportOptions: {
                        columns: [ ':visible' ]
                    },
                },
                {
                    extend: 'pdfHtml5',
                    text:      '<i class="far fa-file-pdf"></i>',
                    titleAttr: 'PDF',
                    exportOptions: {
                        columns: [ ':visible' ]
                    },
                }
            ],

        } );
    } );
</script>

