<?php
/**
 * @title            Lang Class
 * @desc             Loading and management files languages (I18N).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2010-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Translate
 */

namespace PH7\Framework\Translate {
    defined('PH7') or exit('Restricted access');

    use PH7\Framework\Config\Config;
    use PH7\Framework\Cookie\Cookie;
    use PH7\Framework\Navigation\Browser;
    use PH7\Framework\Registry\Registry;

    class Lang
    {
        const COOKIE_NAME = 'pHSLang';
        const COOKIE_LIFETIME = 172800;
        const REQUEST_PARAM_NAME = 'l';
        const LANG_FOLDER_LENGTH = 5;
        const ISO_LANG_CODE_LENGTH = 2;

        /** @var Config */
        private $oConfig;

        /** @var string */
        private $sDefaultLang;

        /** @var string */
        private $sUserLang;

        /** @var string */
        private $sLangName;

        public function __construct()
        {
            $this->oConfig = Config::getInstance();

            $this->initializeUserLangOverride();
        }

        /**
         * Get JavaScript language file.
         *
         * @param string $sPath The path.
         * @param string $sFileName The language name. Default is the constant: 'PH7_LANG_CODE'
         *
         * @return string Valid file name (with the extension).
         *
         * @throws Exception If the language file is not found.
         */
        public static function getJsFile($sPath, $sFileName = PH7_LANG_CODE)
        {
            if (is_file($sPath . $sFileName . '.js')) {
                return $sFileName . '.js';
            }

            if (is_file($sPath . PH7_DEFAULT_LANG_CODE . '.js')) {
                return PH7_DEFAULT_LANG_CODE . '.js';
            }

            throw new Exception('Language file \'' . $sPath . PH7_DEFAULT_LANG_CODE . '.js\' not found.');
        }

        /**
         * Get the two-letter country code; ISO 3166-1 alpha-2
         *
         * @param string $sLocaleName Locale language name (e.g., locale such as "en_US", ..).
         *
         * @return string e.g., "en"
         */
        public static function getIsoCode($sLocaleName)
        {
            return substr($sLocaleName, 0, static::ISO_LANG_CODE_LENGTH);
        }

        /**
         * Set the default language name.
         *
         * @param string $sNewDefLang Locale language name (e.g., locale such as "en_US", ..).
         *
         * @return self
         */
        public function setDefaultLang($sNewDefLang)
        {
            $this->sDefaultLang = $sNewDefLang;

            return $this;
        }

        /**
         * Set the user language name.
         *
         * @param string $sNewUserLang Prefix of the language.
         *
         * @return self
         */
        public function setUserLang($sNewUserLang)
        {
            $this->sUserLang = $sNewUserLang;

            return $this;
        }

        /**
         * Get the default language name.
         *
         * @return string The locale language name (e.g., locale such as "en_US", ..).
         */
        public function getDefaultLang()
        {
            return $this->sDefaultLang;
        }

        /**
         * Get the current language name.
         *
         * @return string The prefix of the language (e.g., en_US).
         */
        public function getLang()
        {
            return $this->sLangName;
        }

        /**
         * Load the language file.
         *
         * @param string $sFileName The language filename (e.g., "global").
         * @param string $sPath If you want to change the default path (the path to the current module), you can specify the path.
         *
         * @return self
         */
        public function load($sFileName, $sPath = null)
        {
            textdomain($sFileName);
            bindtextdomain($sFileName, (empty($sPath) ? Registry::getInstance()->path_module_lang : $sPath));
            bind_textdomain_codeset($sFileName, PH7_ENCODING);

            return $this;
        }

        /**
         * Loading language files.
         *
         * @return self
         *
         * @throws Exception If the language file is not found.
         */
        public function init()
        {
            if (!empty($this->sUserLang) &&
                $this->oConfig->load(PH7_PATH_APP_LANG . $this->sUserLang . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) &&
                is_file(PH7_PATH_APP_LANG . $this->sUserLang . '/language.php')
            ) {
                $this->sLangName = $this->sUserLang;
                include PH7_PATH_APP_LANG . $this->sUserLang . '/language.php';
                date_default_timezone_set($this->oConfig->values['language.application']['timezone']);
            } elseif ($this->oConfig->load(PH7_PATH_APP_LANG . $this->sDefaultLang . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) &&
                is_file(PH7_PATH_APP_LANG . $this->sDefaultLang . '/language.php')
            ) {
                $this->sLangName = $this->sDefaultLang;
                include PH7_PATH_APP_LANG . $this->sDefaultLang . '/language.php';
                date_default_timezone_set($this->oConfig->values['language.application']['timezone']);
            } elseif ($this->oConfig->load(PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) &&
                is_file(PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . '/language.php')
            ) {
                $this->sLangName = PH7_DEFAULT_LANG;
                include PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . '/language.php';
                date_default_timezone_set($this->oConfig->values['language.application']['timezone']);
            } else {
                throw new Exception('Language file \'' . PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' and/or Language file \'' . PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . 'language.php\' not found.');
            }

            // Set the encoding for the specific language set.
            $this->setEncoding();

            return $this;
        }

        private function initializeUserLangOverride()
        {
            $oCookie = new Cookie;

            if ($this->isLangParamSet()) {
                $this->sUserLang = $_REQUEST[self::REQUEST_PARAM_NAME];
                $oCookie->set(static::COOKIE_NAME, $this->sUserLang, static::COOKIE_LIFETIME);
            } elseif ($oCookie->exists(static::COOKIE_NAME)) {
                $this->sUserLang = $oCookie->get(static::COOKIE_NAME);
            } else {
                $this->sUserLang = (new Browser)->getLanguage();
            }

            unset($oCookie);
        }

        /**
         * Set the correct charset to the site.
         *
         * @return void
         */
        private function setEncoding()
        {
            if (!defined('PH7_ENCODING')) {
                define('PH7_ENCODING', $this->oConfig->values['language']['charset']);
            }

            mb_internal_encoding(PH7_ENCODING);
            mb_http_output(PH7_ENCODING);
            mb_http_input(PH7_ENCODING);
            mb_language('uni');
            mb_regex_encoding(PH7_ENCODING);
        }

        /**
         * Check if a language name has been specified and meets the length requirement.
         *
         * @return bool
         */
        private function isLangParamSet()
        {
            return !empty($_REQUEST[self::REQUEST_PARAM_NAME]) &&
                strlen($_REQUEST[self::REQUEST_PARAM_NAME]) === static::LANG_FOLDER_LENGTH;
        }
    }
}

namespace {
    use PH7\Framework\Parse\SysVar;
    use PH7\Framework\Registry\Registry;

    /**
     * Check if GetText PHP extension exists, if not, it'll includes the GetText library.
     */
    if (!function_exists('gettext')) {
        require __DIR__ . '/Adapter/Gettext/gettext.inc.php';
    }

    /**
     * Language helper function.
     *
     * @param string $sVar [, string $... ]
     *
     * @return string Returns the text with gettext function or language in an array (this depends on whether a key language was found in the language table).
     */
    function t(...$aTokens)
    {
        $sToken = $aTokens[0];
        $sToken = (Registry::getInstance()->lang !== '' && array_key_exists($sToken, Registry::getInstance()->lang) ? Registry::getInstance()->lang[$sToken] : gettext($sToken));

        for ($i = 1, $iFuncArgs = count($aTokens); $i < $iFuncArgs; $i++) {
            $sToken = str_replace('%' . ($i - 1) . '%', $aTokens[$i], $sToken);
        }

        return (new SysVar)->parse($sToken);
    }

    /**
     * Plural version of t() function.
     *
     * @param string $sMsg1 Singular string.
     * @param string $sMsg2 Plurial string.
     * @param integer $iNumber
     *
     * @return string Returns the text with ngettext function which is the correct plural form of message identified by msgid1 and msgid2 for count n.
     */
    function nt($sMsg1, $sMsg2, $iNumber)
    {
        $sMsg1 = str_replace('%n%', $iNumber, $sMsg1);
        $sMsg2 = str_replace('%n%', $iNumber, $sMsg2);

        return ngettext($sMsg1, $sMsg2, $iNumber);
    }
}
