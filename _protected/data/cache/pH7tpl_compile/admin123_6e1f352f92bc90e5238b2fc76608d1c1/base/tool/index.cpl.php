<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 22:55:52
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\tool\index.tpl
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
?><div class="center"> <div class="border m_marg vs_padd"> <h2><?php echo t('Database'); ?></h2> <?php LinkCoreForm::display(t('Optimization Tables'), PH7_ADMIN_MOD, 'tool', 'optimize', array('is'=>1)) ;?> &nbsp; &bull; &nbsp; <?php LinkCoreForm::display(t('Repair Tables'), PH7_ADMIN_MOD, 'tool', 'repair', array('is'=>1)) ;?> <p class="small"><?php echo t('These actions can take minutes depending of the database size.'); ?></p> </div></div>