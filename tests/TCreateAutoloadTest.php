<?php
/**
 * SysGen - System Generator with Formdin Framework
 * Download Formdin Framework: https://github.com/bjverde/formDin
 *
 * @author  Bjverde <bjverde@yahoo.com.br>
 * @license https://github.com/bjverde/sysgen/blob/master/LICENSE GPL-3.0
 * @link    https://github.com/bjverde/sysgen
 *
 * PHP Version 5.6
 */

$path =  __DIR__.'/../';
require_once $path.'includes/constantes.php';
require_once $path.'classes/autoload_sysgen.php';

use PHPUnit\Framework\TestCase;

define('TEOL',"\n");
define('TTAB',chr(9));
/**
 * TDAOCreate test case.
 */
class TCreateAutoloadTest extends TestCase
{

    /**
     * @var TCreateAutoload
     */
	private $testClass;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(){
    	$_SESSION[APLICATIVO]['GEN_SYSTEM_ACRONYM']='test';
        parent::setUp();
        $this->testClass = new TCreateAutoload();
        
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(){
    	$this->testClass = null;        
        parent::tearDown();
    }
    
    public function testShow_numLines(){
        $esperado = 18;

        $resultArray = $this->testClass->show(false);
        $size = count($resultArray);
        $this->assertEquals( $esperado, $size);
    }
}