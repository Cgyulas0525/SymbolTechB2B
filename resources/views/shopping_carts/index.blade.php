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
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <section class="content-header">
                            <div class="row">
                                <div class="mylabel col-sm-1">
                                    <h4>{{ langClass::trans('Kosár') }}</h4>
                                </div>
                                <div class="mylabel col-sm-1">
                                    <h5 class="text-right">{{ langClass::trans('Megrendelő:') }}</h5>
                                </div>
                                <div class="col-sm-2">
                                    {!! Form::select('contact', App\Services\CustomerOrderContactService::contactSelect(), empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact'],
                                            ['class'=>'select2 form-control', 'id' => 'contact']) !!}
                                </div>
                                <div class="mylabel col-sm-1">
                                    <h5 class="text-right">{{ langClass::trans('Év:') }}</h5>
                                </div>
                                <div class="col-sm-1">
                                    {!! Form::select('year', App\Services\CustomerOrderYearService::handle(), empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear'],
                                            ['class'=>'select2 form-control', 'id' => 'year']) !!}
                                </div>
                            </div>
                        </section>
                    </div>
                <div class="col-lg-12 col-md-12 col-xs-12">
                    @include('flash::message')
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered partners-table" style="width: 100%;"></table>
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

        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('[data-widget="pushmenu"]').PushMenu('collapse');

            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 390,
                scrollX: true,
                paging: false,
                order: [[1, 'desc']],
                ajax: "{{ route('shoppingCartIndex', ['customerContact' => ( (empty($_COOKIE['scContact']) ? 0 : $_COOKIE['scContact']) == 0 ? myUser::user()->customercontact_id : -99999),
                                                       'year' => empty($_COOKIE['scYear']) ? date('Y') : $_COOKIE['scYear']]) }}",
                columns: [
                    {title: '<a class="btn btn-primary" title="Felvitel" href="{!! route('shoppingCarts.create') !!}"><i class="fa fa-plus-square"></i></a>',
                        data: 'action', sClass: "text-center", width: '250px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Bizonylatszám') . "'"; ?>, data: 'VoucherNumber', name: 'VoucherNumber'},
                    {title: <?php echo "'" . langClass::trans('Tétel') . "'"; ?>, data: 'DetailNumber', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'DetailNumber'},
                    {title: <?php echo "'" . langClass::trans('Kelt') . "'"; ?>, data: 'VoucherDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'150px', name: 'VoucherDate'},
                    {title: <?php echo "'" . langClass::trans('Száll.hat.') . "'"; ?>, data: 'DeliveryDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'150px', name: 'DeliveryDate'},
                    {title: <?php echo "'" . langClass::trans('Netto') . "'"; ?>, data: 'NetValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'NetValue'},
                    {title: <?php echo "'" . langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'VatValue'},
                    {title: <?php echo "'" . langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'GrossValue'},
                    {title: <?php echo "'" . langClass::trans('Pénznem') . "'"; ?>, data: 'CurrencyName', sClass: "text-center", name: 'CurrencyName'},
                    {title: <?php echo "'" . langClass::trans('Fizetési mód') . "'"; ?>, data: 'PaymentMethodName', name: 'PaymentMethodName'},
                    {title: <?php echo "'" . langClass::trans('Szállítási mód') . "'"; ?>, data: 'TransportModeName', name: 'TransportModeName'},
                    {title: <?php echo "'" . langClass::trans('Rendelésszám') . "'"; ?>, data: 'CustomerOrderVoucherNumber', name: 'CustomerOrderVoucherNumber'},
                ],
                buttons: []
            });

            function changeTableUrl() {

                let url = '{{ route('shoppingCartIndex', [":customerContact", ":year"]) }}';

                url = url.replace(':customerContact', ($('#contact').val() == 0) ? <?php echo myUser::user()->customercontact_id; ?> : -99999);
                url = url.replace(':year', $('#year').val());

                table.ajax.url(url).load();
            }

            $('#contact').change(function () {

                createCookie('scContact', $('#contact').val(), '30');
                changeTableUrl();

            })

            $('#year').change(function () {

                createCookie('scYear', $('#year').val(), 30);
                changeTableUrl();

            })


        });
    </script>
@endsection


