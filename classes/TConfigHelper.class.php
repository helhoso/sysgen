<?php
/**
 * SysGen - Gerador de sistemas com Formdin Framework
 * https://github.com/bjverde/sysgen
 */
class TConfigHelper {
	
	public static function phpVersionValid($html){
		$texto = '<b>Versão do PHP</b>: ';
		if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
			$texto =  $texto.'<span class="success">'.phpversion().'</span><br>';
			$html->add($texto);
			$result = true;
		}else if(version_compare(PHP_VERSION, '5.4.0') >= 0){
			$texto =  $texto.'<span class="failure">'.phpversion().' </span><br>';
			$texto =  $texto.'<span class="alert">Para um melhor desempenho atualize seu servidor para PHP 7.0.0 ou seperior </span><br>';
			$html->add($texto);
			$result = true;
		}else{
			$texto =  $texto.'<span class="failure">'.phpversion().' atualize seu sistema para o PHP 5.4.0 ou seperior </span><br>';
			$texto =  $texto.'<br><br><span class="alert">O FormDin precisa de uma versão mais atual do PHP</span><br>';
			$html->add($texto);
			$result = false;
		}
		return $result;
	}
	
	public static function testar($extensao=null,$html){
		if( extension_loaded($extensao) )	{
			$html->add('<b>'.$extensao.'</b>: <span class="success">Instalada.</span><br>');
			$result = true;
		}else {
			$html->add('<b>'.$extensao.'</b>: <span class="failure">Não instalada</span><br>');
			$result = false;
		}
		return $result;
	}
	
	public static function validatePDO($DBMS,$html){		
		$result = false;
		if( self::testar('PDO',$html) )	{
			$result = true;
		}
		
		if($result == false){
			$texto ='<span class="alert">Instale a extensão PDO. DEPOIS tente novamente</span><br>';
			$texto = $texto.'(PHP Data Objects) é uma extensão que fornece uma interface padronizada para trabalhar com diversos bancos<br>';
			$html->add($texto);
		}
		return $result;
	}
	
	public static function validateDBMS($DBMS,$html){
		// https://secure.php.net/manual/pt_BR/pdo.drivers.php
		$result = false;
		if( $DBMS == DBMS_MYSQL )	{
			if ( self::testar('PDO_MYSQL',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_SQLITE){
			if ( self::testar('PDO_SQLITE',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_SQLSERVER){
			if ( self::testar('PDO_SQLSRV',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_ACCESS){
			if ( self::testar('PDO_ODBC',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_FIREBIRD){
			if ( self::testar('PDO_FIREBIRD',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_ORACLE){
			if ( self::testar('PDO_OCI',$html)) {
				$result = true;
			}
		}else if( $DBMS == DBMS_POSTGRES){
			if ( self::testar('PDO_PGSQL',$html)) {
				$result = true;
			}
		}
		
		if($result == false){
			$texto ='<br><span class="alert">Instale a extensão PDO para banco de dados: '.$DBMS.'.<br> DEPOIS tente novamente</span><br>';
			$html->add($texto);
		}
		
		return $result;
	}
	
	public static function validatePDOAndDBMS($DBMS,$html){
		if( self::validatePDO($DBMS, $html) && self::validateDBMS($DBMS, $html) )	{
			$result = true;
		}else{
			$result = false;
		}		
		return $result;
	}
	
	public static function showAbaDBMS($DBMS,$DBMSAba){
		if( $DBMS == $DBMSAba )	{
			$result = true;
		}else{
			$result = false;
		}
		return $result;
	}
	
	public static function showMsg($type,$msg){
		if( $type == 1 )	{
			$msg = '<span class="success">'.$msg.'</span><br>';
		}else if ($type == 0){
			$msg = '<span class="failure">'.$msg.'</span><br>';
		}else if($type == -1 ){
			$msg = '<span class="alert">'.$msg.'</span><br>';
		}else {
			$msg = $msg.'<br>';
		}
		return $msg;
	}
	
	public static function getTDAOConect($tableName){
		$dbType   = $_SESSION[APLICATIVO]['DBMS']['TYPE'];
		$user     = $_SESSION[APLICATIVO]['DBMS']['USER'];
		$password = $_SESSION[APLICATIVO]['DBMS']['PASSWORD'];
		$dataBase = $_SESSION[APLICATIVO]['DBMS']['DATABASE'];
		$host     = $_SESSION[APLICATIVO]['DBMS']['HOST'];
		$port     = $_SESSION[APLICATIVO]['DBMS']['PORT'];
		$schema   = $_SESSION[APLICATIVO]['DBMS']['SCHEMA'];
		
		$dao = new TDAO($tableName,$dbType,$user,$password,$dataBase,$host,$port,$schema);
		return $dao;
	}
	
	public static function loadTablesFromDatabase(){
		$dao = self::getTDAOConect(null);
		$dados = $dao->loadTablesFromDatabase();
		if(!is_array($dados)){
			throw new InvalidArgumentException('List of Tables Names not is array');
		}
		return $dados;
	}
	
	public static function loadFieldsFromDatabase(){
		$listTables = self::loadTablesFromDatabase();
		$listTableNames = $listTables['TABLE_NAME'];
		foreach ($listTableNames as $key=>$value){
			$dao = self::getTDAOConect($value);
			$dados = $dao->loadFieldsOneTableFromDatabase();
			d($dados);
		}
		//return $dados;
	}
	
	public static function createDaoVoFromTable($tableName, $listColumns){	
		$gerador = new TDAOCreate($frm->get('tabela'), $coluna_chave, $diretorio);
		foreach($listColumns as $k=>$v) {
			$gerador->addColumn($v);
		}
		$showSchema = $frm->get('sit_const_schema');
		$gerador->setShowSchema($showSchema);
		$gerador->setWithSqlPagination($TPGRID);
		$gerador->setDatabaseManagementSystem($TPBANCO);
		$gerador->saveVO();
		$gerador->saveDAO();
	}
	
}
?>