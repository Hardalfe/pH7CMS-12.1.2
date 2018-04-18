<?php
/**
 * @title          Retrieve News Feed from a RSS URL.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 * @version        1.0
 */

namespace PH7;

use DOMDocument;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Error\CException\PH7Exception;

class NewsFeedCore
{
    const DEF_NUM_NEWS = 10;
    const NEWS_URL = 'http://ph7cms.com/feed/';
    const CACHE_GROUP = 'str/sys/mod/admin';

    /** @var DOMDocument */
    private $oXml;

    /** @var Cache */
    private $oCache;

    /** @var array */
    private $aData = array();

    public function __construct()
    {
        $this->oXml = new DOMDocument;
        $this->oCache = new Cache;
    }

    /**
     * Gets the XML links.
     *
     * @param int $iNum Number of news to get. Default: 10
     *
     * @return array The XML tree.
     *
     * @throws PH7Exception If the Feed URL is not valid.
     */
    public function getSoftware($iNum = self::DEF_NUM_NEWS)
    {
        $this->oCache->start(self::CACHE_GROUP, 'software_feed_news' . $iNum, 3600 * 24);

        if (!$this->aData = $this->oCache->get()) {
            if (!@$this->oXml->load(static::NEWS_URL)) {
                throw new PH7Exception('Unable to retrieve news feeds for the URL: "' . static::NEWS_URL . '"');
            }

            $iCount = 0;
            foreach ($this->oXml->getElementsByTagName('item') as $oItem) {
                $sLink = $oItem->getElementsByTagName('link')->item(0)->nodeValue;

                $this->aData[$sLink]['title'] = $oItem->getElementsByTagName('title')->item(0)->nodeValue;
                $this->aData[$sLink]['link'] = $sLink;
                $this->aData[$sLink]['description'] = $oItem->getElementsByTagName('description')->item(0)->nodeValue;

                if (++$iCount === $iNum) {
                    break; // If we have the number of news we want, we stop the foreach loop.
                }
            }
            $this->oCache->put($this->aData);
        }

        return $this->aData;
    }
}
