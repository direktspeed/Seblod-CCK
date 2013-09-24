<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'cck.base.install.install' );

// Script
class JCckInstallerScriptPlugin
{
	protected $cck;
	protected $core;
	
	// install
	function install( $parent )
	{
		if ( $this->core === true ) {
			return;
		}
		$db		=	JFactory::getDbo();

		// Publish
		$query	=	'UPDATE #__extensions SET enabled = 1 WHERE type = "'.$this->cck->type.'" AND element = "'.$this->cck->element.'"';
		$query	=	( $this->cck->group ) ? $query.' AND folder = "'.$this->cck->group.'"' : $query;
		$db->setQuery( $query );
		$db->query();

		// Integration
		if ( $this->cck->group == 'cck_storage_location' ) {
			if ( isset( $this->cck->xml->cck_integration ) ) {
				JFactory::getLanguage()->load( 'plg_cck_storage_location_'.$this->cck->element, JPATH_ADMINISTRATOR, 'en-GB' );
				$integration	=	array( 'component', 'options', 'vars', 'view' );
				$title			=	JText::_( 'PLG_CCK_STORAGE_LOCATION_'.$this->cck->element.'_LABEL2' );
				foreach ( $integration as $i=>$elem ) {
					if ( isset( $this->cck->xml->cck_integration->$elem ) ) {
						$integration[$elem]	=	(string)$this->cck->xml->cck_integration->$elem;
						unset( $integration[$i] );
					}
				}
				$query			=	'INSERT IGNORE INTO #__cck_core_objects (`title`,`name`,`component`,`options`,`vars`,`view`)'
								.	' VALUES ("'.$title.'", "'.$this->cck->element.'", "'.$integration['component'].'", "'.$db->escape( $integration['options'] ).'", "'.$integration['vars'].'", "'.$integration['view'].'")';
				$db->setQuery( $query );
				$db->query();
			}
		}
	}
	
	// uninstall
	function uninstall( $parent )
	{
		$db		=	JFactory::getDbo();
		
		// Integration
		if ( $this->cck->group == 'cck_storage_location' ) {
			$db->setQuery( 'DELETE FROM #__cck_core_objects WHERE name = "'.$this->cck->element.'"' );
			$db->query();
		}
	}
	
	// update
	function update( $parent )
	{
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$app		=	JFactory::getApplication();
		$this->core	=	( isset( $app->cck_core ) ) ? $app->cck_core : false;
		if ( $this->core === true ) {
			return;
		}
		$this->cck	=	CCK_Install::init( $parent );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		if ( $this->core === true ) {
			return;
		}
		CCK_Install::import( $parent, 'install', $this->cck );
	}
}
?>