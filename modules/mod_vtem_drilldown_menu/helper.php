<?php
/*------------------------------------------------------------------------
# mod_drilldown_menu - VTEM DrillDown Module
# ------------------------------------------------------------------------
# author Nguyen Van Tuyen
# copyright Copyright (C) 2010 VTEM.NET. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.vtem.net
# Technical Support: Forum - http://vtem.net/en/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die;
if($params->get('module_usage')){
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
}
class modDrillDownMenuHelper
{ 
    public static function hasChildren($id)
    {

        $mainframe = JFactory::getApplication();
        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
        $id = (int)$id;
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__k2_categories  WHERE parent={$id} AND published=1 AND trash=0 ";
        if (K2_JVERSION != '15')
        {
            $query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
            if ($mainframe->getLanguageFilter())
            {
                $languageTag = JFactory::getLanguage()->getTag();
                $query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
            }

        }
        else
        {
            $query .= " AND access <= {$aid}";
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum())
        {
            echo $db->stderr();
            return false;
        }

        if (count($rows))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function treerecurse(&$params, $id = 0, $level = 0, $begin = false)
    {

        static $output;
        if ($begin)
        {
            $output = '';
        }
        $mainframe = JFactory::getApplication();
        $root_id = (int)$params->get('root_id');
        $end_level = $params->get('end_level', NULL);
        $id = (int)$id;
        $catid = JRequest::getInt('id');
        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $user = JFactory::getUser();
        $aid = (int)$user->get('aid');
        $db = JFactory::getDBO();

        switch ($params->get('categoriesListOrdering'))
        {

            case 'alpha' :
                $orderby = 'name';
                break;

            case 'ralpha' :
                $orderby = 'name DESC';
                break;

            case 'order' :
                $orderby = 'ordering';
                break;

            case 'reversedefault' :
                $orderby = 'id DESC';
                break;

            default :
                $orderby = 'id ASC';
                break;
        }

        if (($root_id != 0) && ($level == 0))
        {
            $query = "SELECT * FROM #__k2_categories WHERE parent={$root_id} AND published=1 AND trash=0 ";

        }
        else
        {
            $query = "SELECT * FROM #__k2_categories WHERE parent={$id} AND published=1 AND trash=0 ";
        }

        if (K2_JVERSION != '15')
        {
            $query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
            if ($mainframe->getLanguageFilter())
            {
                $languageTag = JFactory::getLanguage()->getTag();
                $query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
            }

        }
        else
        {
            $query .= " AND access <= {$aid}";
        }

        $query .= " ORDER BY {$orderby}";

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum())
        {
            echo $db->stderr();
            return false;
        }

        if ($level < intval($end_level) || is_null($end_level))
        {
            $output .= '<ul class="vt_drilldownmenu vtem-drill-level'.$level.'">';
            foreach ($rows as $row)
            {
                if (($option == 'com_k2') && ($view == 'itemlist') && ($catid == $row->id))
                {
                    $active = ' class="activeCategory"';
                }
                else
                {
                    $active = '';
                }

                if (self::hasChildren($row->id))
                {
                    $output .= '<li'.$active.'><a href="'.urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($row->id.':'.urlencode($row->alias)))).'"><span class="catTitle">'.$row->name.'</span></a>';
                    self::treerecurse($params, $row->id, $level + 1);
                    $output .= '</li>';
                }
                else
                {
                    $output .= '<li'.$active.'><a href="'.urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($row->id.':'.urlencode($row->alias)))).'"><span class="catTitle">'.$row->name.'</span></a></li>';
                }
            }
            $output .= '</ul>';
        }

        return $output;
    }

	
	////////////////////////////////Menu/////////////////////////
	
	
	static function getList(&$params)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		// If no active menu, use default
		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();

		$user = JFactory::getUser();
		$levels = $user->getAuthorisedViewLevels();
		asort($levels);
		$key = 'menu_items'.$params.implode(',', $levels).'.'.$active->id;
		$cache = JFactory::getCache('mod_menu', '');
		if (!($items = $cache->get($key)))
		{
			// Initialise variables.
			$list		= array();
			$db			= JFactory::getDbo();

			$path		= $active->tree;
			$start		= (int) $params->get('startLevel');
			$end		= (int) $params->get('endLevel');
			$showAll	= $params->get('showAllChildren', 1);
			$items 		= $menu->getItems('menutype', $params->get('menutype'));

			$lastitem	= 0;

			if ($items) {
				foreach($items as $i => $item)
				{
					if (($start && $start > $item->level)
						|| ($end && $item->level > $end)
						|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))
						|| ($start > 1 && !in_array($item->tree[$start-2], $path))
					) {
						unset($items[$i]);
						continue;
					}

					$item->deeper = false;
					$item->shallower = false;
					$item->level_diff = 0;

					if (isset($items[$lastitem])) {
						$items[$lastitem]->deeper		= ($item->level > $items[$lastitem]->level);
						$items[$lastitem]->shallower	= ($item->level < $items[$lastitem]->level);
						$items[$lastitem]->level_diff	= ($items[$lastitem]->level - $item->level);
					}

					$item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);

					$lastitem			= $i;
					$item->active		= false;
					$item->flink = $item->link;

					// Reverted back for CMS version 2.5.6
					switch ($item->type)
					{
						case 'separator':
							// No further action needed.
							continue;

						case 'url':
							if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
								// If this is an internal Joomla link, ensure the Itemid is set.
								$item->flink = $item->link.'&Itemid='.$item->id;
							}
							break;

						case 'alias':
							// If this is an alias use the item id stored in the parameters to make the link.
							$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
							break;

						default:
							$router = JSite::getRouter();
							if ($router->getMode() == JROUTER_MODE_SEF) {
								$item->flink = 'index.php?Itemid='.$item->id;
							}
							else {
								$item->flink .= '&Itemid='.$item->id;
							}
							break;
					}

					if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
						$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
					}
					else {
						$item->flink = JRoute::_($item->flink);
					}

					$item->title = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
					$item->anchor_css   = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false);
					$item->anchor_title = htmlspecialchars($item->params->get('menu-anchor_title', ''), ENT_COMPAT, 'UTF-8', false);
					$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
				}

				if (isset($items[$lastitem])) {
					$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->level);
					$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->level);
					$items[$lastitem]->level_diff	= ($items[$lastitem]->level - ($start?$start:1));
				}
			}

			$cache->store($items, $key);
		}
		return $items;
	}
}
