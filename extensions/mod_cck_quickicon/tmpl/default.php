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

if ( JCck::on() ) {
	/*
	$n	=	( count( $buttons ) == 3 ) ? 1 : 4;
	for ( $i = 0; $i < $n; $i++ ) {
		$buttons[$i]['image']	=	'';
		$buttons[$i]['text']	=	'&nbsp;&mdash;&nbsp;'.$buttons[$i]['text'];
	}
	array_unshift( $buttons, $buttons[$n] );
	unset( $buttons[$n+1] );
	*/
	$html	=	JHtml::_( 'icons.buttons', $buttons );
	if ( !empty( $html ) ) { ?>
    <div class="row-striped">
        <?php echo $html; ?>
    </div>
<?php } } else { ?>
    <div id="cpanel">
    <?php
    foreach ( $buttons as $button ) {
        echo modCCKQuickIconHelper::button( $button );
	}
    ?>
    </div>
<?php } ?>