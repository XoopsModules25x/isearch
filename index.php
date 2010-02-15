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

include "../../mainfile.php";
$xoopsOption['template_main'] = 'isearch_index.html';
include_once XOOPS_ROOT_PATH.'/header.php';
include_once XOOPS_ROOT_PATH."/modules/isearch/include/functions.php";
$isearch_handler =& xoops_getmodulehandler('searches', 'isearch');
$visiblekeywords=0;
$visiblekeywords=isearch_getmoduleoption('showindex');
$xoopsTpl->assign('visiblekeywords', $visiblekeywords);

if($visiblekeywords>0) {
	$totalcount=$isearch_handler->getCount();
	$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
	$critere=new Criteria('keyword');
	$critere->setSort('datesearch');
	$critere->setLimit($visiblekeywords);
	$critere->setStart($start);
	$critere->setOrder('DESC');
	include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
	$pagenav = new XoopsPageNav($totalcount, $visiblekeywords, $start, 'start', '');
	$xoopsTpl->assign('pagenav', $pagenav->renderNav());

	$elements = $isearch_handler->getObjects($critere);
	foreach($elements as $oneelement) {
		$xoopsTpl->append('keywords',array('keyword' => $oneelement->getVar('keyword'),'date' => formatTimestamp(strtotime($oneelement->getVar('datesearch')))));
	}
}

include_once(XOOPS_ROOT_PATH."/footer.php");
?>