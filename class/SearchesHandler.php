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


/**
 * isearch Searches Handler
 */
class IsearchSearchesHandler extends XoopsPersistableObjectHandler
{
    /**
     * @param XoopsDatabase $db
     *
     * @return void
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'isearch_searches', Searches::class, 'isearchid');
    }

    /**
     * @deprecated
     * {@inheritDoc}
     * @see XoopsPersistableObjectHandler::create()
     */
    /*
    public function create($isNew = true)    {
        $searches = new searches();
        if ($isNew) {
            $searches->setNew();
        }
        return $searches;
    }
    */
    /**
     * @deprecated
     * {@inheritDoc}
     * @see XoopsObjectHandler::get()
     */
    /*
    public function get($id)    {
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
    */

    /**
     * insert object into dB
     *
     * {@inheritDoc}
     * @see XoopsObjectHandler::insert()
     *
     * @param \XoopsObject $searches
     * @param bool         $force true to force write
     *
     * @return bool|int insert status or new ID if successful insert
     */

    public function insert(\XoopsObject $searches, $force = true)
    {
        if (!$searches instanceof \Searches) {
            return false;
        }
        if (!$searches->isDirty()) {
            return true;
        }
        if (!$searches->cleanVars()) {
            foreach ($searches->getErrors() as $oneerror) {
                trigger_error($oneerror);
            }

            return false;
        }
        foreach ($searches->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($searches->isNew()) {
            $ip     = isearch_IP();
            $format = "INSERT INTO '%s' (isearchid, keyword, datesearch, uid, ip) VALUES ('%u', '%s', '%s', '%u', '%s')";
            $sql    = sprintf($format, $this->db->prefix('isearch_searches'), $this->db->genId($this->db->prefix('isearch_searches') . '_isearchid_seq'), $keyword, $datesearch, $uid, $this->db->quoteString($ip));
            $force  = true;
        } else {
            $format = "UPDATE '%s' SET keyword='%d', datesearch='%s', uid='%u', ip='%s' WHERE isearchid = '%u'";
            $sql    = sprintf($format, $this->db->prefix('isearch_searches'), $keyword, $datesearch, $uid, $this->db->quoteString($ip), $isearchid);
        }
        if (false !== $force) {
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


    /**
     * @deprecated
     * {@inheritDoc}
     * @see XoopsObjectHandler::delete()
     */
    /*
    public function delete($searches, $force = false)
    {
        if (get_class($searches) != 'searches') {
            return false;
        }
        $sql = sprintf("DELETE FROM `%s` WHERE isearchid = %u", $this->db->prefix('isearch_searches'), $searches->getVar('isearchid'));
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
    */

    /**
     *  Returns the lowest date and the higher date
     *
     * @param $min
     * @param $max
     * @return string &$min returns minimum date
     */
    public function getMinMaxDate(&$min, &$max)
    {
        $sql    = "SELECT min(date_format(datesearch, '%X-%m-%d')) AS mindate, max(date_format(datesearch, '%X-%m-%d')) AS maxdate FROM " . $this->db->prefix('isearch_searches');
        $result = $this->db->query($sql);
        list($min, $max) = $this->db->fetchRow($result);
    }

    /**
     * Count the number of unique days in the database
     *
     * @return int count of the number of unique days
     */
    public function getUniqueDaysCount()
    {
        $count  = 0;
        $sql    = "SELECT COUNT(DISTINCT(date_format(datesearch, '%X-%m-%d'))) AS cpt  FROM " . $this->db->prefix('isearch_searches');
        $result = $this->db->query($sql);
        list($count) = $this->db->fetchRow($result);

        return (int)$count;
    }

    /**
     * Returns the number of searches per day
     *
     * @param string $start the day to start searching
     * @param int    $limit
     *
     * @return array() count of searches grouped by day
     */
    public function getCountPerDay($start, $limit)
    {
        $ret    = [];
        $sql    = "SELECT COUNT(date_format(datesearch, '%X-%m-%d')) AS cpt, date_format(datesearch, '%X-%m-%d') AS shdate FROM " . $this->db->prefix('isearch_searches') . " GROUP BY date_format(datesearch, '%X-%m-%d') ORDER BY date_format(datesearch, '%X-%m-%d') DESC";
        $result = $this->db->query($sql, $limit, $start);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['shdate']] = $myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Get the unique number of different IPs
     *
     * @return int number of unique IP addresses
     */
    public function getIPsCount()
    {
        $sql    = 'SELECT COUNT(DISTINCT(ip)) AS cpt FROM ' . $this->db->prefix('isearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (is_array($myrow) && array_key_exists('cpt', $myrow)) ? (int)$myrow['cpt'] : 0;
    }

    /**
     * Returns IPs count
     *
     * @param int  $start     place to start in the Db
     * @param int  $limit     number of IPs to return
     * @param bool $id_as_key true return with object's ID as key
     *
     * @return array list of IPs
     */
    public function getIPs($start, $limit, $id_as_key = false)
    {
        $ret    = [];
        $sql    = 'SELECT COUNT(*) AS cpt, ip FROM ' . $this->db->prefix('isearch_searches') . ' GROUP BY ip ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['ip']] = $myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Get the unique number of people who used the search
     *
     * @return int count of different people who used search
     */
    public function getBiggestContributorsCount()
    {
        $sql    = 'SELECT COUNT(DISTINCT(uid)) AS cpt FROM ' . $this->db->prefix('isearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (is_array($myrow) && array_key_exists('cpt', $myrow)) ? (int)$myrow['cpt'] : 0;
    }

    /**
     * Returns users according to their use of the search
     *
     * @param int  $start     location of where to start the search
     * @param int  $limit     max number of users to return
     * @param bool $id_as_key array true - keys include the user's id
     *
     * @todo remove $id_as_key as it's not being used, no practical value should always be set to true
     *
     * @return array containing int count
     */
    public function getBiggestContributors($start, $limit, $id_as_key = false)
    {
        $ret    = [];
        $sql    = 'SELECT COUNT(*) AS cpt, uid FROM ' . $this->db->prefix('isearch_searches') . ' GROUP BY uid ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['uid']] = (int)$myrow['cpt'];
        }

        return $ret;
    }

    /**
     * Returns the number of unique keywords in the database
     *
     * @return int number of unique keywords
     */
    public function getMostSearchedCount()
    {
        $sql    = 'SELECT COUNT(DISTINCT(keyword)) AS cpt FROM ' . $this->db->prefix('isearch_searches');
        $result = $this->db->query($sql);
        $myrow  = $this->db->fetchArray($result);

        return (is_array($myrow) && array_key_exists('cpt', $myrow)) ? (int)$myrow['cpt'] : 0;
    }

    /**
     * Returns statistics about keywords, ordered on the number of time they are searched
     *
     * @param int  $start     where to start searching in the dB
     * @param int  $limit     number to return
     * @param bool $id_as_key use the isearch ID as the array key
     *
     * @todo remove $id_as_key as it isn't be used
     *
     * @return array either empty (none found) | array[] =>(['keyword']=>value, ['count']=>num of times searched))
     */
    public function getMostSearched($start, $limit, $id_as_key = false)
    {
        $ts     = \MyTextSanitizer::getInstance();
        $ret    = [];
        $sql    = 'SELECT COUNT(keyword) AS cpt, keyword, isearchid FROM ' . $this->db->prefix('isearch_searches') . ' GROUP BY keyword ORDER BY cpt DESC';
        $result = $this->db->query($sql, $limit, $start);
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['isearchid']] = ['keyword' => $ts->htmlSpecialChars($myrow['keyword']), 'count' => (int)$myrow['cpt']];
        }

        return $ret;
    }

    /**
     * @deprecated
     * @param Criteria $criteria
     * @param bool     $id_as_key
     *
     * @return array containing searches objects
     */
    /*
    public function getObjects($criteria = null, $id_as_key = false)
    {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('isearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
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
       while (false !== ($myrow = $this->db->fetchArray($result))) {
            $searches = new searches();
            $searches->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] = $searches;
            } else {
                $ret[$myrow['isearchid']] = $searches;
            }
            unset($searches);
        }
        return $ret;
    }
    */
    /**
     * @deprecated
     * @param Criteria $criteria
     */
    /*
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('isearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);
        return $count;
    }
    */
    /**
     * @deprecated
     * @param Criteria $criteria
     * @return bool success
     */
    /*
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM '.$this->db->prefix('isearch_searches');
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }
    */
}
