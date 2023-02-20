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
                        <h4>{{ langClass::trans('Belső felhasználók') }}</h4>
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
                order: [[1, 'asc']],
                paging: false,
                ajax: "{{ route('B2BUserIndex') }}",
                columns: [
                    {title: '<a class="btn btn-primary" title="Felvitel" href="{!! route('users.create') !!}"><i class="fa fa-plus-square"></i></a>',
                        data: 'action', sClass: "text-center", width: '150px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Név') . "'"; ?>, data: 'name', name: 'name'},
                    {title: <?php echo "'" . langClass::trans('Email') . "'"; ?>, data: 'email', name: 'email'},
                    {title: <?php echo "'" . langClass::trans('Kép') . "'"; ?>, data: 'kep', sClass: "text-center", width: '150px', name: 'action', orderable: false, searchable: false},
                    {title: <?php echo "'" . langClass::trans('Beosztás') . "'"; ?>, data: 'rgnev', name: 'rgnev'},
                    {title: <?php echo "'" . langClass::trans('Belépés') . "'"; ?>, data: 'B2BLoginCount', render: $.fn.dataTable.render.number( '.', ',', 0), sClass: "text-right", width:'75px', name: 'B2BLoginCount'},
                ]
            });

        });
    </script>
@endsection

