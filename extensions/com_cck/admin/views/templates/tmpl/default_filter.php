<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_filter.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$clear	=	"this.form.getElementById('filter_folder').value='';this.form.getElementById('filter_state').value='1';this.form.getElementById('filter_mode').value='';";
if ( $this->js['filter'] ) {
	$doc->addScriptDeclaration( $this->js['filter'] );
}
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<div class="<?php echo $this->css['filter_search']; ?>">
        <?php
		echo JCckDev::getForm( $cck['core_location_filter'], $this->state->get( 'filter.location' ), $config )."\n";
		echo JCckDev::getForm( $cck['core_filter_input'], $this->escape( $this->state->get( 'filter.search' ) ), $config, array( 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_ITEMS_SEARCH_FILTER' ).'" style="text-align:center;"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_go'], '', $config, array( 'css'=>$this->css['filter_search_button'] ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_search'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="'.$clear.'this.form.submit();"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_clear'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="this.form.getElementById(\'filter_search\').value=\'\';this.form.getElementById(\'filter_location\').value=\'title\';'.$clear.'this.form.submit();"' ) );
		?>
	</div>
	<?php if ( JCck::on() ) { ?>
		<!--<script type="text/javascript">
		Joomla.orderTable = function() {
			table = document.getElementById("sortTable");
			direction = document.getElementById("directionTable");
			order = table.options[table.selectedIndex].value;
			if (order != '<?php echo $listOrder; ?>') {
				dirn = 'asc';
			} else {
				dirn = direction.options[direction.selectedIndex].value;
			}
			Joomla.tableOrdering(order, dirn, '');
		}
		</script>-->
		<div class="<?php echo $this->css['filter_search_list']; ?>">
			<label for="limit" class="element-invisible"><?php echo JText::_( 'JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC' ); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<!--<div class="btn-group pull-right hidden-phone">
			<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
				<option value="asc" <?php if ($listDir == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
				<option value="desc" <?php if ($listDir == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
			</select>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
				<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
			</select>
		</div>-->
	<?php } ?>
	<div class="<?php echo $this->css['filter_select']; ?>">
        <?php
        echo $this->html['filter_select_header'];
		echo JCckDev::getForm( $cck['core_type_filter_template'], $this->state->get( 'filter.mode' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getForm( $cck['core_folder_filter'], $this->state->get( 'filter.folder' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getForm( $cck['core_state_filter'], $this->state->get( 'filter.state' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
        ?>
	</div>
</div>