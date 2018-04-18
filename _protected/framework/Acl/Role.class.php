<?php
/**
 * @title            Acl Role Class
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class Role
{
    /**
     * @param string $sName
     *
     * @return string
     *
     * @throws Exception
     */
    public function __get($sName)
    {
        switch ($sName) {
            case 'sName':
            case 'sPermissions':
                return $this->sName;

            default:
                throw new Exception("Unable to get \"$sName\".");
        }
    }

    /**
     * @param string $sName
     *
     * @param string $sValue
     *
     * @throws Exception
     */
    public function __set($sName, $sValue)
    {
        switch ($sName) {
            case 'sName':
            case 'sPermissions':
                $this->$sName = $sValue;
                break;

            default:
                throw new Exception("Unable to set \"$sName\".");
        }
    }

    /**
     * @param string $sName
     *
     * @return bool
     */
    public function __isset($sName)
    {
        return isset($this->$sName);
    }
}
