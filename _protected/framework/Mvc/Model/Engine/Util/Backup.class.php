<?php
/**
 * @title            Backup (Database) Class
 * @desc             Backs up the database.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Model / Engine / Util
 * @version          1.3
 * @history          04/13/2014 - Replaced the bzip2 compression program by gzip because bzip2 is much too slow to compress and uncompress files and the compression is only a little higher.
 *                   In addition, gzip is much more common on shared hosting that bzip2.
 */

namespace PH7\Framework\Mvc\Model\Engine\Util;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Navigation\Browser;

class Backup
{
    const SQL_FILE_EXT = 'sql';
    const ARCHIVE_FILE_EXT = 'gz';
    const GZIP_COMPRESS_LEVEL = 9;

    /** @var string */
    private $_sPathName;

    /** @var string */
    private $_sSql;

    /**
     * @param string $sPathName Can be null for showing the data only ( by using Backup->back()->show() ). Default NULL
     */
    public function __construct($sPathName = null)
    {
        $this->_sPathName = $sPathName;
    }

    /**
     * Makes a SQL contents backup.
     *
     * @return self
     */
    public function back()
    {
        $this->_sSql =
            "#################### Database Backup ####################\n" .
            '# ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ', Build ' . Kernel::SOFTWARE_BUILD . "\r\n" .
            '# Database name: ' . Config::getInstance()->values['database']['name'] . "\r\n" .
            '# Created on ' . (new CDateTime)->get()->dateTime() . "\r\n" .
            "#########################################################\r\n\r\n";

        $aTables = $aColumns = $aValues = array();
        $oAllTables = Db::showTables();
        while ($aRow = $oAllTables->fetch()) $aTables[] = $aRow[0];
        unset($oAllTables);

        $oDb = Db::getInstance();

        // Loop through tables
        foreach ($aTables as $sTable) {
            $oResult = $oDb->query('SHOW CREATE TABLE ' . $sTable);

            $iNum = (int)$oResult->rowCount();

            if ($iNum > 0) {
                $aRow = $oResult->fetch();

                $this->_sSql .= "#\n# Table: $sTable\r\n#\r\n\r\n";
                $this->_sSql .= "DROP TABLE IF EXISTS $sTable;\r\n\r\n";

                $sValue = $aRow[1];

                /*** Clean up statement ***/
                $sValue = str_replace('`', '', $sValue);

                /*** Table structure ***/
                $this->_sSql .= $sValue . ";\r\n\r\n";

                unset($aRow);
            }
            unset($oResult);

            $oResult = $oDb->query('SELECT * FROM ' . $sTable);

            $iNum = (int)$oResult->rowCount();

            if ($iNum > 0) {
                while ($aRow = $oResult->fetch()) {
                    foreach ($aRow as $sColumn => $sValue) {
                        if (!is_numeric($sColumn)) {
                            if (!empty($sValue) && !is_numeric($sValue)) {
                                $sValue = Db::getInstance()->quote($sValue);
                            }

                            $sValue = str_replace(array("\r", "\n"), array('', '\n'), $sValue);

                            $aColumns[] = $sColumn;
                            $aValues[] = $sValue;
                        }
                    }

                    $this->_sSql .= 'INSERT INTO ' . $sTable . ' (' . implode(', ', $aColumns) . ') VALUES(\'' . implode('\', \'', $aValues) . "');\n";

                    unset($aColumns, $aValues);
                }
                $this->_sSql .= "\r\n\r\n";

                unset($aRow);
            }
            unset($oResult);
        }
        unset($oDb);

        return $this;
    }

    /**
     * Gets the SQL contents.
     *
     * @return string
     */
    public function show()
    {
        return $this->_sSql;
    }

    /**
     * Saves the backup in the server.
     *
     * @return void
     */
    public function save()
    {
        $rHandle = fopen($this->_sPathName, 'wb');
        fwrite($rHandle, $this->_sSql);
        fclose($rHandle);
    }

    /**
     * Saves the backup in the gzip compressed archive in the server.
     *
     * @return void
     */
    public function saveArchive()
    {
        $rArchive = gzopen($this->_sPathName, 'w');
        gzwrite($rArchive, $this->_sSql);
        gzclose($rArchive);
    }

    /**
     * Restore SQL backup file.
     *
     * @return bool|string Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restore()
    {
        $mRet = Various::execQueryFile($this->_sPathName);
        return $mRet !== true ? print_r($mRet, true) : true;
    }

    /**
     * Restore the gzip compressed archive backup.
     *
     * @return bool|string Returns TRUE if there are no errors, otherwise returns "the error message".
     */
    public function restoreArchive()
    {
        $rArchive = gzopen($this->_sPathName, 'r');

        $sSqlContent = '';
        while (!feof($rArchive)) {
            $sSqlContent .= gzread($rArchive, filesize($this->_sPathName));
        }

        gzclose($rArchive);

        $sSqlContent = str_replace(PH7_TABLE_PREFIX, Db::prefix(), $sSqlContent);
        $oDb = Db::getInstance()->exec($sSqlContent);
        unset($sSqlContent);

        return $oDb === false ? print_r($oDb->errorInfo(), true) : true;
    }

    /**
     * Download the backup on the desktop.
     *
     * @return void
     */
    public function download()
    {
        $this->downloadBackup();
    }

    /**
     * Download the backup in the gzip compressed archive on the desktop.
     *
     * @return void
     */
    public function downloadArchive()
    {
        $this->downloadBackup(true);
    }

    /**
     * Generic method that allows you to download a file or a SQL gzip file compressed archive.
     *
     * @param bool $bArchive If TRUE, the string will be compressed in gzip.
     *
     * @return void
     */
    private function downloadBackup($bArchive = false)
    {
        ob_start();
        /***** Set Headers *****/
        (new Browser)->noCache(); // No cache
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->_sPathName);

        /***** Show the SQL contents *****/
        echo($bArchive ? gzencode($this->_sSql, self::GZIP_COMPRESS_LEVEL, FORCE_GZIP) : $this->_sSql);

        /***** Catch output *****/
        $sBuffer = ob_get_contents();
        ob_end_clean();
        echo $sBuffer;
        exit;
    }
}
