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

class isearch_blacklist
{
	var $keywords;	// Holds keywords

	/**
 	 * Get all the kywords
 	 */
	function getAllKeywords()
	{
		$ret = $tbl_black_list = array();
		$myts =& MyTextSanitizer::getInstance();
		$filename=XOOPS_UPLOAD_PATH.'/isearch_black_list.php';
		if(file_exists($filename)) {
			include_once $filename;
			foreach($tbl_black_list as $onekeyword) {
				if(xoops_trim($onekeyword)!='') {
					$onekeyword=$myts->htmlSpecialChars($onekeyword);
					$ret[$onekeyword]=$onekeyword;
				}
			}
		}
		asort($ret);
		$this->keywords=$ret;
		return $ret;
	}

	/**
 	 * Remove one or many keywords from the list
 	 */
	function delete($keyword)
	{
		if(is_array($keyword)) {
			foreach($keyword as $onekeyword) {
				if(isset($this->keywords[$onekeyword])) {
					unset($this->keywords[$onekeyword]);
				}
			}
		} else {
			if(isset($this->keywords[$keyword])) {
				unset($this->keywords[$keyword]);
			}
		}
	}

	/**
 	 * Add one or many keywords
 	 */
	function addkeywords($keyword)
	{
		$myts =& MyTextSanitizer::getInstance();
		if(is_array($keyword)) {
			foreach($keyword as $onekeyword) {
				$onekeyword=xoops_trim($myts->htmlSpecialChars($onekeyword));
				$this->keywords[$onekeyword]=$onekeyword;
			}
		} else {
			$keyword=xoops_trim($myts->htmlSpecialChars($keyword));
			$this->keywords[$keyword]=$keyword;
		}

	}

	/**
 	 * Remove, from a list, all the blacklisted words
 	 */
	function remove_blacklisted($keywords)
	{
		$ret = array();
		$tmp_array = array_values($this->keywords);
		if( is_array($keywords) && count($keywords) > 0 ) {
			foreach ($keywords as $keyword) {
				$add = true;
				foreach($tmp_array as $onebanned) {
					if (!empty($onebanned) && preg_match("/".$onebanned."/i", $keyword)) {
						$add = false;
						break;
					}
				}
				if($add) $ret[] = $keyword;
			}
		}
		return $ret;
	}


	/**
 	 * Save keywords
 	 */
	function store()
	{
		$filename=XOOPS_UPLOAD_PATH.'/isearch_black_list.php';
		if(file_exists($filename)) {
			unlink($filename);
		}
		$fd=fopen($filename,'w') or die('Error unable to create blacklist file');
		fputs($fd,"<?php\n");
		fputs($fd,'$tbl_black_list=array('."\n");
		foreach($this->keywords as $onekeyword) {
			fputs($fd,"\"".$onekeyword."\",\n");
		}
		fputs($fd,"'');\n");
		fputs($fd,"?>\n");
		fclose($fd);
	}
}
?>