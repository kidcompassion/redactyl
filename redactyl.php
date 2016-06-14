<?php
/*
* Plugin Name: Redactyl
* Plugin URI: http://thecreativetemp.com/redactyl
* Description: A plugin that lets you choose words to black out across your site.
* Version: 1.0
* Author: Sally Poulsen
* Author URI: thecreativetemp.com
* License: License: GPL2
 
Redactyl is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Redactyl is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Redactyl. If not, see http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.

*/
//go through every post 
//hook on page load
//redact whatever word has been indicated in the admin panel

defined( 'ABSPATH' ) or die( 'No touchy!' );


function redactyl_add_js($hook){

	wp_enqueue_script('redactyl', plugin_dir_url( __FILE__ ) . 'js/redactyl.js' );
}

add_action( 'admin_enqueue_scripts', 'redactyl_add_js' );

function redactyl_add_css($hook){

	wp_enqueue_style('redactyl-css', plugin_dir_url( __FILE__ ) . 'css/redactyl.css' );
}



add_action( 'admin_enqueue_scripts', 'redactyl_add_css' );

add_action( 'admin_menu', 'redactyl_add_admin_menu' );
add_action( 'admin_init', 'redactyl_settings_init' );


function redactyl_add_admin_menu(  ) { 

	add_options_page( 'Redactyl', 'Redactyl', 'manage_options', 'redactyl', 'redactyl_options_page' );

}


function redactyl_settings_init(  ) { 

	register_setting( 'pluginPage', 'redactyl_settings' );

	add_settings_section( //header for section
		'redactyl_pluginPage_section', 
		__( 'What word[s] would you like to redact?', 'http://sallypoulsen.com' ), 
		'redactyl_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( //adds field
		'redactyl_text_field_0', 
		__( 'Enter a word to redact', 'http://sallypoulsen.com' ), 
		'redactyl_text_field_0_render', 
		'pluginPage', 
		'redactyl_pluginPage_section' 
	);


}


function redactyl_text_field_0_render(  ) { 

	$options = get_option( 'redactyl_settings' );
	//Generate first field, with numeric ID being dynamically generated
	//Generate button to add fields
	//Generate button to delete fields
	//when button is clicked, add a field and increment the ID

	?>



	<input type='text' name='redactyl_settings[redactyl_text_field_0]' value='<?php echo $options['redactyl_text_field_0']; ?>'>
	
	<?php

}


function redactyl_settings_section_callback(  ) { 

	echo __( 'This section description', 'http://sallypoulsen.com' );

}


function redactyl_options_page(  ) { 

	$redacted_words = get_option('redactyl_settings', 'fWay to go, you broke');
	;?>


	<script>
	
	redactedWords = new Object();

	</script>
	<?php 
	foreach ($redacted_words as $key => $value) {
		echo $key;
		echo '<br/>';
		echo $value;
		echo '<br/>';?>
		<script>

			redactedWords["<?php echo $key;?>"] = "<?php echo $value;?>";
		</script>

<?php 
	}


	?>


	
	<div class="redactyl">
		<form action='options.php' method='post'>


			<h2>Redactyl</h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

		</form>
	</div>
	<?php

}


//get data from a setting field



add_filter('the_content', 'redactyl_content');

function redactyl_content($content){
	//get selected word
	
	$redact;
	$allWords = get_option('redactyl_settings', 'Way to go, you broke it');
	//print_r($allWords);
	$i = 0;
	foreach($allWords as $word):
		$redact[$i] = $word;
		$i++;
	endforeach;

	foreach($redact as $r):
		//Get word length
		$wordLength = strlen($r);
		//Make the redaction bar longer that the word by ten px
		$boxLength = 10*$wordLength;
		//Generate the redaction bar and wrap the specified word
		$blackBox = '<span style="margin-left: 2px; background-color: black;width:'. $boxLength .'px; display: inline-block; height: 16px;"></span>';

		//If a redacted word exists, plug the bar in instead, and pass everything into the content var.
		if (stristr( $content , $r) == true){	
			$content = str_ireplace($r, $blackBox, $content);
		}

	endforeach;
	//Return the content var, whether it has redactions or no.
	return $content;
}


add_filter('the_title', 'redactyl_title');

function redactyl_title($title){


	$redact;
	$allWords = get_option('redactyl_settings', 'Way to go, you broke it');
	//print_r($allWords);
	
	$i = 0;
	foreach($allWords as $word):
		$redact[$i] = $word;
		$i++;
	endforeach;

	foreach($redact as $r):
		//Get word length
		$wordLength = strlen($r);
		//Make the redaction bar longer that the word by ten px
		$boxLength = 10*$wordLength;
		//Generate the redaction bar and wrap the specified word
		$blackBox = '<span style="margin-left: 2px; background-color: black;width:'. $boxLength .'px; display: inline-block; height: 16px;"></span>';

		//If a redacted word exists, plug the bar in instead, and pass everything into the title var.
		if (stristr( $title , $r) == true){	
			$content = str_ireplace($r, $blackBox, $title);
		}

	endforeach;
	//Return the title var, whether it has redactions or no.
	return $title;
	
}


?>