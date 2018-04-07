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

use WideImage\Operation\AddNoise;

/**
 * Class IsearchBlacklist
 */
class Blacklist
{
    protected $keywords;    // Holds keywords

    /**
     * Get all the keywords
     * @todo move black_list.php file to XOOPS_UPLOAD_PATH . '/isearch/' directory
     *
     * @return array
     */
    public function getAllKeywords()
    {
        $ret      = $tbl_black_list = [];
        $myts     = \MyTextSanitizer::getInstance();
        $filename = XOOPS_UPLOAD_PATH . '/isearch_black_list.php';
        if (file_exists($filename)) {
            require_once $filename;
            foreach ($tbl_black_list as $onekeyword) {
                if ('' !== xoops_trim($onekeyword)) {
                    $onekeyword       = $myts->htmlSpecialChars($onekeyword);
                    $ret[$onekeyword] = $onekeyword;
                }
            }
        }
        asort($ret);
        $this->keywords = $ret;

        return $ret;
    }

    /**
     * Remove one or more keywords from the list
     *
     * @param string|array $keyword is the keyword(s) to remove
     *
     * @return void
     */
    public function delete($keyword)
    {
        if (is_array($keyword)) {
            foreach ($keyword as $onekeyword) {
                if (isset($this->keywords[$onekeyword])) {
                    unset($this->keywords[$onekeyword]);
                }
            }
        } else {
            if (isset($this->keywords[$keyword])) {
                unset($this->keywords[$keyword]);
            }
        }
    }

    /**
     * Add one or more keywords
     *
     * @param string|array $keyword is the keyword(s) to add
     *
     * @return void
     */
    public function addkeywords($keyword)
    {
        $myts = \MyTextSanitizer::getInstance();
        if (is_array($keyword)) {
            foreach ($keyword as $onekeyword) {
                $this->keywords[$onekeyword] = xoops_trim($myts->htmlSpecialChars($onekeyword));
            }
        } else {
            $this->keywords[$keyword] = xoops_trim($myts->htmlSpecialChars($keyword));
        }
    }

    /**
     * Remove, from a list, all the blacklisted words
     *
     * @param array $keywords
     * @return array
     */
    public function removeBlacklisted($keywords)
    {
        $ret       = [];
        $tmp_array = array_values($this->keywords);
        if (is_array($keywords) && count($keywords) > 0) {
            foreach ($keywords as $keyword) {
                $add = true;
                foreach ($tmp_array as $onebanned) {
                    if (!empty($onebanned) && preg_match('/' . $onebanned . '/i', $keyword)) {
                        $add = false;
                        break;
                    }
                }
                if ($add) {
                    $ret[] = $keyword;
                }
            }
        }

        return $ret;
    }

    /**
     * Save keywords to file system
     *
     * @todo move storage of this file to XOOPS_UPLOAD_PATH . '/isearch' directory
     *
     * @return void
     */
    public function store()
    {
        $filename = XOOPS_UPLOAD_PATH . '/isearch_black_list.php';
        if (file_exists($filename)) {
            unlink($filename);
        }
        $fd = fopen($filename, 'w') || exit('Error unable to create blacklist file');
        fwrite($fd, "<?php\n");
        fwrite($fd, '$tbl_black_list=array(' . "\n");
        foreach ($this->keywords as $onekeyword) {
            fwrite($fd, '"' . $onekeyword . "\",\n");
        }
        fwrite($fd, "'');\n");
        fwrite($fd, "?>\n");
        fclose($fd);
    }
}
