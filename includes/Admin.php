<?php
namespace ADS\WCCR;

defined('ABSPATH') || exit;

use ADS\WCCR\NameList;

/**
 * Admin Menu for Customer Reviews
 */
class Admin {

    public function __construct(){

        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
    }

    /**
     * Initialize the admin menu
     *
     * @return void
     */
    public function add_admin_menu() {
        add_menu_page(
            'Customer Reviews',          // Page title (top of the page)
            'Customer Reviews',          // Menu title (in the sidebar)
            'manage_woocommerce',        // Capability (or 'manage_options')
            'vh-wccr-customer-reviews',  // Menu slug (used in URL)
            [ $this, 'admin_page_html' ],   // Callback to render page content
            'dashicons-testimonial',     // Menu icon
            56                           // Position (optional)
        );
    }

    public function admin_page_html() { ?>
     <div class="vh-wccr-app text-gray-800 font-sans max-w-2xl mx-auto mt-10 bg-blue-100 rounded-lg shadow-md">
        <div class="flex items-center justify-center pt-5 mb-6">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-200 mr-3">
            <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 17.25l-6.16 3.73 1.64-7.03L2 9.24l7.19-.61L12 2.5l2.81 6.13 7.19.61-5.48 4.71 1.64 7.03z"/>
            </svg>
            </span>
            <h1 class="text-3xl font-bold text-blue-800 text-center uppercase">Add Customer Reviews Ratings</h1>
        </div>

        <div x-data="reviewForm()" class="bg-white p-6 shadow-lg space-y-6 border border-gray-200">
            <?php
            echo moduleDropdownField(
                'reviewer_name',
                'Reviewer Name',
                NameList::get_names(),
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

            <div
                x-data="productDropdown()"
                x-init="fetchProducts()"
                class="relative"
            >
                <button
                    @click="open = !open"
                    class="w-full! flex items-center justify-between px-4 py-2.5 bg-white border border-gray-300 rounded-xl shadow-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                    x-text="options[selected] || 'Choose Product'"
                ></button>

               <ul
                x-show="open"
                @click.away="open = false"
                class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg flex flex-wrap max-h-96 overflow-y-auto"
            >
                <!-- Search Input -->
                <li class="w-full px-4 py-2">
                    <input
                        type="text"
                        placeholder="Search products..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none border! border-gray-300! rounded-xl! shadow-sm! text-sm! text-gray-800! focus:outline-none! focus:ring-2! focus:ring-blue-500! transition!"
                        x-model.debounce.500ms="searchTerm"
                        @input="searchProducts()"
                    />
                </li>

                <!-- Product Items -->
                <template x-for="(label, key) in options" :key="key">
                    <li 
                       @click="selected = key; $store.reviewForm.selectedProduct = key; open = false"
                        :class="{ 'bg-blue-100': selected === key }"
                        class="w-1/3 box-border px-4 py-2 text-sm font-bold text-gray-700 hover:bg-blue-50 cursor-pointer transition"
                        x-text="label"
                    ></li>
                </template>

                <!-- Load More Button -->
                <li class="w-full text-center py-2" x-show="hasMore">
                    <button
                        @click.stop="loadMore()"
                        class="text-blue-600 hover:underline text-sm"
                    >Load More</button>
                </li>
            </ul>


                <input type="hidden" name="selectedProduct" :value="selected" />
            </div>
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
            <div x-html="message" class="text-green-600 font-semibold mt-2"></div>
        </div>
        </div>

   <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('reviewForm', {
                selectedProduct: '',
            });
        });

        function productDropdown() {
            return {
                open: false,
                selected: '',
                options: {},
                hasMore: false,
                page: 1,
                per_page: 21,
                searchTerm: '',
                debounceTimer: null,

                fetchProducts(reset = false) {
                    const pageToFetch = reset ? 1 : this.page;
                    fetch(`/wp-json/vapehub/v1/products?page=${pageToFetch}&per_page=${this.per_page}&search=${encodeURIComponent(this.searchTerm)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (reset) {
                                this.options = data.options;
                                this.page = 2;
                            } else {
                                this.options = { ...this.options, ...data.options };
                                this.page++;
                            }
                            this.hasMore = data.has_more;
                            if (!this.selected && data.default_select) {
                                this.selected = data.default_select;
                            }
                        });
                },

                loadMore() {
                    this.fetchProducts();
                },

                watchSearchTerm() {
                    this.$watch('searchTerm', () => {
                        clearTimeout(this.debounceTimer);
                        this.debounceTimer = setTimeout(() => {
                            this.fetchProducts(true);
                        }, 300);
                    });
                },

                init() {
                    this.fetchProducts(true);
                    this.watchSearchTerm();
                }
            };
        }

        function reviewForm() {
            return {
                reviewerName: '',
                rating: 0,
                review: '',
                reviewDate: '',
                message: '',
                loading: false,

                submitForm() {
                    this.loading = true;
                    this.message = '';

                    fetch(WCCR_SCRIPTS.ajax_url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'submit_wc_review',
                            nonce: WCCR_SCRIPTS.nonce,
                            name: this.reviewerName,
                            rating: this.rating,
                            review: this.review,
                            reviewDate: this.reviewDate,
                            selectedProduct: Alpine.store('reviewForm').selectedProduct,
                        }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.message = data.success
                            ? (data.data || 'Review submitted successfully!')
                            : (data.data || 'Failed to submit review.');
                        if (data.success) {
                            this.reviewerName = '';
                            this.rating = 0;
                            this.review = '';
                            this.reviewDate = '';
                            Alpine.store('reviewForm').selectedProduct = '';
                        }
                    })
                    .catch(() => {
                        this.message = 'Something went wrong.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            };
        }
</script>

    <?php
    }
}