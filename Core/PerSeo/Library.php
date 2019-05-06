<?php

namespace PerSeo;

class Library
{
	public static function css($name, $file, $version = NULL, $type = NULL) {
		switch($type) {
			case 'maxcdn':
			return '<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/'.$name.'/'.$version.'/'.$file.'.css" crossorigin="anonymous">';
			break;			
			default:
			return '<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/'.$name.'/'.$version.'/'.$file.'.css" crossorigin="anonymous">';
			break;	
		}		
	}
	public static function js($name, $file, $version = NULL, $type = NULL) {
		switch($type) {
			case 'maxcdn':
			return '<script src="https://maxcdn.bootstrapcdn.com/'.$name.'/'.$version.'/'.$file.'.js" crossorigin="anonymous"></script>';
			break;			
			default:
			return '<script src="//cdnjs.cloudflare.com/ajax/libs/'.$name.'/'.$version.'/'.$file.'.js" crossorigin="anonymous"></script>';
			break;	
		}		
	}	
	public static function jquery($type, $name, $version = NULL) {
		switch($type) {
			default:
			return '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/'.$version.'/'.$name.'.js"></script>';
			break;	
		}		
	}
	public static function bootstrap($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/'.$version.'/css/'.$name.'.css" rel="stylesheet" crossorigin="anonymous">';
			break;
			case 'js':
			return '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/'.$version.'/js/'.$name.'.js" crossorigin="anonymous"></script>';
			break;
		}
	}
	public static function FontAwesome($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link href="https://maxcdn.bootstrapcdn.com/font-awesome/'.$version.'/css/'.$name.'.css" rel="stylesheet" crossorigin="anonymous">';
			break;
		}
	}
	public static function Ionicons($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/'.$version.'/css/'.$name.'.css" />';
			break;
		}
	}
	public static function AdminLTE($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/'.$version.'/css/'.$name.'.css" />';
			break;
		}
	}
	public static function iCheck($type, $name, $version = NULL) {
		switch($type) {
			case 'css-skins-square':
			return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/iCheck/'.$version.'/skins/square/'.$name.'.css" />';
			break;
			case 'js':
			return '<script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/'.$version.'/'.$name.'.js"></script>';
			break;
		}
	}
	public static function Morrisjs($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/'.$version.'/'.$name.'.css" />';
			break;
			case 'js':
			return '<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/'.$version.'/'.$name.'.js"></script>';
			break;
		}
	}
	public static function jvectormap($type, $name, $version = NULL) {
		switch($type) {
			case 'css':
			return '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jvectormap/'.$version.'/'.$name.'.css" />';
			break;
			case 'js':
			return '<script src="https://cdnjs.cloudflare.com/ajax/libs/jvectormap/'.$version.'/'.$name.'.js"></script>';
			break;
		}
	}	
}