@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('APP_CUSTOMER', langClass::trans('Cég név:')) !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('APP_CUSTOMER', env('APP_CUSTOMER'),['class'=>'form-control', 'required' => 'true', 'id' => 'APP_CUSTOMER']) !!}
                {!! Form::hidden('APP_CUSTOMER_OLD', env('APP_CUSTOMER'),['class'=>'form-control', 'required' => 'true', 'id' => 'APP_CUSTOMER_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('WEBSYX_URL', 'WebSyx URL:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('WEBSYX_URL', env('WEBSYX_URL'),['class'=>'form-control', 'required' => 'true', 'id' => 'WEBSYX_URL']) !!}
                {!! Form::hidden('WEBSYX_URL_OLD', env('WEBSYX_URL'),['class'=>'form-control', 'required' => 'true', 'id' => 'WEBSYX_URL_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('FILE_UPLOAD', 'FILE_UPLOAD:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('FILE_UPLOAD', env('FILE_UPLOAD'),['class'=>'form-control', 'required' => 'true', 'id' => 'FILE_UPLOAD']) !!}
                {!! Form::hidden('FILE_UPLOAD_OLD', env('FILE_UPLOAD'),['class'=>'form-control', 'required' => 'true', 'id' => 'FILE_UPLOAD_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('XML_DIR', 'XML_DIR:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('XML_DIR', env('XML_DIR'),['class'=>'form-control', 'required' => 'true', 'id' => 'XML_DIR']) !!}
                {!! Form::hidden('XML_DIR_OLD', env('XML_DIR'),['class'=>'form-control', 'required' => 'true', 'id' => 'XML_DIR_OLD']) !!}
            </div>
        </div>
    </div>
</div>
{{--<div class="form-group col-sm-6">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-3">--}}
{{--                {!! Form::label('FTP_HOST', 'FTP_HOST:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-9">--}}
{{--                {!! Form::text('FTP_HOST', env('FTP_HOST'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_HOST']) !!}--}}
{{--                {!! Form::hidden('FTP_HOST_OLD', env('FTP_HOST'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_HOST']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-6">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-3">--}}
{{--                {!! Form::label('FTP_USERNAME', 'FTP_USERNAME:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-9">--}}
{{--                {!! Form::text('FTP_USERNAME', env('FTP_USERNAME'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_USERNAME']) !!}--}}
{{--                {!! Form::hidden('FTP_USERNAME_OLD', env('FTP_USERNAME'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_USERNAME_OLD']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-6">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-3">--}}
{{--                {!! Form::label('FTP_PASSWORD', 'FTP_PASSWORD:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-9">--}}
{{--                {!! Form::text('FTP_PASSWORD', env('FTP_PASSWORD'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_PASSWORD']) !!}--}}
{{--                {!! Form::hidden('FTP_PASSWORD_OLD', env('FTP_PASSWORD'),['class'=>'form-control', 'required' => 'true', 'id' => 'FTP_PASSWORD_OLD']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-6">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-3">--}}
{{--                {!! Form::label('MAIL_ENCRYPTION', 'MAIL_ENCRYPTION:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-9">--}}
{{--                {!! Form::text('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_ENCRYPTION']) !!}--}}
{{--                {!! Form::hidden('MAIL_ENCRYPTION_OLD', env('MAIL_ENCRYPTION'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_ENCRYPTION_OLD']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form-group col-sm-6">--}}
{{--    <div class="form-group col-sm-12">--}}
{{--        <div class="row">--}}
{{--            <div class="mylabel col-sm-3">--}}
{{--                {!! Form::label('MAIL_FROM_ADDRESS', 'MAIL_FROM_ADDRESS:') !!}--}}
{{--            </div>--}}
{{--            <div class="mylabel col-sm-9">--}}
{{--                {!! Form::text('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_FROM_ADDRESS']) !!}--}}
{{--                {!! Form::hidden('MAIL_FROM_ADDRESS_OLD', env('MAIL_FROM_ADDRESS'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_FROM_ADDRESS_OLD']) !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

@section('scripts')

    @include('functions.ajax_js')
    @include('functions.sweetalert_js')

    <script type="text/javascript">

        $(function () {

            ajaxSetup();

            function changeEnvironmentVariable(key, value) {
                $.ajax({
                    type: "GET",
                    url:"{{url('api/changeEnvironmentVariable')}}",
                    data: { key: key, value: value },
                    success: function (response) {
                        if ( response.name != null ) {
                            console.log('Error:', response);
                        }
                    },
                    error: function (response) {
                        console.log('Error:', response);
                        alert('nem ok');
                    }
                });
            }

            function environmentVariable(vmi) {
                let uj = $('#'+vmi).val();
                let old = $('#'+vmi+'_OLD').val();
                console.log(old, uj);
                if (uj != old) {
                    changeEnvironmentVariable(vmi, uj);
                }
            }

            $('#saveBtn').click(function (e) {
                environmentVariable('APP_CUSTOMER');
                environmentVariable('WEBSYX_URL');
                environmentVariable('FTP_HOST');
                environmentVariable('FTP_USERNAME');
                environmentVariable('FTP_PASSWORD');
                environmentVariable('FILE_UPLOAD');
                environmentVariable('XML_DIR');
            });
        });

    </script>

@endsection

