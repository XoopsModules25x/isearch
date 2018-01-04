<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Admin index file
 *
 * @package   module\Isearch\admin
 * @author    Raul Recio (aka UNFOR)
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2017 {@link https://xoops.org XOOPS Project}
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 *
 * @see       Xmf\Module\Admin
 */

require_once __DIR__ . '/admin_header.php';
xoops_cp_header();

/* @var Xmf\Module\Helper $isHelper */
$isearchHandler = $isHelper->getHandler('searches');
$totalSearches  = $isearchHandler->getCount();

$adminObject->addInfoBox(_MD_ISEARCH_SEARCH_CONF);
$adminObject->addInfoBoxLine(sprintf('<span class="infolabel">' . _MD_ISEARCH_TOTAL_SEARCHES . '</span>', '<span class="infotext green bold">' . $totalSearches . '</span>'));

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayIndex();

require_once __DIR__ . '/admin_footer.php';
