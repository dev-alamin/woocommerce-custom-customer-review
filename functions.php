<?php 
defined( 'ABSPATH' ) || exit;

function get_wc_products_for_dropdown() {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$products      = get_posts( $args );
	$options       = array();
	$first_product = '';

	foreach ( $products as $index => $product ) {
		$options[ $product->ID ] = $product->post_title;
		if ( $index === 0 ) {
			$first_product = $product->ID;
		}
	}

	return array(
		'options'        => $options,
		'default_select' => $first_product,
	);
}
function vapehub_get_products_for_dropdown( WP_REST_Request $request ) {
    $page      = max( 1, intval( $request->get_param( 'page' ) ) );
    $per_page  = min( 100, intval( $request->get_param( 'per_page' ) ) ?: 20 );
    $search    = sanitize_text_field( $request->get_param( 'search' ) );

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
    );

    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    $query   = new WP_Query( $args );
    $options = [];

    foreach ( $query->posts as $post ) {
        $product = wc_get_product( $post->ID );
        if ( $product && $product->get_name() ) {
            $product_id   = $product->get_id();
            $product_name = esc_html( $product->get_name() );
            $review_count = intval( $product->get_review_count() );

            $options[ $product_id ] = sprintf(
                '<div class="">
                    <span class="text-gray-800 font-medium">%s</span>
                    <span class="text-xs bg-blue-100 text-blue-800 rounded-full px-2 py-0.5">%d</span>
                </div>',
                $product_name,
                $review_count
            );
        }
    }

    $total_count = $query->found_posts;

    return array(
        'options'        => $options,
        'default_select' => $page === 1 && ! empty( $options ) ? array_key_first( $options ) : '',
        'has_more'       => $page * $per_page < $total_count,
    );
}


add_action( 'rest_api_init', 'vapehub_register_product_dropdown_api' );

function vapehub_register_product_dropdown_api() {
    register_rest_route( 'vapehub/v1', '/products', array(
        'methods'             => 'GET',
        'callback'            => 'vapehub_get_products_for_dropdown',
        'permission_callback' => '__return_true',
        'args'                => array(
            'page'      => array(
                'type'    => 'integer',
                'default' => 1,
            ),
            'per_page'  => array(
                'type'    => 'integer',
                'default' => 20,
            ),
            'search'    => array(
                'type' => 'string',
                'default' => '',
            ),
        ),
    ) );
}

function moduleDropdownField( $name, $label, $options = array(), $selected = '', $xModel = '', $id = '', $extraClass = '' ) {
	$id              = $id ?: $name;
	$fieldKey        = esc_attr( $name );
	$fieldLabel      = esc_html( $label );
	$dropdownId      = esc_attr( $id );
	$xModelVar       = $xModel ?: 'selectedOption';
	$defaultSelected = esc_attr( $selected );

	// Flatten options as JS object string
	$optionsJS = '{' . implode(
		',',
		array_map(
			function ( $key, $value ) {
				return "'" . esc_attr( $key ) . "':'" . esc_attr( $value ) . "'";
			},
			array_keys( $options ),
			$options
		)
	) . '}';

	ob_start();
	?>
	<div 
        x-data="{ 
            open: false, 
            selected: '<?php echo $defaultSelected; ?>', 
            options: <?php echo $optionsJS; ?>,
        }"

		class="relative w-full! mb-6 <?php echo esc_attr( $extraClass ); ?>"
	>
		<label for="<?php echo $dropdownId; ?>" class="block text-sm font-semibold text-gray-800 mb-2">
			<?php echo $fieldLabel; ?>
		</label>

		<button
			type="button"
			@click="open = !open"
			class="w-full! flex items-center justify-between px-4 py-2.5 bg-white border border-gray-300 rounded-xl shadow-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
		>
			<span x-text="options[selected]"></span>
			<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
			</svg>
		</button>

        <ul
            x-show="open"
            @click.away="open = false"
            @keydown.escape.window="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg flex flex-wrap"
        >
            <!-- Standard options (excluding 'custom') -->
            <template x-for="[key, label] in Object.entries(options)" :key="key">
                <template x-if="key !== 'custom'">
                    <li 
                        @click="selected = key; reviewerName = label; open = false"
                        :class="{ 'bg-blue-100': selected === key }"
                        class="w-1/3 box-border px-4 font-bold py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer transition"
                        x-text="label"
                    ></li>
                </template>
            </template>

        </ul>

        <input type="hidden" id="<?php echo $dropdownId; ?>" name="<?php echo $fieldKey; ?>" :value="selected" />

	</div>
	<?php
	return ob_get_clean();
}

function moduleSelectTwo( $name, $label, $options = array(), $selected = '', $xModel = '', $id = '', $extraClass = '' ) {
	$id         = $id ?: $name;
	$fieldKey   = esc_attr( $name );
	$fieldLabel = esc_html( $label );
	$selectId   = esc_attr( $id );
	$selected   = esc_attr( $selected );
	$extraClass = esc_attr( $extraClass );
	$xModelAttr = $xModel ? 'x-model="' . esc_attr( $xModel ) . '"' : '';

	ob_start();
	?>
	<div class="w-full mb-6 <?php echo $extraClass; ?>">
		<label for="<?php echo $selectId; ?>" class="block text-sm font-medium text-gray-700 mb-1">
			<?php echo $fieldLabel; ?>
		</label>
        <input type="hidden" name="selectedProduct" x-model="selectedProduct" />
		<select name="<?php echo $fieldKey; ?>" id="<?php echo $selectId; ?>" <?php echo $xModelAttr; ?>
			class="select2-dropdown w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
			<?php foreach ( $options as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>>
					<?php echo esc_html( $value ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
	return ob_get_clean();
}


