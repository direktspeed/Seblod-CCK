<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewVersions extends JCckBaseLegacyViewList
{
	protected $vName	=	'version';

	// prepareToolbar
	public function prepareToolbar()
	{
		$canDo			=	Helper_Admin::getActions();
		$this->e_type	=	$this->state->get( 'filter.e_type' );
		$type			=	( $this->e_type == 'search' ) ? _C4_TEXT : _C2_TEXT;
		
		JToolBarHelper::title( JText::_( _C6_TEXT.'_MANAGER' ).' - '.JText::_( 'COM_CCK_'.$type.'s' ), $this->vName.'s.png' );
		if ( $canDo->get( 'core.delete' ) ) {
			JToolBarHelper::custom( $this->vName.'s'.'.delete', 'delete', 'delete', 'JTOOLBAR_DELETE', true );
		}
	}
}
?>