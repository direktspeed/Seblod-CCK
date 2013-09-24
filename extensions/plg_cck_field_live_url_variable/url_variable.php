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

// Plugin
class plgCCK_Field_LiveUrl_Variable extends JCckPluginLive
{
	protected static $type	=	'url_variable';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$app		=	JFactory::getApplication();
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		
		// Prepare
		$ignore_null	=	$options->get( 'ignore_null', 0 );
		$multiple		=	$options->get( 'multiple', 0 );
		$return			=	$options->get( 'return', 'first' );
		if ( $multiple ) {
			$variables	=	$options->get( 'variables', '' );
			$variables	=	explode( '||', $variables );
			if ( count( $variables ) ) {
				foreach ( $variables as $variable ) {
					$request	=	'get'.ucfirst( $options->get( 'type', 'string' ) );
					$variable	=	preg_replace( '/\s+/', '', $variable );
					$result		=	(string)$app->input->$request( $variable, '' );
					
					if ( $ignore_null ) {
						$live	=	( $result ) ? $result : $live;
						if ( $return == 'first' && $live ) {
							break;
						}
					} else {
						$live	=	( $result != '' ) ? $result : $live;
						if ( $return == 'first' && $live != ''  ) {
							break;
						}
					}
				}
			}
			
		} else {
			$variable		=	$options->get( 'variable', $field->name );
			if ( $variable ) {
				$request	=	'get'.ucfirst( $options->get( 'type', 'string' ) );
				$live		=	(string)$app->input->$request( $variable, '' );
			}
		}
		
		// Set
		$value	=	( $ignore_null && !$live ) ? '' : (string)$live;
	}
}
?>