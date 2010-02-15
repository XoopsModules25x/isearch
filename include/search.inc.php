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

if (!defined('XOOPS_ROOT_PATH')) {
	die("XOOPS root path not defined");
}

function isearch_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsUser;
	include_once XOOPS_ROOT_PATH.'/modules/isearch/include/functions.php';
	include_once XOOPS_ROOT_PATH.'/modules/isearch/class/blacklist.php';
	$isearch_handler =& xoops_getmodulehandler('searches', 'isearch');
	$banned = array();
	$banned = isearch_getmoduleoption('bannedgroups');
	$uid = 0;
	$datesearch = date('Y-m-d h:i:s');

	if (is_object($xoopsUser)) {
	    $groups = $xoopsUser->getGroups();
	    $uid = $xoopsUser->getVar('uid');
	} else {
		$groups = array(XOOPS_GROUP_ANONYMOUS);
	}

	// Check bad IPs
	$add = true;
	$badIps = array();
	$badIpsList = xoops_trim(isearch_getmoduleoption('remove_ip'));
	$countBadIps = 0;
	if($badIps != '') {
	    if(strstr($badIpsList, "\n") !== false) {
	        $badIps = implode("\n", $badIpsList);
	    } else {
	        $badIps[0] = $badIpsList;
	    }
	}

	if(count($badIps) > 0) {
	    $userIp = isearch_IP();
	    foreach($badIps as $badIp) {
	        if (!empty($badIp) && preg_match("/".$badIp."/", $userIp)) {
			    $add = false;
			    break;
	        }
	    }
	}
	if(!$add) {
	    return array();
	}

	$blacklist = new isearch_blacklist();
	$blacklist->getAllKeywords();	// Load keywords from blacklist
	$queryarray = $blacklist->remove_blacklisted($queryarray);
	$count = count($queryarray);
	if (count(array_intersect($groups, $banned)) == 0 && $userid == 0) {	// If it's not a banned user and if we are not viewing someone's profile
		if (is_array($queryarray) && $count >0) {
			for($i = 0; $i < $count; $i++) {
				$isearch = $isearch_handler->create(true);
				$isearch->setVar('uid',$uid);
				$isearch->setVar('datesearch',$datesearch);
				$isearch->setVar('keyword',$queryarray[$i]);
				$isearch_handler->insert($isearch);
			}
		}
	}
	return array();
}
?>