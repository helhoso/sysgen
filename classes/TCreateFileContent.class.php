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
class TCreateFileContent
{
    private $lines;
    private $EOL;
    private $TAB;
    private $DS;
    private $filePath;
    private $fileName;

    public function __construct()
    {
    }
    //--------------------------------------------------------------------------------------
    public function setFilePath($filePath)
    {
        if (empty($filePath)) {
            throw new InvalidArgumentException('FilePath is empty');
        }
        $this->filePath    = $filePath;
    }
    public function getFilePath()
    {
        return $this->filePath;
    }
    //--------------------------------------------------------------------------------------
    public function setFileName($formFileName)
    {
        if (empty($formFileName)) {
            throw new InvalidArgumentException('FileName is empty');
        }
        $this->fileName    = $formFileName;
    }
    public function getFileName()
    {
        return $this->fileName;
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
    public function addLine($strNewValue = null, $boolNewLine = true)
    {
        $strNewValue = is_null($strNewValue) ? TAB.'//' . str_repeat('-', 80) : $strNewValue;
        $this->lines[] = $strNewValue.( $boolNewLine ? EOL : '');
    }
    //--------------------------------------------------------------------------------------
    public function addBlankLine()
    {
        $this->addLine('');
    }
    //--------------------------------------------------------------------------------------
    public function addSysGenHeaderNote()
    {
        $headerNote[] = '/**'.EOL;
        $headerNote[] = ' * System generated by SysGen (System Generator with Formdin Framework) '.EOL;
        $headerNote[] = ' * Download SysGen: https://github.com/bjverde/sysgen'.EOL;
        $headerNote[] = ' * Download Formdin Framework: https://github.com/bjverde/formDin'.EOL;
        $headerNote[] = ' * '.EOL;
        $headerNote[] = ' * SysGen  Version: '.SYSTEM_VERSION.EOL;
        $headerNote[] = ' * FormDin Version: '.FORMDIN_VERSION.EOL;
        $headerNote[] = ' * '.EOL;
        $headerNote[] = ' * System '.$_SESSION[APLICATIVO]['GEN_SYSTEM_ACRONYM'].' created in: '.DateTimeHelper::getNow().EOL;
        $headerNote[] = ' */'.EOL;
        $this->lines = array_merge($this->lines, $headerNote);
    }
    //---------------------------------------------------------------------------------------
    /**
     * @codeCoverageIgnore
     */
    public function saveFile()
    {
        $fullPathfile = $this->filePath.DS.$this->fileName;
        if ($fullPathfile) {
            if (file_exists($fullPathfile)) {
                unlink($fullPathfile);
            }
            $payload = $this->show(false);
            file_put_contents($fullPathfile, $payload);
        }
    }
}
