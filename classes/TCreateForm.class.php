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


if (!defined('EOL')) {
    define('EOL', "\n");
}
if (!defined('TAB')) {
    define('TAB', chr(9));
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class TCreateForm
{
    private $formTitle;
    private $formPath;
    private $formFileName;
    private $primaryKeyTable;
    private $tableRef;
    private $tableRefClass;
    private $tableRefDAO;
    private $tableRefVO;
    private $listColumnsName;
    private $lines;
    private $gridType;
    private $listColumnsProperties;
    
    const FORMDIN_TYPE_DATE = 'DATE';
    const FORMDIN_TYPE_TEXT = 'TEXT';
    const FORMDIN_TYPE_NUMBER = 'NUMBER';
    const FORMDIN_TYPE_COLUMN_NAME = 'FORMDIN_TYPE';

    public function __construct()
    {
        $this->setFormTitle(null);
        $this->setFormPath(null);
        $this->setFormFileName(null);
        $this->setPrimaryKeyTable(null);
        $this->setGridType(null);
    }
    //--------------------------------------------------------------------------------------
    public function setFormTitle($formTitle)
    {
        $formTitle = ( !empty($formTitle) ) ? $formTitle : "titulo";
        $this->formTitle    = $formTitle;
    }
    //--------------------------------------------------------------------------------------
    public function getFormTitle()
    {
        return $this->formTitle;
    }
    //--------------------------------------------------------------------------------------
    public function setFormPath($formPath)
    {
        $formPath = ( !empty($formPath) ) ?$formPath : "/modulos";
        $this->formPath    = $formPath;
    }
    //--------------------------------------------------------------------------------------
    public function getFormPath()
    {
        return $this->formPath;
    }
    //--------------------------------------------------------------------------------------
    public function setFormFileName($formFileName)
    {
        $formFileName = ( !empty($formFileName) ) ?$formFileName : "form-".date('Ymd-Gis');
        $this->formFileName    = strtolower($formFileName.'.php');
    }
    //--------------------------------------------------------------------------------------
    public function getFormFileName()
    {
        return $this->formFileName;
    }
    //--------------------------------------------------------------------------------------
    public function setPrimaryKeyTable($primaryKeyTable)
    {
        $primaryKeyTable = ( !empty($primaryKeyTable) ) ?$primaryKeyTable : "id";
        $this->primaryKeyTable    = strtoupper($primaryKeyTable);
    }
    //--------------------------------------------------------------------------------------
    public function getPrimaryKeyTable()
    {
        return $this->primaryKeyTable;
    }
    //--------------------------------------------------------------------------------------
    public function getTableRefCC($tableRef)
    {
        $tableRef = strtolower($tableRef);
        return ucfirst($tableRef);
    }
    //--------------------------------------------------------------------------------------
    public function setTableRef($tableRef)
    {
        $this->tableRef      = strtolower($tableRef);
        $this->tableRefClass = $this->getTableRefCC($tableRef);
        $this->tableRefDAO   = $this->getTableRefCC($tableRef).'DAO';
        $this->tableRefVO    = $this->getTableRefCC($tableRef).'VO';
    }
    //--------------------------------------------------------------------------------------
    public function setListColunnsName($listColumnsName)
    {
        array_shift($listColumnsName);
        $this->listColumnsName = array_map('strtoupper', $listColumnsName);
    }
    //--------------------------------------------------------------------------------------
    public function validateListColumnsName()
    {
        return isset($this->listColumnsName) && !empty($this->listColumnsName);
    }
    //--------------------------------------------------------------------------------------
    public function setGridType($gridType)
    {
        $gridType = ( !empty($gridType) ) ?$gridType : GRID_SIMPLE;
        $this->gridType = $gridType;
    }
    public function getGridType()
    {
        return $this->gridType;
    }
    //--------------------------------------------------------------------------------------
    public function setListColumnsProperties($listColumnsProperties)
    {
        if (!is_array($listColumnsProperties)) {
            throw new InvalidArgumentException('List of Columns Properties not is a array');
        }
        $this->listColumnsProperties = $listColumnsProperties;
    }
    public function getListColumnsProperties()
    {
        return $this->listColumnsProperties;
    }
    //------------------------------------------------------------------------------------
    public function getLinesArray()
    {
        return $this->lines;
    }
    //------------------------------------------------------------------------------------
    public function getLinesString()
    {
        $string = implode($this->lines);
        return trim($string);
    }
    //--------------------------------------------------------------------------------------
    private function addLine($strNewValue = null, $boolNewLine = true)
    {
        $strNewValue = is_null($strNewValue) ? TAB.'//' . str_repeat('-', 80) : $strNewValue;
        $this->lines[] = $strNewValue.( $boolNewLine ? EOL : '');
    }
    //--------------------------------------------------------------------------------------
    private function addBlankLine()
    {
        $this->addLine('');
    }
    
    /***
     * Create variable with string sql basica
     **/
    public static function convertDataType2FormDinType($dataType)
    {
        $dataType = strtoupper($dataType);
        $result = 'TEXT';
        switch ($dataType) {
            case 'DATETIME':
            case 'DATETIME2':
            case 'DATE':
            case 'TIMESTAMP':
                //case preg_match( '/date|datetime|timestamp/i', $DATA_TYPE ):
                $result = self::FORMDIN_TYPE_DATE;
                break;
            case 'BIGINT':
            case 'DECIMAL':
            case 'DOUBLE':
            case 'FLOAT':
            case 'INT':
            case 'INT64':
            case 'INTEGER':
            case 'NUMERIC':
            case 'NUMBER':
            case 'REAL':
            case 'SMALLINT':
            case 'TINYINT':
                //case preg_match( '/decimal|real|float|numeric|number|int|int64|integer|double|smallint|bigint|tinyint/i', $DATA_TYPE ):
                $result = self::FORMDIN_TYPE_NUMBER;
                break;
            default:
                $result = self::FORMDIN_TYPE_TEXT;
        }
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieRequired($key)
    {
        $result = true;
        if (ArrayHelper::has('REQUIRED', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['REQUIRED'][$key];
        }
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieDataType($key)
    {
        $result = null;
        if (ArrayHelper::has('DATA_TYPE', $this->listColumnsProperties)) {
            //$result = strtolower($this->listColumnsProperties['DATA_TYPE'][$key]);
            $result = strtoupper($this->listColumnsProperties['DATA_TYPE'][$key]);
        }
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieFormDinType($key)
    {
        $result = null;
        if (ArrayHelper::has(self::FORMDIN_TYPE_COLUMN_NAME, $this->listColumnsProperties)) {
            $result = strtoupper($this->listColumnsProperties[self::FORMDIN_TYPE_COLUMN_NAME][$key]);
        }
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function addFieldTypeToolTip($key, $fieldName)
    {
        $COLUMN_COMMENT = null;
        if (ArrayHelper::has('COLUMN_COMMENT', $this->listColumnsProperties)) {
            $COLUMN_COMMENT = $this->listColumnsProperties['COLUMN_COMMENT'][$key];
            if (!empty($COLUMN_COMMENT)) {
                $this->addLine('$frm->getLabel(\''.$fieldName.'\')->setToolTip(\''.$COLUMN_COMMENT.'\');');
            }
        }
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieCharMax($key)
    {
        $result = null;
        if (ArrayHelper::has('CHAR_MAX', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['CHAR_MAX'][$key];
        }
        $result = empty($result) ? 50 : $result;
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieNumLength($key)
    {
        $result = null;
        if (ArrayHelper::has('NUM_LENGTH', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['NUM_LENGTH'][$key];
        }
        $result = empty($result) ? 4 : $result;
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieNumScale($key)
    {
        $result = null;
        if (ArrayHelper::has('NUM_SCALE', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['NUM_SCALE'][$key];
        }
        $result = empty($result) ? 0 : $result;
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieKeyType($key)
    {
        $result = null;
        if (ArrayHelper::has('KEY_TYPE', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['KEY_TYPE'][$key];
        }
        $result = empty($result) ? false : $result;
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function getColumnsPropertieReferencedTable($key)
    {
        $result = null;
        if (ArrayHelper::has('REFERENCED_TABLE_NAME', $this->listColumnsProperties)) {
            $result = $this->listColumnsProperties['REFERENCED_TABLE_NAME'][$key];
        }
        $result = empty($result) ? false : $result;
        return $result;
    }
    //--------------------------------------------------------------------------------------
    private function addFieldNumberOrForenKey($key, $fieldName, $REQUIRED)
    {
        $NUM_LENGTH = self::getColumnsPropertieNumLength($key);
        $NUM_SCALE  = self::getColumnsPropertieNumScale($key);
        $KEY_TYPE   = self::getColumnsPropertieKeyType($key);
        $REFERENCED_TABLE_NAME = self::getColumnsPropertieReferencedTable($key);
        
        if ($KEY_TYPE == 'FOREIGN KEY') {
            $REFERENCED_TABLE_NAME = $this->getTableRefCC($REFERENCED_TABLE_NAME);
            $this->addLine('$list'.$REFERENCED_TABLE_NAME.' = '.$REFERENCED_TABLE_NAME.'::selectAll();');
            $this->addLine('$frm->addSelectField(\''.$fieldName.'\', \''.$fieldName.'\','.$REQUIRED.',$list'.$REFERENCED_TABLE_NAME.',null,null,null,null,null,null,\' \',0);');
            $this->addFieldTypeToolTip($key, $fieldName);
        } else {
            $this->addLine('$frm->addNumberField(\''.$fieldName.'\', \''.$fieldName.'\','.$NUM_LENGTH.','.$REQUIRED.','.$NUM_SCALE.');');
            $this->addFieldTypeToolTip($key, $fieldName);
        }
    }
    //--------------------------------------------------------------------------------------
    private function addFieldType($key, $fieldName)
    {
        /**
         * Esse ajuste do $key acontece em função do setListColunnsName descarta o primeiro
         * registro que assume ser a chave primaria.
         */
        $key = $key+1;
        $CHAR_MAX    = self::getColumnsPropertieCharMax($key);
        $REQUIRED    = self::getColumnsPropertieRequired($key);
        $DATA_TYPE   = self::getColumnsPropertieDataType($key);
        $formDinType = self::getColumnsPropertieFormDinType($key);

        switch ($formDinType) {
            case self::FORMDIN_TYPE_DATE:
                $this->addLine('$frm->addDateField(\''.$fieldName.'\', \''.$fieldName.'\','.$REQUIRED.');');
                $this->addFieldTypeToolTip($key, $fieldName);
                break;
            case self::FORMDIN_TYPE_NUMBER:
                $this->addFieldNumberOrForenKey($key, $fieldName, $REQUIRED);
                break;
            default:
                if ($CHAR_MAX < CHAR_MAX_TEXT_FIELD) {
                    $this->addLine('$frm->addTextField(\''.$fieldName.'\', \''.$fieldName.'\','.$CHAR_MAX.','.$REQUIRED.','.$CHAR_MAX.');');
                } else {
                    $this->addLine('$frm->addMemoField(\''.$fieldName.'\', \''.$fieldName.'\','.$CHAR_MAX.','.$REQUIRED.',80,3);');
                }
                $this->addFieldTypeToolTip($key, $fieldName);
        }
    }
    
    //--------------------------------------------------------------------------------------
    private function addFields()
    {
        $this->addLine('$frm->addHiddenField( $primaryKey );   // coluna chave da tabela');
        if ($this->validateListColumnsName()) {
            foreach ($this->listColumnsName as $key => $value) {
                $this->addFieldType($key, $value);
            }
        }
    }
    //--------------------------------------------------------------------------------------
    private function addBasicViewController_logCatch($qtdTab)
    {
        $logType = ArrayHelper::getDefaultValeu($_SESSION[APLICATIVO], 'logType', 2);
        if ($logType == 2) {
            $this->addLine($qtdTab.'catch (DomainException $e) {');
            $this->addLine($qtdTab.TAB.'$frm->setMessage( $e->getMessage() );');
            $this->addLine($qtdTab.'}');
        }
        $this->addLine($qtdTab.'catch (Exception $e) {');
        if (($logType == 1) || ($logType == 2)) {
            $this->addLine($qtdTab.TAB.'MessageHelper::logRecord($e);');
        }
        $this->addLine($qtdTab.TAB.'$frm->setMessage( $e->getMessage() );');
        $this->addLine($qtdTab.'}');
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaViewController_salvar()
    {
        $this->addLine(TAB.'case \'Salvar\':');
        $this->addLine(TAB.TAB.'try{');
        $this->addLine(TAB.TAB.TAB.'if ( $frm->validate() ) {');
        $this->addLine(TAB.TAB.TAB.TAB.'$vo = new '.$this->tableRefVO.'();');
        $this->addLine(TAB.TAB.TAB.TAB.'$frm->setVo( $vo );');
        $this->addLine(TAB.TAB.TAB.TAB.'$resultado = '.$this->tableRefClass.'::save( $vo );');
        $this->addLine(TAB.TAB.TAB.TAB.'if($resultado==1) {');
        $this->addLine(TAB.TAB.TAB.TAB.TAB.'$frm->setMessage(\'Registro gravado com sucesso!!!\');');
        $this->addLine(TAB.TAB.TAB.TAB.TAB.'$frm->clearFields();');
        $this->addLine(TAB.TAB.TAB.TAB.'}else{');
        $this->addLine(TAB.TAB.TAB.TAB.TAB.'$frm->setMessage($resultado);');
        $this->addLine(TAB.TAB.TAB.TAB.'}');
        $this->addLine(TAB.TAB.TAB.'}');
        $this->addLine(TAB.TAB.'}');
        $this->addBasicViewController_logCatch(TAB.TAB);
        $this->addLine(TAB.'break;');
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaViewController_buscar()
    {
        $this->addLine();
        $this->addLine(TAB.'case \'Buscar\':');
        $this->addGetWhereGridParametersArray(TAB.TAB);
        $this->addLine(TAB.TAB.'$whereGrid = $retorno;');
        $this->addLine(TAB.'break;');
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaViewController_limpar()
    {
        $this->addLine();
        $this->addLine(TAB.'case \'Limpar\':');
        $this->addLine(TAB.TAB.'$frm->clearFields();');
        $this->addLine(TAB.'break;');
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaViewController_gdExcluir()
    {
        $this->addLine();
        $this->addLine(TAB.'case \'gd_excluir\':');
        $this->addLine(TAB.TAB.'try{');
        $this->addLine(TAB.TAB.TAB.'$id = $frm->get( $primaryKey ) ;');
        $this->addLine(TAB.TAB.TAB.'$resultado = '.$this->tableRefClass.'::delete( $id );;');
        $this->addLine(TAB.TAB.TAB.'if($resultado==1) {');
        $this->addLine(TAB.TAB.TAB.TAB.'$frm->setMessage(\'Registro excluido com sucesso!!!\');');
        $this->addLine(TAB.TAB.TAB.TAB.'$frm->clearFields();');
        $this->addLine(TAB.TAB.TAB.'}else{');
        $this->addLine(TAB.TAB.TAB.TAB.'$frm->clearFields();');
        $this->addLine(TAB.TAB.TAB.TAB.'$frm->setMessage($resultado);');
        $this->addLine(TAB.TAB.TAB.'}');
        $this->addLine(TAB.TAB.'}');
        $this->addBasicViewController_logCatch(TAB.TAB);
        $this->addLine(TAB.'break;');
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaViewController()
    {
        $this->addBlankLine();
        $this->addLine('$acao = isset($acao) ? $acao : null;');
        $this->addLine('switch( $acao ) {');
        $this->addBasicaViewController_salvar();
        if ($this->gridType == GRID_SIMPLE) {
            $this->addBasicaViewController_buscar();
        }
        $this->addBasicaViewController_limpar();
        $this->addBasicaViewController_gdExcluir();
        $this->addLine('}');
    }
    //--------------------------------------------------------------------------------------
    public function getMixUpdateFields()
    {
        if ($this->validateListColumnsName()) {
            $mixUpdateFields = '$primaryKey.\'|\'.$primaryKey.\'';
            foreach ($this->listColumnsName as $key => $value) {
                $mixUpdateFields = $mixUpdateFields.','.$value.'|'.$value;
            }
            $mixUpdateFields = $mixUpdateFields.'\';';
            $mixUpdateFields = '$mixUpdateFields = '.$mixUpdateFields;
        }
        return $mixUpdateFields;
    }
    //--------------------------------------------------------------------------------------
    public function addColumnsGrid($qtdTab)
    {
        //$this->addLine($qtdTab.'$gride->addColumn($primaryKey,\'id\',50,\'center\');');
        $this->addLine($qtdTab.'$gride->addColumn($primaryKey,\'id\');');
        if ($this->validateListColumnsName()) {
            foreach ($this->listColumnsName as $key => $value) {
                //$this->addLine($qtdTab.'$gride->addColumn(\''.$value.'\',\''.$value.'\',50,\'center\');');
                $this->addLine($qtdTab.'$gride->addColumn(\''.$value.'\',\''.$value.'\');');
            }
        }
    }
    //--------------------------------------------------------------------------------------
    public function addGetWhereGridParameters_fied($primeira, $campo, $qtdTabs)
    {
        if ($primeira == true) {
            $this->addLine($qtdTabs.'\''.$campo.'\'=>$frm->get(\''.$campo.'\')');
        } else {
            $this->addLine($qtdTabs.',\''.$campo.'\'=>$frm->get(\''.$campo.'\')');
        }
    }
    //--------------------------------------------------------------------------------------
    public function addGetWhereGridParametersFields($qtdTabs)
    {
        foreach ($this->listColumnsName as $key => $value) {
            $this->addGetWhereGridParameters_fied(false, $value, $qtdTabs);
        }
    }
    //--------------------------------------------------------------------------------------
    public function addGetWhereGridParametersArray($qtdTabs)
    {
        $this->addLine($qtdTabs.'$retorno = array(');
        $this->addGetWhereGridParameters_fied(true, $this->getPrimaryKeyTable(), $qtdTabs.TAB.TAB);
        $this->addgetWhereGridParametersFields($qtdTabs.TAB.TAB);
        $this->addLine($qtdTabs.');');
    }
    //--------------------------------------------------------------------------------------
    public function addGetWhereGridParameters()
    {
        if ($this->validateListColumnsName()) {
            $this->addBlankLine();
            $this->addLine('function getWhereGridParameters(&$frm){');
            $this->addLine(TAB.'$retorno = null;');
            $this->addLine(TAB.'if($frm->get(\'BUSCAR\') == 1 ){');
            $this->addGetWhereGridParametersArray(TAB.TAB);
            $this->addLine(TAB.'}');
            $this->addLine(TAB.'return $retorno;');
            $this->addLine('}');
        }
    }
    //--------------------------------------------------------------------------------------
    private function addBasicaGrid()
    {
        $this->addBlankLine();
        $this->addLine('$dados = '.$this->tableRefClass.'::selectAll($primaryKey,$whereGrid);');
        $this->addLine($this->getMixUpdateFields());
        $this->addLine('$gride = new TGrid( \'gd\'        // id do gride');
        $this->addLine('				   ,\'Gride\'     // titulo do gride');
        $this->addLine('				   ,$dados 	      // array de dados');
        $this->addLine('				   ,null		  // altura do gride');
        $this->addLine('				   ,null		  // largura do gride');
        $this->addLine('				   ,$primaryKey   // chave primaria');
        $this->addLine('				   ,$mixUpdateFields');
        $this->addLine('				   );');
        $this->addColumnsGrid(null);
        $this->addLine('$frm->addHtmlField(\'gride\',$gride);');
    }
    //--------------------------------------------------------------------------------------
    public function addGridPagination_jsScript_init_parameter($frist, $parameter)
    {
        $result = null;
        if ($frist == true) {
            $result = '"'.$parameter.'":""';
        } else {
            $result = ',"'.$parameter.'":""';
        }
        return $result;
    }
    //--------------------------------------------------------------------------------------
    public function addGridPagination_jsScript_init_allparameters($qtdTab)
    {
        if ($this->validateListColumnsName()) {
            $line = null;
            $line = $line.$this->addGridPagination_jsScript_init_parameter(true, 'BUSCAR');
            $line = $line.$this->addGridPagination_jsScript_init_parameter(false, $this->getPrimaryKeyTable());
            foreach ($this->listColumnsName as $key => $value) {
                $line = $line.$this->addGridPagination_jsScript_init_parameter(false, $value);
            }
            $line = $qtdTab.'var Parameters = {'.$line.'};';
            $this->addLine($line);
        }
    }
    //--------------------------------------------------------------------------------------
    public function addGridPagination_jsScript_init()
    {
        $this->addLine('function init() {');
        $this->addGridPagination_jsScript_init_allparameters(TAB);
        $this->addLine(TAB.'fwGetGrid(\''.$this->formFileName.'\',\'gride\',Parameters,true);');
        $this->addLine('}');
    }
    //--------------------------------------------------------------------------------------
    public function addGridPagination_jsScript_buscar()
    {
        $this->addLine('function buscar() {');
        $this->addLine(TAB.'jQuery("#BUSCAR").val(1);');
        $this->addLine(TAB.'init();');
        $this->addLine('}');
    }
    //--------------------------------------------------------------------------------------
    public function addGridPagination_jsScript()
    {
        $this->addLine('<script>');
        $this->addGridPagination_jsScript_init();
        $this->addGridPagination_jsScript_buscar();
        $this->addLine('</script>');
    }
    //--------------------------------------------------------------------------------------
    public function addGrid()
    {
        
        if ($this->gridType == GRID_SIMPLE) {
            $this->addBasicaGrid();
            $this->addBlankLine();
            $this->addLine('$frm->show();');
            $this->addLine("?>");
        } else {
            $this->addGetWhereGridParameters();
            $this->addBlankLine();
            $this->addLine('if( isset( $_REQUEST[\'ajax\'] )  && $_REQUEST[\'ajax\'] ) {');
            $this->addLine(TAB.'$maxRows = ROWS_PER_PAGE;');
            $this->addLine(TAB.'$whereGrid = getWhereGridParameters($frm);');
            if ($this->gridType == GRID_SQL_PAGINATION) {
                $this->addLine(TAB.'$page = PostHelper::get(\'page\');');
                $this->addLine(TAB.'$dados = '.$this->tableRefClass.'::selectAllPagination( $primaryKey, $whereGrid, $page,  $maxRows);');
                $this->addLine(TAB.'$realTotalRowsSqlPaginator = '.$this->tableRefClass.'::selectCount( $whereGrid );');
            } elseif ($this->gridType == GRID_SCREEN_PAGINATION) {
                $this->addLine(TAB.'$dados = '.$this->tableRefClass.'::selectAll( $primaryKey, $whereGrid );');
            }
            $this->addLine(TAB.$this->getMixUpdateFields());
            $this->addLine(TAB.'$gride = new TGrid( \'gd\'                        // id do gride');
            $this->addLine(TAB.'				   ,\'Gride with SQL Pagination\' // titulo do gride');
            $this->addLine(TAB.'				   );');
            $this->addLine(TAB.'$gride->addKeyField( $primaryKey ); // chave primaria');
            $this->addLine(TAB.'$gride->setData( $dados ); // array de dados');
            if ($this->gridType == GRID_SQL_PAGINATION) {
                $this->addLine(TAB.'$gride->setRealTotalRowsSqlPaginator( $realTotalRowsSqlPaginator );');
            }
            $this->addLine(TAB.'$gride->setMaxRows( $maxRows );');
            $this->addLine(TAB.'$gride->setUpdateFields($mixUpdateFields);');
            $this->addLine(TAB.'$gride->setUrl( \''.$this->getFormFileName().'\' );');
            $this->addBlankLine();
            $this->addColumnsGrid(TAB);
            $this->addBlankLine();
            $this->addLine(TAB.'$gride->show();');
            $this->addLine(TAB.'die();');
            $this->addLine('}');
            $this->addBlankLine();
            $this->addLine('$frm->addHtmlField(\'gride\');');
            $this->addLine('$frm->addJavascript(\'init()\');');
            $this->addLine('$frm->show();');
            $this->addBlankLine();
            $this->addLine("?>");
            $this->addGridPagination_jsScript();
        }
    }
    //--------------------------------------------------------------------------------------
    public function addButtons()
    {
        if ($this->gridType == GRID_SIMPLE) {
            $this->addLine('$frm->addButton(\'Buscar\', null, \'Buscar\', null, null, true, false);');
        } else {
            $this->addLine('$frm->addButton(\'Buscar\', null, \'btnBuscar\', \'buscar()\', null, true, false);');
        }
        $this->addLine('$frm->addButton(\'Salvar\', null, \'Salvar\', null, null, false, false);');
        $this->addLine('$frm->addButton(\'Limpar\', null, \'Limpar\', null, null, false, false);');
    }
    //--------------------------------------------------------------------------------------
    public function showForm($print = false)
    {
        $this->lines=null;
        $this->addLine('<?php');
        $this->addLine('defined(\'APLICATIVO\') or die();');
        $this->addBlankLine();
        if ($this->gridType == GRID_SIMPLE) {
            $this->addLine('$whereGrid = \' 1=1 \';');
        }
        $this->addLine('$primaryKey = \''.$this->getPrimaryKeyTable().'\';');
        $this->addLine('$frm = new TForm(\''.$this->formTitle.'\',800,950);');
        $this->addLine('$frm->setFlat(true);');
        $this->addLine('$frm->setMaximize(true);');
        //$this->addLine('$frm->setAutoSize(true);');  // https://github.com/bjverde/formDin/issues/48 problema com o Chrome
        $this->addBlankLine();
        $this->addBlankLine();
        if ($this->gridType != GRID_SIMPLE) {
            $this->addLine('$frm->addHiddenField( \'BUSCAR\' ); //Campo oculto para buscas');
        }
        $this->addFields();
        $this->addBlankLine();
        $this->addButtons();
        $this->addBlankLine();
        $this->addBasicaViewController();
        $this->addBlankLine();
        $this->addGrid();
        
        if ($print) {
            echo $this->getLinesString();
        } else {
            return $this->getLinesString();
        }
    }
    //---------------------------------------------------------------------------------------
    /**
     * @codeCoverageIgnore
     */
    public function saveForm()
    {
        $fileName = $this->formPath.DS.$this->formFileName;
        if ($fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            $payload = $this->showForm(false);
            file_put_contents($fileName, $payload);
        }
    }
}
