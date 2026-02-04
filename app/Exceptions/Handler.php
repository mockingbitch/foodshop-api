<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Context thêm vào log khi report exception (production: url, method, user_id).
     */
    protected function context(): array
    {
        $ctx = array_filter([
            'user_id' => Auth::id(),
        ]);

        if (app()->runningInConsole()) {
            $ctx['context'] = 'console';
            return $ctx;
        }

        $req = request();
        if ($req) {
            $ctx['url'] = $req->fullUrl();
            $ctx['method'] = $req->method();
        }

        return $ctx;
    }
}
