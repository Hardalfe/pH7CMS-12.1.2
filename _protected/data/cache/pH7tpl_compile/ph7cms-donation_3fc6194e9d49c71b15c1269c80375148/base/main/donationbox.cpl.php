<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-16 15:52:04
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/ph7cms-donation\views/base\tpl\main\donationbox.tpl
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
?><div class="col-md-12"> <div id="box_block" class="center"> <h1><?php echo t('Make Your Website Better!'); ?></h1> <form action="<?php echo $form_action; ?>" method="post"> <?php echo $form_body; ?> <input type="image" name="submit" src="<?php echo $this->registry->url_themes_module . PH7_TPL_MOD_NAME . PH7_SH . PH7_IMG?>paypal_donate.en.png" alt="Donate" /> </form> <p><?php echo t('-OR-'); ?></p> <p class="s_bMarg"> <a href="<?php echo $config->values['module.setting']['patreon.link'] ;?>"><?php echo t('Become a Patron!'); ?></a> </p> </div> <p class="center"> <?php echo t('Will You Be Nice Today...?'); ?><br /> <span class="bold"><?php echo t('And make your website better with regular updates.'); ?></span><br /> <span class="underline"><?php echo t('Be like 78% of our users who contribute to the software on a regular basis.'); ?></span> </p></div>