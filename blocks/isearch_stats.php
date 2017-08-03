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
 * @package   modules\isearch\blocks
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */
function b_isearch_stats_show()
{
    $moduleDirName = basename(dirname(__DIR__));
    $isHelper      = Xmf\Module\Helper::getHelper($moduleDirName);

    include_once $isHelper->path('include/functions.php');

    $isearchHandler = $isHelper->getHandler('searches');

    $block = array();
    $visiblekeywords = $isHelper->getConfig('showindex', 10);
    if($visiblekeywords > 0) {
        $keywords_count = $isHelper->getConfig('admincount', 10);

        // Total keywords count
        $block['total_keywords'] = $isearchHandler->getCount();

        // Most searched elements
        $elements = $isearchHandler->getMostSearched(0,$keywords_count);
        foreach($elements as $keywordid => $datas) {
            $block['mostsearched'][] = array('keyword' => $datas['keyword'],
                                               'count' => $datas['count']
            );
        }
    }
    return $block;
}
