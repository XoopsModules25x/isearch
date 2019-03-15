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
 * iSearch installation scripts
 *
 * @package   module\Isearch\includes
 * @author    Taiwen Jiang <phppp@users.sourceforge.net>
 * @author    ZySpec <owners@zyspec.com>
 * @copyright https://xoops.org 2001-2017 XOOPS Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @link      https://xoops.org XOOPS
 * @since     1.91
 */

use XoopsModules\Isearch;

/**
 * @internal {Make sure you PROTECT THIS FILE}
 */

if ((!defined('XOOPS_ROOT_PATH'))
    || !($GLOBALS['xoopsUser'] instanceof \XoopsUser)
    || !($GLOBALS['xoopsUser']->isAdmin())) {
    exit('Restricted access' . PHP_EOL);
}

/**
 *
 * Prepares system prior to attempting to install module
 *
 * @param \XoopsModule $module
 *
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_install_isearch(\XoopsModule $module)
{
    /** @var \XoopsModules\Isearch\Utility $utility */
    $utility = new \XoopsModules\Isearch\Utility();

    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPHP($module);

    return $xoopsSuccess && $phpSuccess;
}

/**
 *
 * Performs tasks required during installation of the module
 *
 * @param \XoopsModule $module
 *
 * @return bool true if installation successful, false if not
 */
function xoops_module_install_isearch(\XoopsModule$module)
{
    return true;
}
