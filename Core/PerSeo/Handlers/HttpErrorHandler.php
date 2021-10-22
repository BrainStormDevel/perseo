<?php
declare(strict_types=1);

namespace PerSeo\Handlers;

//use App\Application\Actions\ActionError;
//use App\Application\Actions\ActionPayload;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    /**
     * @inheritdoc
     */
         
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $type = self::SERVER_ERROR;
        $description = 'An internal error has occurred while processing your request.';

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $description = $exception->getMessage();

            if ($exception instanceof HttpNotFoundException) {
                $statusCode = 404;
                $type = self::RESOURCE_NOT_FOUND;
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $statusCode = 405;
                $type = self::NOT_ALLOWED;
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $statusCode = 401;
                $type = self::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpForbiddenException) {
                $statusCode = 403;
                $type = self::UNAUTHENTICATED;
            } elseif ($exception instanceof HttpBadRequestException) {
                $statusCode = 400;
                $type = self::BAD_REQUEST;
            } elseif ($exception instanceof HttpNotImplementedException) {
                $statusCode = 501;
                $type = self::NOT_IMPLEMENTED;
            }
        }

        if (
            !($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $description = $exception->getMessage();
        }

        $error = [
            'statusCode' => $statusCode,
            'error' => [
                'type' => $type,
                'description' => $description,
            ],
        ];
        $encodedPayload = json_encode($error, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }
}
