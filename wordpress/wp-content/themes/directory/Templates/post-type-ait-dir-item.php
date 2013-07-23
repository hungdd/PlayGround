{extends $layout}

{block content}

<article id="post-item-category">

	<header class="entry-header">
	
		<h1 class="entry-title">
			<span>{!$term->name}</span>
			<span style="float:right">
				<a style="padding:0" href="#" id="list-view" title="Xem danh sách">
					<img src="<?php echo get_template_directory_uri() ?>/design/img/default-category-icon.png"/>
				</a>
				<a style="padding:0" href="#" id="map-view" title="Xem bản đồ">
					<img style="height:35px" src="<?php echo get_template_directory_uri() ?>/design/img/map-icon/default.png"/>
				</a>
			</span>
		</h1>

		<div class="category-breadcrumb clearfix">
			<span class="here">{__ 'You are here:'}</span>
			<span class="home"><a href="{!$homeUrl}">{__ 'Trang Chủ'}</a>&nbsp;&nbsp;&gt;</span>
			{foreach $ancestors as $anc}
			{first}<span class="ancestors">{/first}
				<a href="{!$anc->link}">{!$anc->name}</a>&nbsp;&nbsp;&gt;
			{last}</span>{/last}
			{/foreach}
			<span class="name"><a href="{!$term->link}">{!$term->name}</a></span>
			
			<?php $idx = 0; ?>
			{foreach $tax_queries as $tax}
				{first}&nbsp;&nbsp;&gt;<span class="predecessors">{/first}
					<a href="{!$tax->link}">{!$tax->name}</a>
					{if $idx < count($tax_queries) - 1}
						&nbsp;&#124;
					{/if}
					<?php $idx++; ?>
				{last}</span>{/last}
			{/foreach}
		</div>

	</header>

	<div id="directory-main-bar" data-category="{$mapCategory}" data-location="{$mapLocation}" data-search="{$mapSearch}" data-geolocation="{ifset $isGeolocation}true{else}false{/ifset}">
    </div>
	{include 'snippets/map-javascript.php'}

	<div class="category-items clearfix">
    <?php if ($is_all):?>
        
            <?php foreach ($posts as $item):?>
            <ul class="items">
			<li class="item clear <?php echo $item['packageClass'];?>">
				<div class="thumbnail">
					<img width="100" height="100" src="<?php echo $item['image'];?>" alt="<?php echo $item['title'];?>">
					<div class="comment-count"><?php echo $item['comment_count']; ?></div>
				</div>
				
				<div class="description">
					<h3>
						<a href="<?php echo $item['link'];?>"><?php echo $item['title'];?></a>
						<span class="rating">
							<?php for ($i = 1; $i <= $item['rating']['max']; $i++ ){
							     if ($i <= $item['rating']['val']) {
							          echo '<span class="star active"></span>';
							     } else {
							         echo '<span class="star"></span>';
							     }
								
                                }
							?>
						</span>

					</h3>
                    <p>
					<?php echo $item['excerpt'];?>
                    </p>
				</div>
			</li></ul>
            <?php endforeach;?>
		
    <?php else: ?>
		<ul n:foreach="$posts as $item" class="items">
			<li class="item clear{ifset $item->packageClass} {$item->packageClass}{/ifset}">
				{if $item->thumbnailDir}
				<div class="thumbnail">
					<img src="{timthumb src => $item->thumbnailDir, w => 100, h => 100}" alt="{__ 'Item thumbnail'}">
					<div class="comment-count">{$item->commentsCount}</div>
				</div>
				{/if}
				
				<div class="description">
					<h3>
						<a href="{!$item->link}">{$item->title}</a>
						{if $item->rating}
						<span class="rating">
							{for $i = 1; $i <= $item->rating['max']; $i++}
								<span class="star{if $i <= $item->rating['val']} active{/if}"></span>
							{/for}
						</span>
						{/if}
					</h3>
					{!$item->excerpt}
				</div>
			</li>
		</ul>
    <?php endif;?>
	</div>
	
	{willPaginate}
	<nav class="paginate-links">
		{paginateLinks true}
	</nav>
	{/willPaginate}

</article><!-- /#post-item-category -->

{ifset $themeOptions->advertising->showBox4}
<div id="advertising-box-4" class="advertising-box">
    {!$themeOptions->advertising->box4Content}
</div>
{/ifset}

<script type="text/javascript">
	jQuery(function(){
		jQuery('div#directory-main-bar').hide();
		jQuery('a#list-view').click(function(){
			mapCenter = map.getCenter();
			mapZoom = map.getZoom();
			jQuery('div#directory-main-bar').hide();
			jQuery('div.category-items').show();
			jQuery('nav.paginate-links').show();
		});
		
		jQuery('a#map-view').click(function(){
			jQuery('div#directory-main-bar').show();
			jQuery('div.category-items').hide();
			jQuery('nav.paginate-links').hide();
			google.maps.event.trigger(map, "resize");
			map.setZoom(mapZoom);
			map.setCenter(mapCenter);
		});
	});
</script>

{/block}