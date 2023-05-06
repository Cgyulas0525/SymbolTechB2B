@if (md5(myUser::user()->megjegyzes) != myUser::user()->password )
    <li class="nav-item">
        <a href="{{ route('dIndex') }}"
           class="nav-link {{ (Request::is('dIndex*') || Request::is('login*')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p> {{ langClass::trans('Vezérlő') }}</p>
        </a>
    </li>

    @if ( myUser::user()->rendszergazda === 0 )

{{--        <li class="nav-item">--}}
{{--            <a href="{{ route('customerOrders.create') }}"--}}
{{--               class="nav-link">--}}
{{--            </a>--}}
{{--        </li>--}}
        @if (env('FAVORITE_PRODUCTS') == 1)
            <li class="nav-item">
                <a href="{{ route('customerContactFavoriteProducts.index') }}"
                   class="nav-link {{ Request::is('customerContactFavoriteProducts*') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    <p>{{ langClass::trans('Kedvenc termékek') }} ({{ \App\Models\CustomerContactFavoriteProduct::count() }}) </p>
                </a>
            </li>
        @endif
        <li class="nav-item">
            <a href="{{ route('editShoppingCart') }}"
               class="nav-link {{ Request::is('editShoppingCart*') ? 'active' : '' }}">
                <i class="fas fa-cart-plus"></i>
                <p> {{ langClass::trans('Új Kosár') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('shoppingCartIndex', ['customerContact' => myUser::user()->customercontact_id, 'year' => date('Y')]) }}"
               class="nav-link {{ Request::is('shoppingCarts*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <p> {{ langClass::trans('Kosár') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('customerOrderIndex', ['customerContact' => myUser::user()->customercontact_id, 'year' => date('Y')]) }}"
               class="nav-link {{ Request::is('customerOrder*') ? 'active' : '' }}">
                <i class="fas fa-cart-arrow-down"></i>
                <p> {{ langClass::trans('Megrendelések') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('profil', myUser::user()->id) }}"
               class="nav-link {{ Request::is('profil*') ? 'active' : '' }}">
                <i class="fas fa-user-cog"></i>
                <p> {{ langClass::trans('Profil') }}</p>
            </a>
        </li>
    @endif

    @if ( myUser::user()->rendszergazda > 0 )
        <li class="nav-item">
            <a href="{{ route('B2BCustomerUserIndex') }}"
               class="nav-link {{ Request::is('B2BCustomerUserIndex') ? 'active' : '' }}">
                <i class="fas fa-people-arrows"></i>
                <p> {{ langClass::trans('B2B felhasználók') }}</p>
            </a>
        </li>
        @if ( myUser::user()->rendszergazda === 2 )
            <li class="nav-item">
                <a href="{{ route('B2BUserIndex') }}"
                   class="nav-link {{ Request::is('B2BUserIndex*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    <p> {{ langClass::trans('Belső felhasználók') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('logItems.index') }}"
                    class="nav-link {{ Request::is('logItems*') ? 'active' : '' }}">
                    <i class="fas fa-database"></i>
                    <p> {{ langClass::trans('Log adatok') }}</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link {{ Request::is('settingIndex*') || Request::is('communicationIndex*')  ? 'active' : '' }}">
                    <i class="fas fa-copy"></i>
                    <p>
                        {{ langClass::trans('Beállítások') }}
                        <i class="fas fa-angle-left right"></i>
                        <span class="badge badge-info right"></span>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('settingIndex') }}"
                           class="nav-link {{ Request::is('settingIndex*') ? 'active' : '' }}">
                            <i class="far fa-envelope-open"></i>
                            <p> {{ langClass::trans('Email') }}</p>
                        </a>
                    </li>
                     <li class="nav-item">
                        <a href="{{ route('communicationIndex') }}"
                           class="nav-link {{ Request::is('communicationIndex*') ? 'active' : '' }}">
                            <i class="fas fa-broadcast-tower"></i>
                            <p> {{ langClass::trans('Kommunikáció') }}</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('apis.index') }}"
                   class="nav-link {{ Request::is('apis*') ? 'active' : '' }}">
                    <i class="fas fa-exchange-alt"></i>
                    <p>Api</p>
                </a>
            </li>


            {{--            <li class="nav-item" id="xmlImport">--}}
{{--                <a href="{{ route('XMLImport') }}"--}}
{{--                   class="nav-link">--}}
{{--                    <i class="nav-icon fas fa-file-import"></i>--}}
{{--                    <p>{{ langClass::trans('XML Import') }}</p>--}}
{{--                </a>--}}
{{--            </li>--}}
        @endif
        <li class="nav-item">
            <a href="{{ route('languages.index') }}"
               class="nav-link {{ Request::is('languages*') ? 'active' : '' }}">
                <i class="fas fa-globe"></i>
                <p>{{ langClass::trans('Nyelvek') }}</p>
            </a>
        </li>
    @endif
@endif

{{--<li class="nav-item">--}}
{{--    <a href="{{ route('shoppingCartDetails.index') }}"--}}
{{--       class="nav-link {{ Request::is('shoppingCartDetails*') ? 'active' : '' }}">--}}
{{--        <p>Shopping Cart Details</p>--}}
{{--    </a>--}}
{{--</li>--}}






