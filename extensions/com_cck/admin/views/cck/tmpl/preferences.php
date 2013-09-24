<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: preferences.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JHtml::_('behavior.tooltip');

$doc	=	JFactory::getDocument();
$js		=	'
			(function ($){
				JCck.Dev = {
					submit: function() {
						var task = "preferences";
						$("#task").val(task);
						$.ajax({
							cache: false,
							data: $("#adminForm").serialize(),
							type: "POST",
							url: "index.php?option=com_cck&task="+task+"&format=raw",
							success: function(response) {
								JCck.Dev.close();
								if (response == 1) {
									parent.location.reload();
								}
							}
						});
						return;
					}
    			}
				$(document).ready(function(){
					$("#resetBox").hide();
				});
			})(jQuery);
			';
$doc->addScriptDeclaration( $js );
$user		=	JFactory::getUser();
$options	=	JCckDatabase::loadResult( 'SELECT a.options FROM #__cck_core_preferences AS a WHERE a.userid = '.(int)$user->get( 'id' ) );
$xml		=	JPath::clean( JPATH_ADMINISTRATOR.'/components/com_cck/models/forms/preferences.xml' );

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_workshop.php';
Helper_Workshop::getTemplateParams( $xml, '//form', 'preferences', $options );
?>
