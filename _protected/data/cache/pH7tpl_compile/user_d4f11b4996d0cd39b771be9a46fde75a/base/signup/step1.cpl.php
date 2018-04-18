<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-05 18:59:48
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/user\views/base\tpl\signup\step1.tpl
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
?><div class="left col-md-6"> <?php $this->display('progressbar.inc.tpl'); ?> <?php JoinForm::step1() ;?></div><div class="right col-md-4 animated fadeInRight"> <p><?php echo t('Already registered?'); ?> <a href="<?php $design->url('user','main','login') ;?>"><strong><?php echo t('Sign In!'); ?></strong></a></p> <?php if(!empty($user_ref)) { ?> <div class="center"> <a href="<?php $design->getUserAvatar($username, $sex, 400) ;?>" title="<?php echo $first_name; ?>" data-popup="image"> <img class="avatar s_marg" alt="<?php echo $first_name; ?> <?php echo $username; ?>" title="<?php echo $first_name; ?>" src="<?php $design->getUserAvatar($username, $sex, 200) ;?>" /> </a> </div> <?php } else { ?> <div class="s_tMarg"> <?php $userDesignModel->profilesBlock() ;?> </div> <?php } ?></div>