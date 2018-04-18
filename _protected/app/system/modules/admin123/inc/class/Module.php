<?php
/**
 * @title          Module Management
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 * @version        1.1
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\File as F;

@set_time_limit(0);
@ini_set('memory_limit', '528M');

class Module
{
    const INSTALL = 1;
    const UNINSTALL = 2;
    const MIN_SQL_FILE_SIZE = 12; // Size in bytes
    const REGEX_MODULE_FOLDER_FORMAT = '#^[a-z0-9\-]{2,35}#i';

    /**
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const DIR = 'module';
    const INSTALL_DIR = 'install';
    const SQL_DIR = 'sql';
    const INFO_DIR = 'info';

    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';
    const INSTALL_INST_CONCL_FILE = 'in_conclusion';
    const UNINSTALL_INST_CONCL_FILE = 'un_conclusion';
    const ROUTE_FILE = 'route.xml';

    /** @var F\File */
    private $oFile;

    /** @var string */
    private $sModsDirModFolder;

    /** @var string */
    private $sDefLangRoute;

    /** @var string */
    private $sRoutePath;

    /** @var string */
    private $sModRoutePath;

    public function __construct()
    {
        $this->oFile = new F\File;
        $this->sDefLangRoute = PH7_LANG_CODE;
        $this->sRoutePath = PH7_PATH_APP_CONFIG . 'routes/' . $this->sDefLangRoute . '.xml';
    }

    public function setPath($sModsDirModFolder)
    {
        $this->sModsDirModFolder = $sModsDirModFolder;
    }

    public function run($sSwitch)
    {
        if (empty($this->sModsDirModFolder)) {
            /**
             * $this->sModsDirModFolder attribute must be defined by the method Module::setPath() before executing the following methods!
             * See the ModuleController for more information (Module::setPath() method).
             */
            return false;
        }

        $sValue = $this->checkParam($sSwitch);

        if ($sValue === static::INSTALL) {
            $this->file($sValue);
            $this->route($sValue);
            $this->sql($sValue);
        } else {
            $this->sql($sValue);
            $this->route($sValue);
            $this->file($sValue);
        }
    }

    /**
     * Shows Available modules.
     *
     * @param string $sSwitch \PH7\Module::INSTALL | \PH7\Module::UNINSTALL
     *
     * @return array List of available modules.
     */
    public function showAvailableMods($sSwitch)
    {
        $sValue = $this->checkParam($sSwitch);
        $aFolders = array();

        foreach ($this->readMods($sValue) as $sFolder) {
            $aFolders[$sFolder] = $sFolder;
        }

        return $aFolders;
    }

    /**
     * Checks if the module is valid.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     * @param string $sFolder The folder
     *
     * @return boolean Returns TRUE if it is correct, FALSE otherwise.
     */
    public function checkModFolder($sSwitch, $sFolder)
    {
        $sValue = $this->checkParam($sSwitch);
        $sFullPath = ($sValue === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder : PH7_PATH_MOD . $sFolder;

        return !preg_match(static::REGEX_MODULE_FOLDER_FORMAT, $sFolder) || !is_file($sFullPath . PH7_CONFIG . PH7_CONFIG_FILE) || (PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder === PH7_PATH_MOD . $sFolder) ? false : true;
    }

    /**
     * Get the module information in the config.ini file.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     * @param string $sFolder
     *
     * @return boolean
     */
    public function readConfig($sSwitch, $sFolder)
    {
        $sValue = $this->checkParam($sSwitch);
        $sPath = ($sValue === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sFolder : PH7_PATH_MOD . $sFolder;

        return Config::getInstance()->load($sPath . PH7_CONFIG . PH7_CONFIG_FILE);
    }

    /**
     * Get the instructions.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return string|boolean Returns "false" if the file does not exist or if it fails, otherwise returns the "file contents".
     */
    public function readInstruction($sSwitch)
    {
        $sValue = $this->checkParam($sSwitch);
        $sDir = $this->sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::INFO_DIR . PH7_DS;
        $sPath = ($sValue === static::INSTALL) ? PH7_PATH_MOD . $sDir . static::INSTALL_INST_CONCL_FILE : PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sDir . static::UNINSTALL_INST_CONCL_FILE;

        try {
            return F\Import::file($sPath);
        } catch (F\Exception $e) {
            return '<p class="error">' . t('Instruction file not found!') . '</p>';
        }
    }

    /**
     * Read the modules folders.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return array Returns the module folders.
     */
    private function readMods($sSwitch)
    {
        $sPath = ($sSwitch === static::INSTALL) ? PH7_PATH_REPOSITORY . static::DIR . PH7_DS : PH7_PATH_MOD;

        return $this->oFile->readDirs($sPath);
    }

    /**
     * FOR INSTALL: Movement of the back module of the repository to the modules directory OR FOR UNISTALL: Movement of the back module of the modules directory to the repository.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void
     */
    private function file($sSwitch)
    {
        if ($sSwitch === static::INSTALL) {
            $this->oFile->systemRename(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sModsDirModFolder, PH7_PATH_MOD); // Files of module
            $this->oFile->chmod(PH7_PATH_MOD . $this->sModsDirModFolder, 0777);
        } else {
            $this->oFile->systemRename(PH7_PATH_MOD . $this->sModsDirModFolder, PH7_PATH_REPOSITORY . static::DIR . PH7_DS); // Files of module
            $this->oFile->chmod(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $this->sModsDirModFolder, 0777);
        }
    }

    /**
     * FOR INSTALL: Execute SQL statements for module installation OR FOR UNISTALL: Uninstalling the database.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void If it found a query SQL error, it display an error message with exit() function.
     */
    private function sql($sSwitch)
    {
        $sSqlFile = Config::getInstance()->values['database']['type_name'] . PH7_DS . ($sSwitch === static::INSTALL ? static::INSTALL_SQL_FILE : static::UNINSTALL_SQL_FILE);
        $sPath = PH7_PATH_MOD . $this->sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::SQL_DIR . PH7_DS . $sSqlFile;

        if (is_file($sPath) && filesize($sPath) > static::MIN_SQL_FILE_SIZE) {
            $mQuery = (new ModuleModel)->run($sPath);

            if ($mQuery !== true) {
                exit(t('Unable to execute the query SQL of module.<br />Error Message: %0%', '<pre>' . print_r($mQuery) . '</pre>'));
            }
        }
    }

    /**
     * Add or remove the routes module.
     *
     * @param string $sSwitch Module::INSTALL or Module::UNINSTALL constant.
     *
     * @return void
     */
    private function route($sSwitch)
    {
        $this->sModRoutePath = PH7_PATH_MOD . $this->sModsDirModFolder . static::INSTALL_DIR . PH7_DS . static::ROUTE_FILE;

        if (is_file($this->sModRoutePath)) {
            ($sSwitch === static::INSTALL) ? $this->addRoute() : $this->removeRoute();
        }
    }

    /**
     * Add the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function addRoute()
    {
        $sRoute = $this->oFile->getFile($this->sRoutePath);
        $sModRoute = $this->oFile->getFile($this->sModRoutePath);

        $sNewRoute = str_replace('</routes>', '', $sRoute);
        $sNewRoute .= $sModRoute . F\File::EOL . '</routes>';

        return $this->oFile->putFile($this->sRoutePath, $sNewRoute);
    }

    /**
     * Remove the module routes in the global configs/routes/<lang>.xml file.
     *
     * @return boolean
     */
    private function removeRoute()
    {
        $sRoute = $this->oFile->getFile($this->sRoutePath);
        $sModRoute = $this->oFile->getFile($this->sModRoutePath);

        $sNewRoute = str_replace($sModRoute . F\File::EOL, '', $sRoute);

        return $this->oFile->putFile($this->sRoutePath, $sNewRoute);
    }

    /**
     * Remove the module repository folder.
     *
     * @param string $sModuleDir Folder of module.
     *
     * @return boolean Returns TRUE if the folder has been deleted, FALSE otherwise.
     */
    private function removeModDir($sModuleDir)
    {
        return $this->oFile->deleteDir(PH7_PATH_REPOSITORY . static::DIR . PH7_DS . $sModuleDir);
    }

    /**
     * Checks if the constant is correct.
     *
     * Note: This method is valid only for public methods, it is not necessary to check the private methods.
     *
     * @param string $sSwitch The check constant.
     *
     * @return string Returns the constant if it is correct, otherwise an error message with exit() function.
     */
    private function checkParam($sSwitch)
    {
        if ($sSwitch === static::INSTALL) {
            return static::INSTALL;
        }

        if ($sSwitch === static::UNINSTALL) {
            return static::UNINSTALL;
        }

        exit('Wrong value in the parameter of the method: ' . __METHOD__ . ' in the class: ' . __CLASS__);
    }
}
