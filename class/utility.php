<?php
/*
 iSearch Utility Class Definition

 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module:  iSearch
 *
 * @package   \module\isearch\class
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @copyright http://xoops.org 2001-2017 &copy; XOOPS Project
 * @author    zyspec
 * @author    Mamba
 * @since     File available since version 1.91
 */

 /**
  * IsearchUtility
  *
  * Static utility class to provide common functionality
  *
  */
class IsearchUtility
{
    /**
     *
     * Verifies XOOPS version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @param null|string        $requiredVer
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerXoops(XoopsModule $module = null, $requiredVer = null)
    {
        $moduleDirName = basename(dirname(__DIR__));
        if (null === $module) {
            $module        = XoopsModule::getByDirname($moduleDirName);
        }
        xoops_loadLanguage('admin', $moduleDirName);
        //check for minimum XOOPS version
        $currentVer = substr(XOOPS_VERSION, 6); // get the numeric part of string
        $currArray  = explode('.', $currentVer);
        if (null === $requiredVer) {
            $requiredVer = '' . $module->getInfo('min_xoops'); //making sure it's a string
        }
        $reqArray = explode('.', $requiredVer);
        $success  = true;
        foreach ($reqArray as $k => $v) {
            if (isset($currArray[$k])) {
                if ($currArray[$k] > $v) {
                    break;
                } elseif ($currArray[$k] == $v) {
                    continue;
                } else {
                    $success = false;
                    break;
                }
            } else {
                if ((int)$v > 0) { // handles things like x.x.x.0_RC2
                    $success = false;
                    break;
                }
            }
        }

        if (false === $success) {
            $module->setErrors(sprintf(_AM_ISEARCH_ERROR_BAD_XOOPS, $requiredVer, $currentVer));
        }

        return $success;
    }
    /**
     *
     * Verifies PHP version meets minimum requirements for this module
     * @static
     * @param XoopsModule $module
     *
     * @return bool true if meets requirements, false if not
     */
    public static function checkVerPhp(XoopsModule $module)
    {
        xoops_loadLanguage('admin', $module->dirname());
        // Check for minimum PHP version
        $success = true;
        $verNum  = PHP_VERSION;
        $reqVer  =& $module->getInfo('min_php');
        if ((false !== $reqVer) && ('' !== $reqVer)) {
            if (version_compare($verNum, (string)$reqVer, '<')) {
                $module->setErrors(sprintf(_AM_ISEARCH_ERROR_BAD_PHP, $reqVer, $verNum));
                $success = false;
            }
        }
        return $success;
    }
    /**
     *
     * Remove files and (sub)directories
     *
     * @param string $src source directory to delete
     *
     * @see Xmf\Module\Helper::getHelper()
     * @see Xmf\Module\Helper::isUserAdmin()
     *
     * @return bool true on success
     */
    public static function deleteDirectory($src)
    {
        // Only continue if user is a 'global' Admin
        if (!($GLOBALS['xoopsUser'] instanceof XoopsUser) || !$GLOBALS['xoopsUser']->isAdmin()) {
            return false;
        }

        $success = true;
        // Remove old files
        $dirInfo = new SplFileInfo($src);
        // Validate is a directory
        if ($dirInfo->isDir()) {
            $fileList = array_diff(scandir($src, SCANDIR_SORT_NONE), array('..', '.'));
            foreach ($fileList as $k => $v) {
                $fileInfo = new SplFileInfo($src . '/' . $v);
                if ($fileInfo->isDir()) {
                    // Recursively handle subdirectories
                    if (!$success = static::deleteDirectory($fileInfo->getRealPath())) {
                        break;
                    }
                } else {
                    // Delete the file
                    if (!($success = unlink($fileInfo->getRealPath()))) {
                        break;
                    }
                }
            }
            // Now delete this (sub)directory if all the files are gone
            if ($success) {
                $success = rmdir($dirInfo->getRealPath());
            }
        } else {
            // Input is not a valid directory
            $success = false;
        }
        return $success;
    }

    /**
     *
     * Recursively remove directory
     *
     * @todo currently won't remove directories with hidden files, should it?
     *
     * @param string $src directory to remove (delete)
     *
     * @return bool true on success
     */
    public static function rrmdir($src)
    {
        // Only continue if user is a 'global' Admin
        if (!($GLOBALS['xoopsUser'] instanceof XoopsUser) || !$GLOBALS['xoopsUser']->isAdmin()) {
            return false;
        }

        // If source is not a directory stop processing
        if (!is_dir($src)) {
            return false;
        }

        // Open the source directory to read in files
        $iterator = new DirectoryIterator($src);
        foreach ($iterator as $fObj) {
            if ($fObj->isFile()) {
                $filename = $fObj->getPathname();
                $fObj = null; // clear this iterator object to close the file
                if (!unlink($filename)) {
                    return false; // couldn't delete the file
                }
            } elseif (!$fObj->isDot() && $fObj->isDir()) {
                // Try recursively on directory
                static::rrmdir($fObj->getPathname());
            }
        }
        $iterator = null;   // clear iterator Obj to close file/directory
        return rmdir($src); // remove the directory & return results
    }

    /**
     * Recursively move files from one directory to another
     *
     * @param String $src - Source of files being moved
     * @param String $dest - Destination of files being moved
     *
     * @return bool true on success
     */
    public static function rmove($src, $dest)
    {
        // Only continue if user is a 'global' Admin
        if (!($GLOBALS['xoopsUser'] instanceof XoopsUser) || !$GLOBALS['xoopsUser']->isAdmin()) {
            return false;
        }

        // If source is not a directory stop processing
        if (!is_dir($src)) {
            return false;
        }

        // If the destination directory does not exist and could not be created stop processing
        if (!is_dir($dest) && !mkdir($dest, 0755)) {
            return false;
        }

        // Open the source directory to read in files
        $iterator = new DirectoryIterator($src);
        foreach ($iterator as $fObj) {
            if ($fObj->isFile()) {
                rename($fObj->getPathname(), $dest . '/' . $fObj->getFilename());
            } elseif (!$fObj->isDot() && $fObj->isDir()) {
                // Try recursively on directory
                static::rmove($fObj->getPathname(), $dest . '/' . $fObj->getFilename());
            }
        }
        $iterator = null;   // clear iterator Obj to close file/directory
        return rmdir($src); // remove the directory & return results
    }

    /**
     * Recursively copy directories and files from one directory to another
     *
     * @param string $src  - Source of files being moved
     * @param string $dest - Destination of files being moved
     *
     * @see Xmf\Module\Helper::getHelper()
     * @see Xmf\Module\Helper::isUserAdmin()
     *
     * @return bool true on success
     */
    public static function rcopy($src, $dest)
    {
        // Only continue if user is a 'global' Admin
        if (!($GLOBALS['xoopsUser'] instanceof XoopsUser) || !$GLOBALS['xoopsUser']->isAdmin()) {
            return false;
        }

        // If source is not a directory stop processing
        if (!is_dir($src)) {
            return false;
        }

        // If the destination directory does not exist and could not be created stop processing
        if (!is_dir($dest) && !mkdir($dest, 0755)) {
            return false;
        }

        // Open the source directory to read in files
        $iterator = new DirectoryIterator($src);
        foreach($iterator as $fObj) {
            if($fObj->isFile()) {
                copy($fObj->getPathname(), $dest . '/' . $fObj->getFilename());
            } else if(!$fObj->isDot() && $fObj->isDir()) {
                static::rcopy($fObj->getPathname(), $dest . '/' . $fObj-getFilename());
            }
        }
        return true;
    }
}
