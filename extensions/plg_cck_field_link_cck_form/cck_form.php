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
class plgCCK_Field_LinkCCK_Form extends JCckPluginLink
{
	protected static $type	=	'cck_form';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_Field_LinkPrepareContent
	public static function onCCK_Field_LinkPrepareContent( &$field, &$config = array() )
	{		
		if ( self::$type != $field->link ) {
			return;
		}
		
		// Prepare
		$link	=	parent::g_getLink( $field->link_options );
		
		// Set
		$field->link	=	'';
		self::_link( $link, $field, $config );
	}
	
	// _link
	protected static function _link( $link, &$field, &$config )
	{
		$app			=	JFactory::getApplication();
		$form			=	$link->get( 'form', '' );
		$edit			=	$link->get( 'form_edition', 1 );
		$edit			=	( !$form && $edit ) ? '&id='.$config['pk'] : '';
		$form			=	( $form ) ? $form : $config['type'];
		$itemId			=	$link->get( 'itemid', $app->input->getInt( 'Itemid', 0 ) );
		$uri			=	JFactory::getURI();
		$return			=	base64_encode( $uri );
		$custom			=	$link->get( 'custom', '' );
		$redirection	=	$link->get( 'redirection', '' );
		
		if ( !( $form ) ) {
			return;
		}
		
		// Check
		if ( $edit != '' ) {
			$stage			=	$link->get( 'form_edition_stage', '' );
			if ( $stage != '' ) {
				$edit		.=	'&stage='.(int)$stage;
			}
			$user 			=	JCck::getUser();
			$canEdit		=	$user->authorise( 'core.edit', 'com_cck.form.'.$config['type_id'] );
			$canEditOwn		=	$user->authorise( 'core.edit.own', 'com_cck.form.'.$config['type_id'] );
			if ( ( !$canEdit && !$canEditOwn ) ||
				 ( !$canEdit && $canEditOwn && $config['author'] != $user->get( 'id' ) ) ||
				 ( $canEdit && !$canEditOwn && $config['author'] == $user->get( 'id' ) ) ) {
				if ( !$link->get( 'no_access', 1 ) ) {
					$field->display	=	0;
				}
				return;
			}
		}
		
		// Prepare
		$link_class		=	$link->get( 'class', '' );
		$link_rel		=	$link->get( 'rel', '' );
		$link_target	=	$link->get( 'target', '' );
		$link_title		=	$link->get( 'title', '' );
		$link_title2	=	$link->get( 'title_custom', '' );
		$tmpl			=	$link->get( 'tmpl', '' );
		$tmpl			=	$tmpl ? '&tmpl='.$tmpl : '';
		$vars			=	$tmpl;	// + live
		
		// Set
		if ( is_array( $field->value ) ) {
			foreach ( $field->value as $f ) {
				$c				=	$custom;
				$c				=	parent::g_getCustomSelfVars( self::$type, $f, $c, $config );
				$c				=	$c ? '&'.$c : '';
				$f->link		=	'index.php?option=com_cck&view=form&layout=edit&type='.$form.$edit.$vars.'&Itemid='.$itemId.$c;
				if ( $redirection != '-1' ) {
					$f->link	.=	'&return='.$return;
				}
				$f->link		=	JRoute::_( $f->link );
				$f->link_class	=	$link_class ? $link_class : ( isset( $f->link_class ) ? $f->link_class : '' );
				$f->link_rel	=	$link_rel ? $link_rel : ( isset( $f->link_rel ) ? $f->link_rel : '' );
				$f->link_target	=	$link_target ? $link_target : ( isset( $f->link_target ) ? $f->link_target : '' );
				$f->link_title	=	$link_title ? ( $link_title == '2' ? $link_title2 : ( isset( $f->link_title ) ? $f->link_title : '' ) ) : '';
			}
			$field->link		=	'#';	//todo
		} else {
			$custom				=	parent::g_getCustomVars( self::$type, $field, $custom, $config );
			$field->link		=	JRoute::_( 'index.php?option=com_cck&view=form&layout=edit&type='.$form.$edit.$vars.'&Itemid='.$itemId );
			$separator			=	( strpos( $field->link, '?' ) !== false ) ? '&' : '?';
			if ( $custom ) {
				$field->link	.=	$separator.$custom;
				$separator		=	'&';
			}
			if ( $redirection != '-1' ) {
				$field->link	.=	$separator.'return='.$return;
			}
			$field->link_class	=	$link_class ? $link_class : ( isset( $field->link_class ) ? $field->link_class : '' );
			$field->link_rel	=	$link_rel ? $link_rel : ( isset( $field->link_rel ) ? $field->link_rel : '' );
			$field->link_target	=	$link_target ? $link_target : ( isset( $field->link_target ) ? $field->link_target : '' );
			$field->link_title	=	$link_title ? ( $link_title == '2' ? $link_title2 : ( isset( $field->link_title ) ? $field->link_title : '' ) ) : '';
		}
	}
}
?>