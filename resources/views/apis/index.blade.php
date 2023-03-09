@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="pubic/css/app.css">
    @include('layouts.datatables_css')
    @include('layouts.costumcss')
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary" >
            <div class="box-body">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <section class="content-header">
                        <div class="row">
                            <div class="col-sm-3">
                                <h4>{{ langClass::trans('API futtatások') }}</h4>
                            </div>
                            <div class="col-sm-9">
                                <div class="pull-left">
                                    @include('tools.buttonNonPict', ['akcio' => ["btn btn-primary currencyButton",
                                                                                "btn btn-warning getSUXSDButton",
                                                                                "btn btn-success getSUXMLButton",
                                                                                "btn btn-danger deleteOutputButton",
                                                                                "btn btn-secondary sendBtn"],
                                                              'btnName' => ['Árfolyam',
                                                                            'SÜ Struktúra',
                                                                            'SÜ Adatok',
                                                                            'Output törlés',
                                                                            'Kosár SÜ-be'],
                                                              'title' => ['Árfolyam',
                                                                          'SÜ Struktúra',
                                                                          'SÜ Adatok',
                                                                          'Output törlés',
                                                                          'Kosár SÜ-be']])
                                </div>
                            </div>
                        </div>


{{--                        <div class="col-lg-1 col-md-6 col-xs-12 margintop10">--}}
{{--                            --}}{{--                            <h1><a href="{!! route('getSUXSD') !!}" class="btn btn-success adminDBButton">{{ langClass::trans('SÜ Adat struktúra') }}</a></h1>--}}
{{--                            <h1><a href="{!! route('getSUXML') !!}" class="btn btn-success adminDBButton">{{ langClass::trans('SÜ Adatok') }}</a></h1>--}}
{{--                            <h1><a href="#" class="btn btn-success adminDBButton" id="currencyButton">{{ langClass::trans('Árfolyam') }}</a></h1>--}}
{{--                            <h1><a href="#" class="btn btn-success adminDBButton" id="sendBtn">{{ langClass::trans('Kosár SÜ-be') }}</a></h1>--}}
{{--                        </div>--}}

                    </section>
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-lg-5 col-md-12 col-xs-12">
                            <div class="box box-primary">
                                <div class="box-body"  >
                                    <table class="table table-hover table-bordered partners-table" style="width: 100%;"></table>
                                </div>
                            </div>
                            <div class="text-center"></div>
                        </div>
                        <div class="col-lg-5 col-md-12 col-xs-12">
                            <div class="box box-primary">
                                <div class="box-body"  >
                                    <table class="table table-hover table-bordered apimodel-table" style="width: 100%;"></table>
                                </div>
                            </div>
                            <div class="text-center"></div>
                        </div>
                        <div class="col-lg-2 col-md-12 col-xs-12" style="margin-top: 37px;">
                            <div class="box box-primary">
                                <div class="box-body"  >
                                    <table class="table table-hover table-bordered apimodelerror-table" style="width: 100%; "></table>
                                </div>
                            </div>
                            <div class="text-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    <script src="{{asset('/public/js/dtControl.js')}}"></script>
    <script src="{{asset('/public/js/currencyFormat.js')}}"></script>


    <script type="text/javascript">

        $('[data-widget="pushmenu"]').PushMenu('collapse');

        var currentLocation = window.location;

        function format(d) {
            // `d` is the original data object for the row
            return (
                '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<tr>' + '<td style="width: 150px;">Rekord:</td>' + '<td style="width: 150px; text-align: right;">' + currencyFormat(d.recordnumber) + '</td>' + '</tr>' +
                '<tr>' + '<td style="width: 150px;">Insert:</td>' + '<td style="width: 150px; text-align: right;">' + currencyFormat(d.insertednumber) + '</td>' + '</tr>' +
                '<tr>' + '<td style="width: 150px;">Update:</td>' + '<td style="width: 150px; text-align: right;">'+ currencyFormat(d.updatednumber) + '</td>' + '</tr>' +
                '<tr>' + '<td style="width: 150px;">Error:</td>' + '<td style="width: 150px; text-align: right;">'+ currencyFormat(d.errornumber) + '</td>' + '</tr>' +
                '</table>'
            );
        }

        function errorformat(d) {
            // `d` is the original data object for the row
            return (
                '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
                '<tr>' + '<td style="width: 10%;">SQL:</td>' + '<td style="width: 90%; text-align: left;">' + d.smtp + '</td>' + '</tr>' +
                '<tr>' + '<td style="width: 10%;">Error:</td>' + '<td style="width: 90%; text-align: left;">' + d.error + '</td>' + '</tr>' +
                '</table>'
            );
        }


        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 390,
                scrollX: true,
                paging: false,
                order: [[0, 'desc'], [1, 'asc']],
                ajax: "{{ route('apis.index') }}",
                columns: [
                    // {title: '', data: 'action', sClass: "text-center", width: '30px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Dátum') . "'"; ?>, data: 'created_at', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD hh:mm:ss') : ''; }, sClass: "text-center", width:'150px', name: 'created_at'},
                    {title: <?php echo "'" . langClass::trans('File') . "'"; ?>, data: 'filename', name: 'filename'},
                    {title: <?php echo "'" . langClass::trans('Id') . "'"; ?>, data: 'id', name: 'id'},
                ],
                columnDefs: [
                    {
                        visible: false,
                        targets: [2]
                    },
                ],
                buttons: [
                    {
                        text: 'Táblák',
                        action: function () {
                            var row = this.rows( { selected: true } ).data();
                            let url = '{{ route('indexApimodel', [ ':id']) }}';
                            if (row.length > 0 && row.length < 2 ) {
                                url = url.replace(':id',  row[0].id);
                            } else {
                                let id = -999999;
                                url = url.replace(':id', id);

                            }
                            aptable.ajax.url(url).load();
                        }
                    }
                ],
            });

            var aptable = $('.apimodel-table').DataTable({
                serverSide: true,
                scrollY: 390,
                scrollX: true,
                paging: false,
                order: [[1, 'asc']],
                ajax: "{{ route('indexApimodel', [ 'id' => -999999]) }}",
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                        width: '30px',
                    },
                    {title: <?php echo "'" . langClass::trans('Tábla') . "'"; ?>, data: 'model', name: 'model'},
                    {title: <?php echo "'" . langClass::trans('Id') . "'"; ?>, data: 'id', name: 'id'},
                ],
                columnDefs: [
                    {
                        visible: false,
                        targets: [2]
                    },
                ],
                buttons: [
                    {
                        text: 'Hibák',
                        action: function () {
                            var row = this.rows( { selected: true } ).data();
                            let url = '{{ route('indexApimodelerror', [ ':id']) }}';
                            if (row.length > 0 && row.length < 2 ) {
                                url = url.replace(':id',  row[0].id);
                            } else {
                                let id = -999999;
                                url = url.replace(':id', id);

                            }
                            aetable.ajax.url(url).load();
                        }
                    }
                ],
            });

            var aetable = $('.apimodelerror-table').DataTable({
                serverSide: true,
                scrollY: 390,
                scrollX: true,
                paging: false,
                order: [[1, 'asc']],
                ajax: "{{ route('indexApimodel', [ 'id' => -999999]) }}",
                columns: [
                    {
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                        width: '30px',
                    },
                    {title: <?php echo "'" . langClass::trans('Hiba') . "'"; ?>, data: 'error', name: 'error'},
                ],
                buttons: [],
            });

            $('.apimodel-table tbody').on('click', 'td.dt-control', function () {
                dtControl(this, aptable, format);
            });

            $('.apimodelerror-table tbody').on('click', 'td.dt-control', function () {
                dtControl(this, aetable, errorformat);
            });

            $('.sendBtn').click(function (e) {
                swal.fire({
                    title: <?php echo "'" . langClass::trans("Kosár adatok SÜ ERP-be!") . "'"; ?>,
                    text: <?php echo "'" . langClass::trans("Biztosan átadja a tételeket?") . "'"; ?>,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: <?php echo "'" . langClass::trans("SÜ ERP-be") . "'"; ?>,
                    cancelButtonText: <?php echo "'" . langClass::trans("Kilép") . "'"; ?>
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type:"GET",
                            url:"{{ route('SendShoppingCart') }}",
                            // data: { Customer: 2},
                            success: function (response) {
                                // alert(response);
                                console.log(response);
                            },
                            error: function (response) {
                                console.log('Error:', response);
                                // alert('nem ok');
                            }
                        });
                    }
                });
            });

            $('.currencyButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy betölti az árfolyamokat?",
                    text: "A mai napi MNB árfolyamok!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Betöltés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('api/importCurrency')}}",
                            success: function (response) {
                                console.log('Response:', response);
                                window.location.href = currentLocation;
                            },
                            error: function (response) {
                                console.log('Error:', response);
                            }
                        });
                    }
                })
            });

            $('.deleteOutputButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy törli az output könyvtár file-it?",
                    text: "Output könyvtár ürítés!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Törlés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('api/deleteOutputFiles')}}",
                            success: function (response) {
                                console.log('Response:', response);
                            },
                            error: function (response) {
                                console.log('Error:', response);
                            }
                        });
                    }
                })
            });

            $('.getSUXSDButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy betölti az SÜ adatbázis struktúra exportot?",
                    text: "Adatbázis struktúra változás átvezetés!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Betöltés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('api/structureProcess')}}",
                            success: function (response) {
                                console.log('Response:', response);
                                window.location.href = currentLocation;
                            },
                            error: function (response) {
                                console.log('Error:', response);
                            }
                        });
                    }
                })
            });

            $('.getSUXMLButton').click(function (event) {
                event.preventDefault();
                Swal.fire({
                    title: "Biztos, hogy betölti a SÜ adatbázis exportot?",
                    text: "Adatbázis betöltés!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: "Betöltés",
                    cancelButtonText: "Kilép",
                }).then((result) => {
                    if (result.isConfirmed) {
                        alert(currentLocation);
                        window.location.href = currentLocation;
                        {{--$.ajax({--}}
                        {{--    type: "GET",--}}
                        {{--    url:"{{url('api/deleteOutputFiles')}}",--}}
                        {{--    success: function (response) {--}}
                        {{--        console.log('Response:', response);--}}
                        {{--    },--}}
                        {{--    error: function (response) {--}}
                        {{--        console.log('Error:', response);--}}
                        {{--    }--}}
                        {{--});--}}
                    }
                })
            });

        });
    </script>
@endsection


