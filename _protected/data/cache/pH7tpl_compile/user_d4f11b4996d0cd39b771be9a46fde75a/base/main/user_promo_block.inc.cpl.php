<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 21:58:13
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/user\views/base\tpl\main\user_promo_block.inc.tpl
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
?><h1 class="red3 italic underline s_bMarg"><?php echo $slogan; ?></h1><?php if($is_users_block) { ?> <div class="center profiles_window thumb pic_block"> <?php $userDesignModel->profiles(0, $number_profiles) ;?> </div><?php } ?><div class="s_tMarg" id="promo_text"> <h2><?php echo t('Meet &amp; date amazing people near %0%!', $design->geoIp(false)); ?></h2> <?php echo $promo_text; ?></div>