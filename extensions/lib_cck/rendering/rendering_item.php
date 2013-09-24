<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: rendering_item.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Rendering Item
class CCK_Item
{
	private $me;
	
	var $fields;
	
	var $methodRender;
	
	// init
	public function init()
	{
		$this->me			=	( isset( $this->fields_list ) && $this->fields_list ) ? $this->fields_list : ( ( isset( $this->fields ) ) ? $this->fields : new stdClass );
		
		$this->methodRender	=	'onCCK_FieldRenderContent';
	}
	
	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
        $property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
			$fieldname	=	$args[0];
			$count		=	count( $args );
			if ( $count ==  1 ) {
				if ( empty( $property ) ) {
					if ( isset ( $this->me[$fieldname] ) ) {
						return $this->me[$fieldname];
					}
				} else {
					if ( isset ( $this->me[$fieldname]->$property ) ) {
						return $this->me[$fieldname]->$property;
					}
				}
			} else {
				if ( $count == 2 ) {					
					return empty( $property ) ? @$this->me[$fieldname]->value[$args[1]] : @$this->me[$fieldname]->value[$args[1]]->$property;
				} else {
					return empty( $property ) ? @$this->me[$fieldname]->value[$args[1]]->value[$args[2]] : @$this->me[$fieldname]->value[$args[1]][$args[2]]->$property;
				}
			}
        }
    }
	
	// __get
    public function __get( $property ) {
		if ( isset( $this->$property ) ) {
			return $this->$property;
		}
    }
	
	// getLabel
	public function getLabel( $fieldname = '', $html = false, $morelabel = '' )
	{
		if ( ! isset ( $this->me[$fieldname] ) ) {
			return;
		}
		
		$label	=	trim( $this->me[$fieldname]->label );
		if ( !( $html === true && $label ) ) {
			return trim( $label );
		}
		
		if ( $morelabel ) {
			$label	.=	'<span class="star"> '.$morelabel.'</span>';
		}
		if ( $label ) {
			$label	=	'<label for="'.$this->me[$fieldname]->name.'">'.$label.'</label>';
		}
		
		return $label;
	}
	
	// getField (deprecated)
	public function getField( $fieldname )
	{
		return $this->renderField( $fieldname );
	}
	
	// getFields todo
	
	// renderField
	public function renderField( $fieldname )
	{
		$field	=	$this->get( $fieldname );
		if ( !$field ) {
			return;
		}
		
		$html	=	'';
		if ( $field->display > 1 ) {
			$html	=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, $this->methodRender, array( $field ) );
		}
		
		return $html;
	}
}
?>