<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Inc / Class / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class NoteDesign extends WriteDesignCoreModel
{
    /**
     * @param object $oNoteModel
     *
     * @return void Output the URL of the thumbnail.
     */
    public static function thumb($oNoteModel)
    {
        echo '<div>';
        if (!empty($oNoteModel->thumb)) {
            echo '<a href="', Uri::get('note', 'main', 'read', $oNoteModel->username . ',' . $oNoteModel->postId), '" class="pic thumb" data-load="ajax"><img src="', PH7_URL_DATA_SYS_MOD, 'note/', PH7_IMG, $oNoteModel->username, PH7_SH, $oNoteModel->thumb, '" alt="', $oNoteModel->pageTitle, '" title="', $oNoteModel->pageTitle, '" /></a>';
        } else {
            (new AvatarDesignCore)->get($oNoteModel->username, $oNoteModel->firstName, $oNoteModel->sex, 100);
        }
        echo '</div>';
    }
}
