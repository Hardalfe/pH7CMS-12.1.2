<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 22:56:04
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/forum\views/base\tpl\forum\index.tpl
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
?><div class="center"> <?php if(empty($error)) { ?> <?php foreach($categories as $category) { ?> <h2 class="s_tMarg underline"><?php echo $category->title ;?></h2> <?php if(AdminCore::auth()) { ?> <a class="btn btn-default btn-sm" href="<?php $design->url('forum', 'admin', 'editcategory', $category->categoryId) ;?>"><?php echo t('Edit'); ?></a> | <?php $design->popupLinkConfirm(t('Delete'), 'forum', 'admin', 'deletecategory', $category->categoryId, 'btn btn-default btn-sm') ;?><br /><br /> <?php } ?> <?php foreach($forums as $forum) { ?> <?php if($category->categoryId == $forum->categoryId) { ?> <h3 class="italic"><a href="<?php $design->url('forum', 'forum', 'topic', "$forum->name,$forum->forumId") ;?>"><?php echo escape($forum->name, true) ;?></a></h3> <p><?php echo escape($forum->description, true) ;?></p> <?php if(AdminCore::auth()) { ?> <a class="btn btn-default btn-sm" href="<?php $design->url('forum', 'admin', 'editforum', $forum->forumId) ;?>"><?php echo t('Edit'); ?></a> | <?php $design->popupLinkConfirm(t('Delete'), 'forum', 'admin', 'deleteforum', $forum->forumId, 'btn btn-default btn-sm') ;?><br /><br /> <?php } ?> <?php } ?> <?php } ?> <?php } ?> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?> <?php } else { ?> <p><?php echo $error; ?></p> <?php } ?> <?php if(AdminCore::auth()) { ?> <br /><hr /> <p> <a class="btn btn-default btn-md" href="<?php $design->url('forum', 'admin', 'addcategory') ;?>"><?php echo t('Add Category'); ?></a> <a class="btn btn-default btn-md" href="<?php $design->url('forum', 'admin', 'addforum') ;?>"><?php echo t('Add Forum'); ?></a> </p> <?php } ?></div>