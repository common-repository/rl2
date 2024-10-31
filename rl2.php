<?php
/*
Plugin Name: rl2
Plugin URI: http://localhost/wordpress/rl2
Description: A resource list plugin using custom posts and taxonomies
Version: 0.2
Author: Randy Wright
Author URI: http://lrw.net/
License: GPLv2
*/

register_activation_hook( __FILE__, 'rl2_install' );

function rl2_install() {
  // activation stuff here
  if( version_compare( get_bloginfo('version'), '3.1', '<') ) {
    deactivate_plugins(basename(__FILE__)); //take it out
  }

/*  $rl2_options = array(
    'view' => 'grid',
    'food' => 'bacon',
    'mode' => 'zombie',
  );
  update_option( 'rl2_options', $rl2_options );
*/
}

register_deactivation_hook(__FILE__, 'rl2_uninstall');

function rl2_uninstall() {
  // deactivation code here
}

// Set up the post type. 
add_action( 'init', 'rl2_resource_register_post_types' );

// Registers post type.
function rl2_resource_register_post_types() {

    // args for the post type. 
    $rl2_resource_args = array(
        'public' => true,
        'query_var' => 'rl2_resource',
        'rewrite' => array(
            'slug' => 'rl2_resource',
            'with_front' => false,
        ),
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'custom-fields',
            'comments'
        ),
        'labels' => array(
            'name' => 'Resources',
            'singular_name' => 'Resource',
            'add_new' => 'Add New Resources',
            'add_new_item' => 'Add New Resource',
            'edit_item' => 'Edit Resource',
            'new_item' => 'New Resource',
            'view_item' => 'View Resource',
            'search_items' => 'Search Resources',
            'not_found' => 'No Resources Found',
            'not_found_in_trash' => 'No Resources Found In Trash'
        ),
    );

    /* Register the music album post type. */
    register_post_type( 'rl2_resource', $rl2_resource_args );
}

/* Set up the taxonomies. */
add_action( 'init', 'rl2_resource_register_taxonomies' );

/* Registers taxonomies. */
function rl2_resource_register_taxonomies() {


    /* Set up the resource categories taxonomy arguments. */
    $resource_categories_args = array(
        'hierarchical' => true,
        'query_var' => 'resource_categories', 
        'show_tagcloud' => true,
        'rewrite' => array(
            'slug' => 'resourceCategories',
            'with_front' => false
        ),
        'labels' => array(
            'name' => 'Resource Categories',
            'singular_name' => 'Resource Category',
            'edit_item' => 'Edit Resource Category',
            'update_item' => 'Update Resource Category',
            'add_new_item' => 'Add New Resource Category',
            'new_item_name' => 'New Resource Category Name',
            'all_items' => 'All Resource Categories',
            'search_items' => 'Search Resource Categories',
            'parent_item' => 'Parent Resource Category',
            'parent_item_colon' => 'Parent Resource Category:',
        ),
    );

    /* Register the album genre taxonomy. */
    register_taxonomy( 'resource_categories', array( 'rl2_resource' ), $resource_categories_args );
}

// shortcode 
add_action( 'init', 'rl2_resource_register_shortcodes' );

function rl2_resource_register_shortcodes() {
    /* Register the [rl2_resources] shortcode. */
    add_shortcode( 'rl2_resources', 'rl2_resource_shortcode' );
    add_shortcode( 'rl2_user_form', 'rl2_user_form_shortcode');
}


function rl2_user_form_shortcode($atts) {

//error_log( "line ". __LINE__." get user form", 0 );

  return get_rl2_user_form();
}

function rl2_resource_shortcode($atts) {

    extract( shortcode_atts( array( 'rcat' => '*' ), $atts ) ); // no defaults
//error_log( "line ". __LINE__." rcat: ".$rcat, 0 );

    /* Query from the database. */
    $loop = new WP_Query(
        array(
            'post_type' => 'rl2_resource',
            'orderby' => 'title',
            'order' => 'ASC',
            'posts_per_page' => -1,
	    'resource_categories' => $rcat
        )
    );

    /* Check if any rl2_resources were returned. */
    if ( $loop->have_posts() ) {

        /* Open an unordered list. */
        $output = '<ul class="rl2_resources">';

        /* Loop through the list (The Loop). */
        while ( $loop->have_posts() ) {

            $loop->the_post();

            /* Display the title title. */
            $output .= the_title(
                '<li><a href="' . get_permalink() . '">',
                '</a></li>',
                false
            );
        }
        /* Close the unordered list. */
        $output .= '</ul>';
    }
    /* If no resources were found. */
    else {
        $output = '<p>No resources have been published.';
    }
    /* Return the resourcess list. */
    return $output;
}


// output metaboxes for editing
function rl2_resource_data_meta_box($post,$box) {
  $id = $post->ID;

  echo( '<table border="0">' );
    //rl2_url
  $url = get_post_meta($id,'_rl2_url',true);
  echo( '<tr><td>'.'URL'.': </td><td><input type="text" name="rl2_url" value="'.
    esc_attr($url).'" size="40" /></td></tr>'."\n");
    //rl2_email
  $email = get_post_meta($id,'_rl2_email',true);
  echo( '<tr><td>'.'Email'.': </td><td><input type="text" name="rl2_email" value="'.
    esc_attr($email).'" size="40" /></td></tr>'."\n");

    //rl2_addr1
  $addr1 = get_post_meta($id,'_rl2_addr1',true);
  echo( '<tr><td>'.'Address 1'.': </td><td><input type="text" name="rl2_addr1" value="'.
    esc_attr($addr1).'" size="40" /></td></tr>'."\n");

    //rl2_addr2
  $addr2 = get_post_meta($id,'_rl2_addr2',true);
  echo( '<tr><td>'.'Address 2'.': </td><td><input type="text" name="rl2_addr2" value="'.
    esc_attr($addr2).'" size="40" /></td></tr>'."\n");

    //rl2_city
  $city = get_post_meta($id,'_rl2_city',true);
  echo( '<tr><td>'.'City'.': </td><td><input type="text" name="rl2_city" value="'.
    esc_attr($city).'" size="40" /></td></tr>'."\n");

    //rl2_state
  $state = get_post_meta($id,'_rl2_state',true);
  echo( '<tr><td>'.'State'.': </td><td><input type="text" name="rl2_state" value="'.
    esc_attr($state).'" size="40" /></td></tr>'."\n");

    //rl2_zip
  $zip = get_post_meta($id,'_rl2_zip',true);
  echo( '<tr><td>'.'Zip'.': </td><td><input type="text" name="rl2_zip" value="'.
    esc_attr($zip).'" size="40" /></td></tr>'."\n");

    //rl2_telephone
  $telephone = get_post_meta($id,'_rl2_telephone',true);
  echo( '<tr><td>'.'Telephone'.': </td><td><input type="text" name="rl2_telephone" value="'.
    esc_attr($telephone).'" size="40" /></td></tr>'."\n");

    //rl2_submitter
  $submitter = get_post_meta($id,'_rl2_submitter',true);
  echo( '<tr><td>'.'Submitter Name'.': </td><td><input type="text" name="rl2_submitter" value="'.
    esc_attr($submitter).'" size="40" /></td></tr>'."\n");

}


// register the scripts
// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'rl2.js', array( 'jquery', 'jquery-form' ) );
wp_enqueue_script( 'jquery-form' );

// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// register the handlers
add_action('wp_ajax_rl2_ajax_post_create', 'rl2_ajax_post_create' );
add_action('wp_ajax_nopriv_rl2_ajax_post_create', 'rl2_ajax_post_create' );

// handle ajax post request
function rl2_ajax_post_create() {
	global $_POST; //contents sent by client ajax

error_log( "line ". __LINE__, 0 );

        $nonce=$_POST['_wpnonce'];	    
        if (! wp_verify_nonce($nonce, 'my-nonce') ) die("Security check");
error_log( "line ". __LINE__, 0 );

	$post = array(
		'comment_status' => 'open', // [ 'closed' | 'open' ] // 'closed' means no comments.
		'ping_status' => 'open', //[ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
		'post_author' => 'rl2_owner', // [ <user ID> ] //The user ID number of the author.
		'post_category' => array(), //[ array(<category id>, <...>) ] //Add some categories.
		'post_content' => $_POST[rl2_description],  //[ <the text of the post> ] //The full text of the post.
		'post_date' => current_time($type, 1), //[ Y-m-d H:i:s ] //The time post was made.
		'post_date_gmt' => current_time($type, 0), // [ Y-m-d H:i:s ] //The time post was made, in GMT.
		'post_excerpt' => '', // [ <an excerpt> ] For all your post excerpt needs.
		'post_name' => $_POST['rl2_name'], //[ <the name> ] // The name (slug) for your post
		'post_status' => 'draft', //[ 'draft' | 'publish' | 'pending'| 'future' | 'private' ] //Set the status of the new post. 
		'post_title' => $_POST['rl2_name'], //[ <the title> ] //The title of your post.
		'post_type' => 'rl2_resource', // [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] //You may want to insert a regular post, page, link, a menu item or some custom post type
		'tax_input' => array(), // [ array( 'taxonomy_name' => array( 'term', 'term2', 'term3' ) ) ] // support for custom taxonomies. 
	);

// other post fields
//  'ID' => [ <post id> ] //Are you updating an existing post?
//  'menu_order' => '', [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
//  'pinged' => //[ ? ] //?
//  'post_parent' => [ <post ID> ] //Sets the parent of the new post.
//  'post_password' => [ ? ] //password for post?
//  'tags_input' => [ '<tag>, <tag>, <...>' ] //For tags.
//  'to_ping' => [ ? ] //?

	$post_id = wp_insert_post( $post, $wp_error );
	error_log( "line ". __LINE__." post_id: ".$post_id, 0 );
	
	if( $post_id ) { // non zero means post was created
	
		// save the post meta data
	  if( isset($_POST['rl2_url']) ) {
	    update_post_meta($post_id,'_rl2_url',esc_attr($_POST['rl2_url']) );
	  }
	  if( isset($_POST['rl2_email']) ) {
	    update_post_meta($post_id,'_rl2_email',esc_attr($_POST['rl2_email']) );
	  }
	  if( isset($_POST['rl2_addr1']) ) {
	    update_post_meta($post_id,'_rl2_addr1',esc_attr($_POST['rl2_addr1']) );
	  }
	  if( isset($_POST['rl2_addr2']) ) {
	    update_post_meta($post_id,'_rl2_addr2',esc_attr($_POST['rl2_addr2']) );
	  }
	  if( isset($_POST['rl2_city']) ) {
	    update_post_meta($post_id,'_rl2_city',esc_attr($_POST['rl2_city']) );
	  }
	  if( isset($_POST['rl2_state']) ) {
	    update_post_meta($post_id,'_rl2_state',esc_attr($_POST['rl2_state']) );
	  }
	  if( isset($_POST['rl2_zip']) ) {
	    update_post_meta($post_id,'_rl2_zip',esc_attr($_POST['rl2_zip']) );
	  }
	  if( isset($_POST['rl2_telephone']) ) {
	    update_post_meta($post_id,'_rl2_telephone',esc_attr($_POST['rl2_telephone']) );
	  }
	  if( isset($_POST['rl2_submitter']) ) {
	    update_post_meta($post_id,'_rl2_submitter',esc_attr($_POST['rl2_submitter']) );
	  }
	  rl2_list_email_notification($post_id);
	}

	    // generate the response
	$response = json_encode( array( 'success' => true ) );
 
	    // response output
	header( "Content-Type: application/json" );
	echo $response;
	die();
}


	// save the metabox inputs
function rl2_save_meta_boxes($post_id) {
	global $wpdb, $_POST;

  	if( isset($_POST['rl2_url']) ) {	  
		update_post_meta($post_id,'_rl2_url',esc_attr($_POST['rl2_url']) );
	}
	if( isset($_POST['rl2_email']) ) {
	  update_post_meta($post_id,'_rl2_email',esc_attr($_POST['rl2_email']) );
	}
	if( isset($_POST['rl2_addr1']) ) {  
	update_post_meta($post_id,'_rl2_addr1',esc_attr($_POST['rl2_addr1']) );
	}
	if( isset($_POST['rl2_addr2']) ) {
	    update_post_meta($post_id,'_rl2_addr2',esc_attr($_POST['rl2_addr2']) );
	}
	if( isset($_POST['rl2_city']) ) {
	    update_post_meta($post_id,'_rl2_city',esc_attr($_POST['rl2_city']) );
	}
	if( isset($_POST['rl2_state']) ) {
	    update_post_meta($post_id,'_rl2_state',esc_attr($_POST['rl2_state']) );
	}
	if( isset($_POST['rl2_zip']) ) {
	    update_post_meta($post_id,'_rl2_zip',esc_attr($_POST['rl2_zip']) );
	}
	if( isset($_POST['rl2_telephone']) ) {
	    update_post_meta($post_id,'_rl2_telephone',esc_attr($_POST['rl2_telephone']) );
	}
	if( isset($_POST['rl2_submitter']) ) {
	    update_post_meta($post_id,'_rl2_submitter',esc_attr($_POST['rl2_submitter']) );
	}

}

function rl2_list_email_notification ($id)  {
    global $wpdb, $_POST;

//error_log( "line ". __LINE__." id: ".$id, 0 );

    $friends = get_bloginfo('admin_email');
    $i_noti_sub = get_bloginfo('name')." received a new entry via Resource List Plugin ";
    $i_noti = '<p>A new link was submitted through Resource List Plugin from.</p> <p>Name        : '. stripslashes($_POST['rl2_submitter']) .


	    '<br>resource_url		: '. $_POST['rl2_url'] .
            '<br>resource_name		: '. $_POST['rl2_name'] .
            '<br>resource_email		: '. $_POST['rl2_email'] .
            '<br>resource_addr1		: '. $_POST['rl2_addr1'] .
            '<br>resource_addr2		: '. $_POST['rl2_addr2'] .
            '<br>resource_city		: '. $_POST['rl2_city'] .
            '<br>resource_state		: '. $_POST['rl2_state'] .
            '<br>resource_zip		: '. $_POST['rl2_zip'].
            '<br>resource_telephone	: '. $_POST['rl2_telephone'] .
            '<br>resource description	: '. $_POST['rl2_description'] .
            '<br>submitter_name		: '. $_POST['rl2_submitter'] .
            '</p>' .
            '<p>' .
            '<a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=resource_list_unique&amp;r_ls_mailconf='.$id.
            '" >Approve and display it on your list</a><br>'.
            '<a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=resource_list_unique&amp;delete='.$id.'">Delete it </a>' ;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Your  blog: '.get_bloginfo('name').' using Resource List Plugin ' . "\r\n";

    mail($friends, $i_noti_sub , $i_noti , $headers );
    return $post_ID;
}

add_action('admin_init', 'rl2_resource_meta_box_init' );

/**
 * initializer for meta boxes, no args, no returns
 *
 */
function rl2_resource_meta_box_init() {
    // meta boxes
  add_meta_box('rl2_resource_meta','Resource Data', 'rl2_resource_data_meta_box', 'rl2_resource', 'normal', 'default' );
  
    // save data when post is saved 
  add_action('save_post', 'rl2_save_meta_boxes' );
}

add_filter('the_content', 'rl2_add_end' );


/** 
 * display the Resource entry at the end of the content, used when displaying the rl2_resource post type.
 * 
 * 
 * @since
 * @uses
 * @param string $content The post content
 * @return
 */
function rl2_add_end ($content) {

	$id = get_the_ID();;
	$my_type = get_post_type();
	if( $my_type != 'rl2_resource' ) { return $content; }

	if( is_singular('rl2_resource') ) { $single = 1; } else { $single = 0; }

	$content .= '<!-- id: '.$id.', content type: '.$my_type.', single:'.$single."  -->\n";

	if( ! my_type == 'rl2_resource' ) { return $content; }

	$addon = '<p><table border="0">' ;  

	    //rl2_url
	$url = get_post_meta($id,'_rl2_url',true);
	if( $url ) {$addon .= '<tr><td>'.'URL'.': </td><td><a href="'.esc_attr($url).'">'. esc_attr($url).'</a></td></tr>'."\n";}

	    //rl2_email
	$email = get_post_meta($id,'_rl2_email',true);
	if( $email) {$addon .= '<tr><td>'.'Email'.': </td><td><a href="mailto:'.esc_attr($email).'">'.esc_attr($email)."</a></td></tr>\n"; }

	    //rl2_addr1
	$addr1 = get_post_meta($id,'_rl2_addr1',true);
	if( $addr1 ) {$addon .= '<tr><td>'.'Address 1'.': </td><td>'.  esc_attr($addr1).'</td></tr>'."\n"; }

	    //rl2_addr2
	$addr2 = get_post_meta($id,'_rl2_addr2',true);
	if( $addr2 ) {$addon .= '<tr><td>'.'Address 2'.': </td><td>'.  esc_attr($addr2).'</td></tr>'."\n";}

	    //rl2_city
	$city = get_post_meta($id,'_rl2_city',true);
	if( $city ) {$addon .= '<tr><td>'.'City'.': </td><td>'.  esc_attr($city).'</td></tr>'."\n"; }

	    //rl2_state
	$state = get_post_meta($id,'_rl2_state',true);
	if( $state) {$addon .= '<tr><td>'.'State'.': </td><td>'.  esc_attr($state).'</td></tr>'."\n";}

	    //rl2_zip
	$zip = get_post_meta($id,'_rl2_zip',true);;
	if( $zip ) {$addon .= '<tr><td>'.'Zip'.': </td><td>'.  esc_attr($zip).'</td></tr>'."\n";}

	    //rl2_telephone
	$telephone = get_post_meta($id,'_rl2_telephone',true);
	if( $telephone ) {$addon .= '<tr><td>'.'Telephone'.': </td><td>'.  esc_attr($telephone).'</td></tr>'."\n";}

	    //rl2_submitter
//	$submitter = get_post_meta($id,'_rl2_submitter',true);
//	$addon .= '<tr><td>'.'Submitter Name'.': </td><td>'.  esc_attr($submitter).'</td></tr>'."\n";

	$addon .= "</table>\n";

  
	$content .= $addon;
	$content .= get_rl2_user_form();
 
	return $content;
}

	// return the input form
function get_rl2_user_form() {
	global $wpdb, $post ;
	//$i_options = get_option("resource_list_all_options");
	$ok = "<script language=\"javascript\">\n".
	      "jQuery('#resourceToggler').click(function() {
	        jQuery('#resoureceListFormWrapper').toggle('slow', function() {
	        // Animation complete.
	        });
	      });
              jQuery('#resoureceListFormWrapper').hide();

		jQuery(document).ready(function() {
		  var options = {
		   dataType: 'json',
		   url: MyAjax.ajaxurl, 
		   clearForm: true,
		   success: showResponse,
		   error: function() { alert('In order to prevent spam, this from can only be used for one submission. Reload the page to submit another'); } }; 
                  jQuery('#info').ajaxForm(options); }); 
			// post-submit callback 
		function showResponse(responseText, statusText, xhr, \$output1 )  { 
			// for normal html responses, the first argument to the success callback 
		    // is the XMLHttpRequest object's responseText property 
 
		    // if the ajaxForm method was passed an Options Object with the dataType 
		    // property set to 'xml' then the first argument to the success callback 
		    // is the XMLHttpRequest object's responseXML property 
 
		    // if the ajaxForm method was passed an Options Object with the dataType 
		    // property set to 'json' then the first argument to the success callback 
		    // is the json data object returned by the server 
 
		    alert( 'Thanks for your submission.'); 
		    jQuery('#resoureceListFormWrapper').hide();
		} 
              </script>\n";

	return '		<!--empty q--><b><span id="resourceToggler" style="color: blue; text-decoration: underline;">Resource Submission Form</span></b>'."\n".'<div id="output1"></div><br><br clear="all" />'.
	            '		<div id="resoureceListFormWrapper">'.
	              '		<form action="" method="post" id="info">
				 <!--  Resource Submission Form --> '."\n".$ok.'
                                  <div id="sbys">
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_name">Resource Name</label>
					  <input type="text" id="rl2_url" name="rl2_name">
				   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_url">Resource URL</label>
					  <input type="text" id="rl2_url" name="rl2_url">
				   </div>
				   </div>
				   
                                  <div id="sbys">
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_email">Resource Email</label>
					  <input type="text" id="rl2_email" name="rl2_email">
				   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_addr1">Resource Address 1</label>
					  <input type="text" id="rl2_addr1" name="rl2_addr1">
				   </div>
				   </div>


                                  <div id="sbys">
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_addr2">Resource Address 2</label>
					  <input type="text" id="rl2_addr2" name="rl2_addr2">
				   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_city">Resource City</label>
					  <input type="text" id="rl2_city" name="rl2_city">
				   </div>
				   </div>


                                  <div id="sbys">
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_state">Resource State</label>
					  <input type="text" id="rl2_state" name="rl2_state">
				   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_zip">Resource Zip</label>
					  <input type="text" id="rl2_zip" name="rl2_zip">
				   </div>
				   </div>


                                  <div id="sbys">
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_country">Resource Country</label>
					  <input type="text" id="rl2_country" name="rl2_country">
				   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_telephone">Resource Telephone</label>
					  <input type="text" id="rl2_telephone" name="rl2_telephone">
				   </div>
				   </div>


                                  <div id="sbys">
				   <div id="rl2-list-wrap"  class="slider">
                                        <label for="rl2_description">Resource Description</label>
                                        <textarea name="rl2_description" rows="3" id="rl2_description"></textarea>
                                   </div>
				   <div id="rl2-list-wrap" class="slider">
					  <label for="rl2_submitter">Your Name</label>
					  <input type="text" id="rl2_submitter" name="rl2_submitter">
				   </div>
				   </div>
                                  <div id="sbys">

				   <input type="hidden" name="newprop" value="'.$post->ID.'" />
				   <input type="submit" name="submit" id="btn"  value="submit">
				   <input type="hidden" name="action" value="rl2_ajax_post_create">
				   <input type="hidden" name="_wpnonce" value="'.wp_create_nonce('my-nonce').'">
				   </div>

				</form></div>'."\n";

}

add_action('wp_print_styles', 'add_my_stylesheet');

function add_my_stylesheet() {
    $myStyleUrl = WP_PLUGIN_URL . '/rl2/rl2.css';
    $myStyleFile = WP_PLUGIN_DIR . '/rl2/rl2.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('myStyleSheets', $myStyleUrl);
        wp_enqueue_style( 'myStyleSheets');
    }
}

add_filter('wp_head','add_resource_list_js');

function add_resource_list_js() {
        $i_options = get_option("resource_list_all_options");
	
      if ($i_options['resource_list_form'] == "yes") {
           echo '<!-- line: '.__LINE__.' --><script type="text/javascript" src="http://www.google.com/jsapi"></script>'.
                    '<script type="text/javascript">google.load("jquery", "1");</script>';
       //         echo '<script type="text/javascript" src="'.WP_PLUGIN_URL .'/rl2/rl2.js"></script>';
       }
}
                                

?>
