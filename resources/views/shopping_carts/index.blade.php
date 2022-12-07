@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="pubic/css/app.css">
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary" >
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-xs-12">
                        <section class="content-header">
                            <h4>{{ \App\Classes\langClass::trans('Kosár') }}</h4>
                        </section>
                    </div>
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

    <script type="text/javascript">
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
                order: [[1, 'desc']],
                ajax: "{{ route('shoppingCarts.index') }}",
                columns: [
                    {title: '<a class="btn btn-primary" title="Felvitel" href="{!! route('shoppingCarts.create') !!}"><i class="fa fa-plus-square"></i></a>',
                        data: 'action', sClass: "text-center", width: '250px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Bizonylatszám') . "'"; ?>, data: 'VoucherNumber', name: 'VoucherNumber'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Tétel') . "'"; ?>, data: 'DetailNumber', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'DetailNumber'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Kelt') . "'"; ?>, data: 'VoucherDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'150px', name: 'VoucherDate'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Száll.hat.') . "'"; ?>, data: 'DeliveryDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'150px', name: 'DeliveryDate'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Netto') . "'"; ?>, data: 'NetValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'NetValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'VatValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'GrossValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Pénznem') . "'"; ?>, data: 'CurrencyName', sClass: "text-center", name: 'CurrencyName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Fizetési mód') . "'"; ?>, data: 'PaymentMethodName', name: 'PaymentMethodName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Szállítási mód') . "'"; ?>, data: 'TransportModeName', name: 'TransportModeName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Rendelésszám') . "'"; ?>, data: 'CustomerOrderVoucherNumber', name: 'CustomerOrderVoucherNumber'},
                ]
            });

        });
    </script>
@endsection


