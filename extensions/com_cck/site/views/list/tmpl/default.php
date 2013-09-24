<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	Helper_Include::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#seblod_form").validationEngine("validate",task) === true) { Joomla.submitform("search", document.getElementById("seblod_form")); }';
} else {
	$js	=	'Joomla.submitform("search", document.getElementById("seblod_form"));';
}
$app	=	JFactory::getApplication();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';
?>

<script type="text/javascript">
<?php echo $this->config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>

<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_list_title ) {
	$tag		=	$this->tag_list_title;
	$class		=	trim( $this->class_list_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . @$this->search->title . '</'.$tag.'>';
}
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}

echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.JRoute::_( 'index.php?option='.$this->option ).'" autocomplete="off" method="get" id="seblod_form" name="seblod_form">';
echo '<div class="cck_page_search'.$this->pageclass_sfx.'">' . $this->form . '</div>';
?>

<div class="clr"></div>
<div>
	<?php if ( !JFactory::getApplication()->getCfg( 'sef' ) || !$this->config['Itemid'] || $this->home !== false ) { ?>
	<input type="hidden" name="option" value="com_cck" />
	<input type="hidden" name="view" value="list" />
	<?php if ( $this->home === false ) { ?>
	<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" />
	<?php } }
	$tmpl	=	$app->input->get( 'tmpl', '' );
	if ( $tmpl ) { ?>
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
	<?php } ?>
	<input type="hidden" name="search" value="<?php echo $this->search->name; ?>" />
	<input type="hidden" name="task" value="search" />
</div>
</form>

<div class="cck_page_list<?php echo $this->pageclass_sfx; ?>" id="system">
	<?php
	if ( $this->show_items_number ) {
		$label	=	$this->label_items_number;
		if ( $this->config['doTranslation'] ) {
			$label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ) ) );
		}
		echo '<div class="'.$this->class_items_number.'"><span>' . $this->total .'</span> '. $label . '</div>';
	}
	if ( $this->show_pagination == -1 || $this->show_pagination == 1 ) {
		echo '<div class="'.$this->class_pagination.'">' . $this->pagination->getPagesLinks() . '</div>';
	}
	if ( @$this->search->content > 0 ) {
		echo '<div class="cck_page_items">'.$this->data.'</div>';
	} else {
		echo $this->loadTemplate( 'items' );
	}
	if ( $this->show_pages_number || $this->show_pagination > -1 ) {
	    echo '<div class="'.$this->class_pagination.'">';
		$pagesCounter	=	$this->pagination->getPagesCounter();
    	if ( $this->show_pages_number && $pagesCounter ) {
	        echo '<p class="counter">' . $pagesCounter . '</p>';
    	}
		if ( $this->show_pagination > -1 ) {
			echo $this->pagination->getPagesLinks();
		}
	    echo '</div>';
	}
    ?>
</div>

<?php
if ( $this->show_list_desc == 2 && $this->description != '' ) {
	echo '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
?>
</div></div>