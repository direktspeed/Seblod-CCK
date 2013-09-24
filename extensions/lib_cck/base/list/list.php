<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// List
class CCK_List
{
	// generateRange
	public static function generateRange( $min, $max )
	{
		$digit		=	'[0-9]';
		$lenMax		=	strlen( $max );
		$lenMin		=	strlen( $min );
		$lenDiff	=	$lenMax - $lenMin;
		$min 		=	str_pad( $min, $lenMax, 0, STR_PAD_LEFT );
		$max		=	(string)$max;
		
		// find length of common prefix
		for ( $i = 0; $i < $lenMin && $min[$i] == $max[$i]; $i++ );
		$prefixLength	=	$i;
		// add non-conflicting ranges from each end
		for ( $i = $lenMax, $j = 0; $i-- > 1 + $prefixLength; $j++ ) {
			$lower	=	$min[$i];
			$upper	=	$max[$i];
			// correct bounds if not final range
			if ( $j ) {
				++$lower;
				--$upper;
			}
			// lower bound
			if ( $lower < 10 ) {
				$char		=	( $lower == 9 ) ? 9 : '[' . $lower . '-9]';
				$pattern[]	=	( $j >= $lenMin ? '' : substr( $min, $lenDiff, $i - $lenDiff ) ) . $char . str_repeat( $digit, $j );
			}
			// upper bound
			if ( $upper >= 0 ) {
				$char		=	$upper ? '[0-' . $upper . ']' : 0;
				$pattern[]	=	substr($max, 0, $i) . $char . str_repeat( $digit, $j );
			}
		}
		// add middle range
		if ( !$j || $max[$prefixLength] - $min[$prefixLength] > 1 ) {
			$prefix	=	substr( $min, 0, $prefixLength );
			$lower	=	@$min[$prefixLength];
			$upper	=	@$max[$prefixLength];
			// correct bounds if not final range
			if ( $j && $i == $prefixLength ) {
				++$lower;
				--$upper;
			}
			$char		=	( $lower == $upper ) ? $lower : '[' . $lower . '-' . $upper . ']';
			$pattern[]	=	$prefix . $char . @str_repeat( $digit, $lenMax - $prefixLength - 1 );
		}
	 
		return join( '|', $pattern );
	}

	// getFields
	public static function getFields( $search, $client, $excluded = '', $idx = true, $cck = false )
	{
		// Client
		if ( $client == 'all' )  {
			$where 	=	' WHERE b.name = "'.$search.'"';
		} else {
			$where 	=	' WHERE b.name = "'.$search.'" AND c.client = "'.$client.'"';
		}
		if ( $excluded != '' ) {
			$where	.=	' AND a.id NOT IN ('.$excluded.')';
		}
		
		// Access
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$where	.=	' AND c.access IN ('.$access.')';
		
		$query	=	' SELECT DISTINCT a.*, c.client,'
				.	' c.label as label2, c.variation, c.variation_override, c.required, c.required_alert, c.validation, c.validation_options, c.live, c.live_options, c.live_value, c.markup_class, c.match_collection, c.match_mode, c.match_options, c.match_value, c.stage, c.access, c.computation, c.computation_options, c.conditional, c.conditional_options, c.position'
				.	' FROM #__cck_core_fields AS a '
				.	' LEFT JOIN #__cck_core_search_field AS c ON c.fieldid = a.id'
				. 	' LEFT JOIN #__cck_core_searchs AS b ON b.id = c.searchid'
				.	$where
				.	' ORDER BY c.ordering ASC';
				;
		$fields	=	( $idx ) ? JCckDatabase::loadObjectList( $query, 'name' ) : JCckDatabase::loadObjectList( $query ); //#
		
		if ( ! count( $fields ) ) {
			$fields	=	array();
		}
		
		return $fields;
	}
	
	// getFields_Items
	public static function getFields_Items( $search_name, $client, $access )
	{
		$query		=	'SELECT cc.*, c.label as label2, c.variation, c.link, c.link_options, c.markup_class, c.typo, c.typo_label, c.typo_options, c.access, c.position'
					.	' FROM #__cck_core_search_field AS c'
					.	' LEFT JOIN #__cck_core_searchs AS sc ON sc.id = c.searchid'
					.	' LEFT JOIN #__cck_core_fields AS cc ON cc.id = c.fieldid'
					.	' WHERE sc.name = "'.$search_name.'" AND sc.published = 1 AND c.client = "'.$client.'" AND c.access IN ('.$access.')'
					.	' ORDER BY c.ordering ASC'
					;
		
		return JCckDatabase::loadObjectList( $query, 'name' ); //#
	}
	
	// getList
	public static function getList( $ordering, $areas, $fields, $fields_order, &$config, $current, $options, $user )
	{
		JPluginHelper::importPlugin( 'search', 'cck' );
		$doCache	=	$options->get( 'cache' );
		$doDebug	=	$options->get( 'debug' );
		$dispatcher	=	JDispatcher::getInstance();
		
		// Debug
		if ( $doDebug ) {
			$profiler	=	JProfiler::getInstance();
		}
		
		if ( $doCache ) {
			$group		=	( $doCache == '2' ) ? 'com_cck_'.$config['type'] : 'com_cck';
			$cache		=	JFactory::getCache( $group );
			$cache->setCaching( 1 );
			$isCached	=	' [Cache=ON]';
			$user		=	( $doCache == '3' && $user->id > 0 ) ? $user : NULL;
			$results	=	$cache->call( array( $dispatcher, 'trigger' ), 'onContentSearch',
										  array( '', '', $ordering, $areas['active'], $fields, $fields_order, &$config, $current, $options, $user ) );
		} else {
			$isCached	=	' [Cache=OFF]';
			$results	=	$dispatcher->trigger( 'onContentSearch', array( '', '', $ordering, $areas['active'], $fields, $fields_order, &$config, $current, $options, $user ) );
		}
		$list			=	isset( $results[0] ) ? $results[0] : array();
		
		// Debug
		if ( $doDebug ) {
			echo $profiler->mark( 'afterSearch'.$isCached ).'<br />';
			if ( isset( $current['stage'] ) && (int)$current['stage'] > 0 ) {
				echo '<br />';
			}
		}
		
		return $list;
	}
	
	// getPositions
	public static function getPositions( $search_id, $client )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$search_id.'_'.$client] ) ) {
			$cache[$search_id.'_'.$client]	=	JCckDatabase::loadObjectList( 'SELECT a.position, a.client, a.legend, a.variation, a.variation_options, a.width, a.height, a.css'
																			. ' FROM #__cck_core_search_position AS a'
																			. ' WHERE a.searchid = '.(int)$search_id.' AND a.client ="'.(string)$client.'"', 'position' );
		}
		
		return $cache[$search_id.'_'.$client];
	}

	// getSearch
	public static function getSearch( $name, $id, $location = '' )
	{
		// todo: API (move)
		$query	=	'SELECT a.id, a.title, a.name, a.description, a.content, a.location, a.storage_location, b.app as folder_app,'
				.	' a.options, a.template_search, a.template_filter, a.template_list, a.template_item'
				.	' FROM #__cck_core_searchs AS a'
				.	' LEFT JOIN #__cck_core_folders AS b ON b.id = a.folder'
				.	' WHERE a.name ="'.(string)$name.'" AND a.published = 1';
		
		return JCckDatabase::loadObject( $query );
	}
	
	// getTemplate
	public static function getTemplateStyle( $id )
	{
		if ( ! $id ) {
			return;
		}
		static $cache	=	array();

		if ( !isset( $cache[$id] ) ) {
			$query			=	'SELECT a.id, a.template as name, a.params FROM #__template_styles AS a'
							.	' LEFT JOIN #__cck_core_templates AS b ON b.name = a.template'
							.	' WHERE a.id = '.(int)$id.' AND b.published = 1'
							;
			$cache[$id]	=	JCckDatabase::loadObject( $query );
		}

		return $cache[$id];
	}
	
	// render
	public static function render( $list, $search, $path, $client, $itemId, $options )
	{
		$app	=	JFactory::getApplication();
		$user	=	JFactory::getUser();
		$access	=	implode( ',', $user->getAuthorisedViewLevels() );
		$data	=	'';
		
		include JPATH_LIBRARIES_CCK.'/base/list/list_inc_list.php';
		
		if ( $options->get( 'prepare_content', JCck::getConfig_Param( 'prepare_content', 1 ) ) ) {
			JPluginHelper::importPlugin( 'content' );
			$data	=	JHtml::_( 'content.prepare', $data );
		}
		
		return $data;
	}
}
?>