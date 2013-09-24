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

JCckDev::initScript( 'link', $this->item );
require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/helper_admin.php'; //todo: >> core_storage_location
?>

<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_LINK_'.$this->item->name.'_DESC' ) ); ?>
    <ul class="adminformlist adminformlist-2cols">
        <?php
		echo JCckDev::renderForm( 'core_sef', '', $config, array( 'selectlabel'=>'Inherited', 'storage_field'=>'sef' ) );
		echo JCckDev::renderForm( 'core_menuitem', '', $config, array( 'selectlabel'=>'Inherited' ) );
		echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Use Value=optgroup||Field=2', 'storage_field'=>'content' ) );
		// echo JCckDev::renderForm( 'core_dev_select', '', $config, array( 'defaultvalue'=>'', 'label'=>'Content', 'selectlabel'=>'Current', 'options'=>'Next=4||Previous=5||Use Value=optgroup||Field=2', 'storage_field'=>'content' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Field Name', 'storage_field'=>'content_fieldname' ) );
		echo JCckDev::renderBlank( '<input type="hidden" id="blank_li" value="" />' );
		echo JCckDev::renderForm( 'core_storage_location', '', $config, array( 'label'=>'Content Object', 'storage_field'=>'content_location' ) );

		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_CONSTRUCTION' ) . '<span class="mini">('.JText::_( 'COM_CCK_GENERIC' ).')</span>' );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Class', 'size'=>24, 'storage_field'=>'class' ) );
		echo JCckDev::renderForm( 'core_dev_text', '', $config, array( 'label'=>'Rel', 'size'=>32, 'storage_field'=>'rel' ) );
		echo JCckDev::renderForm( 'core_options_target', '', $config, array( 'defaultvalue'=>'', 'selectlabel'=>'Inherited', 'storage_field'=>'target' ) );
		echo JCckDev::renderForm( 'core_tmpl', '', $config );
		echo JCckDev::renderForm( 'core_dev_textarea', '', $config, array( 'label'=>'Custom variables', 'cols'=>92, 'rows'=>1, 'storage_field'=>'custom' ), array(), 'w100' );
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#itemid').isDisabledWhen('sef','0');
	$('#content_fieldname,#content_location,#blank_li').isVisibleWhen('content','2');
});
</script>