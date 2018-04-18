<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 21:58:12
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/user\views/base\tpl\main\index.guest_splash.inc.tpl
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
?><?php if($is_bg_video) { ?> <?php $total_videos = count(glob(PH7_PATH_TPL . PH7_TPL_NAME . '/file/splash/*_vid.jpg')) ;?> <?php $i = mt_rand(1,$total_videos) ;?> <?php if(!$this->browser->isMobile()) { ?> <style scoped="scoped">video#bgvid{background: url(<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>file/splash/<?php echo $i; ?>_vid.jpg) no-repeat}</style> <video autoplay loop muted poster="<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>file/splash/<?php echo $i; ?>_vid.jpg" id="bgvid"> <source src="<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>file/splash/<?php echo $i; ?>_vid.webm" type="video/webm" /> <source src="<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>file/splash/<?php echo $i; ?>_vid.mp4" type="video/mp4" /> </video> <?php } else { ?> <style scoped="scoped"> body { background: url('<?php echo PH7_URL_TPL . PH7_TPL_NAME . PH7_SH?>file/splash/<?php echo $i; ?>_vid.jpg') repeat-y; background-size: cover; top: 50%; left: 50%; } </style> <?php } } ?><div class="col-md-8 login_block animated fadeInDown"> <?php LoginSplashForm::display() ;?></div><?php if(!$is_mobile) { ?> <div class="left col-md-8 animated fadeInLeft"> <?php $this->display($this->getCurrentController() . PH7_DS . 'user_promo_block.inc.tpl', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?> </div><?php } ?><div class="left col-md-4 animated fadeInRight"> <h1 class="red3 italic underline"><?php echo $headline; ?></h1> <div class="login_button hidden center"> <a href="<?php $design->url('user','main','login') ;?>" class="btn btn-primary btn-lg"> <strong><?php echo t('Login'); ?></strong> </a> </div> <?php JoinForm::step1() ;?> <?php if($is_mobile) { ?> <div class="s_tMarg"></div> <?php $this->display($this->getCurrentController() . PH7_DS . 'user_promo_block.inc.tpl', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); ?> <?php } ?></div>