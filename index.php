<?php
/*
Plugin Name: Trackbase
Plugin URI: 
Description: Track all your link activity
Version: 1.0
Author: Jaime Rossello
Author URI: https://jaimerossello.com
License: GPLv2 or later
*/

function trackbase_db_install() {
	global $wpdb;
	global $trackbase_db_version;

	$table_name = $wpdb->prefix . 'trackbase';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`page_id` varchar(200) DEFAULT NULL,
			`type` varchar(200) DEFAULT NULL,
			`date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`url` varchar(200) DEFAULT NULL,
			`isGooglebot` varchar(200) DEFAULT NULL,
			`refered_url` varchar(200) DEFAULT NULL,
			`click_url` varchar(200) DEFAULT NULL,
			`click_class` varchar(200) DEFAULT NULL,
			`click_id` varchar(200) DEFAULT NULL,
			`ip` varchar(200) DEFAULT NULL,
			`location` varchar(200) DEFAULT NULL,
			`user_agent` varchar(200) DEFAULT NULL,
			`clicks` varchar(200) DEFAULT NULL,
			`click_x` varchar(200) DEFAULT NULL,
			`click_y` varchar(200) DEFAULT NULL,
			`scroll` varchar(200) DEFAULT NULL,
			`new_visit` varchar(200) DEFAULT NULL,
			`time_on_page` varchar(200) DEFAULT NULL,
			`time_on_site` varchar(200) DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `url` (`url`(191))
	) $charset_collate;";
	echo'sqlerror';

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function trackbase_db_install_data() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'trackbase';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			//'title' => current_time( 'mysql' ), 
			'url' => 'Test',
		)
	);
}
register_activation_hook( __FILE__, 'trackbase_db_install' );
register_activation_hook( __FILE__, 'trackbase_db_install_data' );

/* add_action( 'admin_init', 'wpdocs_plugin_admin_init' );
function wpdocs_plugin_admin_init() {
    wp_register_style( 'trackbase', plugins_url( 'css/style2.css', __FILE__ ) );
	wp_enqueue_style( 'trackbase' );
} */
 

add_action( 'admin_enqueue_scripts', 'custom_wp_toolbar_css_admin' );
function custom_wp_toolbar_css_admin() {
	
	wp_enqueue_script( 'trackbase', plugins_url( 'assets/js/search.js', __FILE__ ), array('jquery'), '1.0', false );
	
    wp_register_style( 'trackbase', plugins_url( 'assets/css/style.css', __FILE__ ) );
	wp_enqueue_style( 'trackbase' );
}

add_action( 'wp_enqueue_scripts', 'ajax_load_trackbase_scripts' );
function ajax_load_trackbase_scripts() {

	//wp_enqueue_style( 'trackbase', plugins_url( '/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'trackbase', plugins_url( 'assets/js/track.js', __FILE__ ), array('jquery'), '1.0', false );
	wp_localize_script( 'trackbase', 'trackbase', array(
		'ajax_url' => admin_url( 'admin-ajax.php' )
	));
}


//require dirname(__FILE__).'/settings.php';
//include('modules/hooks.php');

/* Setting up Dashboard Page */
//require_once 'admin/settings.php';
add_action('admin_menu', 'trackbase_dash_menu');
function trackbase_dash_menu(){
    add_menu_page( 'Trackbase', 'trackbase', 'manage_options', 'trackbase', 'trackbase_init', plugins_url( 'assets/img/track.fireball.png', __FILE__ ) );
}

/* Dump data on plugin settings page */
function trackbase_init(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'trackbase';
	
	?>
		<span class="trackbase-icon"></span><h1>trackbase</h1>
	

		<input type="text" id="userInput" onkeyup="process(this)" placeholder="Search.." />
		<div class="trackbase-dashboard-item fullwidth"><div id="underInput"></div></div>

	
	<div class="trackbase-dashboard-item fullwidth">

	</div>

<!-- Views -->	
	
	<div class="trackbase-dashboard-item">
		<div><h2>Views</h2></div>
	<hr>
		<ul>
			<li><div><h2>TODAY</h2>
			<br>
			<?php
			$results3 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'view' AND date >= CURDATE() - INTERVAL 0 DAY");
			echo $results3[0]->todaycount;
			?>
			</div></li><li><div><h2>LAST WEEK</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'view' AND date >= CURDATE() - INTERVAL 7 DAY");
			echo $results4[0]->todaycount;
			?>
			</div></li><li><div><h2>LAST 30 DAYS</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'view' AND date >= CURDATE() - INTERVAL 30 DAY");
			echo $results4[0]->todaycount;
			?>
			</div></li><li><div><h2>ALL TIME</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'view'");
			echo $results4[0]->todaycount;
			?>
			</div></li>
		</ul>
	<hr>
	
		<div><h2>Top Visited Pages</h2></div>

		<div>
		<?php
		$results = $wpdb->get_results( "SELECT click_url, COUNT(*) as todalcount FROM $table_name WHERE type = 'view' GROUP BY url ORDER BY COUNT(*) DESC LIMIT 10" );
	
		$i = 1;
		if (count($results) > 0) {
			$display_row = null;
			foreach ($results as $res) {
				echo '<span class="pos">'. $i++ .'.</span> '. $res->todalcount .' views - '. $res->url.'<br>';
			}
		}
		
		?>
		</div>
		
	<hr>
		<div><h2>Recent Visited Pages</h2></div>

		<div>
		<?php
		$results2 = $wpdb->get_results( "SELECT * FROM $table_name WHERE type = 'view'" );

		if (count($results2) > 0) {
			$display_row = null;
			foreach ($results2 as $res2) {
				 echo $res2->url.'<br>';
			}
		}
		?>
		</div>
	</div>

<!-- Clicks -->	
	
	<div class="trackbase-dashboard-item">
	<div><h2>Clicks</h2></div>
	<hr>
		<ul>
			<li><div><h2>TODAY</h2>
			<br>
			<?php
			$results3 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'click' AND date >= CURDATE() - INTERVAL 0 DAY");
			echo $results3[0]->todaycount;
			?>
			</div></li><li><div><h2>LAST WEEK</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'click' AND date >= CURDATE() - INTERVAL 7 DAY");
			echo $results4[0]->todaycount;
			?>
			</div></li><li><div><h2>LAST 30 DAYS</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'click' AND date >= CURDATE() - INTERVAL 30 DAY");
			echo $results4[0]->todaycount;
			?>
			</div></li><li><div><h2>ALL TIME</h2>
			<br>
			<?php
			$results4 = $wpdb->get_results( "SELECT COUNT(*) as todaycount FROM $table_name WHERE type = 'click'");
			echo $results4[0]->todaycount;
			?>
			</div></li>
		</ul>
	<hr>
	
		<div><h2>Top Clicked links</h2></div>

		<div>
		<?php
		$results = $wpdb->get_results( "SELECT click_url, COUNT(*) as todalcount FROM $table_name WHERE type = 'click' GROUP BY click_url ORDER BY COUNT(*) DESC LIMIT 10" );
	
		$i = 1;
		if (count($results) > 0) {
			$display_row = null;
			foreach ($results as $res) {
				echo '<span class="pos">'. $i++ .'.</span> '. $res->todalcount .' clicks - '. $res->click_url.'<br>';
			}
		}
		
		?>
		</div>
		
	<hr>
		<div><h2>Recent Clicked links</h2></div>
		
		<div>
		<?php
		$results2 = $wpdb->get_results( "SELECT * FROM $table_name WHERE type = 'click'" );

		if (count($results2) > 0) {
			$display_row = null;
			foreach ($results2 as $res2) {
				 echo $res2->click_url.'<br>';
			}
		}
		?>
		</div>
	</div>
<?php
}


/* AJAX call funtion to load more posts */
add_action( 'wp_ajax_nopriv_post_trackbase', 'post_trackbase' );
add_action( 'wp_ajax_post_trackbase', 'post_trackbase' );
function post_trackbase() {
	$page_id = get_the_ID();
	$type = $_POST['type'];
	$click_class = $_POST['click_class'];
	$click_id = $_POST['click_id'];
	$click_x = $_POST['click_x'];
	$click_y = $_POST['click_y'];
	$clicks = $_POST['clicks'];
	$click_url = $_POST['click_url'];
	$url = $_POST['url'];
	
	$scroll_evt = $_POST['scroll_evt'];
	$time_on_page = $_POST['time_on_page'];
	$time_on_site = $_POST['time_on_site'];
	
	$datet = date('Y-m-d H:i:s');
	$ip = $_SERVER['REMOTE_ADDR'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	$isGooglebot = 0;
	if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), "googlebot")){
		$isGooglebot = 1;
	}
	$refered_url = $_SERVER['HTTP_REFERER'];
	
	$browser = get_browser(null, true);
	
	$location = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 

	global $wpdb;
	
	$table_name = $wpdb->prefix . 'trackbase';
	
	$wpdb->insert( 
		$table_name, 
		array(
			'page_id' => $page_id,
			'type' => $type,
			'date' => $datet,
			'ip' => $ip,
			'location' => $location->country,
			'user_agent' => $user_agent,
			'isGooglebot' => $isGooglebot,
			'url' => $url,
			'refered_url' => $refered_url,
			'click_url' => $click_url,
			'click_class' => $click_class,
			'click_id' => $click_id,
			'clicks' => $clicks,
			'click_x' => $click_x,
			'click_y' => $click_y,
			'scroll' => $scroll_evt,
			'time_on_page' => $time_on_page,
			'time_on_site' => $time_on_site,
		) 
	);
	
	$wpdb->print_error();
	
	die();
		
	} else {
		wp_redirect( get_permalink( $_REQUEST['link'] ) );
		exit();
	}
}


/* AJAX call funtion to load more posts */
add_action( 'wp_ajax_nopriv_post_nquery', 'post_nquery' );
add_action( 'wp_ajax_post_nquery', 'post_nquery' );
function post_nquery() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'trackbase';
	
	$nquery = $_POST['nquery'];
	
	$results = $wpdb->get_results( "SELECT * FROM $table_name" );
	
		if (count($results) > 0) {
			$display_row = null;
			foreach ($results as $res) {
				if (strpos($res->click_url, $nquery) !== false) {
					echo $res->click_url.'<br>';
				}
			}
		}
	
	die();
}

/* Heat Map*/
function heatmap_update_adminbar($wp_adminbar) {

  $wp_adminbar->add_node([
    'id' => 'trackbase_heatmap',
    'title' => '<span class="trackbase-icon"></span><span id="heatmap">Heatmap</span>',
	'href' => '/wp-admin/admin.php?page=trackbase',
    'meta' => [
      'target' => '_self'
    ]
  ]);

}
add_action('admin_bar_menu', 'heatmap_update_adminbar', 999);

function heatmap(){
	if ( is_admin_bar_showing() ) {

	//build heatmap on/off
	//dump heat points
		
	}
}
add_action( 'init', 'heatmap' );