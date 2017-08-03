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
require_once __DIR__ . '/../../../include/cp_header.php';
xoops_cp_header();
require_once XOOPS_ROOT_PATH . '/modules/isearch/include/functions.php';

/* @var XoopsDatabase $GLOBALS ['xoopsDB'] */
/* @var XoopsUser $GLOBALS ['xoopsUser'] */

if (($GLOBALS['xoopsUser'] instanceof XoopsUser) && $GLOBALS['xoopsUser']->isAdmin($GLOBALS['xoopsModule']->mid())) {
    if (!isearch_FieldExists('ip', $GLOBALS['xoopsDB']->prefix('isearch_searches'))) {
        isearch_AddField("ip varchar(32) NOT NULL default ''", $GLOBALS['xoopsDB']->prefix('isearch_searches'));
    }
    echo '<br>' . _OK;
} else {
    printf("<h2>%s</h2>\n", _ERRORS);
}
xoops_cp_footer();
