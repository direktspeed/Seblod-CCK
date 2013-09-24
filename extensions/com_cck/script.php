<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

// Script
class com_cckInstallerScript
{
	// install
	function install( $parent )
	{
	}
	
	// uninstall
	function uninstall( $parent )
	{
		$db		=	JFactory::getDbo();
		$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE type = "package" AND element = "pkg_cck"' );
		$eid	=	$db->loadResult();
		
		$db->setQuery( 'SELECT extension_id FROM #__extensions WHERE type = "plugin" AND element = "cck" AND folder="system"' );
		$cck	=	$db->loadResult();
		
		// Uninstall FULL PACKAGE only if package exists && system plugin exists..
		if ( $eid && $cck ) {
			$manifest	=	JPATH_ADMINISTRATOR.'/manifests/packages/pkg_cck.xml';
			if ( JFile::exists( $manifest ) ) {
				$xml	=	JFactory::getXML( $manifest ); // Keep it this way until platform 13.x
			}
			if ( isset( $xml->files ) ) {
				unset( $xml->files->file[3] );
				$xml->asXML( $manifest );
			}
			
			jimport( 'joomla.installer.installer' );
			$installer	=	JInstaller::getInstance();
			$installer->uninstall( 'package', $eid );
		}
	}
	
	// update
	function update( $parent )
	{
		// WAITING FOR JOOMLA 1.7.x FIX
		$app		=	JFactory::getApplication();
		$config		=	JFactory::getConfig();
		$tmp_path	=	$config->get( 'tmp_path' );
		$tmp_dir 	=	uniqid( 'cck_var_' );
		$path 		= 	$tmp_path.'/'.$tmp_dir;
		$src		=	JPATH_SITE.'/libraries/cck/rendering/variations';
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, $path );
			$app->cck_core_temp_var	=	$tmp_dir;
		}
		// WAITING FOR JOOMLA 1.7.x FIX
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$version	=	new JVersion;
		
		if ( version_compare( $version->getShortVersion(), '2.5.0', 'lt' ) ) {
			Jerror::raiseWarning( null, 'This package IS NOT meant to be used on Joomla! 1.7. You should upgrade your site with Joomla 2.5 first, and then install it again !' );
			return false;
		}
		
		$app		=	JFactory::getApplication();
		$lang		=	JFactory::getLanguage();
		
		$app->cck_core				=	true;
		$app->cck_core_version_old	=	self::_getVersion();
		
		set_time_limit( 0 );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		$app	=	JFactory::getApplication();
		$db		=	JFactory::getDbo();
		
		$app->cck_core_version		=	self::_getVersion();
		
		if ( $type == 'update' ) {
			$params	=	JComponentHelper::getParams( 'com_cck' );
			$uix	=	$params->get( 'uix', '' );
			if ( $uix == 'nano' ) {
				$params->set( 'uix', '' );
				$db	=	JFactory::getDbo();
				$db->setQuery( 'UPDATE #__extensions SET params = "'.$db->escape( $params->toString() ).'" WHERE element = "com_cck"' );
				$db->execute();
			}
		} elseif ( 'install' ) {
			$rule	=	'{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":[],"core.delete":[],"core.delete.own":{"6":1},"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';			
			$query	=	'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE name = "com_cck"';
			$db->setQuery( $query );
			$db->execute();
		}
		
		// CMS Autoloader
		$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/cms';
		if ( JFolder::exists( $src ) ) {
			JFolder::copy( $src, JPATH_SITE.'/libraries/cms/cck', '', true );
		}
		if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
			jimport( 'cck.base.cck_5_2' );
			$src	=	JPATH_ADMINISTRATOR.'/components/com_cck/install/src/php5.2/libraries/cms/cck/cck.php';
			if ( JFile::exists( $src ) ) {
				JFile::copy( $src, JPATH_SITE.'/libraries/cms/cck/cck.php' );
			}
		}
	}
	
	// _getVersion
	function _getVersion( $default = '2.0.0' )
	{
		$db		=	JFactory::getDbo();
		
		$db->setQuery( 'SELECT manifest_cache FROM #__extensions WHERE element = "com_cck"' );
		$res		=	$db->loadResult();
		$registry	=	new JRegistry;
		$registry->loadString( $res );
		
		return $registry->get( 'version', $default );
	}
}
?>