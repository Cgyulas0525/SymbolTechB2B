@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if (myUser::user()->name === "administrator")
            @include('dashboard.createUser')
        @else
            @if (md5(myUser::user()->megjegyzes) == myUser::user()->password ) {
                @include('dashboard.passwordChange')
            @else
                @if ( myUser::user()->rendszergazda === 0 )
                    @include('dashboard.dashboard')
                @endif
                @if ( myUser::user()->rendszergazda === 1 )
                    @include('dashboard.customerDashboard')
                @endif
                @if ( myUser::user()->rendszergazda === 2 )
                    @include('dashboard.adminDashboard')
                @endif
            @endif
        @endif
    </div>
</div>
@endsection
