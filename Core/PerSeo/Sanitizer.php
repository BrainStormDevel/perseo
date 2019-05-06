<?php

namespace PerSeo;

class Sanitizer
{	
	private $GET;
	
	private $POST;
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $requestInterface
     * @param \Psr\Http\Message\ResponseInterface      $response
     * @param callable                                 $next
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(
        \Psr\Http\Message\ServerRequestInterface $requestInterface,
        \Psr\Http\Message\ResponseInterface $response,
        callable $next) {
			$this->GET = $requestInterface->getQueryParams();
			$this->POST = $requestInterface->getParsedBody();
        return $next($requestInterface, $response);
    }
	public function GET($var = NULL, $type = NULL)
	{
		if (isset($this->GET[$var])) {
			if (!is_array($this->GET[$var])) {
				switch($type) {
				case 'htm':
				return \PerSeo\Sanitize::no_xss($this->GET[$var]);
				break;
				case 'int':
				return intval($this->GET[$var]);
				break;
				case 'user':
				return \PerSeo\Sanitize::user($this->GET[$var]);
				break;
				case 'pass':
				return \PerSeo\Sanitize::pwd($this->GET[$var]);
				break;
				case 'alpha':
				return \PerSeo\Sanitize::alpha($this->GET[$var]);
				break;				
				case 'seo':
				return \PerSeo\Sanitize::to_url($this->GET[$var]);
				break;
				case 'email':
				return \PerSeo\Sanitize::email($this->GET[$var]);
				break;
				case 'url':
				return filter_var($this->GET[$var], FILTER_SANITIZE_URL);
				break;
				default:
				return \PerSeo\Sanitize::no_html($this->GET[$var]);
				break;	
				}
			}
		}
	}
	public function POST($var = NULL, $type = NULL)
	{
		if (isset($this->POST[$var])) {
			if (!is_array($this->POST[$var])) {
				switch($type) {
				case 'htm':
				return \PerSeo\Sanitize::no_xss($this->POST[$var]);
				break;
				case 'int':
				return intval($this->POST[$var]);
				break;
				case 'user':
				return \PerSeo\Sanitize::user($this->POST[$var]);
				break;
				case 'pass':
				return \PerSeo\Sanitize::pwd($this->POST[$var]);
				break;
				case 'alpha':
				return \PerSeo\Sanitize::alpha($this->POST[$var]);
				break;				
				case 'seo':
				return \PerSeo\Sanitize::to_url($this->POST[$var]);
				break;
				case 'email':
				return \PerSeo\Sanitize::email($this->POST[$var]);
				break;
				case 'url':
				return filter_var($this->POST[$var], FILTER_SANITIZE_URL);
				break;
				default:
				return \PerSeo\Sanitize::no_html($this->POST[$var]);
				break;	
				}
			}
		}
	}	
}