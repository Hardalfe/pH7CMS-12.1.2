<?php
/**
 * @title          PH7 Bad Method Call Exception Class
 * @desc           Exception Bad Method Call handling.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Error / CException
 * @version        1.0
 */

namespace PH7\Framework\Error\CException;

defined('PH7') or exit('Restricted access');

use BadMethodCallException;

class PH7BadMethodCallException extends BadMethodCallException
{
    use Escape;

    /**
     * @param string $sMsg
     */
    public function __construct($sMsg)
    {
        parent::__construct($sMsg);
        $this->init($sMsg);
    }
}
