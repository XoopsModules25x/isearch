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
function b_isearch_big_user_show()
{
	include_once XOOPS_ROOT_PATH."/modules/isearch/include/functions.php";
	$isearch_handler =& xoops_getmodulehandler('searches', 'isearch');
	$block = array();
	$visiblekeywords = isearch_getmoduleoption('showindex');
	if($visiblekeywords > 0) {
		$tmpisearch = new searches();
		$keywords_count=isearch_getmoduleoption('admincount');

		// Total keywords count
		$block['total_keywords']=$isearch_handler->getCount();

		// Biggest users
		$elements = $isearch_handler->getBiggestContributors(0,$keywords_count);
		foreach($elements as $oneuser => $onecount) {
			$block['biggesusers'][]=array('uid'=>$oneuser,'uname'=>$tmpisearch->uname($oneuser),'count'=>$onecount);
		}
	}
	return $block;
}
?>