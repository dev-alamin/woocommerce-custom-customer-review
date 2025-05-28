<?php
defined('ABSPATH') || exit;

add_action('admin_menu', 'vh_wccr_admin_menu');

function vh_wccr_admin_menu() {
    add_menu_page(
        'Customer Reviews',          // Page title (top of the page)
        'Customer Reviews',          // Menu title (in the sidebar)
        'manage_woocommerce',        // Capability (or 'manage_options')
        'vh-wccr-customer-reviews',  // Menu slug (used in URL)
        'vh_wccr_admin_page_html',   // Callback to render page content
        'dashicons-testimonial',     // Menu icon
        56                           // Position (optional)
    );
}

function vh_wccr_admin_page_html() {
    $reviewer = [
        'james_smith'       => 'James Smith',
        'emily_johnson'     => 'Emily Johnson',
        'oliver_brown'      => 'Oliver Brown',
        'harry_evans'       => 'Harry Evans',
        'amelia_clarke'     => 'Amelia Clarke',
        'jack_wilson'       => 'Jack Wilson',
        'olivia_moore'      => 'Olivia Moore',
        'charlie_hughes'    => 'Charlie Hughes',
        'mia_walker'        => 'Mia Walker',
        'alfie_hall'        => 'Alfie Hall',
        'ava_green'         => 'Ava Green',
        'george_king'       => 'George King',
        'isla_baker'        => 'Isla Baker',
        'noah_wright'       => 'Noah Wright',
        'sophie_scott'      => 'Sophie Scott',
        'leo_morris'        => 'Leo Morris',
        'grace_wood'        => 'Grace Wood',
        'archie_thomas'     => 'Archie Thomas',
        'ruby_harris'       => 'Ruby Harris',
        'freddie_lewis'     => 'Freddie Lewis',
        'lily_clark'        => 'Lily Clark',
        'oscar_robinson'    => 'Oscar Robinson',
        'chloe_james'       => 'Chloe James',
        'theo_white'        => 'Theo White',
        'ella_martin'       => 'Ella Martin',
        'henry_jackson'     => 'Henry Jackson',
        'poppy_turner'      => 'Poppy Turner',
        'joshua_cooper'     => 'Joshua Cooper',
        'evie_hill'         => 'Evie Hill',
        'archie_edwards'    => 'Archie Edwards',
        'isabella_mitchell' => 'Isabella Mitchell',
        'lucas_carter'      => 'Lucas Carter',
        'daisy_phillips'    => 'Daisy Phillips',
        'logan_parker'      => 'Logan Parker',
        'sienna_adams'      => 'Sienna Adams',
        'ethan_collins'     => 'Ethan Collins',
        'millie_bennett'    => 'Millie Bennett',
        'alexander_watson'  => 'Alexander Watson',
        'rosie_wright'      => 'Rosie Wright',
        'mason_brooks'      => 'Mason Brooks',
        'holly_wood'        => 'Holly Wood',
        'finley_harrison'   => 'Finley Harrison',
        'lola_hughes'       => 'Lola Hughes',
        'benjamin_ward'     => 'Benjamin Ward',
        'matilda_cox'       => 'Matilda Cox',
        'samuel_richards'   => 'Samuel Richards',
        'erin_gray'         => 'Erin Gray',
        'jake_patel'        => 'Jake Patel',
        'scarlett_kelly'    => 'Scarlett Kelly',
        'harvey_barnes'     => 'Harvey Barnes',
        'florence_murray'   => 'Florence Murray'
    ];
    ?>
     <div class="vh-wccr-app text-gray-800 font-sans max-w-2xl mx-auto mt-10 bg-blue-100 rounded-lg shadow-md">
        <h1 class="text-3xl pt-5 pl-5 font-bold mb-6 text-blue-800 text-center uppercase">Customer Reviews</h1>

        <div x-data="reviewForm()" class="bg-white p-6 shadow-lg space-y-6 border border-gray-200">
            <?php
            echo moduleDropdownField(
                'reviewer_name',
                'Reviewer Name',
                $reviewer,
                'james_smith', // default
                'reviewerName' // x-model var
            );

            ?>

            <!-- Star Rating -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <div class="flex space-x-1">
                    <template x-for="i in 5" :key="i">
                    <svg @click="rating = i" :class="rating >= i ? 'text-yellow-400' : 'text-gray-300'"
                        class="w-12 h-12 cursor-pointer hover:text-yellow-500 transition-colors"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.19 3.674h3.862c.969 0 1.371 1.24.588 1.81l-3.124 2.27 1.19 3.674c.3.921-.755 1.688-1.54 1.117L10 13.347l-3.124 2.27c-.784.571-1.838-.196-1.54-1.117l1.19-3.674-3.124-2.27c-.784-.57-.38-1.81.588-1.81h3.862l1.19-3.674z" />
                    </svg>
                    </template>
                </div>
            </div>

            <!-- Review Text -->
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Review</label>
            <textarea x-model="review" rows="4"
                        class="block w-full p-3! border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none border! border-gray-300! rounded-xl! shadow-sm! text-sm! text-gray-800! focus:outline-none! focus:ring-2! focus:ring-blue-500! transition!"
                        placeholder="Write your review here..."></textarea>
            </div>

            <!-- Product Select -->
            <?php
            $product_data = get_wc_products_for_dropdown();
        
            echo moduleDropdownField(
                'selectedProduct',
                'Choose Product',
                $product_data['options'],
                $product_data['default_select'],
                'selectedProduct' // x-model var
            );
            ?>

            <!-- Review Date -->
            <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Review Date</label>
            <input type="date" x-model="reviewDate" id="reviewDate" name="reviewDate"
                    class="block w-full p-2! border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 border border-gray-300! rounded-xl! shadow-sm! text-sm! text-gray-800! focus:outline-none! focus:ring-2! focus:ring-blue-500! transition!" />
            </div>

            <!-- Submit Button -->
            <div>
            <button @click="submitForm"
                    type="button"
                    class=" inline-flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold text-sm rounded-full shadow-lg transform transition-all duration-200 ease-in-out hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    x-bind:disabled="loading">
                <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="loading ? 'Saving...' : 'Submit Review'"></span>
            </button>
            </div>

            <!-- Message -->
            <div x-text="message" class="text-green-600 font-semibold mt-2"></div>
        </div>
        </div>



    <script>
        function reviewForm() {
    return {
        reviewerName: '',
        rating: 0,
        review: '',
        selectedProduct: '', // matches your PHP xModel var
        reviewDate: '',
        message: '',
        loading: false,

        reviewerNames: ['James Smith', 'Emily Johnson', 'Oliver Brown', 'Isla Wilson', 'Harry Davies'],
        products: [
            { id: 1, name: 'Wireless Mouse' },
            { id: 2, name: 'Bluetooth Speaker' },
            { id: 3, name: 'USB-C Charger' },
        ],

        submitForm() {
            this.loading = true;
            this.message = '';

            fetch(WCCR_SCRIPTS.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'submit_wc_review',
                    nonce: WCCR_SCRIPTS.nonce,
                    name: this.reviewerName,
                    rating: this.rating,
                    review: this.review,
                    reviewDate: this.reviewDate,
                    selectedProduct: this.selectedProduct
                }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.message = 'Review submitted!';
                    this.name = '';
                    this.rating = 0;
                    this.review = '';
                    this.reviewDate,
                    this.selectedProduct = '';
                    this.reviewDate = '';
                } else {
                    this.message = data.data || 'Failed to submit review.';
                }
            })
            .catch(() => {
                this.message = 'Something went wrong.';
            })
            .finally(() => {
                this.loading = false;
            });
        }
    }
}


    </script>
    <?php
}

add_action('wp_ajax_submit_wc_review', 'wccr_handle_submit_review');

function wccr_handle_submit_review() {
    check_ajax_referer('generate_review_nonce', 'nonce');

    $name        = sanitize_text_field($_POST['name'] ?? '');
    $rating      = intval($_POST['rating'] ?? 0);
    $review      = sanitize_textarea_field($_POST['review'] ?? '');
    $product_id  = intval($_POST['selectedProduct'] ?? 0);
    $review_date = sanitize_text_field($_POST['reviewDate'] ?? '');

    if (!$name || !$review || !$product_id || $rating < 1 || $rating > 5) {
        wp_send_json_error('Invalid form data');
    }

    // Validate and format review date
    $comment_date = current_time('mysql');
    if ($review_date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $review_date)) {
        $comment_date = $review_date . ' 00:00:00';
    }

    // Insert comment as WooCommerce review
    $commentdata = array(
        'comment_post_ID'      => $product_id,
        'comment_author'       => ucwords(str_replace( '_', ' ', $name )),
        'comment_author_email' => 'demo@demo.com', // Optional: use dummy or map with reviewer ID
        'comment_content'      => $review,
        'comment_type'         => 'review',
        'comment_approved'     => 1, // or 0 for pending moderation
        'comment_date'         => $comment_date,
        'comment_date_gmt'     => get_gmt_from_date($comment_date),
    );

    $comment_id = wp_insert_comment($commentdata);

    if (is_wp_error($comment_id) || !$comment_id) {
        wp_send_json_error('Could not submit review');
    }

    // Set rating meta
    add_comment_meta($comment_id, 'rating', $rating);

    wp_send_json_success('Review submitted');
}
