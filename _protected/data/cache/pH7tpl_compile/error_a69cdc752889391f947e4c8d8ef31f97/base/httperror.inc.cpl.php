<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-06 15:48:30
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\templates/themes/base\tpl\error.inc.tpl
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
?><div class="center"> <p><?php echo $error_desc; ?></p> <?php if(isset($pOH_not_found)) { ?> <div class="error-image center"></div> <h2><?php echo t('Relax and go'); ?> <a href="<?php echo $this->registry->site_url?>"><?php echo t('home'); ?></a></h2> <?php } ?></div>