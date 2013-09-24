<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: ecommerce.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerce
abstract class JCckEcommerce
{
	public static $_me			=	'cck_ecommerce';
	public static $_config		=	NULL;
	
	public static $currency		=	NULL;
	public static $promotions	=	NULL;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config
	
	// _setConfig
	public static function _setConfig()
	{		
		if ( self::$_config ) {
			return self::$_config;
		}

		$config			=	new stdClass;
		$config->params =	JComponentHelper::getParams( 'com_'.self::$_me );
		
		self::$_config	=&	$config;
	}
	
	// getConfig
	public static function getConfig()
	{		
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config;
	}
	
	// getConfig_Param
	public static function getConfig_Param( $name, $default = '' )
	{
		if ( ! self::$_config ) {
			self::_setConfig();
		}
		
		return self::$_config->params->get( $name, $default );
	}
	
	// getUIX
	public static function getUIX()
	{
		return ( self::getConfig_Param( 'uix', '' ) == 'nano' ) ? 'compact' : 'full';
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Cart Definition
	
	// getCartDefinition
	public static function getCartDefinition( $name )
	{
		static $definitions	=	array();
		
		if ( !isset( $definitions[$name] ) ) {
			$definitions[$name]	=	JCckDatabase::loadObject( 'SELECT title, name, storage_location, storage_table, storage_field, multicart, multistore, quantity, request_code, request_payment, request_payment_table, request_payment_field'
															. ' FROM #__cck_more_ecommerce_cart_definitions WHERE name = "'.$name.'"' );
			if ( strpos( $definitions[$name]->request_payment_field, '$' ) !== false ) {
				$definitions[$name]->request_payment_field	=	str_replace( '$', strtolower( JCckEcommerce::getCurrency()->code ), $definitions[$name]->request_payment_field );
			}
		}
		
		return $definitions[$name];
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Currency
	
	// getCurrency
	public static function getCurrency( $id = 0 )
	{
		static $currency	=	NULL;
		
		if ( (int)$id > 0 ) {
			return JCckDatabase::loadObject( 'SELECT a.id, a.title, a.code, a.conversion_rate, a.lft, a.rgt'
										   . ' FROM #__cck_more_ecommerce_currencies AS a WHERE a.id = "'.$id.'"' );
		}
		if ( !is_object( $currency ) ) {
			$app		=	JFactory::getApplication();
			$user		=	JCck::getUser();	//JCckUser::getUser();
			
			$code		=	'';
			if ( self::getConfig_Param( 'currency_dynamic', 0 ) ) {
				$code	=	$app->input->cookie->get( 'cck_ecommerce_currency', '' );
			}
			if ( !$code ) {
				$property	=	JCckEcommerce::getConfig_Param( 'currency_user' );
				if ( isset( $user->$property ) ) {
					$code	=	$user->$property;
				}
				if ( !$code ) {
					$code	=	JCckEcommerce::getConfig_Param( 'currency', 0 );
					if ( !$code ) {
						$lang	=	JFactory::getLanguage();
						if ( !$lang->hasKey( 'COM_CCK_CURRENCY_AUTO' ) == 1 ) {
							$lang->load( 'com_cck_default', JPATH_SITE );
						}
						$code	=	JText::_( 'COM_CCK_CURRENCY_AUTO' );
					}
				}
			}
			if ( !$code ) {
				$code	=	'USD';
			}
			$currency	=	JCckDatabase::loadObject( 'SELECT a.id, a.title, a.code, a.conversion_rate, a.lft, a.rgt'
													. ' FROM #__cck_more_ecommerce_currencies AS a WHERE a.code = "'.$code.'"' );
		}
		
		return $currency;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Payments

	// getGateway
	public static function getGateway( $type )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$type] ) ) {
			$cache[$type]			=	JCckDatabase::loadObject( 'SELECT id, title, options'
																. ' FROM #__cck_more_ecommerce_gateways WHERE type = "'.$type.'"' );
			$cache[$type]->options	=	new JRegistry( $cache[$type]->options );
		}
		
		return $cache[$type];
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Products

	// getTotal
	public static function getTotal( $items, $target = 'price' )
	{
		$total	=	0;

		if ( count( $items ) ) {
			foreach ( $items as $item ) {
				$qty	=	$item->quantity;
				$total	+=	$item->price * $qty;
			}
		}
		
		return $total;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Promotions
	
	// getPromotions
	public static function getPromotions( $type = '' )
	{
		if ( !self::$promotions ) {
			self::$promotions	=	self::_setPromotions();
		}
		
		return ( $type ) ? ( isset( self::$promotions[$type] ) ? self::$promotions[$type] : array() ) : self::$promotions;
	}
	
	// _setPromotions
	protected static function _setPromotions()
	{
		$db		=	JFactory::getDbo();
		$null	=	$db->getNullDate();
		$now	=	JFactory::getDate()->toSql();

		$promotions	=	JCckDatabase::loadObjectListArray( 'SELECT a.title, a.type, a.discount, a.discount_amount, a.groups'
														.  ' FROM #__cck_more_ecommerce_promotions AS a'
														.  ' WHERE a.published = 1'
														.  ' AND (a.publish_up = '.JCckDatabase::quote( $null ).' OR '.'a.publish_up <= '.JCckDatabase::quote( $now ).')'
														.  ' AND (a.publish_down = '.JCckDatabase::quote( $null ).' OR '.'a.publish_up >= '.JCckDatabase::quote( $now ).')'
														.  ' ORDER BY a.title', 'type' );
		
		return $promotions;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stores

	// getStore
	public static function getStore( $id )
	{
		static $cache	=	array();
		
		if ( !isset( $cache[$id] ) ) {
			$cache[$id]	=	JCckDatabase::loadObject( 'SELECT id, title, home, parent_id, parent_fee, parent_amount, options'
															. ' FROM #__cck_more_ecommerce_stores WHERE id = '.$id );
			$cache[$id]->options	=	new JRegistry( $cache[$id]->options );
		}
		
		return $cache[$id];
	}
}
?>