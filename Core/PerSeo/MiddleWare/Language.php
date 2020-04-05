<?php

namespace PerSeo\MiddleWare;

class Language
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke(
        \Slim\Http\Request $request,
        \Slim\Http\Response $response,
        callable $next
    ) {
        $myreq = (substr($request->getUri()->getPath(), 0) == '/' ? $request->getUri()->getPath() : "/". $request->getUri()->getPath());
		$filteredreq = preg_replace('/[^a-zA-Z0-9-_\/]/', '#', $myreq);
		$regmatch = str_replace('/', '\/', ((substr($filteredreq, -1) == '/') ? substr_replace($filteredreq, '', -1) : $filteredreq)) . '[\/]?$';
        if ($this->container->has('db')) {
            $db = $this->container->get('db');
            $result = $db->select('routes', [
				'request',
                'dest',
                'type',
                'redirect',
            ], [
				"OR" => [
					'request' => $myreq,
					'dest[REGEXP]' => $regmatch
				]
            ]);
        }
        if (!empty($result[0]['dest'])) {
			$thisreq = $result[0]['request'];
            $type = $result[0]['type'];
            $dest = $result[0]['dest'];
			$destalt = $result[0]['dest'] .'/';
            $redirect = (int) $result[0]['redirect'];
			if (($myreq != $thisreq) && ($type == 1)) {
				$uriBase = '//'.$_SERVER['HTTP_HOST'].$request->getUri()->getBasePath();
				return $response->withRedirect($uriBase.$thisreq, $redirect);
			}
            if (isset($_COOKIE['lang'])) {
                $mylang = strtolower($_COOKIE['lang']);
            } else {
                if (!empty(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)))) {
                    $mylang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
                } else {
                    $mylang = $this->container->get('settings.global')['language'];
                }
            }			
			if (preg_match('/^\/[A-Za-z]{2}\//', $dest, $matches)) {
					$mylang = preg_replace("/[^A-Za-z]/", "", $matches[0]);
					if ($type == 1) { $dest = substr($dest, 3); }
			}
			$this->container->set('current.language', $mylang);
            if ($type == 1) {
                $basepath = $request->getUri()->getbasePath();
                $request = $request->withUri($request->getUri()->withPath($dest));
                $request = $request->withUri($request->getUri()->withbasePath($basepath));
            } else {
                $uriBase = '//'.$_SERVER['HTTP_HOST'].$request->getUri()->getBasePath();
                return $response->withRedirect($uriBase.$dest, $redirect);
            }
        }
		else {
			if ($this->container->has('settings.global') && ($this->container->get('settings.global')['locale'])) {
				$languages = $this->container->get('settings.global')['languages'];
				if (isset($_COOKIE['lang']) && in_array(strtolower($_COOKIE['lang']), $languages)) {
					$currlang = strtolower($_COOKIE['lang']);
				} else {
					if (in_array(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)), $languages)) {
						$currlang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
					} else {
						$currlang = $this->container->get('settings.global')['language'];
					}
				}
				$req = ((strlen($request->getUri()->getPath()) > 1) && (substr($request->getUri()->getPath(), 0,
						1) == '/') ? substr($request->getUri()->getPath(), 1) : $request->getUri()->getPath());
				$basepath = $request->getUri()->getbasePath();
				$langurl = explode('/', $req);
				if (empty($result[0]['dest']) && ($request->isGet()) && ($req != '/') && ($langurl[0] != 'admin')) {
					if (!empty($langurl[0]) && (in_array($langurl[0], $languages))) {
						$currlang = $langurl[0];
						$this->container->set('redirect.url', $request->getUri()->getBasePath().'/'.$currlang);
						$finalstring = substr($req, strlen($currlang));
						$request = $request->withUri($request->getUri()->withPath($finalstring));
						$request = $request->withUri($request->getUri()->withbasePath($basepath));
					} else {
						$this->container->set('current.language', $currlang);

						throw new \Slim\Exception\NotFoundException($request, $response);
					}
				}
				if ($this->container->get('settings.global')['locale']) {
					$this->container->set('redirect.url', $request->getUri()->getBasePath().'/'.$currlang);
				} else {
					$this->container->set('redirect.url', $request->getUri()->getBasePath());
				}
				$this->container->set('current.language', $currlang);
			} else {
				$this->container->set('redirect.url', $request->getUri()->getBasePath());
				if (isset($_COOKIE['lang'])) {
					$currlang = strtolower($_COOKIE['lang']);
				} else {
					if (!empty(strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)))) {
						$currlang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
					} else {
						$currlang = 'en';
					}
				}
				$this->container->set('current.language', $currlang);
			}
		}

        return $next($request, $response);
    }
}
