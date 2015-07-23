<?php
/**
 * Created by PhpStorm.
 * User: chpyr
 * Date: 28/10/14
 * Time: 12:42
 */

namespace Cpyree\SynologyBundle\Tests\Services;

use Cpyree\SynologyBundle\Services\SynologySession;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SynologySessionTest extends KernelTestCase{
	/**
	 * @var EntityManager
	 */
	private $em;

	/** @var SynologySession $syno */
	private $syno;

	public function setUp(){
		self::bootKernel();
		$this->syno = static::$kernel->getContainer()->get('cpyree_synology.session');
		$this->syno->login();
	}

	public function testLogin(){
		$this->assertEquals(true, $this->syno->login());
	}
	public function testSend(){
		$infoRequestResult = $this->syno->send(
			"query.cgi",
			array(
				'api'       =>  'SYNO.API.Info',
				'version'   =>  '1',
				'method'    =>  'query',
				'query'     =>  'all',
			));
		$this->assertEquals('Cpyree\SynologyBundle\Webapi\WebApiResponse',get_class($infoRequestResult));
		$this->assertEquals(true,is_array($infoRequestResult->getData()));
		$data = $infoRequestResult->getData();
		$this->assertEquals(true,!empty($data));

	}
} 