<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Tag Chips
 * Description:       Add form fields to Edit Tag page in wp-admin
 * Version:           1.0
 * Author:            Micah Robinson
 * License:           GPL-2.0
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-tag-chips
 WC tested up to: 5.1.1
 */

/** Die if accessed directly
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		// ONLY RUN IF WOOCOMMERCE IS ACTIVE....

	if ( is_admin() ) {
		$taxonomy= 'product_tag';
		add_action( "{$taxonomy}_add_form_fields", 'add_chip_tag_form', 10);
		add_action( "{$taxonomy}_edit_form_fields", 'edit_chip_tag_form', 10, 2 );
		function add_chip_tag_form( $taxonomy ) {
			?>
				<div class="form-field">
					<label for="chiptext">Chip Text</label>
					<input name="chiptext" id="chiptext" type="text" size="40" value="" placeholder="">
					<p class="description">Add text to see a preview of the chip that will appear above the product name.</p>
				</div>
				<div class="form-field">
					<label for="chipbgcolor">Chip Color</label>
					<input name="chipbgcolor" id="chipbgcolor" type="text" size="40" value="" placeholder="">
					<p class="description">Add an HTML color name or HEX code to see a preview of the chip.</p>
				</div>
				<div class="form-field">
					<label for="chiptxtcolor">Chip Text Color</label>
					<input name="chiptxtcolor" id="chiptxtcolor" type="text" size="40" value="" placeholder="">
					<p class="description">Add an HTML color name or HEX code to see a preview of the chip.</p>
				</div>
				<div class="form-field" id="chip-demo">
					<label>Preview</label>
					<div class="white-display-box"><div class="tag-chip"></div></div>
				</div>
			<?php
		};
		function edit_chip_tag_form( $tag, $taxonomy ) {
			$termid = $tag->term_id;
			$chip_text_str = get_option( "chip_$termid" );
			$chip_text_obj = unserialize( $chip_text_str );
			?>
		    <tr class="form-field chip-form chip-form-top">
		        <th valign="top" scope="row">
		            <label for="chiptext">Chip Text</label>
		        </th>
		        <td>
		            <input name="chiptext" id="chiptext" type="text" size="40" value="<?php echo $chip_text_obj['chip_text']; ?>" placeholder="">
		            <p class="description">Add text to see a preview of the chip that will appear above the product name.</p>
		        </td>
		    </tr>
			<tr class="form-field chip-form chip-form-middle">
		        <th valign="top" scope="row">
		            <label for="chipbgcolor">Chip Color</label>
		        </th>
		        <td>
		            <input name="chipbgcolor" id="chipbgcolor" type="text" size="40" value="<?php echo $chip_text_obj['chip_bg_color']; ?>" placeholder="">
		            <p class="description">Add an HTML color name or HEX code to see a preview of the chip.</p>
		        </td>
		    </tr>
		    </tr>
			<tr class="form-field chip-form chip-form-middle">
		        <th valign="top" scope="row">
		            <label for="chiptxtcolor">Chip Text Color</label>
		        </th>
		        <td>
		            <input name="chiptxtcolor" id="chiptxtcolor" type="text" size="40" value="<?php echo $chip_text_obj['chip_txt_color']; ?>" placeholder="">
		            <p class="description">Add an HTML color name or HEX code to see a preview of the chip.</p>
		        </td>
		    </tr>
			<tr class="form-field chip-form chip-form-bottom" id="chip-demo">
		        <th valign="top" scope="row">
		            <label for="chiptext">Preview</label>
		        </th>
						<td>
							<div class="white-display-box"><div class="tag-chip" style="background-color:<?php echo $chip_text_obj['chip_bg_color']; ?>;color:<?php echo $chip_text_obj['chip_txt_color']; ?>;"><?php echo $chip_text_obj['chip_text']; ?></div></div>
						</td>
				</tr>
			<?php
		};

		add_action ( "edited_{$taxonomy}", 'save_chip_tag_form' );
		add_action ( "created_{$taxonomy}",'save_chip_tag_form' );
		function save_chip_tag_form( $term_id ) {
			if ( isset( $_POST['chiptext'] ) ) {
				$termid= $term_id;
				$prev_chip_text_str = get_option( "chip_$termid" );
				if ( $prev_chip_text_str !== false ) {
					$tag_chip['chip_text']= $_POST['chiptext'];
					$tag_chip['chip_bg_color']= $_POST['chipbgcolor'];
					$tag_chip['chip_txt_color']= $_POST['chiptxtcolor'];
					update_option( "chip_$termid", serialize($tag_chip) );
				} else {
					$tag_chip= array(
						'chip_text'=> '',
						'chip_bg_color'=> '',
						'chip_txt_color'=> ''
					);
					$tag_chip['chip_text']= $_POST['chiptext'];
					$tag_chip['chip_bg_color']= $_POST['chipbgcolor'];
					$tag_chip['chip_txt_color']= $_POST['chiptxtcolor'];
					add_option( "chip_$termid", serialize($tag_chip), '', 'yes' );
				}
			}
		}
	};
	add_action( 'woocommerce_single_product_summary', 'add_tag_chip', 4 );
	function add_tag_chip(){
		if ( is_product() ) {
			global $product;
			$tagIds= $product->get_tag_ids();
			foreach( $tagIds as $tagId ){
				$chipStr= get_option( "chip_$tagId");
				if($chipStr){
					$chipObj= unserialize($chipStr);
					$chip_text= $chipObj['chip_text'];
					$bg_color= $chipObj['chip_bg_color'];
					$txt_color= $chipObj['chip_txt_color'];
					if( $chip_text !== false && $chip_text !== '' ){
						echo '<div class="tag-chip" style="background-color:' . $bg_color . ';color:' . $txt_color . ';">' . $chip_text . '</div>';
					}
				}
			}
		}
	};

	// Register admin style sheet.
	add_action( 'admin_enqueue_scripts', 'register_admin_tag_chips_styles' );
	function register_admin_tag_chips_styles() {
		wp_enqueue_style( 'woocommerce-tag-chips-admin-style', plugins_url( basename( dirname(__FILE__) ) ) . '/admin/css/woocommerce-tag-chips.css' );
		wp_enqueue_script('woocommerce-tag-chips-admin-script', plugins_url( basename( dirname(__FILE__) ) ) . '/admin/js/woocommerce-tag-chips.js' );
	};
	// Register public style sheet.
	add_action( 'wp_enqueue_scripts', 'register_public_tag_chips_styles' );
	function register_public_tag_chips_styles() {

		wp_register_style( 'woocommerce-tag-chips-public-style', plugins_url( basename( dirname(__FILE__) ) ) . '/public/css/woocommerce-tag-chips.css' );
		wp_enqueue_style('woocommerce-tag-chips-public-style');
	};
}
