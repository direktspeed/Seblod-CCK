<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: promotion.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckEcommerceCart
abstract class JCckEcommerceCart
{
	// apply
	public static function countItems( $definition )
	{
		$cache	=	array();
		$count	=	0;
		$user	=	JFactory::getUser();
		
		if ( !isset( $cache[$definition] ) ) {
			$cache[$definition]	=	JCckDatabase::loadResult( 'SELECT COUNT(a.id) FROM #__cck_more_ecommerce_cart_product AS a'
															. ' LEFT JOIN #__cck_more_ecommerce_carts AS b ON b.id = a.cart_id WHERE b.type = "'.$definition.'" AND b.user_id = '.$user->id.' AND b.state = 1' );
		}

		return $cache[$definition];
	}
}
?>