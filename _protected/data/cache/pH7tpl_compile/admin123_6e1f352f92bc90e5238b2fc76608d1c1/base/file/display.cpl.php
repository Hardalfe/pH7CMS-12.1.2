<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-03 22:56:57
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\_protected\app/system/modules/admin123\views/base\tpl\file\display.tpl
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
?><div id="elfinder"></div><script src="<?php echo PH7_URL_STATIC?>fileManager/js/elfinder.js"></script><script> $(function () { $('#elfinder').elfinder({ url: pH7Url.base + '<?php echo PH7_ADMIN_MOD . PH7_SH?>asset/ajax/fileManager/<?php echo $type; ?>Connector/' }).elfinder('instance'); });</script>