<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 22:59:52
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\file\publicdisplay.inc.tpl
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
?><div class="center"> <?php if(!empty($filesList)) { ?> <ul> <?php foreach($filesList as $file) { ?> <?php $short_path = str_replace([PH7_PATH_ROOT, '\\', '//'], ['', '/', '/'], $file) ;?> <li><a href="<?php $design->url(PH7_ADMIN_MOD, 'file', 'publicedit', $short_path, false) ;?>" title="<?php echo t('Click for display/edit this file'); ?>"><?php echo $short_path; ?></a></li> <?php } ?> </ul> <?php } else { ?> <p><?php echo t('Templates File Not Found!'); ?></p> <?php } ?></div>