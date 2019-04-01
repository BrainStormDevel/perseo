<?php

namespace PerSeo;

class Request
{
	public static function GET($var = NULL, $type = NULL)
	{
		if (isset($_GET[$var])) {
			if (!is_array($_GET[$var])) {
				switch($type) {
				case 'htm':
				return \PerSeo\Sanitize::no_xss($_GET[$var]);
				break;
				case 'int':
				return intval($_GET[$var]);
				break;
				case 'user':
				return \PerSeo\Sanitize::user($_GET[$var]);
				break;
				case 'pass':
				return \PerSeo\Sanitize::pwd($_GET[$var]);
				break;
				case 'alpha':
				return \PerSeo\Sanitize::alpha($_GET[$var]);
				break;				
				case 'seo':
				return \PerSeo\Sanitize::to_url($_GET[$var]);
				break;
				case 'email':
				return \PerSeo\Sanitize::email($_GET[$var]);
				break;
				case 'url':
				return filter_var($_GET[$var], FILTER_SANITIZE_URL);
				break;
				default:
				return \PerSeo\Sanitize::no_html($_GET[$var]);
				break;	
				}
			}
			else { die('PerSeo ERROR : type confusion detected'); }
		}
	}
	public static function POST($var = NULL, $type = NULL)
	{
		if (isset($_POST[$var])) {
			if (!is_array($_POST[$var])) {
				switch($type) {
				case 'htm':
				return \PerSeo\Sanitize::no_xss($_POST[$var]);
				break;
				case 'int':
				return intval($_POST[$var]);
				break;
				case 'user':
				return \PerSeo\Sanitize::user($_POST[$var]);
				break;
				case 'pass':
				return \PerSeo\Sanitize::pwd($_POST[$var]);
				break;
				case 'alpha':
				return \PerSeo\Sanitize::alpha($_POST[$var]);
				break;				
				case 'seo':
				return \PerSeo\Sanitize::to_url($_POST[$var]);
				break;
				case 'email':
				return \PerSeo\Sanitize::email($_POST[$var]);
				break;
				case 'url':
				return filter_var($_POST[$var], FILTER_SANITIZE_URL);
				break;
				default:
				return \PerSeo\Sanitize::no_html($_POST[$var]);
				break;	
				}
			}
			else { die('PerSeo ERROR : abnormal operation'); }
		}
	}
	public static function checkpost() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$secure = new \PerSeo\Secure;
			$token = $secure->find_first($_POST);
			if (!empty($token)) {
				$result = $secure->validate_token($token);
				if (!$result) {
					throw new \Exception('PerSeo ERROR : wrong token');
				}
			}
			else { throw new \Exception('PerSeo ERROR : empty token'); }
		}	
	}
}