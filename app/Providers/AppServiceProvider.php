<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Users;
use App\Observers\UsersObserver;
use App\Models\ShoppingCart;
use App\Observers\ShoppingCartObserver;
use App\Models\ShoppingCartDetail;
use App\Observers\ShoppingCartDetailObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        config(['user_id' => 0,
                'customer_id' => 0,
                'customer_name' => '',
                'user_picture' => '',
                'user_rendszergazda' => 0,
                'noAviablePicture' => null,
                'excelCode' => 0,
                'excelQuantity' => 0
            ]);

        Users::observe(UsersObserver::class);
        ShoppingCart::observe(ShoppingCartObserver::class);
        ShoppingCartDetail::observe(ShoppingCartDetailObserver::class);

    }
}
