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
class plgCCK_FieldButton_Submit extends JCckPluginField
{
	protected static $type		=	'button_submit';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path		=	parent::g_getPath( self::$type.'/' );
		$field->label2	=	trim( @$field->label2 );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value			=	$field->label;
		$field->label	=	'';
		
		// Prepare
		$class	=	'button btn' . ( $field->css ? ' '.$field->css : '' );
		$click	=	isset( $config['submit'] ) ? ' onclick="'.$config['submit'].'(\'form.save\');return false;"' : '';
		$attr	=	'class="'.$class.'"'.$click . ( $field->attributes ? ' '.$field->attributes : '' );
		if ( $field->bool ) {
			$label	=	$value;
			if ( JCck::on() ) {
				if ( $field->bool6 == 3 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>';
					$attr		.=	' title="'.$value.'"';
				} elseif ( $field->bool6 == 2 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	$value."\n".'<span class="icon-'.$options2['icon'].'"></span>';
				} elseif ( $field->bool6 == 1 ) {
					$options2	=	JCckDev::fromJSON( $field->options2 );
					$label		=	'<span class="icon-'.$options2['icon'].'"></span>'."\n".$value;
				}
			}
			$type	=	( $field->bool7 == 1 ) ? 'submit' : 'button';
			$form	=	'<button type="'.$type.'" id="'.$id.'" name="'.$name.'" '.$attr.'>'.$label.'</button>';
			$tag	=	'button';
		} else {
			$form	=	'<input type="submit" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
			$tag	=	'input';
		}
		if ( $field->bool2 == 1 ) {
			$alt	=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( $config['client'] == 'search' ) {
				$onclick	=	'onclick="jQuery(\'#'.$config['formId'].'\').clearForm();"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_RESET' ).'">'.JText::_( 'COM_CCK_RESET' ).'</a>';				
			} else {
				$onclick	=	'onclick="Joomla.submitform(\'cancel\', document.getElementById(\'seblod_form\'));"';
				$form		.=	$alt.'<a href="javascript: void(0);" '.$onclick.' title="'.JText::_( 'COM_CCK_CANCEL' ).'">'.JText::_( 'COM_CCK_CANCEL' ).'</a>';
			}
		} elseif ( $field->bool2 == 2 ) {
			$alt		=	$field->bool3 ? ' '.JText::_( 'COM_CCK_OR' ).' ' : "\n";
			if ( !isset( $options2 ) ) {
				$options2	=	JCckDev::fromJSON( $field->options2 );
			}
			$field2		=	(object)array( 'link'=>$options2['alt_link'], 'link_options'=>$options2['alt_link_options'], 'id'=>$id, 'name'=>$name, 'text'=>htmlspecialchars( $options2['alt_link_text'] ), 'value'=>'' );
			JCckPluginLink::g_setLink( $field2, $config );
			JCckPluginLink::g_setHtml( $field2, 'text' );
			$form		.=	$alt.$field2->html;
		}
		
		// Set
		if ( ! $field->variation ) {  
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<'.$tag, ' ', '', $config );
		}
		$field->value	=	'';
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
}
?>