<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-04 19:25:16
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\admin\browse.tpl
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
?><form method="post" action="<?php $design->url(PH7_ADMIN_MOD,'admin','browse') ;?>"> <?php $designSecurity->inputToken('admin_action') ;?> <div class="table-responsive panel panel-default"> <div class="panel-heading bold"><?php echo t('Admins Manager'); ?></div> <table class="table table-striped"> <thead> <tr> <th><input type="checkbox" name="all_action" /></th> <th><?php echo t('Admin ID#'); ?></th> <th><?php echo t('Email Address'); ?></th> <th><?php echo t('User'); ?></th> <th><?php echo t('IP'); ?></th> <th><?php echo t('Join Date'); ?></th> <th><?php echo t('Last Activity'); ?></th> <th><?php echo t('Last Edit'); ?></th> <th><?php echo t('Action'); ?></th> </tr> </thead> <tfoot> <tr> <th><input type="checkbox" name="all_action" /></th> <th> <button class="red btn btn-default btn-md" type="submit" onclick="return checkChecked()" formaction="<?php $design->url(PH7_ADMIN_MOD,'admin','deleteall') ;?>" ><?php echo t('Delete'); ?> </button> </th> <th> </th> <th> </th> <th> </th> <th> </th> <th> </th> <th> </th> <th> </th> </tr> </tfoot> <tbody> <?php foreach($browse as $admin) { ?> <?php $adminId = (int)$admin->profileId ;?> <tr> <td> <input type="checkbox" name="action[]" value="<?php echo $adminId; ?>_<?php echo $admin->username ;?>" /> </td> <td><?php echo $adminId; ?></td> <td><?php echo $admin->email ;?></td> <td> <?php echo $admin->username ;?><br /> <span class="gray"><?php echo $admin->firstName ;?></span> </td> <td><?php $design->ip($admin->ip) ;?></td> <td class="small"><?php echo $dateTime->get($admin->joinDate)->dateTime() ;?></td> <td class="small"> <?php if(!empty($admin->lastActivity)) { ?> <?php echo $dateTime->get($admin->lastActivity)->dateTime() ;?> <?php } else { ?> <?php echo t('No login'); ?> <?php } ?> </td> <td class="small"> <?php if(!empty($admin->lastEdit)) { ?> <?php echo $dateTime->get($admin->lastEdit)->dateTime() ;?> <?php } else { ?> <?php echo t('No editing'); ?> <?php } ?> </td> <td class="small"> <a href="<?php $design->url(PH7_ADMIN_MOD,'account','edit',$adminId) ;?>" title="<?php echo t('Edit this Admin'); ?>"><?php echo t('Edit'); ?></a> <?php if(!AdminCore::isRootProfileId($adminId) ) { ?> | <?php $design->popupLinkConfirm(t('Delete'), PH7_ADMIN_MOD, 'admin', 'delete', $adminId.'_'.$admin->username) ;?> <?php } ?> </td> </tr> <?php } ?> </tbody> </table> </div></form><?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?>