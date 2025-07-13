<?php
declare(strict_types=1);

namespace PerSeo\Handlers;

use Slim\Psr7\Response as BaseResponse;
use Slim\Psr7\Cookies;

class CookieResponse extends BaseResponse {
    public function withCookies(Cookies $cookies) : self {
        return $this->withHeader('Set-Cookie', $cookies->toHeaders());
    }
}
