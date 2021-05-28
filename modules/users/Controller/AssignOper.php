<?php

namespace Modules\users\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Odan\Session\SessionInterface;
use PerSeo\DB;

final class AssignOper
{
    protected $db;
	protected $session;

    public function __construct(ContainerInterface $container, SessionInterface $session)
    {
		$this->session = $session;
        $this->db = ($container->has('db') ? $container->get('db') : null);
    }
	
    public function __invoke(Request $request, Response $response): Response {
		$post = $request->getParsedBody();
		$id = (int) (!empty($post['id']) ? $post['id'] : '');
		$mvserial = (string) (!empty($post['mvserial']) ? $post['mvserial'] : '');
		$this->db->query("INSERT INTO <LZ_MAST> (MVSERIAL, MVNUMDOC, MVDATDOC, MVCODCON, ANDESCRI, OPERATORE, INIZIOEVAS, MVNUMREG, STATO) SELECT <view_doc_mast.MVSERIAL>, <view_doc_mast.MVNUMDOC>, <view_doc_mast.MVDATDOC>, <view_doc_mast.MVCODCON>, <view_doc_mast.ANDESCRI>, :idop AS Espr1, NOW() AS Espr2, <view_doc_mast.MVNUMREG>, 2 AS Espr22 FROM <view_doc_mast> WHERE <view_doc_mast.MVSERIAL> = :mvserial", [
			":idop" => $id,
			":mvserial" => $mvserial
		]);
		$error = $this->db->error();
		if (($error[1] != null) && ($error[2] != null)) {
			throw new \Exception($error[2], 1);
		}
		$this->db->query("INSERT INTO <LZ_DETT> (MVCODICE, MVSERIAL, CPROWNUM, MVCODART, MVDESART, MVUNIMIS, MVQTAUM1, MVDATEVA, MVFLOMAG) SELECT Trim(MVCODICE) AS Espr1, Trim(MVSERIAL) AS Espr2, <doc_dett.CPROWNUM>, Trim(MVCODICE) AS Espr3, <doc_dett.MVDESART>, <doc_dett.MVUNIMIS>, <doc_dett.MVQTAMOV>, <doc_dett.MVDATEVA>, <doc_dett.MVFLOMAG> FROM <doc_dett> WHERE (((Trim(MVSERIAL))=:mvserial) ) order by cprownum", [
			":mvserial" => $mvserial
		]);
		$error = $this->db->error();
		if (($error[1] != null) && ($error[2] != null)) {
			throw new \Exception($error[2], 1);
		}
		$result = array(
			'code' => '0',
			'msg' => 'Ordine assegnato correttamente a Operatore'
        );	
		//var_dump($this->db->last());
		$response->getBody()->write(json_encode($result));
		return $response;
    }
}