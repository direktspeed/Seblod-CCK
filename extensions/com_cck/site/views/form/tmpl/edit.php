<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app	=	JFactory::getApplication();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';
?>

<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_form_title ) {
	$tag		=	$this->tag_form_title;
	$class		=	trim( $this->class_form_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . @$this->type->title . '</'.$tag.'>';
}
if ( $this->show_form_desc == 1 && $this->description != '' ) {
	echo '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
if ( @$this->config['error'] === true ) {
	return;
}

if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	Helper_Include::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#seblod_form").validationEngine("validate",task) === true) { if (jQuery("#seblod_form").isStillReady() === true) { jQuery("#seblod_form input[name=\'config[unique]\']").val("'.$this->unique.'"); Joomla.submitform("save", document.getElementById("seblod_form")); } }';
} else {
	$js	=	'if (jQuery("#seblod_form").isStillReady() === true) { jQuery("#seblod_form input[name=\'config[unique]\']").val("'.$this->unique.'"); Joomla.submitform("save", document.getElementById("seblod_form")); }';
}
?>

<script type="text/javascript">
<?php echo $this->config['submit']; ?> = function(task) { <?php echo $js; ?> }
</script>

<?php
echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.JRoute::_( 'index.php?option='.$this->option ).'" autocomplete="off" enctype="multipart/form-data" method="post" id="seblod_form" name="seblod_form">';
echo '<div class="cck_page_form'.$this->pageclass_sfx.'" id="system">' . $this->data . '</div>';
?>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo $this->id; ?>" />
    <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
    
    <input type="hidden" name="config[type]" value="<?php echo $this->type->name; ?>" />
    <input type="hidden" name="config[stage]" value="<?php echo $this->stage; ?>" />
    <input type="hidden" name="config[skip]" value="<?php echo $this->skip; ?>" />
    <input type="hidden" name="config[url]" value="<?php echo $this->config['url']; ?>" />
    <input type="hidden" name="config[id]" value="<?php echo @$this->config['id']; ?>" />
    <input type="hidden" name="config[itemId]" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" />
    <input type="hidden" name="config[unique]" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>
</form>

<?php
if ( $this->show_form_desc == 2 && $this->description != '' ) {
	echo '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
?>
</div></div>