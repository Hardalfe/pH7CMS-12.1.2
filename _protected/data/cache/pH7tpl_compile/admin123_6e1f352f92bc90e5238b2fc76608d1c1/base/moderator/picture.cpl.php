<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-06 14:56:39
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\moderator\picture.tpl
Template Engine: PH7Tpl version 1.3.0 by Pierre-Henry Soria
*/
/***************************************************************************
 *     pH7CMS Social Dating CMS | Pierre-Henry Soria
 *               --------------------
 * @since      Mon Mar 21 2011
 * @author     SORIA Pierre-Henry
 * @email      hello@ph7cms.com
 * @link       http://ph7cms.com
 * @copyright  (c) 2011-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license    Creative Commons Attribution 3.0 License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/
?><?php if(!empty($pictures)) { ?> <ul> <?php foreach($pictures as $picture) { ?> <?php $action = ($picture->approved == 1) ? 'disapprovedphoto' : 'approvedphoto' ;?> <div class="thumb_photo"> <a href="<?php echo PH7_URL_DATA_SYS_MOD?>picture/img/<?php echo $picture->username ;?>/<?php echo $picture->albumId ;?>/<?php echo $file = str_replace('original', '1000', $picture->file) ;?>" title="<?php echo $picture->title ;?>" data-popup="image"> <img src="<?php echo PH7_URL_DATA_SYS_MOD?>picture/img/<?php echo $picture->username ;?>/<?php echo $picture->albumId ;?>/<?php echo $file = str_replace('original', '400', $picture->file) ;?>" alt="<?php echo $picture->title ;?>" title="<?php echo $picture->title ;?>" /> </a> <p class="italic"> <?php echo t('Posted by'); ?> <?php $design->getProfileLink($picture->username) ;?><br /> <small><?php echo t('Posted on %0%', $picture->createdDate); ?></small> </p> <div> <?php $text = ($picture->approved == 1) ? t('Disapproved') : t('Approved') ;?> <?php LinkCoreForm::display($text, PH7_ADMIN_MOD,'moderator', $action, array('picture_id'=>$picture->pictureId)) ;?> | <?php LinkCoreForm::display(t('Delete'), PH7_ADMIN_MOD, 'moderator', 'deletephoto', array('album_id'=>$picture->albumId, 'picture_id'=>$picture->pictureId, 'id'=>$picture->profileId, 'username'=>$picture->username, 'picture_link'=>$picture->file)) ;?> </div> </div> <?php } ?> </ul> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); } else { ?> <p class="center"> <?php echo t('No Pictures found for the moderation treatment.'); ?> </p><?php } ?>