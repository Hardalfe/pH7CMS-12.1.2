<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Pattern\Statik;

class TwoFactorAuthDesignCore
{
    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Get the "Enable Two-Factor Authentication" link.
     *
     * @param string $sMod
     *
     * @return void
     */
    public static function link($sMod)
    {
        echo
        '<p class="center">
            <a class="s_marg btn btn-primary" href="' . Uri::get('two-factor-auth', 'main', 'setup', $sMod) . '">' . t('Two-Factor Authentication') . '</a>
        </p>';
    }
}
