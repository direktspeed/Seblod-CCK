<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_app.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on() ) { ?>
	<div class="<?php echo $this->css['batch']; ?>" id="collapseModal">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">x</button>
			<h3><?php echo JText::_( 'COM_CCK_APP_PROCESS'); ?></h3>
		</div>
		<div class="modal-body">
			<p><?php echo JText::_( 'COM_CCK_APP_PROCESS_DESC' ); ?></p>
			<div class="control-group">
				<div class="control-label">
					<label for="batch_folder"><?php echo JText::_( 'COM_CCK_SELECT_ELEMENTS' ); ?></label>
				</div>
				<div class="controls">
					<?php echo JCckDev::getForm( 'core_app_elements', '', $config, array( 'bool'=>1, 'storage_field'=>'app_elements' ) ); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="batch_folder"><?php echo JText::_( 'COM_CCK_ADD_DEPENDENCIES_CATEGORIES' ); ?></label>
				</div>
				<div class="controls">
					<?php echo JCckDev::getForm( 'core_app_dependencies', '', $config, array() ); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<label for="batch_folder"><?php echo JText::_( 'COM_CCK_ADD_DEPENDENCIES_MENU' ); ?></label>
				</div>
				<div class="controls">
					<?php echo JCckDev::getForm( 'core_app_dependencies_menu', '', $config, array( 'css'=>'no-chosen' ) ); ?>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button class="btn btn-primary" type="button" onclick="" data-dismiss="modal"><?php echo JText::_( 'COM_CCK_CLOSE' ); ?></button>
		</div>
	</div>
<?php } else { ?>
	<div class="<?php echo $this->css['batch'].$this->css['joomla3']; ?>">
	    <div class="legend top left"><?php echo JText::_( 'COM_CCK_APP_PROCESS' ); ?></div>
		<div style="font-size: 1.1em; margin-bottom:8px;"><?php echo JText::_( 'COM_CCK_APP_PROCESS_DESC' ); ?></div>
	    <ul class="adminformlist">
		    <li>
			    <?php 
				echo JCckDev::renderForm( 'core_app_elements', '', $config, array( 'storage_field'=>'app_elements' ) );
				echo JCckDev::renderForm( 'core_app_dependencies', '', $config, array() );
				echo JCckDev::renderForm( 'core_app_dependencies_menu', '', $config, array() );
				?>
		    </li>
	    </ul>
	</div>
<?php } ?>

<script type="text/javascript">
jQuery(document).ready(function($) {	
	$(".app-download").live("click", function() {
		var id = $(this).attr("title");
		var url = "<?php echo $link2; ?>"+id;
		var elements = $("#app_elements").myVal();
		document.location.href	=	url+"&elements="+elements+"&dep_categories="+$("#app_dependencies_categories").myVal()+"&dep_menu="+$("#app_dependencies_menu").myVal();
		return;
	});
	<?php if ( !JCck::on() ) { ?>
		$("ul.adminformlist li > label").css("width","175px");
	<?php } ?>
});
</script>
