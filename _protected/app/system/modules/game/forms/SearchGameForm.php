<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Game / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class SearchGameForm
{
    public static function display($iWidth = null)
    {
        $aOptions = ['description' => t('Enter Name, Description, Keyword or ID of a Game.')];
        if (!empty($iWidth)) {
            $aOptions = ['style' => 'width:' . ((int)$iWidth * 1.09) . 'px'];
        }

        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => Uri::get('game', 'main', 'result') . PH7_SH, 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Search(t('Search Games'), 'looking', $aOptions));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array(SearchCoreModel::TITLE => t('Title'), SearchCoreModel::VIEWS => t('Popular'), SearchCoreModel::RATING => t('Rated'), SearchCoreModel::DOWNLOADS => t('Downloaded'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array(SearchCoreModel::ASC => t('Ascending'), SearchCoreModel::DESC => t('Descending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', array('icon' => 'search')));
        $oForm->render();
    }
}
