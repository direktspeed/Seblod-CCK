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

// JCckEcommercePromotion
abstract class JCckEcommercePromotion
{
	// apply
	public static function apply( $type, &$total )
	{
		
		$user		=	JCck::getUser();
		$my_groups	=	$user->getAuthorisedGroups();
		
		$currency	=	JCckEcommerce::getCurrency();
		$discount	=	'';
		$promotions	=	JCckEcommerce::getPromotions( $type );
		
		if ( count( $promotions ) ) {
			foreach ( $promotions as $p ) {
				$groups		=	explode( ',', $p->groups );
				if ( count( array_intersect( $my_groups, $groups ) ) > 0 ) {
					switch ( $p->discount ) {
						case 'free':
							$discount	=	'FREE';
							$total		=	0;
							break;
						case 'minus':
							$discount	=	'- '.$currency->lft.$p->discount_amount.$currency->right;
							$total		-=	$p->discount_amount;
							break;
						case 'percentage':
							$discount	=	'- '.$p->discount_amount.' %';
							$total		=	$total - ( $total * $p->discount_amount / 100 );
							break;
						default:
							break;
					}
					
				}
			}
		}
		
		return $discount;
	}
}
?>