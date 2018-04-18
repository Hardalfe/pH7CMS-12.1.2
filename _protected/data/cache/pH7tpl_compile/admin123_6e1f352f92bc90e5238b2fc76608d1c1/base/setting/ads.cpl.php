<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-05 14:15:43
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\setting\ads.tpl
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
?><div class="col-md-10"> <?php UpdateAdsForm::display() ;?> <br /> <?php $sSlug = (AdsCore::getTable() === AdsCore::AFFILIATE_AD_TABLE_NAME) ? 'affiliate' : '' ;?> <p><a class="bold btn btn-default btn-md" href="<?php $design->url(PH7_ADMIN_MOD, 'setting', 'addads', $sSlug) ;?>"><?php echo t('Add a new banner'); ?></a></p> <br /> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?></div>