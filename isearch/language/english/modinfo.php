<?php
/**
 * ****************************************************************************
 * isearch - MODULE FOR XOOPS
 * Copyright (c) Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         isearch
 * @author 			Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */

define('_MI_ISEARCH_NAME',"iSearch");
define('_MI_ISEARCH_DESC',"With this module you can know what people are searching on your website.");

define('_MI_ISEARCH_ADMMENU1',"Statistics");
define('_MI_ISEARCH_ADMMENU2',"Prune");
define('_MI_ISEARCH_ADMMENU3',"Export");
define('_MI_ISEARCH_ADMMENU4',"Blacklist");

define('_MI_ISEARCH_OPT0',"Count of searches to show on the module's index page");
define('_MI_ISEARCH_OPT0_DSC',"Select the number of searches users can see on the module's index page (0=show nothing)");

define('_MI_ISEARCH_OPT1',"Groups you don't want to record");
define('_MI_ISEARCH_OPT1_DSC',"All the searches made by people who are in those groups will not be recorded");

define('_MI_ISEARCH_OPT2',"Count of keywords visible in the administration");
define('_MI_ISEARCH_OPT2_DSC',"");

define('_MI_ISEARCH_OPT3', "List of IPs you don't want to record searches");
define('_MI_ISEARCH_OPT3_DSC', "Type one IP per line and use the same conventions as the IPs ban in the general preferences of your site");

define('_MI_ISEARCH_BNAME1',"Last searchs");
define('_MI_ISEARCH_BNAME2',"Biggest users of the search");
define('_MI_ISEARCH_BNAME3',"Statistics");
?>
