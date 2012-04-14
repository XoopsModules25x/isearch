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

/**
 * Returns a module's option
 */
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
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->getByDirname($repmodule);
		$config_handler =& xoops_gethandler('config');
		if ($module) {
		    $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
	    	if(isset($moduleConfig[$option])) {
	    		$retval= $moduleConfig[$option];
	    	}
		}
	}
	$tbloptions[$option]=$retval;
	return $retval;
}

/**
 * Create (in a link) a javascript confirmation box
 */
function isearch_JavascriptLinkConfirm($msg)
{
	return "onclick=\"javascript:return confirm('".str_replace("'"," ",$msg)."')\"";
}

/**
 * Verify that a field exists inside a mysql table
 *
 * @package iSearch
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
*/
function isearch_FieldExists($fieldname,$table)
{
	global $xoopsDB;
	$result=$xoopsDB->queryF("SHOW COLUMNS FROM	$table LIKE '$fieldname'");
	return($xoopsDB->getRowsNum($result) > 0);
}

/**
 * Add a field to a mysql table
 *
 * @package iSearch
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
*/
function isearch_AddField($field, $table)
{
	global $xoopsDB;
	$result=$xoopsDB->queryF("ALTER TABLE " . $table . " ADD $field;");
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
    if (! empty($proxy_ip) && $is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}', $proxy_ip, $regs) && count($regs) > 0) {
        $the_IP = $regs[0];
    } else {
        $the_IP = $_SERVER['REMOTE_ADDR'];
    }
    return $the_IP;
}
?>