<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-04 14:06:09
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\module\to_uninstall.inc.tpl
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
?><?php if(!$oModule->showAvailableMods(Module::UNINSTALL)) { ?> <h2 class="underline"><?php echo t('No modules available in your %software_name% repository'); ?></h2><?php } else { ?> <h2 class="underline"><?php echo t('Module(s) installed on your website:'); ?></h2><br /> <form method="post"> <?php foreach($oModule->showAvailableMods(Module::UNINSTALL) as $sFolder) { ?> <?php $sModsDirModFolder = $oFile->checkExtDir($sFolder) ;?> <?php if($oModule->checkModFolder(Module::UNINSTALL, $sModsDirModFolder)) { ?> <?php $oModule->readConfig(Module::UNINSTALL, $sModsDirModFolder) ;?> <p class="underline italic"><a href="<?php echo $config->values['module.information']['website'] ;?>" title="<?php echo t('Website of module'); ?>"><?php echo $config->values['module.information']['name'] ;?></a> <?php echo t('version'); ?> <?php echo $config->values['module.information']['version'] ;?> <?php echo t('by'); ?> <a href="mailto:<?php echo $config->values['module.information']['email'] ;?>" title="<?php echo t('Contact Author'); ?>"><?php echo $config->values['module.information']['author'] ;?></a></p> <button type="submit" class="btn btn-default btn-md" name="submit_mod_uninstall" value="<?php echo $sModsDirModFolder ;?>" onclick="return confirm('<?php echo t('Are you sure you want to uninstall this module?'); ?>');"><?php echo t('Uninstall'); ?> <?php echo $config->values['module.information']['name'] ;?></button><br /> <?php } else { ?> <button type="submit" class="error disabled btn btn-default btn-md" disabled="disabled"><?php echo t('Module path id not valid!'); ?></button><br /> <?php } ?> <hr /><br /> <?php } ?> </form><?php } ?>