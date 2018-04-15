<?php namespace XoopsModules\Isearch;

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
 * @package   modules\Isearch\class
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */

require_once XOOPS_ROOT_PATH . '/modules/isearch/include/functions.php';

class Searches extends \XoopsObject
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('isearchid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('keyword', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('datesearch', XOBJ_DTYPE_TXTBOX, null, false, 19);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, false, 32);
    }

    /**
     * Returns the user name for the current keyword (if the parameter is null)
     *
     * @param int $uid return user uname for requested id, if null|0|false then for this user
     *
     * @return string user|real name
     */
    public function uname($uid = 0)
    {
        static $tblusers = [];
        $option = -1;
        if (empty($uid)) {
            $uid = $this->getVar('uid');
        }

        if (is_array($tblusers) && array_key_exists($uid, $tblusers)) {
            return $tblusers[$uid];
        }
//        $isHelper    = Xmf\Module\Helper::getHelper(basename(dirname(__DIR__)));
        $isHelper    = \XoopsModules\Isearch\Helper::getInstance();
        $useUserName = $isHelper->getConfig('useusername', 0);
        /** @var XoopsUser $GLOBALS ['xoopsUser'] */
        $tblusers[$uid] = $GLOBALS['xoopsUser']::getUnameFromId($uid, $useUserName);

        return $tblusers[$uid];
    }
}
