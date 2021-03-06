<?php

$latteParams['post'] = WpLatte::createPostEntity(
	$GLOBALS['wp_query']->post,
	array(
		'meta' => $GLOBALS['pageOptions'],
	)
);

$latteParams['options'] = get_post_meta($latteParams['post']->id, '_ait-dir-item', true);
// check url link
if (isset($latteParams['options']['web']) && !empty($latteParams['options']['web']) && (strpos($latteParams['options']['web'],'http://') === false && strpos($latteParams['options']['web'],'https://') === false)){
    $latteParams['options']['web'] = 'http://'.$latteParams['options']['web'];
}

$thumbnailDir = wp_get_attachment_image_src( get_post_thumbnail_id($latteParams['post']->id) );
if($thumbnailDir !== false){
	$latteParams['thumbnailDir'] = $thumbnailDir[0];
} else {
	$latteParams['thumbnailDir'] = $aitThemeOptions->directory->defaultItemImage;
}
// get term for this items
$terms = wp_get_post_terms($latteParams['post']->id, 'ait-dir-item-category');

// get items from current category 
$latteParams['term'] = array();
$latteParams['ancestors'] = array();
$latteParams['items'] = array();

// pending preview
if($GLOBALS['wp_query']->post->post_status != 'publish'){
	
	$item = $GLOBALS['wp_query']->post;
	// options
	$item->optionsDir = get_post_meta($item->ID, '_ait-dir-item', true);
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
	$item->packageClass = getItemPackageClass($item->post_author);

	$latteParams['term'] = null;
	$latteParams['items'] = array($item);
	$latteParams['ancestors'] = array();
	
} else {
	if(isset($terms[0])){
		
		// term
		$terms[0]->link = get_term_link(intval($terms[0]->term_id), 'ait-dir-item-category');
		$terms[0]->icon = getCategoryMeta("icon", intval($terms[0]->term_id));
		$terms[0]->marker = getCategoryMeta("marker", intval($terms[0]->term_id));

		$termAncestors = array_reverse(get_ancestors(intval($terms[0]->term_id), 'ait-dir-item-category'));
		$ancestors = array();
		foreach ($termAncestors as $anc) {
			$term = get_term($anc, 'ait-dir-item-category');
			$term->link = get_term_link(intval($term->term_id), 'ait-dir-item-category');
			$ancestors[] = $term;
		}

		$categoryID = intval($terms[0]->term_id);
		$location = 0;
		$search = '';
		$radiusKm = ($aitThemeOptions->directory->showDistanceInDetail) ? $aitThemeOptions->directory->showDistanceInDetail : 1000 ;
		// center and radius
		$radius = array($radiusKm,$latteParams['options']['gpsLatitude'],$latteParams['options']['gpsLongitude']);

		$items = getItems($categoryID,$location,$search,$radius);

		$latteParams['term'] = $terms[0];
		$latteParams['items'] = $items;
		$latteParams['ancestors'] = $ancestors;

	} else {
		// no category selected

		// all items
		$items = getItems();
		$thisItem;
		for($i = 0; $i < count($items); $i++) {
			if($items[$i]->ID == $latteParams['post']->id) { 
				$thisItem = $items[$i]; 
			}
		}
		unset($items);

		$latteParams['term'] = null;
		$latteParams['items'] = array($thisItem);
		$latteParams['ancestors'] = array();
	}
}

$post_id = $latteParams['post']->id;

//upcoming events
global $wpdb;
$sql = "select p.ID from $wpdb->posts p, $wpdb->postmeta m, $wpdb->postmeta m2
			where p.post_type='ait-dir-event'
				and p.post_status='publish'
				and (p.ID = m.post_id
					and m.meta_key = 'pg_event_expire_date'
					and (m.meta_value = 'Never' or date(m.meta_value) >= curdate())
				)
				and (p.ID = m2.post_id
					and m2.meta_key = 'pg_event_venue'
					and m2.meta_value = $post_id
				)
			limit 5";
$event_id_query = $wpdb->get_results($sql);
$latteParams['events'] = array();
if (count($event_id_query) > 0){
	$event_ids = array();
	foreach ($event_id_query as $eid){
		$event_ids[] = $eid->ID;
	}

	$query = array(
		'post_type' => 'ait-dir-event',
		'post__in' => $event_ids
	);
	$events = get_posts($query);
	$latteParams['events'] = WpLatte::createPostEntity($events);
}

//recent reviews
$reviews = get_posts(array(
	'post_type' => 'ait-dir-review',
	'meta_query' => array(
		array(
			'key' => 'pg_review_venue',
			'value' => array($post_id),
			'compare' => 'IN'
		)
	)
));
$latteParams['reviews'] = WpLatte::createPostEntity($reviews);

$latteParams['isDirSingle'] = true;

$latteParams['sidebarType'] = 'item';

$latteParams['rating'] = aitCalculateMeanForPost($latteParams['post']->id);

/**
 * Fire!
 */
WPLatte::createTemplate(basename(__FILE__, '.php'), $latteParams)->render();