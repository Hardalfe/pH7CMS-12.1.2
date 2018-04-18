<?php
/**
 * @title            Advertisement Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ads
 */

namespace PH7\Framework\Ads;

defined('PH7') or exit('Restricted access');

use PH7\DbTableName;
use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Mvc\Model\Ads as ModelAds;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Parse\SysVar;
use stdClass;

class Ads
{
    const PARAM_URL = 'ad_click';

    /**
     * Output Advertisement.
     *
     * @param stdClass $oData Db query.
     *
     * @return string
     */
    public static function output(stdClass $oData)
    {
        // Stat Advertisement Shows
        Statistic::setView($oData->adsId, DbTableName::AD);

        // Advertisement Clicks
        $oHttpRequest = new HttpRequest;
        if ($oHttpRequest->getExists(static::PARAM_URL) &&
            $oHttpRequest->get(static::PARAM_URL) == $oData->adsId
        ) {
            ModelAds::setClick($oData->adsId);
        }
        unset($oHttpRequest);

        return (new SysVar)->parse($oData->code);
    }
}
