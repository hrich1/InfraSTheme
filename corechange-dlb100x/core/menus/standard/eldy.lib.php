<?php
/* Copyright (C) 2010-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010      Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2012-2015 Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2015      Marcos García        <marcosgdf@gmail.com>
 * Copyright (C) 2018      Ferran Marcet        <fmarcet@2byte.es>
 * Copyright (C) 2018-2019  Frédéric France         <frederic.france@netlogic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *  \file		htdocs/core/menus/standard/eldy.lib.php
 *  \brief		Library for file eldy menus
 */
require_once DOL_DOCUMENT_ROOT.'/core/class/menubase.class.php';


/**
 * Core function to output top menu eldy
 *
 * @param 	DoliDB	$db				Database handler
 * @param 	string	$atarget		Target (Example: '' or '_top')
 * @param 	int		$type_user     	0=Menu for backoffice, 1=Menu for front office
 * @param  	array	$tabMenu        If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	Menu	$menu			Object Menu to return back list of menu entries
 * @param	int		$noout			1=Disable output (Initialise &$menu only).
 * @param	string	$mode			'top', 'topnb', 'left', 'jmobile'
 * @return	int						0
 */
function print_eldy_menu($db, $atarget, $type_user, &$tabMenu, &$menu, $noout = 0, $mode = '')
{
	global $user,$conf,$langs,$dolibarr_main_db_name;

	$mainmenu=(empty($_SESSION["mainmenu"])?'':$_SESSION["mainmenu"]);
	$leftmenu=(empty($_SESSION["leftmenu"])?'':$_SESSION["leftmenu"]);

	$id='mainmenu';
	$listofmodulesforexternal=explode(',', $conf->global->MAIN_MODULES_FOR_EXTERNAL);

	if (empty($noout)) print_start_menu_array();

    $usemenuhider = 1;

    // Show/Hide vertical menu. The hamburger icon for .menuhider action.
    if ($mode != 'jmobile' && $mode != 'topnb' && $usemenuhider && empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
	{
    	$showmode=1;
    	$classname = 'class="tmenu menuhider"';
    	$idsel='menu';

    	$menu->add('#', (! empty($conf->global->THEME_TOPMENU_DISABLE_IMAGE) ? '<span class="fa fa-bars"></span>' : ''), 0, $showmode, $atarget, "xxx", '', 0, $id, $idsel, $classname);
	}

    $menu_arr = array();
	// Home
	$menu_arr[] = array(
		'name' => 'Home',
		'link' => '/index.php?mainmenu=home&amp;leftmenu=home',
		'title' => (! empty($conf->global->THEME_TOPMENU_DISABLE_IMAGE) ? '<span class="fa fa-home"></span>' : "Home") ,
		'level' => 0,
		'enabled' => $showmode = 1,
		'target' => $atarget,
		'mainmenu' => "home",
		'leftmenu' => '',
		'position' => 10,
		'id' => $id,
		'idsel' => 'home',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "home" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "home" ) ? 0 : 1 ),
		'loadLangs' => array(),
		'submenus' => array(),
	);

	// Members
	$tmpentry = array(
	    'enabled' => (! empty($conf->adherent->enabled) ) ,
	    'perms' => (! empty($user->rights->adherent->lire) ),
	    'module' => 'adherent'
	);
	$menu_arr[] = array(
		'name' => 'Members',
		'link' => '/adherents/index.php?mainmenu=members&amp;leftmenu=',
		'title' => "MenuMembers",
		'level' => 0,
		'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "members",
		'leftmenu' => '',
		'position' => 18,
		'id' => $id,
		'idsel' => 'members',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "members" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "members" ) ? 0 : 1 ),
		'loadLangs' => array(),
		'submenus' => array(),
	);

	// Third parties
	$tmpentry = array(
	    'enabled'=> (	( ! empty($conf->societe->enabled) &&
	        ( empty($conf->global->SOCIETE_DISABLE_PROSPECTS) || empty($conf->global->SOCIETE_DISABLE_CUSTOMERS) )
	        )
	        || ! empty($conf->fournisseur->enabled)
	        ),
	    'perms'=> (! empty($user->rights->societe->lire) || ! empty($user->rights->fournisseur->lire)),
	    'module'=>'societe|fournisseur'
	);
	$menu_arr[] = array(
		'name' => 'Companies',
		'link' => '/societe/index.php?mainmenu=companies&amp;leftmenu=',
		'title' => "ThirdParties",
		'level' => 0,
		'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "companies",
		'leftmenu' => '',
		'position' => 30,	// InfraS change 20 to 30
		'id' => $id,
		'idsel' => 'companies',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "companies" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "companies" ) ? 0 : 1 ),
		'loadLangs' => array("companies","suppliers"),
		'submenus' => array(),
	);

	// Products-Services
	$tmpentry = array(
		'enabled'=> (! empty($conf->product->enabled) || ! empty($conf->service->enabled)),
		'perms'=> (! empty($user->rights->produit->lire) || ! empty($user->rights->service->lire)),
	    'module'=>'product|service'
	);
	$menu_arr[] = array(
		'name' => 'Products',
		'link' => '/product/index.php?mainmenu=products&amp;leftmenu=',
		'title' => (! empty($conf->product->enabled) && ! empty($conf->service->enabled))
					? ( "ProductsPipeServices" )	// InfraS change ( array("TMenuProducts" , " | " ,"TMenuServices") ) by ( "ProductsPipeServices" )
					: (! empty($conf->product->enabled)? "TMenuProducts" : "TMenuServices" ),
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "products",
		'leftmenu' => '',
		'position' => 20,	// InfraS change 30 to 20
		'id' => $id,
		'idsel' => 'products',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "products" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "products" ) ? 0 : 1 ),
		'loadLangs' => array("products"),
		'submenus' => array(),
	);

	// MRP
	$tmpentry = array(
	    'enabled'=>(! empty($conf->bom->enabled) || ! empty($conf->mrp->enabled)),
	    'perms'=>(! empty($user->rights->bom->read) || ! empty($user->rights->mrp->read)),
	    'module'=>'bom|mrp'
	);
	$menu_arr[] = array(
	    'name' => 'TMenuMRP',
	    'link' => '/mrp/index.php?mainmenu=mrp&amp;leftmenu=',
	    'title' => $langs->trans("TMenuMRP"),
	    'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
	    'target' => $atarget,
	    'mainmenu' => "mrp",
	    'leftmenu' => '',
	    'position' => 30,
	    'id' => $id,
	    'idsel' => 'mrp',
	    'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "mrp" ) ? 'class="tmenusel"' : 'class="tmenu"',
	    'prefix' => '',
	    'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "mrp" ) ? 0 : 1 ),
	    'loadLangs' => array("mrp"),
	    'submenus' => array(),
	);

	// Projects
	$tmpentry = array(
	    'enabled'=> ( ! empty($conf->projet->enabled) ? 1 : 0),
	    'perms'=> (! empty($user->rights->projet->lire) ? 1 : 0),
	    'module'=>'projet'
	);
	$menu_arr[] = array(
		'name' => 'Projet',
		'link' => '/projet/index.php?mainmenu=project&amp;leftmenu=',
		'title' => (empty($conf->global->PROJECT_USE_OPPORTUNITIES) || $conf->global->PROJECT_USE_OPPORTUNITIES == 2 )
					? (($conf->global->PROJECT_USE_OPPORTUNITIES == 2)?"Leads":"Projects")
					: "LeadsOrProjects",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "project",
		'leftmenu' => '',
		'position' => 40,	// InfraS change 35 to 40
		'id' => $id,
		'idsel' => 'project',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "project" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "project" ) ? 0 : 1 ),
		'loadLangs' => array("projects"),
		'submenus' => array(),
	);

	// Commercial
	$tmpentry = array(
	    'enabled'=>(! empty($conf->propal->enabled) ||
	        ! empty($conf->commande->enabled) ||
	        ! empty($conf->supplier_order->enabled) ||
	        ! empty($conf->supplier_proposal->enabled) ||
	        ! empty($conf->contrat->enabled) ||
	        ! empty($conf->ficheinter->enabled)
	        )?1:0,
	    'perms'=>(! empty($user->rights->societe->lire) || ! empty($user->rights->societe->contact->lire)),
	    'module'=>'propal|commande|supplier_order|contrat|ficheinter'
	);
	$menu_arr[] = array(
		'name' => 'Commercial',
		'link' => '/comm/index.php?mainmenu=commercial&amp;leftmenu=',
		'title' => "Commercial",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "commercial",
		'leftmenu' => '',
		'position' => 35,	// InfraS change 40 to 35
		'id' => $id,
		'idsel' => 'commercial',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "commercial" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "commercial" ) ? 0 : 1 ),
		'loadLangs' => array("commercial"),
		'submenus' => array(),
	);

	// Billing - Financial
	$tmpentry = array(
	    'enabled'=>(! empty($conf->facture->enabled) ||
	        ! empty($conf->don->enabled) ||
	        ! empty($conf->tax->enabled) ||
	        ! empty($conf->salaries->enabled) ||
	        ! empty($conf->supplier_invoice->enabled) ||
	        ! empty($conf->loan->enabled)
	        )?1:0,
	    'perms'=>(! empty($user->rights->facture->lire) || ! empty($user->rights->don->contact->lire)
	        || ! empty($user->rights->tax->charges->lire) || ! empty($user->rights->salaries->read)
	        || ! empty($user->rights->fournisseur->facture->lire) || ! empty($user->rights->loan->read)),
	    'module'=>'facture|supplier_invoice|don|tax|salaries|loan'
	);
	$menu_arr[] = array(
		'name' => 'Compta',
		'link' => '/compta/index.php?mainmenu=billing&amp;leftmenu=',
		'title' =>  "MenuFinancial",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "billing",
		'leftmenu' => '',
		'position' => 50,
		'id' => $id,
		'idsel' => 'billing',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "billing" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "billing" ) ? 0 : 1 ),
		'loadLangs' => array("compta"),
		'submenus' => array(),
	);

	// Bank
	$tmpentry = array(
	    'enabled'=>(! empty($conf->banque->enabled) || ! empty($conf->prelevement->enabled)),
	    'perms'=>(! empty($user->rights->banque->lire) || ! empty($user->rights->prelevement->lire)),
	    'module'=>'banque|prelevement'
	);
	$menu_arr[] = array(
		'name' => 'Bank',
		'link' => '/compta/bank/list.php?mainmenu=bank&amp;leftmenu=',
		'title' =>  "MenuBankCash",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "bank",
		'leftmenu' => '',
		'position' => 52,
		'id' => $id,
		'idsel' => 'bank',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "bank" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',

		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "bank" ) ? 0 : 1 ),

		'loadLangs' => array("compta","banks"),
		'submenus' => array(),
	);

	// Accounting
	$tmpentry = array(
	    'enabled'=>(! empty($conf->comptabilite->enabled) || ! empty($conf->accounting->enabled) || ! empty($conf->asset->enabled)),
	    'perms'=>(! empty($user->rights->compta->resultat->lire) || ! empty($user->rights->accounting->mouvements->lire) || ! empty($user->rights->asset->read)),
        'module'=>'comptabilite|accounting|asset'
	);
	$menu_arr[] = array(
		'name' => 'Accounting',
		'link' => '/accountancy/index.php?mainmenu=accountancy&amp;leftmenu=',
		'title' =>  "MenuAccountancy",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "accountancy",
		'leftmenu' => '',
		'position' => 54,
		'id' => $id,
		'idsel' => 'accountancy',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "accountancy" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',

		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "accountancy" ) ? 0 : 1 ),

		'loadLangs' => array("compta","accountancy","assets"),
		'submenus' => array(),
	);

	// HRM
	$tmpentry = array(
	    'enabled'=>(! empty($conf->hrm->enabled) || ! empty($conf->holiday->enabled) || ! empty($conf->deplacement->enabled) || ! empty($conf->expensereport->enabled)),
	    'perms'=>(! empty($user->rights->hrm->employee->read) || ! empty($user->rights->holiday->write) || ! empty($user->rights->deplacement->lire) || ! empty($user->rights->expensereport->lire)),
	    'module'=>'hrm|holiday|deplacement|expensereport'
	);
	$menu_arr[] = array(
		'name' => 'HRM',
		'link' => '/hrm/index.php?mainmenu=hrm&amp;leftmenu=',
		'title' =>  "HRM",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "hrm",
		'leftmenu' => '',
		'position' => 80,
		'id' => $id,
		'idsel' => 'hrm',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "hrm" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',

		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "hrm" ) ? 0 : 1 ),

		'loadLangs' => array("holiday"),
		'submenus' => array(),
	);
	/* InfraS move to line 473
	// Tools
	$tmpentry = array(
	    'enabled'=>1,
	    'perms'=>1,
	    'module'=>''
	);
	$menu_arr[] = array(
		'name' => 'Tools',
		'link' => '/core/tools.php?mainmenu=tools&amp;leftmenu=',
		'title' =>  "Tools",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "tools",
		'leftmenu' => '',
		'position' => 90,
		'id' => $id,
		'idsel' => 'tools',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',

		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools" ) ? 0 : 1 ),

		'loadLangs' => array("other"),
		'submenus' => array(),
	);

	// Add menus
	foreach($menu_arr as $key => $smenu)
	{
		$smenu = (object) $smenu;

		if ($smenu->enabled)
		{
			if ($smenu->session)
			{
				$_SESSION['idmenu']='';
			}

			// Load Langue
			if(! empty($smenu->loadLangs))
			{
				$langs->loadLangs($smenu->loadLangs);
			}

			// Trans title
			$mtitle = '';
			if(is_array($smenu->title))
			{
				foreach($smenu->title as $item)
				{
					$mtitle .=  $langs->trans($item);
				}
			}
			else
			{
				$mtitle =  $langs->trans($smenu->title);
			}
			// Add item
			$menu->add($smenu->link, $mtitle, $smenu->level, $smenu->enabled, $smenu->target, $smenu->mainmenu, $smenu->leftmenu, $smenu->position, $smenu->id, $smenu->idsel, $smenu->classname, $smenu->prefix);
		}
	}
	*/
	// Show personalized menus
	$menuArbo = new Menubase($db, 'eldy');
	$newTabMenu = $menuArbo->menuTopCharger('', '', $type_user, 'eldy', $tabMenu);	// Return tabMenu with only top entries

	$num = count($newTabMenu);
	$lastpos	= 0;	// Ajout InfraS
	for($i = 0; $i < $num; $i++)
	{
		$idsel=(empty($newTabMenu[$i]['mainmenu'])?'none':$newTabMenu[$i]['mainmenu']);

		$showmode=isVisibleToUserType($type_user, $newTabMenu[$i], $listofmodulesforexternal);
		if ($showmode == 1)
		{
			$substitarray = array('__LOGIN__' => $user->login, '__USER_ID__' => $user->id, '__USER_SUPERVISOR_ID__' => $user->fk_user);
			$substitarray['__USERID__'] = $user->id;	// For backward compatibility
			$newTabMenu[$i]['url'] = make_substitutions($newTabMenu[$i]['url'], $substitarray);

		    // url = url from host, shorturl = relative path into dolibarr sources
			$url = $shorturl = $newTabMenu[$i]['url'];
			if (! preg_match("/^(http:\/\/|https:\/\/)/i", $newTabMenu[$i]['url']))	// Do not change url content for external links
			{
				$tmp=explode('?', $newTabMenu[$i]['url'], 2);
				$url = $shorturl = $tmp[0];
				$param = (isset($tmp[1])?$tmp[1]:'');

				if (! preg_match('/mainmenu/i', $param) || ! preg_match('/leftmenu/i', $param)) $param.=($param?'&':'').'mainmenu='.$newTabMenu[$i]['mainmenu'].'&amp;leftmenu=';
				//$url.="idmenu=".$newTabMenu[$i]['rowid'];    // Already done by menuLoad
				$url = dol_buildpath($url, 1).($param?'?'.$param:'');
				//$shorturl = $shorturl.($param?'?'.$param:'');
                $shorturl = $url;
				if (DOL_URL_ROOT) $shorturl = preg_replace('/^'.preg_quote(DOL_URL_ROOT, '/').'/', '', $shorturl);
			}

			// Define the class (top menu selected or not)
			if (! empty($_SESSION['idmenu']) && $newTabMenu[$i]['rowid'] == $_SESSION['idmenu']) $classname='class="tmenusel"';
			elseif (! empty($_SESSION["mainmenu"]) && $newTabMenu[$i]['mainmenu'] == $_SESSION["mainmenu"]) $classname='class="tmenusel"';
			else $classname='class="tmenu"';
		}
		elseif ($showmode == 2) $classname='class="tmenu"';

		$menu->add($shorturl, $newTabMenu[$i]['titre'], 0, $showmode, ($newTabMenu[$i]['target']?$newTabMenu[$i]['target']:$atarget), ($newTabMenu[$i]['mainmenu']?$newTabMenu[$i]['mainmenu']:$newTabMenu[$i]['rowid']), ($newTabMenu[$i]['leftmenu']?$newTabMenu[$i]['leftmenu']:''), $newTabMenu[$i]['position'], $id, $idsel, $classname);
		$lastpos	= $newTabMenu[$i]['position'] > $lastpos ? $newTabMenu[$i]['position'] : $lastpos;	// Ajout InfraS
	}
	// InfraS move from line 366
	// Tools
	$tmpentry = array(
	    'enabled'=>1,
	    'perms'=>1,
	    'module'=>''
	);
	$menu_arr[] = array(
		'name' => 'Tools',
		'link' => '/core/tools.php?mainmenu=tools&amp;leftmenu=',
		'title' =>  "Tools",
		'level' => 0,
	    'enabled' => $showmode = isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal),
		'target' => $atarget,
		'mainmenu' => "tools",
		'leftmenu' => '',
		'position' => $lastpos + 5,	// InfraS change 90 to $lastpos + 5
		'id' => $id,
		'idsel' => 'tools',
		'classname' =>  $classname = ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools" ) ? 'class="tmenusel"' : 'class="tmenu"',
		'prefix' => '',
		'session' => ( ( $_SESSION["mainmenu"] && $_SESSION["mainmenu"] == "tools" ) ? 0 : 1 ),
		'loadLangs' => array("other"),
		'submenus' => array(),
	);
	// Add menus
	foreach($menu_arr as $key => $smenu)
	{
		$smenu = (object) $smenu;
		if ($smenu->enabled)
		{
			if ($smenu->session)
			{
				$_SESSION['idmenu']='';
			}
			// Load Langue
			if(! empty($smenu->loadLangs))
			{
				$langs->loadLangs($smenu->loadLangs);
			}
			// Trans title
			$mtitle = '';
			if(is_array($smenu->title))
			{
				foreach($smenu->title as $item)
				{
					$mtitle .=  $langs->trans($item);
				}
			}
			else
			{
				$mtitle =  $langs->trans($smenu->title);
			}
			// Add item
			$menu->add($smenu->link, $mtitle, $smenu->level, $smenu->enabled, $smenu->target, $smenu->mainmenu, $smenu->leftmenu, $smenu->position, $smenu->id, $smenu->idsel, $smenu->classname, $smenu->prefix);
		}
	}

	// Sort on position
	$menu->liste = dol_sort_array($menu->liste, 'position');

    // Output menu entries
    if (empty($noout)) {
        foreach($menu->liste as $menkey => $menuval) {
            print_start_menu_entry($menuval['idsel'], $menuval['classname'], $menuval['enabled']);
            print_text_menu_entry($menuval['titre'], $menuval['enabled'], (($menuval['url']!='#' && !preg_match('/^(http:\/\/|https:\/\/)/i', $menuval['url'])) ? DOL_URL_ROOT:'').$menuval['url'], $menuval['id'], $menuval['idsel'], $menuval['classname'], ($menuval['target']?$menuval['target']:$atarget));
            print_end_menu_entry($menuval['enabled']);
        }
    }

	$showmode=1;
    if (empty($noout)) {
        print_start_menu_entry('', 'class="tmenuend"', $showmode);
        print_end_menu_entry($showmode);
        print_end_menu_array();
    }

	return 0;
}


/**
 * Output start menu array
 *
 * @return	void
 */
function print_start_menu_array()
{
    global $conf;

	print '<div class="tmenudiv">';
	print '<ul role="navigation" class="tmenu"'.(empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER)?'':' title="Top menu"').'>';
}

/**
 * Output start menu entry
 *
 * @param	string	$idsel		Text
 * @param	string	$classname	String to add a css class
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_start_menu_entry($idsel, $classname, $showmode)
{
	if ($showmode)
	{
		print '<li '.$classname.' id="mainmenutd_'.$idsel.'">';
		//print '<div class="tmenuleft tmenusep"></div>';
		print '<div class="tmenucenter">';
	}
}

/**
 * Output menu entry
 *
 * @param	string	$text		Text
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @param	string	$url		Url
 * @param	string	$id			Id
 * @param	string	$idsel		Id sel
 * @param	string	$classname	Class name
 * @param	string	$atarget	Target
 * @return	void
 */
function print_text_menu_entry($text, $showmode, $url, $id, $idsel, $classname, $atarget)
{
	global $langs;

	if ($showmode == 1) {
		print '<a class="tmenuimage" tabindex="-1" href="'.$url.'"'.($atarget?' target="'.$atarget.'"':'').' title="'.dol_escape_htmltag($text).'">';
		print '<div class="'.$id.' '.$idsel.' topmenuimage"><span class="'.$id.' tmenuimage" id="mainmenuspan_'.$idsel.'"></span></div>';
		print '</a>';
		print '<a '.$classname.' id="mainmenua_'.$idsel.'" href="'.$url.'"'.($atarget?' target="'.$atarget.'"':'').' title="'.dol_escape_htmltag($text).'">';
		print '<span class="mainmenuaspan">';
		print $text;
		print '</span>';
		print '</a>';
	} elseif ($showmode == 2) {
		print '<div class="'.$id.' '.$idsel.' topmenuimage tmenudisabled"><span class="'.$id.'" id="mainmenuspan_'.$idsel.'"></span></div>';
		print '<a class="tmenudisabled" id="mainmenua_'.$idsel.'" href="#" title="'.dol_escape_htmltag($langs->trans("NotAllowed")).'">';
		print '<span class="mainmenuaspan">';
		print $text;
		print '</span>';
		print '</a>';
	}
}

/**
 * Output end menu entry
 *
 * @param	int		$showmode	0 = hide, 1 = allowed or 2 = not allowed
 * @return	void
 */
function print_end_menu_entry($showmode)
{
	if ($showmode)
	{
		print '</div></li>';
	}
	print "\n";
}

/**
 * Output menu array
 *
 * @return	void
 */
function print_end_menu_array()
{
	print '</ul>';
	print '</div>';
	print "\n";
}



/**
 * Core function to output left menu eldy
 * Fill &$menu (example with $forcemainmenu='home' $forceleftmenu='all', return left menu tree of Home)
 *
 * @param	DoliDB		$db                 Database handler
 * @param 	array		$menu_array_before  Table of menu entries to show before entries of menu handler (menu->liste filled with menu->add)
 * @param   array		$menu_array_after   Table of menu entries to show after entries of menu handler (menu->liste filled with menu->add)
 * @param	array		$tabMenu       		If array with menu entries already loaded, we put this array here (in most cases, it's empty)
 * @param	Menu		$menu				Object Menu to return back list of menu entries
 * @param	int			$noout				Disable output (Initialise &$menu only).
 * @param	string		$forcemainmenu		'x'=Force mainmenu to mainmenu='x'
 * @param	string		$forceleftmenu		'all'=Force leftmenu to '' (= all). If value come being '', we change it to value in session and 'none' if not defined in session.
 * @param	array		$moredata			An array with more data to output
 * @return	int								nb of menu entries
 */
function print_left_eldy_menu($db, $menu_array_before, $menu_array_after, &$tabMenu, &$menu, $noout = 0, $forcemainmenu = '', $forceleftmenu = '', $moredata = null)
{
	global $user, $conf, $langs, $dolibarr_main_db_name, $mysoc;

	//var_dump($tabMenu);

	$newmenu = $menu;

	$mainmenu=($forcemainmenu?$forcemainmenu:$_SESSION["mainmenu"]);
	$leftmenu=($forceleftmenu?'':(empty($_SESSION["leftmenu"])?'none':$_SESSION["leftmenu"]));

    $usemenuhider = 0;

	// Show logo company
	if (empty($conf->global->MAIN_MENU_INVERT) && empty($noout) && ! empty($conf->global->MAIN_SHOW_LOGO) && empty($conf->global->MAIN_OPTIMIZEFORTEXTBROWSER))
	{
		$mysoc->logo_mini=$conf->global->MAIN_INFO_SOCIETE_LOGO_MINI;
		if (! empty($mysoc->logo_mini) && is_readable($conf->mycompany->dir_output.'/logos/thumbs/'.$mysoc->logo_mini))
		{
			$urllogo=DOL_URL_ROOT.'/viewimage.php?cache=1&amp;modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_mini);
		}
		else
		{
			$urllogo=DOL_URL_ROOT.'/theme/dolibarr_logo.png';
		}
		$title=$langs->trans("GoIntoSetupToChangeLogo");
		print "\n".'<!-- Show logo on menu -->'."\n";
		print '<div class="blockvmenuimpair blockvmenulogo">'."\n";
		print '<div class="menu_titre" id="menu_titre_logo"></div>';
		print '<div class="menu_top" id="menu_top_logo"></div>';
		print '<div class="menu_contenu" id="menu_contenu_logo">';
		print '<div class="center"><img class="mycompany" title="'.dol_escape_htmltag($title).'" alt="" src="'.$urllogo.'" style="max-width: 70%"></div>'."\n";
		print '</div>';
		print '<div class="menu_end" id="menu_end_logo"></div>';
		print '</div>'."\n";
	}

	if (is_array($moredata) && ! empty($moredata['searchform']))	// searchform can contains select2 code or link to show old search form or link to switch on search page
	{
        print "\n";
        print "<!-- Begin SearchForm -->\n";
        print '<div id="blockvmenusearch" class="blockvmenusearch">'."\n";
        print $moredata['searchform'];
        print '</div>'."\n";
        print "<!-- End SearchForm -->\n";
	}

	if (is_array($moredata) && ! empty($moredata['bookmarks']))
	{
	    print "\n";
	    print "<!-- Begin Bookmarks -->\n";
	    print '<div id="blockvmenubookmarks" class="blockvmenubookmarks">'."\n";
	    print $moredata['bookmarks'];
	    print '</div>'."\n";
	    print "<!-- End Bookmarks -->\n";
	}

	/**
	 * We update newmenu with entries found into database
	 * --------------------------------------------------
	 */
	if ($mainmenu)	// If this is empty, loading hard coded menu and loading personalised menu will fail
	{
		/*
		 * Menu HOME
		 */
		if ($mainmenu == 'home')
		{
			$langs->load("users");

			// Home - dashboard
			$newmenu->add("/index.php?mainmenu=home&amp;leftmenu=home", $langs->trans("MyDashboard"), 0, 1, '', $mainmenu, 'home', 0, '', '', '', '<i class="fa fa-bar-chart fa-fw paddingright"></i>');

			// Setup
			$newmenu->add("/admin/index.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Setup"), 0, $user->admin, '', $mainmenu, 'setup', 0, '', '', '', '<i class="fa fa-wrench fa-fw paddingright"></i>');

	//		if ($usemenuhider || empty($leftmenu) || $leftmenu=="setup") // InfraS change
	//		{ // InfraS change
			    // Load translation files required by the page
                $langs->loadLangs(array("admin","help"));

				$warnpicto='';
				if (empty($conf->global->MAIN_INFO_SOCIETE_NOM) || empty($conf->global->MAIN_INFO_SOCIETE_COUNTRY))
				{
					$langs->load("errors");
					$warnpicto =' '.img_warning($langs->trans("WarningMandatorySetupNotComplete"));
				}
				$newmenu->add("/admin/company.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("MenuCompanySetup").$warnpicto, 1);
				$warnpicto='';
				if (count($conf->modules) <= (empty($conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING)?1:$conf->global->MAIN_MIN_NB_ENABLED_MODULE_FOR_WARNING))	// If only user module enabled
				{
					$langs->load("errors");
					$warnpicto = ' '.img_warning($langs->trans("WarningMandatorySetupNotComplete"));
				}
				$newmenu->add("/admin/modules.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Modules").$warnpicto, 1); // InfraS change
				$newmenu->add("/admin/menus.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Menus"), 1); // InfraS change
				$newmenu->add("/admin/ihm.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("GUISetup"), 1); // InfraS change

				$newmenu->add("/admin/translation.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Translation"), 1); // InfraS change
				$newmenu->add("/admin/defaultvalues.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("DefaultValues"), 1); // InfraS change
				$newmenu->add("/admin/boxes.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Boxes"), 1); // InfraS change
				$newmenu->add("/admin/delais.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("MenuWarnings"), 1); // InfraS change
				$newmenu->add("/admin/security_other.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Security"), 1); // InfraS change
				$newmenu->add("/admin/limits.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("MenuLimits"), 1); // InfraS change
				$newmenu->add("/admin/pdf.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("PDF"), 1); // InfraS change
				$newmenu->add("/admin/mails.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Emails"), 1); // InfraS change
				$newmenu->add("/admin/sms.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("SMS"), 1); // InfraS change
				$newmenu->add("/admin/dict.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("Dictionary"), 1); // InfraS change
				$newmenu->add("/admin/const.php?mainmenu=home&amp;leftmenu=setup", $langs->trans("OtherSetup"), 1); // InfraS change
	//		} // InfraS change

			// System tools
			$newmenu->add("/admin/tools/index.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("AdminTools"), 0, $user->admin, '', $mainmenu, 'admintools', 0, '', '', '', '<i class="fa fa-server fa-fw paddingright"></i>');
	//		if ($usemenuhider || empty($leftmenu) || preg_match('/^admintools/', $leftmenu)) // InfraS change
	//		{ // InfraS change
			    // Load translation files required by the page
                $langs->loadLangs(array('admin', 'help'));

				$newmenu->add('/admin/system/dolibarr.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoDolibarr'), 1); // InfraS change
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=='admintools_info') { // InfraS change
                    $newmenu->add('/admin/system/modules.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('Modules'), 2); // InfraS change
				    $newmenu->add('/admin/triggers.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('Triggers'), 2); // InfraS change
                    $newmenu->add('/admin/system/filecheck.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('FileCheck'), 2); // InfraS change
     //           } // InfraS change
				$newmenu->add('/admin/system/browser.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoBrowser'), 1);
				$newmenu->add('/admin/system/os.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoOS'), 1);
				$newmenu->add('/admin/system/web.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoWebServer'), 1);
				$newmenu->add('/admin/system/phpinfo.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoPHP'), 1);
				//if (function_exists('xdebug_is_enabled')) $newmenu->add('/admin/system/xdebug.php', $langs->trans('XDebug'),1);
				$newmenu->add('/admin/system/database.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('InfoDatabase'), 1);
				if (function_exists('eaccelerator_info')) $newmenu->add("/admin/tools/eaccelerator.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("EAccelerator"), 1);
				//$newmenu->add("/admin/system/perf.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("InfoPerf"),1);
				$newmenu->add("/admin/tools/dolibarr_export.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Backup"), 1);
				$newmenu->add("/admin/tools/dolibarr_import.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Restore"), 1);
				$newmenu->add("/admin/tools/update.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("MenuUpgrade"), 1);
				$newmenu->add("/admin/tools/purge.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Purge"), 1);
				$newmenu->add("/admin/tools/listevents.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Audit"), 1);
				$newmenu->add("/admin/tools/listsessions.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("Sessions"), 1);
				$newmenu->add('/admin/system/about.php?mainmenu=home&amp;leftmenu=admintools', $langs->trans('ExternalResources'), 1);

				if (! empty($conf->product->enabled) || ! empty($conf->service->enabled))
				{
					$langs->load("products");
				    $newmenu->add("/product/admin/product_tools.php?mainmenu=home&amp;leftmenu=admintools", $langs->trans("ProductVatMassChange"), 1, $user->admin);
				}
			}

			$newmenu->add("/user/home.php?leftmenu=users", $langs->trans("MenuUsersAndGroups"), 0, $user->rights->user->user->lire, '', $mainmenu, 'users', 0, '', '', '', '<i class="fa fa-users fa-fw paddingright"></i>');
			if ($user->rights->user->user->lire)
			{
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="users") // InfraS change
	//			{ // InfraS change
					$newmenu->add("/user/home.php?leftmenu=users", $langs->trans("Users"), 1, $user->rights->user->user->lire || $user->admin); // InfraS change
					$newmenu->add("/user/card.php?leftmenu=users&action=create", $langs->trans("NewUser"), 2, ($user->rights->user->user->creer || $user->admin) && !(! empty($conf->multicompany->enabled) && $conf->entity > 1 && $conf->global->MULTICOMPANY_TRANSVERSE_MODE), '', 'home');
					$newmenu->add("/user/list.php?leftmenu=users", $langs->trans("ListOfUsers"), 2, $user->rights->user->user->lire || $user->admin);
					$newmenu->add("/user/hierarchy.php?leftmenu=users", $langs->trans("HierarchicView"), 2, $user->rights->user->user->lire || $user->admin);
					if (! empty($conf->categorie->enabled))
					{
						$langs->load("categories");
						$newmenu->add("/categories/index.php?leftmenu=users&type=7", $langs->trans("UsersCategoriesShort"), 2, $user->rights->categorie->lire, '', $mainmenu, 'cat');
					}
					$newmenu->add("/user/home.php?leftmenu=users", $langs->trans("Groups"), 1, ($user->rights->user->user->lire || $user->admin) && !(! empty($conf->multicompany->enabled) && $conf->entity > 1 && $conf->global->MULTICOMPANY_TRANSVERSE_MODE)); // InfraS change
					$newmenu->add("/user/group/card.php?leftmenu=users&action=create", $langs->trans("NewGroup"), 2, (($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->write:$user->rights->user->user->creer) || $user->admin) && !(! empty($conf->multicompany->enabled) && $conf->entity > 1 && $conf->global->MULTICOMPANY_TRANSVERSE_MODE));
					$newmenu->add("/user/group/list.php?leftmenu=users", $langs->trans("ListOfGroups"), 2, (($conf->global->MAIN_USE_ADVANCED_PERMS?$user->rights->user->group_advance->read:$user->rights->user->user->lire) || $user->admin) && !(! empty($conf->multicompany->enabled) && $conf->entity > 1 && $conf->global->MULTICOMPANY_TRANSVERSE_MODE));
	//			} // InfraS change
			}
	//	} // InfraS change


		/*
		 * Menu THIRDPARTIES
		 */
		if ($mainmenu == 'companies')
		{
			// Societes
			if (! empty($conf->societe->enabled))
			{
				$langs->load("companies");
				$newmenu->add("/societe/index.php?leftmenu=thirdparties", $langs->trans("ThirdParty"), 0, $user->rights->societe->lire, '', $mainmenu, 'thirdparties');

				if ($user->rights->societe->creer)
				{
					$newmenu->add("/societe/card.php?leftmenu=thirdparties&action=create", $langs->trans("MenuNewThirdParty"), 1); // InfraS change
					if (! $conf->use_javascript_ajax) $newmenu->add("/societe/card.php?action=create&amp;private=1", $langs->trans("MenuNewPrivateIndividual"), 1);
				}
			}

			$newmenu->add("/societe/list.php?leftmenu=thirdparties", $langs->trans("List"), 1);

			// Prospects
			if (! empty($conf->societe->enabled) && empty($conf->global->SOCIETE_DISABLE_PROSPECTS))
			{
				$langs->load("commercial");
				$newmenu->add("/societe/list.php?type=p&amp;leftmenu=thirdparties", $langs->trans("ListProspectsShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'prospects'); // InfraS change
				/* no more required, there is a filter that can do more
				if ($usemenuhider || empty($leftmenu) || $leftmenu=="prospects") $newmenu->add("/societe/list.php?type=p&amp;sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;search_stcomm=-1", $langs->trans("LastProspectDoNotContact"), 2, $user->rights->societe->lire);
				if ($usemenuhider || empty($leftmenu) || $leftmenu=="prospects") $newmenu->add("/societe/list.php?type=p&amp;sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;search_stcomm=0", $langs->trans("LastProspectNeverContacted"), 2, $user->rights->societe->lire);
				if ($usemenuhider || empty($leftmenu) || $leftmenu=="prospects") $newmenu->add("/societe/list.php?type=p&amp;sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;search_stcomm=1", $langs->trans("LastProspectToContact"), 2, $user->rights->societe->lire);
				if ($usemenuhider || empty($leftmenu) || $leftmenu=="prospects") $newmenu->add("/societe/list.php?type=p&amp;sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;search_stcomm=2", $langs->trans("LastProspectContactInProcess"), 2, $user->rights->societe->lire);
				if ($usemenuhider || empty($leftmenu) || $leftmenu=="prospects") $newmenu->add("/societe/list.php?type=p&amp;sortfield=s.datec&amp;sortorder=desc&amp;begin=&amp;search_stcomm=3", $langs->trans("LastProspectContactDone"), 2, $user->rights->societe->lire);
				*/
				$newmenu->add("/societe/card.php?leftmenu=thirdparties&amp;action=create&amp;type=p", $langs->trans("MenuNewProspect"), 2, $user->rights->societe->creer); // InfraS change
				//$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=p", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
			}

			// Customers/Prospects
			if (! empty($conf->societe->enabled) && empty($conf->global->SOCIETE_DISABLE_CUSTOMERS))
			{
				$langs->load("commercial");
				$newmenu->add("/societe/list.php?type=c&amp;leftmenu=thirdparties", $langs->trans("ListCustomersShort"), 1, $user->rights->societe->lire, '', $mainmenu, 'customers'); // InfraS change

				$newmenu->add("/societe/card.php?leftmenu=thirdparties&amp;action=create&amp;type=c", $langs->trans("MenuNewCustomer"), 2, $user->rights->societe->creer); // InfraS change
				//$newmenu->add("/contact/list.php?leftmenu=customers&amp;type=c", $langs->trans("Contacts"), 2, $user->rights->societe->contact->lire);
			}

			// Suppliers
			if (! empty($conf->societe->enabled) && (! empty($conf->fournisseur->enabled) || ! empty($conf->supplier_proposal->enabled)))
			{
				$langs->load("suppliers");
				$newmenu->add("/societe/list.php?type=f&amp;leftmenu=thirdparties", $langs->trans("ListSuppliersShort"), 1, ($user->rights->fournisseur->lire || $user->rights->supplier_proposal->lire), '', $mainmenu, 'suppliers'); // InfraS change
				$newmenu->add("/societe/card.php?leftmenu=thirdparties&amp;action=create&amp;type=f", $langs->trans("MenuNewSupplier"), 2, $user->rights->societe->creer && ($user->rights->fournisseur->lire || $user->rights->supplier_proposal->lire)); // InfraS change
			}

			// Categories
			if (! empty($conf->categorie->enabled))
			{
				$langs->load("categories");
				if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS) || empty($conf->global->SOCIETE_DISABLE_CUSTOMERS))
				{
					// Categories prospects/customers
					$menutoshow=$langs->trans("CustomersProspectsCategoriesShort");
					if (! empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) $menutoshow=$langs->trans("CustomersCategoriesShort");
					if (! empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) $menutoshow=$langs->trans("ProspectsCategoriesShort");
					$newmenu->add("/categories/index.php?leftmenu=thirdparties&amp;type=2", $menutoshow, 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
				}
				// Categories suppliers
				if (! empty($conf->fournisseur->enabled))
				{
					$newmenu->add("/categories/index.php?leftmenu=thirdparties&amp;type=1", $langs->trans("SuppliersCategoriesShort"), 1, $user->rights->categorie->lire); // InfraS change
				}
			}

			// Contacts
			$newmenu->add("/societe/index.php?leftmenu=contacts", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("Contacts") : $langs->trans("ContactsAddresses")), 0, $user->rights->societe->contact->lire, '', $mainmenu, 'contacts'); // InfraS change
			$newmenu->add("/contact/card.php?leftmenu=contacts&amp;action=create", (! empty($conf->global->SOCIETE_ADDRESSES_MANAGEMENT) ? $langs->trans("NewContact") : $langs->trans("NewContactAddress")), 1, $user->rights->societe->contact->creer);
			$newmenu->add("/contact/list.php?leftmenu=contacts", $langs->trans("List"), 1, $user->rights->societe->contact->lire);
			if (empty($conf->global->SOCIETE_DISABLE_PROSPECTS)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=p", $langs->trans("Prospects"), 2, $user->rights->societe->contact->lire);
			if (empty($conf->global->SOCIETE_DISABLE_CUSTOMERS)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=c", $langs->trans("Customers"), 2, $user->rights->societe->contact->lire);
			if (! empty($conf->fournisseur->enabled)) $newmenu->add("/contact/list.php?leftmenu=contacts&type=f", $langs->trans("Suppliers"), 2, $user->rights->societe->contact->lire);
			$newmenu->add("/contact/list.php?leftmenu=contacts&type=o", $langs->trans("ContactOthers"), 2, $user->rights->societe->contact->lire);
			//$newmenu->add("/contact/list.php?userid=$user->id", $langs->trans("MyContacts"), 1, $user->rights->societe->contact->lire);

			// Categories
			if (! empty($conf->categorie->enabled))
			{
				$langs->load("categories");
				// Categories Contact
				$newmenu->add("/categories/index.php?leftmenu=contacts&amp;type=4", $langs->trans("ContactCategoriesShort"), 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
			}
		}

		/*
		 * Menu COMMERCIAL
		 */
		if ($mainmenu == 'commercial')
		{
			$langs->load("companies");

			// Customer proposal
			if (! empty($conf->propal->enabled))
			{
				$langs->load("propal");
				$newmenu->add("/comm/propal/index.php?leftmenu=propals", $langs->trans("Proposals"), 0, $user->rights->propale->lire, '', $mainmenu, 'propals', 100);
				$newmenu->add("/comm/propal/card.php?action=create&amp;leftmenu=propals", $langs->trans("NewPropal"), 1, $user->rights->propale->creer);
				$newmenu->add("/comm/propal/list.php?leftmenu=propals", $langs->trans("List"), 1, $user->rights->propale->lire);
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="propals") { // InfraS change
                    $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=0", $langs->trans("PropalsDraft"), 2, $user->rights->propale->lire);
				    $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=1", $langs->trans("PropalsOpened"), 2, $user->rights->propale->lire);
				    $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=2", $langs->trans("PropalStatusSigned"), 2, $user->rights->propale->lire);
				    $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=3", $langs->trans("PropalStatusNotSigned"), 2, $user->rights->propale->lire);
				    $newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=4", $langs->trans("PropalStatusBilled"), 2, $user->rights->propale->lire);
                    //$newmenu->add("/comm/propal/list.php?leftmenu=propals&viewstatut=2,3,4", $langs->trans("PropalStatusClosedShort"), 2, $user->rights->propale->lire);
     //           } // InfraS change
				$newmenu->add("/comm/propal/stats/index.php?leftmenu=propals", $langs->trans("Statistics"), 1, $user->rights->propale->lire);
			}

            // Customers orders
            if (! empty($conf->commande->enabled))
            {
                $langs->load("orders");
                $newmenu->add("/commande/index.php?leftmenu=orders", $langs->trans("CustomersOrders"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders', 200);
                $newmenu->add("/commande/card.php?action=create&amp;leftmenu=orders", $langs->trans("NewOrder"), 1, $user->rights->commande->creer);
                $newmenu->add("/commande/list.php?leftmenu=orders", $langs->trans("List"), 1, $user->rights->commande->lire);
     //           if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders") { // InfraS change
                    $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->commande->lire);
                    $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->commande->lire);
                    if (! empty($conf->expedition->enabled)) $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=2", $langs->trans("StatusOrderSentShort"), 2, $user->rights->commande->lire);
                    $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=3", $langs->trans("StatusOrderDelivered"), 2, $user->rights->commande->lire);
                    //$newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=4", $langs->trans("StatusOrderProcessed"), 2, $user->rights->commande->lire);
                    $newmenu->add("/commande/list.php?leftmenu=orders&viewstatut=-1", $langs->trans("StatusOrderCanceledShort"), 2, $user->rights->commande->lire);
     //           } // InfraS change
                $newmenu->add("/commande/stats/index.php?leftmenu=orders", $langs->trans("Statistics"), 1, $user->rights->commande->lire);
            }

			// Suppliers orders
            if (! empty($conf->supplier_order->enabled))
			{
				$langs->load("orders");
				$newmenu->add("/fourn/commande/index.php?leftmenu=orders_suppliers", $langs->trans("SuppliersOrders"), 0, $user->rights->fournisseur->commande->lire, '', $mainmenu, 'orders_suppliers', 400);
				$newmenu->add("/fourn/commande/card.php?action=create&amp;leftmenu=orders_suppliers", $langs->trans("NewOrder"), 1, $user->rights->fournisseur->commande->creer);
				$newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers", $langs->trans("List"), 1, $user->rights->fournisseur->commande->lire);

	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders_suppliers") { // InfraS change
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=0", $langs->trans("StatusOrderDraftShort"), 2, $user->rights->fournisseur->commande->lire);
                    if (empty($conf->global->SUPPLIER_ORDER_HIDE_VALIDATED)) $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=1", $langs->trans("StatusOrderValidated"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=2", $langs->trans("StatusOrderApprovedShort"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=3", $langs->trans("StatusOrderOnProcessShort"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=4", $langs->trans("StatusOrderReceivedPartiallyShort"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=5", $langs->trans("StatusOrderReceivedAll"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=6,7", $langs->trans("StatusOrderCanceled"), 2, $user->rights->fournisseur->commande->lire);
                    $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&statut=9", $langs->trans("StatusOrderRefused"), 2, $user->rights->fournisseur->commande->lire);
     //           } // InfraS change
                // Billed is another field. We should add instead a dedicated filter on list. if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders_suppliers") $newmenu->add("/fourn/commande/list.php?leftmenu=orders_suppliers&billed=1", $langs->trans("StatusOrderBilled"), 2, $user->rights->fournisseur->commande->lire);


				$newmenu->add("/commande/stats/index.php?leftmenu=orders_suppliers&amp;mode=supplier", $langs->trans("Statistics"), 1, $user->rights->fournisseur->commande->lire);
			}

			// Contrat
			if (! empty($conf->contrat->enabled))
			{
				$langs->load("contracts");
				$newmenu->add("/contrat/index.php?leftmenu=contracts", $langs->trans("ContractsSubscriptions"), 0, $user->rights->contrat->lire, '', $mainmenu, 'contracts', 2000);
				$newmenu->add("/contrat/card.php?action=create&amp;leftmenu=contracts", $langs->trans("NewContractSubscription"), 1, $user->rights->contrat->creer);
				$newmenu->add("/contrat/list.php?leftmenu=contracts", $langs->trans("List"), 1, $user->rights->contrat->lire);
				$newmenu->add("/contrat/services_list.php?leftmenu=contracts", $langs->trans("MenuServices"), 1, $user->rights->contrat->lire);
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="contracts") { // InfraS change
                    $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=0", $langs->trans("MenuInactiveServices"), 2, $user->rights->contrat->lire);
				    $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=4", $langs->trans("MenuRunningServices"), 2, $user->rights->contrat->lire);
				    $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=4&amp;filter=expired", $langs->trans("MenuExpiredServices"), 2, $user->rights->contrat->lire);
                    $newmenu->add("/contrat/services_list.php?leftmenu=contracts&amp;mode=5", $langs->trans("MenuClosedServices"), 2, $user->rights->contrat->lire);
    //            } // InfraS change
			}

			// Interventions
			if (! empty($conf->ficheinter->enabled))
			{
				$langs->load("interventions");
				$newmenu->add("/fichinter/index.php?leftmenu=ficheinter", $langs->trans("Interventions"), 0, $user->rights->ficheinter->lire, '', $mainmenu, 'ficheinter', 2200);
				$newmenu->add("/fichinter/card.php?action=create&amp;leftmenu=ficheinter", $langs->trans("NewIntervention"), 1, $user->rights->ficheinter->creer, '', '', '', 201);
				$newmenu->add("/fichinter/list.php?leftmenu=ficheinter", $langs->trans("List"), 1, $user->rights->ficheinter->lire, '', '', '', 202);
				if ($conf->global->MAIN_FEATURES_LEVEL >= 2) $newmenu->add("/fichinter/card-rec.php?leftmenu=ficheinter", $langs->trans("ModelList"), 1, $user->rights->ficheinter->lire, '', '', '', 203);
				$newmenu->add("/fichinter/stats/index.php?leftmenu=ficheinter", $langs->trans("Statistics"), 1, $user->rights->fournisseur->commande->lire);
			}
		}


		/*
		 * Menu COMPTA-FINANCIAL
		 */
		if ($mainmenu == 'billing')
		{
			$langs->load("companies");

			// Customers invoices
			if (! empty($conf->facture->enabled))
			{
				$langs->load("bills");
				$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills", $langs->trans("BillsCustomers"), 0, $user->rights->facture->lire, '', $mainmenu, 'customers_bills');
				$newmenu->add("/compta/facture/card.php?leftmenu=customers_bills&action=create", $langs->trans("NewBill"), 1, $user->rights->facture->creer); // InfraS change
				$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills", $langs->trans("List"), 1, $user->rights->facture->lire, '', $mainmenu, 'customers_bills_list');

	//			if ($usemenuhider || empty($leftmenu) || preg_match('/customers_bills(|_draft|_notpaid|_paid|_canceled)$/', $leftmenu)) // InfraS change
	//			{ // InfraS change
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_draft&amp;search_status=0", $langs->trans("BillShortStatusDraft"), 2, $user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_notpaid&amp;search_status=1", $langs->trans("BillShortStatusNotPaid"), 2, $user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_paid&amp;search_status=2", $langs->trans("BillShortStatusPaid"), 2, $user->rights->facture->lire);
					$newmenu->add("/compta/facture/list.php?leftmenu=customers_bills_canceled&amp;search_status=3", $langs->trans("BillShortStatusCanceled"), 2, $user->rights->facture->lire);
	//			} // InfraS change
				$newmenu->add("/compta/facture/invoicetemplate_list.php?leftmenu=customers_bills_templates", $langs->trans("ListOfTemplates"), 1, $user->rights->facture->creer, '', $mainmenu, 'customers_bills_templates');    // No need to see recurring invoices, if user has no permission to create invoice.

				$newmenu->add("/compta/paiement/list.php?leftmenu=customers_bills_payment", $langs->trans("Payments"), 1, $user->rights->facture->lire, '', $mainmenu, 'customers_bills_payment');

				if (! empty($conf->global->BILL_ADD_PAYMENT_VALIDATION))
				{
					$newmenu->add("/compta/paiement/tovalidate.php?leftmenu=customers_bills_tovalid", $langs->trans("MenuToValid"), 2, $user->rights->facture->lire, '', $mainmenu, 'customer_bills_tovalid');
				}
				$newmenu->add("/compta/paiement/rapport.php?leftmenu=customers_bills_reports", $langs->trans("Reportings"), 2, $user->rights->facture->lire, '', $mainmenu, 'customers_bills_reports');

				$newmenu->add("/compta/facture/stats/index.php?leftmenu=customers_bills_stats", $langs->trans("Statistics"), 1, $user->rights->facture->lire, '', $mainmenu, 'customers_bills_stats');
			}

			// Suppliers invoices
			if (! empty($conf->societe->enabled) && ! empty($conf->supplier_invoice->enabled))
			{
				$langs->load("bills");
				$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills", $langs->trans("BillsSuppliers"), 0, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills');
				$newmenu->add("/fourn/facture/card.php?leftmenu=suppliers_bills&amp;action=create", $langs->trans("NewBill"), 1, $user->rights->fournisseur->facture->creer, '', $mainmenu, 'suppliers_bills_create');
				$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills", $langs->trans("List"), 1, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_list');

	//			if ($usemenuhider || empty($leftmenu) || preg_match('/suppliers_bills/', $leftmenu)) { // InfraS change
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_draft&amp;search_status=0", $langs->trans("BillShortStatusDraft"), 2, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_draft');
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_notpaid&amp;search_status=1", $langs->trans("BillShortStatusNotPaid"), 2, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_notpaid');
					$newmenu->add("/fourn/facture/list.php?leftmenu=suppliers_bills_paid&amp;search_status=2", $langs->trans("BillShortStatusPaid"), 2, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_paid');
	//			} // InfraS change

				$newmenu->add("/fourn/facture/paiement.php?leftmenu=suppliers_bills_payment", $langs->trans("Payments"), 1, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_payment');

				$newmenu->add("/fourn/facture/rapport.php?leftmenu=suppliers_bills_report", $langs->trans("Reportings"), 2, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_report');

				$newmenu->add("/compta/facture/stats/index.php?mode=supplier&amp;leftmenu=suppliers_bills_stats", $langs->trans("Statistics"), 1, $user->rights->fournisseur->facture->lire, '', $mainmenu, 'suppliers_bills_stats');
			}

			// Orders
			if (! empty($conf->commande->enabled))
			{
				$langs->load("orders");
				if (! empty($conf->facture->enabled)) $newmenu->add("/commande/list.php?leftmenu=orders&amp;viewstatut=-3&amp;billed=0&amp;contextpage=billableorders", $langs->trans("MenuOrdersToBill2"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
				//if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->rights->commande->lire);
			}

			// Supplier Orders to bill
			if (! empty($conf->supplier_invoice->enabled))
			{
				if (! empty($conf->global->SUPPLIER_MENU_ORDER_RECEIVED_INTO_INVOICE))
				{
					$langs->load("supplier");
					$newmenu->add("/fourn/commande/list.php?leftmenu=orders&amp;search_status=5&amp;billed=0", $langs->trans("MenuOrdersSupplierToBill"), 0, $user->rights->commande->lire, '', $mainmenu, 'orders');
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="orders") $newmenu->add("/commande/", $langs->trans("StatusOrderToBill"), 1, $user->rights->commande->lire);
				}
			}


			// Donations
			if (! empty($conf->don->enabled))
			{
				$langs->load("donations");
				$newmenu->add("/don/index.php?leftmenu=donations&amp;mainmenu=billing", $langs->trans("Donations"), 0, $user->rights->don->lire, '', $mainmenu, 'donations');
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="donations") { // InfraS change
                    $newmenu->add("/don/card.php?leftmenu=donations&amp;action=create", $langs->trans("NewDonation"), 1, $user->rights->don->creer);
                    $newmenu->add("/don/list.php?leftmenu=donations", $langs->trans("List"), 1, $user->rights->don->lire);
     //           } // InfraS change
				// if ($leftmenu=="donations") $newmenu->add("/don/stats/index.php",$langs->trans("Statistics"), 1, $user->rights->don->lire);
			}

			// Taxes and social contributions
			if (! empty($conf->tax->enabled) || ! empty($conf->salaries->enabled) || ! empty($conf->loan->enabled) || ! empty($conf->banque->enabled))
			{
				global $mysoc;

				$permtoshowmenu=((! empty($conf->tax->enabled) && $user->rights->tax->charges->lire) || (! empty($conf->salaries->enabled) && ! empty($user->rights->salaries->read)) || (! empty($conf->loan->enabled) && $user->rights->loan->read) || (! empty($conf->banque->enabled) && $user->rights->banque->lire));
				$newmenu->add("/compta/charges/index.php?leftmenu=tax&amp;mainmenu=billing", $langs->trans("MenuSpecialExpenses"), 0, $permtoshowmenu, '', $mainmenu, 'tax');

				// Social contributions
				if (! empty($conf->tax->enabled))
				{
					$newmenu->add("/compta/sociales/list.php?leftmenu=tax_social", $langs->trans("MenuSocialContributions"), 1, $user->rights->tax->charges->lire);
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_social/i', $leftmenu)) { // InfraS change
                        $newmenu->add("/compta/sociales/card.php?leftmenu=tax_social&action=create", $langs->trans("MenuNewSocialContribution"), 2, $user->rights->tax->charges->creer);
					    $newmenu->add("/compta/sociales/list.php?leftmenu=tax_social", $langs->trans("List"), 2, $user->rights->tax->charges->lire);
                        $newmenu->add("/compta/sociales/payments.php?leftmenu=tax_social&amp;mainmenu=billing&amp;mode=sconly", $langs->trans("Payments"), 2, $user->rights->tax->charges->lire);
    //                } // InfraS change
					// VAT
					if (empty($conf->global->TAX_DISABLE_VAT_MENUS))
					{
						$newmenu->add("/compta/tva/list.php?leftmenu=tax_vat&amp;mainmenu=billing", $langs->transcountry("VAT", $mysoc->country_code), 1, $user->rights->tax->charges->lire, '', $mainmenu, 'tax_vat');
	//					if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_vat/i', $leftmenu)) { // InfraS change
                            $newmenu->add("/compta/tva/card.php?leftmenu=tax_vat&action=create", $langs->trans("New"), 2, $user->rights->tax->charges->creer);
						    $newmenu->add("/compta/tva/list.php?leftmenu=tax_vat", $langs->trans("List"), 2, $user->rights->tax->charges->lire);
						    $newmenu->add("/compta/tva/index.php?leftmenu=tax_vat", $langs->trans("ReportByMonth"), 2, $user->rights->tax->charges->lire);
						    $newmenu->add("/compta/tva/clients.php?leftmenu=tax_vat", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
                            $newmenu->add("/compta/tva/quadri_detail.php?leftmenu=tax_vat", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
    //                    } // InfraS change
						global $mysoc;

						//Local Taxes 1
						if($mysoc->useLocalTax(1) && (isset($mysoc->localtax1_assuj) && $mysoc->localtax1_assuj=="1"))
						{
							$newmenu->add("/compta/localtax/list.php?leftmenu=tax_1_vat&amp;mainmenu=billing&amp;localTaxType=1", $langs->transcountry("LT1", $mysoc->country_code), 1, $user->rights->tax->charges->lire);
	//						if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_1_vat/i', $leftmenu)) { // InfraS change
                                $newmenu->add("/compta/localtax/card.php?leftmenu=tax_1_vat&action=create&amp;localTaxType=1", $langs->trans("New"), 2, $user->rights->tax->charges->creer);
							    $newmenu->add("/compta/localtax/list.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("List"), 2, $user->rights->tax->charges->lire);
							    $newmenu->add("/compta/localtax/index.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByMonth"), 2, $user->rights->tax->charges->lire);
							    $newmenu->add("/compta/localtax/clients.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
                                $newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_1_vat&amp;localTaxType=1", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
    //                        } // InfraS change
						}
						//Local Taxes 2
						if($mysoc->useLocalTax(2) && (isset($mysoc->localtax2_assuj) && $mysoc->localtax2_assuj=="1"))
						{
							$newmenu->add("/compta/localtax/list.php?leftmenu=tax_2_vat&amp;mainmenu=billing&amp;localTaxType=2", $langs->transcountry("LT2", $mysoc->country_code), 1, $user->rights->tax->charges->lire);
	//						if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_2_vat/i', $leftmenu)) { // InfraS change
                                $newmenu->add("/compta/localtax/card.php?leftmenu=tax_2_vat&action=create&amp;localTaxType=2", $langs->trans("New"), 2, $user->rights->tax->charges->creer);
							    $newmenu->add("/compta/localtax/list.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("List"), 2, $user->rights->tax->charges->lire);
							    $newmenu->add("/compta/localtax/index.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByMonth"), 2, $user->rights->tax->charges->lire);
							    $newmenu->add("/compta/localtax/clients.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByCustomers"), 2, $user->rights->tax->charges->lire);
                                $newmenu->add("/compta/localtax/quadri_detail.php?leftmenu=tax_2_vat&amp;localTaxType=2", $langs->trans("ReportByQuarter"), 2, $user->rights->tax->charges->lire);
    //                        } // InfraS change
						}
					}
				}

				// Salaries
				if (! empty($conf->salaries->enabled))
				{
					$langs->load("salaries");
					$newmenu->add("/compta/salaries/list.php?leftmenu=tax_salary&amp;mainmenu=billing", $langs->trans("Salaries"), 1, $user->rights->salaries->read, '', $mainmenu, 'tax_salary');
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_salary/i', $leftmenu)) { // InfraS change
                        $newmenu->add("/compta/salaries/card.php?leftmenu=tax_salary&action=create", $langs->trans("NewPayment"), 2, $user->rights->salaries->write);
					    $newmenu->add("/compta/salaries/list.php?leftmenu=tax_salary", $langs->trans("Payments"), 2, $user->rights->salaries->read);
                        $newmenu->add("/compta/salaries/stats/index.php?leftmenu=tax_salary", $langs->trans("Statistics"), 2, $user->rights->salaries->read);
     //               } // InfraS change
				}

				// Loan
				if (! empty($conf->loan->enabled))
				{
					$langs->load("loan");
					$newmenu->add("/loan/list.php?leftmenu=tax_loan&amp;mainmenu=billing", $langs->trans("Loans"), 1, $user->rights->loan->read, '', $mainmenu, 'tax_loan');
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_loan/i', $leftmenu)) { // InfraS change
                        $newmenu->add("/loan/card.php?leftmenu=tax_loan&action=create", $langs->trans("NewLoan"), 2, $user->rights->loan->write);
                        //$newmenu->add("/loan/payment/list.php?leftmenu=tax_loan",$langs->trans("Payments"),2,$user->rights->loan->read);
     //               } // InfraS change
				}

				// Various payment
				if (! empty($conf->banque->enabled) && empty($conf->global->BANK_USE_OLD_VARIOUS_PAYMENT))
				{
					$langs->load("banks");
					$newmenu->add("/compta/bank/various_payment/list.php?leftmenu=tax_various&amp;mainmenu=billing", $langs->trans("MenuVariousPayment"), 1, $user->rights->banque->lire, '', $mainmenu, 'tax_various');
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/^tax_various/i', $leftmenu)) { // InfraS change
                        $newmenu->add("/compta/bank/various_payment/card.php?leftmenu=tax_various&action=create", $langs->trans("New"), 2, $user->rights->banque->modifier);
                        $newmenu->add("/compta/bank/various_payment/list.php?leftmenu=tax_various", $langs->trans("List"), 2, $user->rights->banque->lire);
    //                } // InfraS change
				}
			}
		}

		/*
		 * Menu COMPTA-FINANCIAL
		 */
		if ($mainmenu == 'accountancy')
		{
			$langs->load("companies");

			// Accounting (Double entries)
			if (! empty($conf->accounting->enabled))
			{
				$permtoshowmenu=(! empty($conf->accounting->enabled) || $user->rights->accounting->bind->write || $user->rights->compta->resultat->lire);
				//$newmenu->add("/accountancy/index.php?leftmenu=accountancy", $langs->trans("MenuAccountancy"), 0, $permtoshowmenu, '', $mainmenu, 'accountancy');

				// Configuration
				$newmenu->add("/accountancy/index.php?leftmenu=accountancy_admin", $langs->trans("Setup"), 0, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin', 1);
	//			if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_admin/', $leftmenu)) { // InfraS change
					$newmenu->add("/accountancy/admin/index.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("General"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_general', 10);

					// Fiscal year - Not really yet used. In a future will lock some periods.
					if ($conf->global->MAIN_FEATURES_LEVEL > 1) {
						$newmenu->add("/accountancy/admin/fiscalyear.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("FiscalPeriod"), 1, $user->rights->accounting->fiscalyear, '', $mainmenu, 'fiscalyear', 20);
					}

					$newmenu->add("/accountancy/admin/journals_list.php?id=35&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("AccountingJournals"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_journal', 30);
					$newmenu->add("/accountancy/admin/accountmodel.php?id=31&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("Pcg_version"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_chartmodel', 40);
					$newmenu->add("/accountancy/admin/account.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("Chartofaccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_chart', 41);
					$newmenu->add("/accountancy/admin/categories_list.php?id=32&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("AccountingCategory"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_chart', 50);
					$newmenu->add("/accountancy/admin/defaultaccounts.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuDefaultAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_default', 60);
					if (! empty($conf->banque->enabled)) {
						$newmenu->add("/compta/bank/list.php?mainmenu=accountancy&leftmenu=accountancy_admin&search_status=-1", $langs->trans("MenuBankAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_bank', 70);
					}
					if (! empty($conf->facture->enabled) || ! empty($conf->fournisseur->enabled)) {
						$newmenu->add("/admin/dict.php?id=10&from=accountancy&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuVatAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_default', 80);
					}
					if (! empty($conf->tax->enabled)) {
						$newmenu->add("/admin/dict.php?id=7&from=accountancy&search_country_id=".$mysoc->country_id."&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuTaxAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_default', 90);
					}
					if (! empty($conf->expensereport->enabled)) {
						$newmenu->add("/admin/dict.php?id=17&from=accountancy&mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuExpenseReportAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_default', 100);
					}
					$newmenu->add("/accountancy/admin/productaccount.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuProductsAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_product', 110);
					$newmenu->add("/accountancy/admin/closure.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("MenuClosureAccounts"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_closure', 120);
					$newmenu->add("/accountancy/admin/export.php?mainmenu=accountancy&leftmenu=accountancy_admin", $langs->trans("ExportOptions"), 1, $user->rights->accounting->chartofaccount, '', $mainmenu, 'accountancy_admin_export', 130);
	//			} // InfraS change

                // Transfer in accounting
                $newmenu->add("/accountancy/index.php?leftmenu=accountancy_transfer", $langs->trans("TransferInAccounting"), 0, $user->rights->accounting->bind->write, '', $mainmenu, 'transfer', 1);

                // Binding
                // $newmenu->add("", $langs->trans("Binding"), 0, $user->rights->accounting->bind->write, '', $mainmenu, 'dispatch');
                if (! empty($conf->facture->enabled))
                {
                	$newmenu->add("/accountancy/customer/index.php?leftmenu=accountancy_dispatch_customer&amp;mainmenu=accountancy", $langs->trans("CustomersVentilation"), 1, $user->rights->accounting->bind->write, '', $mainmenu, 'dispatch_customer');
    //            	if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_customer/', $leftmenu)) { // InfraS change
                		$newmenu->add("/accountancy/customer/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer", $langs->trans("ToBind"), 2, $user->rights->accounting->bind->write);
                		$newmenu->add("/accountancy/customer/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_customer", $langs->trans("Binded"), 2, $user->rights->accounting->bind->write);
    //            	} // InfraS change
                }
                if (! empty($conf->supplier_invoice->enabled))
                {
                	$newmenu->add("/accountancy/supplier/index.php?leftmenu=accountancy_dispatch_supplier&amp;mainmenu=accountancy", $langs->trans("SuppliersVentilation"), 1, $user->rights->accounting->bind->write, '', $mainmenu, 'dispatch_supplier');
    //            	if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_supplier/', $leftmenu)) { // InfraS change
                		$newmenu->add("/accountancy/supplier/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier", $langs->trans("ToBind"), 2, $user->rights->accounting->bind->write);
                		$newmenu->add("/accountancy/supplier/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_supplier", $langs->trans("Binded"), 2, $user->rights->accounting->bind->write);
    //            	} // InfraS change
                }
                if (! empty($conf->expensereport->enabled))
                {
                	$newmenu->add("/accountancy/expensereport/index.php?leftmenu=accountancy_dispatch_expensereport&amp;mainmenu=accountancy", $langs->trans("ExpenseReportsVentilation"), 1, $user->rights->accounting->bind->write, '', $mainmenu, 'dispatch_expensereport');
    //            	if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_dispatch_expensereport/', $leftmenu)) { // InfraS change
                		$newmenu->add("/accountancy/expensereport/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_expensereport", $langs->trans("ToBind"), 2, $user->rights->accounting->bind->write);
                		$newmenu->add("/accountancy/expensereport/lines.php?mainmenu=accountancy&amp;leftmenu=accountancy_dispatch_expensereport", $langs->trans("Binded"), 2, $user->rights->accounting->bind->write);
    //            	} // InfraS change
                }

				// Journals
				if(! empty($conf->accounting->enabled) && ! empty($user->rights->accounting->comptarapport->lire) && $mainmenu == 'accountancy')
				{
					$newmenu->add('/accountancy/index.php?leftmenu=accountancy_transfer', $langs->trans("RegistrationInAccounting"), 1, $user->rights->accounting->comptarapport->lire); // InfraS change

					// Multi journal
					$sql = "SELECT rowid, code, label, nature";
					$sql.= " FROM ".MAIN_DB_PREFIX."accounting_journal";
					$sql.= " WHERE entity = ".$conf->entity;
					$sql.= " AND active = 1";
					$sql.= " ORDER BY label DESC";

					$resql = $db->query($sql);
					if ($resql)
					{
						$numr = $db->num_rows($resql);
						$i = 0;

						if ($numr > 0)
						{
							while ($i < $numr)
							{
								$objp = $db->fetch_object($resql);

								$nature='';

								// Must match array $sourceList defined into journals_list.php
								if ($objp->nature == 2 && ! empty($conf->facture->enabled)) $nature="sells";
								if ($objp->nature == 3 && ! empty($conf->fournisseur->enabled)) $nature="purchases";
								if ($objp->nature == 4 && ! empty($conf->banque->enabled)) $nature="bank";
								if ($objp->nature == 5 && ! empty($conf->expensereport->enabled)) $nature="expensereports";
								if ($objp->nature == 1) $nature="various";
								if ($objp->nature == 8) $nature="inventory";
								if ($objp->nature == 9) $nature="hasnew";

								// To enable when page exists
								if (empty($conf->global->ACCOUNTANCY_SHOW_DEVELOP_JOURNAL))
								{
									if ($nature == 'various' || $nature == 'hasnew' || $nature == 'inventory') $nature='';
								}

								if ($nature)
								{
									$langs->load('accountancy');
									$journallabel=$langs->transnoentities($objp->label);	// Labels in this table are set by loading llx_accounting_abc.sql. Label can be 'ACCOUNTING_SELL_JOURNAL', 'InventoryJournal', ...
									$newmenu->add('/accountancy/journal/'.$nature.'journal.php?mainmenu=accountancy&leftmenu=accountancy_journal&id_journal='.$objp->rowid, $journallabel, 2, $user->rights->accounting->comptarapport->lire);
								}
								$i++;
							}
						}
						else
						{
							// Should not happend. Entries are added
							$newmenu->add('', $langs->trans("NoJournalDefined"), 2, $user->rights->accounting->comptarapport->lire);
						}
					}
					else dol_print_error($db);
					$db->free($resql);
				}

                // Accounting
                $newmenu->add("/accountancy/index.php?leftmenu=accountancy_accountancy", $langs->trans("MenuAccountancy"), 0, $user->rights->accounting->mouvements->lire, '', $mainmenu, 'accountancy', 1);

                // General Ledger
				$newmenu->add("/accountancy/bookkeeping/list.php?mainmenu=accountancy&amp;leftmenu=accountancy_accountancy", $langs->trans("Bookkeeping"), 1, $user->rights->accounting->mouvements->lire);

				// Balance
				$newmenu->add("/accountancy/bookkeeping/balance.php?mainmenu=accountancy&amp;leftmenu=accountancy_accountancy", $langs->trans("AccountBalance"), 1, $user->rights->accounting->mouvements->lire);

				// Files
				if ((! empty($conf->global->MAIN_FEATURES_LEVEL) && $conf->global->MAIN_FEATURES_LEVEL >= 1) || ! empty($conf->global->ACCOUNTANCY_SHOW_EXPORT_FILES_MENU))
				{
					$newmenu->add("/compta/accounting-files.php?mainmenu=accountancy&amp;leftmenu=accountancy_files", $langs->trans("AccountantFiles"), 1, $user->rights->accounting->mouvements->lire);
				}

				// Reports
				$newmenu->add("/compta/resultat/index.php?mainmenu=accountancy&amp;leftmenu=accountancy_report", $langs->trans("Reportings"), 1, $user->rights->accounting->comptarapport->lire, '', $mainmenu, 'ca');

	//			if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) { // InfraS change
                    $newmenu->add("/compta/resultat/index.php?leftmenu=accountancy_report", $langs->trans("MenuReportInOut"), 2, $user->rights->accounting->comptarapport->lire);
				    $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=accountancy_report", $langs->trans("ByPredefinedAccountGroups"), 3, $user->rights->accounting->comptarapport->lire);
                    $newmenu->add("/compta/resultat/result.php?leftmenu=accountancy_report", $langs->trans("ByPersonalizedAccountGroups"), 3, $user->rights->accounting->comptarapport->lire);
    //            } // InfraS change

				$modecompta='CREANCES-DETTES';
				if(! empty($conf->accounting->enabled) && ! empty($user->rights->accounting->comptarapport->lire) && $mainmenu == 'accountancy') $modecompta='BOOKKEEPING';	// Not yet implemented. Should be BOOKKEEPINGCOLLECTED
				if ($modecompta)
				{
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) { // InfraS change
                        $newmenu->add("/compta/stats/index.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportTurnover"), 2, $user->rights->accounting->comptarapport->lire);
					    $newmenu->add("/compta/stats/casoc.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->rights->accounting->comptarapport->lire);
					    $newmenu->add("/compta/stats/cabyuser.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByUsers"), 3, $user->rights->accounting->comptarapport->lire);
					    $newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"), 3, $user->rights->accounting->comptarapport->lire);
                        $newmenu->add("/compta/stats/byratecountry.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByVatRate"), 3, $user->rights->accounting->comptarapport->lire);
    //                } // InfraS change
				}

				$modecompta='RECETTES-DEPENSES';
				//if (! empty($conf->accounting->enabled) && ! empty($user->rights->accounting->comptarapport->lire) && $mainmenu == 'accountancy') $modecompta='';	// Not yet implemented. Should be BOOKKEEPINGCOLLECTED
				if ($modecompta)
				{
	//				if ($usemenuhider || empty($leftmenu) || preg_match('/accountancy_report/', $leftmenu)) { // InfraS change
					    $newmenu->add("/compta/stats/index.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ReportTurnoverCollected"), 2, $user->rights->accounting->comptarapport->lire);
					    $newmenu->add("/compta/stats/casoc.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByCompanies"), 3, $user->rights->accounting->comptarapport->lire);
					    $newmenu->add("/compta/stats/cabyuser.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByUsers"), 3, $user->rights->accounting->comptarapport->lire);
					    //$newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByProductsAndServices"),3,$user->rights->accounting->comptarapport->lire);
                        //$newmenu->add("/compta/stats/byratecountry.php?leftmenu=accountancy_report&modecompta=".$modecompta, $langs->trans("ByVatRate"),3,$user->rights->accounting->comptarapport->lire);
    //                } // InfraS change
				}
            }

			// Accountancy (simple)
            if (! empty($conf->comptabilite->enabled))
            {
            	// Files
            	if ((! empty($conf->global->MAIN_FEATURES_LEVEL) && $conf->global->MAIN_FEATURES_LEVEL >= 1) || ! empty($conf->global->ACCOUNTANCY_SHOW_EXPORT_FILES_MENU))
            	{
            		$newmenu->add("/compta/accounting-files.php?mainmenu=accountancy&amp;leftmenu=accountancy_files", $langs->trans("AccountantFiles"), 0, $user->rights->compta->resultat->lire, '', $mainmenu, 'files');
            	}

                // Bilan, resultats
                $newmenu->add("/compta/resultat/index.php?leftmenu=report&amp;mainmenu=accountancy", $langs->trans("Reportings"), 0, $user->rights->compta->resultat->lire, '', $mainmenu, 'ca');

    //            if ($usemenuhider || empty($leftmenu) || preg_match('/report/', $leftmenu)) { // InfraS change
                    $newmenu->add("/compta/resultat/index.php?leftmenu=report", $langs->trans("MenuReportInOut"), 1, $user->rights->compta->resultat->lire);
                    $newmenu->add("/compta/resultat/clientfourn.php?leftmenu=report", $langs->trans("ByCompanies"), 2, $user->rights->compta->resultat->lire);
                    /* On verra ca avec module compabilite expert
                    $newmenu->add("/compta/resultat/compteres.php?leftmenu=report","Compte de resultat",2,$user->rights->compta->resultat->lire);
                    $newmenu->add("/compta/resultat/bilan.php?leftmenu=report","Bilan",2,$user->rights->compta->resultat->lire);
                    */
                    $newmenu->add("/compta/stats/index.php?leftmenu=report", $langs->trans("ReportTurnover"), 1, $user->rights->compta->resultat->lire);

                    /*
                    $newmenu->add("/compta/stats/cumul.php?leftmenu=report","Cumule",2,$user->rights->compta->resultat->lire);
                    if (! empty($conf->propal->enabled)) {
                        $newmenu->add("/compta/stats/prev.php?leftmenu=report","Previsionnel",2,$user->rights->compta->resultat->lire);
                        $newmenu->add("/compta/stats/comp.php?leftmenu=report","Transforme",2,$user->rights->compta->resultat->lire);
                    }
                    */
                    $newmenu->add("/compta/stats/casoc.php?leftmenu=report", $langs->trans("ByCompanies"), 2, $user->rights->compta->resultat->lire);
                    $newmenu->add("/compta/stats/cabyuser.php?leftmenu=report", $langs->trans("ByUsers"), 2, $user->rights->compta->resultat->lire);
                    $newmenu->add("/compta/stats/cabyprodserv.php?leftmenu=report", $langs->trans("ByProductsAndServices"), 2, $user->rights->compta->resultat->lire);
                    $newmenu->add("/compta/stats/byratecountry.php?leftmenu=report", $langs->trans("ByVatRate"), 2, $user->rights->compta->resultat->lire);

                    // Journaux
                    $newmenu->add("/compta/journal/sellsjournal.php?leftmenu=report", $langs->trans("SellsJournal"), 1, $user->rights->compta->resultat->lire, '', '', '', 50);
                    $newmenu->add("/compta/journal/purchasesjournal.php?leftmenu=report", $langs->trans("PurchasesJournal"), 1, $user->rights->compta->resultat->lire, '', '', '', 51);
     //           } // InfraS change
                //if ($leftmenu=="ca") $newmenu->add("/compta/journaux/index.php?leftmenu=ca",$langs->trans("Journaux"),1,$user->rights->compta->resultat->lire||$user->rights->accounting->comptarapport->lire);
            }

			// Assets
			if (! empty($conf->asset->enabled))
			{
				$newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuAssets"), 0, $user->rights->asset->read, '', $mainmenu, 'asset');
				$newmenu->add("/asset/card.php?leftmenu=asset&amp;action=create", $langs->trans("MenuNewAsset"), 1, $user->rights->asset->write);
				$newmenu->add("/asset/list.php?leftmenu=asset&amp;mainmenu=accountancy", $langs->trans("MenuListAssets"), 1, $user->rights->asset->read);
				$newmenu->add("/asset/type.php?leftmenu=asset_type", $langs->trans("MenuTypeAssets"), 1, $user->rights->asset->read, '', $mainmenu, 'asset_type');
	//			if ($usemenuhider || empty($leftmenu) || preg_match('/asset_type/', $leftmenu)) { // InfraS change
                    $newmenu->add("/asset/type.php?leftmenu=asset_type&amp;action=create", $langs->trans("MenuNewTypeAssets"), 2, $user->rights->asset->configurer);
                    $newmenu->add("/asset/type.php?leftmenu=asset_type", $langs->trans("MenuListTypeAssets"), 2, $user->rights->asset->read);
     //           } // InfraS change
			}
		}


		/*
		 * Menu BANK
		 */
		if ($mainmenu == 'bank')
		{
			// Load translation files required by the page
			$langs->loadLangs(array("withdrawals","banks","bills","categories"));

			// Bank-Caisse
			if (! empty($conf->banque->enabled))
			{
				$newmenu->add("/compta/bank/list.php?leftmenu=bank&amp;mainmenu=bank", $langs->trans("MenuBankCash"), 0, $user->rights->banque->lire, '', $mainmenu, 'bank');

				$newmenu->add("/compta/bank/card.php?leftmenu=bank&amp;action=create", $langs->trans("MenuNewFinancialAccount"), 1, $user->rights->banque->configurer); // InfraS change
				$newmenu->add("/compta/bank/list.php?leftmenu=bank&amp;mainmenu=bank", $langs->trans("List"), 1, $user->rights->banque->lire, '', $mainmenu, 'bank');
				$newmenu->add("/compta/bank/bankentries_list.php?leftmenu=bank", $langs->trans("ListTransactions"), 1, $user->rights->banque->lire); // InfraS change
				$newmenu->add("/compta/bank/budget.php?leftmenu=bank", $langs->trans("ListTransactionsByCategory"), 1, $user->rights->banque->lire); // InfraS change

				$newmenu->add("/compta/bank/transfer.php?leftmenu=bank", $langs->trans("MenuBankInternalTransfer"), 1, $user->rights->banque->transfer); // InfraS change
			}

			if (! empty($conf->categorie->enabled))
			{
				$langs->load("categories");
				$newmenu->add("/categories/index.php?leftmenu=bank&amp;type=5", $langs->trans("Rubriques"), 1, $user->rights->categorie->creer, '', $mainmenu, 'tags'); // InfraS change
				$newmenu->add("/compta/bank/categ.php?leftmenu=bank", $langs->trans("RubriquesTransactions"), 1, $user->rights->categorie->creer, '', $mainmenu, 'tags'); // InfraS change
			}

			// Prelevements
			if (! empty($conf->prelevement->enabled))
			{
				$newmenu->add("/compta/prelevement/index.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("StandingOrders"), 0, $user->rights->prelevement->bons->lire, '', $mainmenu, 'withdraw');

	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="withdraw") { // InfraS change
                    //$newmenu->add("/compta/prelevement/demandes.php?status=0&amp;mainmenu=bank",$langs->trans("StandingOrderToProcess"),1,$user->rights->prelevement->bons->lire);

				    $newmenu->add("/compta/prelevement/create.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("NewStandingOrder"), 1, $user->rights->prelevement->bons->creer); // InfraS change

				    $newmenu->add("/compta/prelevement/bons.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("WithdrawalsReceipts"), 1, $user->rights->prelevement->bons->lire); // InfraS change
				    $newmenu->add("/compta/prelevement/list.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("WithdrawalsLines"), 1, $user->rights->prelevement->bons->lire); // InfraS change
				    $newmenu->add("/compta/prelevement/rejets.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("Rejects"), 1, $user->rights->prelevement->bons->lire); // InfraS change
				    $newmenu->add("/compta/prelevement/stats.php?leftmenu=withdraw&amp;mainmenu=bank", $langs->trans("Statistics"), 1, $user->rights->prelevement->bons->lire); // InfraS change

                    //$newmenu->add("/compta/prelevement/config.php",$langs->trans("Setup"),1,$user->rights->prelevement->bons->configurer);
    //            } // InfraS change
			}

			// Gestion cheques
			if (empty($conf->global->BANK_DISABLE_CHECK_DEPOSIT) && ! empty($conf->banque->enabled) && (! empty($conf->facture->enabled) || ! empty($conf->global->MAIN_MENU_CHEQUE_DEPOSIT_ON)))
			{
				$newmenu->add("/compta/paiement/cheque/index.php?leftmenu=checks&amp;mainmenu=bank", $langs->trans("MenuChequeDeposits"), 0, $user->rights->banque->cheque, '', $mainmenu, 'checks');
				if (preg_match('/checks/', $leftmenu)) {
                    $newmenu->add("/compta/paiement/cheque/card.php?leftmenu=checks_bis&amp;action=new&amp;mainmenu=bank", $langs->trans("NewChequeDeposit"), 1, $user->rights->banque->cheque);
                    $newmenu->add("/compta/paiement/cheque/list.php?leftmenu=checks_bis&amp;mainmenu=bank", $langs->trans("List"), 1, $user->rights->banque->cheque);
                }
			}

			// Cash Control
			if (! empty($conf->takepos->enabled) || ! empty($conf->cashdesk->enabled))
			{
				$permtomakecashfence = ($user->rights->cashdesk->use ||$user->rights->takepos->use);
				$newmenu->add("/compta/cashcontrol/cashcontrol_list.php?leftmenu=cashcontrol&amp;action=list", $langs->trans("POS"), 0, $permtomakecashfence, '', $mainmenu, 'cashcontrol'); // InfraS change
				$newmenu->add("/compta/cashcontrol/cashcontrol_card.php?leftmenu=cashcontrol&amp;action=create", $langs->trans("NewCashFence"), 1, $permtomakecashfence); // InfraS change
				$newmenu->add("/compta/cashcontrol/cashcontrol_list.php?leftmenu=cashcontrol&amp;action=list", $langs->trans("List"), 1, $permtomakecashfence); // InfraS change
			}
		}

		/*
		 * Menu PRODUCTS-SERVICES
		 */
		if ($mainmenu == 'products')
		{
			// Products
			if (! empty($conf->product->enabled))
			{
				$newmenu->add("/product/index.php?leftmenu=product&amp;type=0", $langs->trans("Products"), 0, $user->rights->produit->lire, '', $mainmenu, 'product');
				$newmenu->add("/product/card.php?leftmenu=product&amp;action=create&amp;type=0", $langs->trans("NewProduct"), 1, $user->rights->produit->creer);
				$newmenu->add("/product/list.php?leftmenu=product&amp;type=0", $langs->trans("List"), 1, $user->rights->produit->lire);
				if (! empty($conf->stock->enabled))
				{
					$newmenu->add("/product/reassort.php?leftmenu=product&amp;type=0", $langs->trans("MenuStocks"), 1, $user->rights->produit->lire && $user->rights->stock->lire); // InfraS change
				}
				if (! empty($conf->productbatch->enabled))
				{
					$langs->load("stocks");
					$newmenu->add("/product/reassortlot.php?leftmenu=product&amp;type=0", $langs->trans("StocksByLotSerial"), 1, $user->rights->produit->lire && $user->rights->stock->lire); // InfraS change
					$newmenu->add("/product/stock/productlot_list.php?leftmenu=product", $langs->trans("LotSerial"), 1, $user->rights->produit->lire && $user->rights->stock->lire); // InfraS change
				}
				if (! empty($conf->variants->enabled))
				{
					$newmenu->add("/variants/list.php?leftmenu=product", $langs->trans("VariantAttributes"), 1, $user->rights->produit->lire); // InfraS change
				}
				if (! empty($conf->propal->enabled) || ! empty($conf->commande->enabled) || ! empty($conf->facture->enabled) || ! empty($conf->fournisseur->enabled) || ! empty($conf->supplier_proposal->enabled))
				{
					$newmenu->add("/product/stats/card.php?id=all&leftmenu=product&type=0", $langs->trans("Statistics"), 1, $user->rights->produit->lire && $user->rights->propale->lire); // InfraS change
				}

				// Categories
				if (! empty($conf->categorie->enabled))
				{
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=product&amp;type=0", $langs->trans("Categories"), 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
				}
			}

			// Services
			if (! empty($conf->service->enabled))
			{
				$newmenu->add("/product/index.php?leftmenu=service&amp;type=1", $langs->trans("Services"), 0, $user->rights->service->lire, '', $mainmenu, 'service');
				$newmenu->add("/product/card.php?leftmenu=service&amp;action=create&amp;type=1", $langs->trans("NewService"), 1, $user->rights->service->creer);
				$newmenu->add("/product/list.php?leftmenu=service&amp;type=1", $langs->trans("List"), 1, $user->rights->service->lire);
				if (! empty($conf->propal->enabled) || ! empty($conf->commande->enabled) || ! empty($conf->facture->enabled) || ! empty($conf->fournisseur->enabled) || ! empty($conf->supplier_proposal->enabled))
				{
					$newmenu->add("/product/stats/card.php?id=all&leftmenu=service&type=1", $langs->trans("Statistics"), 1, $user->rights->service->lire && $user->rights->propale->lire); // InfraS change
				}
				// Categories
				if (! empty($conf->categorie->enabled))
				{
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=service&amp;type=0", $langs->trans("Categories"), 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
					//if ($usemenuhider || empty($leftmenu) || $leftmenu=="cat") $newmenu->add("/categories/list.php", $langs->trans("List"), 1, $user->rights->categorie->lire);
				}
			}

			// Warehouse
			if (! empty($conf->stock->enabled))
			{
				$langs->load("stocks");
				$newmenu->add("/product/stock/index.php?leftmenu=stock", $langs->trans("Warehouses"), 0, $user->rights->stock->lire, '', $mainmenu, 'stock');
				$newmenu->add("/product/stock/card.php?leftmenu=stock&action=create", $langs->trans("MenuNewWarehouse"), 1, $user->rights->stock->creer); // InfraS change
				$newmenu->add("/product/stock/list.php?leftmenu=stock", $langs->trans("List"), 1, $user->rights->stock->lire); // InfraS change
				$newmenu->add("/product/stock/movement_list.php?leftmenu=stock", $langs->trans("Movements"), 1, $user->rights->stock->mouvement->lire); // InfraS change

                $newmenu->add("/product/stock/massstockmove.php?leftmenu=stock", $langs->trans("MassStockTransferShort"), 1, $user->rights->stock->mouvement->creer); // InfraS change
                if ($conf->supplier_order->enabled) $newmenu->add("/product/stock/replenish.php?leftmenu=stock", $langs->trans("Replenishment"), 1, $user->rights->stock->mouvement->creer && $user->rights->fournisseur->lire); // InfraS change
			}

			// Inventory
			if ($conf->global->MAIN_FEATURES_LEVEL >= 2)
			{
    			if (! empty($conf->stock->enabled))
    			{
    				$langs->load("stocks");
					if (empty($conf->global->MAIN_USE_ADVANCED_PERMS))
					{
						$newmenu->add("/product/inventory/list.php?leftmenu=inventory", $langs->trans("Inventory"), 0, $user->rights->stock->lire, '', $mainmenu, 'stock'); // InfraS change
						$newmenu->add("/product/inventory/card.php?leftmenu=inventory&action=create", $langs->trans("NewInventory"), 1, $user->rights->stock->creer); // InfraS change
						$newmenu->add("/product/inventory/list.php?leftmenu=inventory", $langs->trans("List"), 1, $user->rights->stock->lire); // InfraS change
					}
					else
					{
						$newmenu->add("/product/inventory/list.php?leftmenu=inventory", $langs->trans("Inventory"), 0, $user->rights->stock->inventory_advance->read, '', $mainmenu, 'stock'); // InfraS change
						$newmenu->add("/product/inventory/card.php?leftmenu=inventory&action=create", $langs->trans("NewInventory"), 1, $user->rights->stock->inventory_advance->write); // InfraS change
						$newmenu->add("/product/inventory/list.php?leftmenu=inventory", $langs->trans("List"), 1, $user->rights->stock->inventory_advance->read); // InfraS change
					}
    			}
			}

			// Shipments
			if (! empty($conf->expedition->enabled))
			{
				$langs->load("sendings");
				$newmenu->add("/expedition/index.php?leftmenu=sendings", $langs->trans("Shipments"), 0, $user->rights->expedition->lire, '', $mainmenu, 'sendings');
				$newmenu->add("/expedition/card.php?action=create2&amp;leftmenu=sendings", $langs->trans("NewSending"), 1, $user->rights->expedition->creer);
				$newmenu->add("/expedition/list.php?leftmenu=sendings", $langs->trans("List"), 1, $user->rights->expedition->lire);
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="sendings") { // InfraS change
                    $newmenu->add("/expedition/list.php?leftmenu=sendings&viewstatut=0", $langs->trans("StatusSendingDraftShort"), 2, $user->rights->expedition->lire);
				    $newmenu->add("/expedition/list.php?leftmenu=sendings&viewstatut=1", $langs->trans("StatusSendingValidatedShort"), 2, $user->rights->expedition->lire);
                    $newmenu->add("/expedition/list.php?leftmenu=sendings&viewstatut=2", $langs->trans("StatusSendingProcessedShort"), 2, $user->rights->expedition->lire);
    //            } // InfraS change
				$newmenu->add("/expedition/stats/index.php?leftmenu=sendings", $langs->trans("Statistics"), 1, $user->rights->expedition->lire);
			}

            // Receptions
			if (! empty($conf->reception->enabled))
			{
				$langs->load("receptions");
				$newmenu->add("/reception/index.php?leftmenu=receptions", $langs->trans("Receptions"), 0, $user->rights->reception->lire, '', $mainmenu, 'receptions');
				$newmenu->add("/reception/card.php?action=create2&amp;leftmenu=receptions", $langs->trans("NewReception"), 1, $user->rights->reception->creer);
				$newmenu->add("/reception/list.php?leftmenu=receptions", $langs->trans("List"), 1, $user->rights->reception->lire);
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="receptions") // InfraS change
				$newmenu->add("/reception/list.php?leftmenu=receptions&viewstatut=0", $langs->trans("StatusReceptionDraftShort"), 2, $user->rights->reception->lire); // InfraS change
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="receptions") // InfraS change
				$newmenu->add("/reception/list.php?leftmenu=receptions&viewstatut=1", $langs->trans("StatusReceptionValidatedShort"), 2, $user->rights->reception->lire); // InfraS change
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="receptions") // InfraS change
				$newmenu->add("/reception/list.php?leftmenu=receptions&viewstatut=2", $langs->trans("StatusReceptionProcessedShort"), 2, $user->rights->reception->lire); // InfraS change
				$newmenu->add("/reception/stats/index.php?leftmenu=receptions", $langs->trans("Statistics"), 1, $user->rights->reception->lire);
			}
		}

		/*
		 * Menu PRODUCTS-SERVICES MRP
		 */
		if ($mainmenu == 'mrp')
		{
		    // BOM
		    if (! empty($conf->bom->enabled) || ! empty($conf->mrp->enabled))
		    {
		        $langs->load("mrp");

		        $newmenu->add("/mrp/index.php?mainmenu=mrp&leftmenu=bom", $langs->trans("MenuBOM"), 0, $user->rights->bom->read, '', $mainmenu, 'bom'); // InfraS change
		        $newmenu->add("/bom/bom_card.php?leftmenu=bom&amp;action=create", $langs->trans("NewBOM"), 1, $user->rights->bom->write);
		        $newmenu->add("/bom/bom_list.php?leftmenu=bom", $langs->trans("List"), 1, $user->rights->bom->read);
		    }
		}

		/*
		 * Menu PROJECTS
		 */
		if ($mainmenu == 'project')
		{
			if (! empty($conf->projet->enabled))
			{
				$langs->load("projects");

				$search_project_user = GETPOST('search_project_user', 'int');

				$tmpentry = array(
                    'enabled'=>(! empty($conf->projet->enabled)),
				    'perms'=>(! empty($user->rights->projet->lire)),
                    'module'=>'projet'
                );
				$showmode=isVisibleToUserType($type_user, $tmpentry, $listofmodulesforexternal);

				$titleboth=$langs->trans("LeadsOrProjects");
				$titlenew = $langs->trans("NewLeadOrProject");	// Leads and opportunities by default
				if ($conf->global->PROJECT_USE_OPPORTUNITIES == 0)
				{
					$titleboth=$langs->trans("Projects");
					$titlenew = $langs->trans("NewProject");
				}
				if ($conf->global->PROJECT_USE_OPPORTUNITIES == 2) {	// 2 = leads only
					$titleboth=$langs->trans("Leads");
					$titlenew = $langs->trans("NewLead");
				}

				// Project assigned to user
				$newmenu->add("/projet/index.php?leftmenu=projects".($search_project_user?'&search_project_user='.$search_project_user:''), $titleboth, 0, $user->rights->projet->lire, '', $mainmenu, 'projects');
				$newmenu->add("/projet/card.php?leftmenu=projects&action=create".($search_project_user?'&search_project_user='.$search_project_user:''), $titlenew, 1, $user->rights->projet->creer);

				if ($conf->global->PROJECT_USE_OPPORTUNITIES == 0)
				{
					$newmenu->add("/projet/list.php?leftmenu=projects".($search_project_user?'&search_project_user='.$search_project_user:'').'&search_status=99', $langs->trans("List"), 1, $showmode, '', 'project', 'list'); // InfraS change
				}
				elseif ($conf->global->PROJECT_USE_OPPORTUNITIES == 1)
				{
					$newmenu->add("/projet/list.php?leftmenu=projects".($search_project_user?'&search_project_user='.$search_project_user:''), $langs->trans("List"), 1, $showmode, '', 'project', 'list'); // InfraS change
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=projects&search_opp_status=openedopp&search_status=99&contextpage=lead', $langs->trans("ListOpenLeads"), 2, $showmode); // InfraS change
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=projects&search_opp_status=notopenedopp&search_status=99&contextpage=project', $langs->trans("ListOpenProjects"), 2, $showmode); // InfraS change
				}
				elseif ($conf->global->PROJECT_USE_OPPORTUNITIES == 2) {	// 2 = leads only
					$newmenu->add('/projet/list.php?mainmenu=project&amp;leftmenu=projects&search_opp_status=openedopp&search_status=99', $langs->trans("List"), 2, $showmode); // InfraS change
				}

				$newmenu->add("/projet/stats/index.php?leftmenu=projects", $langs->trans("Statistics"), 1, $user->rights->projet->lire);

				// Categories
				if (! empty($conf->categorie->enabled))
				{
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=projects&amp;type=6", $langs->trans("Categories"), 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
				}

				if (empty($conf->global->PROJECT_HIDE_TASKS))
				{
					// Project affected to user
					$newmenu->add("/projet/activity/index.php?leftmenu=tasks".($search_project_user?'&search_project_user='.$search_project_user:''), $langs->trans("Activities"), 0, $user->rights->projet->lire);
					$newmenu->add("/projet/tasks.php?leftmenu=tasks&action=create", $langs->trans("NewTask"), 1, $user->rights->projet->creer);
					$newmenu->add("/projet/tasks/list.php?leftmenu=tasks".($search_project_user?'&search_project_user='.$search_project_user:''), $langs->trans("List"), 1, $user->rights->projet->lire);
				    $newmenu->add("/projet/tasks/stats/index.php?leftmenu=tasks", $langs->trans("Statistics"), 1, $user->rights->projet->lire); // InfraS change

				    $newmenu->add("/projet/activity/perweek.php?leftmenu=tasks".($search_project_user?'&search_project_user='.$search_project_user:''), $langs->trans("NewTimeSpent"), 1, $user->rights->projet->lire); // InfraS change
				}
			}
		}

		/*
		 * Menu HRM
		*/
		if ($mainmenu == 'hrm')
		{
			// HRM module
			if (! empty($conf->hrm->enabled))
			{
				$langs->load("hrm");

				$newmenu->add("/user/list.php?mainmenu=hrm&leftmenu=hrm&mode=employee", $langs->trans("Employees"), 0, $user->rights->hrm->employee->read, '', $mainmenu, 'hrm');
				$newmenu->add("/user/card.php?mainmenu=hrm&leftmenu=hrm&action=create&employee=1", $langs->trans("NewEmployee"), 1, $user->rights->hrm->employee->write);
				$newmenu->add("/user/list.php?mainmenu=hrm&leftmenu=hrm&mode=employee&contextpage=employeelist", $langs->trans("List"), 1, $user->rights->hrm->employee->read);
			}

			// Leave/Holiday/Vacation module
			if (! empty($conf->holiday->enabled))
			{
			    // Load translation files required by the page
                $langs->loadLangs(array("holiday","trips"));

				$newmenu->add("/holiday/list.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("CPTitreMenu"), 0, $user->rights->holiday->read, '', $mainmenu, 'hrm'); // InfraS change
				$newmenu->add("/holiday/card.php?mainmenu=hrm&leftmenu=holiday&action=request", $langs->trans("New"), 1, $user->rights->holiday->write);
				$newmenu->add("/holiday/list.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("List"), 1, $user->rights->holiday->read); // InfraS change
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="hrm") { // InfraS change
                    $newmenu->add("/holiday/list.php?search_statut=1&mainmenu=hrm&leftmenu=holiday", $langs->trans("DraftCP"), 2, $user->rights->holiday->read); // InfraS change
				    $newmenu->add("/holiday/list.php?search_statut=2&mainmenu=hrm&leftmenu=holiday", $langs->trans("ToReviewCP"), 2, $user->rights->holiday->read); // InfraS change
				    $newmenu->add("/holiday/list.php?search_statut=3&mainmenu=hrm&leftmenu=holiday", $langs->trans("ApprovedCP"), 2, $user->rights->holiday->read); // InfraS change
				    $newmenu->add("/holiday/list.php?search_statut=4&mainmenu=hrm&leftmenu=holiday", $langs->trans("CancelCP"), 2, $user->rights->holiday->read); // InfraS change
                    $newmenu->add("/holiday/list.php?search_statut=5&mainmenu=hrm&leftmenu=holiday", $langs->trans("RefuseCP"), 2, $user->rights->holiday->read); // InfraS change
    //            } // InfraS change
				$newmenu->add("/holiday/define_holiday.php?mainmenu=hrm&leftmenu=holiday&action=request", $langs->trans("MenuConfCP"), 1, $user->rights->holiday->read); // InfraS change
				$newmenu->add("/holiday/month_report.php?mainmenu=hrm&leftmenu=holiday", $langs->trans("MenuReportMonth"), 1, $user->rights->holiday->read_all);
				$newmenu->add("/holiday/view_log.php?mainmenu=hrm&leftmenu=holiday&action=request", $langs->trans("MenuLogCP"), 1, $user->rights->holiday->define_holiday);
			}

			// Trips and expenses (old module)
			if (! empty($conf->deplacement->enabled))
			{
				$langs->load("trips");
				$newmenu->add("/compta/deplacement/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->rights->deplacement->lire, '', $mainmenu, 'tripsandexpenses');
				$newmenu->add("/compta/deplacement/card.php?action=create&amp;leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->rights->deplacement->creer);
				$newmenu->add("/compta/deplacement/list.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->rights->deplacement->lire);
				$newmenu->add("/compta/deplacement/stats/index.php?leftmenu=tripsandexpenses&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->rights->deplacement->lire);
			}

			// Expense report
			if (! empty($conf->expensereport->enabled))
			{
				$langs->load("trips");
				$newmenu->add("/expensereport/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("TripsAndExpenses"), 0, $user->rights->expensereport->lire, '', $mainmenu, 'expensereport');
				$newmenu->add("/expensereport/card.php?action=create&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("New"), 1, $user->rights->expensereport->creer);
				$newmenu->add("/expensereport/list.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("List"), 1, $user->rights->expensereport->lire);
	//			if ($usemenuhider || empty($leftmenu) || $leftmenu=="expensereport") { // InfraS change
                    $newmenu->add("/expensereport/list.php?search_status=0&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Draft"), 2, $user->rights->expensereport->lire);
				    $newmenu->add("/expensereport/list.php?search_status=2&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Validated"), 2, $user->rights->expensereport->lire);
				    $newmenu->add("/expensereport/list.php?search_status=5&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Approved"), 2, $user->rights->expensereport->lire);
				    $newmenu->add("/expensereport/list.php?search_status=6&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Paid"), 2, $user->rights->expensereport->lire);
				    $newmenu->add("/expensereport/list.php?search_status=4&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Canceled"), 2, $user->rights->expensereport->lire);
                    $newmenu->add("/expensereport/list.php?search_status=99&amp;leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Refused"), 2, $user->rights->expensereport->lire);
    //            } // InfraS change
				$newmenu->add("/expensereport/stats/index.php?leftmenu=expensereport&amp;mainmenu=hrm", $langs->trans("Statistics"), 1, $user->rights->expensereport->lire);
			}

			if (! empty($conf->projet->enabled))
			{
				if (empty($conf->global->PROJECT_HIDE_TASKS))
				{
					$langs->load("projects");

					$search_project_user = GETPOST('search_project_user', 'int');

					$newmenu->add("/projet/activity/perweek.php?leftmenu=tasks".($search_project_user?'&search_project_user='.$search_project_user:''), $langs->trans("NewTimeSpent"), 0, $user->rights->projet->lire);
				}
			}
		}


		/*
		 * Menu TOOLS
		 */
		if ($mainmenu == 'tools')
		{
			if (empty($user->socid)) // limit to internal users
			{
				$langs->load("mails");
				$newmenu->add("/admin/mails_templates.php?leftmenu=email_templates", $langs->trans("EMailTemplates"), 0, 1, '', $mainmenu, 'email_templates');
			}

			if (! empty($conf->mailing->enabled))
			{
				$newmenu->add("/comm/mailing/index.php?leftmenu=mailing", $langs->trans("EMailings"), 0, $user->rights->mailing->lire, '', $mainmenu, 'mailing');
				$newmenu->add("/comm/mailing/card.php?leftmenu=mailing&amp;action=create", $langs->trans("NewMailing"), 1, $user->rights->mailing->creer);
				$newmenu->add("/comm/mailing/list.php?leftmenu=mailing", $langs->trans("List"), 1, $user->rights->mailing->lire);
			}

			if (! empty($conf->export->enabled))
			{
				$langs->load("exports");
				$newmenu->add("/exports/index.php?leftmenu=export", $langs->trans("FormatedExport"), 0, $user->rights->export->lire, '', $mainmenu, 'export');
				$newmenu->add("/exports/export.php?leftmenu=export", $langs->trans("NewExport"), 1, $user->rights->export->creer);
				//$newmenu->add("/exports/export.php?leftmenu=export",$langs->trans("List"),1, $user->rights->export->lire);
			}

			if (! empty($conf->import->enabled))
			{
				$langs->load("exports");
				$newmenu->add("/imports/index.php?leftmenu=import", $langs->trans("FormatedImport"), 0, $user->rights->import->run, '', $mainmenu, 'import');
				$newmenu->add("/imports/import.php?leftmenu=import", $langs->trans("NewImport"), 1, $user->rights->import->run);
			}
		}

		/*
		 * Menu MEMBERS
		 */
		if ($mainmenu == 'members')
		{
			if (! empty($conf->adherent->enabled))
			{
				// Load translation files required by the page
                $langs->loadLangs(array("members","compta"));

				$newmenu->add("/adherents/index.php?leftmenu=members&amp;mainmenu=members", $langs->trans("Members"), 0, $user->rights->adherent->lire, '', $mainmenu, 'members');
				$newmenu->add("/adherents/card.php?leftmenu=members&amp;action=create", $langs->trans("NewMember"), 1, $user->rights->adherent->creer);
				$newmenu->add("/adherents/list.php?leftmenu=members", $langs->trans("List"), 1, $user->rights->adherent->lire);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=-1", $langs->trans("MenuMembersToValidate"), 2, $user->rights->adherent->lire);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1", $langs->trans("MenuMembersValidated"), 2, $user->rights->adherent->lire);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=uptodate", $langs->trans("MenuMembersUpToDate"), 2, $user->rights->adherent->lire);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=1&amp;filter=outofdate", $langs->trans("MenuMembersNotUpToDate"), 2, $user->rights->adherent->lire);
				$newmenu->add("/adherents/list.php?leftmenu=members&amp;statut=0", $langs->trans("MenuMembersResiliated"), 2, $user->rights->adherent->lire);
				$newmenu->add("/adherents/stats/index.php?leftmenu=members", $langs->trans("MenuMembersStats"), 1, $user->rights->adherent->lire);

				$newmenu->add("/adherents/cartes/carte.php?leftmenu=members", $langs->trans("MembersCards"), 1, $user->rights->adherent->export); // InfraS change
	//			if (! empty($conf->global->MEMBER_LINK_TO_HTPASSWDFILE) && ($usemenuhider || empty($leftmenu) || $leftmenu=='none' || $leftmenu=="members" || $leftmenu=="export")) // InfraS change
				$newmenu->add("/adherents/htpasswd.php?leftmenu=members", $langs->trans("Filehtpasswd"), 1, $user->rights->adherent->export); // InfraS change

				if (! empty($conf->categorie->enabled))
				{
					$langs->load("categories");
					$newmenu->add("/categories/index.php?leftmenu=members&amp;type=3", $langs->trans("Categories"), 1, $user->rights->categorie->lire, '', $mainmenu, 'cat'); // InfraS change
				}

				$newmenu->add("/adherents/index.php?leftmenu=subscription&amp;mainmenu=members", $langs->trans("Subscriptions"), 0, $user->rights->adherent->cotisation->lire); // InfraS change
				$newmenu->add("/adherents/list.php?leftmenu=subscription&amp;statut=-1,1&amp;mainmenu=members", $langs->trans("NewSubscription"), 1, $user->rights->adherent->cotisation->creer); // InfraS change
				$newmenu->add("/adherents/subscription/list.php?leftmenu=subscription", $langs->trans("List"), 1, $user->rights->adherent->cotisation->lire); // InfraS change
				$newmenu->add("/adherents/stats/index.php?leftmenu=subscription", $langs->trans("MenuMembersStats"), 1, $user->rights->adherent->lire); // InfraS change

				//$newmenu->add("/adherents/index.php?leftmenu=export&amp;mainmenu=members",$langs->trans("Tools"),0,$user->rights->adherent->export, '', $mainmenu, 'export');
				//if (! empty($conf->export->enabled) && ($usemenuhider || empty($leftmenu) || $leftmenu=="export")) $newmenu->add("/exports/index.php?leftmenu=export",$langs->trans("Datas"),1,$user->rights->adherent->export);

				// Type
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members", $langs->trans("MembersTypes"), 0, $user->rights->adherent->configurer, '', $mainmenu, 'setup');
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members&amp;action=create", $langs->trans("New"), 1, $user->rights->adherent->configurer);
				$newmenu->add("/adherents/type.php?leftmenu=setup&amp;mainmenu=members", $langs->trans("List"), 1, $user->rights->adherent->configurer);
			}
		}

		// Add personalized menus and modules menus
		//var_dump($newmenu->liste);    //
		$menuArbo = new Menubase($db, 'eldy');
		$newmenu = $menuArbo->menuLeftCharger($newmenu, $mainmenu, $leftmenu, (empty($user->societe_id)?0:1), 'eldy', $tabMenu);
		//var_dump($newmenu->liste);    //

		// We update newmenu for special dynamic menus
		if (!empty($user->rights->banque->lire) && $mainmenu == 'bank')	// Entry for each bank account
		{
			require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';

			$sql = "SELECT rowid, label, courant, rappro";
			$sql.= " FROM ".MAIN_DB_PREFIX."bank_account";
			$sql.= " WHERE entity = ".$conf->entity;
			$sql.= " AND clos = 0";
			$sql.= " ORDER BY label";

			$resql = $db->query($sql);
			if ($resql)
			{
				$numr = $db->num_rows($resql);
				$i = 0;

				if ($numr > 0) 	$newmenu->add('/compta/bank/list.php?leftmenu=banklist', $langs->trans("BankAccounts"), 0, $user->rights->banque->lire); // InfraS change

				while ($i < $numr)
				{
					$objp = $db->fetch_object($resql);
					$newmenu->add('/compta/bank/card.php?id='.$objp->rowid, $objp->label, 1, $user->rights->banque->lire);
					if ($objp->rappro && $objp->courant != Account::TYPE_CASH && empty($objp->clos))  // If not cash account and not closed and can be reconciliate
					{
						$newmenu->add('/compta/bank/bankentries_list.php?leftmenu=banklist&action=reconcile&contextpage=banktransactionlist-'.$objp->rowid.'&account='.$objp->rowid.'&id='.$objp->rowid.'&search_conciliated=0', $langs->trans("Conciliate"), 2, $user->rights->banque->consolidate); // InfraS change
					}
					$i++;
				}
			}
			else dol_print_error($db);
			$db->free($resql);
		}

		if (!empty($conf->ftp->enabled) && $mainmenu == 'ftp')	// Entry for FTP
		{
			$MAXFTP=20;
			$i=1;
			while ($i <= $MAXFTP)
			{
				$paramkey='FTP_NAME_'.$i;
				//print $paramkey;
				if (! empty($conf->global->$paramkey))
				{
					$link="/ftp/index.php?idmenu=".$_SESSION["idmenu"]."&numero_ftp=".$i;

					$newmenu->add($link, dol_trunc($conf->global->$paramkey, 24));
				}
				$i++;
			}
		}
	}

	//var_dump($tabMenu);    //
	//var_dump($newmenu->liste);

	// Build final $menu_array = $menu_array_before +$newmenu->liste + $menu_array_after
	//var_dump($menu_array_before);exit;
	//var_dump($menu_array_after);exit;
	$menu_array=$newmenu->liste;
	if (is_array($menu_array_before)) $menu_array=array_merge($menu_array_before, $menu_array);
	if (is_array($menu_array_after))  $menu_array=array_merge($menu_array, $menu_array_after);
	//var_dump($menu_array);exit;
	if (! is_array($menu_array)) return 0;

	// TODO Use the position property in menu_array to reorder the $menu_array
	//var_dump($menu_array);
	/*$new_menu_array = array();
	$level=0; $cusor=0; $position=0;
	$nbentry = count($menu_array);
	while (findNextEntryForLevel($menu_array, $cursor, $position, $level))
	{

	    $cursor++;
	}*/

	// Show menu
	$invert=empty($conf->global->MAIN_MENU_INVERT)?"":"invert";
	if (empty($noout))
	{
		$altok=0; $blockvmenuopened=false; $lastlevel0='';
		$num=count($menu_array);
		for ($i = 0; $i < $num; $i++)     // Loop on each menu entry
		{
			$showmenu=true;
			if (! empty($conf->global->MAIN_MENU_HIDE_UNAUTHORIZED) && empty($menu_array[$i]['enabled'])) 	$showmenu=false;

			// Begin of new left menu block
			if (empty($menu_array[$i]['level']) && $showmenu)
			{
				$altok++;
				$blockvmenuopened=true;
				$lastopened=true;
				for($j = ($i + 1); $j < $num; $j++)
				{
				    if (empty($menu_array[$j]['level'])) $lastopened=false;
				}
				if ($altok % 2 == 0)
				{
					print '<div class="blockvmenu blockvmenuimpair'.$invert.($lastopened?' blockvmenulast':'').($altok == 1 ? ' blockvmenufirst':'').'">'."\n";
				}
				else
				{
					print '<div class="blockvmenu blockvmenupair'.$invert.($lastopened?' blockvmenulast':'').($altok == 1 ? ' blockvmenufirst':'').'">'."\n";
				}
			}

			// Add tabulation
			$tabstring='';
			$tabul=($menu_array[$i]['level'] - 1);
			if ($tabul > 0)
			{
				for ($j=0; $j < $tabul; $j++)
				{
					$tabstring.='&nbsp;&nbsp;&nbsp;';
				}
			}

			// $menu_array[$i]['url'] can be a relative url, a full external url. We try substitution
			$substitarray = array('__LOGIN__' => $user->login, '__USER_ID__' => $user->id, '__USER_SUPERVISOR_ID__' => $user->fk_user);
			$substitarray['__USERID__'] = $user->id;	// For backward compatibility
			$menu_array[$i]['url'] = make_substitutions($menu_array[$i]['url'], $substitarray);

			$url = $shorturl = $shorturlwithoutparam = $menu_array[$i]['url'];
			if (! preg_match("/^(http:\/\/|https:\/\/)/i", $menu_array[$i]['url']))
			{
			    $tmp=explode('?', $menu_array[$i]['url'], 2);
			    $url = $shorturl = $tmp[0];
			    $param = (isset($tmp[1])?$tmp[1]:'');    // params in url of the menu link

			    // Complete param to force leftmenu to '' to close open menu when we click on a link with no leftmenu defined.
			    if ((! preg_match('/mainmenu/i', $param)) && (! preg_match('/leftmenu/i', $param)) && ! empty($menu_array[$i]['mainmenu']))
			    {
			        $param.=($param?'&':'').'mainmenu='.$menu_array[$i]['mainmenu'].'&leftmenu=';
			    }
			    if ((! preg_match('/mainmenu/i', $param)) && (! preg_match('/leftmenu/i', $param)) && empty($menu_array[$i]['mainmenu']))
			    {
			        $param.=($param?'&':'').'leftmenu=';
			    }
			    //$url.="idmenu=".$menu_array[$i]['rowid'];    // Already done by menuLoad
			    $url = dol_buildpath($url, 1).($param?'?'.$param:'');
			    $shorturlwithoutparam = $shorturl;
			    $shorturl = $shorturl.($param?'?'.$param:'');
			}


			print '<!-- Process menu entry with mainmenu='.$menu_array[$i]['mainmenu'].', leftmenu='.$menu_array[$i]['leftmenu'].', level='.$menu_array[$i]['level'].' enabled='.$menu_array[$i]['enabled'].', position='.$menu_array[$i]['position'].' -->'."\n";

			// Menu level 0
			if ($menu_array[$i]['level'] == 0)
			{
				if ($menu_array[$i]['enabled'])     // Enabled so visible
				{
					print '<div class="menu_titre">'.$tabstring;
					if ($shorturlwithoutparam) print '<a class="vmenu" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>';
					else print '<span class="vmenu">';
					print ($menu_array[$i]['prefix']?$menu_array[$i]['prefix']:'').$menu_array[$i]['titre'];
					if ($shorturlwithoutparam) print '</a>';
					else print '</span>';
					print '&nbsp;&nbsp;&nbsp;<span class="infrastoggle fa fa-caret-down" style="color: #888;"></span>';	// InfraS ajout
					print '</div>'."\n";
					$lastlevel0='enabled';
				}
				elseif ($showmenu)                 // Not enabled but visible (so greyed)
				{
					print '<div class="menu_titre">'.$tabstring.'<font class="vmenudisabled">'.$menu_array[$i]['titre'].'</font></div>'."\n";
					$lastlevel0='greyed';
				}
				else
				{
				    $lastlevel0='hidden';
				}
				if ($showmenu)
				{
					print '<div class="menu_top"></div>'."\n";
				}
			}

			// Menu level > 0
			if ($menu_array[$i]['level'] > 0)
			{
				$cssmenu = '';
				if ($menu_array[$i]['url']) $cssmenu = ' menu_contenu'.dol_string_nospecial(preg_replace('/\.php.*$/', '', $menu_array[$i]['url']));

				if ($menu_array[$i]['enabled'] && $lastlevel0 == 'enabled')     // Enabled so visible, except if parent was not enabled.
				{
					print '<div class="menu_contenu'.$cssmenu.'">'.$tabstring;
					if ($shorturlwithoutparam) print '<a class="vsmenu" href="'.$url.'"'.($menu_array[$i]['target']?' target="'.$menu_array[$i]['target'].'"':'').'>';
					else print '<span class="vsmenu">';
					print $menu_array[$i]['titre'];
					if ($shorturlwithoutparam) print '</a>';
					else print '</span>';
					// If title is not pure text and contains a table, no carriage return added
					if (! strstr($menu_array[$i]['titre'], '<table')) print '<br>';
					print '</div>'."\n";
				}
				elseif ($showmenu && $lastlevel0 == 'enabled')       // Not enabled but visible (so greyed), except if parent was not enabled.
				{
					print '<div class="menu_contenu'.$cssmenu.'">'.$tabstring.'<font class="vsmenudisabled vsmenudisabledmargin">'.$menu_array[$i]['titre'].'</font><br></div>'."\n";
				}
			}

			// If next is a new block or if there is nothing after
			if (empty($menu_array[$i+1]['level']))               // End menu block
			{
				if ($showmenu)
					print '<div class="menu_end"></div>'."\n";
				if ($blockvmenuopened) { print '</div>'."\n"; $blockvmenuopened=false; }
			}
		}

		if ($altok) print '<div class="blockvmenuend"></div>';    // End menu block
	}

	return count($menu_array);
}
