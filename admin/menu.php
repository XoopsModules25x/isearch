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
 * @package   modules\isearch\admin
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */
/*
$adminmenu[0]['title']     = _MI_ISEARCH_ADMMENU1;
$adminmenu[0]['link']     = "admin/index.php?op=stats";
$adminmenu[1]['title']     = _MI_ISEARCH_ADMMENU2;
$adminmenu[1]['link']     = "admin/index.php?op=purge";
$adminmenu[2]['title']     = _MI_ISEARCH_ADMMENU3;
$adminmenu[2]['link']     = "admin/index.php?op=export";
$adminmenu[3]['title']     = _MI_ISEARCH_ADMMENU4;
$adminmenu[3]['link']     = "admin/index.php?op=blacklist";
*/
use Xmf\Module\Admin;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$adminmenu = array(
    array('title' => _MI_ISEARCH_ADMENU0,
           'link' => 'admin/index.php',
           'desc' => _MI_ISEARCH_ADMIN_INDEX_DESC,
           'icon' => Admin::menuIconPath('home.png')
    ),
    array('title' => _MI_ISEARCH_ADMENU1,
           'link' => 'admin/main.php?op=stats',
           'desc' => _MI_ISEARCH_ADMIN_STATS_DESC,
           'icon' => Admin::menuIconPath('stats.png')
    ),
    array('title' => _MI_ISEARCH_ADMENU2,
           'link' => 'admin/main.php?op=purge',
           'desc' => _MI_ISEARCH_ADMIN_PURGE_DESC,
           'icon' => Admin::menuIconPath('delete.png')
    ),
    array('title' => _MI_ISEARCH_ADMENU3,
           'link' => 'admin/main.php?op=export',
           'desc' => _MI_ISEARCH_ADMIN_EXPORT_DESC,
           'icon' => Admin::menuIconPath('export.png')
    ),
    array('title' => _MI_ISEARCH_ADMENU4,
           'link' => 'admin/main.php?op=blacklist',
           'desc' => _MI_ISEARCH_ADMIN_BLACKLIST_DESC,
           'icon' => Admin::menuIconPath('metagen.png')
    ),
    array('title' => _MI_ISEARCH_ADMENU_ABOUT,
           'link' => 'admin/about.php',
           'desc' => _MI_ISEARCH_ADMIN_ABOUT_DESC,
           'icon' => Admin::menuIconPath('about.png')
    )
);
