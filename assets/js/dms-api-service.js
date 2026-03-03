/**
 * DMS API Service - Centralized API calls
 * 
 * All API calls to tigondms.com endpoints are centralized here
 * 
 * @package DMS_Bridge
 */

(function ($) {
    'use strict';

    /**
     * DMS API Service Object
     */
    const DMSApiService = {
        /**
         * Base API URL
         */
        baseUrl: 'https://api.tigondms.com/wp-website',

        /**
         * S3 Bucket URLs
         */
        s3Urls: {
            carts: 'https://s3.amazonaws.com/test.docs.s3/carts/',
            windowStickers: 'https://s3.amazonaws.com/prod.docs.s3/cart-window-stickers/'
        },

        /**
         * API Endpoints
         */
        endpoints: {
            getCarts: '/get-carts',
            getCartModels: '/get-cart-models',
            getCartColors: '/get-cart-colors',
            getCartById: '/get-cart-by-id',
            getFeaturedCarts: '/get-featured-carts',
            tigonStores: '/tigon-stores'
        },

        /**
         * Get full URL for an endpoint
         * 
         * @param {string} endpointKey - Key from endpoints object
         * @return {string} Full URL
         */
        getUrl: function (endpointKey) {
            const endpoint = this.endpoints[endpointKey];
            if (!endpoint) {
                console.error('DMS API Service: Unknown endpoint key:', endpointKey);
                return null;
            }
            return this.baseUrl + endpoint;
        },

        /**
         * Fetch carts with filters and pagination
         * 
         * @param {Object} requestBody - Request body with filters, pagination, etc.
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getCarts: function (requestBody, options) {
            const defaults = {
                timeout: 30000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('getCarts'),
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestBody || {}),
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        },

        /**
         * Fetch cart models based on make keys
         * 
         * @param {Array} makeKeys - Array of make keys (e.g., ['denago', 'club_car'])
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getCartModels: function (makeKeys, options) {
            const defaults = {
                timeout: 10000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('getCartModels'),
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ makeKeys: makeKeys || [] }),
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        },

        /**
         * Fetch cart colors based on make keys
         * 
         * @param {Array} makeKeys - Array of make keys (e.g., ['denago', 'club_car'])
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getCartColors: function (makeKeys, options) {
            const defaults = {
                timeout: 10000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('getCartColors'),
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ makeKeys: makeKeys || [] }),
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        },

        /**
         * Fetch a single cart by ID
         * 
         * @param {string} cartId - Cart ID
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getCartById: function (cartId, options) {
            const defaults = {
                timeout: 20000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('getCartById'),
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ cartId: cartId }),
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        },

        /**
         * Fetch featured carts for a location
         * 
         * @param {string} key - Location key (e.g., 'national', 'tigon_hatfield')
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getFeaturedCarts: function (key, options) {
            const defaults = {
                timeout: 20000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('getFeaturedCarts'),
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ key: key || 'national' }),
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        },

        /**
         * Fetch store locations
         * 
         * @param {Object} options - Additional options (timeout, success, error callbacks)
         * @return {jqXHR} jQuery AJAX object
         */
        getTigonStores: function (options) {
            const defaults = {
                timeout: 10000,
                success: function () {},
                error: function () {}
            };
            const opts = $.extend({}, defaults, options || {});

            return $.ajax({
                url: this.getUrl('tigonStores'),
                type: 'GET',
                timeout: opts.timeout,
                success: opts.success,
                error: opts.error
            });
        }
    };

    // Make DMSApiService available globally
    window.DMSApiService = DMSApiService;

})(jQuery);

