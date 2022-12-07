@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="public/css/app.css">
    <link rel="stylesheet" href="public/css/datatables.css">
    <link rel="stylesheet" href="public/css/Highcharts.css">
    @include('layouts.costumcss')
@endsection

<?php
    $coffers = App\Classes\customerOfferClass::customerOffers(session('customer_id'));
?>
@section('content')
    <div class="row">
        @if ( $coffers->count() > 0 )
            @foreach ( $coffers as $coffer)
                <div class="{{ App\Classes\utilityClass::offerColLg($coffers->count()) }}" style="text-align: center;">
                    <!-- small box -->
                    <div class="card" style="background-color: #64d5ca; text-align: center; height: 200px;">
                        </br>
                        <h3 class=card-title" >{{ $coffer->Name }}</h3>
                        <h3 class="card-title">{{ date('Y.m.d', strtotime($coffer->ValidFrom)) }} - {{ date('Y.m.d', strtotime($coffer->ValidTo)) }}</h3>
                        <h3 class="card-title">{{ \App\Classes\langClass::trans('Termék') }}: {{ number_format(App\Classes\customerOfferClass::customerOfferProductsCount($coffer->Id), 0, ',', '.') }} {{ \App\Classes\langClass::trans('db') }}</h3>
                    </div>
                    <a href="{{ route('oneOffer', $coffer->Id )}}" data-scroll class="btn btn-primary btn-danger topmarginMinusz1em">Bővebben <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            @endforeach
        @endif
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12 topmargin1em">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ \App\Classes\langClass::trans('Megrendelések') }}</h3>
                   <table class="table table-bordered">
                       <tbody>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Nyitott') }}</td>
                           <td class="text-right">{{ number_format(customerOrderClass::nyitottMegrendelesek(session('customer_id')), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Tétel') }}</td>
                           <td class="text-right">{{ number_format(customerOrderClass::nyitottMegrendelesTetelSzam(session('customer_id')), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Érték') }}</td>
                           <td class="text-right">{{ number_format(customerOrderClass::openCustomerOrderValue(session('customer_id')), 0, ',', '.')}}</td>
                       </tr>
                       </tbody>
                   </table>
                       <!-- /.card-body -->
                </div>
                <a href="{{ route('customerOrders.index') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12 topmargin1em">
            <!-- small box -->
            <div class="small-box bg-default">
                <div class="inner">
                    <h3 class="card-title">{{ \App\Classes\langClass::trans('Hitel keret') }}</h3>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>{{ \App\Classes\langClass::trans('Nyitott') }}</td>
                            <td class="text-right">{{ number_format(customerClass::CustomerDebitQuotaValue(session('customer_id'), 'MaxCredit'), 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>{{ \App\Classes\langClass::trans('Felhasznált') }}</td>
                            <td class="text-right">{{ number_format(App\Models\Customer::where('Deleted', 0)->get()->count() * 15, 0, ',', '.')}}</td>
                        </tr>
                        <tr>
                            <td>{{ \App\Classes\langClass::trans('Szabad') }}</td>
                            <td class="text-right">{{ number_format(App\Models\Customer::where('Deleted', 0)->get()->count() * 148321, 0, ',', '.')}}</td>
                        </tr>
                        </tbody>
                    </table>
                    <!-- /.card-body -->
                </div>
                <a href="{{ route('customerOrders.index') }}" class="small-box-footer sajatBox">Tovább <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @if (env('EURO_CLEAR') == 1)
            <div class="col-lg-12 col-md-12 col-xs-12">
        @else
            <div class="col-lg-6 col-md-6 col-xs-12">
        @endif
            <!-- small box -->
            <div class="small-box bg-default">
                <div class="inner">
                    <h3 class="card-title">Utolsó 3 hónap rendelései</h3>
                    <div class="clearfix"></div>
                    <div class="box box-primary topmargin1em">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered customer-table" style="width: 100%;">
                            </table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
                <a href="{{ route('customerOrders.index') }}" class="small-box-footer sajatBox">Tovább <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        @if (env('EURO_CLEAR') != 1)
            <div class="col-lg-6 col-md-6 col-xs-12">
                <div class="col-12">
                    <h3><i class="fas fa-chart-bar"></i> Megrendelések az elmúlt 12 hónapban</h3>
                </div>
                <div class="card card-info">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#tablazat" data-toggle="tab">Táblázat</a></li>
                            <li class="nav-item"><a class="nav-link" href="#activity" data-toggle="tab">Érték</a></li>
                            <li class="nav-item"><a class="nav-link" href="#average" data-toggle="tab">Átlag érték</a></li>
                            <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab" >Darab</a></li>
                            <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Tétel</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="tablazat">
                                <div>
                                    <div class="box-body"  >
                                        <table class="table table-bordered table-hover CustomerOrderInterval-table"  style="width: 100%;"></table>
                                    </div>
                                </div>
                            </div>
                            @include('dashboard.customerOrderIntervalTab', ['hcid' => 'activity',
                                                                            'hcjs' => 'customerOrderInterval'])
                            @include('dashboard.customerOrderIntervalTab', ['hcid' => 'average',
                                                                            'hcjs' => 'customerOrderAverageInterval'])
                            @include('dashboard.customerOrderIntervalTab', ['hcid' => 'timeline',
                                                                            'hcjs' => 'customerOrderSumInterval'])
                            @include('dashboard.customerOrderIntervalTab', ['hcid' => 'settings',
                                                                            'hcjs' => 'customerOrderDetailSumInterval'])
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    @include('layouts.RowCallBack_js')
    @include('layouts.highcharts_js')
    @include('dashboard.customerOrderInterval_js')
    @include('dashboard.customerOrderAverageInterval_js')
    @include('dashboard.customerOrderSumInterval_js')
    @include('dashboard.customerOrderDetailSumInterval_js')
    @include('hsjs.hsjs')

    <script type="text/javascript">
        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('[data-widget="pushmenu"]').PushMenu('collapse');

            function currencyFormatDE(num) {
                return (
                    num
                        .toFixed(0) // always two decimal digits
                        .replace('.', ',') // replace decimal point character with ,
                        .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
                ) // use . as a separator
            }

            var table = $('.customer-table').DataTable({
                serverSide: true,
                scrollY: 300,
                scollX: true,
                colReorder: true,
                ajax: "{{ route('indexCOLastTreeMonth') }}",
                order: [1, 'desc'],
                buttons: [],
                paging: false,
                select: false,
                filter: false,
                columns: [
                    {title: <?php echo "'" . App\Classes\langClass::trans('Megrendelés szám') . "'"; ?>, data: 'VoucherNumber', width:'30%', name: 'VoucherNumber'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Dátum') . "'"; ?>, data: 'VoucherDate', render: function (data, type, row) { return data ? moment(data).format('YYYY.MM.DD') : ''; }, sClass: "text-center", width:'10%', name: 'VoucherDate'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Netto') . "'"; ?>, data: 'NetValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'15%', name: 'NetValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('ÁFA') . "'"; ?>, data: 'VatValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'15%', name: 'VatValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Bruttó') . "'"; ?>, data: 'GrossValue', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'15%', name: 'GrossValue'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Pénznem') . "'"; ?>, data: 'currencyName', sClass: "text-center", width:'5%', name: 'currencyName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Tétel') . "'"; ?>, data: 'tetelszam', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'10%', name: 'tetelszam'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Státusz') . "'"; ?>, data: 'statusName', name: 'statusName'},
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

            var coiTable = $('.CustomerOrderInterval-table').DataTable({
                serverSide: true,
                scrollY: 300,
                colReorder: true,
                ajax: "{{ route('CustomerOrderInterval', [date('Y-m-d', strtotime('today - 12 months')), date('Y-m-d', strtotime('today'))]) }}",
                buttons: [],
                paging: false,
                select: false,
                filter: false,
                columns: [
                    {title: 'Hónap', data: 'nev', name: 'VoucherNumber'},
                    {title: 'Összeg', data: 'osszeg', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'100px', name: 'osszeg'},
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

        });
    </script>
@endsection

