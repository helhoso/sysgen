<?php
/**
 * SysGen - System Generator with Formdin Framework
 * https://github.com/bjverde/sysgen
 */

if(!defined('EOL')){ define('EOL',"\n"); }
if(!defined('TAB')){ define('TAB',chr(9)); }
if(!defined('DS')){ define('DS',DIRECTORY_SEPARATOR); }
class TCreateMenu extends  TCreateFileContent{
	private $listTableNames;

	public function __construct(){
	    $this->setFileName('menu.php');
	    $path = TGeneratorHelper::getPathNewSystem().DS.'includes'.DS;
	    $this->setFilePath($path);
	}
	//--------------------------------------------------------------------------------------
	public function setListTableNames($listTableNames) {
		$this->validateListTableNames($listTableNames);
		$this->listTableNames = $listTableNames;
	}
	public function getListTableNames() {
		return $this->listTableNames;
	}
	//--------------------------------------------------------------------------------------
	private function validateListTableNames($listTableNames) {
		$listTableNames = empty($listTableNames)?$this->getListTableNames():$listTableNames;
		if(empty($listTableNames)){
			throw new InvalidArgumentException('List of Tables Names is empty');
		}
		if(!is_array($listTableNames)){
			throw new InvalidArgumentException('List of Tables Names not is array');
		}
	}
	//--------------------------------------------------------------------------------------
	public function addBasicMenuItems() {
		$this->validateListTableNames(null);
		$listTableNames = $this->listTableNames['TABLE_NAME'];
		foreach($listTableNames as $key=>$table){
			$tableType = strtoupper($this->listTableNames['TABLE_TYPE'][$key]);
			if($tableType == 'TABLE'){
			    $this->addLine('$menu->add(\'1.'.$key.'\',1,\''.strtolower($table).'\',\'modulos/'.strtolower($table).'.php\');');
			}
		}
	}
	//--------------------------------------------------------------------------------------
	public function show($print=false) {
		$this->lines=null;
        $this->addLine('<?php');
        $this->addSysGenHeaderNote();
        $this->addBlankLine();
        $this->addLine('$menu = new TMenuDhtmlx();');
        $this->addLine('$menu->add(\'1\', null, \'Menu\', null, null, \'menu-alt-512.png\');');
        $this->addBasicMenuItems();
        $this->addLine('$menu->add(\'9\', null, \'Sobre\', \'modulos/sys_about.php\', null, \'information-circle.jpg\');');
        $this->addBlankLine();
        $this->addLine('$menu->add(\'10\',null,\'Config Ambiente\',null,null,\'setting-gear-512.png\');');
        $this->addLine('$menu->add(\'10.1\',\'10\',\'Ambiente Resumido\',\'modulos/sys_environment_summary.php\',null,\'information-circle.jpg\');');
        $this->addLine('$menu->add(\'10.2\',\'10\',\'PHPInfo\',\'modulos/sys_environment.php\',null,\'php_logo.png\');');
        $this->addLine('$menu->add(\'10.4\',\'10\',\'Gerador VO/DAO\',\'../base/includes/gerador_vo_dao.php\');');
        $this->addLine('$menu->add(\'10.5\',\'10\',\'Gerador Form VO/DAO\',\'../base/includes/gerador_form_vo_dao.php\',null,\'smiley-1-512.png\');');
        $this->addLine('$menu->getXml();');
        $this->addLine('?>');
        if( $print){
        	echo $this->getLinesString();
		}else{
			return $this->getLinesString();
		}
	}
}
?>