<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable, Exception;
use App\Traits\SendResponseTrait;
use Illuminate\Auth\AuthenticationException;



class Handler extends ExceptionHandler
{
    use SendResponseTrait;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $rendered = parent::render($request, $exception);

        if( !$request->is('api/*') )/* For browser errors */
            return $rendered;


        if($exception instanceof AuthenticationException){
            return $this->apiResponse('false', 403, 'User is not logged in.',null, false);
        }

        if($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException){
            return $this->apiResponse('error', 403, 'You do not have the required authorization.');
        }

        if($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
            return $this->apiResponse('error', $rendered->getStatusCode(), 'Method is not allowed');
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->apiResponse('error', $rendered->getStatusCode(), 'Try to access undefined api');
        }

        if ($exception instanceof UnauthorizedException) {
            return $this->apiResponse('error', 403, 'You do not have the required authorization.');
        }

        if ($this->isHttpException($exception) && $exception->getStatusCode() == 404) {
            return $this->apiResponse('error', 404, 'Try to access undefined api');
        }

        if ($exception instanceof TokenMismatchException && Auth::guest()) {
            return $this->apiResponse('error', 500, $exception->getMessage());
        }

        if ($exception instanceof TokenMismatchException && getenv('APP_ENV') != 'local') {
            return $this->apiResponse('error', 404, 'Try to access undefined api');
        }

        if($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException && getenv('APP_ENV') != 'local') {
            return $this->apiResponse('error', 404, $exception->getMessage());
        }

        if($exception instanceof \Illuminate\Database\QueryException ){
            return $this->apiResponse('error', 500, $exception->getMessage());
        }

        if(($exception instanceof PDOException || $exception instanceof QueryException) && getenv('APP_ENV') != 'local') {
            return $this->apiResponse('error', 500, $exception->getMessage());
        }

        if ($exception instanceof ClientException) {
            return $this->apiResponse('error', 500, $exception->getMessage());
        }

        if ($exception instanceof \Illuminate\Contracts\Container\BindingResolutionException) {
            return $this->apiResponse('error', 404, 'Try to access undefined api');
        }

        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->apiResponse('error', 404, 'Try to access undefined api');
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return $this->apiResponse('error', 404, 'Try to access undefined api');
        }

        /** Return default on form request validation **/
        if( $exception instanceof  \Illuminate\Validation\ValidationException ){

            return $rendered;
        }

        return $this->apiResponse('error', 400, "Something went wrong." . $exception->getMessage() );
        return $rendered;

    }

}
