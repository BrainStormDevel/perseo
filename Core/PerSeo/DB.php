<?php

namespace PerSeo;

use Exception;
use Medoo\Medoo;

class DB extends Medoo
{
    private $err = Array();

    public function __construct(
        $DB = null,
        $DBNAME = null,
        $DBHOST = null,
        $DBUSER = null,
        $DBPASS = null,
        $DBPREFIX = null,
        $DBENCODING = "utf8"
    ) {
        try {
            parent::__construct([
                'database_type' => (is_null($DB) ? constant('DB') : $DB),
                'database_name' => (is_null($DBNAME) ? constant('DBNAME') : $DBNAME),
                'server' => (is_null($DBHOST) ? constant('DBHOST') : $DBHOST),
                'username' => (is_null($DBUSER) ? constant('DBUSER') : $DBUSER),
                'password' => (is_null($DBPASS) ? constant('DBPASS') : $DBPASS),
                'prefix' => (is_null($DBPREFIX) ? (defined('TBL_') ? constant('TBL_') : null) : $DBPREFIX),
                'charset' => (is_null($DBENCODING) ? constant('DBENCODING') : $DBENCODING)
            ]);
        } catch (Exception $e) {
            $this->err = Array(
                "err" => 1,
                "code" => $e->getCode(),
                "msg" => $e->getMessage()
            );
        }
    }

    public function err()
    {
        return $this->err;
    }
}