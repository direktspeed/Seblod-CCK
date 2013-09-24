<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.controlleradmin' );

// Controller
class CCKControllerList extends JControllerAdmin
{
	protected $text_prefix	=	'COM_CCK';
	
	// __construct
	public function __construct( $config = array() )
	{
		parent::__construct( $config );
	}
	
	// getModel
	public function getModel( $name = 'List', $prefix = CCK_MODEL, $config = array( 'ignore_request' => true ) )
	{
		$model	=	parent::getModel( $name, $prefix, $config );
		
		return $model;
	}
	
	// delete
	public function delete()
	{
		// JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );
		
		$app	=	JFactory::getApplication();
		$model	=	$this->getModel();
		$cid	=	$app->input->get( 'cid', array(), 'array' );
		
		jimport('joomla.utilities.arrayhelper');
		JArrayHelper::toInteger( $cid );
		
		if ( $nb = $model->delete( $cid ) ) {
			$msg		=	JText::_( 'COM_CCK_SUCCESSFULLY_DELETED' ); // todo: JText::plural( 'COM_CCK_N_SUCCESSFULLY_DELETED', $nb );
			$msgType	=	'message';
		} else {
			$msg		=	JText::_( 'JERROR_AN_ERROR_HAS_OCCURRED' );
			$msgType	=	'error';
		}
		
		$this->setRedirect( $this->_getReturnPage(), $msg, $msgType );
	}
	
	// search	
	public function search()
	{
		parent::display( true );
	}
	
	// _getReturnPage
	protected function _getReturnPage( $base = false )
	{
		$app	=	JFactory::getApplication();
		$return	=	$app->input->getBase64( 'return' );
		
		if ( empty( $return ) || !JUri::isInternal( base64_decode( $return ) ) ) {
			return ( $base == true ) ? JURI::base() : 'index.php?option=com_cck';
		} else {
			return base64_decode( $return );
		}
	}
}
?>