<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Main Predefined Abstract Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl / Predefined
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @version          1.0.1
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Predefined;

defined('PH7') or exit('Restricted access');

abstract class Predefined
{
    const PHP_OPEN = '<?php ';
    const PHP_CLOSE = '?>';
    const WRITE = 'echo ';

    /** @var string */
    protected $sCode;

    /** @var string */
    protected $sLeftDelim = '{';

    /** @var string */
    protected $sRightDelim = '}';

    /**
     * @param string $sCode
     */
    public function __construct($sCode)
    {
        $this->sCode = $sCode;
    }

    /**
     * Adding Variable.
     *
     * @param string $sKey
     * @param string $sValue
     * @param boolean Print the variable. Default TRUE
     *
     * @return void
     */
    protected function addVar($sKey, $sValue, $bPrint = true)
    {
        $this->sCode = str_replace('$' . $sKey, $sValue, $this->sCode);
        $this->sCode = str_replace(
            $this->sLeftDelim . $sKey . $this->sRightDelim,
            static::PHP_OPEN . ($bPrint ? static::WRITE : '') . $sValue . static::PHP_CLOSE,
            $this->sCode
        );
    }

    /**
     * Adding Function.
     *
     * @param string $sKey
     * @param string $sValue
     *
     * @return void
     */
    protected function addFunc($sKey, $sValue)
    {
        $this->sCode = preg_replace(
            '#' . $sKey . '#',
            static::PHP_OPEN . static::WRITE . $sValue . static::PHP_CLOSE,
            $this->sCode
        );
    }

    /**
     * Gets the parsed variables.
     *
     * @return string
     */
    public function get()
    {
        return $this->sCode;
    }

    /**
     * Assign the global variables/functions.
     *
     * @return self
     */
    abstract public function assign();
}
