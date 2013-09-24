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
class plgCCK_Field_LiveJoomla_User extends JCckPluginLive
{
	protected static $type	=	'joomla_user';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LivePrepareForm
	public function onCCK_Field_LivePrepareForm( &$field, &$value = '', &$config = array() )
	{
		if ( self::$type != $field->live ) {
			return;
		}
		
		// Init
		$live		=	'';
		$options	=	parent::g_getLive( $field->live_options );
		
		// Prepare
		$property	=	$options->get( 'property' );
		if ( $property ) {
			$user	=	JCck::getUser(); // todo: JCckUser::getUser();
			if ( $user->id > 0 && $user->guest == 1 ) {
				if ( !( $property == 'ip' || $property == 'session_id' ) ) {
					$user	=	new JUser( 0 );
				}
			}
			if ( isset( $user->$property ) ) {
				$live	=	$user->$property;
				if ( is_array( $live ) ) {
					$live	=	implode( ',', $live );
				}
			}
		}
		
		// Set
		$value	=	(string)$live;
	}
}
?>