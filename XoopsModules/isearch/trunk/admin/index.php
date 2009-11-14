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

include_once '../../../include/cp_header.php';
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
include_once XOOPS_ROOT_PATH.'/modules/isearch/admin/functions.php';
include_once XOOPS_ROOT_PATH.'/modules/isearch/include/functions.php';


if (file_exists(XOOPS_ROOT_PATH.'/modules/isearch/language/' . $xoopsConfig['language'] . '/main.php')) {
	include_once XOOPS_ROOT_PATH.'/modules/isearch/language/' . $xoopsConfig['language'] . '/main.php';
} else {
	include_once XOOPS_ROOT_PATH.'/modules/isearch/language/english/main.php';
}

/**
 * Module's parameters
 */
$keywords_count = isearch_getmoduleoption('admincount');


// **********************************************************************************************************************************************
// **** Main
// **********************************************************************************************************************************************
$op = 'default';
if(isset($_POST['op'])) {
 $op = $_POST['op'];
} elseif(isset($_GET['op'])) {
	$op = $_GET['op'];
}
$isearch_handler =& xoops_getmodulehandler('searches', 'isearch');
$myts =& MyTextSanitizer::getInstance();

switch ($op) {
	/**
 	 * Remove datas by keyword or by date
 	 */
	case 'purge':
		include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
        xoops_cp_header();
        isearch_adminmenu(1);
        echo '<br />';
		$sform = new XoopsThemeForm(_AM_ISEARCH_PRUNE, 'pruneform', XOOPS_URL.'/modules/isearch/admin/index.php', 'post');
		$sform->addElement(new XoopsFormTextDateSelect(_AM_ISEARCH_PRUNE_DATE, 'prune_date',15,time()), false);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_PRUNE_KEYONLY, 'keyword', 50, 255, ''), false);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_IP, 'ip', 20, 255, ''), false);
		$sform->addElement(new XoopsFormHidden('op', 'ConfirmBeforeToPrune'), false);
		$button_tray = new XoopsFormElementTray(_AM_ISEARCH_PRUNE_DESC ,'');
		$submit_btn = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform->display();
		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;


	/**
 	 * Ask a confirmation before to remove keywords
 	 */
	case 'ConfirmBeforeToPrune':
        xoops_cp_header();
        isearch_adminmenu(1);
        echo '<br />';
		$criteria = new CriteriaCompo();

		$date='';
		$timestamp=0;
		$keyword='';
		$ip = '';

		if(isset($_POST['prune_date']) && xoops_trim($_POST['prune_date'])!='') {
			$date=$_POST['prune_date'];
			$timestamp=mktime(0,0,0,intval(substr($date,5,2)), intval(substr($date,8,2)), intval(substr($date,0,4)));
			$date=date('Y-m-d',$timestamp);
			$criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $date,'<='));
		}
		if(isset($_POST['keyword']) && xoops_trim($_POST['keyword'])!='') {
			$keyword = $_POST['keyword'];
			$criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']),'='));
		}
		if(isset($_POST['ip']) && xoops_trim($_POST['ip'])!='') {
			$ip = isset($_POST['ip']) ? $_POST['ip'] : '';
			$criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']),'='));
		}
		$count=0;
		$count=$isearch_handler->getCount($criteria);
		if($count>0) {
			$msg=sprintf(_AM_ISEARCH_PRUNE_CONFIRM,$count);
			xoops_confirm(array( 'op' => 'pruneKeywords', 'keyword' => $keyword, 'prune_date' => $timestamp, 'ip' => $ip,'ok' => 1), 'index.php', $msg);
		} else {
			printf(_AM_ISEARCH_NOTHING_PRUNE);
		}
		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;


	/**
 	 * Effectively delete keywords
 	 */
	case 'pruneKeywords':
		$timestamp = 0;
		$keyword = '';
		$ip = '';
		$criteria = new CriteriaCompo();

		if(isset($_POST['prune_date']) && intval($_POST['prune_date'])!=0) {
			$timestamp=$_POST['prune_date'];
			$date=date('Y-m-d',$timestamp);
			$criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $date,'<='));
		}
		if(isset($_POST['keyword']) && xoops_trim($_POST['keyword'])!='') {
			$keyword = $_POST['keyword'];
			$criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']),'='));
		}
		if(isset($_POST['ip']) && xoops_trim($_POST['ip'])!='') {
			$ip = isset($_POST['ip']) ? $_POST['ip'] : '';
			$criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']),'='));
		}

		if(intval($_POST['ok'])==1) {
			xoops_cp_header();
			$isearch_handler->deleteAll($criteria);
			redirect_header('index.php?op=purge', 2, _AM_ISEARCH_DBUPDATED);
		}
		break;


	/**
 	 * Remove a keyword from the database (directly called from the statistics part)
 	 */
	case 'removekeyword':
		xoops_cp_header();
		if(intval($_GET['id'])!=0) {
		    $tmp_search = $isearch_handler->get(intval($_GET['id']));
		    if(is_object($tmp_search)) {
				$critere = new Criteria('keyword', $tmp_search->getVar('keyword'),'=');
				$isearch_handler->deleteAll($critere);
			}
			unset($tmp_search);
		}
		redirect_header('index.php', 2, _AM_ISEARCH_DBUPDATED);
		break;


	/**
 	 * Export datas to a pure text file
 	 */
	case 'export':
        xoops_cp_header();
        isearch_adminmenu(2);
        echo '<br />';
		include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
        $min=$max='';
        $mint=$maxt=0;
        $isearch_handler->getMinMaxDate($min,$max);
		$mint=strtotime($min);
		$maxt=strtotime($max);

		$sform = new XoopsThemeForm(_AM_ISEARCH_EXPORT, 'exportform', XOOPS_URL.'/modules/isearch/admin/index.php', 'post');
		$dates_tray = new XoopsFormElementTray(_AM_ISEARCH_EXPORT_BETWEEN);
		$date1 = new XoopsFormTextDateSelect('', 'date1',15,$mint);
		$date2 = new XoopsFormTextDateSelect(_AM_ISEARCH_EXPORT_AND, 'date2',15,$maxt);
		$dates_tray->addElement($date1);
		$dates_tray->addElement($date2);
		$sform->addElement($dates_tray,false);
		$sform->addElement(new XoopsFormSelectUser(_AM_ISEARCH_USER,'user',true,'',5,true),false);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_KEYWORD, 'keyword', 50, 255, ''), false);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_IP, 'ip', 10, 32, ''), false);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_DATE_FORMAT, 'dateformat', 15, 255, _SHORTDATESTRING), true);
		$sform->addElement(new XoopsFormText(_AM_ISEARCH_DELIMITER, 'delimiter', 2, 255, ';'), true);
		$sform->addElement(new XoopsFormHidden('op', 'SearchExport'), false);
		$button_tray = new XoopsFormElementTray('' ,'');
		$submit_btn = new XoopsFormButton('', 'post', _SUBMIT, 'submit');
		$button_tray->addElement($submit_btn);
		$sform->addElement($button_tray);
		$sform->display();
		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;


	/**
 	 * Lauch the export
 	 */
	case 'SearchExport':
        xoops_cp_header();
        isearch_adminmenu(2);
        $criteria = new CriteriaCompo();
		$dateformat = isset($_POST['dateformat']) ? $_POST['dateformat'] : '';
		$delimiter = isset($_POST['delimiter']) ? $_POST['delimiter'] : ';';
		$searchfile=XOOPS_ROOT_PATH.'/uploads/isearch_keywords.txt';
		$searchfile2 =XOOPS_URL.'/uploads/isearch_keywords.txt';
		$tbl=array();

		if(isset($_POST['date1']) && isset($_POST['date2'])) {
			$startdate=date('Y-m-d',strtotime($_POST['date1']));
			$enddate=date('Y-m-d',strtotime($_POST['date2']));
			$criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $startdate,'>='));
			$criteria->add(new Criteria("date_format(datesearch,'%X-%m-%d')", $enddate,'<='));
		}
		if(isset($_POST['user']) && xoops_trim($_POST['user'])!='') {
			$criteria->add(new Criteria('uid', '('.implode(',', $_POST['user']).')','IN'));
		}
		if(isset($_POST['keyword']) && xoops_trim($_POST['keyword'])!='') {
			$criteria->add(new Criteria('keyword', $myts->addSlashes($_POST['keyword']),'='));
		}
		if(isset($_POST['ip']) && xoops_trim($_POST['ip'])!='') {
			$criteria->add(new Criteria('ip', $myts->addSlashes($_POST['ip']),'='));
		}
		$criteria->setSort('datesearch');
		$criteria->setOrder('desc');

		$tbl=$isearch_handler->getObjects($criteria);
		if(count($tbl)>0) {
			$fp = fopen($searchfile,'w');
			if(!$fp) {
				redirect_header('index.php',4,sprintf(_AM_ISEARCH_EXPORT_ERROR,$searchfile));
			}
			$tmpisearch = new searches();
			fwrite($fp,'id'.$delimiter.'date'.$delimiter.'keyword'.$delimiter.'uid'.$delimiter.'uname'.$delimiter.'ip'."\r\n");
			foreach($tbl as $onesearch) {
				fwrite($fp,$onesearch->getVar('isearchid').$delimiter.formatTimestamp(strtotime($onesearch->getVar('datesearch'))).$delimiter.$onesearch->getVar('keyword').$delimiter.$onesearch->getVar('uid').$delimiter.$tmpisearch->uname($onesearch->getVar('uid')).$delimiter.$onesearch->getVar('ip')."\r\n");
			}
			fclose($fp);
			printf(_AM_ISEARCH_EXPORT_READY,$searchfile2,XOOPS_URL.'/modules/isearch/admin/index.php?op=deletefile');
		} else {
			echo _AM_ISEARCH_NOTHING_TO_EXPORT;
		}
		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;


	/**
 	 * Delete the exported file
 	 */
	case 'deletefile':
		xoops_cp_header();
		$statfile=XOOPS_ROOT_PATH.'/uploads/isearch_keywords.txt';
		if(unlink($statfile)) {
			redirect_header('index.php', 2, _AM_ISEARCH_DELETED_OK);
		} else {
			redirect_header('index.php', 2, _AM_ISEARCH_DELETED_PB);
		}
		break;


	/**
 	 * Blacklist manager
 	 */
	case 'blacklist':
		xoops_cp_header();
		isearch_adminmenu(3);
		include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
		include_once XOOPS_ROOT_PATH.'/modules/isearch/class/blacklist.php';
		echo '<h3>'._AM_ISEARCH_BLACKLIST.'</h3>';
		$sform = new XoopsThemeForm(_AM_ISEARCH_BLACKLIST, 'MetagenBlackList', XOOPS_URL.'/modules/isearch/admin/index.php', 'post');
		$sform->addElement(new XoopsFormHidden('op', 'MetagenBlackList'), false);

		// Remove words
		$remove_tray = new XoopsFormElementTray(_AM_ISEARCH_BLACKLIST);
		$remove_tray->setDescription(_AM_ISEARCH_BLACKLIST_DESC);
		$blacklist=new XoopsFormSelect('', 'blacklist','',5,true);
		$words=array();
		$metablack = new isearch_blacklist();
		$words=$metablack->getAllKeywords();
		if(is_array($words) && count($words)>0) {
			foreach ($words as $key => $value) {
				$blacklist->addOption($key,$value);
			}
		}
		$blacklist->setDescription(_AM_ISEARCH_BLACKLIST_DESC);
		$remove_tray->addElement($blacklist,false);
		$remove_btn = new XoopsFormButton('', 'go', _AM_ISEARCH_DELETE, 'submit');
		$remove_tray->addElement($remove_btn,false);
		$sform->addElement($remove_tray);

		// Add some words
		$add_tray = new XoopsFormElementTray(_AM_ISEARCH_BLACKLIST_ADD);
		$add_tray->setDescription(_AM_ISEARCH_BLACKLIST_ADD_DSC);
		$add_field = new XoopsFormTextArea('', 'keywords', '', 5, 70);
		$add_tray->addElement($add_field,false);
		$add_btn = new XoopsFormButton('', 'go', _AM_ISEARCH_BLACKLIST_ADD, 'submit');
		$add_tray->addElement($add_btn,false);
		$sform->addElement($add_tray);
		$sform->display();
		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;


	/**
 	 * Add a word in the blacklist
 	 */
	case 'addblacklist':
		include_once XOOPS_ROOT_PATH.'/modules/isearch/class/blacklist.php';
		if(intval($_GET['id'])!=0) {
		    $tmp_search = $isearch_handler->get(intval($_GET['id']));
		    if(is_object($tmp_search)) {
				$keyword = $tmp_search->getVar('keyword');
				$blacklist = new isearch_blacklist();
				$keywords=$blacklist->getAllKeywords();
				$blacklist->addkeywords($keyword);
				$blacklist->store();
			}
		}
		redirect_header('index.php?op=stats', 2, _AM_ISEARCH_DBUPDATED);
		break;


	/**
 	 * Actions on the blacklist (add or remove keyword(s))
 	 */
	case 'MetagenBlackList':
		include_once XOOPS_ROOT_PATH.'/modules/isearch/class/blacklist.php';
		$blacklist = new isearch_blacklist();
		$keywords=$blacklist->getAllKeywords();

		if(isset($_POST['go']) && $_POST['go']==_AM_ISEARCH_DELETE) {
			foreach($_POST['blacklist'] as $black_id) {
				$blacklist->delete($black_id);
			}
			$blacklist->store();
		} else {
			if(isset($_POST['go']) && $_POST['go']==_AM_ISEARCH_BLACKLIST_ADD) {
				$p_keywords = $_POST['keywords'];
				$keywords = explode("\n",$p_keywords);
				foreach($keywords as $keyword) {
					if(xoops_trim($keyword)!='') {
						$blacklist->addkeywords(xoops_trim($keyword));
					}
				}
				$blacklist->store();
			}
		}
		redirect_header('index.php?op=blacklist', 2, _AM_ISEARCH_DBUPDATED);
		break;


	/**
	 * Remove content based on the IP
	 */
	 case 'removeip':
		xoops_cp_header();
		if(intval($_GET['id'])!=0) {
		    $tmp_search = $isearch_handler->get(intval($_GET['id']));
		    if(is_object($tmp_search)) {
				$critere = new Criteria('ip', $tmp_search->getVar('ip'),'=');
				$isearch_handler->deleteAll($critere);
			}
			unset($tmp_search);
		}
		redirect_header('index.php', 2, _AM_ISEARCH_DBUPDATED);
		break;


	/**
 	 * Default action, show statistics about keywords, users and many other things
 	 */
	case 'stats':
	default:
        xoops_cp_header();
        isearch_adminmenu(0);

        // Last x words (according to the module's option 'admincount') ***************************************************************************************
		$start = 0;
		$more_parameter = 'op=stats';
		if(isset($_GET['start1'])) {
			$start = intval($_GET['start1']);
		} elseif(isset($_SESSION['start1'])) {
				$start=intval($_SESSION['start1']);
		}
		$_SESSION['start1']=$start;
		$s_keyword = $s_uid = $s_ip = '';
		if(isset($_POST['s_keyword'])) {
			$s_keyword = $_POST['s_keyword'];
		} elseif(isset($_GET['s_keyword'])) {
			$s_keyword = $_GET['s_keyword'];
		}

		if(isset($_POST['s_uid'])) {
			$s_uid = $_POST['s_uid'];
		} elseif(isset($_GET['s_uid'])) {
			$s_uid = $_GET['s_uid'];
		}

		if(isset($_POST['s_ip'])) {
			$s_ip = $_POST['s_ip'];
		} elseif(isset($_GET['s_ip'])) {
			$s_ip = $_GET['s_ip'];
		}

		$critere = new CriteriaCompo();
		if($s_keyword != '') {
			$critere->add(new Criteria('keyword', $s_keyword,'LIKE'));
			$more_parameter .= '&s_keyword='.$s_keyword;
		}

		if($s_uid != '') {
			if(!is_numeric($s_uid)) {
				$member_handler =& xoops_gethandler('member');
				$crituser = new Criteria('uname', $s_uid,'LIKE');
				$tbl_users = array();
				$tbl_users = $member_handler->getUsers($crituser);
				if(count($tbl_users)>0) {
					$tbl_users2 = array();
					foreach($tbl_users as $one_user) {
						$tbl_users2[] = $one_user->getvar('uid');
					}
				}
				$users_list = '('.implode(',',$tbl_users2).')';
				$critere->add(new Criteria('uid', $users_list,'IN'));
			} else {
				$s_uid = intval($s_uid);
				$critere->add(new Criteria('uid', $s_uid,'='));
			}
			$more_parameter .= '&s_uid='.$s_uid;
		}

		if($s_ip != '') {
			$critere->add(new Criteria('ip', $s_ip,'LIKE'));
			$more_parameter .= '&s_ip='.$s_ip;
		}
		$critere->setSort('datesearch');
		$critere->setLimit($keywords_count);
		$critere->setStart($start);
		$critere->setOrder('DESC');

        // Total count of keywords
		$totalcount=$isearch_handler->getCount($critere);
		echo '<h3>'.sprintf(_AM_ISEARCH_STATS,$totalcount).'</h3>';

		$pagenav = new XoopsPageNav( $totalcount, $keywords_count, $start, 'start1', $more_parameter);
		$elements = $isearch_handler->getObjects($critere);
		isearch_collapsableBar('keywordscount', 'keywordscounticon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='keywordscounticon' name='keywordscounticon' src=" . XOOPS_URL . "/modules/isearch/images/close12.gif alt='' /></a>&nbsp;"._AM_ISEARCH_KEYWORDS."</h4>";
		echo "<div id='keywordscount'>";
		echo '<br />';
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr><th align='center'>"._AM_ISEARCH_ID."</th><th align='center'>"._AM_ISEARCH_KEYWORD."</th><th align='center'>"._AM_ISEARCH_DATE."</th><th align='center'>"._AM_ISEARCH_USER."</th><th align='center'>"._AM_ISEARCH_IP."</th><th align='center'>"._AM_ISEARCH_ACTION."</th></tr>";
		$class='';
		foreach($elements as $oneelement) {
			$class = ($class == 'even') ? 'odd' : 'even';
			$link1 = "<a href='".XOOPS_URL.'/search.php?query='.$oneelement->getVar('keyword')."&action=results' target='_blank'>".$oneelement->getVar('keyword')."</a>";
			$link2 = "<a href='".XOOPS_URL."/userinfo.php?uid=".$oneelement->getVar('uid')."'>".$oneelement->uname()."</a>";
			$action_del = "<a ".isearch_JavascriptLinkConfirm(_AM_ISEARCH_AREYOUSURE)." href='index.php?op=removekeyword&id=".$oneelement->getVar('isearchid')."' title='"._AM_ISEARCH_DELETE."'><img src='../images/delete.png' border='0' alt='"._AM_ISEARCH_DELETE."'></a>";
			$action_black = "<a ".isearch_JavascriptLinkConfirm(_AM_ISEARCH_AREYOUSURE)." href='index.php?op=addblacklist&id=".$oneelement->getVar('isearchid')."' title='"._AM_ISEARCH_BLACKLIST."'><img src='../images/list.png' border='0' alt='"._AM_ISEARCH_BLACKLIST."'></a>";
			$action_remove_ip = "<a ".isearch_JavascriptLinkConfirm(_AM_ISEARCH_AREYOUSURE)." href='index.php?op=removeip&id=".$oneelement->getVar('isearchid')."' title='"._AM_ISEARCH_IP."'><img src='../images/ip.png' border='0' alt='"._AM_ISEARCH_IP."'></a>";
			echo "<tr class='".$class."'><td align='center'>" . $oneelement->getVar('isearchid')."</td><td align='center'>" . $link1 . "</td><td align='center'>".formatTimestamp(strtotime($oneelement->getVar('datesearch')))."</td><td align='center'>".$link2."</td><td align='center'>".$oneelement->getVar('ip')."</td><td align='center'>".$action_del.'&nbsp;'.$action_black.'&nbsp;'.$action_remove_ip.'</td></tr>';
		}
		echo "<tr><form method='post' action='index.php'><th align='center'>"._AM_ISEARCH_FILTER_BY."</th><th align='center'><input type='text' name='s_keyword' value='".$s_keyword."' size='10' /></th><th align='center'></th><th align='center'><input type='text' name='s_uid' value='".$s_uid."' size='10' /></th><th align='center'><input type='text' name='s_ip' value='".$s_ip."' size='10' /></th><th align='center'><input type='submit' name='btngo_filter' value='"._GO."' /></th></form></tr>";
		echo "</table><div align='right'>".$pagenav->renderNav().'</div></div><br />';


		// Most searched words ********************************************************************************************************************************
		$start = 0;
		if(isset($_GET['start2'])) {
			$start = intval($_GET['start2']);
		} elseif(isset($_SESSION['start2'])) {
				$start=intval($_SESSION['start2']);
		}
		$_SESSION['start2']=$start;

		$pagenav = new XoopsPageNav($isearch_handler->getMostSearchedCount(), $keywords_count, $start, 'start2', 'op=stats');
		$elements = $isearch_handler->getMostSearched($start,$keywords_count);
		isearch_collapsableBar('mostsearch', 'mostsearchicon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='mostsearchicon' name='mostsearchicon' src=" . XOOPS_URL . "/modules/isearch/images/close12.gif alt='' /></a>&nbsp;"._AM_ISEARCH_MOST_SEARCH."</h4>";
		echo "<div id='mostsearch'>";
		echo '<br />';
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr><th align='center'>"._AM_ISEARCH_HITS."</th><th align='center'>"._AM_ISEARCH_KEYWORD."</th><th align='center'>"._AM_ISEARCH_ACTION."</th></tr>";
		$class='';
		foreach($elements as $onekeyword_id => $onekeyword_datas) {
		    $onekeyword = $onekeyword_datas['keyword'];
		    $onekeywordcount = $onekeyword_datas['count'];
			$class = ($class == 'even') ? 'odd' : 'even';
			$link1 = "<a href='".XOOPS_URL.'/search.php?query='.$onekeyword."&action=results' target='_blank'>".$onekeyword."</a>";
			$action_del = "<a ".isearch_JavascriptLinkConfirm(_AM_ISEARCH_AREYOUSURE)." href='index.php?op=removekeyword&id=".$onekeyword_id."' title='"._AM_ISEARCH_DELETE."'><img src='../images/delete.png' border='0' alt='"._AM_ISEARCH_DELETE."' /></a>";
			$action_black = "<a ".isearch_JavascriptLinkConfirm(_AM_ISEARCH_AREYOUSURE)." href='index.php?op=addblacklist&id=".$onekeyword_id."' title='"._AM_ISEARCH_BLACKLIST."'><img src='../images/list.png' border='0' alt='"._AM_ISEARCH_BLACKLIST."' /></a>";
			echo "<tr class='".$class."'><td align='center'>" . $onekeywordcount."</td><td align='center'>" . $link1 . "</td><td align='center'>".$action_del.'&nbsp;'.$action_black."</td></tr>";
		}
		echo "</table><div align='right'>".$pagenav->renderNav().'</div></div><br />';

		// Biggest users of the search ************************************************************************************************************************
		$tmpisearch = new searches();
		$start = 0;
		if(isset($_GET['start3'])) {
			$start = intval($_GET['start3']);
		} elseif(isset($_SESSION['start3'])) {
				$start=intval($_SESSION['start3']);
		}
		$_SESSION['start3']=$start;

		$pagenav = new XoopsPageNav($isearch_handler->getBiggestContributorsCount(), $keywords_count, $start, 'start3', 'op=stats');
		$elements = $isearch_handler->getBiggestContributors($start,$keywords_count);
		isearch_collapsableBar('bigcontribut', 'bigcontributicon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='bigcontributicon' name='bigcontributicon' src=" . XOOPS_URL . "/modules/isearch/images/close12.gif alt='' /></a>&nbsp;"._AM_ISEARCH_BIGGEST_USERS."</h4>";
		echo "<div id='bigcontribut'>";
		echo '<br />';
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr><th align='center'>"._AM_ISEARCH_USER."</th><th align='center'>"._AM_ISEARCH_HITS."</th></tr>";
		$class='';
		foreach($elements as $oneuser => $onecount) {
			$class = ($class == 'even') ? 'odd' : 'even';
			$link1 = "<a href='".XOOPS_URL."/userinfo.php?uid=".$oneuser."'>".$tmpisearch->uname($oneuser)."</a>";
			echo "<tr class='".$class."'><td align='center'>" . $link1."</td><td align='center'>" .$onecount. "</td></tr>";
		}
		echo "</table><div align='right'>".$pagenav->renderNav().'</div></div><br />';

        // daily stats ****************************************************************************************************************************************
		$start = 0;
		if(isset($_GET['start4'])) {
			$start = intval($_GET['start4']);
		} elseif(isset($_SESSION['start4'])) {
				$start=intval($_SESSION['start4']);
		}
		$_SESSION['start4']=$start;
		$pagenav = new XoopsPageNav($isearch_handler->getUniqueDaysCount(), $keywords_count, $start, 'start4', 'op=stats');
		$elements = $isearch_handler->GetCountPerDay($start,$keywords_count);
		isearch_collapsableBar('daystat', 'daystaticon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='daystaticon' name='daystaticon' src=" . XOOPS_URL . "/modules/isearch/images/close12.gif alt='' /></a>&nbsp;"._AM_ISEARCH_DAY_STATS."</h4>";
		echo "<div id='daystat'>";
		echo '<br />';
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr><th align='center'>"._AM_ISEARCH_DATE."</th><th align='center'>"._AM_ISEARCH_USE."</th></tr>";
		$class='';
		foreach($elements as $onedate => $onecount) {
			$class = ($class == 'even') ? 'odd' : 'even';
			$datefordisplay=formatTimestamp(strtotime($onedate),'s');
			echo "<tr class='".$class."'><td align='center'>" . $datefordisplay."</td><td align='center'>" .$onecount. "</td></tr>";
		}
		echo "</table><div align='right'>".$pagenav->renderNav().'</div></div><br />';

		// IP stats *******************************************************************************************************************************************
		$start = 0;
		if(isset($_GET['start4'])) {
			$start = intval($_GET['start4']);
		} elseif(isset($_SESSION['start4'])) {
				$start=intval($_SESSION['start4']);
		}
		$_SESSION['start4']=$start;

		$pagenav = new XoopsPageNav($isearch_handler->getIPsCount(), $keywords_count, $start, 'start4', 'op=stats');
		$elements = $isearch_handler->getIPs($start,$keywords_count);
		isearch_collapsableBar('ipcount', 'ipcounticon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='ipcounticon' name='ipcounticon' src=" . XOOPS_URL . "/modules/isearch/images/close12.gif alt='' /></a>&nbsp;"._AM_ISEARCH_IP."</h4>";
		echo "<div id='ipcount'>";
		echo '<br />';
		echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
		echo "<tr><th align='center'>"._AM_ISEARCH_IP."</th><th align='center'>"._AM_ISEARCH_HITS."</th></tr>";
		$class='';
		foreach($elements as $oneip => $onecount) {
			$class = ($class == 'even') ? 'odd' : 'even';
			echo "<tr class='".$class."'><td align='center'>" .$oneip."</td><td align='center'>" .$onecount. "</td></tr>";
		}
		echo "</table><div align='right'>".$pagenav->renderNav().'</div></div><br />';



		echo "<br /><div align='center'><a href='http://xoops.instant-zero.com' target='_blank'><img src='../images/instantzero.gif'></a></div>";
		break;
}
xoops_cp_footer();
?>

