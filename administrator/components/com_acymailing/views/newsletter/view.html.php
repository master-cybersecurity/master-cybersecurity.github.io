<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.6.2
 * @author	acyba.com
 * @copyright	(C) 2009-2014 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php


class NewsletterViewNewsletter extends acymailingView
{
	var $type = 'news';
	var $ctrl = 'newsletter';
	var $nameListing = 'NEWSLETTERS';
	var $nameForm = 'NEWSLETTER';
	var $icon = 'newsletter';
	var $aclCat = 'newsletters';
	var $doc = 'newsletter';

	function display($tpl = null)
	{
		$function = $this->getLayout();
		if(method_exists($this,$function)) $this->$function();

		parent::display($tpl);
	}

	function listing(){

		JHTML::_('behavior.modal','a.modal');

		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$config = acymailing_config();

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$pageInfo->filter->order->value = $app->getUserStateFromRequest( $paramBase.".filter_order", 'filter_order',	'a.mailid','cmd' );
		$pageInfo->filter->order->dir	= $app->getUserStateFromRequest( $paramBase.".filter_order_Dir", 'filter_order_Dir',	'desc',	'word' );
		$pageInfo->search = $app->getUserStateFromRequest( $paramBase.".search", 'search', '', 'string' );
		$pageInfo->search = JString::strtolower( $pageInfo->search );
		$selectedList = $app->getUserStateFromRequest( $paramBase."filter_list",'filter_list',0,'int');
		$selectedCreator = $app->getUserStateFromRequest( $paramBase."filter_creator",'filter_creator',0,'int');

		$pageInfo->limit->value = $app->getUserStateFromRequest( $paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int' );
		$pageInfo->limit->start = $app->getUserStateFromRequest( $paramBase.'.limitstart', 'limitstart', 0, 'int' );

		$database	= JFactory::getDBO();


		$searchMap = array('a.mailid','a.alias','a.subject','a.fromname','a.fromemail','a.replyname','a.replyemail','a.userid','b.name','b.username','b.email');
		$filters = array();
		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search,true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ",$searchMap)." LIKE $searchVal";
		}

		$filters[] = 'a.type = \''.$this->type.'\'';

		if(!empty($selectedList)) $filters[] = 'c.listid = '.$selectedList;
		if(!empty($selectedCreator)) $filters[] = 'a.userid = '.$selectedCreator;

		$selection = array_merge($searchMap,array('a.created','a.frequency','a.senddate','a.published','a.type','a.visible'));

		if(empty($selectedList)){
			if($app->isAdmin()){
				$query = 'SELECT '.implode(',',$selection).' FROM '.acymailing_table('mail').' as a';
				$queryCount = 'SELECT COUNT(a.mailid) FROM '.acymailing_table('mail').' as a';
			} else{
				$query = $query = 'SELECT DISTINCT '.implode(',',$selection).' FROM '.acymailing_table('listmail').' as c';
				$query .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
				$queryCount = 'SELECT COUNT(DISTINCT c.mailid) FROM '.acymailing_table('listmail').' as c';
				$queryCount .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
			}
		}else{
			$query = 'SELECT '.implode(',',$selection).' FROM '.acymailing_table('listmail').' as c';
			$query .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
			$queryCount = 'SELECT COUNT(c.mailid) FROM '.acymailing_table('listmail').' as c';
			$queryCount .= ' JOIN '.acymailing_table('mail').' as a on a.mailid = c.mailid ';
		}

		$query.= ' LEFT JOIN '.acymailing_table('users',false).' as b on a.userid = b.id ';
		$query.= ' WHERE ('.implode(') AND (',$filters).')';

		if(count($filters)>1) $queryCount.= ' LEFT JOIN '.acymailing_table('users',false).' as b on a.userid = b.id ';

		$queryCount.= ' WHERE ('.implode(') AND (',$filters).')';

		if(!$app->isAdmin()){
			$listClass = acymailing_get('class.list');
			$lists = $listClass->getFrontendLists();
			if(!empty($lists)){
				$frontListsIds = array();
				if(empty($selectedList)){
					foreach($lists as $oneList){
						$frontListsIds[] = $oneList->listid;
					}
					$query .= ' AND c.listid IN ('.implode(',',$frontListsIds).')';
					$queryCount .= ' AND c.listid IN ('.implode(',',$frontListsIds).')';
				}
			}
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$database->setQuery($query,$pageInfo->limit->start,$pageInfo->limit->value);
		$rows = $database->loadObjectList();

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search,$rows);
		}

		$database->setQuery($queryCount);
		$pageInfo->elements->total = $database->loadResult();

		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination( $pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value );

		if($app->isAdmin()){
			acymailing_setTitle(JText::_($this->nameListing),$this->icon,$this->ctrl);

			$bar = JToolBar::getInstance('toolbar');
			$buttonPreview = JText::_('ACY_PREVIEW');
			if($this->type == 'autonews'){
				JToolBarHelper::custom('generate', 'process', '',JText::_('GENERATE'),false);
			}elseif($this->type == 'news'){
				$buttonPreview.=' / '.JText::_('SEND');
			}

			JToolBarHelper::custom('preview', 'acypreview', '',$buttonPreview, true);

			JToolBarHelper::divider();
			JToolBarHelper::addNew();
			JToolBarHelper::editList();
			if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_delete','all'))) JToolBarHelper::deleteList(JText::_('ACY_VALIDDELETEITEMS'));
			JToolBarHelper::spacer();
			if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_copy','all'))) JToolBarHelper::custom( 'copy', 'copy.png', 'copy.png', JText::_('ACY_COPY') );
			if(acymailing_level(3)){
				$bar->appendButton( 'Acypopup', 'upload', JText::_('IMPORT'), "index.php?option=com_acymailing&ctrl=newsletter&task=upload&tmpl=component");
			}
			JToolBarHelper::divider();

			$bar->appendButton( 'Pophelp',$this->doc);
			if(acymailing_isAllowed($config->get('acl_cpanel_manage','all'))) $bar->appendButton( 'Link', 'acymailing', JText::_('ACY_CPANEL'), acymailing_completeLink('dashboard') );
		}

		$filters = new stdClass();

		if($app->isAdmin()){
			$listmailType = acymailing_get('type.listsmail');
			$listmailType->type = $this->type;
			$filters->list = $listmailType->display('filter_list',$selectedList);
		} else{
			$accessibleLists = array();
			$accessibleLists[] = JHTML::_('select.option', '0', JText::_('ALL_LISTS') );
			foreach($lists as $oneList){
				$accessibleLists[] = JHTML::_('select.option', $oneList->listid, $oneList->name );
			}
			$filters->list = JHTML::_('select.genericlist', $accessibleLists, 'filter_list', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int) $selectedList );
		}
		$mailcreatorType = acymailing_get('type.mailcreator');
		$mailcreatorType->type = $this->type;

		$filters->creator = $mailcreatorType->display('filter_creator',$selectedCreator);

		$this->assignRef('filters',$filters);
		$toggleClass = acymailing_get('helper.toggle');
		$this->assignRef('toggleClass',$toggleClass);
		$this->assignRef('rows',$rows);
		$this->assignRef('pageInfo',$pageInfo);
		$this->assignRef('pagination',$pagination);
		$delay = acymailing_get('type.delaydisp');
		$this->assignRef('delay',$delay);
		$this->assignRef('config',$config);
		$this->assign('app', $app);

		if($this->type == 'autonews'){
			$frequency = acymailing_get('type.frequency');
			$this->assignRef('frequencyType', $frequency);
		}
	}

	function form(){
		$app = JFactory::getApplication();
		$mailid = acymailing_getCID('mailid');
		$templateClass = acymailing_get('class.template');
		$config =& acymailing_config();

		$my = JFactory::getUser();
		if(!empty($mailid)){
			$mailClass = acymailing_get('class.mail');
			$mail = $mailClass->get($mailid);

			if(!empty($mail->tempid)){
				$myTemplate = $templateClass->get($mail->tempid);
			}
		}else{
			$mail = new stdClass();
			$mail->created = time();
			$mail->published = 0;
			if($this->type == 'followup') $mail->published = 1;
			$mail->visible = 1;
			$mail->html = 1;
			$mail->body = '';
			$mail->altbody = '';
			$mail->tempid = 0;

			$templateid = JRequest::getInt('templateid');
			if(empty($templateid) AND !empty($my->email)){
				$subscriberClass = acymailing_get('class.subscriber');
				$currentSubscriber = $subscriberClass->get($my->email);
				if(!empty($currentSubscriber->template)) $templateid = $currentSubscriber->template;
			}

			if(empty($templateid)){
				$myTemplate = $templateClass->getDefault();
			}else{
				$myTemplate = $templateClass->get($templateid);
			}

			if(!empty($myTemplate->tempid)){
				$mail->body = acymailing_absoluteURL($myTemplate->body);
				$mail->altbody = $myTemplate->altbody;
				$mail->tempid = $myTemplate->tempid;
				$mail->subject = $myTemplate->subject;
				$mail->replyname = $myTemplate->replyname;
				$mail->replyemail = $myTemplate->replyemail;
				$mail->fromname = $myTemplate->fromname;
				$mail->fromemail = $myTemplate->fromemail;
			}

			if($this->type == 'autonews'){
				$mail->frequency = 0;
			}

			if(!$app->isAdmin()){
				if($config->get('frontend_sender',0)){
					$mail->fromname = $my->name;
					$mail->fromemail = $my->email;
				}else{
					if(empty($mail->fromname)) $mail->fromname = $config->get('from_name');
					if(empty($mail->fromemail)) $mail->fromemail = $config->get('from_email');
				}

				if($config->get('frontend_reply',0)){
					$mail->replyname = $my->name;
					$mail->replyemail = $my->email;
				}else{
					if(empty($mail->replyname)) $mail->replyname = $config->get('reply_name');
					if(empty($mail->replyemail)) $mail->replyemail = $config->get('reply_email');
				}
			}
		}

		$sentbyname = '';
		if(!empty($mail->sentby)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT `name` FROM `#__users` WHERE `id`= '.intval($mail->sentby).' LIMIT 1');
			$sentbyname = $db->loadResult();
		}
		$this->assignRef('sentbyname',$sentbyname);

		if(JRequest::getVar('task','') == 'replacetags'){
			$mailerHelper = acymailing_get('helper.mailer');
			$templateClass = acymailing_get('class.template');
			$mail->template = $templateClass->get($mail->tempid);

			JPluginHelper::importPlugin('acymailing');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('acymailing_replacetags',array(&$mail,false));
			if(!empty($mail->altbody)) $mail->altbody = $mailerHelper->textVersion($mail->altbody,false);
		}

		$extraInfos = '';
		$values = new stdClass();
		if($this->type == 'followup'){
			$campaignid = JRequest::getInt('campaign',0);
			$extraInfos .= '&campaign='.$campaignid;

			$values->delay = acymailing_get('type.delay');
			$this->assignRef('campaignid',$campaignid);

		}else{
			$listmailClass = acymailing_get('class.listmail');
			$lists = $listmailClass->getLists($mailid);
		}

		if($app->isAdmin()){
			acymailing_setTitle(JText::_($this->nameForm),$this->icon,$this->ctrl.'&task=edit&mailid='.$mailid.$extraInfos);

			$bar = JToolBar::getInstance('toolbar');
			if(acymailing_isAllowed($config->get('acl_templates_view','all'))){
				$bar->appendButton( 'Acypopup', 'acytemplate', JText::_('ACY_TEMPLATE'), "index.php?option=com_acymailing&ctrl=template&task=theme&tmpl=component",750,550);
			}

			if(acymailing_isAllowed($config->get('acl_tags_view','all'))) $bar->appendButton( 'Acytags',$this->type);

			if(in_array($this->type,array('news','followup')) && acymailing_isAllowed($config->get('acl_tags_view','all'))){
				JToolBarHelper::custom('replacetags', 'replacetag', '',JText::_('REPLACE_TAGS'), false);
			}

			$buttonPreview = JText::_('ACY_PREVIEW');
			if($this->type=='news'){
				$buttonPreview .= ' / '.JText::_('SEND');
			}
			JToolBarHelper::divider();
			JToolBarHelper::custom('savepreview', 'acypreview', '',$buttonPreview, false);
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();
			JToolBarHelper::divider();
			$bar->appendButton( 'Pophelp',$this->doc);
		}

		$values->maxupload = (acymailing_bytes(ini_get('upload_max_filesize')) > acymailing_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize');


		$toggleClass = acymailing_get('helper.toggle');
		if(!$app->isAdmin()){
			$toggleClass->ctrl = 'frontnewsletter';
			$toggleClass->extra = '&listid='.JRequest::getInt('listid');

			$copyAllLists = $lists;
			foreach($copyAllLists as $listid => $oneList){
				if(!$oneList->published OR empty($my->id)){
					unset($lists[$listid]);
					continue;
				}
				if($oneList->access_manage == 'all') continue;
				if((int)$my->id == (int)$oneList->userid) continue;
				if(!acymailing_isAllowed($oneList->access_manage)){
					unset($lists[$listid]);
					continue;
				}
			}

			if(empty($lists)){
				$app = JFactory::getApplication();
				$app->enqueueMessage('You don\'t have the rights to add or edit an e-mail','error');
				$app->redirect(acymailing_completeLink('frontnewsletter',false,true));
			}

		}


		$editor = acymailing_get('helper.editor');
		$editor->setTemplate($mail->tempid);
		$editor->name = 'editor_body';
		$editor->content = $mail->body;
		$editor->prepareDisplay();

		$js = "function updateAcyEditor(htmlvalue){";
		$js .= 'if(htmlvalue == \'0\'){window.document.getElementById("htmlfieldset").style.display = \'none\'}else{window.document.getElementById("htmlfieldset").style.display = \'block\'}';
		$js .= '}';
		$js .='window.addEvent(\'load\', function(){ updateAcyEditor('.$mail->html.'); });';

		$script = 'function addFileLoader(){
		var divfile=window.document.getElementById("loadfile");
		var input = document.createElement(\'input\');
		input.type = \'file\';
		input.style.width = \'auto\';
		input.name = \'attachments[]\';
		divfile.appendChild(document.createElement(\'br\'));
		divfile.appendChild(input);}
		';

		if(!ACYMAILING_J16){
			$script .= 'function submitbutton(pressbutton){
						if (pressbutton == \'cancel\') {
							submitform( pressbutton );
							return;
						}';
		}else{
			$script .= 'Joomla.submitbutton = function(pressbutton) {
						if (pressbutton == \'cancel\') {
							Joomla.submitform(pressbutton,document.adminForm);
							return;
						}';
		}
		$script .= 'if(window.document.getElementById("subject").value.length < 2){alert(\''.JText::_('ENTER_SUBJECT',true).'\'); return false;}';
		$script .= $editor->jsCode();
		if(!ACYMAILING_J16){
			$script .= 'submitform( pressbutton );} ';
		}else{
			$script .= 'Joomla.submitform(pressbutton,document.adminForm);}; ';
		}

		$script .= "function changeTemplate(newhtml,newtext,newsubject,stylesheet,fromname,fromemail,replyname,replyemail,tempid){
			if(newhtml.length>2){".$editor->setContent('newhtml')."}
			var vartextarea =$('altbody'); if(newtext.length>2) vartextarea.innerHTML = newtext;
			document.getElementById('tempid').value = tempid;
			if(fromname.length>1){document.getElementById('fromname').value = fromname;}
			if(fromemail.length>1){document.getElementById('fromemail').value = fromemail;}
			if(replyname.length>1){document.getElementById('replyname').value = replyname;}
			if(replyemail.length>1){document.getElementById('replyemail').value = replyemail;}
			if(newsubject.length>1){document.getElementById('subject').value = newsubject;}
			".$editor->setEditorStylesheet('tempid')."
		}
		";

		if($mail->html == 1){
			$script .= "var zoneEditor = 'editor_body';";
		}else{
			$script .= "var zoneEditor = 'altbody';";
		}
		$script .= "
			var zoneToTag = 'altbody';
			function initTagZone(html){ if(html == 0){ zoneEditor = 'altbody'; }else{ zoneEditor = 'editor_body'; }}
		";

		$script .= "var previousSelection = false;
			function insertTag(tag){
				if(zoneEditor == 'editor_body'){
					try{
						jInsertEditorText(tag,'editor_body',previousSelection);
						return true;
					} catch(err){
						alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter');
						return false;
					}
				} else{
					try{
						simpleInsert(document.getElementById(zoneToTag), tag);
						return true;
					} catch(err){
						alert('Error inserting the tag in the '+ zoneToTag + 'zone. Please copy/paste it manually in your Newsletter.');
						return false;
					}
				}
			}
			";
			$script .= "function simpleInsert(myField, myValue) {
				if (document.selection) {
					myField.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
				} else if (myField.selectionStart || myField.selectionStart == '0') {
					var startPos = myField.selectionStart;
					var endPos = myField.selectionEnd;
					myField.value = myField.value.substring(0, startPos)
						+ myValue
						+ myField.value.substring(endPos, myField.value.length);
				} else {
					myField.value += myValue;
				}
			}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( $js.$script );

		if($this->type == 'autonews'){
			JHTML::_('behavior.modal','a.modal');
			$this->assign('frequencyType',acymailing_get('type.frequency'));
			$this->assign('generatingMode',acymailing_get('type.generatemode'));
		}

		$this->assignRef('toggleClass',$toggleClass);
		$this->assignRef('lists',$lists);
		$this->assignRef('editor',$editor);
		$this->assignRef('mail',$mail);
		$tabs = acymailing_get('helper.acytabs');
		$tabs->setOptions(array('useCookie' => true));

		$this->assignRef('tabs',$tabs);
		$this->assignRef('values',$values);
		$this->assignRef('config',$config);

	}

	function preview(){
		$app = JFactory::getApplication();
		$mailid = acymailing_getCID('mailid');
		$config = acymailing_config();

		JHTML::_('behavior.modal','a.modal');

		$mailerHelper = acymailing_get('helper.mailer');
		$mailerHelper->loadedToSend = false;
		$mail = $mailerHelper->load($mailid);

		$user = JFactory::getUser();
		$userClass = acymailing_get('class.subscriber');
		$receiver = $userClass->get($user->email);
		$mail->sendHTML = true;
		$mailerHelper->dispatcher->trigger('acymailing_replaceusertags',array(&$mail,&$receiver,false));
		if(!empty($mail->altbody)) $mail->altbody = $mailerHelper->textVersion($mail->altbody,false);

		$listmailClass = acymailing_get('class.listmail');
		$lists = $listmailClass->getReceivers($mail->mailid,true,false);

		$receiversClass = acymailing_get('type.testreceiver');

		$paramBase = ACYMAILING_COMPONENT.'.'.$this->getName();
		$infos = new stdClass();
		$infos->receiver_type = $app->getUserStateFromRequest( $paramBase.".receiver_type", 'receiver_type', '','string' );
		$infos->test_html = $app->getUserStateFromRequest( $paramBase.".test_html", 'test_html', 1,'int' );
		$infos->test_email = $app->getUserStateFromRequest( $paramBase.".test_email", 'test_email', '','string' );

		if($app->isAdmin()){
			acymailing_setTitle(JText::_('ACY_PREVIEW').' : '.$this->escape($mail->subject),$this->icon,$this->ctrl.'&task=preview&mailid='.$mailid);

			$bar = JToolBar::getInstance('toolbar');
			if($this->type == 'news'){
				if(acymailing_level(1) && acymailing_isAllowed($config->get('acl_newsletters_schedule','all'))){
					if($mail->published == 2){
						JToolBarHelper::custom('unschedule', 'unschedule', '',JText::_('UNSCHEDULE'), false);
					}else{
						$bar->appendButton( 'Acypopup', 'schedule', JText::_('SCHEDULE'), "index.php?option=com_acymailing&ctrl=send&task=scheduleready&tmpl=component&mailid=".$mailid);
					}
				}
				if(acymailing_isAllowed($config->get('acl_newsletters_send','all'))){
					$bar->appendButton( 'Acypopup', 'acysend', JText::_('SEND'), "index.php?option=com_acymailing&ctrl=send&task=sendready&tmpl=component&mailid=".$mailid);
				}
				JToolBarHelper::divider();
			}

				if(acymailing_isAllowed($config->get('acl_'.$this->aclCat.'_spam_test','all'))) $bar->appendButton( 'Acypopup', 'spamtest', JText::_('SPAM_TEST'), "index.php?option=com_acymailing&ctrl=send&task=spamtest&tmpl=component&mailid=".$mailid,1000,638);

			if($mail->html == 1){
				$bar->appendButton( 'Directprint');
			}
			JToolBarHelper::divider();

			JToolBarHelper::custom('edit', 'edit', '',JText::_('ACY_EDIT'), false);
			JToolBarHelper::cancel('cancel',JText::_('ACY_CLOSE'));
			JToolBarHelper::divider();
			$bar->appendButton( 'Pophelp',$this->doc);
			if(acymailing_isAllowed($config->get('acl_cpanel_manage','all'))) $bar->appendButton( 'Link', 'acymailing', JText::_('ACY_CPANEL'), acymailing_completeLink('dashboard') );
		}

		$this->assignRef('lists',$lists);
		$this->assignRef('infos',$infos);
		$this->assignRef('receiverClass',$receiversClass);
		$this->assignRef('mail',$mail);

		if($mail->html){
			$templateClass = acymailing_get('class.template');
			$templateClass->displayPreview('newsletter_preview_area',$mail->tempid,$mail->subject);
		}
	}

	function upload(){
	}
}
