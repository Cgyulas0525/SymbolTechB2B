<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Users;
use App\Observers\UsersObserver;

use Illuminate\Foundation\AliasLoader;
use App\Classes\langClass;
use App\Classes\logClass;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('langClass', langClass::class);
            $loader->alias('logClass', logClass::class);
        });
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


        /* hogy lehessen forcing https */
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Users::observe(UsersObserver::class);
    }
}
