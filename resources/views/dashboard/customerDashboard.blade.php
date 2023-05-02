@extends('layouts.app')

@section('css')
{{--    <link rel="stylesheet" href="public/css/app.css">--}}
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-12" style="margin-top: 10px;">
            <!-- small box -->
            <div class="small-box bg-default">
               <div class="inner">
                   <h3 class="card-title">{{ session('customer_name') }}</h3>
                   <table class="table table-bordered">
                       <tbody>
                       <tr>
                           <td>{{ langClass::trans('B2B partner') }}</td>
                           <td class="text-right">{{ number_format(App\Classes\adminClass::B2BCustomerContactCount()->count(), 0, ',', '.')}}</td>
                       </tr>
                       <tr>
                           <td>{{ langClass::trans('Partner felhasználó') }}</td>
                           <td class="text-right">{{ number_format(App\Models\Users::where('rendszergazda', '0')->get()->count(), 0, ',', '.')}}</td>
                       </tr>
                       </tbody>
                   </table>
                       <!-- /.card-body -->
                </div>
                <a href="{{ route('B2BCustomerUserIndex') }}" class="small-box-footer sajatBox">{{ langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-xs-12" style="margin-top: 10px;">
            <!-- small box -->
            <div class="small-box bg-default">
                <div class="inner">
                    <h3 class="card-title">{{ langClass::trans('B2B felhasználók') }}</h3>
                    <div class="clearfix"></div>
                    <div class="box box-primary">
                        <div class="box-body"  >
                            <table class="table table-hover table-bordered customer-table" style="width: 100%;">
                            </table>
                        </div>
                    </div>
                    <div class="text-center"></div>
                </div>
                <a href="{{ route('B2BCustomerUserIndex') }}" class="small-box-footer sajatBox">{{ langClass::trans('Tovább') }} <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.datatables_js')
    @include('layouts.RowCallBack_js')

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
                scrollY: 390,
                scrollX: true,
                order: [[1, 'asc'],[2, 'asc']],
                colReorder: true,
                paging: false,
                ajax: "{{ route('B2BCustomerUserIndex') }}",
                columns: [
                    {title: '<a class="btn btn-primary" title="Felvitel" href="{!! route('B2BCustomerUserCreate') !!}"><i class="fa fa-plus-square"></i></a>',
                        data: 'action', sClass: "text-center", width: '150px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Partner cég') . "'"; ?>, data: 'customerName', name: 'customerName'},
                    {title: <?php echo "'" . langClass::trans('Név') . "'"; ?>, data: 'name', name: 'name'},
                    {title: <?php echo "'" . langClass::trans('Email') . "'"; ?>, data: 'email', name: 'email'},
                    {title: <?php echo "'" . langClass::trans('Telephely') . "'"; ?>, data: 'CustomerAddressName', name: 'CustomerAddressName'},
                    {title: <?php echo "'" . langClass::trans('Szállítási mód') . "'"; ?>, data: 'TransportModeName', name: 'TransportModeName'},
                ],
                buttons: []
                // buttons: [
                //     {
                //         text: 'Mind +',
                //         action: function () {
                //             table.rows().select();
                //         }
                //     },
                //     {
                //         text: 'Mind -',
                //         action: function () {
                //             table.rows().deselect();
                //         }
                //     }
                // ],
                // fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                //     RCB(nRow, aData, iDisplayIndex, iDisplayIndexFull);
                // },
            });

        });
    </script>
@endsection

