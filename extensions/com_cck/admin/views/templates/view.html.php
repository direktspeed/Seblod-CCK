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
class CCKViewTemplates extends JCckBaseLegacyViewList
{
	protected $vName	=	'template';
	protected $vTitle	=	_C1_TEXT;
	
	// prepareToolbar
	public function prepareToolbar()
	{
		Helper_Admin::addToolbar( $this->vName, $this->vTitle, $this->state->get( 'filter.folder' ) );

		if ( JCck::on() ) {
			JHtmlSidebar::setAction( 'index.php?option=com_cck&view=templates' );
		}
	}
	
	// getSortFields
	protected function getSortFields()
	{
		return array( 'title'=>JText::_( 'COM_CCK_TITLE' ),
					  'a.folder'=>JText::_( 'COM_CCK_APP_FOLDER' ),
					  'a.published'=>JText::_( 'COM_CCK_STATE' ),
					  'a.id'=>JText::_( 'JGRID_HEADING_ID' )
		);
	}
}
?>