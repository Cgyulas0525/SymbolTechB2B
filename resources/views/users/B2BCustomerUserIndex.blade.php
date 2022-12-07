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
                        <h4>{{ \App\Classes\langClass::trans('B2B Felhasználók') }}</h4>
                    </section>
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
                order: [[1, 'asc'],[2, 'asc']],
                colReorder: true,
                paging: false,
                ajax: "{{ route('B2BCustomerUserIndex') }}",
                columns: [
                    {title: '<a class="btn btn-primary" title="Felvitel" href="{!! route('B2BCustomerUserCreate') !!}"><i class="fa fa-plus-square"></i></a>',
                        data: 'action', sClass: "text-center", width: '150px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Partner cég') . "'"; ?>, data: 'customerName', name: 'customerName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Név') . "'"; ?>, data: 'name', name: 'name'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Email') . "'"; ?>, data: 'email', name: 'email'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Telephely') . "'"; ?>, data: 'CustomerAddressName', name: 'CustomerAddressName'},
                    {title: <?php echo "'" . App\Classes\langClass::trans('Szállítási mód') . "'"; ?>, data: 'TransportModeName', name: 'TransportModeName'},
                ]
            });

        });
    </script>
@endsection


