<?php

namespace Modules\login\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Odan\Session\SessionInterface;

final class Login
{
    protected $type;
    protected $db;
    protected $cookie;
	protected $container;
	protected $session;

    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
		//$this->container = $container;
		$this->session = $session;
		//$settings = ($container->has('settings') ? $container->get('settings') : null);
        $this->db = ($container->has('db') ? $container->get('db') : null);
        //$this->cookie = $settings['cookie'];
    }
	
    public function __invoke(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$username = (string) (!empty($post['username']) ? $post['username'] : '');
		$password = (string) (!empty($post['password']) ? $post['password'] : '');
		$type = (string) (!empty($post['type']) ? $post['type'] : '');
		$this->type = (($type == 'admin') ? 'admins' : 'users');
		$result = $this->check($username, $password);
		$response->getBody()->write($result);
		return $response;
    }

    protected function check($user, $pass)
    {
        try {
            /*if ($this->islogged()) {
                throw new \Exception("OK", 0);
            }*/
            if (!$user or !$pass) {
                throw new \Exception("USR_PASS_EMPTY", 1);
            }
            $result = $this->db->select($this->type, [
                'id',
				'nome',
				'cognome',
                'user',
                'password',
                'ruolo'
            ], [
                'user' => $user
            ]);
			if (empty($result)) {
                throw new \Exception("USR_PASS_ERR", 1);
            }
            $error = $this->db->error();
            if (($error[1] != null) && ($error[2] != null)) {
                throw new \Exception($error[2], 1);
            }
            if (password_verify($pass, $result[0]['password'])) {
				$this->session->set('logged', true);
				$this->session->set('type', (string) $this->type);
				$this->session->set('uid', (int) $result[0]['id']);
				$this->session->set('nome', (string) $result[0]['nome']);
				$this->session->set('cognome', (string) $result[0]['cognome']);
				$this->session->set('user', (string) $result[0]['user']);
				$this->session->set('ruolo', (int) $result[0]['ruolo']);
                $result = array(
                    'code' => '0',
                    'msg' => 'OK'
                );
            } else {
                throw new \Exception("USR_PASS_ERR", 1);
            }
        } catch (\Exception $e) {
            $result = array(
                'code' => $e->getCode(),
                'msg' => $e->getMessage()
            );
        }
        return json_encode($result);
    }
}