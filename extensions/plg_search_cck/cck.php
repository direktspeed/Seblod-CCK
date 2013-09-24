<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cck.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgSearchCCK extends JPlugin
{
	// __construct
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
		
		$this->loadLanguage();
	}
	
	// onContentSearchAreas
	public function onContentSearchAreas()
	{
		static $areas	=	array( 'cck'=>'CCK' );
		
		return $areas;
	}
	
	// onContentSearch
	public function onContentSearch( $text = '', $phrase = '', $ordering = '', $areas = NULL, $fields = array(), $fields_order = array(), &$config = array(), $current = array(), $options = NULL, $user = NULL )
	{
		if ( is_array( $areas ) ) {
			if ( ! array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) ) ) {
				return array();
			}
		} else {
			return array();
		}
		if ( !count( $fields ) ) {
			return array();
		}
		if ( ! $user ) {
			$user	=	JCck::getUser();
		}
		if ( !is_object( $options ) ) {
			$options	=	new JRegistry;
		}
		
		$db				=	JFactory::getDbo();
		$dispatcher		=	JDispatcher::getInstance();
		$doLimit		=	false;
		$limit			=	$options->get( 'limit' );
		$glues			=	1;
		$order			=	'';
		$order_string	=	'';
		$where			=	'';
		$w				=	-1;
		$where2			=	array();
		$t				=	1;
		$t2				=	1;
		$tables			=	array( '#__cck_core'=>array( '_'=>'t0', 'fields'=>array(), 'join'=>1 ) );
		$colums			=	array();
		
		if ( isset( $config['joins'] ) && is_array( $config['joins'] ) ) {
			foreach ( $config['joins'] as $j ) {
				if ( $j->table ) {
					if ( !isset( $tables[$j->table] ) ) {
						$tables[$j->table]	=	array( '_'=>'t'.$t++, 'fields'=>array(), 'key'=>$j->column, 'join'=>2, 'join_key'=>$j->column2, 'join_table'=>$j->table2 );
					}
				}
			}
			$t2		=	count( $tables );
		}
		foreach ( $fields as $field ) {
			if ( $current['stage'] != (int)$field->stage ) {
				continue;
			}
			$name2	=	( $field->match_collection != '' ) ? '\\\|[0-9]\\\|'.$field->match_collection : '';
			$value	=	( $field->live == 'stage' ) ? $current['stages'][(!$field->live_value) ? 1 : $field->live_value] : $field->value;
			$Pf		=	$field->storage_field;
			$Pt		=	$field->storage_table;
			
			if ( ((( $value !== '' && $field->match_mode != 'none' ) || ( $field->match_mode == 'not_empty' || $field->match_mode == 'not_null' )) && $field->storage != 'none' )
			|| ( $field->type == 'search_operator' && $field->match_mode != 'none' ) ) {
				$glue	=	'';
				$sql	=	'';
				
				if ( $field->match_options != '' ) {
					$field->match_options	=	new JRegistry( $field->match_options );
				}

				// Glue
				if ( $glues == 1 ) {
					$glue	=	( $where != '' ) ? 'AND' : '';
					$where	.=	$glue;
					if ( $glue != '' ) {
						$where2[++$w]	=	$glue;
					}
					$glue	=	'';
				}
				
				// Sql
				if ( $field->type == 'search_generic' ) {
					if ( count( $field->children ) ) {
						$sql		=	'(';
						$k			=	0;
						foreach ( $field->children as $child ) {
							if ( $k > 0 ) {
								$sql	.=	' OR ';
							}
							$child->match_mode		=	$field->match_mode;
							$child->match_options	=	$field->match_options;
							$child->match_value		=	$field->match_value;
							if ( $child->storage && $child->storage != 'none' ) {
								$Pf		=	$child->storage_field;
								$Pt		=	$child->storage_table;
								// -
								if ( $Pt && !isset( $tables[$Pt] ) ) {
									$tables[$Pt]	=	array( '_'=>'t'.$t, 'fields'=>array(), 'join'=>1 );
									$t++;
								}
								$tables[$Pt]['location']	=	$child->storage_location;
								// -
								$name	=	$child->storage_field2 ? $child->storage_field2 : $child->name;
								if ( $Pt != '' ) {
									$target	=	$tables[$Pt]['_'].'.'.$Pf;
									$tables[$Pt]['fields'][$Pf]	=	( $Pt == '#__cck_core' ) ? $value : $name;
								}
								if ( JCck::callFunc( 'plgCCK_Field'.$child->type, 'isFriendly' ) ) {
									if ( isset( $fields[$child->name] ) ) {
										$value2	=	JCck::callFunc_Array( 'plgCCK_Field'.$child->type, 'getValueFromOptions', array( $fields[$child->name], $value, $config ) );
									} else {
										$value2	=	JCck::callFunc_Array( 'plgCCK_Field'.$child->type, 'getValueFromOptions', array( $child, $value, $config ) );
									}
								} else {
									$value2		=	$value;
								}
								require_once JPATH_PLUGINS.'/cck_storage/'.$child->storage.'/'.$child->storage.'.php';
								$sql	.=	JCck::callFunc_Array( 'plgCCK_Storage'.$child->storage, 'onCCK_StoragePrepareSearch', array( &$child, $child->match_mode, $value2, $name, $name2, $target ) );
							}
							$k++;
						}
						$sql	.=	')';
					}
				} elseif ( $field->type == 'search_ordering' ) {
					$sql	=	'';
					array_pop( $where2 );
					if ( isset( $field->children[$value] ) ) {
						if ( !isset( $fields_order[$field->children[$value]->name] )  ) {
							$fields_order[$field->children[$value]->name]	=	$field->children[$value];
						}
					}
				} elseif ( $field->type == 'search_operator' ) {
					// Glue
					if ( $field->value == '((' ) {
						$glues	=	0;
						$sql	=	'(';
					} elseif ( $field->value == '))' ) {
						$glues	=	1;
						$sql	=	')';
					} else {
						if ( $where2[$w] != '(' ) {
							$glue	=	$field->value;
							$sql	=	$glue;
						}
					}
				} else {
					// -
					if ( $Pt && !isset( $tables[$Pt] ) ) {
						$tables[$Pt]	=	array( '_'=>'t'.$t, 'fields'=>array(), 'join'=>1 );
						$t++;
					}
					$tables[$Pt]['location']	=	$field->storage_location;
					// -
					
					$name	=	$field->storage_field2 ? $field->storage_field2 : $field->name;
					if ( $Pt != '' ) {
						$target	=	$tables[$Pt]['_'].'.'.$Pf;
						$tables[$Pt]['fields'][$Pf]	=	( $Pt == '#__cck_core' ) ? $value : $name;
					}
					
					require_once JPATH_PLUGINS.'/cck_storage/'.$field->storage.'/'.$field->storage.'.php';
					$sql	=	JCck::callFunc_Array( 'plgCCK_Storage'.$field->storage, 'onCCK_StoragePrepareSearch', array( &$field, $field->match_mode, $value, $name, $name2, $target ) );
				}
				$where			.=	$sql;
				$where2[++$w]	=	$sql;
			} else {
				if (  @$glue ) {
					$where2[$w]	=	'';
				}
			}
		}
		$where	=	implode( ' ', $where2 );
		
		// -------- -------- Order
		if ( ! $order ) {
			$order	=	' t1.title ASC';
		}
		
		$inherit	=	array( 'bridge'=>'' );
		JPluginHelper::importPlugin( 'cck_storage_location' );
		if ( $current['stage'] == 0 ) {
			$query	=	$db->getQuery( true );
			$query->select( 't0.id as pid, t0.pk as pk, t0.pkb as pkb' );
			$query->from( '`#__cck_core` AS t0' );
			self::_buildQuery( $dispatcher, $query, $tables, $t, $config, $inherit, $user, $config['doSelect'] );
			$query->select( 't0.cck as cck, t0.storage_location as loc' );
			$query->select( 'tt.id AS type_id, tt.alias AS type_alias' );
			$query->join( 'LEFT', '`#__cck_core_types` AS tt ON tt.name = t0.cck' );
			if ( $where != '' ) {
				$query->where( $where );
			}
			$query->group( 't0.pk' );
			self::_buildQueryOrdering( $order, $ordering, $fields_order, $dispatcher, $query, $tables, $t, $config, $current, $inherit, $user );
			if ( $doLimit ) {
				$db->setQuery( $query, $config['limitstart'], $config['limitend'] );
			} else {
				$db->setQuery( $query, 0, ( ( $limit > 0 ) ? $limit : '' ) );
			}
			$results	=	$db->loadObjectList();
		} else {
			$query	=	$db->getQuery( true );
			$query->select( 't0.pk as pk' );
			$query->from( '`#__cck_core` AS t0' );
			self::_buildQuery( $dispatcher, $query, $tables, $t, $config, $inherit, $user, false );
			if ( $where != '' ) {
				$query->where( $where );
			}
			$query->group( 't0.pk' );
			
			$db->setQuery( $query );
			$results	=	$db->loadColumn();
		}
		
		// Debug
		if ( $options->get( 'debug' ) ) {
			echo $query.'<br /><br />';
		}
		
		unset( $fields );
		unset( $fields_order );
		unset( $tables );
		
		return $results;
	}
	
	// _buildQuery
	protected static function _buildQuery( &$dispatcher, &$query, &$tables, &$t, &$config, &$inherit, $user, $doSelect )
	{
		if ( isset( $tables['#__cck_core']['fields']['storage_location'] ) ) {
			$config['location']	=	$tables['#__cck_core']['fields']['storage_location'];
			$inherit['table']	=	'';
		} elseif ( isset( $tables['#__cck_core']['fields']['cck'] ) ) {
			$core	=	self::_getStorage( $tables['#__cck_core']['fields']['cck'] );
			if ( is_object( $core ) ) {
				$config['location']	=	$core->storage_location;
				$inherit['table']	=	$core->storage_table;
			}
		}
		if ( isset( $config['location'] ) && $config['location'] ) {
			$dispatcher->trigger( 'onCCK_Storage_LocationPrepareSearch', array( $config['location'], &$query, &$tables, &$t, &$config, &$inherit, $user ) );
		}
		
		self::_buildQuery2( $dispatcher, $query, $tables, $t, $config, $inherit, $user, $doSelect, 1 );
		self::_buildQuery2( $dispatcher, $query, $tables, $t, $config, $inherit, $user, false, 2 );
		if ( !( isset( $tables['#__cck_core']['fields']['cck'] ) || isset( $tables['#__cck_core']['fields']['storage_location'] ) ) && $config['location'] ) {
			$query->where( 't0.storage_location = "'.$config['location'].'"' );
		}
	}
	
	// _buildQuery2
	protected static function _buildQuery2( &$dispatcher, &$query, &$tables, &$t, &$config, &$inherit, $user, $doSelect, $join )
	{
		foreach ( $tables as $tk=>$tv ) {
			$j	=	( isset( $tv['join'] ) ) ? $tv['join'] : 1;
			if ( $tv['_'] != 't0' && $j == $join ) {
				if ( ! $config['location'] && $tv['_'] == 't1' ) {
					$config['location']	=	$tv['location'];
					$inherit['table']	=	$tk;
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareSearch', array( $config['location'], &$query, &$tables, &$t, &$config, &$inherit, $user ) );
				}
				if ( $doSelect === true ) {
					$query->select( ' '.$tv['_'].'.*' );
				}
				$key		=	( isset( $tables[$tk]['key'] ) ) ? $tables[$tk]['key'] : 'id';
				$join_table	=	( isset( $tables[$tk]['join_table'] ) && $tables[$tables[$tk]['join_table']]['_'] ) ? $tables[$tables[$tk]['join_table']]['_'] : 't0';
				$join_key	=	( isset( $tables[$tk]['join_key'] ) ) ? $tables[$tk]['join_key'] : ( ( $tk == $inherit['bridge'] ) ? 'pkb' : 'pk' );
				$query->join( 'LEFT', '`'.$tk.'` AS '.$tv['_'].' ON '.$tv['_'].'.'.$key.' = '.$join_table.'.'.$join_key );
			}
		}
	}
	
	// _buildQueryOrdering
	protected static function _buildQueryOrdering( &$order, &$ordering, $fields_order, &$dispatcher, &$query, &$tables, &$t, &$config, $current, &$inherit, $user )
	{
		if ( $ordering != '' ) {
			if ( $ordering == '-1' ) {
				// Todo: alias. + join table if doesn't exist..
				if ( $current['order_by'] ) {
					$query->order( $current['order_by'] );
				}
			} else {
				if ( @$config['location'] ) {
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareOrder', array( $config['location'], &$ordering, &$tables, &$config ) );
					if ( $ordering ) {
						$query->order( $ordering );
					}
				}
			}
		} else {
			if ( count( $fields_order ) ) {
				foreach ( $fields_order as $field ) {
					$order		=	'';
					$dir		=	$field->match_mode;
					
					if ( $dir ) {
						$s_field	=	$field->storage_field;
						$s_table	=	$field->storage_table;
						
						if ( ! isset( $tables[$s_table] ) && $s_table ) {
							$tables[$s_table]['_']		=	't'.$t;
							$tables[$s_table]['fields']	=	array();
							$key						=	'id';
							$join_key					=	'pk';
							$query->join( 'LEFT', '`'.$s_table.'` AS '.$tables[$s_table]['_'].' ON '.$tables[$s_table]['_'].'.'.$key.' = t0.'.$join_key );
							$t++;
						}
						
						$order		=	$tables[$s_table]['_'] .'.'. $s_field .' '. $dir;
						$query->order( $order );
					}
				}
			} else {
				$ordering	=	'alpha';
				if ( @$config['location'] ) {
					$dispatcher->trigger( 'onCCK_Storage_LocationPrepareOrder', array( $config['location'], &$ordering, &$tables, &$config ) );
					if ( $ordering ) {
						$query->order( $ordering );
					}
				}
			}
		}
	}

	// _getStorage
	protected static function _getStorage( $cck )
	{
		static $cache	=	array();
		$cck			=	str_replace( array( ',', ' ' ), array( '","', '","' ), $cck );
		$x				=	str_replace( '","', '___', $cck );
		
		if ( !isset( $cache[$x] ) ) {
			$cache[$x]	=	JCckDatabase::loadObject( 'SELECT storage_location, storage_table FROM #__cck_core WHERE cck IN ("'.$cck.'") ORDER BY id DESC LIMIT 1' );
		}

		return $cache[$x];
	}
}