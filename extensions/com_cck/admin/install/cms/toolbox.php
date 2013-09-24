<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: ecommerce.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerce
abstract class JCckToolbox
{
	public static $_me			=	'cck_toolbox';
	public static $_config		=	NULL;

	public static $processing	=	NULL;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config
	
	// _setConfig
	public static function _setConfig()
	{		
		if ( self::$_config ) {
			return self::$_config;
		}

		$config			=	JComponentHelper::getParams( 'com_'.self::$_me );
		
		self::$_config	=&	$config;
	}
	
	// getConfig
	public static function getConfig()
	{
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Processing
	
	// process
	public static function process( $event )
	{
		$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile FROM #__cck_more_toolbox_processings WHERE published = 1 ORDER BY ordering', 'type' );

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					include_once JPATH_SITE.$p->scriptfile;
				}
			}
		}
	}
}
?>