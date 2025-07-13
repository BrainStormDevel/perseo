<?php
declare(strict_types=1);

namespace PerSeo\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use PerSeo\Handlers\CookieResponse;

class CookieFactory implements ResponseFactoryInterface {
    public function createResponse(int $code = 200, string $reasonPhrase = '') : Response {
        return (new CookieResponse())->withStatus($code, $reasonPhrase);
    }
}
