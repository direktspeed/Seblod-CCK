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

require_once JPATH_SITE.'/plugins/cck_storage_location/joomla_category/joomla_category.php';

// Class
class plgCCK_Storage_LocationJoomla_Category_Exporter extends plgCCK_Storage_LocationJoomla_Category
{
	// onCCK_Storage_LocationExport
	public static function onCCK_Storage_LocationExport( $items, &$config = array() )
	{
		// Init
		$excluded	=	array( 'id', 'asset_id', 'level', 'path', 'lft', 'rgt', 'tags', 'tagsHelper' );
		$excluded2	=	array( 'cck'=>'' );
		$tables		=	array();
		
		// Prepare
		$table	=	self::_getTable();
		$fields	=	$table->getProperties();
		if ( count( $excluded ) ) {
			foreach ( $excluded as $exclude ) {
				unset( $fields[$exclude] );
			}
		}
		if ( count( $config['fields2'] ) ) {
			foreach ( $config['fields2'] as $k=>$field ) {
				if ( !isset( $storages[$field->storage_table] ) ) {
					$tables[$field->storage_table]	=	JCckDatabase::loadObjectList( 'SELECT * FROM '.$field->storage_table, 'id' );
				}
				$fields[$field->name]	=	'';
			}
		}
		$fields	=	array_keys( $fields );
		if ( $config['ftp'] == '1' ) {
			$config['buffer']	.=	str_putcsv( $fields, $config['separator'] )."\n";
		} else {
			fputcsv( $config['handle'], $fields, $config['separator'] );
		}
		
		// Set
		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				// Core
				$table	=	self::_getTable( $item->pk );
				$fields	=	$table->getProperties();
				if ( count( $excluded ) ) {
					foreach ( $excluded as $exclude ) {
						unset( $fields[$exclude] );
					}
				}
				// Core > Custom
				if ( self::$custom && isset( $fields[self::$custom] ) ) {
					preg_match_all( CCK_Content::getRegex(), $fields[self::$custom], $values );
					$tables[self::$table][$item->pk]->{self::$custom}	=	array();
					$fields[self::$custom]								=	'';
					if ( count( $values[1] ) ) {
						foreach ( $values[1] as $k=>$v ) {
							if ( $v == self::$custom ) {
								$fields[self::$custom]	=	$values[2][$k];
							} elseif ( !isset( $excluded2[$v] ) ) {
								$tables[self::$table][$item->pk]->{self::$custom}[$v]	=	$values[2][$k];
							}	
						}
					}
				}
				// More
				if ( count( $config['fields2'] ) ) {
					foreach ( $config['fields2'] as $name=>$field ) {
						if ( $field->storage == 'standard' ) {
							$fields[$field->name]	=	@$tables[$field->storage_table][$item->pk]->{$field->storage_field};
						} else {
							$name	=	$field->storage_field2 ? $field->storage_field2 : $name;
							if ( !isset( $tables[$field->storage_table][$item->pk]->{$field->storage_field} ) ) {
								$tables[$field->storage_table][$item->pk]->{$field->storage_field}	=	array();	// TODO
							}
							$fields[$field->name]	=	@$tables[$field->storage_table][$item->pk]->{$field->storage_field}[$name];
						}
					}
				}
				if ( $config['ftp'] == '1' ) {
					$config['buffer']	.=	str_putcsv( $fields, $config['separator'] )."\n";
				} else {
					fputcsv( $config['handle'], $fields, $config['separator'] );
				}
			}
		}
	}
}
?>