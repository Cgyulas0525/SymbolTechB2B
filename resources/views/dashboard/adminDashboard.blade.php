@extends('layouts.app')

<?php
$customerLogin = App\Classes\adminClass::B2BCustomerLoginCount(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                               date('Y-m-d H:i:s', strtotime('today + 1 day')));
?>

@section('css')
    <link rel="stylesheet" href="public/css/app.css">
    <link rel="stylesheet" href="public/css/datatables.css">
    <link rel="stylesheet" href="public/css/Highcharts.css">

    @include('layouts.costumcss')
@endsection

@section('content')
    <div class="row">

        <div class="col-lg-3 col-md-6 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ session('customer_name') }}</h3>
                   <table class="table table-bordered">
                       <tbody>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Felhasználók összesen') }}</td>
                           <td class="text-right">{{ number_format(App\Models\Users::where('rendszergazda', '<>', '2')->get()->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ session('customer_name') . ' '. \App\Classes\langClass::trans('felhasználók') }}</td>
                           <td class="text-right">{{ number_format(App\Models\Users::where('rendszergazda', '1')->get()->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('B2B partnerek') }}</td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BCustomerContactCount()->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Partner felhasználók') }}</td>
                           <td class="text-right">{{ number_format(App\Models\Users::where('rendszergazda', '0')->get()->count(), 0, ',', '.')}}</td>
                       </tr>
                       </tbody>
                   </table>
                       <!-- /.card-body -->
                </div>
                <a href="{{ route('B2BUserIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ \App\Classes\langClass::trans('Belépés 3 hónap') }}</h3>
                   <table class="table table-bordered">
                       <tbody>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('Felhasználók összesen') }}</td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BLogin(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                                                                            date('Y-m-d H:i:s', strtotime('today + 1 day')), NULL)->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ session('customer_name') }} {{ \App\Classes\langClass::trans('felhasználók') }} </td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BEmployeeLogin(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                                                                            date('Y-m-d H:i:s', strtotime('today + 1 day')), [1])->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ session('customer_name') }} {{ \App\Classes\langClass::trans('rendszergazdák') }}</td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BEmployeeLogin(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                                                                            date('Y-m-d H:i:s', strtotime('today + 1 day')), [2])->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ \App\Classes\langClass::trans('B2B partnerek') }}</td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BCustomerContactLogin(date('Y-m-d H:i:s', strtotime('today - 3 month')),
                                                                                                                   date('Y-m-d H:i:s', strtotime('today + 1 day')))->count(), 0, ',', '.')}}</td>
                       </tr>
                       </tbody>
                   </table>
                       <!-- /.card-body -->
                </div>
                <a href="{{ route('B2BCustomerUserIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-5 col-md-6 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
                <div class="inner">
                    <h3 class="card-title">{{ \App\Classes\langClass::trans('B2B partnerek') }}</h3>
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered b2bcustomer" style="width: 100%;">
                            </table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
                <a href="{{ route('B2BUserIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-1 col-md-6 col-xs-12 margintop10">
            <h1><a href="{!! route('settingIndex') !!}" class="btn btn-warning adminDBButton">{{ \App\Classes\langClass::trans('Beállítások') }}</a></h1>
            <h1><a href="{!! route('getCurrencyRate') !!}" class="btn btn-success adminDBButton">{{ \App\Classes\langClass::trans('Árfolyam') }}</a></h1>
        </div>



        <div class="col-lg-6 col-md-6 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ \App\Classes\langClass::trans('Belépés 3 hónap') }}</h3>
                       <!-- /.card-body -->
                   <div class="clearfix"></div>
                   <div class="box box-primary">
                       <div class="box-body"  >
                           <table class="table table-hover table-bordered customerLogin-table" style="width: 100%; height: 50%;">
                           </table>
                       </div>
                   </div>
                   <div class="text-center"></div>
                </div>
                <a href="{{ route('customerIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ \App\Classes\langClass::trans('Belépés 3 hónap') }}</h3>
                       <!-- /.card-body -->
                   <div class="clearfix"></div>
                   <div>
                       <figure class="highcharts-figure">
                           <div id="ThreeMonthsLogin"></div>
                       </figure>
                   </div>
                   <div class="text-center"></div>
                </div>
                <a href="{{ route('B2BCustomerUserIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-xs-12 margintop10">
            <!-- small box -->
            <div class="small-box bg-default">
                <div class="inner">
                    <h3 class="card-title">{{ session('customer_name') }} {{ \App\Classes\langClass::trans('B2B felhasználók') }}</h3>
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered customer-table" style="width: 100%;">
                            </table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
                <a href="{{ route('B2BUserIndex') }}" class="small-box-footer sajatBox">{{ \App\Classes\langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    @include('layouts.RowCallBack_js')
    @include('layouts.highcharts_js')
    @include('hsjs.hsjs')

    <script type="text/javascript">
        $(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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
                scrollY: 250,
                colReorder: true,
                order: [[0, 'asc']],
                ajax: "{{ route('B2BUserIndex') }}",
                paging: false,
                columns: [
                    {{--{title: '<a class="btn btn-primary"title="Felvitel" href="{!! route('users.create') !!}"><i class="fa fa-plus-square"></i></a>',--}}
                    {{--    data: 'action', sClass: "text-center", width: '150px', name: 'action', orderable: false, searchable: false},--}}
                    {title: <?php echo "'" . App\Classes\langClass::trans('Név') . "'"; ?>, data: 'name', name: 'name'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Email') . "'"; ?>, data: 'email', name: 'email'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Kép') . "'"; ?>, data: 'kep', sClass: "text-center", width: '150px', name: 'kep', orderable: false, searchable: false},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Beosztás') . "'"; ?>, data: 'rgnev', name: 'rgnev'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Belépett') . "'"; ?>, data: 'B2BLoginCount', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'50px', name: 'B2BLoginCount'},
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

            var table = $('.b2bcustomer').DataTable({
                serverSide: true,
                scrollY: 135,
                colReorder: true,
                order: [[1, 'asc']],
                ajax: "{{ route('B2BCustomerIndex') }}",
                paging: false,
                searching: false,
                info: false,
                columns: [
                    {title: '', data: 'action', sClass: "text-center", width: '50px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Név') . "'"; ?>, data: 'Name', name: 'Name'},
                ],
                buttons: [],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

            var cltable = $('.customerLogin-table').DataTable({
                serverSide: true,
                scrollY: 250,
                colReorder: true,
                order: [[0, 'asc']],
                ajax: "{{ route('B2BCustomerLoginCountIndex') }}",
                paging: false,
                columns: [
                    {title: <?php echo "'" . App\Classes\langClass::trans('Név') . "'"; ?>, data: 'customerName', width:'250px', name: 'customerName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Belépett') . "'"; ?>, data: 'db', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'50px', name: 'db'},
                ],
                fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                },
            });

            function kategoriaFeltolt(data, value){
                var vkategoria = [];
                for (j = 0; j < data.length; j++){
                    vkategoria.push(data[j].value);
                }
                return vkategoria;
            }

            function kategoriaPieData(data){
                var vpieData = [];
                for (j = 0; j < data.length; j++){
                    vpieData.push({name: data[j].customerName, y: parseInt(Math.round(data[j].db).toFixed(0))});
                }
                return vpieData;
            }

            var kategoria = kategoriaFeltolt(<?php echo $customerLogin; ?>, 'customerName');
            var pieData = kategoriaPieData(<?php echo $customerLogin; ?>);
            var chart = HighChartPie( 'ThreeMonthsLogin', 'pie', 365, kategoria, pieData, <?php echo "'" . App\Classes\langClass::trans('Felhasználói belépések') . "'"; ?>,
                <?php echo "'" . App\Classes\langClass::trans('Felhasználónként') . "'"; ?>, 'DB', 200, true, true, '40%');


        });
    </script>
@endsection

