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
 * @package   modules\Isearch\frontside
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

require __DIR__ . '/../../mainfile.php';

$GLOBALS['xoopsOption']['template_main'] = 'isearch_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$moduleDirName = basename(__DIR__);
$isHelper      = Xmf\Module\Helper::getHelper($moduleDirName);

require_once $isHelper->path('include/functions.php');

$isearchHandler = $isHelper->getHandler('searches');

$visiblekeywords = $isHelper->getConfig('showindex', 10);
$xoopsTpl->assign('visiblekeywords', (int)$visiblekeywords);

if ((int)$visiblekeywords > 0) {
    $totalcount = $isearchHandler->getCount();
    $start      = isset($_GET['start']) ? (int)$_GET['start'] : 0;
    $critere    = new Criteria('keyword');
    $critere->setSort('datesearch');
    $critere->setLimit($visiblekeywords);
    $critere->setStart($start);
    $critere->setOrder('DESC');
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $pagenav = new XoopsPageNav($totalcount, $visiblekeywords, $start, 'start', '');
    $xoopsTpl->assign('pagenav', $pagenav->renderNav());

    $elements = $isearchHandler->getObjects($critere);
    foreach ($elements as $oneelement) {
        $xoopsTpl->append('keywords', [
            'keyword' => $oneelement->getVar('keyword'),
            'date'    => formatTimestamp(strtotime($oneelement->getVar('datesearch')))
        ]);
    }
}

require_once XOOPS_ROOT_PATH . '/footer.php';
