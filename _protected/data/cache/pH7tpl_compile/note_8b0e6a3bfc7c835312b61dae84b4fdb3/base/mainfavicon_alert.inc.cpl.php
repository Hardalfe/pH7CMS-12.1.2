<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2018-04-04 20:01:35
Compiled file from: C:\xampp\htdocs\pH7CMS-12.1.2\templates/themes/base\tpl\favicon_alert.inc.tpl
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
?><?php $favicon_alert = 0 ; if(!empty($count_unread_mail)) { ?> <?php $favicon_alert += $count_unread_mail ; } if(!empty($count_pen_friend_request)) { ?> <?php $favicon_alert += $count_pen_friend_request ; } if($favicon_alert > 0) { ?> <script src="<?php echo PH7_URL_STATIC . PH7_JS?>tinycon.js"></script> <script>Tinycon.setBubble(<?php echo $favicon_alert; ?>)</script><?php } ?>