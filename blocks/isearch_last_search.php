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
function b_isearch_last_search_show()
{
    $moduleDirName = basename(dirname(__DIR__));
    $isHelper      = Xmf\Module\Helper::getHelper($moduleDirName);

    require_once $isHelper->path('include/functions.php');

    $isearchHandler = $isHelper->getHandler('searches');

    $block           = [];
    $visiblekeywords = $isHelper->getConfig('showindex', 10);

    if ($visiblekeywords > 0) {
        $block['visiblekeywords'] = $visiblekeywords;
        $totalcount               = $isearchHandler->getCount();
        $start                    = 0;
        $critere                  = new Criteria('isearchid', 0, '<>');
        $critere->setSort('datesearch');
        $critere->setLimit($visiblekeywords);
        $critere->setStart($start);
        $critere->setOrder('DESC');
        $tmpisearch = new searches();
        $elements   = $isearchHandler->getObjects($critere);
        foreach ($elements as $oneelement) {
            $search              = [
                'keyword' => $oneelement->getVar('keyword'),
                'date'    => formatTimestamp(strtotime($oneelement->getVar('datesearch'))),
                'uid'     => $oneelement->getVar('keyword'),
                'uname'   => $tmpisearch->uname($oneelement->getVar('uid')),
                'link'    => "<a href='" . XOOPS_URL . '/search.php?query=' . $oneelement->getVar('keyword') . "&action=results' target='_blank'>"
            ];
            $block['searches'][] = $search;
            unset($search);
        }
    }

    return $block;
}
