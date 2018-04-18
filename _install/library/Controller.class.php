<?php
/**
 * @title            Controller Core Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://ph7cms.com
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use Smarty;

abstract class Controller implements Controllable
{
    const PHP_TIMEZONE_DIRECTIVE = 'date.timezone';
    const VIEW_CACHE_LIFETIME = 86400; // 86400 seconds = 24h

    const SOFTWARE_NAME = 'pH7CMS';
    const DEFAULT_SITE_NAME = 'My Dating WebApp';
    const SOFTWARE_PREFIX_COOKIE_NAME = 'pH7';
    const SOFTWARE_WEBSITE = 'http://ph7cms.com';
    const SOFTWARE_LICENSE_URL = 'http://ph7cms.com/legal/license';
    const SOFTWARE_REQUIREMENTS_URL = 'http://ph7cms.com/doc/en/requirements';
    const PAYPAL_DONATE_URL = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X457W3L7DAPC6';
    const PATREON_URL = 'https://www.patreon.com/bePatron?u=3534366';
    const SOFTWARE_EMAIL = 'hello@ph7cms.com';
    const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    const AUTHOR_URL = 'https://github.com/pH-7';
    const SOFTWARE_GIT_REPO = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    const SOFTWARE_TWITTER = '@pH7Soft';
    const SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.';
    const SOFTWARE_COPYRIGHT = '© (c) 2012-%s, Pierre-Henry Soria. All Rights Reserved.';
    const TOTAL_INSTALL_STEPS = 6;

    /**
     * VERSION NAMES:
     * 1.0, 1.1 branches were "pOH", 1.2 was "pOW", 1.3, 1.4 were "p[H]", 2.* was "H2O", 3.* was "H3O", 4.* was "HCO",
     * 5.* was "pCO", 6.* was "WoW", 7.*, 8.* were "NaOH", 10.* was "pKa" and 12.* is "PHS"
     */
    const SOFTWARE_VERSION_NAME = 'PHS';
    const SOFTWARE_VERSION = '12.1.2';
    const SOFTWARE_BUILD = '1';

    const DEFAULT_LANG = 'en';
    const DEFAULT_THEME = 'base';

    /** @var Smarty */
    protected $oView;

    /** @var string */
    protected $sCurrentLang;

    public function __construct()
    {
        global $LANG;

        // Initialize PHP session
        $this->initializePHPSession();

        // Verify and correct the time zone if necessary
        $this->checkTimezone();

        // Language initialization
        $this->sCurrentLang = (new Language)->get();
        include_once PH7_ROOT_INSTALL . 'langs/' . $this->sCurrentLang . '/install.lang.php';

        /* Smarty initialization */
        $this->oView = new Smarty;
        $this->oView->use_sub_dirs = true;
        $this->oView->setTemplateDir(PH7_ROOT_INSTALL . 'views/' . self::DEFAULT_THEME);
        $this->oView->setCompileDir(PH7_ROOT_INSTALL . 'data/caches/smarty_compile');
        $this->oView->setCacheDir(PH7_ROOT_INSTALL . 'data/caches/smarty_cache');
        $this->oView->setPluginsDir(PH7_ROOT_INSTALL . 'library/Smarty/plugins');

        // Smarty Cache
        $this->oView->caching = 0; // 0 = Cache disabled |  1 = Cache never expires | 2 = Set the cache duration at "cache_lifetime" attribute
        $this->oView->cache_lifetime = self::VIEW_CACHE_LIFETIME;

        $this->oView->assign('LANG', $LANG);
        $this->oView->assign('software_name', self::SOFTWARE_NAME);
        $this->oView->assign('software_version', self::SOFTWARE_VERSION . ' ' . self::SOFTWARE_VERSION_NAME . ' - Build ' . self::SOFTWARE_BUILD);
        $this->oView->assign('software_website', self::SOFTWARE_WEBSITE);
        $this->oView->assign('software_license_url', self::SOFTWARE_LICENSE_URL);
        $this->oView->assign('paypal_donate_url', self::PAYPAL_DONATE_URL);
        $this->oView->assign('patreon_url', self::PATREON_URL);
        $this->oView->assign('software_author', self::SOFTWARE_AUTHOR);
        $this->oView->assign('software_copyright', sprintf(self::SOFTWARE_COPYRIGHT, date('Y')));
        $this->oView->assign('software_email', self::SOFTWARE_EMAIL);
        $this->oView->assign('tpl_name', self::DEFAULT_THEME);
        $this->oView->assign('current_lang', $this->sCurrentLang);
        $this->oView->assign('total_install_steps', self::TOTAL_INSTALL_STEPS);
    }

    /**
     * Check if the session is already initialized (thanks "session_status()" PHP >= 5.4)
     * And initialize it if it isn't the case.
     *
     * @return void
     */
    protected function initializePHPSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    /**
     * Set a default timezone if it is not already configured.
     *
     * @return void
     */
    protected function checkTimezone()
    {
        if (!ini_get(self::PHP_TIMEZONE_DIRECTIVE)) {
            date_default_timezone_set(PH7_DEFAULT_TIMEZONE);
        }
    }
}
