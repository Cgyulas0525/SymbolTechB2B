<aside class="main-sidebar sidebar-light-primary elevation-4">
    <a href="{{ route('home') }}" class="brand-link">
        <img src={{ URL::asset('/public/img/B2B.png') }}
             alt="{{ config('app.name') }} Logo"
             class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-600">{{ config('app.name') }}</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('layouts.menu')
            </ul>
        </nav>
    </div>

</aside>



