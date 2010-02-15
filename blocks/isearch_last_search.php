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
function b_isearch_last_search_show()
{
	include_once XOOPS_ROOT_PATH.'/modules/isearch/include/functions.php';
	$isearch_handler =& xoops_getmodulehandler('searches', 'isearch');
	$visiblekeywords = 0;
	$block = array();
	$visiblekeywords = isearch_getmoduleoption('showindex');
	if( $visiblekeywords > 0 ) {
		$block['visiblekeywords'] = $visiblekeywords;
		$totalcount=$isearch_handler->getCount();
		$start = 0;
		$critere =new Criteria('isearchid', 0, '<>');
		$critere->setSort('datesearch');
		$critere->setLimit($visiblekeywords);
		$critere->setStart($start);
		$critere->setOrder('DESC');
		$tmpisearch = new searches();
		$elements = $isearch_handler->getObjects($critere);
		foreach($elements as $oneelement) {
			$search = array();
			$search['keyword'] = $oneelement->getVar('keyword');
			$search['date'] = formatTimestamp(strtotime($oneelement->getVar('datesearch')));
			$search['uid'] = $oneelement->getVar('keyword');
			$search['uname'] = $tmpisearch->uname($oneelement->getVar('uid'));
			$search['link'] = "<a href='".XOOPS_URL.'/search.php?query='.$oneelement->getVar('keyword')."&action=results' target='_blank'>";
			$block['searches'][]=$search;
			unset($search);
		}
	}
	return $block;
}
?>