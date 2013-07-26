<?php

/**
 * AIT Theme Admin
 *
 * Copyright (c) 2011, AIT s.r.o (http://ait-themes.com)
 *
 */

register_taxonomy( 'ait-dir-item-category', array( 'ait-dir-item' ), array(
	'hierarchical' => true,
	'labels' => array(
		'name'			=> 'Loại Địa Điểm',
		'singular_name' => _x( 'Loại Địa Điểm', 'taxonomy singular name', 'ait'),
		'search_items'	=> __( 'Tìm Loại Địa Điểm', 'ait'),
		'all_items'		=> __( 'Tất cả Loại Địa Điểm', 'ait'),
		'parent_item'	=> __( 'Loại Địa Điểm cấp cha', 'ait'),
		'parent_item_colon' => __( 'Loại Địa Điểm cấp cha:', 'ait'),
		'edit_item'		=> __( 'Sửa Loại Địa Điểm', 'ait'),
		'update_item'	=> __( 'Cập nhật Loại Địa Điểm', 'ait'),
		'add_new_item'	=> __( 'Thêm Loại Địa Điểm mới', 'ait'),
		'new_item_name' => __( 'Tên Loại Địa Điểm mới', 'ait'),
	),
	'show_ui' => true,
	'rewrite' => array( 'slug' => 'cat' ),
	'query_var' => 'dir-item-category'
));

register_taxonomy( 'ait-dir-item-location', array( 'ait-dir-item','ait-dir-event' ), array(
	'hierarchical' => true,
	'labels' => array(
		'name'			=> 'Khu Vực',
		'singular_name' => _x( 'Khu Vực', 'taxonomy singular name', 'ait'),
		'search_items'	=> __( 'Tìm Khu Vực', 'ait'),
		'all_items'		=> __( 'Tất cả Khu Vực', 'ait'),
		'parent_item'	=> __( 'Khu Vực cấp cha', 'ait'),
		'parent_item_colon' => __( 'Khu Vực cấp cha:', 'ait'),
		'edit_item'		=> __( 'Sửa Khu Vực', 'ait'),
		'update_item'	=> __( 'Cập nhật Khu Vực', 'ait'),
		'add_new_item'	=> __( 'Thêm Khu Vực mới', 'ait'),
		'new_item_name' => __( 'Tên Khu Vực mới', 'ait'),
	),
	'show_ui' => true,
	'rewrite' => false,
	'query_var' => 'dir-item-location'
));


//Purpose
$labels = array(
    'name'                          => __( 'Mục Đích', 'site5framework' ),
    'singular_name'                 => __( 'Mục Đích', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Mục Đích', 'site5framework' ),
    'popular_items'                 => __( 'Mục Đích phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Mục Đích', 'site5framework' ),
    'parent_item'                   => __( 'Mục Đích cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Mục Đích', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Mục Đích', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Mục Đích', 'site5framework' ),
    'new_item_name'                 => __( 'Mục Đích mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Mục Đích bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Mục Đích', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Mục Đích phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Mục Đích', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'purpose', 'with_front' => false ),
    'query_var'                     => 'purpose'
);

register_taxonomy( 'purpose', array('ait-dir-item'), $args );

//Culture taxonomy
$labels = array(
    'name'                          => __( 'Văn Hoá Ẩm Thực', 'site5framework' ),
    'singular_name'                 => __( 'Văn Hoá Ẩm Thực', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Văn Hoá Ẩm Thực', 'site5framework' ),
    'popular_items'                 => __( 'Văn Hoá Ẩm Thực phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Văn Hoá Ẩm Thực', 'site5framework' ),
    'parent_item'                   => __( 'Văn Hoá Ẩm Thực cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Văn Hoá Ẩm Thực', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Văn Hoá Ẩm Thực', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Văn Hoá Ẩm Thực', 'site5framework' ),
    'new_item_name'                 => __( 'Văn Hoá Ẩm Thực mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Văn Hoá Ẩm Thực bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Văn Hoá Ẩm Thực', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Văn Hoá Ẩm Thực phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Văn Hoá Ẩm Thực', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'culture', 'with_front' => false ),
    'query_var'                     => 'culture'
);

register_taxonomy( 'culture', array('ait-dir-item'), $args );

//Dishes taxonomy
$labels = array(
    'name'                          => __( 'Thể Loại Món', 'site5framework' ),
    'singular_name'                 => __( 'Thể Loại Món', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Thể Loại Món', 'site5framework' ),
    'popular_items'                 => __( 'Thể Loại Món phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Thể Loại Món', 'site5framework' ),
    'parent_item'                   => __( 'Thể Loại Món cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Thể Loại Món', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Thể Loại Món', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Thể Loại Món', 'site5framework' ),
    'new_item_name'                 => __( 'Thể Loại Món mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Thể Loại Món bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Thể Loại Món', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Thể Loại Món phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Thể Loại Món', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'dishes', 'with_front' => false ),
    'query_var'                     => 'dishes'
);

register_taxonomy( 'dishes', array('ait-dir-item'), $args );

//Facility taxonomy
$labels = array(
    'name'                          => __( 'Tiện Nghi', 'site5framework' ),
    'singular_name'                 => __( 'Tiện Nghi', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Tiện Nghi', 'site5framework' ),
    'popular_items'                 => __( 'Tiện Nghi phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Tiện Nghi', 'site5framework' ),
    'parent_item'                   => __( 'Tiện Nghi cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Tiện Nghi', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Tiện Nghi', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Tiện Nghi', 'site5framework' ),
    'new_item_name'                 => __( 'Tiện Nghi mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Tiện Nghi bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Tiện Nghi', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Tiện Nghi phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Tiện Nghi', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'facility', 'with_front' => false ),
    'query_var'                     => 'facility'
);

register_taxonomy( 'facility', array('ait-dir-item'), $args );

//Timeframe taxonomy
$labels = array(
    'name'                          => __( 'Thời Gian Hoạt Động', 'site5framework' ),
    'singular_name'                 => __( 'Thời Gian Hoạt Động', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Thời Gian Hoạt Động', 'site5framework' ),
    'popular_items'                 => __( 'Thời Gian Hoạt Động phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Thời Gian Hoạt Động', 'site5framework' ),
    'parent_item'                   => __( 'Thời Gian Hoạt Động cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Thời Gian Hoạt Động', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Thời Gian Hoạt Động', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Thời Gian Hoạt Động', 'site5framework' ),
    'new_item_name'                 => __( 'Thời Gian Hoạt Động mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Thời Gian Hoạt Động bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Thời Gian Hoạt Động', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Thời Gian Hoạt Động phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Thời Gian Hoạt Động', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'timeframe', 'with_front' => false ),
    'query_var'                     => 'timeframe'
);

register_taxonomy( 'timeframe', array('ait-dir-item'), $args );

//Avgprice taxonomy
$labels = array(
    'name'                          => __( 'Mức Giá Trung Bình', 'site5framework' ),
    'singular_name'                 => __( 'Mức Giá Trung Bình', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Mức Giá Trung Bình', 'site5framework' ),
    'popular_items'                 => __( 'Mức Giá Trung Bình phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Mức Giá Trung Bình', 'site5framework' ),
    'parent_item'                   => __( 'Mức Giá Trung Bình cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Mức Giá Trung Bình', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Mức Giá Trung Bình', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Mức Giá Trung Bình', 'site5framework' ),
    'new_item_name'                 => __( 'Mức Giá Trung Bình mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Mức Giá Trung Bình bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Mức Giá Trung Bình', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Mức Giá Trung Bình phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Mức Giá Trung Bình', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'avgprice', 'with_front' => false ),
    'query_var'                     => 'avgprice'
);

register_taxonomy( 'avgprice', array('ait-dir-item'), $args );

//Merchandise taxonomy
$labels = array(
    'name'                          => __( 'Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'singular_name'                 => __( 'Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'popular_items'                 => __( 'Sản Phẩm/Hàng Hoá phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Toàn bộ Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'parent_item'                   => __( 'Sản Phẩm/Hàng Hoá cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'new_item_name'                 => __( 'Sản Phẩm/Hàng Hoá mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách tên Sản Phẩm/Hàng Hoá bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Sản Phẩm/Hàng Hoá', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Sản Phẩm/Hàng Hoá phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Sản Phẩm/Hàng Hoá', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'merchandise', 'with_front' => false ),
    'query_var'                     => 'merchandise'
);

register_taxonomy( 'merchandise', array('ait-dir-item'), $args );

//capacity
$labels = array(
    'name'                          => __( 'Số Lượng Chỗ', 'site5framework' ),
    'singular_name'                 => __( 'Số Lượng Chỗ', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Số Lượng Chỗ', 'site5framework' ),
    'popular_items'                 => __( 'Số Lượng Chỗ phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Số Lượng Chỗ', 'site5framework' ),
    'parent_item'                   => __( 'Số Lượng Chỗ cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Số Lượng Chỗ', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Số Lượng Chỗ', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Số Lượng Chỗ', 'site5framework' ),
    'new_item_name'                 => __( 'Số Lượng Chỗ mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Số Lượng Chỗ bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Số Lượng Chỗ', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Số Lượng Chỗ phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Số Lượng Chỗ', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'capacity', 'with_front' => false ),
    'query_var'                     => 'capacity'
);

register_taxonomy( 'capacity', array('ait-dir-item','ait-dir-event'), $args );

//event type
$labels = array(
    'name'                          => __( 'Loại Sự Kiện', 'site5framework' ),
    'singular_name'                 => __( 'Loại Sự Kiện', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Loại Sự Kiện', 'site5framework' ),
    'popular_items'                 => __( 'Loại Sự Kiện phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Loại Sự Kiện', 'site5framework' ),
    'parent_item'                   => __( 'Loại Sự Kiện cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Loại Sự Kiện', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Loại Sự Kiện', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Loại Sự Kiện', 'site5framework' ),
    'new_item_name'                 => __( 'Loại Sự Kiện mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Loại Sự Kiện bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Loại Sự Kiện', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Loại Sự Kiện phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Loại Sự Kiện', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'event/event_types', 'with_front' => false ),
    'query_var'                     => 'event_types'
);

register_taxonomy( 'event_types', 'ait-dir-event', $args );

//ticket
$labels = array(
    'name'                          => __( 'Giá Vé', 'site5framework' ),
    'singular_name'                 => __( 'Giá Vé', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Giá Vé', 'site5framework' ),
    'popular_items'                 => __( 'Giá Vé phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Giá Vé', 'site5framework' ),
    'parent_item'                   => __( 'Giá Vé cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Giá Vé', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Giá Vé', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Giá Vé', 'site5framework' ),
    'new_item_name'                 => __( 'Giá Vé mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Giá Vé bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Giá Vé', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Giá Vé phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Giá Vé', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'event/ticketprice', 'with_front' => false ),
    'query_var'                     => 'ticketprice'
);

register_taxonomy( 'ticketprice', 'ait-dir-event', $args );

//entertainment_industry
$labels = array(
    'name'                          => __( 'Dịch vụ giải trí', 'site5framework' ),
    'singular_name'                 => __( 'Dịch vụ giải trí', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm ', 'site5framework' ),
    'popular_items'                 => __( 'Dịch vụ giải trí phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Dịch vụ giải trí', 'site5framework' ),
    'parent_item'                   => __( 'Dịch vụ giải trí cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Dịch vụ giải trí', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Dịch vụ giải trí', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Dịch vụ giải trí', 'site5framework' ),
    'new_item_name'                 => __( 'Dịch vụ giải trí mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Dịch vụ giải trí bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớtDịch vụ giải trí', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Dịch vụ giải trí phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Dịch vụ giải trí', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'entertainment_industry', 'with_front' => false ),
    'query_var'                     => 'entertainment_industry'
);

register_taxonomy( 'entertainment_industry', 'ait-dir-item', $args );

//music_style
$labels = array(
    'name'                          => __( 'Thể loại nhạc', 'site5framework' ),
    'singular_name'                 => __( 'Thể loại nhạc', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Thể loại nhạc', 'site5framework' ),
    'popular_items'                 => __( 'Thể loại nhạc phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Thể loại nhạc', 'site5framework' ),
    'parent_item'                   => __( 'Thể loại nhạc cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Thể loại nhạc', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Thể loại nhạc', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Thể loại nhạc', 'site5framework' ),
    'new_item_name'                 => __( 'Thể loại nhạc mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Thể loại nhạc bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Thể loại nhạc', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Thể loại nhạc phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Thể loại nhạc', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'music_style', 'with_front' => false ),
    'query_var'                     => 'music_style'
);

register_taxonomy( 'music_style', 'ait-dir-item', $args );

//beauty_service
$labels = array(
    'name'                          => __( 'Dịch vụ làm đẹp', 'site5framework' ),
    'singular_name'                 => __( 'Dịch vụ làm đẹp', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Dịch vụ làm đẹp', 'site5framework' ),
    'popular_items'                 => __( 'Dịch vụ làm đẹp phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Dịch vụ làm đẹp', 'site5framework' ),
    'parent_item'                   => __( 'Dịch vụ làm đẹp cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Dịch vụ làm đẹp', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Dịch vụ làm đẹp', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Dịch vụ làm đẹp', 'site5framework' ),
    'new_item_name'                 => __( 'Dịch vụ làm đẹp mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Dịch vụ làm đẹp bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Dịch vụ làm đẹp', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Dịch vụ làm đẹp phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Dịch vụ làm đẹp', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'beauty_service', 'with_front' => false ),
    'query_var'                     => 'beauty_service'
);

register_taxonomy( 'beauty_service', 'ait-dir-item', $args );


//watching_type
$labels = array(
    'name'                          => __( 'Loại hình để xem', 'site5framework' ),
    'singular_name'                 => __( 'Loại hình để xem', 'site5framework' ),
    'search_items'                  => __( 'Tìm kiếm Loại hình để xem', 'site5framework' ),
    'popular_items'                 => __( 'Loại hình để xem phổ biến', 'site5framework' ),
    'all_items'                     => __( 'Tất cả Loại hình để xem', 'site5framework' ),
    'parent_item'                   => __( 'Loại hình để xem cấp cha', 'site5framework' ),
    'edit_item'                     => __( 'Sửa Loại hình để xem', 'site5framework' ),
    'update_item'                   => __( 'Cập nhật Loại hình để xem', 'site5framework' ),
    'add_new_item'                  => __( 'Thêm Loại hình để xem', 'site5framework' ),
    'new_item_name'                 => __( 'Loại hình để xem mới', 'site5framework' ),
    'separate_items_with_commas'    => __( 'Phân cách Loại hình để xem bằng dấu phẩy (,)', 'site5framework' ),
    'add_or_remove_items'           => __( 'Thêm/bớt Loại hình để xem', 'site5framework' ),'',
    'choose_from_most_used'         => __( 'Chọn trong danh sách Loại hình để xem phổ biến', 'site5framework' )
);

$args = array(
    'label'                         => __( 'Loại hình để xem', 'site5framework' ),
    'labels'                        => $labels,
    'public'                        => true,
    'hierarchical'                  => true,
    'show_ui'                       => true,
    'show_in_nav_menus'             => true,
    'args'                          => array( 'orderby' => 'term_order' ),
    'rewrite'                       => array( 'slug' => 'watching_type', 'with_front' => false ),
    'query_var'                     => 'watching_type'
);

register_taxonomy( 'watching_type', 'ait-dir-item', $args );

function aitDirItemPostType()
{
	register_post_type( 'ait-dir-item',
		array(
			'labels' => array(
				'name'			=> 'Địa Điểm',
				'singular_name' => 'Địa Điểm',
				'add_new'		=> 'Đăng ký Địa Điểm',
				'add_new_item'	=> 'Đăng ký Địa Điểm',
				'edit_item'		=> 'Sửa Địa Điểm',
				'new_item'		=> 'Địa Điểm mới',
				'not_found'		=> 'Không có Địa Điểm nào',
				'not_found_in_trash' => 'Không có Địa Điểm nào đã xoá',
				'menu_name'		=> 'Địa Điểm',
			),
			'description' => 'Quản lý Địa Điểm',
			'public' => true,
			'show_in_nav_menus' => true,
			'supports' => array(
				'title',
            	'thumbnail',
				'editor',
				'excerpt',
				'page-attributes',
				'comments',
				'revisions'
			),
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => AIT_FRAMEWORK_URL . '/CustomTypes/dir-item/dir-item.png',
			'menu_position' => $GLOBALS['aitThemeCustomTypes']['dir-item'],
			'has_archive' => true,
			'query_var' => true,//'dir-item',
			'rewrite' => array('slug' => 'item'),
			// 'capability_type' => 'ait-dir-item',
			// 'map_meta_cap' => true
		)
	);
	
	register_post_type( 'ait-dir-event',
		array(
			'labels' => array(
				'name'			=> 'Sự Kiện',
				'singular_name' => 'Sự Kiện',
				'add_new'		=> 'Đăng ký Sự Kiện',
				'add_new_item'	=> 'Đăng ký Sự Kiện',
				'edit_item'		=> 'Sửa Sự Kiện',
				'new_item'		=> 'Sự Kiện mới',
				'not_found'		=> 'Không có Sự Kiện nào',
				'not_found_in_trash' => 'Không có Sự Kiện nào đã xoá',
				'menu_name'		=> 'Sự Kiện',
			),
			'description' => 'Quản lý Sự Kiện',
			'public' => true,
			'show_in_nav_menus' => true,
			'supports' => array(
				'title',
            	'thumbnail',
				'editor',
				'excerpt',
				'page-attributes',
				'comments',
				'revisions'
			),
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => AIT_FRAMEWORK_URL . '/CustomTypes/dir-item/dir-item.png',
			'menu_position' => $GLOBALS['aitThemeCustomTypes']['dir-item'],
			'has_archive' => true,
			'query_var' => true,//'dir-event',
			'rewrite' => array('slug' => 'event'),
			// 'capability_type' => 'ait-dir-item',
			// 'map_meta_cap' => true
		)
	);
	
	register_post_type( 'ait-dir-review',
		array(
			'labels' => array(
				'name'			=> 'Đánh Giá',
				'singular_name' => 'Đánh Giá',
				'add_new'		=> 'Viết Đánh Giá',
				'add_new_item'	=> 'Viết Đánh Giá',
				'edit_item'		=> 'Sửa Đánh Giá',
				'new_item'		=> 'Đánh Giá mới',
				'not_found'		=> 'Không có Đánh Giá nào',
				'not_found_in_trash' => 'Không có Đánh Giá nào đã xoá',
				'menu_name'		=> 'Đánh Giá',
			),
			'description' => 'Quản lý Đánh Giá',
			'public' => true,
			'show_in_nav_menus' => true,
			'supports' => array(
				'title',
            	'thumbnail',
				'editor',
				'excerpt',
				'page-attributes',
				'comments',
				'revisions'
			),
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => AIT_FRAMEWORK_URL . '/CustomTypes/dir-item/dir-item.png',
			'menu_position' => $GLOBALS['aitThemeCustomTypes']['dir-item'],
			'has_archive' => true,
			'query_var' => true,//'dir-event',
			'rewrite' => array('slug' => 'review'),
			// 'capability_type' => 'ait-dir-item',
			// 'map_meta_cap' => true
		)
	);
	
	aitDirItemTaxonomies();

	flush_rewrite_rules(false);
}

function aitDirItemTaxonomies()
{

	

}
add_action( 'init', 'aitDirItemPostType');

function aitDirItemFeaturedImageMetabox()
{
	global $wp_meta_boxes;
	// only if exist
	if(isset($wp_meta_boxes['ait-dir-item'])){
		foreach ($wp_meta_boxes['ait-dir-item'] as $contextName => $context) {
			foreach ($context as $boxesName => $boxes) {
				foreach ($boxes as $boxName => $box) {
					if($boxName == 'postimagediv'){
						remove_meta_box( 'postimagediv', 'ait-dir-item', 'side' );
						add_meta_box('postimagediv', 'Hình ảnh đại diện', 'post_thumbnail_meta_box', 'ait-dir-item', 'normal', 'high');
					}
				}
			}
		}
	}
	
	if(isset($wp_meta_boxes['ait-dir-event'])){
		foreach ($wp_meta_boxes['ait-dir-event'] as $contextName => $context) {
			foreach ($context as $boxesName => $boxes) {
				foreach ($boxes as $boxName => $box) {
					if($boxName == 'postimagediv'){
						remove_meta_box( 'postimagediv', 'ait-dir-event', 'side' );
						add_meta_box('postimagediv', 'Hình ảnh đại diện', 'post_thumbnail_meta_box', 'ait-dir-event', 'normal', 'high');
					}
				}
			}
		}
	}
}
add_action('do_meta_boxes', 'aitDirItemFeaturedImageMetabox',1);

$dirItemOptions = new WPAlchemy_MetaBox(array
(
	'id' => '_ait-dir-item',
	'title' => __('Thông tin Địa Điểm', 'ait'),
	'types' => array('ait-dir-item'),
	'context' => 'normal',
	'priority' => 'core',
	'config' => dirname(__FILE__) . '/' . basename(__FILE__, '.php') . '.neon',
	'js' => dirname(__FILE__) . '/' . basename(__FILE__, '.php') . '.js',
));

function aitDirItemChangeColumns($cols)
{
	$cols = array(
		'cb'			=> '<input type="checkbox" />',
		'title'			=> __( 'Tiêu đề', 'ait'),
		'author'		=> __('Tác giả bài viết', 'ait'),
		'thumbnail'		=> __( 'Hình ảnh', 'ait'),
		'address' 		=> __( 'Địa chỉ', 'ait'),
		'category'		=> __( 'Loại Địa Điểm', 'ait'),
		'ait-dir-item-location'	=> __( 'Khu Vực', 'ait'),
	);

	return $cols;
}
add_filter( "manage_ait-dir-item_posts_columns", "aitDirItemChangeColumns" );

function aitDirItemCustomColumns($column, $post_id)
{
	global $dirItemOptions;
	$options = $dirItemOptions->the_meta();

	switch($column){

		case "ait-dir-item-location":
			$terms = get_the_terms($post_id, 'ait-dir-item-location');
			if(!empty($terms)){
				foreach($terms as $term){
					echo "<a href='".get_term_link($term, 'ait-dir-item-location')."' rel='tag'>{$term->name}</p>";
				}
			}else{
				//echo "<p>No locations</p>";
			}
			break;

		case "address":
			if(isset($options['address'])){
				echo $options['address'];
			}
			break;

	}
}
add_action( "manage_posts_custom_column", "aitDirItemCustomColumns", 10, 2);


/************************** Add meta ICON *****************************************/
add_action( 'ait-dir-item-category_edit_form_fields', 'edit_dir_item_category', 10, 2);
add_action( 'ait-dir-item-category_add_form_fields', 'add_dir_item_category', 10, 2);
function edit_dir_item_category($tag, $taxonomy)
{
	$icon = get_option( 'ait_dir_item_category_'.$tag->term_id.'_icon', '' );
	$marker = get_option( 'ait_dir_item_category_'.$tag->term_id.'_marker', '' );
	$excerpt = get_option( 'ait_dir_item_category_'.$tag->term_id.'_excerpt', '' );

	?>
	<tr class="form-field">
        <th scope="row" valign="top"><label for="ait_dir_item_category_excerpt">Giới thiệu tóm tắt</label></th>
        <td>
            <textarea name="ait_dir_item_category_excerpt" id="ait_dir_item_category_excerpt" cols="30" rows="5"><?php echo $excerpt; ?></textarea>
        </td>
    </tr>
	<tr class="form-field">
        <th scope="row" valign="top"><label for="ait_dir_item_category_icon">Icon</label></th>
        <td>
            <input type="text" name="ait_dir_item_category_icon" id="ait_dir_item_category_icon" value="<?php echo $icon; ?>" style="width: 80%;"/>
            <input type="button" value="Select Image" class="media-select" id="ait_dir_item_category_icon_selectMedia" name="ait_dir_item_category_icon_selectMedia" style="width: 15%;">
            <br />
            <p class="description">Icon for category</p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="ait_dir_item_category_marker">Map Marker</label></th>
        <td>
            <input type="text" name="ait_dir_item_category_marker" id="ait_dir_item_category_marker" value="<?php echo $marker; ?>" style="width: 80%;"/>
            <input type="button" value="Select Image" class="media-select" id="ait_dir_item_category_marker_selectMedia" name="ait_dir_item_category_marker_selectMedia" style="width: 15%;">
            <br />
            <p class="description">Marker image in map for category</p>
        </td>
    </tr>
    <?php
}
function add_dir_item_category($tag)
{
	?>
	<div class="form-field">
        <label for="ait_dir_item_category_excerpt">Excerpt</label>
        <textarea name="ait_dir_item_category_excerpt" id="ait_dir_item_category_excerpt" cols="30" rows="5"></textarea>
    </div>
	<div class="form-field">
		<label for="ait_dir_item_category_icon">Icon</label>
		<input type="text" name="ait_dir_item_category_icon" id="ait_dir_item_category_icon" value="" style="width: 80%;"/>
        <input type="button" value="Select Image" class="media-select" id="ait_dir_item_category_icon_selectMedia" name="ait_dir_item_category_icon_selectMedia" style="width: 15%;">
            <br />
            <p class="description">Icon for category</p>
	</div>
	<div class="form-field">
		<label for="ait_dir_item_category_marker">Map Marker</label>
		<input type="text" name="ait_dir_item_category_marker" id="ait_dir_item_category_marker" value="" style="width: 80%;"/>
        <input type="button" value="Select Image" class="media-select" id="ait_dir_item_category_marker_selectMedia" name="ait_dir_item_category_marker_selectMedia" style="width: 15%;">
            <br />
            <p class="description">Marker image in map for category</p>
	</div>
	<?php
}
add_action( 'created_ait-dir-item-category', 'save_dir_item_category', 10, 2);
add_action( 'edited_ait-dir-item-category', 'save_dir_item_category', 10, 2);
function save_dir_item_category($term_id, $tt_id)
{
    if (!$term_id) return;

	if (isset($_POST['ait_dir_item_category_excerpt'])){
		$name = 'ait_dir_item_category_' .$term_id. '_excerpt';
		update_option( $name, $_POST['ait_dir_item_category_excerpt'] );
	}

	if (isset($_POST['ait_dir_item_category_icon'])){
		$name = 'ait_dir_item_category_' .$term_id. '_icon';
		update_option( $name, $_POST['ait_dir_item_category_icon'] );
	}

    if (isset($_POST['ait_dir_item_category_marker'])){
		$name = 'ait_dir_item_category_' .$term_id. '_marker';
		update_option( $name, $_POST['ait_dir_item_category_marker'] );
    }
}

add_action( 'deleted_term_taxonomy', 'delete_dir_item_category' );
function delete_dir_item_category($id)
{
	delete_option( 'ait_dir_item_category_'.$id.'_excerpt' );
	delete_option( 'ait_dir_item_category_'.$id.'_icon' );
	delete_option( 'ait_dir_item_category_'.$id.'_marker' );
}


// TABLE COLUMNS
add_filter("manage_edit-ait-dir-item-category_columns", 'dir_item_category_columns');
function dir_item_category_columns($category_columns) {
	$new_columns = array(
		'cb'        		=> '<input type="checkbox" />',
		'name'      		=> __('Name', 'ait'),
		'description'     	=> __('Description', 'ait'),
		'item_excerpt'	    => __('Excerpt', 'ait'),
		'icon' 				=> __('Icon', 'ait'),
		'marker'			=> __('Marker', 'ait'),
		'slug'      		=> __('Slug', 'ait'),
		'posts'     		=> __('Items', 'ait'),
		);
	return $new_columns;
}

add_filter("manage_ait-dir-item-category_custom_column", 'manage_dir_item_category_columns', 10, 3);
function manage_dir_item_category_columns($out, $column_name, $cat_id) {

	$icon = get_option( 'ait_dir_item_category_'.$cat_id.'_icon', '' );
	$marker = get_option( 'ait_dir_item_category_'.$cat_id.'_marker', '' );
	$excerpt = get_option( 'ait_dir_item_category_'.$cat_id.'_excerpt', '' );

	switch ($column_name) {
		case 'item_excerpt':
			if($excerpt && !empty($excerpt)){
				$out .= $excerpt;
			}
			break;
 		case 'icon':
			if(!empty($icon)){
				$out .= '<img src="'.$icon.'" alt="" width="50" height="50">';
			}
 			break;
 		case 'marker':
			if(!empty($marker)){
				$out .= '<img src="'.$marker.'" alt="" width="50" height="50">';
			}
 			break;
		default:
			break;
	}
	return $out;
}

function aitDirItemSortableColumns()
{
  return array(
    'title'=> 'title',
    'category'=> 'category'
  );
}
add_filter( "manage_edit-ait-dir-item_sortable_columns", "aitDirItemSortableColumns" );

//PlayGround Event Metabox
$event_prefix = 'pg_';

$event_meta_box = array(
	'id' => 'eventbox',
	'title' => 'Thông tin Sự Kiện',
	'page' => 'ait-dir-event',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

        array(
			'name' => 'Địa điểm tổ chức',
			'desc' => '',
			'id' => $event_prefix . 'event_venue',
			'type' => 'venue',
			'std' => ''
		),
		
        array(
            'name' => 'Ngày tổ chức',
            'desc' => 'VD: Chủ Nhật hàng tuần',
            'id' => $event_prefix . 'event_date',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Thời gian tổ chức',
            'desc' => 'VD: 10:00AM - 05:00PM',
            'id' => $event_prefix . 'event_time',
            'type' => 'text',
            'std' => ''
        ),

		array(
            'name' => 'Ngày hết hạn',
            'desc' => 'VD: 2014-06-20',
            'id' => $event_prefix . 'event_expire_date',
            'type' => 'text',
            'std' => ''
        )

	),

);


add_action('admin_menu', 'event_add_box');

// Add meta box
function event_add_box() {
	global $event_meta_box;

	add_meta_box($event_meta_box['id'], $event_meta_box['title'], 'event_show_box', $event_meta_box['page'], $event_meta_box['context'], $event_meta_box['priority']);
}

// Callback function to show fields in meta box
function event_show_box() {
	global $event_meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="event_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($event_meta_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ($field['type']) {
			case 'hidden':
                echo '<img src="', $meta ? $meta : $field['std'], '" id="', $field['id'], '_img" style="width:600px"/>';
				echo '<input type="hidden" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:600px" />',
					'<br />', $field['desc'];
				break;
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea class="theEditor" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];

				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>',
				'<br />', $field['desc'];
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
			case 'venue':
				echo '<link rel="stylesheet" href="', get_template_directory_uri(), '/design/css/select2.css">';
				echo '<script type="text/javascript" src="', get_template_directory_uri(), '/design/js/libs/select2.min.js"></script>';
				echo '<script type="text/javascript" src="', get_template_directory_uri(), '/design/js/libs/select2_locale_vi.js"></script>';
				$places = get_posts(array('post_type'=>array('ait-dir-item'),'post_status'=> 'publish','numberposts'=>-1));
				echo '<select id="', $field['id'], '" name="', $field['id'], '" style="width:100%">';
				echo '<option></option>';
				foreach ($places as $place){
					echo '<option value="', $place->ID, '">', $place->post_title, '</option>';
				}
				echo '</select>';
				echo 	'<script type="text/javascript">',
							'jQuery("select#', $field['id'], '").select2({placeholder:"Chọn địa điểm tổ chức",allowClear:true});';
				if ($meta){
					echo 'jQuery("select#', $field['id'], '").select2("val",', $meta, ');';
				}
				echo '</script>';
				break;
            case "upload":
            echo '<div class="upload_button_div"> <span id="', $field['id'], '"><a href="#" id="set-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary">Add Media</a></b></span><br /><small>'.$field['desc'].'<small></div>';
                break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}

	echo '</table>';
}

add_action('save_post', 'event_save_data');

// Save data from meta box
function event_save_data($post_id) {
	global $event_meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['event_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($event_meta_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];
		
		if ($field['id'] == 'pg_event_expire_date' && $new == ''){
			$new = 'Never';
		}

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}

//PlayGround Review Metabox
$review_prefix = 'pg_';

$review_meta_box = array(
	'id' => 'reviewbox',
	'title' => 'Thông tin Đánh Giá',
	'page' => 'ait-dir-review',
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(

        array(
			'name' => 'Đánh giá cho địa điểm / sự kiện',
			'desc' => '',
			'id' => $review_prefix . 'review_venue',
			'type' => 'venue',
			'std' => ''
		),
		
        array(
            'name' => 'Ngày sử dụng dịch vụ',
            'desc' => 'VD: 2013-06-20',
            'id' => $review_prefix . 'review_date',
            'type' => 'text',
            'std' => ''
        ),

        array(
            'name' => 'Thời gian sử dụng dịch vụ',
            'desc' => 'VD: 10:00AM - 05:00PM',
            'id' => $review_prefix . 'review_time',
            'type' => 'text',
            'std' => ''
        )

	),

);


add_action('admin_menu', 'review_add_box');

// Add meta box
function review_add_box() {
	global $review_meta_box;

	add_meta_box($review_meta_box['id'], $review_meta_box['title'], 'review_show_box', $review_meta_box['page'], $review_meta_box['context'], $review_meta_box['priority']);
}

// Callback function to show fields in meta box
function review_show_box() {
	global $review_meta_box, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="review_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

	echo '<table class="form-table">';

	foreach ($review_meta_box['fields'] as $field) {
		// get current post meta data
		$meta = get_post_meta($post->ID, $field['id'], true);

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td>';
		switch ($field['type']) {
			case 'hidden':
                echo '<img src="', $meta ? $meta : $field['std'], '" id="', $field['id'], '_img" style="width:600px"/>';
				echo '<input type="hidden" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:600px" />',
					'<br />', $field['desc'];
				break;
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
					'<br />', $field['desc'];
				break;
			case 'textarea':
				echo '<textarea class="theEditor" name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
					'<br />', $field['desc'];

				break;
			case 'select':
				echo '<select name="', $field['id'], '" id="', $field['id'], '">';
				foreach ($field['options'] as $option) {
					echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
				}
				echo '</select>',
				'<br />', $field['desc'];
				break;
			case 'radio':
				foreach ($field['options'] as $option) {
					echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
				}
				break;
			case 'venue':
				echo '<link rel="stylesheet" href="', get_template_directory_uri(), '/design/css/select2.css">';
				echo '<script type="text/javascript" src="', get_template_directory_uri(), '/design/js/libs/select2.min.js"></script>';
				echo '<script type="text/javascript" src="', get_template_directory_uri(), '/design/js/libs/select2_locale_vi.js"></script>';
				$places = get_posts(array('post_type'=>array('ait-dir-item','ait-dir-event'),'post_status'=> 'publish','numberposts'=>-1));
				echo '<select id="', $field['id'], '" name="', $field['id'], '" style="width:100%">';
				echo '<option></option>';
				foreach ($places as $place){
					echo '<option value="', $place->ID, '">', $place->post_title, '</option>';
				}
				echo '</select>';
				echo 	'<script type="text/javascript">',
							'jQuery("select#', $field['id'], '").select2({placeholder:"Chọn địa điểm / sự kiẹn",allowClear:true});';
				if ($meta){
					echo 'jQuery("select#', $field['id'], '").select2("val",', $meta, ');';
				}
				echo '</script>';
				break;
            case "upload":
            echo '<div class="upload_button_div"> <span id="', $field['id'], '"><a href="#" id="set-post-thumbnail" onclick="jQuery(\'#add_image\').click();return true;" class="button-primary">Add Media</a></b></span><br /><small>'.$field['desc'].'<small></div>';
                break;
			case 'checkbox':
				echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
				break;
		}
		echo 	'<td>',
			'</tr>';
	}

	echo '</table>';
}

add_action('save_post', 'review_save_data');

// Save data from meta box
function review_save_data($post_id) {
	global $review_meta_box;

	// verify nonce
	if (!wp_verify_nonce($_POST['review_meta_box_nonce'], basename(__FILE__))) {
		return $post_id;
	}

	// check autosave
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return $post_id;
	}

	// check permissions
	if ('page' == $_POST['post_type']) {
		if (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
	} elseif (!current_user_can('edit_post', $post_id)) {
		return $post_id;
	}

	foreach ($review_meta_box['fields'] as $field) {
		$old = get_post_meta($post_id, $field['id'], true);
		$new = $_POST[$field['id']];

		if ($new && $new != $old) {
			update_post_meta($post_id, $field['id'], $new);
		} elseif ('' == $new && $old) {
			delete_post_meta($post_id, $field['id'], $old);
		}
	}
}

?>