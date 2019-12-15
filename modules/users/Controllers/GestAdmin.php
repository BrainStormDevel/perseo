<?php

namespace users\Controllers;

class GestAdmin
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function AddNew()
    {
        try {
            $id = $this->container->get('Sanitizer')->POST('form_id', 'int');
            $user = $this->container->get('Sanitizer')->POST('form_user', 'user');
            $pass = $this->container->get('Sanitizer')->POST('form_pass', 'pass');
            $email = $this->container->get('Sanitizer')->POST('form_email', 'email');
            $type = $this->container->get('Sanitizer')->POST('form_type', 'int');
            if (!empty($user) && !empty($pass) && !empty($email) && !empty($type)) {
                if (\login\Controllers\Login::priv() > $type) {
                    throw new \Exception('Non hai i privilegi per poter aggiungere questo utente', 200);
                }
                $db = $this->container->get('db');
                $login = new \login\Controllers\Login($this->container, 'admins');
                if (empty($id)) {
                    $db->insert("admins", [
                        "user" => $user,
                        "pass" => $login->create_hash($pass),
                        "email" => $email,
                        "superuser" => '',
                        "type" => $type,
                        "stato" => '0'
                    ]);
                    $lastid = $db->id();
                    if ($lastid <= 0) {
                        throw new \Exception('Errore utente o email già presente', 200);
                    }
                    $result = Array(
                        "err" => 0,
                        "id" => $lastid,
                        "code" => '000',
                        "message" => 'OK'
                    );
                } else {
                    if (\login\Controllers\Login::id() == $id) {
                        $data = $db->update("admins", [
                            "user" => $user,
                            "pass" => $login->create_hash($pass),
                            "email" => $email,
                            "type" => $type,
                        ], [
                            "id" => $id
                        ]);
                    } else {
                        $data = $db->update("admins", [
                            "user" => $user,
                            "pass" => $login->create_hash($pass),
                            "email" => $email,
                            "type" => $type,
                        ], [
                            "id" => $id,
                            "superuser" => ''
                        ]);
                    }
                    if ($data->rowCount() <= 0) {
                        throw new \Exception('Errore aggiornamento utente', 200);
                    } else {
                        $result = Array(
                            "err" => 0,
                            "id" => $id,
                            "code" => '000',
                            "message" => 'OK'
                        );
                    }
                }
            }
			return $result;
        } catch (\Throwable $e) {
			throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    public function Del()
    {
        try {
            $id = $this->container->get('Sanitizer')->POST('form_id', 'int');
            if (!empty($id)) {
                if (\login\Controllers\Login::id() == $id) {
                    throw new \Exception('Non puoi eliminare il tuo utente', 200);
                }
                if (\login\Controllers\Login::priv() > 1) {
                    throw new \Exception('Non hai i permessi per eliminare questo utente', 200);
                }
                $db = $this->container->get('db');
                $data = $db->delete("admins", [
                    "AND" => [
                        "id" => $id,
                        "superuser" => ''
                    ]
                ]);
                if ($data->rowCount() <= 0) {
                    throw new \Exception('Errore Eliminazione utente', 200);
                }
                $result = Array(
                    "err" => 0,
                    "id" => $id,
                    "code" => '000',
                    "message" => 'OK'
                );
            }
			return $result;
        } catch (\Throwable $e) {
			throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}