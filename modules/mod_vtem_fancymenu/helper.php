<?php
/**
* @Copyright Copyright (C) 2010 VTEM . All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @link     	http://www.vtem.net
**/
// no direct access
defined('_JEXEC') or die('Restricted access');

class modvtemmenuHelper {
    function GetMenu(&$params) {
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
        // If no active menu, use default
        $active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();
        $user = JFactory::getUser();
        $levels = $user->getAuthorisedViewLevels();
        asort($levels);
        $key = 'menu_items' . $params . implode(',', $levels) . '.' . $active->id;
        $cache = JFactory::getCache('mod_vtem_fancymenu', '');
        if (!($items = $cache->get($key))) {
            // Initialise variables.
            $list = array();
            $modules = array();
            $db = JFactory::getDbo();
            $document = JFactory::getDocument();

            // load the libraries
            jimport('joomla.application.module.helper');
            $path = isset($active) ? $active->tree : array();
            $start = (int) $params->get('startLevel');
            $end = (int) $params->get('endLevel');
            $items = $menu->getItems('menutype', $params->get('menutype'));

            // if no items in the menu then exit
            if (!$items)
                return false;

            $lastitem = 0;
            $modulesList = modvtemmenuHelper::CreateModulesList();// list all modules

            foreach ($items as $i => $item) {
                $isdependant = $params->get('dependantitems', false) ? ($start > 1 && !in_array($item->tree[$start - 2], $path)) : false;
                if (($start && $start > $item->level)
                        || ($end && $item->level > $end)
                        || $isdependant
                ) {
                    unset($items[$i]);
                    continue;
                }

                $item->deeper = false;
                $item->shallower = false;
                $item->level_diff = 0;

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->deeper = ($item->level > $items[$lastitem]->level);
                    $items[$lastitem]->shallower = ($item->level < $items[$lastitem]->level);
                    $items[$lastitem]->level_diff = ($items[$lastitem]->level - $item->level);
                }

                // Test if this is the last item
                $item->is_end = !isset($items[$i + 1]);

                $item->parent = (boolean) $menu->getItems('parent_id', (int) $item->id, true);
                $item->active = false;
                $item->flink = $item->link;

                switch ($item->type) {
                    case 'separator':
                        continue;// No further action needed.
                    case 'url':
                        if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
                            $item->flink = $item->link . '&Itemid=' . $item->id;
                        }
                        $item->flink = JFilterOutput::ampReplace(htmlspecialchars($item->flink));
                        break;

                    case 'alias':
                        $item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
                        break;
                    default:
                        $router = JSite::getRouter();
                        if ($router->getMode() == JROUTER_MODE_SEF) {
                            $item->flink = 'index.php?Itemid=' . $item->id;
                        } else {
                            $item->flink .= '&Itemid=' . $item->id;
                        }
                        break;
                }

                if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {
                    $item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
                } else {
                    $item->flink = JRoute::_($item->flink);
                }		

                //  ---------------- begin the module work on items --------------------
                $item->ftitle = htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8', false);
                $item->ftitle = JFilterOutput::ampReplace($item->ftitle);
                $parentItem = modvtemmenuHelper::getParentItem($item->parent_id, $items);
				$item->classe = htmlspecialchars($item->params->get('menu-anchor_css', ''), ENT_COMPAT, 'UTF-8', false) ? $item->params->get('menu-anchor_css', '').' ' : '';
				$item->menu_image   = $item->params->get('menu_image', '') ? htmlspecialchars($item->params->get('menu_image', ''), ENT_COMPAT, 'UTF-8', false) : '';
				if ($item->menu_image) {
						$item->params->get('menu_text', 1 ) ?
						$item->ftitle = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
						$item->ftitle = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
				}else { $item->ftitle = $item->title;
				}

                // ---- add some classes ----
                $item->classe .= ' item' . $item->id;
                if (isset($active) && $active->id == $item->id) {
                    $item->classe .= ' current';
                }
                if (is_array($path) &&
                        ( ($item->type == 'alias'
                        && in_array($item->params->get('aliasoptions'), $path))
                        || in_array($item->id, $path))) {
                    $item->classe .= ' active';
                    $item->active = true;
                }
                if ($item->deeper) {
                    $item->classe .= ' deeper';
                }
                if ($item->parent) {
                    if ($params->get('layout', 'default') != '_:flatlist')
                        $item->classe .= ' parent';
                }

                $item->classe .= $item->is_end ? ' last' : '';
                $item->classe .= ! isset($items[$i - 1]) ? ' first' : '';

                if (isset($items[$lastitem])) {
                    $items[$lastitem]->classe .= $items[$lastitem]->shallower ? ' last' : '';
                    $item->classe .= $items[$lastitem]->deeper ? ' first' : '';
                    if (isset($items[$i + 1]) AND $item->level - $items[$i + 1]->level > 1) {
                        $parentItem->classe .= ' last';
                    }
                }


                // ---- manage params ----
				$item->megaGroup = '';
                if (preg_match('/\[cols([0-9]+)\]/', $item->ftitle, $resultat)) {
                    $item->ftitle = preg_replace('/\[cols[0-9]+\]/', '', $item->ftitle);
                    $item->classe .= strval(" mega-cols" . $resultat[1]);
                }
                if (preg_match('/\[span([0-9]+)\]/', $item->ftitle, $resultat)) {
                    $item->ftitle = preg_replace('/\[span[0-9]+\]/', '', $item->ftitle);
                    $item->megaGroup = strval("span" . $resultat[1]);
                }

                // -- manage module --
                $style = 'xhtml';
                if (preg_match('/\[modid=([0-9]+)\]/', $item->ftitle, $resultat)) {
                    $item->ftitle = preg_replace('/\[modid=[0-9]+\]/', '', $item->ftitle);
                    $item->content = '<div class="vtemmenu_mod mod-content clearfix">' . modvtemmenuHelper::GenModuleById($resultat[1], $params, $modulesList, $style) . '</div>';
                }

                // -- manage rel attribute --
                $item->rel = '';
                if (preg_match('/\[rel=([a-z]+)\]/i', $item->ftitle, $resultat)) {
                    $item->ftitle = preg_replace('/\[rel=[a-z]+\]/i', '', $item->ftitle);
                    $item->rel = ' rel="' . $resultat[1] . '"';
                }

                // -- manage link description --
                
				$resultat = explode("||", $item->ftitle);
				if (isset($resultat[1])) {
					$item->ftitle = $resultat[0].'<i class="vt_desc">'.$resultat[1].'</i>';
				} else {
					$item->ftitle = $resultat[0];
				}

                $lastitem = $i;
            } // end of boucle for each items

            if (isset($items[$lastitem])) {
                $items[$lastitem]->deeper = (($start ? $start : 1) > $items[$lastitem]->level);
                $items[$lastitem]->shallower = (($start ? $start : 1) < $items[$lastitem]->level);
                $items[$lastitem]->level_diff = ($items[$lastitem]->level - ($start ? $start : 1));
            }

            $cache->store($items, $key);
        }
        return $items;
    }

    static function getParentItem($id, $items) {
        foreach ($items as $item) {
            if ($item->id == $id)
                return $item;
        }
    }

    static function GenModuleById($moduleid, $params, $modulesList, $style) {
        $attribs['style'] = $style;
        $modtitle = $modulesList[$moduleid]->title;
        $modname = $modulesList[$moduleid]->module;
        if (JModuleHelper::isEnabled($modname)) {
            $module = JModuleHelper::getModule($modname, $modtitle);
            return JModuleHelper::renderModule($module, $attribs);
        }
        return 'Module ID=' . $moduleid . ' not found !';
    }

    static function CreateModulesList() {
        $db = JFactory::getDBO();
        $query = "
			SELECT *
			FROM #__modules
			WHERE published=1
			ORDER BY id";
        $db->setQuery($query);
        $modulesList = $db->loadObjectList('id');
        return $modulesList;
    }
}

?>