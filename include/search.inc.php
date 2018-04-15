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
 * @package   modules\Isearch\includes
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

use XoopsModules\Isearch;

defined('XOOPS_ROOT_PATH') || die('Restricted access');

function isearchSearch($queryarray, $andor, $limit, $offset, $userid)
{
    $moduleDirName = basename(dirname(__DIR__));
    $isHelper      = \XoopsModules\Isearch\Helper::getInstance();

    require_once $isHelper->path('include/functions.php');
    require_once $isHelper->path('class/blacklist.php');

    $isearchHandler = $isHelper->getHandler('Searches');

    $banned     = $isHelper->getConfig('bannedgroups', []);
    $uid        = 0;
    $datesearch = date('Y-m-d h:i:s');

    if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
        $groups =& $GLOBALS['xoopsUser']->getGroups();
        $uid    = $GLOBALS['xoopsUser']->getVar('uid');
    } else {
        $groups = [XOOPS_GROUP_ANONYMOUS];
    }

    // Check bad IPs
    $add         = true;
    $badIps      = [];
    $badIpsList  = $isHelper->getConfig('remove_ip', '');
    $countBadIps = 0;
    if ('' !== $badIps) {
        if (false !== strpos($badIpsList, "\n")) {
            $badIps = implode("\n", $badIpsList);
        } else {
            $badIps[0] = $badIpsList;
        }
    }

    if (count($badIps) > 0) {
        $userIp = isearch_IP();
        foreach ($badIps as $badIp) {
            if (!empty($badIp) && preg_match('/' . $badIp . '/', $userIp)) {
                $add = false;
                break;
            }
        }
    }
    if (!$add) {
        return [];
    }

    $blacklist = new Isearch\Blacklist();
    $blacklist->getAllKeywords();    // Load keywords from blacklist
    $queryarray = $blacklist->removeBlacklisted($queryarray);
    $count      = count($queryarray);
    if (0 == count(array_intersect($groups, $banned)) && 0 == $userid) {    // If it's not a banned user and if we are not viewing someone's profile
        if (is_array($queryarray) && $count > 0) {
            for ($i = 0; $i < $count; ++$i) {
                $isearch = $isearchHandler->create(true);
                $isearch->setVar('uid', $uid);
                $isearch->setVar('datesearch', $datesearch);
                $isearch->setVar('keyword', $queryarray[$i]);
                $isearchHandler->insert($isearch);
            }
        }
    }

    return [];
}
