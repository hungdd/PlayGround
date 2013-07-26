<?php

require_once dirname(__FILE__) . '/paypal/class-paypal.php';

require_once dirname(__FILE__) . '/rating.php';

/**
 * Directory Find in radius function
 **/
 
function getDistance ($radiusInKm, $cenLat, $cenLng, $lat, $lng) {
    $radiusInKm = intval($radiusInKm);
	$cenLat = floatval($cenLat);
	$cenLng = floatval($cenLng);
	$lat = floatval($lat);
	$lng = floatval($lng);
    $ggmap = 'http://maps.googleapis.com/maps/api/distancematrix/json?sensor=false&';
    $ggmap .= "origins=$cenLat,$cenLng&destinations=$lat,$lng";
    $data = file_get_contents($ggmap);
    $data = json_decode($data);
    if (!empty($data) && $data->status == 'OK') {
        $distance = intval($data->rows[0]->elements[0]->distance->value);
        if ($distance <= ($radiusInKm*1000)) {
            return $distance;
        } else {
            return false;
        }
    }
    return false;
}

function isPointInRadius($radiusInKm, $cenLat, $cenLng, $lat, $lng)
{
	$radiusInKm = intval($radiusInKm);
	$cenLat = floatval($cenLat);
	$cenLng = floatval($cenLng);
	$lat = floatval($lat);
	$lng = floatval($lng);
	$distance = ( 6371 * acos( cos( deg2rad($cenLat) ) * cos( deg2rad( $lat ) ) * cos( deg2rad( $lng ) - deg2rad($cenLng) ) + sin( deg2rad($cenLat) ) * sin( deg2rad( $lat ) ) ) );
	if($distance <= $radiusInKm){
		return $distance;
	} else {
		return false;
	}
}

function parseMapOptions($adminOptions)
{
	$options = array();

	$options['draggable'] = (isset($adminOptions->draggable)) ? "true" : "false";
	$options['mapTypeControl'] = (isset($adminOptions->mapTypeControl)) ? "true" : "false";
	$options['mapTypeId'] = 'google.maps.MapTypeId.'.$adminOptions->mapTypeId;
	$options['scrollwheel'] = (isset($adminOptions->scrollwheel)) ? "true" : "false";
	$options['panControl'] = (isset($adminOptions->panControl)) ? "true" : "false";
	$options['rotateControl'] = (isset($adminOptions->rotateControl)) ? "true" : "false";
	$options['scaleControl'] = (isset($adminOptions->scaleControl)) ? "true" : "false";
	$options['streetViewControl'] = (isset($adminOptions->streetViewControl)) ? "true" : "false";
	$options['zoomControl'] = (isset($adminOptions->zoomControl)) ? "true" : "false";

	return $options;
}

function custom_pre_get_posts($query) {
	// only main query
	if($query->is_main_query() && !$query->is_admin){
		if(isset($_GET['dir-search']) && isset($_GET['s']) && isset($_GET['categories']) && isset($_GET['locations'])){

			$taxquery = array();
			$taxquery['relation'] = 'AND';

			if(isset($_GET['categories']) && !empty($_GET['categories'])){
				$taxquery[] = array(
					'taxonomy' => 'ait-dir-item-category',
					'field' => 'id',
					'terms' => array($_GET['categories']),
					'include_children' => true
				);
			}
			if(isset($_GET['locations']) && !empty($_GET['locations'])){
				$taxquery[] = array(
					'taxonomy' => 'ait-dir-item-location',
					'field' => 'id',
					'terms' => array($_GET['locations']),
					'include_children' => true
				);
			}
			$query->set('tax_query',$taxquery);

			$num = (isset($GLOBALS['aitThemeOptions']->search->searchItemsPerPage)) ? $GLOBALS['aitThemeOptions']->search->searchItemsPerPage : 10;
			$query->set('posts_per_page',$num);

			// filter only items by geolocation
			if(isset($_GET['geo'])){
				$category = $_GET['categories'];
				$location = $_GET['locations'];
				$params = array(
					'post_type'			=> 'ait-dir-item',
					'nopaging'			=>	true,
					'post_status'		=> 'publish'
				);
				$taxquery = array();
				$taxquery['relation'] = 'AND';
				if($category != 0){
					$taxquery[] = array(
						'taxonomy' => 'ait-dir-item-category',
						'field' => 'id',
						'terms' => $category,
						'include_children' => true
					);
				}
				if($location != 0){
					$taxquery[] = array(
						'taxonomy' => 'ait-dir-item-location',
						'field' => 'id',
						'terms' => $location,
						'include_children' => true
					);
				}
				if($category != 0 || $location != 0){
					$params['tax_query'] = $taxquery;
				}
				if($query->get('s') != ''){
					$params['s'] = $query->get('s');
				}
				$itemsQuery = new WP_Query();
				$items = $itemsQuery->query($params);
				$notIn = array();
				// add item details
				foreach ($items as $key => $item) {
					// options
					$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
					// filter radius
					if(!isPointInRadius(intval($_GET['geo-radius']), floatval($_GET['geo-lat']), floatval($_GET['geo-lng']), $item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude'])){
						$notIn[] = $item->ID;
					}
				}
				// filter
				$query->set('post__not_in',$notIn);
			}
		}
		
		if($query->is_tax && isset($query->query_vars["dir-item-category"])){
			$num = (isset($GLOBALS['aitThemeOptions']->directory->categoryItemsPerPage)) ? $GLOBALS['aitThemeOptions']->directory->categoryItemsPerPage : 10;
			$query->set('posts_per_page',$num);
		}
	}
	return $query;
}
add_filter('pre_get_posts','custom_pre_get_posts');


$aitCategoryMeta = array();

function getCategoryMeta( $what, $categoryID ){
	global $aitCategoryMeta, $wpdb;

	// get cache = all values
	if(empty($aitCategoryMeta)){
		$results = $wpdb->get_results( "SELECT * FROM ".$wpdb->options." WHERE option_name LIKE 'ait\_dir\_item\_category\_%'" );
		foreach ($results as $r) {
			preg_match('!\d+!', $r->option_name, $matches);
			$id = (int)$matches[0];
			if(!isset($aitCategoryMeta[$id])) {
				$aitCategoryMeta[$id] = array();
			}
			if(strpos($r->option_name,'icon') !== false){
				$aitCategoryMeta[$id]['icon'] = $r->option_value;
			} else if(strpos($r->option_name,'marker') !== false){
				$aitCategoryMeta[$id]['marker'] = $r->option_value;
			} else {
				$aitCategoryMeta[$id]['excerpt'] = $r->option_value;
			}

		}
	}

	switch ($what) {
		case 'icon':
			$anc = get_ancestors( $categoryID, 'ait-dir-item-category' );
			$icon = isset($aitCategoryMeta[$categoryID]) ? $aitCategoryMeta[$categoryID]['icon'] : '';
			if(empty($icon)){
				foreach ($anc as $value) {
					if(!empty($aitCategoryMeta[$value]['icon'])){
						$icon = $aitCategoryMeta[$value]['icon'];
						break;
					}
				}
				if(empty($icon)){
					$icon = $GLOBALS['aitThemeOptions']->directory->defaultCategoryIcon;
				}
			}
			return $icon;
		case 'marker':
			$anc = get_ancestors( $categoryID, 'ait-dir-item-category' );
			$marker = isset($aitCategoryMeta[$categoryID]) ? $aitCategoryMeta[$categoryID]['marker'] : '';
			if(empty($marker)){
				foreach ($anc as $value) {
					if(!empty($aitCategoryMeta[$value]['marker'])){
						$marker = $aitCategoryMeta[$value]['marker'];
						break;
					}
				}
				if(empty($marker)){
					$marker = $GLOBALS['aitThemeOptions']->directoryMap->defaultMapMarkerImage;
				}
			}
			return $marker;
		case 'excerpt':
			$excerpt = '';
			if(isset($aitCategoryMeta[$categoryID]['excerpt'])){
				$excerpt = $aitCategoryMeta[$categoryID]['excerpt'];
			}
			return $excerpt;
		default:
			break;
	}
}

/**
 * Get package class for item
 * @param  int $id author id
 * @return string     class
 */
function getItemPackageClass( $authorId )
{
	$user = new WP_User( $authorId );
	if(isset($user->roles[0])){
		return $user->roles[0];
	} else {
		return null;
	}
}

// Get manually excerpt or automatic excerpt for wp post
function aitGetPostExcerpt($excerpt, $content){
	$newExcerpt = '';
	$trimExcerpt = trim($excerpt);
	if(empty($trimExcerpt)){
		$exc = substr(strip_tags($content), 0, 300);
        $pos = strrpos($exc, " ");
	    $newExcerpt = substr($exc, 0, ($pos ? $pos : -1)) . "...";
	} else {
		$newExcerpt = $excerpt;
	}
	return $newExcerpt;
}

/**
 * get items from DB
 * @param  integer $category category ID
 * @param  integer $location location ID
 * @param  string  $search   search keyword
 * @param  array   $radius   (radius in km, lat, lon)
 * @return array             items
 */
function getItems($category = 0, $location = 0, $search = '', $radius = array()){
	$params = array(
		'post_type'			=> 'ait-dir-item',
		'nopaging'			=>	true,
		'post_status'		=> 'publish'
	);

	$taxquery = array();
	$taxquery['relation'] = 'AND';
	if($category != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-category',
            'field' => 'id',
            'terms' => $category,
            'include_children' => true
		);
	}
	if($location != 0){
		$taxquery[] = array(
			'taxonomy' => 'ait-dir-item-location',
            'field' => 'id',
            'terms' => $location,
            'include_children' => true
		);
	}
	if($category != 0 || $location != 0){
		$params['tax_query'] = $taxquery;
	}

	if($search != ''){
		$params['s'] = $search;
	}

	$itemsQuery = new WP_Query();
	$items = $itemsQuery->query($params);

	// add item details
	foreach ($items as $key => $item) {
		// options
		$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
		// filter radius
		if(!empty($radius) && !isPointInRadius($radius[0], $radius[1], $radius[2], $item->optionsDir['gpsLatitude'], $item->optionsDir['gpsLongitude'])){
			unset($items[$key]);
		} else {
			// link
			$item->link = get_permalink($item->ID);
			// thumbnail
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($item->ID) );
			if($image !== false){
				$item->thumbnailDir = $image[0];
			} else {
				$item->thumbnailDir = $GLOBALS['aitThemeOptions']->directory->defaultItemImage;
			}
			// marker
			$terms = wp_get_post_terms($item->ID, 'ait-dir-item-category');
			$termMarker = $GLOBALS['aitThemeOptions']->directoryMap->defaultMapMarkerImage;
			if(isset($terms[0])){
				$termMarker = getCategoryMeta("marker", intval($terms[0]->term_id));
			}
			$item->marker = $termMarker;
			// excerpt
			$item->excerptDir = aitGetPostExcerpt($item->post_excerpt,$item->post_content);
			// package class
			$item->packageClass = getItemPackageClass($item->post_author);
			// rating
			$item->rating = aitCalculateMeanForPost($item->ID);
		}
	}

	return $items;

}

// allow ajax
add_action( 'wp_ajax_get_items', 'getItemsAjax' );
add_action( 'wp_ajax_nopriv_get_items', 'getItemsAjax' );

function getItemsAjax()
{
	global $aitThemeOptions;

	// all items
	if(isset($_POST['radius'])){
		$items = getItems($_POST['category'],$_POST['location'],$_POST['search'],$_POST['radius']);
	} else {
		$items = getItems($_POST['category'],$_POST['location'],$_POST['search']);
	}

	foreach ($items as $item) {
		$item->timthumbUrl = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 120, 'h' => 160), "", "&amp;") : '';
		$item->timthumbUrlContent = (isset($item->thumbnailDir)) ? TIMTHUMB_URL . "?" . http_build_query(array('src' => $item->thumbnailDir, 'w' => 100, 'h' => 100), "", "&amp;") : '';
	}

	$output = json_encode($items);
 	// response output
    header( "Content-Type: application/json" );
    echo $output;
    exit;
}

/**
 * Users Submitting Functionality
 */

function add_theme_caps() {

	global $aitThemeOptions;

	// directory items capability
	$capability_type = 'ait-dir-item';
	$capabilitiesAdmin = array(
		"edit_{$capability_type}" => true,
		"read_{$capability_type}" => true,
		"delete_{$capability_type}" => true,
		"edit_{$capability_type}s" => true,
		"edit_others_{$capability_type}s" => true,
		"publish_{$capability_type}s" => true,
		"read_private_{$capability_type}s" => true,
		"delete_{$capability_type}s" => true,
		"delete_private_{$capability_type}s" => true,
		"delete_published_{$capability_type}s" => true,
		"delete_others_{$capability_type}s" => true,
		"edit_private_{$capability_type}s" => true,
		"edit_published_{$capability_type}s" => true,
		"assign_dir_category" => true,
		"assign_dir_location" => true
	);

	// set admin capability
	$adminRole = get_role( 'administrator' );
	foreach ($capabilitiesAdmin as $key => $value) {
		$adminRole->add_cap( $key );
	}

	$subscriberRole = get_role( 'subscriber' );
	$subscriberRole->add_cap( 'directory_account_update' );

	// update user roles from admin
	if(isset($_POST['action']) && $_POST['action'] == 'update' && strpos($_SERVER['PHP_SELF'],'options.php') !== false){
		$optionName = $_POST['option_page'];
		if(isset($_POST[$optionName]) && isset($_POST[$optionName]['members'])){

			$prefixName = '';
			$options = $_POST[$optionName]['members'];
			$roles = $GLOBALS['wp_roles']->role_names;

			$capabilitiesDirectory = array(
				"edit_{$capability_type}s" => true,
				"read_private_{$capability_type}s" => false,
				"edit_published_{$capability_type}s" => true,
				"delete_{$capability_type}s" => true,
				"delete_published_{$capability_type}s" => true,
				"assign_dir_category" => true,
				"assign_dir_location" => true,
				"read" => true,
				"upload_files" => true,
				"directory_account_update" => true
			);

			if(isset($options['role1Enable'])){
				// rename or create
				//if(!(array_key_exists('directory_1', $roles) && ($roles['directory_1'] == $options->role1Name))){
					remove_role( 'directory_1' );
					$caps = $capabilitiesDirectory;
					if(!isset($options['role1Approve'])){
						$caps["publish_{$capability_type}s"] = true;
					}
					add_role( 'directory_1', $prefixName . $options['role1Name'], $caps);
				//}
			} else {
				remove_role( 'directory_1' );
			}

			if(isset($options['role2Enable'])){
				// rename or create
				//if(!(array_key_exists('directory_2', $roles) && ($roles['directory_2'] == $options->role2Name))){
					remove_role( 'directory_2' );
					$caps = $capabilitiesDirectory;
					if(!isset($options['role2Approve'])){
						$caps["publish_{$capability_type}s"] = true;
					}
					add_role( 'directory_2', $prefixName . $options['role2Name'], $caps);
				//}
			} else {
				remove_role( 'directory_2' );
			}

			if(isset($options['role3Enable'])){
				// rename or create
				//if(!(array_key_exists('directory_3', $roles) && ($roles['directory_3'] == $options->role3Name))){
					remove_role( 'directory_3' );
					$caps = $capabilitiesDirectory;
					if(!isset($options['role3Approve'])){
						$caps["publish_{$capability_type}s"] = true;
					}
					add_role( 'directory_3', $prefixName . $options['role3Name'], $caps);
				//}
			} else {
				remove_role( 'directory_3' );
			}

			if(isset($options['role4Enable'])){
				// rename or create
				//if(!(array_key_exists('directory_4', $roles) && ($roles['directory_4'] == $options->role4Name))){
					remove_role( 'directory_4' );
					$caps = $capabilitiesDirectory;
					if(!isset($options['role4Approve'])){
						$caps["publish_{$capability_type}s"] = true;
					}
					add_role( 'directory_4', $prefixName . $options['role4Name'], $caps);
				//}
			} else {
				remove_role( 'directory_4' );
			}

			if(isset($options['role5Enable'])){
				// rename or create
				//if(!(array_key_exists('directory_5', $roles) && ($roles['directory_5'] == $options->role5Name))){
					remove_role( 'directory_5' );
					$caps = $capabilitiesDirectory;
					if(!isset($options['role5Approve'])){
						$caps["publish_{$capability_type}s"] = true;
					}
					add_role( 'directory_5', $prefixName . $options['role5Name'], $caps);
				//}
			} else {
				remove_role( 'directory_5' );
			}

		}
	}

	// check number of posts for directory users
	$usrRoles = $GLOBALS['current_user']->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		if((strpos($_SERVER['PHP_SELF'],'post-new.php') !== false) && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-dir-item')){

			$params = array(
				'post_type'			=> 'ait-dir-item',
				'nopaging'			=> true,
				'author'			=> $GLOBALS['current_user']->ID
			);
			$itemsQuery = new WP_Query();
			$items = $itemsQuery->query($params);

			$backUrl = admin_url('edit.php?post_type=ait-dir-item&dir_notification=1');

			if(in_array('directory_1', $usrRoles)){
				if(count($items) >= intval($aitThemeOptions->members->role1Items)){
					header('Location: ' . $backUrl);
				}
			} elseif (in_array('directory_2', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role2Items)){
					header('Location: ' . $backUrl);
				}
			} elseif (in_array('directory_3', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role3Items)){
					header('Location: ' . $backUrl);
				}
			} elseif (in_array('directory_4', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role4Items)){
					header('Location: ' . $backUrl);
				}
			} elseif (in_array('directory_5', $usrRoles)) {
				if(count($items) >= intval($aitThemeOptions->members->role5Items)){
					header('Location: ' . $backUrl);
				}
			}
		}
	}
}
add_action( 'admin_init', 'add_theme_caps');

/**
 * Don't show others items for directory roles
 */
function itemsTableDontShowOtherItems($query) {
	$usrRoles = $GLOBALS['current_user']->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		if((strpos($_SERVER['PHP_SELF'],'edit.php') !== false) && isset($_GET['post_type']) && ($_GET['post_type'] == 'ait-dir-item')){
			$query->set('author',$GLOBALS['current_user']->ID);
		}
	}
	return $query;
}
add_filter('pre_get_posts','itemsTableDontShowOtherItems');

function itemsTableViews($views)
{
	$usrRoles = $GLOBALS['current_user']->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		global $wpdb;

		$user = wp_get_current_user();
		$type = 'ait-dir-item';

		//$cache_key = $type;

		$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d";
		$query .= ' GROUP BY post_status';

		//$count = wp_cache_get($cache_key, 'counts');
		//if ( false !== $count )
		//	return $count;

		$count = $wpdb->get_results( $wpdb->prepare( $query, $type, $user->ID ), ARRAY_A );

		$stats = array();
		foreach ( get_post_stati() as $state )
			$stats[$state] = 0;

		foreach ( (array) $count as $row )
			$stats[$row['post_status']] = $row['num_posts'];

		$stats = (object) $stats;
		//wp_cache_set($cache_key, $stats, 'counts');

		global $locked_post_status, $avail_post_stati;

		$post_type = $type;

		if ( !empty($locked_post_status) )
			return array();

		$status_links = array();
		$num_posts = $stats;
		$class = '';
		$allposts = '';

		$current_user_id = get_current_user_id();

		$total_posts = array_sum( (array) $num_posts );

		// Subtract post types that are not included in the admin all list.
		foreach ( get_post_stati( array('show_in_admin_all_list' => false) ) as $state )
			$total_posts -= $num_posts->$state;

		$class = empty( $class ) && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['show_sticky'] ) ? ' class="current"' : '';
		$status_links['all'] = "<a href='edit.php?post_type=$post_type{$allposts}'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_posts, 'posts' , 'ait'), number_format_i18n( $total_posts ) ) . '</a>';

		foreach ( get_post_stati(array('show_in_admin_status_list' => true), 'objects') as $status ) {
			$class = '';

			$status_name = $status->name;

			if ( !in_array( $status_name, $avail_post_stati ) )
				continue;

			if ( empty( $num_posts->$status_name ) )
				continue;

			if ( isset($_REQUEST['post_status']) && $status_name == $_REQUEST['post_status'] )
				$class = ' class="current"';

			$status_links[$status_name] = "<a href='edit.php?post_status=$status_name&amp;post_type=$post_type'$class>" . sprintf( translate_nooped_plural( $status->label_count, $num_posts->$status_name ), number_format_i18n( $num_posts->$status_name ) ) . '</a>';
		}
		return $status_links;
	} else {
		return $views;
	}
}
add_filter('views_edit-ait-dir-item','itemsTableViews');

/**
 * Show error notice if max items was exceeded
 */
function adminNotices() {
	global $registerMessages;

	if(isset($_GET['dir_notification']) && $_GET['dir_notification'] == '1'){
		echo '<div class="error"><p>'.__('Sorry but you have maximum items for your account type. Upgrade your account to insert new items!','ait').'</p></div>';
	}
	if(isset($registerMessages)){
		echo '<div class="updated"><p>'.$registerMessages.'</p></div>';
	}
}
add_filter( 'admin_notices', 'adminNotices' );

/**
 * Generate upgrade account admin
 */
function upgradeDirectoryAccount() {
	add_users_page(__('Directory Account','ait'), __('Directory Account','ait'), 'directory_account_update', 'dir-account', 'renderUpgradeDirectoryAccount');
}
function renderUpgradeDirectoryAccount() {

	global $current_user;

	$usrRoles = $current_user->roles;

	echo '<div class="wrap">';
	echo '<div id="icon-users" class="icon32"><br></div>';
	echo '<h2>'.__('Directory Account','ait').'</h2>';
	$firstRole = array_shift(array_values($usrRoles));
	if($firstRole){
		if (strpos($firstRole,'directory_') !== false) {
			$roleName = $GLOBALS['wp_roles']->role_names[$firstRole];
			$roleNumber = intval(substr($firstRole, 10));
			$roleCodePrice = 'role'.$roleNumber.'Price';
		} else {
			$roleName = __('None','ait');
			$roleNumber = 0;
			$roleCodePrice = 'none';
		}
		?>
		<form method="post" action="<?php echo admin_url('/?dir-register=upgrade'); ?>" class="wp-user-form">
		<input type="hidden" name="user_id" value="<?php echo $current_user->ID; ?>">
		<table class="form-table">
		<tbody>
			<tr>
				<th><label for="user_account_type"><?php echo __('Account type','ait'); ?></label></th>
				<td><input type="text" name="user_account_type" id="user_account_type" value="<?php echo $roleName; ?>" disabled="disabled" class="regular-text"></td>
			</tr>
			<tr>
				<th><label for="user_account_expiration"><?php echo __('Days left before expiration','ait'); ?></label></th>
				<td><input type="text" name="user_account_expiration" id="user_account_expiration" value="<?php echo getDaysLeft(); ?>" disabled="disabled" class="regular-text"></td>
			</tr>
			<tr>
				<input type="hidden" name="directory-role-current-price" value="<?php echo $roleCodePrice; ?>">
				<?php
				global $aitThemeOptions;
				$output = '<th><label for="directory-role">'.__('Upgrade account','ait').'</label></th><td><select name="directory-role" id="ait_dir_user_account_update">';
				$currency = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
				$roleNumber++;
				$upCount = 0;
				for ($i=$roleNumber; $i <= 5; $i++) {
					$roleEnable = 'role'.$i.'Enable';
					$roleName = 'role'.$i.'Name';
					$rolePrice = 'role'.$i.'Price';
					$free = (trim($aitThemeOptions->members->$rolePrice) == '0') ? true : false;
					if(isset($aitThemeOptions->members->$roleEnable)){
						$output.= '<option value="directory_'.$i.'"';
						if($free) { $output .= ' class="free"'; }
						$output .= '>'.$aitThemeOptions->members->$roleName;
						if(!$free) {
							if($roleCodePrice == 'none'){
								$upgradePrice = trim($aitThemeOptions->members->$rolePrice);
							} else {
								$upgradePrice = floatval(trim($aitThemeOptions->members->$rolePrice)) - floatval(trim($aitThemeOptions->members->$roleCodePrice));
							}
							$output .= ' ('.$upgradePrice.' '.$currency.')';
						} else {
							$output .= ' ('.__('Free','ait').')';
						}
						$output .= '</option>';
						$upCount++;
					}
				}
				$output .= '</select><input type="submit" name="user-submit" value="'.__('Upgrade!', 'ait').'" class="user-submit" /></td>';
				if($upCount > 0) { echo $output; }
				?>
			</tr>
		</tbody>
		</table>
		</form>
		</script>
		<?php
	} else {
	}
	echo '</div>';
}
add_action('admin_menu', 'upgradeDirectoryAccount');


/**
 * Handles registering a new user.
 *
 * @param string $user_login User's username for logging in
 * @param string $user_email User's email address to send password and add
 * @return int|WP_Error Either user's ID or error on failure.
 */
function dir_register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	// registrations disabled
	if (!get_option( 'users_can_register' )){
		$errors->add( 'registrations_disabled',__('ERROR: User registration is currently not allowed.', 'ait') );
		return $errors;
	}

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( 'ERROR: Please enter a username.' , 'ait') );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( 'ERROR: This username is invalid because it uses illegal characters. Please enter a valid username.' , 'ait') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( 'ERROR: This username is already registered. Please choose another one.', 'ait' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( 'ERROR: Please type your e-mail address.', 'ait' ) );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( 'ERROR: The email address isn&#8217;t correct.', 'ait' ) );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( 'ERROR: This email is already registered, please choose another one.', 'ait' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false);
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( 'ERROR: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'ait' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	return $user_id;
}

/**
 * Register or upgrade user
 */
if(isset($_GET['dir-register']) && ($_GET['dir-register'] == 'register' || $_GET['dir-register'] == 'upgrade') && isset($_POST['user-submit'])){

	global $aitThemeOptions, $wp_roles, $registerErrors, $registerMessages;

	$upgrade = false;
	if($_GET['dir-register'] == 'upgrade'){
		$upgrade = true;
		$currentRolePriceName =  $_POST['directory-role-current-price'];
		$userId = $_POST['user_id'];
	} else {
		$userId = dir_register_new_user($_POST['user_login'],$_POST['user_email']);
	}

	// if errors
	if(is_wp_error( $userId )){

		$registerErrors = $userId;

	} else {

		$free = true;
		$price = '0';
		$packageName = '';

		// set role
		if(isset($_POST['directory-role'])){
			$role = $_POST['directory-role'];
			if (($role == "directory_1") || ($role == "directory_2") || ($role == "directory_3") || ($role == "directory_4") || ($role == "directory_5")){
				switch ($role) {
					case "directory_1":
						if(isset($aitThemeOptions->members->role1Price) && trim($aitThemeOptions->members->role1Price) !== '0') {
							$free = false;
							$price = trim($aitThemeOptions->members->role1Price);
							$packageName = $wp_roles->role_names[$_POST['directory-role']];
						}
						break;
					case "directory_2":
						if(isset($aitThemeOptions->members->role2Price) && trim($aitThemeOptions->members->role2Price) !== '0') {
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role2Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role2Price);
							}
							$packageName = $wp_roles->role_names[$_POST['directory-role']];
						}
						break;
					case "directory_3":
						if(isset($aitThemeOptions->members->role3Price) && trim($aitThemeOptions->members->role3Price) !== '0') {
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role3Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role3Price);
							}
							$packageName = $wp_roles->role_names[$_POST['directory-role']];
						}
						break;
					case "directory_4":
						if(isset($aitThemeOptions->members->role4Price) && trim($aitThemeOptions->members->role4Price) !== '0') {
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role4Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role4Price);
							}
							$packageName = $wp_roles->role_names[$_POST['directory-role']];
						}
						break;
					case "directory_5":
						if(isset($aitThemeOptions->members->role5Price) && trim($aitThemeOptions->members->role5Price) !== '0') {
							$free = false;
							if($upgrade && $currentRolePriceName != 'none'){
								$price = floatval(trim($aitThemeOptions->members->role5Price)) - floatval(trim($aitThemeOptions->members->$currentRolePriceName));
							} else {
								$price = trim($aitThemeOptions->members->role5Price);
							}
							$packageName = $wp_roles->role_names[$_POST['directory-role']];
						}
						break;
					default:
						break;
				}
				// non free
				if(isset($aitThemeOptions->members->enablePaypal) && (!$free)){

					$currencyCode = (isset($aitThemeOptions->members->paypalCurrencyCode)) ? $aitThemeOptions->members->paypalCurrencyCode : 'USD';
					$sandbox = (isset($aitThemeOptions->members->paypalType) && $aitThemeOptions->members->paypalType == 'live') ? '' : 'sandbox.';
					$paymentName = (isset($aitThemeOptions->members->paypalPaymentName)) ? $aitThemeOptions->members->paypalPaymentName : __('Directory Package','ait');

					if($upgrade){
						$paymentName .= __(' Upgrade','ait');
					}

					//Our request parameters
					$returnUrl = ($upgrade) ? home_url("/?dir-register=success&upgrade=1&price=".$price."&code=".$currencyCode."&role=".$role) : home_url("/?dir-register=success&price=".$price."&code=".$currencyCode."&role=".$role);
					$requestParams = array(
					   'RETURNURL' => $returnUrl,
					   'CANCELURL' => home_url('/?dir-register=cancel&dir-register-status=3')
					);

					$orderParams = array(
					   'PAYMENTREQUEST_0_AMT' => $price,
					   'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
					   'PAYMENTREQUEST_0_CURRENCYCODE' => $currencyCode,
					   'PAYMENTREQUEST_0_ITEMAMT' => $price
					);

					$item = array(
					   'L_PAYMENTREQUEST_0_NAME0' => $paymentName,
					   'L_PAYMENTREQUEST_0_DESC0' => $packageName,
					   'L_PAYMENTREQUEST_0_AMT0' => $price,
					   'L_PAYMENTREQUEST_0_QTY0' => '1'
					);

					$credentials = array();
					$credentials['USER'] = (isset($aitThemeOptions->members->paypalUser)) ? $aitThemeOptions->members->paypalUser : '';
					$credentials['PWD'] = (isset($aitThemeOptions->members->paypalPassword)) ? $aitThemeOptions->members->paypalPassword : '';
					$credentials['SIGNATURE'] = (isset($aitThemeOptions->members->paypalSignature)) ? $aitThemeOptions->members->paypalSignature : '';
					$sandboxBool = (!empty($sandbox)) ? true : false;

					$paypal = new Paypal($credentials,$sandboxBool);
					$response = $paypal -> request('SetExpressCheckout',$requestParams + $orderParams + $item);

					$errors = new WP_Error();

					if(!$response){
						$errorMessage = __( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'ait' );
						$detailErrorMessage = array_shift(array_values($paypal->getErrors()));
						$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
						$registerErrors = $errors;
					}
					if(is_array($response) && $response['ACK'] == 'Success') { //Request successful
						$token = $response['TOKEN'];
						update_option( 'ait_dir_reg_token_'.$userId, $token );
						header( 'Location: https://www.'.$sandbox.'paypal.com/webscr?cmd=_express-checkout&token=' . urlencode($token) );
						die();
					} else {
						$errorMessage = __( 'ERROR: Bad paypal API settings! Check paypal api credentials in admin settings!', 'ait' );
						$detailErrorMessage = (isset($response['L_LONGMESSAGE0'])) ? $response['L_LONGMESSAGE0'] : '';
						$errors->add( 'bad_paypal_api', $errorMessage . ' ' . $detailErrorMessage );
						$registerErrors = $errors;
					}
				} else {
					// free
					$user = get_userdata( $userId );
					$user->set_role( $role );
					writeActivationTime( $userId, $role );
					if($upgrade){
						// upgrade
						$registerMessages = __('Your directory account was upgraded!','ait');
					} else {
						$registerMessages = __('Your directory account was activated! Check your email address for password!','ait');
					}
				}
			}
		}
	}
}


// check token if user payed
if(isset($_GET['dir-register']) && ($_GET['dir-register'] == 'success') && isset($_GET['token']) && !empty($_GET['token'])){

	global $wpdb, $registerErrors, $registerMessages;

	$token = $_GET['token'];
	$tokenRow = $wpdb->get_row("SELECT * FROM $wpdb->options WHERE option_value = '$token'");

	if($tokenRow){
		$userId = substr($tokenRow->option_name,18);
		// PAY
		$credentials = array();
		$credentials['USER'] = (isset($aitThemeOptions->members->paypalUser)) ? $aitThemeOptions->members->paypalUser : '';
		$credentials['PWD'] = (isset($aitThemeOptions->members->paypalPassword)) ? $aitThemeOptions->members->paypalPassword : '';
		$credentials['SIGNATURE'] = (isset($aitThemeOptions->members->paypalSignature)) ? $aitThemeOptions->members->paypalSignature : '';
		$sandboxBool = (isset($aitThemeOptions->members->paypalType) && $aitThemeOptions->members->paypalType == 'live') ? false : true;

		$paypal = new Paypal($credentials,$sandboxBool);
		$checkoutDetails = $paypal -> request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));
		// Complete the checkout transaction
		$requestParams = array(
			'TOKEN' => $_GET['token'],
			'PAYMENTACTION' => 'Sale',
			'PAYERID' => $_GET['PayerID'],
			'PAYMENTREQUEST_0_AMT' => $_GET['price'], // Same amount as in the original request
			'PAYMENTREQUEST_0_CURRENCYCODE' => $_GET['code'] // Same currency as the original request
		);
		$response = $paypal -> request('DoExpressCheckoutPayment',$requestParams);
		// IF PAYMENT OK
		if( is_array($response) && $response['ACK'] == 'Success') { // Payment successful
			$user = get_userdata( $userId );
			$user->set_role($_GET['role']);
			writeActivationTime( $userId, $_GET['role'] );
			// We'll fetch the transaction ID for internal bookkeeping
			$transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];
			// save transaction id
			update_user_meta( $userId, 'dir_paypal_transaction_id', $transactionId );
			// delete cache db info
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'ait_dir_reg_token_'.$userId ) );
			if(isset($_GET['upgrade'])){
				$registerMessages = __('Your directory account was upgraded!','ait');
				//header( 'Location: '.home_url('?dir-register-status=2') );
				//die();
			} else {
				$registerMessages = __('Your directory account was activated! Check your email address for password!','ait');
				//header( 'Location: '.home_url('?dir-register-status=1') );
				//die();
			}
		}
	}
}
// delete token if user cancel payment
if(isset($_GET['dir-register']) && isset($_GET['token']) && ($_GET['dir-register'] == 'cancel')){
	global $wpdb;
	$token = $_GET['token'];
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_value = %s", $token ) );
}

// detailed user capabilities
function itemDetailedCapabilities($data)
{
	global $aitThemeOptions, $wp_meta_boxes, $current_user;

	$usrRoles = $current_user->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		$roleNumber = substr(array_shift(array_values($usrRoles)), 10);

		$nameAddress = 'role'.$roleNumber.'Address';
		$nameStreetview = 'role'.$roleNumber.'Streetview';
		$nameTelephone = 'role'.$roleNumber.'Telephone';
		$nameEmail = 'role'.$roleNumber.'Email';
		$nameWeb = 'role'.$roleNumber.'Web';
		$nameHours = 'role'.$roleNumber.'Hours';
		$nameAlternativeContent = 'role'.$roleNumber.'AlternativeContent';

		if(isset($wp_meta_boxes['ait-dir-item'])){
			foreach ($wp_meta_boxes['ait-dir-item'] as $contextName => $context) {
				foreach ($context as $boxesName => $boxes) {
					foreach ($boxes as $boxName => $box) {
						// item options
						if($boxName == '_ait-dir-item_metabox'){
							//unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['address']);
							if(!isset($aitThemeOptions->members->$nameAddress)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['address']);
							}
							if(!isset($aitThemeOptions->members->$nameStreetview)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['showStreetview']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewHeading']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewLatitude']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewLongitude']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewPitch']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['streetViewZoom']);
							}
							if(!isset($aitThemeOptions->members->$nameTelephone)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['telephone']);
							}
							if(!isset($aitThemeOptions->members->$nameEmail)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['email']);
							}
							if(!isset($aitThemeOptions->members->$nameWeb)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['web']);
							}
							if(!isset($aitThemeOptions->members->$nameHours)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Opening Hours']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursMonday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursTuesday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursWednesday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursThursday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursFriday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursSaturday']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['hoursSunday']);
							}
							if(!isset($aitThemeOptions->members->$nameAlternativeContent)){
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['Alternative Content']);
								unset($wp_meta_boxes['ait-dir-item'][$contextName][$boxesName][$boxName]['callback'][0]->configData['alternativeContent']);
							}
						}
					}
				}
			}
		}
	}
}
add_action( 'add_meta_boxes', 'itemDetailedCapabilities');
// detailed user capabilities
function removeItemFeatures()
{
	global $aitThemeOptions, $current_user;

	$usrRoles = $current_user->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		$roleNumber = substr(array_shift(array_values($usrRoles)), 10);

		$nameEditor = 'role'.$roleNumber.'Content';
		$nameImage = 'role'.$roleNumber.'Image';
		$nameExcerpt = 'role'.$roleNumber.'Excerpt';
		$nameReviews = 'role'.$roleNumber.'Reviews';

		// attributes
		remove_post_type_support( 'ait-dir-item', 'page-attributes' );
		// editor - content
		if(!isset($aitThemeOptions->members->$nameEditor)){
			remove_post_type_support( 'ait-dir-item', 'editor' );
		}
		// image - thumbnail
		if(!isset($aitThemeOptions->members->$nameImage)){
			remove_post_type_support( 'ait-dir-item', 'thumbnail' );
		}
		// excerpt
		if(!isset($aitThemeOptions->members->$nameExcerpt)){
			remove_post_type_support( 'ait-dir-item', 'excerpt' );
		}
		// reviews - comments
		if(!isset($aitThemeOptions->members->$nameReviews)){
			remove_post_type_support( 'ait-dir-item', 'comments' );
		}

	}
}
add_action('init', 'removeItemFeatures');

/*
/ expiration feature
*/

// write activation time
function writeActivationTime($id, $role)
{
	global $wpdb;

	if($role == 'directory_1' || $role == 'directory_2' || $role == 'directory_3' || $role == 'directory_4' || $role == 'directory_5'){
		update_user_meta( $id, 'dir_activation_time', array( 'role' => $role, 'time' => time()) );
		// expired posts back to published
		$wpdb->query($wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'publish' WHERE post_author = %d AND post_status = 'expired'", intval($id)) );
	}
}
add_action('set_user_role', 'writeActivationTime',1,2);

// schedule the accounts check daily
if( !wp_next_scheduled( 'check_user_activation_times' ) ) {
   wp_schedule_event( time(), 'daily', 'check_user_activation_times' );
}
add_action( 'check_user_activation_times', 'checkUserActivationTimes' );
function checkUserActivationTimes() {
	global $aitThemeOptions, $wpdb;

	if(isset($aitThemeOptions->members)){
		$times = $wpdb->get_results("SELECT user_id, meta_value FROM $wpdb->usermeta WHERE meta_key = 'dir_activation_time'");
		foreach ($times as $time) {
			$data = unserialize($time->meta_value);

			$timeInSec = $data['time'];
			$role = $data['role'];

			$differenceInSec = time() - $timeInSec;
			$differenceInDays = floor($differenceInSec / 60 / 60 / 24);

			if($role == 'directory_1' && isset($aitThemeOptions->members->role1Time) && trim($aitThemeOptions->members->role1Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role1Time);
				if($differenceInDays >= $limit){
					expireUser($time->user_id);
				}
			}
			if($role == 'directory_2' && isset($aitThemeOptions->members->role2Time) && trim($aitThemeOptions->members->role2Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role2Time);
				if($differenceInDays >= $limit){
					expireUser($time->user_id);
				}
			}
			if($role == 'directory_3' && isset($aitThemeOptions->members->role3Time) && trim($aitThemeOptions->members->role3Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role3Time);
				if($differenceInDays >= $limit){
					expireUser($time->user_id);
				}
			}
			if($role == 'directory_4' && isset($aitThemeOptions->members->role4Time) && trim($aitThemeOptions->members->role4Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role4Time);
				if($differenceInDays >= $limit){
					expireUser($time->user_id);
				}
			}
			if($role == 'directory_5' && isset($aitThemeOptions->members->role5Time) && trim($aitThemeOptions->members->role5Time) != '0'){
				$limit = floatval($aitThemeOptions->members->role5Time);
				if($differenceInDays >= $limit){
					expireUser($time->user_id);
				}
			}
		}
	}
}

function expireUser($user_id)
{
	global $wpdb;
	$wpdb->query($wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'expired' WHERE post_author = %d AND post_status = 'publish'", intval($user_id)) );
	$user = get_userdata( $user_id );
	$user->set_role('subscriber');
}

function getDaysLeft()
{
	global $wpdb, $current_user, $aitThemeOptions;

	$data = $wpdb->get_row("SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = 'dir_activation_time' AND user_id = ".$current_user->ID);
	$data = unserialize($data->meta_value);

	$roleNumber = substr($data['role'], 10);
	$optionName = 'role'.$roleNumber.'Time';
	$limit = intval($aitThemeOptions->members->$optionName);
	if($limit > 0){
		$timeInSec = $data['time'];
		$differenceInSec = ($limit * 60 * 60 * 24) - (time() - $timeInSec);
		$differenceInDays = ceil($differenceInSec / 60 / 60 / 24);
		if($differenceInDays <= 0){
			$differenceInDays = __('Expired','ait');
		}
	} else {
		$differenceInDays = __('Unlimited','ait');
	}

	return $differenceInDays;
}

function removeMediaAdminButton($menu)
{
	global $current_user;
	$usrRoles = $current_user->roles;
	if(in_array('directory_1', $usrRoles) || in_array('directory_2', $usrRoles) || in_array('directory_3', $usrRoles) || in_array('directory_4', $usrRoles) || in_array('directory_5', $usrRoles)){
		foreach ($menu as $key => $item) {
			if($item[1] == 'upload_files'){
				unset($menu[$key]);
			}
		}
	}
	return $menu;
}
add_filter("add_menu_classes","removeMediaAdminButton");



add_filter('manage_users_columns', 'add_status_column');
add_filter('manage_users_custom_column', 'manage_status_column', 10, 3);

function add_status_column($columns) {
	$columns['items'] = __('Items','ait');
    $columns['activation_time'] = __('Activation time','ait');
    $columns['transaction_id'] = __('Last PayPal transaction ID','ait');
 	return $columns;

}

function manage_status_column($empty='', $column_name, $id) {
	if( $column_name == 'items' ) {
		return getAuthorItemsCount($id);
    }
	if( $column_name == 'activation_time' ) {
		$data = get_user_meta( $id, 'dir_activation_time', true );
		if($data){
			$dateFormat = get_option( 'date_format', 'm/d/Y' );
			return date($dateFormat,$data['time']);
		}
    }
    if( $column_name == 'transaction_id' ) {
		$data = get_user_meta( $id, 'dir_paypal_transaction_id', true );
		if($data){
			return $data;
		}
    }
}

function getAuthorItemsCount($id) {
	global $wpdb;

	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = 'ait-dir-item' AND post_status = 'publish' AND post_author = ".$id );
	return $count;
}