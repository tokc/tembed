function tembed_type() {
	$labels = array(
		'name' => 'Tembed',
		'singular_name' => 'Tembed',
		'add_new' => 'Add Tembed',
		'all_items' => 'All Tembeds',
		'add_new_item' => 'Add Tembed',
		'edit_item' => 'Edit Tembed',
		'new_item' => 'New Tembed',
		'view_item' => 'View Tembed',
		'search_item' => 'Search Tembeds',
		'not_found' => 'Tembed Not Found',
		'not_found_in_trash' => 'Tembed Not Found in Trash',
		'parent_item_colon' => 'Parent Tembed'
	);
	
	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'publicly_queryable' => true,
		'query_var' => true,
		'rewrite' => true,
		//'capability_type' => 'attachment',
		'hierarchical' => false,
		'supports' => array(
			'title',
			//'custom-fields',
			'thumbnail'
		),
		'menu_position' => 6,
		'exclude_from_search' => true
	);

	register_post_type('tembed', $args);
}

add_action('init', 'tembed_type');

function show_tembed_meta_box() {
	add_meta_box("imgurl", "Tembed", "populate_meta_box", "tembed");
}

add_action('add_meta_boxes', 'show_tembed_meta_box');

function populate_meta_box($post) {
	wp_nonce_field("save_imgurl", "save_imgurl_nonce");
	
	$base_url = get_post_meta($post->ID, 'url', true);
	$img_url = get_post_meta($post->ID, 'imgurl', true);
	$formatted_time = $post->post_title;

	// Input field for variable url.
	echo 'Base URL: <input type="text" name="base_url" value="';
	if ($base_url) echo $base_url;
	echo '"><br>';
	
	// Input field for variable imgurl.
	/*echo 'Img URL: <input type="text" name="imgurl" value="';
	if ($img_url) echo $img_url;
	echo '"><br>';*/

	echo 'Img URL: ';
	if ($img_url) echo $img_url;
	echo '<br>';

	// Input field for variable wp_formatted_time.
	/*echo 'Timestamp: <input type="text" name="wp_formatted_time" value="';
	if ($formatted_time) echo $formatted_time;
	echo '"><br>';*/
	echo 'Timestamp: ';
	if ($formatted_time) echo $formatted_time;
	echo '<br>';
}

function save_imgurl($post_id) {
	// Stuff to make sure that stuff is not bad stuff.
	if (isset($_POST['save_imgurl_nonce'])) {
		if (wp_verify_nonce($_POST['save_imgurl_nonce'], 'save_imgurl')) {
			if (current_user_can('edit_post', $post_id)) {

				// Update base URL.
				if (isset($_POST['base_url'])) {
					$old_url = get_post_meta($post_id, 'url', true);

					$new_url = $_POST['base_url'];

					if ($new_url != $old_url) {
						// If updated_url is set, update all the other meta values.
						$updated_url = $new_url;
						update_post_meta($post_id, 'url', $updated_url);
					}
				}

				if ($updated_url) {
					// Fetch webpage and extract image URL from it.
					$fetched_page = file_get_contents($updated_url);
					
					$dom = new DOMDocument;
					$dom->loadHTML($fetched_page);
					$nodes = $dom->getElementsByTagName("meta");
					foreach ($nodes as $node) {
						if ($node->getAttribute("property") == "og:image") {
							$raw_url = $node->getAttribute("content");
							$split_url = explode(":", $raw_url);
							// Recombine without the :large thing at the end.
							$imgurl_data = $split_url[0] . ":" . $split_url[1];

							update_post_meta($post_id, 'imgurl', $imgurl_data);
						}
					}
				
					// Update timestamp. Not possible with update_post_meta?

					// Extract timestamp from the fetched webpage.
					$time_tag = $dom->getElementsByTagName("span");
					foreach ($time_tag as $tag) {
						// Loop through every <span> and break if we hit one with "data-time".
						// This can extract the wrong time when there are multiple timestamps.
						if ($timestamp = $tag->getAttribute("data-time")) break;
					}
					if ($timestamp) {
						$dt = new DateTime("@$timestamp");
						$formatted_time = $dt->format('Y-m-d H:i:s');

						$arguments = array(
							'ID' => $post_id,
							'post_date' => $formatted_time,
							'post_date_gmt' => $formatted_time,
							'post_title' => $formatted_time
						);

						// For gosh's sake remember to do the unhook-hook thing here.
						remove_action('save_post', 'save_imgurl');
						wp_update_post($arguments);
						add_action('save_post', 'save_imgurl');
					}
				}
			}
		}
	}
}

add_action('save_post', 'save_imgurl');

function tembed_dashboard_css() {
	echo '<style>#imgurl {top: -50px;}</style>';
}

add_action('admin_head', 'tembed_dashboard_css');