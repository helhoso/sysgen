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

class TCreateAutoload extends TCreateFileContent
{

    private $gen_system_acronym;
    
    public function __construct()
    {
        $this->gen_system_acronym = $_SESSION[APLICATIVO]['GEN_SYSTEM_ACRONYM'];
        $this->setFileName('autoload_'.$this->gen_system_acronym.'.php');
        $path = TGeneratorHelper::getPathNewSystem().DS.'classes'.DS;
        $this->setFilePath($path);
    }
    //--------------------------------------------------------------------------------------
    public function show($print = false)
    {
        $autoloadName = $this->gen_system_acronym.'_autoload';
        $this->lines=null;
        $this->addLine('<?php');
        $this->addSysGenHeaderNote();
        $this->addBlankLine();
        $this->addLine('if ( !function_exists( \''.$autoloadName.'\') ) {');
        $this->addLine(ESP.'function '.$autoloadName.'( $class_name )	{');
        $this->addLine(ESP.ESP.'require_once $class_name . \'.class.php\';');
        $this->addLine(ESP.'}');
        $this->addLine('spl_autoload_register(\''.$autoloadName.'\');');
        $this->addLine('}');
        if ($print) {
            echo $this->getLinesString();
        } else {
            return $this->getLinesString();
        }
    }
}
