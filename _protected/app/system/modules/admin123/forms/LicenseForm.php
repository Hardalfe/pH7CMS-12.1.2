<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Mvc\Model\License;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class LicenseForm
{
    /** @var int */
    private static $iLicenseId = 1;

    public static function display()
    {
        if (isset($_POST['submit_license'])) {
            if (\PFBC\Form::isValid($_POST['submit_license'])) {
                new LicenseFormProcess(self::$iLicenseId);
            }

            Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'license', '?set_msg=1'));
        }

        $sStatusColor = (PH7_VALID_LICENSE ? 'success' : 'danger');
        $sLicLink = '<a href="' . Core::SOFTWARE_LICENSE_KEY_URL . '">' . t('Buy your License Key') . '</a>';
        $sStatusTxt = '<span class="label label-' . $sStatusColor . '">' . (PH7_VALID_LICENSE ? t('Active') : t('Inactive')) . '</span>';
        $sLicTypeTxt = '<span class="italic">' . PH7_LICENSE_NAME . '</span>';
        if (PH7_LICENSE_NAME === 'Trial') {
            $sLicTypeTxt .= ' &nbsp; <span class="label label-warning">' . t('%0% to get Premium Features!', $sLicLink) . '</span>';
        }

        $oForm = new \PFBC\Form('form_license');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_license', 'form_license'));
        $oForm->addElement(new \PFBC\Element\Token('license'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="bold">' . t('License Status: %0%', $sStatusTxt) . '</p>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="bold">' . t('Current License Type: %0%', $sLicTypeTxt) . '</p>'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Your License Key:'), 'copyright_key', array('description' => $sLicLink, 'value' => (new License)->get(self::$iLicenseId), 'placeholder' => 'ph7-XXXXXXXXXXXXXXXX', 'autocomplete' => 'off', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button(t('Register'), 'submit', array('icon' => 'key')));
        $oForm->render();
    }
}
