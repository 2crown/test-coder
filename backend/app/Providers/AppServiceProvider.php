<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Date;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Custom JSON response macros
        Response::macro('success', function ($data = null, $message = 'Success', $code = 200) {
            return Response::json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $code);
        });

        Response::macro('error', function ($message = 'Error', $errors = null, $code = 422) {
            return Response::json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        });

        Response::macro('created', function ($data = null, $message = 'Created successfully') {
            return Response::json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], 201);
        });

        Response::macro('deleted', function ($message = 'Deleted successfully') {
            return Response::json([
                'success' => true,
                'message' => $message,
            ], 200);
        });
    }
}
