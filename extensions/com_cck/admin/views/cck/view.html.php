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
class CCKViewCck extends JCckBaseLegacyView
{
	// prepareToolbar
	protected function prepareToolbar()
	{
		$bar	=	JToolBar::getInstance( 'toolbar' );
		$canDo	=	Helper_Admin::getActions();
		$url	=	'index.php?option='.CCK_COM.'&task=box.add&tmpl=component&file=administrator/components/com_cck/views/cck/tmpl/preferences.php';
		
		if ( JCck::on() ) {
			JToolBarHelper::title( CCK_LABEL );
		} else {
			JToolBarHelper::title( '&nbsp;', 'seblod.png' );
		}
		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( CCK_COM, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		require_once JPATH_COMPONENT.'/helpers/toolbar/modalbox.php';
		$bar->appendButton( 'CckModalBox', 'options', JText::_( 'COM_CCK_PREFERENCES' ), $url );
		
		Helper_Admin::addToolbarSupportButton();
	}
}
?>