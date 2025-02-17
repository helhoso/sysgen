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

/**
 * Information about the table or view. With constant names
 * @author bjverde
 *
 */
final class TableInfo
{
    
    const TB_TYPE_TABLE = 'TABLE';
    const TB_TYPE_VIEW  = 'VIEW';
    const TB_TYPE_PROCEDURE  = 'PROCEDURE';
    //-----------------------------------------------------------------------------------
    const LIST_TABLES_DB = 'LIST_TABLES_DB';
    //-----------------------------------------------------------------------------------
    const TP_SYSTEM = 'TP_SYSTEM';
    
    const KEY_TYPE = 'KEY_TYPE';
    const KEY_TYPE_PK = 'PK';
    const KEY_TYPE_FK = 'FOREIGN KEY';
    const COLUMN_NAME = 'COLUMN_NAME';
    const DATA_TYPE   = 'DATA_TYPE';
    
    const ID_COLUMN_FK_SRSELECTED   = 'ID_COLUMN_FK_SRSELECTED';
    const FK_FIELDS_TABLE_SELECTED  = 'FkFieldsTableSelected';
    const FK_TYPE_SCREEN_REFERENCED = 'FK_TYPE_SCREEN_REFERENCED';
    //-----------------------------------------------------------------------------------    
    const DBMS_VERSION_SQLSERVER_2012_GTE = 'DBMS_VERSION_SQLSERVER_2012_GTE';
    const DBMS_VERSION_SQLSERVER_2012_LT  = 'DBMS_VERSION_SQLSERVER_2012_LT';

    const DBMS_VERSION_SQLSERVER_2012_GTE_LABEL = 'SQL Server 2012 (11.0) ou superior';
    const DBMS_VERSION_SQLSERVER_2012_LT_LABEL  = 'SQL Server anteior 2012';

    const DBMS_VERSION_MYSQL_8_GTE = 'DBMS_VERSION_MYSQL_8_GTE';
    const DBMS_VERSION_MYSQL_8_LT  = 'DBMS_VERSION_MYSQL_8_LT';

    const DBMS_VERSION_MYSQL_8_GTE_LABEL = 'MySQL 8 ou superior';
    const DBMS_VERSION_MYSQL_8_LT_LABEL  = 'MySQL anterior a versão 8';

    //-----------------------------------------------------------------------------------    
    public static function getPreDBMS($dbType){
        $return = null;
        switch ($dbType) {
            //-------------------
            case DBMS_MYSQL:
                $return = 'my';
            break;
            //-------------------
            case DBMS_SQLITE:
                $return = 'sq';
            break;
            //-------------------
            case DBMS_SQLSERVER:
                $return = 'ss';
            break;
            //-------------------
            case DBMS_POSTGRES:
                $return = 'pg';
            break;
            //-------------------
            case DBMS_ORACLE:
                $return = 'ora';
            break;
        }
        return $return;
    }
    //-----------------------------------------------------------------------------------    
    public static function getDbmsWithVersion($dbType){
        $return = false;
        switch ($dbType) {
            //-------------------
            case DBMS_MYSQL:
                $return = false;
            break;
            //-------------------
            case DBMS_SQLITE:
                $return = false;
            break;
            //-------------------
            case DBMS_SQLSERVER:
                $return = true;
            break;
            //-------------------
            case DBMS_POSTGRES:
                $return = false;
            break;
            //-------------------
            case DBMS_ORACLE:
                $return = false;
            break;
        }
        return $return;
    }    
}
