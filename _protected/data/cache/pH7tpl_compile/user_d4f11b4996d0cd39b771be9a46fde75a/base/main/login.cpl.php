<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-04 14:59:17
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/user\views/base\tpl\main\login.tpl
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
?><div class="col-md-8"> <p> <?php echo t('Not registered yet?'); ?><br /> <a href="<?php $design->url('user','signup','step1') ;?>"><strong><?php echo t('Join Us Today!'); ?></strong></a> </p> <?php LoginForm::display() ;?> <p> <?php LostPwdDesignCore::link('user') ;?> <?php if(Framework\Mvc\Model\DbConfig::getSetting('userActivationType') == 2) { ?> | <a rel="nofollow" href="<?php $design->url('user','main','resendactivation') ;?>"><?php echo t('Resend activation email'); ?></a> <?php } ?> </p></div><div class="col-md-4 ad_336_280"> <?php $this->designModel->ad(336,280) ;?></div>