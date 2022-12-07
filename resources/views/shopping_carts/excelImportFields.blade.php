@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<?php
    $array = App\Classes\excelImportClass::headLine();
?>

<div class="form-group col-lg-2">
    <div class="row">
        <div class="mylabel col-sm-2">
            {!! Form::label('import_file', 'File:') !!}
        </div>
        <div class="mylabel col-sm-10">
            <label class="image__file-upload">{{ \App\Classes\langClass::trans('Válasszon') }}
                {!! Form::file('import_file',['class'=>'d-none', 'id' => 'import_file', 'accept' => ".xlsx, .xls, .csv"]) !!}
            </label>
        </div>
        <h4>
            <div>
                <p style="text-align: center;">{{ \App\Classes\langClass::trans('Kérem rakja az oszlopokat a következő sorrrendbe') }}:</p>
                <p style="text-align: center;">{{ \App\Classes\langClass::trans('Kód') }}</p>
                <p style="text-align: center;">{{ \App\Classes\langClass::trans('Mennyiség') }}</p>
            </div>
        </h4>
    </div>
</div>
<div class="col-lg-10 col-md-12 col-xs-12 topmargin2em">
    @include('flash::message')
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body"  >
            <table class="table table-hover table-bordered partners-table" id = "myTable" style="width: 100%;">
            </table>
        </div>
    </div>
    <div class="text-center"></div>
    <h4>Rekord: <a id="rekord"> </a> {{ \App\Classes\langClass::trans('Feldolgozott') }}: <a id="feldolgozott"> </a> </h4>
</div>


@section('scripts')
    @include('layouts.datatables_js')
    @include('functions.customNumberFormat_js')
    @include('functions.sweetalert_js')
    @include('functions.ajax_js')
    @include('functions.hideEmptyColumns_js')
    @include('functions.headerRename_js')
    @include('functions.rowCellText_js')

    <script type="text/javascript">

        var table;
        var rows;
        var tableName = 'ExcelImport';
        var oArray = [];

        $(function () {

            ajaxSetup();

            var columnArray = [];
            var titleName;
            var dataName;
            for (i = 0; i < 21; i++) {
                titleName = 'Mező '.concat(i.toString());
                dataName  = 'Field'.concat(i.toString());
                if ( i < 20) {
                    columnArray.push({ title: titleName, data: dataName, name: dataName});
                } else {
                    columnArray.push({title: 'ID', data: 'id', name: 'id'});
                }
            }

            // datatable preInit
            $(document).on( 'preInit.dt', function (e, settings) {
                $.ajax({
                    "url": "{{url('api/datatableLoad')}}",
                    "data": {"name": tableName},
                    "async": false,
                    "dataType": "json",
                    "type": "POST",
                    "success": function (json) {
                        oArray = json;
                    }
                });
            } );

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 400,
                scrollX: true,
                paging: false,
                select: true,
                responsive: true,
                colReorder: true,
                stateSave:  true,
                "sDom": 'Rlfrtip',
                "bStateSave": true,
                order: [[0, 'asc']],
                ajax: "{{ route('excelIndex' ) }}",
                columns: columnArray,
                columnDefs: [
                    {
                        visible: false,
                        targets: [20]
                    },
                ],
                buttons: [],
                initComplete: function (oSettings, json) {
                    // hide empty columns
                    hideEmptyColumns(this);
                    headerRename( table, <?php echo '["' . implode('", "', $array) . '"]' ?>);
                    rows = table.rows().data();
                    $('#rekord').text(rows.length);
                    table.colReorder.order(JSON.parse(oArray['array']));
                },
                "stateSaveCallback": function (settings, data) {
                    // Send an Ajax request to the server with the state object
                    var orderArray = table.colReorder.order();
                    console.log(orderArray.length);
                    $.ajax( {
                        "url": "{{url('api/datatableSave')}}",
                        "data": {"name":tableName, "state": data, "array": orderArray} ,//you can use the id of the datatable as key if it's unique
                        "dataType": "json",
                        "type": "POST",
                        "success": function () {}
                    });
                },
            });

            $("#import_file").on("change", function() {
                $('#fejszoveg').text($('#import_file')[0].files[0].name);
            });

            $('#shopButton').click(function () {
                var title = table.columns().header();
                var titleArray = [];
                for ( i = 0; i < title.length; i++) {
                    if ( table.column( i ).visible() === true ) {
                        titleArray.push(table.column( i ).title());
                    }
                }
                var code;
                var quantity;
                var id;
                var feldolgozott = 0;
                console.log(rows.length);
                for ( i = 0; i < rows.length; i++) {
                    code     = $(rowCellText(i, 0)).text();
                    quantity = $(rowCellText(i, 1)).text();
                    id       = rows[i].id;
                    $.ajax({
                        type:"GET",
                        url:"{{url('api/oneExcelImportToShoppingCartDetail')}}",
                        data: { code: code, quantity: quantity},
                        success: function (response) {
                            console.log('Error:', response);
                            if (response == 0) {
                                $.ajax({
                                    type:"GET",
                                    url:"{{url('api/excelImportIdDelete')}}",
                                    data: { id: id },
                                    success: function (response) {
                                        console.log('Error:', response);
                                    },
                                    error: function (response) {
                                        console.log('Error:', response);
                                        // alert('nem ok');
                                    }
                                });
                                feldolgozott++;
                            }
                        },
                        error: function (response) {
                            // console.log('Error:', response);
                            alert('nem ok');
                        }
                    });
                    $('#feldolgozott').text(feldolgozott);
                }
            });

        });

    </script>
@endsection



