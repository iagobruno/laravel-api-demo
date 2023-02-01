<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

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
        Response::macro('success', function (string $message, array $data = []) {
            return response()->json([
                'message' => $message,
                ...$data,
            ]);
        });

        Response::macro('error', function ($message, $status = 500) {
            return response()->json([
                'message' => $message,
            ], $status);
        });
    }
}
