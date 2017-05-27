<?php
/**
 * ****************************************************************************
 * isearch - MODULE FOR XOOPS
 * Copyright (c) Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         isearch
 * @author             Herv� Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * Version : $Id:
 * ****************************************************************************
 */
function b_isearch_stats_show()
{
    include_once XOOPS_ROOT_PATH."/modules/isearch/include/functions.php";
    $isearch_handler = xoops_getmodulehandler('searches', 'isearch');
    $block = array();
    $visiblekeywords = isearch_getmoduleoption('showindex');
    if($visiblekeywords > 0) {
        $keywords_count = isearch_getmoduleoption('admincount');

        // Total keywords count
        $block['total_keywords'] = $isearch_handler->getCount();

        // Most searched elements
        $elements = $isearch_handler->getMostSearched(0,$keywords_count);
        foreach($elements as $keywordid => $datas) {
            $block['mostsearched'][]=array('keyword'=>$datas['keyword'],'count'=>$datas['count']);
        }
    }
    return $block;
}
