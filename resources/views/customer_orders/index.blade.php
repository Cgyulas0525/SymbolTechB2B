@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="public/css/app.css">
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
                            <div class="col-sm-2">
                                <h4><a id="fejszoveg">{{ langClass::trans('Megrendelések') }}</a></h4>
                            </div>
                            <div class="mylabel col-sm-1">
                                <h5 class="text-right">{{ langClass::trans('Megrendelő:') }}</h5>
                            </div>
                            <div class="col-sm-2">
                                {!! Form::select('contact', App\Services\CustomerOrderContactService::contactSelect(), empty($_COOKIE['coContact']) ? 0 : $_COOKIE['coContact'],
                                        ['class'=>'select2 form-control', 'id' => 'contact']) !!}
                            </div>
                            <div class="mylabel col-sm-1">
                                <h5 class="text-right">{{ langClass::trans('Év:') }}</h5>
                            </div>
                            <div class="col-sm-1">
                                {!! Form::select('year', App\Services\CustomerOrderYearService::handle(), empty($_COOKIE['coYear']) ? date('Y') : $_COOKIE['coYear'],
                                        ['class'=>'select2 form-control', 'id' => 'year']) !!}
                            </div>
                        </div>
                    </section>
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered partners-table w-100"></table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    @include('functions.cookiesFunctions_js')

    <script type="text/javascript">

        var table;
        var tableName = 'CustomerOrder';

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('[data-widget="pushmenu"]').PushMenu('collapse');

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 450,
                scrollX: true,
                order: [2, 'desc'],
                paging: false,
                // "sDom": 'Rlfrtip',
                "bStateSave": true,
                ajax: "{{ route('customerOrderIndex', ['customerContact' => ( (empty($_COOKIE['coContact']) ? 0 : $_COOKIE['coContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                       'year' => empty($_COOKIE['coYear']) ? date('Y') : $_COOKIE['coYear']]) }}",
                columns: [
                    {title: <?php echo "'" . langClass::trans('Tételek') . "'"; ?>, data: 'action', sClass: "text-center", width: '45px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Másolás') . "'"; ?>, data: 'tetelszam', sClass: "text-center", width: '45px', name: 'tetelszam1', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Megrendelés szám') . "'"; ?>, data: 'VoucherNumber', name: 'VoucherNumber'},
                    {title: <?php echo "'" . langClass::trans('Dátum') . "'"; ?>, data: 'VoucherDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'150px', name: 'VoucherDate'},
                    {title: <?php echo "'" . langClass::trans('Netto') . "'"; ?>, data: 'NetValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'NetValue'},
                    {title: <?php echo "'" . langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'VatValue'},
                    {title: <?php echo "'" . langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'GrossValue'},
                    {title: <?php echo "'" . langClass::trans('Pénznem') . "'"; ?>, data: 'currencyName', sClass: "text-center", width:'25px', name: 'currencyName'},
                    {title: <?php echo "'" . langClass::trans('Tétel') . "'"; ?>, data: 'tetelszam', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'tetelszam'},
                    {title: <?php echo "'" . langClass::trans('Státusz') . "'"; ?>, data: 'statusName', name: 'statusName'},
                    {title: <?php echo "'" . langClass::trans('Id') . "'"; ?>, data: 'Id',  sClass: "text-right", width:'75px', name: 'Id', orderable: false, searchable: false, visible: false},
                ],
                columnDefs: [
                    {  targets: 1,
                        render: function (data, type, row, meta) {
                            return '<a type="button" class="edit btn btn-primary btn-sm copyOrder" style="width: 40px;" onclick="copyCustomerOrderToShoppingCart('+meta["row"]+')" title="Másolás"><i class="far fa-copy"></i></a> ';
                        }
                    }
                ],
                buttons: [],
            });


            function changeTableUrl() {

                let url = '{{ route('customerOrderIndex', [":customerContact", ":year"]) }}';

                url = url.replace(':customerContact', ($('#contact').val() == 0) ? <?php echo myUser::user()->customercontact_id; ?> : -99999);
                url = url.replace(':year', $('#year').val());

                table.ajax.url(url).load();
            }

            $('#contact').change(function () {

                createCookie('coContact', $('#contact').val(), '30');
                changeTableUrl();

            })

            $('#year').change(function () {

                createCookie('coYear', $('#year').val(), 30);
                changeTableUrl();

            })

        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast'
            },
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        })

        function copyCustomerOrderToShoppingCart(Row) {
            swal.fire({
                title: <?php echo "'" . langClass::trans("Megrendelés kosárba másolás!") . "'"; ?>,
                text: <?php echo "'" . langClass::trans("Biztosan kosárba másolja a megrendelés összes tételét?") . "'"; ?>,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: <?php echo "'" . langClass::trans("Kosárba") . "'"; ?>,
                cancelButtonText: <?php echo "'" . langClass::trans("Kilép") . "'"; ?>
            }).then((result) => {
                if (result.isConfirmed) {
                    var d = table.row(Row).data();
                    $.ajax({
                        type:"GET",
                        url:"{{url('api/copyCustomerOrderToShoppingCart')}}",
                        data: { Id: d.Id},
                        success: function (response) {
                            Toast.fire({
                                icon: 'success',
                                title: 'A megrendelés tételeit a kosárba másoltuk!'
                            })
                        },
                        error: function (response) {
                            // console.log('Error:', response);
                            alert('nem ok');
                        }
                    });


                    {{--if (fejszoveg === <?php echo "'" . langClass::trans('Összes kosár') . "'"; ?> || fejszoveg === <?php echo "'" . langClass::trans('Idei kosár') . "'"; ?>) {--}}
                    {{--    $.ajax({--}}
                    {{--        type:"GET",--}}
                    {{--        url:"{{url('api/copyShoppingCartToShoppingCart')}}",--}}
                    {{--        data: { Id: d.Id},--}}
                    {{--        success: function (response) {--}}
                    {{--            console.log('Error:', response);--}}
                    {{--        },--}}
                    {{--        error: function (response) {--}}
                    {{--            // console.log('Error:', response);--}}
                    {{--            alert('nem ok');--}}
                    {{--        }--}}
                    {{--    });--}}
                    {{--} else {--}}
                    // }
                }
            });
        }

    </script>
@endsection




