<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Template Engine with Compiler and Cache for pH7 CMS!
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl
 * @copyright        (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @version          1.3.0
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @history          Supports now PHP 5 with beautiful object code (POO), (removed all the ugly object code from PHP 4.x).
 * @history          Supports now PHP 5.3 (added namespace and incorporate the template engine into the pH7Framework).
 * @history          Supports PHP 5.4 (added class member access on instantiation, e.g. (new Foo)->bar(), ...).
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Compress\Compress;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Mail as MailLayout;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;
use PH7\Framework\Mvc\Model\Design as DesignModel;
use PH7\Framework\Parse\SysVar;

class PH7Tpl extends Kernel
{
    const NAME = 'PH7Tpl';
    const AUTHOR = 'Pierre-Henry Soria';
    const VERSION = '1.3.0';
    const LICENSE = 'Creative Commons Attribution 3.0 License - http://creativecommons.org/licenses/by/3.0/';
    const ERR_MSG = 'It seems you have removed the copyright notice(s) in the software. If you really want to remove them, please email: %s';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const COMPILE_DIR = 'pH7tpl_compile';
    const CACHE_DIR = 'pH7tpl_cache';
    const MAIN_COMPILE_DIR = 'public_main';

    const MAIN_PAGE = 'layout';
    const MAIN_COMPILE_PAGE = 'layout.cpl.php';
    const XML_SITEMAP_COMPILE_PAGE = 'mainlayout.xsl.cpl.php';
    const COMPILE_FILE_EXT = '.cpl.php';

    /** @var DesignModel */
    private $designModel;

    /** @var string */
    private $sTplFile;

    /** @var string */
    private $sTemplateDir;

    /** @var string */
    private $sCompileDir;

    /** @var string */
    private $sCompileDir2;

    /** @var string */
    private $sCacheDir;

    /** @var string */
    private $sCacheDir2;

    /** @var string */
    private $sCode;

    /** @var string */
    private $sTemplateDirFile;

    /** @var string */
    private $sCompileDirFile;

    /** @var string */
    private $sCacheDirFile;

    /** @var bool */
    private $bLicense;

    /** @var string */
    private $sTplExt = '.tpl'; // Default extension

    /** @var bool */
    private $bCaching = false;

    /** @var bool */
    private $bHtmlCompressor;

    /** @var bool */
    private $bPhpCompressor;

    /** @var int|null */
    private $mCacheExpire;

    /** @var bool Enable or Disables XML Tags for the Template Engine */
    private $bXmlTags = false;

    /** @var array */
    private $_aVars = array();

    /** @var PH7Tpl */
    private $_oVars;

    // Hack that keeps the $config variable in the template
    protected $config;

    public function __construct()
    {
        parent::__construct();

        $this->checkCompileDir();
        $this->checkCacheDir();

        /** Instance objects for the class * */
        $this->_oVars = $this;
        $this->designModel = new DesignModel;

        $this->bLicense = PH7_VALID_LICENSE;

        $this->bHtmlCompressor = (bool)$this->config->values['cache']['enable.static.minify'];
        $this->bPhpCompressor = (bool)$this->config->values['cache']['enable.static.minify'];
    }

    /**
     * Get the main page file of the template.
     *
     * @return string The main page file.
     */
    public function getMainPage()
    {
        return static::MAIN_PAGE . $this->sTplExt;
    }

    /**
     * Get the template extension.
     *
     * @return string The extension with the dot.
     */
    public function getTplExt()
    {
        return $this->sTplExt;
    }

    /**
     * Set the template extension.
     *
     * @param string $sExt The extension with the dot.
     *
     * @return void
     */
    public function setTplExt($sExt)
    {
        $this->sTplExt = $sExt;
    }

    /**
     * Set the directory for the template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setTemplateDir($sDir)
    {
        if (is_dir($sDir)) {
            $this->sTemplateDir = $this->file->checkExtDir($sDir);
        } else {
            throw new PH7InvalidArgumentException(
                sprintf('<strong>%s</strong> cannot find "%s" template directory.', self::NAME, $sDir)
            );
        }
    }

    /**
     * Set the directory for the compilation template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCompileDir($sDir)
    {
        if (is_dir($sDir)) {
            $this->sCompileDir = $this->file->checkExtDir($sDir);
        } else {
            throw new PH7InvalidArgumentException(
                sprintf(
                    '<strong>%s</strong> cannot find "%s" compile directory.', self::NAME, $sDir)
            );
        }
    }

    /**
     * Set the directory for the cache template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCacheDir($sDir)
    {
        if (is_dir($sDir)) {
            $this->sCacheDir = $this->file->checkExtDir($sDir);
        } else {
            throw new PH7InvalidArgumentException(
                sprintf('<strong>%s</strong> cannot find "%s" cache directory.', self::NAME, $sDir)
            );
        }
    }

    /**
     * Enabled the cache.
     *
     * @param boolean $bCaching
     *
     * @return void
     */
    public function setCaching($bCaching)
    {
        $this->bCaching = (bool)$bCaching;
    }

    /**
     * Check if the cache is enabled.
     *
     * @return boolean
     */
    public function isEnableCache()
    {
        return $this->bCaching;
    }

    /**
     * Set the HTML Compressor.
     *
     * @param boolean $bCompressor
     *
     * @return void
     */
    public function setHtmlCompress($bCompressor)
    {
        $this->bHtmlCompressor = (bool)$bCompressor;
    }

    /**
     * Set the PHP Compressor.
     *
     * @param boolean $bCompressor
     *
     * @return void
     */
    public function setPhpCompress($bCompressor)
    {
        $this->bPhpCompressor = (bool)$bCompressor;
    }

    /**
     * Set the time of expire cache.
     *
     * @param integer $iLifeTime In seconds.
     *
     * @return void
     */
    public function setCacheExpire($iLifeTime)
    {
        $this->mCacheExpire = (int)$iLifeTime; // 3600 seconds = 1 hour cache duration
    }

    /**
     * Enable or Disable the alternate syntax (XML). Default is "false"
     *
     * @param boolean $bIsActive
     *
     * @return void
     */
    public function setXmlSyntax($bIsActive)
    {
        $this->bXmlTags = (bool)$bIsActive;
    }

    /**
     * Adds a variable that can be used by the templates.
     * Adds a new array index to the variable property. This
     * new array index will be treated as a variable by the templates.
     *
     * @see pH7Tpl::assign()
     *
     * @param string $sName The variable name to use in the template
     * @param mixed $mValue (string, object, array, integer, ...) Value Variable
     *
     * @return void
     */
    public function __set($sName, $mValue)
    {
        $this->assign($sName, $mValue);
    }

    /**
     * Retrieve an assigned variable (overload the magic __get method).
     *
     * @see pH7Tpl::getVar()
     *
     * @param string $sKey The variable name.
     *
     * @return mixed The variable value.
     */
    public function __get($sKey)
    {
        return $this->getVar($sKey);
    }

    /**
     * Allows testing with empty() and isset() to work.
     *
     * @param string $sKey
     *
     * @return boolean
     */
    public function __isset($sKey)
    {
        return isset($this->_aVars[$sKey]);
    }

    /**
     * Compiler template.
     *
     * @return boolean
     *
     * @throws TplException If the template file could not be recovered or cannot be written.
     */
    final private function compile()
    {
        // Create compile folder
        $this->file->createDir($this->sCompileDir2);

        if (!$this->sCode = $this->file->getFile($this->sTemplateDirFile)) {
            throw new TplException('Template Fetch Error: ' . $this->sTemplateDirFile);
        }

        // Parser the predefined variables
        $this->sCode = (new Predefined\Variable($this->sCode))->assign()->get();

        // Parser the predefined template functions
        $this->sCode = (new Predefined\Func($this->sCode))->assign()->get();

        // Parser the language constructs
        $this->parse();

        $sPhpHeader = $this->getHeaderContents();

        // Check if the "$design" variable is actually part of the \PH7\Framework\Layout\Html\Design class
        if (!$this->checkDesignInstance()) {
            $this->setErrMsg();
        }

        if ($this->isMainCompilePage()) {
            if (!$this->bLicense) {
                $this->sCode = preg_replace('#<title>(.*?)</title>#is', '<title>$1 (<?php echo t(\'Powered by %software_name%\') ?>)</title>', $this->sCode);
            }

            // It is forbidden to violate the copyright!
            // Thought for me who has spent years for developing a professional, high-quality software and done their best to help developers!
            if (!$this->isMarkCopyright()) {
                $this->setErrMsg();
            }
        }

        if ($this->isXmlSitemapCompilePage() && !$this->isSmallMarkCopyright()) {
            $this->setErrMsg();
        }

        if ($this->bPhpCompressor) {
            $this->sCode = (new Compress)->parsePhp($this->sCode);
        }

        $this->sCode = '<?php ' . $sPhpHeader . '?>' . $this->sCode;

        if ($rHandle = @fopen($this->sCompileDirFile, 'wb')) {
            fwrite($rHandle, $this->sCode);
            fclose($rHandle);
            return true;
        }

        throw new TplException('Could not write compiled file: ' . $this->sCompileDirFile);
    }

    /**
     * Parse the general code for translating the template language.
     *
     * @return void
     */
    private function parse()
    {
        /***** Includes *****/
        $this->sCode = str_replace(
            '{auto_include}',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $this->registry->action . \'' . $this->sTplExt . '\', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{include ([^\{\}\n]+)}#',
            '<?php $this->display($1); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{main_include ([^\{\}\n]+)}#',
            '<?php $this->display($1, PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{def_main_auto_include}#',
            '<?php $this->display(\'' . $this->sTplFile . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{def_main_include ([^\{\}\n]+)}#',
            '<?php $this->display($1, PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS); ?>',
            $this->sCode
        );
        $this->sCode = preg_replace(
            '#{manual_include ([^\{\}\n]+)}#',
            '<?php $this->display($this->getCurrentController() . PH7_DS . $1, $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?>',
            $this->sCode
        );

        /***** Objects *****/
        $this->sCode = str_replace(['$browser->', '$designModel->'],
            ['$this->browser->', '$this->designModel->'],
            $this->sCode
        );

        /***** CLassic Syntax *****/
        $this->classicSyntax();

        /***** XML Syntax *****/
        if ($this->bXmlTags) {
            $this->xmlSyntax();
        }

        /***** Variables *****/
        $this->sCode = preg_replace('#{([a-z0-9_]+)}#i', '<?php echo $$1; ?>', $this->sCode);

        /***** Clears comments {* comment *} *****/
        $this->sCode = preg_replace('#{\*.+\*}#isU', null, $this->sCode);

        /***** Code optimization *****/
        $this->optimization();
    }

    /**
     * Display template.
     *
     * @param string $sTplFile Default NULL
     * @param string $sDirPath Default NULL
     * @param integer $_bInclude Default 1 (TRUE)
     *
     * @return string
     *
     * @throws TplException If the template file does no exist.
     *
     * @throws PH7InvalidArgumentException
     */
    public function display($sTplFile = null, $sDirPath = null, $_bInclude = 1)
    {
        $this->sTplFile = $sTplFile;

        if (!empty($sDirPath)) {
            $this->setTemplateDir($sDirPath);
        }

        $this->sTemplateDirFile = $this->sTemplateDir . 'tpl' . PH7_DS . $this->sTplFile;

        $this->file->createDir($this->sCompileDir);

        if ($this->isMainDir($sDirPath)) {
            $this->sCompileDir2 = $this->sCompileDir . static::MAIN_COMPILE_DIR . PH7_DS . PH7_TPL_NAME . PH7_DS;
        } else {
            $this->sCompileDir2 = $this->sCompileDir . $this->registry->module . '_' . md5($this->registry->path_module) . PH7_DS . PH7_TPL_MOD_NAME . PH7_DS . $this->getCurrentController();
        }

        $this->sCompileDirFile = ($this->isMainDir($sDirPath) ? $this->sCompileDir2 . $this->file->getFileWithoutExt($this->sTplFile) . static::COMPILE_FILE_EXT : $this->sCompileDir2) .
            str_replace($this->getCurrentController(), '', $this->file->getFileWithoutExt($this->sTplFile)) . static::COMPILE_FILE_EXT;

        if (!$this->file->existFile($this->sTemplateDirFile)) {
            throw new TplException(
                sprintf('%s file does no exist.', $this->sTemplateDirFile)
            );
        }


        /*** If the file does not exist or if the template has been modified, recompile the makefiles ***/
        if ($this->file->getModifTime($this->sTemplateDirFile) > $this->file->getModifTime($this->sCompileDirFile)) {
            $this->compile();
        }

        if (!empty($_bInclude)) {
            $bCaching = (bool)$this->config->values['cache']['enable.html.tpl.cache'];

            if ($this->isEnableCache() === true && $bCaching === true && !$this->isMainCompilePage()) {
                $this->cache();
            } else {
                // Extraction Variables
                extract($this->_aVars);
                require $this->sCompileDirFile;
            }
        } else {
            return $this->sCompileDirFile;
        }
    }

    /**
     * Parse an email template.
     *
     * @param string $sMailTplFile
     * @param string $sEmailAddress It is used to create the privacy policy for lute against spam.
     *
     * @return string The contents of the template parsed.
     *
     * @throws TplException If the template file could not be opened.
     */
    public function parseMail($sMailTplFile, $sEmailAddress)
    {
        /**
         * If the template doesn't contain theme for emails, we retrieve the emails default themes.
         */
        if (!is_file($sMailTplFile) && defined('PH7_TPL_NAME')) {
            $sMailTplFile = str_replace(PH7_TPL_NAME, PH7_DEFAULT_THEME, $sMailTplFile);
        }

        if (!$sCode = $this->file->getFile($sMailTplFile)) {
            throw new TplException(
                sprintf('Cannot open "%s" file.', $sMailTplFile)
            );
        }

        /***** Other variables in file "/framework/Parse/SysVar.class.php" with syntax %var% *****/
        $sCode = (new SysVar)->parse($sCode);

        foreach ($this->_aVars as $sKey => $sValue) {
            /*** Variables ***/

            // We can't convert an object to a string with str_replace, which we tested the variables with is_object function
            if (!is_object($sValue)) {
                $sCode = str_replace('{' . $sKey . '}', $sValue, $sCode);
            }

            // Email Address
            //$sCode = str_replace('{email}', $sEmailAddress, $sCode);

            $oMailDesign = new MailLayout;

            /* Headers */

            // Includes
            $sCode = str_replace('{inc_header}', $oMailDesign->header(), $sCode);
            $sCode = str_replace('{inc_sub_header}', $oMailDesign->subHeader(), $sCode);

            /* Footers */

            // Privacy Policy Footer
            $sCode = str_replace('{pp_footer}', $oMailDesign->privacyPolicyFooter($sEmailAddress), $sCode);

            // Bottom Footer
            $sCode = str_replace('{b_footer}', $oMailDesign->bottomFooter(), $sCode);

            // Includes
            $sCode = str_replace('{inc_sub_footer}', $oMailDesign->subFooter($sEmailAddress), $sCode);
            $sCode = str_replace('{inc_footer}', $oMailDesign->footer(), $sCode);
            unset($oMailDesign);
        }

        return $sCode;
    }

    /**
     * Assign variables to the template.
     *
     *
     * @example
     *
     * Example with a string variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('var_name', $sName);
     *
     * === TPL ===
     *     {var_name}
     * </code>
     *
     *
     * Example with an array variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('arr_data_var', $aData);
     *
     * === TPL ===
     *     {% $arr_data_var['key1'] %}
     * </code>
     *
     *
     * Example with an object variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('obj_user_var', $oUser);
     *
     * === TPL ===
     *     {% $obj_user_var->getUsers() %}
     * --- OR ---
     *      {{ $obj_user_var->printUsers() }}
     * </code>
     *
     *
     * @see __set()
     *
     * @param string $sName Variable name
     * @param mixed $mValue (string, object, array, integer, ...) Value Variable
     * @param boolean $bEscape Specify "true" if you want to protect your variables against XSS. Default value is "false"
     * @param boolean $bEscapeStrip If you use escape method, you can also set this parameter to "true" to strip HTML and PHP tags from a string. Default value is "false"
     *
     * @return void
     */
    public function assign($sName, $mValue, $bEscape = false, $bEscapeStrip = false)
    {
        if ($bEscape === true)
            $mValue = $this->str->escape($mValue, $bEscapeStrip);

        $this->_aVars[$sName] = $mValue;
    }

    /**
     * Assign variables from array.
     *
     * @see assign()
     *
     * @param array $aVars
     * @param boolean $bEscape Specify "true" if you want to protect your variables against XSS. Default value is "false"
     * @param boolean $bEscapeStrip If you use escape method, you can also set this parameter to "true" to strip HTML and PHP tags from a string. Default value is "false"
     *
     * @return void
     */
    public function assigns(array $aVars, $bEscape = false, $bEscapeStrip = false)
    {
        foreach ($aVars as $sKey => $sValue) {
            $this->assign($sKey, $sValue, $bEscape = false, $bEscapeStrip = false); // Assign a string variable
        }
    }

    /**
     * Get a variable we assigned with the assign() method.
     *
     * @see __get()
     *
     * @param $sVarName string Name of a variable that is to be retrieved.
     *
     * @return mixed Value of that variable.
     */
    public function getVar($sVarName)
    {
        return isset($this->_aVars[$sVarName]) ? $this->_aVars[$sVarName] : '';
    }

    /**
     * Remove all variables from memory template.
     *
     * @return void
     */
    public function clean()
    {
        unset($this->_aVars, $this->_oVars);
    }

    /**
     * Checks if the compile directory has been defined otherwise we create a default directory.
     *
     * If the folder compile does not exist, it creates a folder.
     *
     * @return self
     */
    private function checkCompileDir()
    {
        $this->sCompileDir = empty($this->sCompileDir) ? PH7_PATH_CACHE . static::COMPILE_DIR . PH7_DS : $this->sCompileDir;

        return $this;
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the folder cache does not exist, it creates a folder.
     *
     * @return self
     */
    private function checkCacheDir()
    {
        $this->sCacheDir = empty($this->sCacheDir) ? PH7_PATH_CACHE . static::CACHE_DIR . PH7_DS : $this->sCacheDir;

        return $this;
    }

    /**
     * Set self pointer on cloned object.
     *
     * @clone
     */
    public function __clone()
    {
        $this->_oVars = $this;
    }

    /**
     * Cache system for the static contents with support for different templates and languages!
     *
     * @return void
     *
     * @throws Exception
     * @throws \PH7\Framework\File\Permission\PermissionException
     * @throws TplException If the cache file could not be written.
     */
    protected function cache()
    {
        // Create cache folder
        $this->file->createDir($this->sCacheDir);

        $this->sCacheDir2 = $this->sCacheDir . PH7_TPL_NAME . PH7_DS . $this->registry->module . '_' . md5($this->
            registry->path_module) . PH7_DS . PH7_TPL_MOD_NAME . PH7_DS . PH7_LANG_NAME . PH7_DS . $this->getCurrentController() . PH7_DS;
        $this->file->createDir($this->sCacheDir2);
        $this->sCacheDirFile = $this->sCacheDir2 . str_replace(PH7_DS, '_', $this->file->getFileWithoutExt($this->sTplFile)) . '.cache.html';

        if ($this->hasCacheExpired()) {
            ob_start();

            // Extraction Variables
            extract($this->_aVars);

            require $this->sCompileDirFile;
            $sOutput = ob_get_contents();
            ob_end_clean();

            if ($this->bHtmlCompressor) {
                $sOutput = (new Compress)->parseHtml($sOutput);
            }

            if (!$this->file->putFile($this->sCacheDirFile, $sOutput)) {
                throw new TplException('Unable to write to cache file: ' . $this->sCacheDirFile);
            }

            echo $sOutput;
        } else {
            readfile($this->sCacheDirFile);
        }
    }

    /**
     * Classic syntax.
     *
     * @return void
     */
    protected function classicSyntax()
    {
        /***** <?php *****/
        $this->sCode = str_replace('{{', '<?php ', $this->sCode);

        /***** ?> *****/
        if (!preg_match('#(;[\s]+}} | ;[\s]+%})#', $this->sCode)) {
            $this->sCode = str_replace(['}}', '%}'], ';?>', $this->sCode);
        } else {
            $this->sCode = str_replace(['}}', '%}'], '?>', $this->sCode);
        }

        /***** <?php echo *****/
        $this->sCode = str_replace('{%', '<?php echo ', $this->sCode);

        /***** if *****/
        $this->sCode = preg_replace('#{if ([^\{\}\n]+)}#', '<?php if($1) { ?>', $this->sCode);

        /***** elseif *****/
        $this->sCode = preg_replace('#{elseif ([^\{\}\n]+)}#', '<?php } elseif($1) { ?>', $this->sCode);

        /***** else *****/
        $this->sCode = str_replace('{else}', '<?php } else { ?>', $this->sCode);

        /***** for *****/
        /*** Example ***/
        /* {for $sData in $aData} <p>Total items: {% $sData_total %} /><br /> Number: {% $sData_i %}<br /> Name: {% $sData %}</p> {/for} */
        $this->sCode = preg_replace(
            '#{for ([^\{\}\n]+) in ([^\{\}\n]+)}#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );

        /***** while *****/
        $this->sCode = preg_replace('#{while ([^\{\}\n]+)}#', '<?php while($1) { ?>', $this->sCode);

        /***** each (foreach) *****/
        $this->sCode = preg_replace('#{each ([^\{\}\n]+) in ([^\{\}\n]+)}#', '<?php foreach($2 as $1) { ?>', $this->sCode);

        /***** endif | endfor | endwhile | endforeach *****/
        $this->sCode = str_replace(['{/if}', '{/for}', '{/while}', '{/each}'], '<?php } ?>', $this->sCode);

        /***** Escape (htmlspecialchars) *****/
        $this->sCode = preg_replace('#{escape ([^\{\}]+)}#', '<?php $this->str->escape($1); ?>', $this->sCode);

        /***** Language *****/
        $this->sCode = preg_replace('#{lang ([^\{\}]+)}#', '<?php echo t($1); ?>', $this->sCode);
        $this->sCode = preg_replace('#{lang}([^\{\}]+){/lang}#', '<?php echo t(\'$1\'); ?>', $this->sCode);

        /***** {literal} JavaScript Code {/literal} *****/
        $this->sCode = preg_replace('#{literal}(.+){/literal}#', '$1', $this->sCode);
    }

    /**
     * Parse XML style syntax.
     *
     * @return void
     */
    protected function xmlSyntax()
    {
        /***** <?php *****/
        $this->sCode = str_replace('<ph:code>', '<?php ', $this->sCode);

        /***** ?> *****/
        if (!preg_match('#;[\s]+</ph:code>$#', $this->sCode))
            $this->sCode = str_replace('</ph:code>', ';?>', $this->sCode);
        else
            $this->sCode = str_replace('</ph:code>', '?>', $this->sCode);

        /***** <?php ?> *****/
        $this->sCode = preg_replace('#<ph:code value=(?:"|\')(.+)(?:"|\') ?/?>#', '<?php $1 ?>', $this->sCode);

        /***** <?php echo *****/
        $this->sCode = preg_replace('#<ph:print value=(?:"|\')(.+)(?:"|\') ?/?>#', '<?php echo ', $this->sCode);

        /***** if *****/
        $this->sCode = preg_replace('#<ph:if test=(?:"|\')([^\<\>"\n]+)(?:"|\')>#', '<?php if($1) { ?>', $this->sCode);

        /***** if isset *****/
        $this->sCode = preg_replace('#<ph:if-set test=(?:"|\')([^\<\>"\n]+)(?:"|\')>#', '<?php if(!empty($1)) { ?>', $this->sCode);

        /***** if empty *****/
        $this->sCode = preg_replace('#<ph:if-empty test=(?:"|\')([^\<\>"\n]+)(?:"|\')>#', '<?php if(empty($1)) { ?>', $this->sCode);

        /***** if equal *****/
        $this->sCode = preg_replace(
            '#<ph:if-equal test=(?:"|\')([^\{\},"\n]+)(?:"|\'),(?:"|\')([^\{\},"\n]+)(?:"|\')>#',
            '<?php if($1 == $2) { ?>',
            $this->sCode
        );


        /***** elseif *****/
        $this->sCode = preg_replace('#<ph:else-if test=(?:"|\')([^\<\>"\n]+)(?:"|\')>#', '<?php elseif($1) { ?>', $this->sCode);

        /***** else *****/
        $this->sCode = str_replace('<ph:else>', '<?php else { ?>', $this->sCode);

        /***** for *****/
        /*** Example ***/
        /* <ph:for test="$sData in $aData"> <p>Total items: <ph:print value="$sData_total" /><br /> Number: <ph:print value="$sData_i" /><br /> Name: <ph:print value="$sData" /></p> </ph:for> */
        $this->sCode = preg_replace(
            '#<ph:for test=(?:"|\')([^\<\>"\n]+) in ([^\<\>"\n]+)(?:"|\')>#',
            '<?php for($1_i=0,$1_total=count($2);$1_i<$1_total;$1_i++) { $1=$2[$1_i]; ?>',
            $this->sCode
        );

        /***** while *****/
        $this->sCode = preg_replace('#<ph:while test=(?:"|\')([^\<\>"\n]+)(?:"|\')>#', '<?php while($1) { ?>', $this->sCode);

        /***** each (foreach) *****/
        $this->sCode = preg_replace(
            '#<ph:each test=(?:"|\')([^\<\>"\n]+) in ([^\<\>"\n]+)(?:"|\')>#',
            '<?php foreach($2 as $1) { ?>',
            $this->sCode
        );

        /***** endif | endfor | endwhile | endforeach *****/
        $this->sCode = str_replace(
            ['</ph:if>', '</ph:else>', '</ph:else-if>', '</ph:for>', '</ph:while>', '</ph:each>', '</ph:if-set>', '</ph:if-empty>', '</ph:if-equal>'],
            '<?php } ?>',
            $this->sCode
        );

        /***** Escape (htmlspecialchars) *****/
        $this->sCode = preg_replace(
            '#<ph:escape value=(?:"|\')([^\{\}]+)(?:"|\') ?/?>#',
            '<?php this->str->escape($1); ?>',
            $this->sCode
        );

        /***** Translate (Gettext) *****/
        $this->sCode = preg_replace('#<ph:lang value=(?:"|\')([^\{\}]+)(?:"|\') ?/?>#', '<?php echo t($1); ?>', $this->sCode);
        $this->sCode = preg_replace('#<ph:lang>([^\{\}]+)</ph:lang>#', '<?php echo t(\'$1\'); ?>', $this->sCode);

        /***** literal JavaScript Code *****/
        $this->sCode = preg_replace('#<ph:literal>(.+)</ph:literal>#', '$1', $this->sCode);
    }

    /**
     * Get the reserved variables.
     *
     * @return array
     */
    public function getReservedWords()
    {
        return ['auto_include', 'def_main_auto_include', 'else', 'literal', 'lang'];
    }

    /**
     * Optimizes the code generated by the compiler php template.
     *
     * @return void
     */
    protected function optimization()
    {
        $this->sCode = preg_replace(['#[\t\r\n];?\?>#s', '#\?>[\t\r\n]+?<\?(php)?#si'], '', $this->sCode);
        $this->sCode = preg_replace('#;{2,}#s', ';', $this->sCode);
    }

    /**
     * Get current controller of the pH7CMS.
     *
     * @return string The current controller
     */
    protected function getCurrentController()
    {
        return $this->httpRequest->currentController();
    }

    /**
     * Get the header content to put in the file.
     *
     * @return string
     */
    final protected function getHeaderContents()
    {
        return '
namespace PH7;
defined(\'PH7\') or exit(\'Restricted access\');
/*
Created on ' . gmdate(self::DATETIME_FORMAT) . '
Compiled file from: ' . $this->sTemplateDirFile . '
Template Engine: ' . self::NAME . ' version ' . self::VERSION . ' by ' . self::AUTHOR . '
*/
/***************************************************************************
 *     ' . self::SOFTWARE_NAME . ' ' . self::SOFTWARE_COMPANY . '
 *               --------------------
 * @since      Mon Mar 21 2011
 * @author     SORIA Pierre-Henry
 * @email      ' . self::SOFTWARE_EMAIL . '
 * @link       ' . self::SOFTWARE_WEBSITE . '
 * @copyright  ' . sprintf(self::SOFTWARE_COPYRIGHT, date('Y')) . '
 * @license    ' . self::LICENSE . '
 ***************************************************************************/
';
    }

    /**
     * Checks if the template file in the $this->sTemplateDirFile attribute is the main page (layout.tpl).
     *
     * @return boolean
     */
    private function isMainPage()
    {
        return preg_match('#' . $this->addSlashes(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . $this->getMainPage()) . '#', $this->sTemplateDirFile);
    }

    /**
     * Checks if the compile file in the $this->sCompileDirFile attribute is the main page (layout.cpl.php).
     *
     * @return boolean
     */
    final private function isMainCompilePage()
    {
        return preg_match(
            '#' . $this->addSlashes($this->sCompileDir . static::MAIN_COMPILE_DIR . PH7_DS . PH7_TPL_NAME . PH7_DS . static::MAIN_COMPILE_PAGE) . '#',
            $this->sCompileDirFile
        );
    }

    /**
     * Checks if the compile file in the $this->sCompileDirFile attribute is the XML (with XSL layout) Sitemap page (mainlayout.xsl.cpl.php).
     *
     * @return boolean
     */
    final private function isXmlSitemapCompilePage()
    {
        return preg_match('#' . static::XML_SITEMAP_COMPILE_PAGE . '#', $this->sCompileDirFile);
    }

    /**
     * Checks if the directory passed by the argument of the method is the main directory.
     *
     * @param string $sDirPath
     *
     * @return boolean
     */
    final private function isMainDir($sDirPath)
    {
        return !empty($sDirPath) && preg_match('#' . $this->addSlashes(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS) . '#', $sDirPath);
    }

    /**
     * Check that the variable "$design" is actually parts of the Design class.
     *
     * @return boolean
     */
    final private function checkDesignInstance()
    {
        return !empty($this->_aVars['design']) && $this->_aVars['design'] instanceof Design;
    }

    /**
     * Checks if the marks licensing, copyright has not been removed.
     *
     * @return boolean
     */
    final private function isMarkCopyright()
    {
        // Skip this step if it's not the base template (because there is no "link()" in layout.tpl of other templates as it includes the "base" one)
        if ($this->notBaseTheme()) {
            return true;
        }

        // "design->link()" can never be removed. Copyright notices won't be displayed if you bought a license
        return false !== strpos($this->sCode, 'design->link()');
    }

    /**
     * Checks if the small links copyright has not been removed.
     *
     * @return boolean
     */
    final private function isSmallMarkCopyright()
    {
        return false !== strpos($this->sCode, 'design->smallLink()');
    }

    /**
     * Check if it's not the base theme.
     *
     * @return boolean Returns TRUE if it's not the base theme, FALSE otherwise.
     */
    final private function notBaseTheme()
    {
        return false === strpos($this->sTemplateDir, PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS) &&
            false !== strpos($this->sCode, '$this->display(\'' . $this->getMainPage() . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS)');
    }

    /**
     * @return bool Returns TRUE if the cache has expired, FALSE otherwise.
     */
    private function hasCacheExpired()
    {
        return $this->file->getModifTime($this->sCompileDirFile) > $this->file->getModifTime($this->sCacheDirFile) ||
            (!empty($this->mCacheExpire) && $this->file->getModifTime($this->sCacheDirFile) < time() - $this->mCacheExpire);
    }

    /**
     * Add slashes to avoid errors with "preg_replace()" with Windows' backslashes in directories.
     *
     * @param string $sStr
     *
     * @return string Escaped string
     */
    private function addSlashes($sStr)
    {
        return addslashes($sStr);
    }

    /**
     * Set the error message.
     *
     * @return void
     */
    final private function setErrMsg()
    {
        $this->sCode = sprintf(static::ERR_MSG, self::SOFTWARE_EMAIL);
    }

    public function __destruct()
    {
        $this->clean();
    }
}
