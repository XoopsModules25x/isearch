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
 * @package   modules\isearch\language
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

define('_MI_ISEARCH_NAME', 'iSearch');
define('_MI_ISEARCH_DESC', 'With this module you can know what people are searching on your website.');

define('_MI_ISEARCH_ADMENU0', 'Home');
define('_MI_ISEARCH_ADMENU1', 'Statistics');
define('_MI_ISEARCH_ADMENU2', 'Prune');
define('_MI_ISEARCH_ADMENU3', 'Export');
define('_MI_ISEARCH_ADMENU4', 'Blacklist');
define('_MI_ISEARCH_ADMENU_ABOUT', 'About');

define('_MI_ISEARCH_ADMIN_INDEX_DESC', 'Module administration home page');
define('_MI_ISEARCH_ADMIN_STATS_DESC', 'Usage statistics');
define('_MI_ISEARCH_ADMIN_PURGE_DESC', 'Delete old information');
define('_MI_ISEARCH_ADMIN_EXPORT_DESC', 'Export a report');
define('_MI_ISEARCH_ADMIN_BLACKLIST_DESC', 'Add/Remove words on the blacklist');
define('_MI_ISEARCH_ADMIN_ABOUT_DESC', 'Info about this module');


define('_MI_ISEARCH_OPT0', 'Count of searches to show on the module\'s index page');
define('_MI_ISEARCH_OPT0_DSC', 'Select the number of searches users can see on the module\'s index page (0=show nothing)');

define('_MI_ISEARCH_OPT1', 'Groups you don\'t want to record');
define('_MI_ISEARCH_OPT1_DSC', 'All the searches made by people who are in those groups will not be recorded');

define('_MI_ISEARCH_OPT2', 'Count of keywords visible in the administration');
define('_MI_ISEARCH_OPT2_DSC', '');

define('_MI_ISEARCH_OPT3', 'List of IPs you don\'t want to record searches');
define('_MI_ISEARCH_OPT3_DSC', 'Type one IP per line and use the same conventions as the IPs ban in the general preferences of your site');

define('_MI_ISEARCH_BNAME1', 'Last search');
define('_MI_ISEARCH_BNAME2', 'Biggest users of the search');
define('_MI_ISEARCH_BNAME3', 'Statistics');
