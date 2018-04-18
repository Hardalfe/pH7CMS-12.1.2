<?php
/**
 * @title            Kernel Class
 * @desc             Kernel Class of pH7CMS.
 *
 * @author           Pierre-Henry Soria <pierre@soria.pw>
 * @copyright        (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Core
 * @version          1.6
 */

namespace PH7\Framework\Core;

defined('PH7') or exit('Restricted access');

use PH7\AdminCore;
use PH7\Framework\Config\Config;
use PH7\Framework\File\File;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Page\Page;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security\Version;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

abstract class Kernel
{
    const SOFTWARE_NAME = 'pH7CMS';
    const SOFTWARE_DESCRIPTION = 'pH7CMS Dating Web App Builder. The ONLY Free, Open Source, Pro Dating Startup Builder for Growing Your Online Business';
    const SOFTWARE_WEBSITE = 'http://ph7cms.com';
    const SOFTWARE_LICENSE_KEY_URL = 'http://ph7cms.com/memberships';
    const SOFTWARE_DOC_URL = 'http://ph7cms.com/doc';
    const SOFTWARE_GIT_REPO = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    const SOFTWARE_ISSUE_URL = self::SOFTWARE_GIT_REPO . '/issues/';
    const SOFTWARE_EMAIL = 'hello@ph7cms.com';
    const SOFTWARE_TWITTER = '@pH7Soft';
    const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    const SOFTWARE_COMPANY = 'Social Dating CMS | Pierre-Henry Soria';
    const SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.';
    const SOFTWARE_COPYRIGHT = '(c) 2011-%s, Pierre-Henry Soria. All Rights Reserved.';
    const SOFTWARE_VERSION_NAME = Version::KERNEL_VERSION_NAME;
    const SOFTWARE_VERSION = Version::KERNEL_VERSION;
    const SOFTWARE_BUILD = Version::KERNEL_BUILD;
    const SOFTWARE_TECHNOLOGY_NAME = Version::KERNEL_TECHNOLOGY_NAME;
    const SOFTWARE_SERVER_NAME = Version::KERNEL_SERVER_NAME;
    const SOFTWARE_USER_AGENT = 'pH7 Web Simulator/1.1.2'; // USER AGENT NAME of Web Simulator
    const SOFTWARE_CRAWLER_NAME = 'ph7crawler'; // CRAWLER BOT NAME

    /** @var Config */
    protected $config;

    /** @var Str */
    protected $str;

    /** @var File */
    protected $file;

    /** @var Http */
    protected $httpRequest;

    /** @var Browser */
    protected $browser;

    /** @var Registry */
    protected $registry;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->str = new Str;
        $this->file = new File;
        $this->httpRequest = new Http;
        $this->browser = new Browser;
        $this->registry = Registry::getInstance();

        /**
         * @internal self::initializeLicenseConstants() cannot be declare more than one time.
         * Because Kernel.class.php file is included many times in the software, we need to check that with a constant.
         */
        if (!defined('PH7_CHECKED_LIC')) {
            define('PH7_CHECKED_LIC', 1); // OK, now we have checked the license key
            $this->initializeLicenseConstants();
        }
    }

    /**
     * Check License key.
     *
     * @return void
     */
    final private function initializeLicenseConstants()
    {
        define('PH7_LICENSE_STATUS', License::ACTIVE_STATUS);
        define('PH7_LICENSE_NAME', 'pH7Builder, Open License');
        define('PH7_VALID_LICENSE', $this->getLicenseStatus());
    }

    /**
     * @return bool
     */
    final private function getLicenseStatus()
    {
        return PH7_LICENSE_STATUS === License::ACTIVE_STATUS;
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
