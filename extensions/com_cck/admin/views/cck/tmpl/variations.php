<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: variations.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$user	=	JFactory::getUser();
$userId	=	$user->id;

Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$doc	=	JFactory::getDocument();
$js		=	'jQuery(document).ready(function($){ $("#submitBox,#resetBox").hide(); });';
$doc->addScriptDeclaration( $js );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName() ); ?>" method="post" id="adminForm" name="adminForm">
<div class="<?php echo $this->css['items']; ?>">
	<table class="<?php echo $this->css['table']; ?>" id="pagination-top">
	<thead>
		<tr>
			<th width="32" class="center hidden-phone nowrap">
                <?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?>
			</th>
			<th width="30" class="center nowrap">
				<?php echo '#'; ?>
			</th>
			<th class="center nowrap">
				<?php echo JText::_( 'COM_CCK_VARIATION' ); ?>
			</th>
			<th width="45%" class="center hidden-phone nowrap">
				<?php echo JText::_( 'COM_CCK_FOLDER' ); ?>
			</th>
			<th width="32" class="center hidden-phone nowrap">
				<?php echo JText::_( 'COM_CCK_ID' ); ?>
			</th>
		</tr>			
	</thead>
	<?php
	// Library
	jimport( 'joomla.filesystem.folder' );
	$items	=	JFolder::folders( JPATH_LIBRARIES.'/cck/rendering/variations' );
	$i		=	0;
	$n		=	count( $items );
	foreach ( $items as $j=>$item ) {
		$folder	=	'/libraries/cck/rendering/variations/';
		$link	=	JRoute::_( 'index.php?option='.$this->option.'&task=template.export_variation&variation='.$item.'&folder='.$folder );
		$last	=	( $j == $n - 1 ) ? ' last' : '';
		$item	=	( $item == 'empty' || $item == 'joomla' || $item == 'seb_css3' ) ? '<strong>'.$item.'</strong>' : $item;
		
		_rowHTML( $i++, $item, $link, $folder, $last );
	}
	// Templates
	$templates	=	JCckDatabase::loadColumn( 'SELECT name FROM #__cck_core_templates' );
	$nb			=	count( $templates );
	foreach ( $templates as $k=>$template ) {
		if ( JFolder::exists( JPATH_SITE.'/templates/'.$template.'/variations' ) ) {
			$items	=	JFolder::folders( JPATH_SITE.'/templates/'.$template.'/variations' );
			$n		=	count( $items );
			if ( $n ) {
				foreach ( $items as $j => $item ) {
					$folder	=	'/templates/'.$template.'/variations/';
					$link	=	JRoute::_( 'index.php?option='.$this->option.'&task=template.export_variation&variation='.$item.'&folder='.$folder );
					$last	=	( ( $j == $n - 1 ) && ( $k != $nb - 1 ) ) ? ' last' : '';
					
					_rowHTML( $i++, $item, $link, $folder, $last );
					$separator	=	'';
				}
			}
		}
	}
	?>
	<tfoot>
		<tr height="40px;">
	        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'closeBox', 'up' ); ?></td>
			<td class="center" colspan="3" id="pagination-bottom"></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'closeBox', 'up' ); ?></td>
		</tr>
	</tfoot>
	</table>
</div>
<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return_v" id="return_v" value="variations" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>
</form>

<?php
Helper_Display::quickCopyright();

// _rowHTML
function _rowHTML( $i, $name, $link, $folder, $last = '' ) {
	?>
    <tr class="row<?php echo $i % 2; ?><?php echo $last; ?>" height="64px;">
        <td class="center hidden-phone">
            <?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?>
        </td>
        <td class="center">
            <a href="<?php echo $link; ?>"><img src="components/<?php echo CCK_COM; ?>/assets/images/18/icon-18-download.png" border="0" alt="" /></a>
        </td>
        <td class="center">
            <?php echo $name; ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $folder; ?>
        </td>
        <td class="center hidden-phone">
            <?php Helper_Display::quickSlideTo( 'closeBox', $i + 1 ); ?>
        </td>
    </tr>
    <?php
}
?>