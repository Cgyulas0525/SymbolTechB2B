@section('css')
    @include('layouts.costumcss')
@endsection

<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('APP_CUSTOMER', \App\Classes\langClass::trans('Cég név:')) !!}
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
                {!! Form::label('MAIL_MAILER', 'MAIL_MAILER:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_MAILER', env('MAIL_MAILER'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_MAILER']) !!}
                {!! Form::hidden('MAIL_MAILER_OLD', env('MAIL_MAILER'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_MAILER_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_HOST', 'MAIL_HOST:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_HOST', env('MAIL_HOST'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_HOST']) !!}
                {!! Form::hidden('MAIL_HOST_OLD', env('MAIL_HOST'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_HOST_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_PORT', 'MAIL_PORT:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_PORT', env('MAIL_PORT'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_PORT']) !!}
                {!! Form::hidden('MAIL_PORT_OLD', env('MAIL_PORT'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_PORT_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_USERNAME', 'MAIL_USERNAME:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_USERNAME', env('MAIL_USERNAME'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_USERNAME']) !!}
                {!! Form::hidden('MAIL_USERNAME_OLD', env('MAIL_USERNAME'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_USERNAME_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_PASSWORD', 'MAIL_PASSWORD:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_PASSWORD', env('MAIL_PASSWORD'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_PASSWORD']) !!}
                {!! Form::hidden('MAIL_PASSWORD_OLD', env('MAIL_PASSWORD'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_PASSWORD_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_ENCRYPTION', 'MAIL_ENCRYPTION:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_ENCRYPTION', env('MAIL_ENCRYPTION'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_ENCRYPTION']) !!}
                {!! Form::hidden('MAIL_ENCRYPTION_OLD', env('MAIL_ENCRYPTION'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_ENCRYPTION_OLD']) !!}
            </div>
        </div>
    </div>
</div>
<div class="form-group col-sm-6">
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="mylabel col-sm-3">
                {!! Form::label('MAIL_FROM_ADDRESS', 'MAIL_FROM_ADDRESS:') !!}
            </div>
            <div class="mylabel col-sm-9">
                {!! Form::text('MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_FROM_ADDRESS']) !!}
                {!! Form::hidden('MAIL_FROM_ADDRESS_OLD', env('MAIL_FROM_ADDRESS'),['class'=>'form-control', 'required' => 'true', 'id' => 'MAIL_FROM_ADDRESS_OLD']) !!}
            </div>
        </div>
    </div>
</div>

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
                environmentVariable('MAIL_MAILER');
                environmentVariable('MAIL_HOST');
                environmentVariable('MAIL_PORT');
                environmentVariable('MAIL_USERNAME');
                environmentVariable('MAIL_PASSWORD');
                environmentVariable('MAIL_ENCRYPTION');
                environmentVariable('MAIL_FROM_ADDRESS');
            });
        });

    </script>

@endsection

