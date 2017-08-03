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
 * @package   modules\isearch\includes
 * @copyright Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @author    Hervé Thouzard of Instant Zero (http://www.instant-zero.com)
 *
 * ****************************************************************************
 */
use WideImage\Operation\AddNoise;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Returns a module's option
 *
 * @deprecated
 * @param string $option
 */
/*
function isearch_getmoduleoption($option, $repmodule='isearch')
{
    global $xoopsModuleConfig, $xoopsModule;
    static $tbloptions= Array();
    if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
        return $tbloptions[$option];
    }

    $retval=false;
    if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
        if(isset($xoopsModuleConfig[$option])) {
            $retval= $xoopsModuleConfig[$option];
        }

    } else {
        $moduleHandler = xoops_getHandler('module');
        $module = $moduleHandler->getByDirname($repmodule);
        $configHandler = xoops_getHandler('config');
        if ($module) {
            $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
            if(isset($moduleConfig[$option])) {
                $retval= $moduleConfig[$option];
            }
        }
    }
    $tbloptions[$option]=$retval;
    return $retval;
}
*/
/**
 * Create (in a link) a javascript confirmation box
 *
 * @param string $msg confirmation message
 *
 * @return string HTML 'onclick'
 */
function isearch_JavascriptLinkConfirm($msg)
{
    return "onclick=\"javascript:return confirm('".str_replace("'", ' ', $msg) . "')\"";
}

/**
 * Verify that a field exists inside a mysql table
 *
 * @author    Instant Zero (http://instant-zero.com/xoops)
 * @copyright (c) Instant Zero
 *
 * @todo filter fieldname and table
 *
 * @param string $fieldname column to search for
 * @param string $table dB table to use
 *
 * @return bool true if column exists in dB table
*/
function isearch_FieldExists($fieldname, $table)
{
    /** @var XoopsDatabase $GLOBALS['xoopsDB'] */
    $result = $GLOBALS['xoopsDB']->queryF("SHOW COLUMNS FROM $table LIKE '$fieldname'");
    return($GLOBALS['xoopsDB']->getRowsNum($result) > 0);
}

/**
 * Add a field to a mysql table
 *
 * @author Instant Zero (http://instant-zero.com/xoops)
 * @copyright (c) Instant Zero
 *
 * @param string $field table column to add
 * @param string $table dB table to use
 *
 * @return mysqli_result|bool query result or FALSE if successful
 *                      or TRUE if successful and no result
*/
function isearch_AddField($field, $table)
{
    /** @var XoopsDatabase $GLOBALS['xoopsDB'] */
    $result = $GLOBALS['xoopsDB']->queryF('ALTER TABLE ' . $table . " ADD $field;");
    return $result;
}

/**
 * Get current user IP
 *
 * @return string IP address (format Ipv4)
 */
function isearch_IP()
{
    $proxy_ip = '';
    if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else
        if (! empty($_SERVER['HTTP_X_FORWARDED'])) {
            $proxy_ip = $_SERVER['HTTP_X_FORWARDED'];
        } else
            if (! empty($_SERVER['HTTP_FORWARDED_FOR'])) {
                $proxy_ip = $_SERVER['HTTP_FORWARDED_FOR'];
            } else
                if (! empty($_SERVER['HTTP_FORWARDED'])) {
                    $proxy_ip = $_SERVER['HTTP_FORWARDED'];
                } else
                    if (! empty($_SERVER['HTTP_VIA'])) {
                        $proxy_ip = $_SERVER['HTTP_VIA'];
                    } else
                        if (! empty($_SERVER['HTTP_X_COMING_FROM'])) {
                            $proxy_ip = $_SERVER['HTTP_X_COMING_FROM'];
                        } else
                            if (! empty($_SERVER['HTTP_COMING_FROM'])) {
                                $proxy_ip = $_SERVER['HTTP_COMING_FROM'];
                            }
    $regs = array();
    if (! empty($proxy_ip) && ($is_ip = preg_match('/^([0-9]{1,3}\.){3,3}[0-9]{1,3}/', $proxy_ip, $regs)) && count($regs) > 0) {
        $the_IP = $regs[0];
    } else {
        $the_IP = $_SERVER['REMOTE_ADDR'];
    }
    return $the_IP;
}
