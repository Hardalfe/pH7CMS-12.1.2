<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-04 19:58:12
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/cool-profile-page\views/base\tpl\main\profile_links.inc.tpl
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
?><div class="row"> <?php if(!$is_own_profile AND $is_im_enabled) { ?> <a class="vs_marg" rel="nofollow" href="<?php echo $messenger_link; ?>" title="<?php echo t('Chat'); ?>"><i class="fa fa-comment-o chat"></i></a> <?php } ?> <?php if($is_lovecalculator_enabled AND !$is_own_profile) { ?> <a class="vs_marg" href="<?php $design->url('love-calculator','main','index',$username) ;?>" title="<?php echo t('Match'); ?>"><i class="fa fa-heart-o heart"></i></a> <?php } ?></div><div class="row"> <?php if($is_mail_enabled AND !$is_own_profile) { ?> <a class="vs_marg" rel="nofollow" href="<?php echo $mail_link; ?>" title="<?php echo t('Send Message'); ?>"><li class="fa fa-envelope-o message"></li></a> <?php } ?> <?php if($is_friend_enabled AND !$is_own_profile) { ?> <a class="vs_marg" ref="nofollow" href="<?php echo $befriend_link; ?>" title="<?php echo t('Add Friend'); ?>"><i class="fa fa-user-plus friend"></i></a> <?php } ?></div>