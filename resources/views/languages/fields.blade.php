<!-- Shortname Field -->
@section('css')
    @include('layouts.costumcss')
    @include('layouts.datatables_css')
@endsection

<div class="col-lg-12 col-md-12 col-xs-12 topmarginMinusz2em">
    @include('flash::message')
    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body"  >
            <table class="table table-hover table-bordered partners-table" id = "myTable" style="width: 100%;"></table>
        </div>
    </div>
    <div class="text-center"></div>
</div>

@section('scripts')
    @include('layouts.datatables_js')
    @include('functions.sweetalert_js')
    @include('functions.ajax_js')

    <script type="text/javascript">

        var table;

        $(function () {

            ajaxSetup();


            table = $('.partners-table').DataTable({
                serverSide: true,
                scrollY: 450,
                scrollX: true,
                paging: false,
                select: false,
                order: [[0, 'asc']],
                ajax: "{{ route('indexLanguage', $languages->shortname ) }}",
                columns: [
                    {title: <?php echo "'" . App\Classes\langClass::trans('Magyar') . "'"; ?>, data: 'huname', name: 'huname', width:'30%'},
                    {title: <?php echo "'" . $languages->name . "'"; ?>, data: 'name', name: 'name', id: 'name'},
                ],
                columnDefs: [
                    {
                        targets: [1],
                        render: function ( data, type, full, meta ) {
                            return '<input class="form-control text-left" type="text" value="'+ data +'" onfocusout="QuantityChange('+meta["row"]+', this.value)" style="height:20px;font-size: 15px;"/>';
                        },
                    }
                ],
                buttons: [],
            });

        });

        function QuantityChange(Row, value) {

            var d = table.row(Row).data();
            if ( d.name != value) {
                $.ajax({
                    type:"GET",
                    url:"{{url('api/itemTraslation')}}",
                    data: { id: d.id, name: value },
                    success: function (response) {
                        console.log('Error:', response);
                    },
                    error: function (response) {
                        // console.log('Error:', response);
                        alert('nem ok');
                    }
                });
            }
        }
    </script>
@endsection
