<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckDevHelper
abstract class JCckDevHelper
{
	public static function formatBytes( $bytes, $precision = 2 )
	{ 
		$units	=	array( 'B', 'KB', 'MB', 'GB', 'TB' ); 
	   
		$bytes	=	max( $bytes, 0 );
		$pow	=	floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow	=	min( $pow, count( $units ) - 1 );
		$bytes	/=	pow( 1024, $pow );
		
		return round( $bytes, $precision ).' '.$units[$pow];
	}
	
	// getBranch
	public static function getBranch( $table, $pk )
	{
		$query 	= 'SELECT s.id, (COUNT(parent.id) - (branch.depth2 + 1)) AS depth2'
				. ' FROM '.$table.' AS s,'
				. $table.' AS parent,'
				. $table.' AS subparent,'
				. ' ('
					. ' SELECT s.id, (COUNT(parent.id) - 1) AS depth2'
					. ' FROM '.$table.' AS s,'
					. $table.' AS parent'
					. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
					. ' AND s.id ='.(int)$pk
					. ' GROUP BY s.id'
					. ' ORDER BY s.lft'
					. ' ) AS branch'
				. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
				. ' AND s.lft BETWEEN subparent.lft AND subparent.rgt'
				. ' AND subparent.id = branch.id'
				. ' GROUP BY s.id'
				. ' ORDER BY s.lft';
		$items	=	JCckDatabase::loadColumn( $query );
		
		return( $items );
	}

	// getRules
	public static function getRules( $rules, $default = '{}' )
	{
		$json	=	'';
		
		if ( count( $rules ) ) {
			foreach ( $rules as $name => $r ) {
				$j	=	'';
				foreach ( $r as $k => $v ) {
					if ( $v != '' ) {
						$j	.=	'"'.$k.'":'.$v.',';
					}
				}
				$json	.=	'"'.$name.'":'.( $j ? '{'.substr( $j, 0, -1 ).'}' : '[]' ).',';
			}
			$json	=	substr( $json, 0, -1 );
		}
		
		return ( $json != '' ) ? '{'.$json.'}' : $default;
	}
	
	// getRouteParams
	public static function getRouteParams( $name )
	{
		static $params	=	array();

		if ( !isset( $params[$name] ) ) {
			$object				=	JCckDatabase::loadObject( 'SELECT a.storage_location, a.options FROM #__cck_core_searchs AS a WHERE a.name = "'.$name.'"' );
			$object->options	=	json_decode( $object->options );

			$params[$name]				=	array();
			$params[$name]['doSEF']		=	( $object->options->sef != '' ) ? $object->options->sef : JCck::getConfig_Param( 'sef', '2' );
			$params[$name]['join_key']	=	'pk';
			$params[$name]['location']	=	$object->storage_location ? $object->storage_location : 'joomla_article';
		}
		
		return $params[$name];
	}

	// getUrlVars
	public static function getUrlVars( $url )
	{
		if ( ( $pos = strpos( $url, '?') ) !== false ) {
			$url	=	substr( $url, $pos + 1 );
		}
		$vars	=	explode( '&', $url );
		$url	=	array();
		if ( count( $vars ) ) {
			foreach ( $vars as $var ) {
				$v	=	explode( '=', $var );
				if ( $v[0] ) {
					$url[$v[0]]	=	$v[1];
				}
			}
		}
		
		$url	=	new JRegistry( $url );
		
		return $url;
	}
	
	// matchUrlVars
	public static function matchUrlVars( $vars, $url = NULL )
	{
		$app	=	JFactory::getApplication();
		$custom	=	( is_object( $url ) ) ? true : false;
		$vars	=	explode( '&', $vars );
		$count	=	count( $vars );

		if ( $count ) {
			foreach ( $vars as $var ) {
				if ( $var ) {
					$v	=	explode( '=', $var );
					$x	=	( $custom !== false ) ? $url->get( $v[0], '' ) : $app->input->get( $v[0], '' );
					if ( $x == $v[1] ) {
						$count--;
					}
				}
			}
		}
		if ( $count > 0 ) {
			return false;
		}
		
		return true;
	}
}
?>