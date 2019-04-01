<?php

namespace Utenti\Models;

class Users
{
	public static function list_admins()
    {
		$db = new \PerSeo\DB;
		$result = $db->select('admins', [
			'id', 
			'user',
			'email',
			'privilegi'
		]);
		return $result;
    }
	public static function add_user(string $table, int $id, string $username, string $password, string $email, int $priv)
    {
		switch ($table) {
			case "admins":
			$mypriv = \PerSeo\Login::privileges();
			$superuser = \PerSeo\Login::superuser();
			if ($id == \PerSeo\Login::id()) { $priv = $mypriv; }
			if ($priv < $mypriv) {
				$msg = array(
					'id' => '0',
					'code' => '1',
					'msg' => 'NO_PRIV'
				);
				break;	
			}
			else {
				try {
				$db = new \PerSeo\DB;
				if ($id == 0) {
					$data = $db->insert("admins", [
						"user" => $username,
						"pass" => \PerSeo\Login::create_hash($password),
						"email" => $email,
						"privilegi" => $priv,
						"stato" => '0'
					]);
					$msg = array(
						'id' => $db->id(),
						'code' => '0',
						'msg' => 'OK'
					);
				}
				else {
					if($superuser) {
						$data = $db->update("admins", [
						"user" => $username,
						"pass" => \PerSeo\Login::create_hash($password),
						"email" => $email,
						"superuser" => \PerSeo\Login::encrypt($username, CRYPT_SALT),
						"privilegi" => $priv,
						"stato" => '0'
						], [
						"id" => $id
						]);
						$dbmsg = ($data->rowCount() > 0 ? 'OK' : 'NO');
						$msg = array(
							'id' => $id,
							'record' => $data->rowCount(),
							'code' => '0',
							'msg' => $dbmsg
						);
						
					}
					else {
						$data = $db->update("admins", [
						"user" => $username,
						"pass" => \PerSeo\Login::create_hash($password),
						"email" => $email,
						"privilegi" => $priv,
						"stato" => '0'
						], [
						"id" => $id,
						"superuser" => NULL
						]);
						$dbmsg = ($data->rowCount() > 0 ? 'OK' : 'NO');
						$msg = array(
							'id' => $id,
							'record' => $data->rowCount(),
							'code' => '0',
							'msg' => $dbmsg
						);						
					}
				}
				}
				catch (Exception $e) {
						$msg = array(
						'id' => '0',
						'code' => $e->getCode(),
						'msg' => $e->getMessage()
						);
				}
			}
			break;
		}
		$CSRFToken = \PerSeo\Secure::generate_token('Admin');
		$token = array(
			'AdminsCSRFname' =>  $CSRFToken['name'],
			'AdminsCSRFToken' =>  $CSRFToken['value']
		);
		$result = array_merge($msg, $token);
		return json_encode($result);
    }
	public static function del_user(string $table, int $id)
    {
		switch ($table) {
			case "admins":
			$mypriv = \PerSeo\Login::privileges();
			if (\PerSeo\Login::id() == $id) {
				$msg = array(
					'record' => '0',
					'code' => '1',
					'msg' => 'NO_SAME_ID'
				);
				break;	
			}
			else {			
			try {
				$db = new \PerSeo\DB;
				$data = $db->delete("admins", [
					"AND" => [
					"id" => $id,
					"superuser" => NULL,
					"privilegi[>=]" => $mypriv
					]
				]);
				$dbmsg = ($data->rowCount() > 0 ? 'OK' : 'NO_PRIV');
				$msg = array(
					'record' => $data->rowCount(),
					'code' => '0',
					'msg' => $dbmsg
				);
			} catch (Exception $e) {
				$msg = array(
				'record' => '0',
				'code' => $e->getCode(),
				'msg' => $e->getMessage()
				);
			}
			}
			break;
		}
		$CSRFToken = \PerSeo\Secure::generate_token('Admin');
		$token = array(
			'AdminsCSRFname' =>  $CSRFToken['name'],
			'AdminsCSRFToken' =>  $CSRFToken['value']
		);
		$result = array_merge($msg, $token);
		return json_encode($result);
    }	
}