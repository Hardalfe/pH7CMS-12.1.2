<?php
/**
 * @title          Picture Form Process Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Form / Processing
 * @version        1.4
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Image\Image;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Moderation\Filter;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class PictureFormProcess extends Form
{
    const MAX_IMAGE_WIDTH = 2500;
    const MAX_IMAGE_HEIGHT = 2500;

    const PICTURE2_SIZE = 400;
    const PICTURE3_SIZE = 600;
    const PICTURE4_SIZE = 800;
    const PICTURE5_SIZE = 1000;
    const PICTURE6_SIZE = 1200;

    /** @var string */
    private $sApproved;

    public function __construct()
    {
        parent::__construct();

        /**
         * @desc This can cause minor errors (eg if a user sent a file that is not a photo).
         * So we hide the errors if we are not in development mode.
         */
        if (!isDebug()) {
            error_reporting(0);
        }

        /**
         * @desc
         * Check if the photo album ID is valid. The value must be numeric.
         * This test is necessary because when the selection exists but that no option is available (this can when a user wants to add photos but he has no album)
         * the return value is of type "string" and the value is "1".
         */
        if (!is_numeric($this->httpRequest->post('album_id'))) {
            \PFBC\Form::setError('form_picture', t('Please add a category before you add some photos.'));
            return; // Stop execution of the method.
        }

        /**
         * Resizing and saving some photos
         */
        $aPhotos = $_FILES['photos']['tmp_name'];
        for ($i = 0, $iNumPhotos = count($aPhotos); $i < $iNumPhotos; $i++) {
            $oPicture1 = new Image(
                $aPhotos[$i],
                self::MAX_IMAGE_WIDTH,
                self::MAX_IMAGE_HEIGHT
            );

            if (!$oPicture1->validate()) {
                \PFBC\Form::setError('form_picture', Form::wrongImgFileTypeMsg());
                return; // Stop execution of the method.
            }

            $sAlbumTitle = MediaCore::cleanTitle($this->httpRequest->post('album_title'));
            $iAlbumId = (int)$this->httpRequest->post('album_id');

            $oPicture2 = clone $oPicture1;
            $oPicture3 = clone $oPicture1;
            $oPicture4 = clone $oPicture1;
            $oPicture5 = clone $oPicture1;
            $oPicture6 = clone $oPicture1;

            $oPicture2->square(self::PICTURE2_SIZE);
            $oPicture3->square(self::PICTURE3_SIZE);
            $oPicture4->square(self::PICTURE4_SIZE);
            $oPicture5->square(self::PICTURE5_SIZE);
            $oPicture6->square(self::PICTURE6_SIZE);

            /* Set watermark text on images */
            $sWatermarkText = DbConfig::getSetting('watermarkTextImage');
            $iSizeWatermarkText = DbConfig::getSetting('sizeWatermarkTextImage');
            $oPicture1->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oPicture2->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oPicture3->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oPicture4->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oPicture5->watermarkText($sWatermarkText, $iSizeWatermarkText);
            $oPicture6->watermarkText($sWatermarkText, $iSizeWatermarkText);

            $sPath = PH7_PATH_PUBLIC_DATA_SYS_MOD . 'picture/img/' . $this->session->get('member_username') . PH7_DS . $iAlbumId . PH7_DS;

            $sFileName = Various::genRnd($oPicture1->getFileName(), 20);

            $sFile1 = $sFileName . '-original.' . $oPicture1->getExt(); // Original
            $sFile2 = $sFileName . '-' . self::PICTURE2_SIZE . PH7_DOT . $oPicture2->getExt();
            $sFile3 = $sFileName . '-' . self::PICTURE3_SIZE . PH7_DOT . $oPicture3->getExt();
            $sFile4 = $sFileName . '-' . self::PICTURE4_SIZE . PH7_DOT . $oPicture4->getExt();
            $sFile5 = $sFileName . '-' . self::PICTURE5_SIZE . PH7_DOT . $oPicture5->getExt();
            $sFile6 = $sFileName . '-' . self::PICTURE6_SIZE . PH7_DOT . $oPicture6->getExt();

            $oPicture1->save($sPath . $sFile1);
            $oPicture2->save($sPath . $sFile2);
            $oPicture3->save($sPath . $sFile3);
            $oPicture4->save($sPath . $sFile4);
            $oPicture5->save($sPath . $sFile5);
            $oPicture6->save($sPath . $sFile6);

            $this->sApproved = DbConfig::getSetting('pictureManualApproval') == 0 ? '1' : '0';

            $this->checkNudityFilter($aPhotos[$i]);

            // It creates a nice title if no title is specified.
            $sTitle = $this->getImageTitle($i, $oPicture1);
            $sTitle = MediaCore::cleanTitle($sTitle);

            (new PictureModel)->addPhoto(
                $this->session->get('member_id'),
                $iAlbumId,
                $sTitle,
                $this->httpRequest->post('description'),
                $sFile1,
                $this->dateTime->get()->dateTime('Y-m-d H:i:s'),
                $this->sApproved
            );
        }

        Picture::clearCache();

        $sModerationText = t('Your photo(s) has/have been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
        $sText = t('Your photo(s) has/have been successfully added!');
        $sMsg = $this->sApproved === '0' ? $sModerationText : $sText;

        Header::redirect(
            Uri::get('picture',
                'main',
                'album',
                $this->session->get('member_username') . ',' . $sAlbumTitle . ',' . $iAlbumId
            ),
            $sMsg
        );
    }

    /**
     * @param string $sFile File path.
     *
     * @return void
     */
    protected function checkNudityFilter($sFile)
    {
        if (DbConfig::getSetting('nudityFilter') && Filter::isNudity($sFile)) {
            // The photo(s) seems to be suitable for adults only, so set for moderation
            $this->sApproved = '0';
        }
    }

    /**
     * Create a nice picture title if no title is specified.
     *
     * @param int $i
     * @param Image $oPicture
     *
     * @return string
     */
    private function getImageTitle($i, Image $oPicture)
    {
        if ($this->httpRequest->postExists('title') &&
            $this->str->length($this->str->trim($this->httpRequest->post('title'))) > 2
        ) {
            return $this->httpRequest->post('title');
        }

        // Otherwise get the name from the file name
        return $this->str->upperFirst(
            str_replace(
                ['-', '_'],
                ' ',
                str_ireplace(PH7_DOT . $oPicture->getExt(), '', escape($_FILES['photos']['name'][$i], true))
            )
        );
    }
}
