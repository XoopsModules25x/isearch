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

include_once XOOPS_ROOT_PATH."/class/xoopsobject.php";
include_once XOOPS_ROOT_PATH.'/modules/isearch/include/functions.php';

class searches extends XoopsObject
{
	function searches()
	{
		$this->initVar("isearchid",XOBJ_DTYPE_INT,null,false,10);
		$this->initVar("keyword",XOBJ_DTYPE_TXTBOX, null, false,100);
		$this->initVar("datesearch",XOBJ_DTYPE_TXTBOX, null, false,19);
		$this->initVar("uid",XOBJ_DTYPE_INT,null,false,10);
		$this->initVar("ip",XOBJ_DTYPE_TXTBOX, null, false,32);
	}

	/**
 	* Returns the user name for the current keyword (if the parameter is null)
 	*/
	function uname($uid=0)
	{
		global $xoopsConfig;
		static $tblusers = Array();
		$option=-1;
		if(empty($uid)) {
			$uid=$this->getVar('uid');
		}

		if(is_array($tblusers) && array_key_exists($uid,$tblusers)) {
			return 	$tblusers[$uid];
		}
		$tblusers[$uid]=XoopsUser::getUnameFromId($uid);
		return $tblusers[$uid];
	}

}

/**
* isearch Handler
*/
class IsearchSearchesHandler extends XoopsObjectHandler
{
	function &create($isNew = true)	{
		$searches = new searches();
		if ($isNew) {
			$searches->setNew();
		}
		return $searches;
	}

	function &get($id)	{
		$sql = 'SELECT * FROM '.$this->db->prefix('isearch_searches').' WHERE isearchid='.intval($id);
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		$numrows = $this->db->getRowsNum($result);
		if ($numrows == 1) {
			$searches = new searches();
			$searches->assignVars($this->db->fetchArray($result));
			return $searches;
		}
		return false;
	}

	function insert(&$searches, $force = false) {
		if (get_class($searches) != 'searches') {
			return false;
		}
		if (!$searches->isDirty()) {
			return true;
		}
		if (!$searches->cleanVars()) {
			foreach($searches->getErrors() as $oneerror) {
				trigger_error($oneerror);
			}
			return false;
		}
		foreach ($searches->cleanVars as $k => $v) {
				${$k} = $v;
		}
		if ($searches->isNew()) {
			$ip = isearch_IP();
			$format = "INSERT INTO %s (isearchid, keyword, datesearch, uid, ip) VALUES (%u, '%s', '%s', %u, %s)";
			$sql = sprintf($format ,$this->db->prefix('isearch_searches'),$this->db->genId($this->db->prefix("isearch_searches")."_isearchid_seq"),$keyword,$datesearch,$uid,$this->db->quoteString($ip));
			$force = true;
		} else {
			$format = "UPDATE %s SET keyword='%d', datesearch='%s', uid=%u, ip=%s WHERE isearchid = %u";
			$sql = sprintf($format, $this->db->prefix('isearch_searches'),$keyword,$datesearch,$uid, $this->db->quoteString($ip), $isearchid);
		}
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		if (empty($isearchid)) {
			$isearchid = $this->db->getInsertId();
		}
		$searches->assignVar('isearchid', $isearchid);
		return $isearchid;
	}


	function delete(&$searches, $force = false)
	{
		if (get_class($searches) != 'searches') {
			return false;
		}
		$sql = sprintf("DELETE FROM %s WHERE isearchid = %u", $this->db->prefix('isearch_searches'), $searches->getVar('isearchid'));
		if (false != $force) {
			$result = $this->db->queryF($sql);
		} else {
			$result = $this->db->query($sql);
		}
		if (!$result) {
			return false;
		}
		return true;
	}

	/**
	*  Returns the lowest date and the higher date
	*/
	function getMinMaxDate(&$min,&$max)
	{
		$sql = "SELECT min(date_format(datesearch,'%X-%m-%d')) as mindate, max(date_format(datesearch,'%X-%m-%d')) as maxdate FROM ".$this->db->prefix('isearch_searches');
		$result = $this->db->query($sql);
		list($min,$max) = $this->db->fetchRow($result);
	}

	/**
	* Count the number of unique days in the database
	*/
	function getUniqueDaysCount()
	{
		$count=0;
		$sql= "SELECT count(distinct(date_format(datesearch,'%X-%m-%d'))) as cpt  FROM ".$this->db->prefix('isearch_searches');
		$result = $this->db->query($sql);
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

	/**
	* Returns the number of searches per day
	*/
	function GetCountPerDay($start,$limit)
	{
		$ret=array();
		$sql="SELECT count(date_format(datesearch,'%X-%m-%d')) as cpt, date_format(datesearch,'%X-%m-%d') as shdate FROM ".$this->db->prefix('isearch_searches')." GROUP BY date_format(datesearch,'%X-%m-%d') ORDER BY date_format(datesearch,'%X-%m-%d') DESC";
		$result = $this->db->query($sql, $limit, $start);
		while ($myrow = $this->db->fetchArray($result)) {
			$ret[$myrow['shdate']]=$myrow['cpt'];
		}
		return $ret;
	}

	/**
	* Get the unique number of different IPs
	*/
	function getIPsCount()
	{
		$sql = "SELECT count(distinct(ip)) as cpt FROM ".$this->db->prefix('isearch_searches');
		$result = $this->db->query($sql);
		$myrow = $this->db->fetchArray($result);
		return $myrow['cpt'];
	}

	/**
	* Returns IPs count
	*/
	function getIPs($start, $limit, $id_as_key = false)
	{
		$ret=array();
		$sql = "SELECT Count(*) as cpt, ip FROM ".$this->db->prefix('isearch_searches')." GROUP BY ip ORDER BY cpt DESC";
		$result = $this->db->query($sql, $limit, $start);
		while ($myrow = $this->db->fetchArray($result)) {
			$ret[$myrow['ip']]=$myrow['cpt'];
		}
		return $ret;
	}


	/**
	* Get the unique number of people who used the search
	*/
	function getBiggestContributorsCount()
	{
		$sql = "SELECT count(distinct(uid)) as cpt FROM ".$this->db->prefix('isearch_searches');
		$result = $this->db->query($sql);
		$myrow = $this->db->fetchArray($result);
		return $myrow['cpt'];
	}

	/**
	* Returns users according to their use of the search
	*/
	function getBiggestContributors($start, $limit, $id_as_key = false)
	{
		$ret=array();
		$sql = "SELECT Count(*) as cpt, uid FROM ".$this->db->prefix('isearch_searches')." GROUP BY uid ORDER BY cpt DESC";
		$result = $this->db->query($sql, $limit, $start);
		while ($myrow = $this->db->fetchArray($result)) {
			$ret[$myrow['uid']]=$myrow['cpt'];
		}
		return $ret;
	}

	/**
	* Returns the number of unique keywords in the database
	*/
	function getMostSearchedCount()
	{
		$sql = "SELECT Count(distinct(keyword)) AS cpt FROM ".$this->db->prefix('isearch_searches');
		$result = $this->db->query($sql);
		$myrow = $this->db->fetchArray($result);
		return $myrow['cpt'];

	}

	/**
	* Returns statistics about keywords, ordered on the number of time they are searched
	*/
	function getMostSearched($start, $limit, $id_as_key = false)
	{
		$ts =& MyTextSanitizer::getInstance();
		$ret = array();
		$sql = "SELECT Count(keyword) AS cpt, keyword, isearchid FROM ".$this->db->prefix('isearch_searches').' GROUP BY keyword ORDER BY cpt desc';
		$result = $this->db->query($sql, $limit, $start);
		while ($myrow = $this->db->fetchArray($result)) {
			$ret[$myrow['isearchid']] = array('keyword' => $ts->htmlSpecialChars($myrow['keyword']), 'count' => $myrow['cpt']);
		}
		return $ret;
	}


	function &getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('isearch_searches');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		if ($criteria->getSort() != '') {
			$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
		}
		$limit = $criteria->getLimit();
		$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		while ($myrow = $this->db->fetchArray($result)) {
			$searches = new searches();
			$searches->assignVars($myrow);
			if (!$id_as_key) {
				$ret[] =& $searches;
			} else {
				$ret[$myrow['isearchid']] =& $searches;
			}
			unset($searches);
		}
		return $ret;
	}

	function getCount($criteria = null)
	{
		$sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('isearch_searches');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if (!$result) {
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

	function deleteAll($criteria = null)
	{
		$sql = 'DELETE FROM '.$this->db->prefix('isearch_searches');
		if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
			$sql .= ' '.$criteria->renderWhere();
		}
		if (!$result = $this->db->queryF($sql)) {
			return false;
		}
		return true;
	}
}
?>
