/**
 * DMS Inventory Filtered - Frontend JavaScript
 * 
 * Handles:
 * - Fetching carts from /get-carts API with filters
 * - Rendering cart grid
 * - Filter sidebar interactions
 * - Pagination
 * 
 * @package DMS_Bridge
 */

(function ($) {
    'use strict';

    // Configuration from WordPress
    const config = window.dmsInventoryConfig || {
        baseUrl: '',
        pageSize: 20,
        comingSoonImage: 'https://tigongolfcarts.com/wp-content/uploads/2024/11/TIGON-GOLF-CARTS-IMAGES-COMING-SOON.jpg'
    };

    // Ensure DMSApiService is available
    if (typeof window.DMSApiService === 'undefined') {
        console.error('DMS Inventory: DMSApiService is not loaded. Make sure dms-api-service.js is enqueued before this script.');
    }

    // State management
    let state = {
        currentPage: 0,
        totalCarts: 0,
        totalPages: 1,
        priceSortASC: null, // null = default, true = low to high, false = high to low
        searchText: '', // Search text for API
        filters: {
            makes: [],
            models: [],
            colors: [],
            seats: [],
            driveTrain: [],
            batteryType: [],
            storeIds: [],
            isNew: null,
            isUsed: null,
            isElectric: null,
            isGas: null,
            isStreetLegal: null,
            isNotStreetLegal: null,
            isLifted: null,
            isNotLifted: null
        },
        locationsData: [],
        modelsData: [],
        colorsData: [],
        isLoading: false,
        isSidebarOpen: false
    };

    /**
     * Initialize the inventory widget
     */
    function init() {
        console.log('🚀 DMS Inventory: Init function called');
        
        const $root = $('#dms-inventory-root');
        console.log('Root element found:', $root.length);
        
        if (!$root.length) {
            console.error('DMS Inventory: Root element #dms-inventory-root not found!');
            return;
        }

        console.log('DMS Inventory: Initializing...');
        console.log('jQuery version:', $.fn.jquery);
        console.log('Document ready state:', document.readyState);

        // Render dynamic filters
        renderMakes();
        renderModelsHint(); // Show hint initially (no makes selected)
        renderColorsHint(); // Show hint initially (no makes selected)

        // Fetch and render locations
        fetchLocations();

        // Bind event handlers
        console.log('Binding filter events...');
        bindFilterEvents();
        console.log('Binding pagination events...');
        bindPaginationEvents();
        console.log('Binding sort events...');
        bindSortEvents();
        console.log('Binding search events...');
        bindSearchEvents();
        console.log('Binding reset filters...');
        bindResetFilters();
        console.log('Binding collapsible filters...');
        bindCollapsibleFilters();
        console.log('Binding sidebar toggle...');
        bindSidebarToggle();

        // Initial fetch
        console.log('Fetching initial carts...');
        fetchCarts();

        console.log('✅ DMS Inventory: Initialized successfully');
    }

    /**
     * Build request body from current state
     * Only includes non-empty/non-null values
     */
    function buildRequestBody() {
        const body = {
            pageNumber: parseInt(state.currentPage, 10),
            pageSize: parseInt(config.pageSize, 10)
        };

        // Search text - only send if not empty
        if (state.searchText && state.searchText.trim() !== '') {
            body.searchText = state.searchText.trim();
        }

        // Price sort - only send if not null (null = default sorting by latest)
        if (state.priceSortASC !== null) {
            body.priceSortASC = state.priceSortASC;
        }

        // Boolean filters - only send if true
        if (state.filters.isNew === true) {
            body.isNew = true;
        }
        if (state.filters.isUsed === true) {
            body.isUsed = true;
        }
        if (state.filters.isElectric === true) {
            body.isElectric = true;
        }
        if (state.filters.isGas === true) {
            body.isGas = true;
        }
        if (state.filters.isStreetLegal === true) {
            body.isStreetLegal = true;
        }
        if (state.filters.isNotStreetLegal === true) {
            body.isNotStreetLegal = true;
        }
        if (state.filters.isLifted === true) {
            body.isLifted = true;
        }
        if (state.filters.isNotLifted === true) {
            body.isNotLifted = true;
        }

        // Array filters - only send if non-empty (lowercase for backend simplicity)
        if (state.filters.makes.length > 0) {
            body.makes = state.filters.makes.map(m => m.toLowerCase());
        }
        if (state.filters.models.length > 0) {
            body.models = state.filters.models.map(m => m.toLowerCase());
        }
        if (state.filters.colors.length > 0) {
            body.colors = state.filters.colors.map(c => c.toLowerCase());
        }
        if (state.filters.seats.length > 0) {
            body.seats = state.filters.seats.map(s => s.toLowerCase());
        }
        if (state.filters.driveTrain.length > 0) {
            body.driveTrain = state.filters.driveTrain.map(d => d.toLowerCase());
        }
        if (state.filters.batteryType.length > 0) {
            body.batteryType = state.filters.batteryType.map(b => b.toLowerCase());
        }
        if (state.filters.storeIds.length > 0) {
            body.storeIds = state.filters.storeIds; // Keep as-is (e.g., ["T1", "T2"])
        }

        return body;
    }

    /**
     * Fetch carts from API
     */
    function fetchCarts() {
        if (state.isLoading) {
            return;
        }

        state.isLoading = true;
        showLoading(true);
        hideNoResults();

        const requestBody = buildRequestBody();

        window.DMSApiService.getCarts(requestBody, {
            timeout: 30000,
            success: function (response) {
                state.isLoading = false;
                showLoading(false);

                // Parse new API response format: { carts: [...], totalCarts: 123, counts: {...} }
                const carts = response?.carts || [];
                const totalCarts = response?.totalCarts || 0;
                
                // Calculate total pages
                state.totalCarts = totalCarts;
                state.totalPages = Math.ceil(totalCarts / config.pageSize);
                
                // Update filter counts from response
                updateFilterCounts(response);
                
                if (Array.isArray(carts) && carts.length > 0) {
                    renderCarts(carts);
                    renderPagination();
                    updateTitle(carts.length);
                } else {
                    renderCarts([]);
                    showNoResults();
                    updateTitle(0);
                }
            },
            error: function (xhr, status, error) {
                state.isLoading = false;
                showLoading(false);
                console.error('DMS Inventory: API error', error, xhr);
                showNoResults();
                updateTitle(0);
            }
        });
    }

    /**
     * Render carts in the grid
     */
    function renderCarts(carts) {
        const $grid = $('#dms-inventory-grid');
        $grid.empty();

        if (!carts || carts.length === 0) {
            return;
        }

        carts.forEach(function (cart) {
            const $card = createCartCard(cart);
            $grid.append($card);
        });
    }

    /**
     * Create a single cart card HTML
     */
    function createCartCard(cart) {
        const make = cart.cartType?.make || '';
        const model = cart.cartType?.model || '';
        const color = cart.cartAttributes?.cartColor || '';
        const retailPrice = cart.retailPrice || 0;
        const cartId = cart._id || '';
        const storeId = cart.cartLocation?.locationId || '';

        // Determine if cart is used - check both isUsed boolean and explicit true value
        const isUsed = cart.isUsed === true || cart.isUsed === 'true' || cart.isUsed === 1;
        
        // Build title with ® between make and model
        let titleParts = [];
        if (make && model) {
            titleParts.push(make + '® ' + model);
        } else if (make) {
            titleParts.push(make);
        } else if (model) {
            titleParts.push(model);
        }
        if (color) {
            titleParts.push(color);
        }
        
        // Get location from store using API data (not hardcoded)
        const locationString = getCityFromStoreId(storeId);
        const cartTitle = titleParts.join(' ') + (locationString ? ' In ' + locationString : '');
        
        // Format price
        const formattedPrice = '$' + retailPrice.toLocaleString('en-US', { 
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        });

        // Get image URL
        let imageUrl = config.comingSoonImage;
        if (cart.imageUrls && Array.isArray(cart.imageUrls) && cart.imageUrls.length > 0) {
            const s3BaseUrl = (window.DMSApiService && window.DMSApiService.s3Urls) 
                ? window.DMSApiService.s3Urls.carts 
                : 'https://s3.us-east-1.amazonaws.com/prod.docs.s3/carts/'; // Fallback
            imageUrl = s3BaseUrl + cart.imageUrls[0];
        }

        // Build inventory URL
        const inventoryUrl = config.baseUrl + '/dms/cart/' + cartId + '/';

        // Create card HTML
        const $card = $(`
            <article class="dms-inventory-cart">
                <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(cartTitle)}" loading="lazy">
                <div class="dms-cart-content">
                    <h3>${escapeHtml(cartTitle)}</h3>
                    <p class="cart-price">${escapeHtml(formattedPrice)}</p>
                    <a href="${escapeHtml(inventoryUrl)}" class="dms-see-details-btn">See Details</a>
                </div>
            </article>
        `);

        return $card;
    }

    /**
     * Get location string from store ID (for cart title)
     */
    /**
     * Get city and state abbreviation from store ID using API data
     * This replaces the old hardcoded getLocationFromStoreId function
     */
    function getCityFromStoreId(storeId) {
        if (!storeId || !state.locationsData || state.locationsData.length === 0) {
            return '';
        }

        // Find location by storeId
        const location = state.locationsData.find(function (loc) {
            const locStoreId = loc.storeId || loc.id || loc._id || '';
            return locStoreId === storeId;
        });

        if (location) {
            // Extract city from address.city or city property
            const city = (location.address && location.address.city) ||
                location.city ||
                location.locationName ||
                '';

            // Extract state and get abbreviation
            const stateFull = (location.address && location.address.state) || location.state || '';
            const stateAbbr = getStateAbbreviation(stateFull);

            // Return "City ST" format
            if (city && stateAbbr) {
                return city + ' ' + stateAbbr;
            }
            return city;
        }

        return '';
    }

    /**
     * Get state abbreviation from full state name
     */
    function getStateAbbreviation(stateName) {
        const stateMap = {
            'Alabama': 'AL', 'Alaska': 'AK', 'Arizona': 'AZ', 'Arkansas': 'AR',
            'California': 'CA', 'Colorado': 'CO', 'Connecticut': 'CT', 'Delaware': 'DE',
            'Florida': 'FL', 'Georgia': 'GA', 'Hawaii': 'HI', 'Idaho': 'ID',
            'Illinois': 'IL', 'Indiana': 'IN', 'Iowa': 'IA', 'Kansas': 'KS',
            'Kentucky': 'KY', 'Louisiana': 'LA', 'Maine': 'ME', 'Maryland': 'MD',
            'Massachusetts': 'MA', 'Michigan': 'MI', 'Minnesota': 'MN', 'Mississippi': 'MS',
            'Missouri': 'MO', 'Montana': 'MT', 'Nebraska': 'NE', 'Nevada': 'NV',
            'New Hampshire': 'NH', 'New Jersey': 'NJ', 'New Mexico': 'NM', 'New York': 'NY',
            'North Carolina': 'NC', 'North Dakota': 'ND', 'Ohio': 'OH', 'Oklahoma': 'OK',
            'Oregon': 'OR', 'Pennsylvania': 'PA', 'Rhode Island': 'RI', 'South Carolina': 'SC',
            'South Dakota': 'SD', 'Tennessee': 'TN', 'Texas': 'TX', 'Utah': 'UT',
            'Vermont': 'VT', 'Virginia': 'VA', 'Washington': 'WA', 'West Virginia': 'WV',
            'Wisconsin': 'WI', 'Wyoming': 'WY'
        };

        // Return abbreviation if found, otherwise return original (in case it's already abbreviated)
        return stateMap[stateName] || stateName;
    }

    /**
     * Render manufacturer checkboxes from config.makesData
     */
    function renderMakes() {
        const $container = $('#dms-filter-makes-options');
        if (!$container.length || !config.makesData) {
            return;
        }

        $container.empty();

        // Sort by recordOrder
        const sortedMakes = [...config.makesData].sort((a, b) => {
            return (a.recordOrder || 999) - (b.recordOrder || 999);
        });

        // Render checkboxes with count spans
        sortedMakes.forEach(function (make) {
            const label = make.label || '';
            const makeKey = make.key || label.toLowerCase().replace(/[^a-z0-9]/g, '_');
            const countKey = makeKey + '_count';
            const $label = $(`<label><input type="checkbox" value="${escapeHtml(label)}"> ${escapeHtml(label)}® <span class="dms-filter-count" data-count="${escapeHtml(countKey)}"></span></label>`);
            $container.append($label);
        });
    }

    /**
     * Show hint message for models filter (when no makes selected)
     */
    function renderModelsHint() {
        const $container = $('#dms-filter-models-options');
        const $modelsGroup = $('[data-filter="models"]');

        if (!$container.length) {
            return;
        }

        $container.empty();
        $container.append('<p class="dms-filter-hint">Select a make first</p>');
        $container.addClass('dms-filter-hidden');
        $modelsGroup.addClass('dms-filter-collapsed');
        $modelsGroup.find('.dms-toggle-icon').text('+');
    }

    /**
     * Show hint message for colors filter (when no makes selected)
     */
    function renderColorsHint() {
        const $container = $('#dms-filter-colors-options');
        const $colorsGroup = $('[data-filter="colors"]');

        if (!$container.length) {
            return;
        }

        $container.empty();
        $container.append('<p class="dms-filter-hint">Select a make first</p>');
        $container.addClass('dms-filter-hidden');
        $colorsGroup.addClass('dms-filter-collapsed');
        $colorsGroup.find('.dms-toggle-icon').text('+');
    }

    /**
     * Get selected make keys from selected make labels
     */
    function getSelectedMakeKeys() {
        const makesDataArray = config.makesData || [];
        return state.filters.makes.map(function (selectedLabel) {
            const makeObj = makesDataArray.find(m => m.label === selectedLabel);
            return makeObj ? makeObj.key : null;
        }).filter(Boolean);
    }

    /**
     * Fetch models from API based on selected makes
     */
    function fetchModelsForSelectedMakes() {
        const $container = $('#dms-filter-models-options');
        const $modelsGroup = $('[data-filter="models"]');

        if (!$container.length) {
            return;
        }

        // If no makes selected, show hint
        if (state.filters.makes.length === 0) {
            renderModelsHint();
            return;
        }

        const makeKeys = getSelectedMakeKeys();
        if (makeKeys.length === 0) {
            renderModelsHint();
            return;
        }

        // Show loading state
        $container.empty();
        $container.append('<p class="dms-filter-hint">Loading models...</p>');
        $container.removeClass('dms-filter-hidden');
        $modelsGroup.removeClass('dms-filter-collapsed');
        $modelsGroup.find('.dms-toggle-icon').text('−');

        // Fetch models from API
        window.DMSApiService.getCartModels(makeKeys, {
            timeout: 10000,
            success: function (response) {
                console.log('DMS Inventory: Models API response:', response);

                const models = Array.isArray(response) ? response : [];
                state.modelsData = models;

                renderModels(models);
            },
            error: function (xhr, status, error) {
                console.error('DMS Inventory: Failed to fetch models', error);
                $container.empty();
                $container.append('<p class="dms-filter-hint">Failed to load models</p>');
            }
        });
    }

    /**
     * Render model checkboxes from API data
     */
    function renderModels(models) {
        const $container = $('#dms-filter-models-options');
        const $modelsGroup = $('[data-filter="models"]');

        $container.empty();

        if (!models || models.length === 0) {
            $container.append('<p class="dms-filter-hint">No models available for selected makes</p>');
            return;
        }

        // Dedupe by label
        const seenLabels = new Set();
        models.forEach(function (model) {
            const label = model.label || '';
            if (label && !seenLabels.has(label)) {
                seenLabels.add(label);
                const isChecked = state.filters.models.includes(label) ? ' checked' : '';
                const $label = $(`<label><input type="checkbox" value="${escapeHtml(label)}"${isChecked}> ${escapeHtml(label)}</label>`);
                $container.append($label);
            }
        });

        // Remove any invalid model selections
        const validModelLabels = new Set(models.map(m => m.label).filter(Boolean));
        state.filters.models = state.filters.models.filter(function (modelLabel) {
            return validModelLabels.has(modelLabel);
        });

        // Auto-expand
        $container.removeClass('dms-filter-hidden');
        $modelsGroup.removeClass('dms-filter-collapsed');
        $modelsGroup.find('.dms-toggle-icon').text('−');
    }

    /**
     * Fetch colors from API based on selected makes
     */
    function fetchColorsForSelectedMakes() {
        const $container = $('#dms-filter-colors-options');
        const $colorsGroup = $('[data-filter="colors"]');

        if (!$container.length) {
            return;
        }

        // If no makes selected, show hint
        if (state.filters.makes.length === 0) {
            renderColorsHint();
            return;
        }

        const makeKeys = getSelectedMakeKeys();
        if (makeKeys.length === 0) {
            renderColorsHint();
            return;
        }

        // Show loading state
        $container.empty();
        $container.append('<p class="dms-filter-hint">Loading colors...</p>');
        $container.removeClass('dms-filter-hidden');
        $colorsGroup.removeClass('dms-filter-collapsed');
        $colorsGroup.find('.dms-toggle-icon').text('−');

        // Fetch colors from API
        window.DMSApiService.getCartColors(makeKeys, {
            timeout: 10000,
            success: function (response) {
                console.log('DMS Inventory: Colors API response:', response);

                const colors = Array.isArray(response) ? response : [];
                state.colorsData = colors;

                renderColors(colors);
            },
            error: function (xhr, status, error) {
                console.error('DMS Inventory: Failed to fetch colors', error);
                $container.empty();
                $container.append('<p class="dms-filter-hint">Failed to load colors</p>');
            }
        });
    }

    /**
     * Render color checkboxes from API data
     */
    function renderColors(colors) {
        const $container = $('#dms-filter-colors-options');
        const $colorsGroup = $('[data-filter="colors"]');

        $container.empty();

        if (!colors || colors.length === 0) {
            $container.append('<p class="dms-filter-hint">No colors available for selected makes</p>');
            return;
        }

        // Dedupe by color value
        const seenColors = new Set();
        colors.forEach(function (colorObj) {
            const color = colorObj.color || '';
            if (color && !seenColors.has(color)) {
                seenColors.add(color);
                const isChecked = state.filters.colors.includes(color) ? ' checked' : '';
                const $label = $(`<label><input type="checkbox" value="${escapeHtml(color)}"${isChecked}> ${escapeHtml(color)}</label>`);
                $container.append($label);
            }
        });

        // Remove any invalid color selections
        const validColors = new Set(colors.map(c => c.color).filter(Boolean));
        state.filters.colors = state.filters.colors.filter(function (color) {
            return validColors.has(color);
        });

        // Auto-expand
        $container.removeClass('dms-filter-hidden');
        $colorsGroup.removeClass('dms-filter-collapsed');
        $colorsGroup.find('.dms-toggle-icon').text('−');
    }

    /**
     * Fetch locations from /tigon-stores API
     */
    function fetchLocations() {
        console.log('DMS Inventory: Fetching locations from API service');

        window.DMSApiService.getTigonStores({
            timeout: 10000,
            success: function (response) {
                console.log('DMS Inventory: Locations API response:', response);

                // Handle different response structures
                let locations = [];
                if (Array.isArray(response)) {
                    locations = response;
                } else if (response && Array.isArray(response.data)) {
                    locations = response.data;
                } else if (response && Array.isArray(response.stores)) {
                    locations = response.stores;
                }

                console.log('DMS Inventory: Parsed locations:', locations);
                state.locationsData = locations;

                // Render location checkboxes
                renderLocations();
            },
            error: function (xhr, status, error) {
                console.error('DMS Inventory: Failed to fetch locations', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                // Still render the container with a message
                renderLocations();
            }
        });
    }

    /**
     * Render location checkboxes (show city names)
     */
    function renderLocations() {
        const $container = $('#dms-filter-locations-options');
        if (!$container.length) {
            console.warn('DMS Inventory: Location filter container not found');
            return;
        }

        $container.empty();

        console.log('DMS Inventory: Rendering', state.locationsData.length, 'locations');

        if (state.locationsData.length === 0) {
            $container.append('<p class="dms-filter-hint">No locations available</p>');
            return;
        }

        // Render checkboxes with storeId + city format and count spans
        let renderedCount = 0;
        state.locationsData.forEach(function (location, index) {
            // Extract city from address.city
            const city = (location.address && location.address.city) ||
                location.city ||
                location.locationName ||
                '';

            // Extract storeId
            const storeId = location.storeId ||
                location.id ||
                location._id ||
                '';

            if (city && storeId) {
                // Display format: "storeId + city" (e.g., "T1 Hatfield")
                const displayName = `${storeId} ${city}`;
                const countKey = storeId.toLowerCase() + '_count'; // e.g., t1_count
                const $label = $(`<label><input type="checkbox" value="${escapeHtml(storeId)}" data-city="${escapeHtml(city)}"> ${escapeHtml(displayName)} <span class="dms-filter-count" data-count="${escapeHtml(countKey)}"></span></label>`);
                $container.append($label);
                renderedCount++;
            }
        });

        if (renderedCount === 0 && state.locationsData.length > 0) {
            $container.append('<p class="dms-filter-hint">Locations data format issue</p>');
        }
    }

    /**
     * Render pagination controls
     */
    function renderPagination() {
        const $pagination = $('#dms-inventory-pagination');
        if (!$pagination.length) {
            return;
        }

        $pagination.empty();

        const currentPage = state.currentPage;
        const maxPages = state.totalPages;

        // Previous button
        const $prev = $('<button type="button" class="dms-page-btn dms-page-prev">&laquo; Prev</button>');
        if (currentPage === 0) {
            $prev.prop('disabled', true).addClass('disabled');
        }
        $pagination.append($prev);

        // Page numbers
        const pagesToShow = calculatePagesToShow(currentPage, maxPages);
        
        pagesToShow.forEach(function (pageNum) {
            if (pageNum === '...') {
                $pagination.append('<span class="dms-page-ellipsis">...</span>');
            } else {
                const $pageBtn = $(`<button type="button" class="dms-page-btn dms-page-num" data-page="${pageNum}">${pageNum + 1}</button>`);
                if (pageNum === currentPage) {
                    $pageBtn.addClass('active');
                }
                $pagination.append($pageBtn);
            }
        });

        // Next button
        const $next = $('<button type="button" class="dms-page-btn dms-page-next">Next &raquo;</button>');
        if (currentPage >= maxPages - 1) {
            $next.prop('disabled', true).addClass('disabled');
        }
        $pagination.append($next);
    }

    /**
     * Calculate which page numbers to show
     */
    function calculatePagesToShow(currentPage, maxPages) {
        const pages = [];
        const showEllipsis = maxPages > 7;

        if (!showEllipsis) {
            // Show all pages
            for (let i = 0; i < maxPages; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(0);

            if (currentPage > 3) {
                pages.push('...');
            }

            // Show pages around current
            const start = Math.max(1, currentPage - 1);
            const end = Math.min(maxPages - 2, currentPage + 1);

            for (let i = start; i <= end; i++) {
                if (!pages.includes(i)) {
                    pages.push(i);
                }
            }

            if (currentPage < maxPages - 4) {
                pages.push('...');
            }

            // Always show last page
            if (!pages.includes(maxPages - 1)) {
                pages.push(maxPages - 1);
            }
        }

        return pages;
    }

    /**
     * Bind filter checkbox events (using delegation for dynamic content)
     */
    function bindFilterEvents() {
        // Manufacturers (makes) - use delegation for dynamically rendered checkboxes
        $(document).on('change', '[data-filter="makes"] input[type="checkbox"]', function () {
            updateArrayFilter('makes', $(this).val(), $(this).is(':checked'));
            // Fetch models and colors from API when makes change
            fetchModelsForSelectedMakes();
            fetchColorsForSelectedMakes();
        });

        // Models - use delegation for dynamically rendered checkboxes
        $(document).on('change', '[data-filter="models"] input[type="checkbox"]', function () {
            updateArrayFilter('models', $(this).val(), $(this).is(':checked'));
        });

        // Locations - use delegation for dynamically rendered checkboxes (sends storeIds)
        $(document).on('change', '[data-filter="locations"] input[type="checkbox"]', function () {
            updateArrayFilter('storeIds', $(this).val(), $(this).is(':checked'));
        });

        // Condition (isNew, isUsed)
        $('[data-filter="condition"] input[type="checkbox"]').on('change', function () {
            const key = $(this).data('key');
            state.filters[key] = $(this).is(':checked') ? true : null;
            resetToFirstPage();
            fetchCarts();
        });

        // Fuel Type (isElectric, isGas)
        $('[data-filter="fuelType"] input[type="checkbox"]').on('change', function () {
            const key = $(this).data('key');
            state.filters[key] = $(this).is(':checked') ? true : null;

            // Show/hide Battery Type filter based on Electric selection
            if (key === 'isElectric') {
                if ($(this).is(':checked')) {
                    $('#dms-battery-type-filter').slideDown(200);
                } else {
                    $('#dms-battery-type-filter').slideUp(200);
                    // Clear battery type selections when Electric is deselected
                    state.filters.batteryType = [];
                    $('[data-filter="batteryType"] input[type="checkbox"]').prop('checked', false);
                }
            }

            resetToFirstPage();
            fetchCarts();
        });

        // Street Legal (isStreetLegal, isNotStreetLegal)
        $('[data-filter="streetLegal"] input[type="checkbox"]').on('change', function () {
            const key = $(this).data('key');
            state.filters[key] = $(this).is(':checked') ? true : null;
            resetToFirstPage();
            fetchCarts();
        });

        // Lifted (isLifted, isNotLifted)
        $('[data-filter="lifted"] input[type="checkbox"]').on('change', function () {
            const key = $(this).data('key');
            state.filters[key] = $(this).is(':checked') ? true : null;
            resetToFirstPage();
            fetchCarts();
        });

        // Seats
        $('[data-filter="seats"] input[type="checkbox"]').on('change', function () {
            updateArrayFilter('seats', $(this).val(), $(this).is(':checked'));
        });

        // Drive Train
        $('[data-filter="driveTrain"] input[type="checkbox"]').on('change', function () {
            updateArrayFilter('driveTrain', $(this).val(), $(this).is(':checked'));
        });

        // Battery Type
        $('[data-filter="batteryType"] input[type="checkbox"]').on('change', function () {
            updateArrayFilter('batteryType', $(this).val(), $(this).is(':checked'));
        });

        // Colors - use delegation for dynamically rendered checkboxes
        $(document).on('change', '[data-filter="colors"] input[type="checkbox"]', function () {
            updateArrayFilter('colors', $(this).val(), $(this).is(':checked'));
        });
    }

    /**
     * Update an array filter
     */
    function updateArrayFilter(filterName, value, isChecked) {
        if (isChecked) {
            if (!state.filters[filterName].includes(value)) {
                state.filters[filterName].push(value);
            }
        } else {
            state.filters[filterName] = state.filters[filterName].filter(v => v !== value);
        }
        resetToFirstPage();
        fetchCarts();
    }

    /**
     * Bind pagination events
     */
    function bindPaginationEvents() {
        $(document).on('click', '.dms-page-prev', function () {
            if (state.currentPage > 0) {
                state.currentPage--;
                fetchCarts();
                scrollToTop();
            }
        });

        $(document).on('click', '.dms-page-next', function () {
            if (state.currentPage < state.totalPages - 1) {
                state.currentPage++;
                fetchCarts();
                scrollToTop();
            }
        });

        $(document).on('click', '.dms-page-num', function () {
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page) && page !== state.currentPage) {
                state.currentPage = page;
                fetchCarts();
                scrollToTop();
            }
        });
    }

    /**
     * Bind sort dropdown events
     */
    function bindSortEvents() {
        $('#dms-sort-dropdown').on('change', function () {
            const value = $(this).val();

            // Set priceSortASC based on selection
            // default/empty = null (sort by latest)
            // price-asc = true (low to high)
            // price-desc = false (high to low)
            if (value === 'price-asc') {
                state.priceSortASC = true;
            } else if (value === 'price-desc') {
                state.priceSortASC = false;
            } else {
                state.priceSortASC = null;
            }

            console.log('Sort changed to:', value, '| priceSortASC:', state.priceSortASC);
            resetToFirstPage();
            fetchCarts();
        });
    }

    /**
     * Bind search bar events
     */
    function bindSearchEvents() {
        const $searchInput = $('#dms-search-input');
        const $searchBtn = $('#dms-search-btn');

        // Search on button click
        $searchBtn.on('click', function () {
            state.searchText = $searchInput.val();
            console.log('Search triggered:', state.searchText);
            resetToFirstPage();
            fetchCarts();
        });

        // Search on Enter key press
        $searchInput.on('keypress', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                state.searchText = $searchInput.val();
                console.log('Search triggered (Enter):', state.searchText);
                resetToFirstPage();
                fetchCarts();
            }
        });
    }

    /**
     * Bind reset filters button
     */
    function bindResetFilters() {
        $('#dms-reset-filters').on('click', function () {
            // Reset state
            state.filters = {
                makes: [],
                models: [],
                colors: [],
                seats: [],
                driveTrain: [],
                batteryType: [],
                storeIds: [],
                isNew: null,
                isUsed: null,
                isElectric: null,
                isGas: null,
                isStreetLegal: null,
                isNotStreetLegal: null,
                isLifted: null,
                isNotLifted: null
            };
            state.currentPage = 0;
            state.priceSortASC = null;
            state.searchText = '';
            state.modelsData = [];
            state.colorsData = [];

            // Uncheck all checkboxes
            $('#dms-filters-sidebar input[type="checkbox"]').prop('checked', false);

            // Reset search input
            $('#dms-search-input').val('');

            // Reset sort dropdown
            $('#dms-sort-dropdown').val('');

            // Hide battery type filter
            $('#dms-battery-type-filter').hide();

            // Re-render models and colors hints (since no makes selected)
            renderModelsHint();
            renderColorsHint();

            // Fetch with reset filters
            fetchCarts();
        });
    }

    /**
 * Bind sidebar toggle for mobile/tablet
 */
    function bindSidebarToggle() {
        console.log('=== DMS SIDEBAR TOGGLE DEBUG START ===');
        
        const $hamburger = $('.dms-filters-icon');
        const $toggle = $('#dms-filters-toggle');
        const $content = $('#dms-filters-content');
        const $arrow = $('.dms-filters-toggle-arrow');

        console.log('Hamburger icon found:', $hamburger.length);
        console.log('Toggle header found:', $toggle.length);
        console.log('Content element found:', $content.length);
        console.log('Arrow element found:', $arrow.length);
        console.log('Current window width:', $(window).width());

        if ($hamburger.length === 0) {
            console.error('DMS Inventory: .dms-filters-icon not found!');
            return;
        }

        if ($content.length === 0) {
            console.error('DMS Inventory: #dms-filters-content not found!');
            return;
        }

        // Function to toggle sidebar
        function toggleSidebar() {
            console.log('🔥 TOGGLE FUNCTION CALLED! 🔥');
            console.log('Content visible before toggle:', $content.is(':visible'));

            if ($content.is(':visible')) {
                console.log('→ Hiding content');
                $content.slideUp(200);
                $arrow.text('▼');
            } else {
                console.log('→ Showing content');
                $content.slideDown(200);
                $arrow.text('▲');
            }

            console.log('Content visible after toggle:', $content.is(':visible'));
        }

        // Unbind first to prevent double-binding
        $hamburger.off('click');
        
        // Bind click to hamburger icon specifically
        $hamburger.on('click', function (e) {
            console.log('🍔 HAMBURGER ICON CLICKED! 🍔');
            console.log('Event target:', e.target);
            console.log('Window width:', $(window).width());
            
            e.preventDefault();
            e.stopPropagation();
            
            toggleSidebar();
        });

        // Also bind to the entire header as backup
        $toggle.off('click').on('click', function (e) {
            console.log('🔥 HEADER CLICKED! 🔥');
            console.log('Event target:', e.target);
            
            // Only toggle on tablet/mobile
            if ($(window).width() <= 1024) {
                toggleSidebar();
            }
        });

        // Close sidebar when window is resized above tablet breakpoint
        $(window).on('resize', function () {
            console.log('Window resized to:', $(window).width());
            if ($(window).width() > 1024) {
                console.log('Desktop view - showing filters');
                $content.show();
                $arrow.text('▼');
            } else {
                console.log('Mobile view - checking state');
                if (!$content.is(':visible')) {
                    $arrow.text('▼');
                }
            }
        });

        // Initial state for mobile - hide content and set arrow
        if ($(window).width() <= 1024) {
            console.log('Initial load: Mobile view detected, hiding content');
            $content.hide();
            $arrow.text('▼');
        } else {
            console.log('Initial load: Desktop view, showing content');
            $content.show();
        }

        console.log('=== DMS SIDEBAR TOGGLE DEBUG END ===');
        console.log('Sidebar toggle bound successfully');
    }
    /**
     * Bind collapsible filter sections
     */
    function bindCollapsibleFilters() {
        $('.dms-filter-toggle').on('click', function () {
            const $group = $(this).closest('.dms-filter-group');
            const $options = $group.find('.dms-filter-options');
            const $icon = $(this).find('.dms-toggle-icon');

            $options.toggleClass('dms-filter-hidden');
            $group.toggleClass('dms-filter-collapsed');
            
            if ($options.hasClass('dms-filter-hidden')) {
                $icon.text('+');
            } else {
                $icon.text('−');
            }
        });
    }

    /**
     * Reset to first page (when filters change)
     */
    function resetToFirstPage() {
        state.currentPage = 0;
    }

    /**
     * Show/hide loading indicator
     */
    function showLoading(show) {
        const $loading = $('#dms-inventory-loading');
        const $grid = $('#dms-inventory-grid');

        if (show) {
            $loading.show();
            $grid.css('opacity', '0.5');
        } else {
            $loading.hide();
            $grid.css('opacity', '1');
        }
    }

    /**
     * Show no results message
     */
    function showNoResults() {
        $('#dms-inventory-no-results').show();
        $('#dms-inventory-pagination').hide();
    }

    /**
     * Hide no results message
     */
    function hideNoResults() {
        $('#dms-inventory-no-results').hide();
        $('#dms-inventory-pagination').show();
    }

    /**
     * Update the inventory title with count
     */
    function updateTitle(count) {
        const $title = $('.dms-inventory-title');
        if (count > 0) {
            const pageStart = (state.currentPage * config.pageSize) + 1;
            const pageEnd = Math.min(pageStart + count - 1, state.totalCarts);
            $title.text(`SHOWING ${pageStart}-${pageEnd} OF ${state.totalCarts} CARTS`);
        } else {
            $title.text('NO CARTS FOUND');
        }
    }

    /**
     * Update filter counts from API response
     * Shows counts next to each filter option
     */
    function updateFilterCounts(response) {
        // Clear all counts first
        $('.dms-filter-count').text('');
        
        if (!response) return;
        
        // Map of data-count attribute to response key
        const countMappings = {
            // Condition
            'new_count': response.new_count,
            'used_count': response.used_count,
            // Fuel Type
            'electric_count': response.electric_count,
            'gas_count': response.gas_count,
            // Street Legal
            'street_legal_yes_count': response.street_legal_yes_count,
            'street_legal_no_count': response.street_legal_no_count,
            // Lifted
            'lifted_yes_count': response.lifted_yes_count,
            'lifted_no_count': response.lifted_no_count,
            // Seats
            'utility_count': response.utility_count,
            '2_seat_count': response['2_seat_count'],
            '4_seat_count': response['4_seat_count'],
            '6_seat_count': response['6_seat_count'],
            '8_seat_count': response['8_seat_count']
        };
        
        // Update static filter counts
        Object.keys(countMappings).forEach(function(key) {
            const count = countMappings[key];
            if (count !== undefined && count !== null) {
                const $span = $('.dms-filter-count[data-count="' + key + '"]');
                $span.text('(' + count + ')');
            }
        });
        
        // Update location counts (T1, T2, etc.)
        if (state.locationsData && state.locationsData.length > 0) {
            state.locationsData.forEach(function(location) {
                const storeId = location.storeId || location.id || '';
                const countKey = storeId.toLowerCase() + '_count'; // e.g., t1_count
                const count = response[countKey];
                if (count !== undefined && count !== null) {
                    const $span = $('.dms-filter-count[data-count="' + countKey + '"]');
                    $span.text('(' + count + ')');
                }
            });
        }
        
        // Update makes counts
        if (config.makesData && config.makesData.length > 0) {
            config.makesData.forEach(function(make) {
                const makeKey = make.key || make.label.toLowerCase().replace(/[^a-z0-9]/g, '_');
                const countKey = makeKey + '_count'; // e.g., denago_count, club_car_count
                const count = response[countKey];
                if (count !== undefined && count !== null) {
                    const $span = $('.dms-filter-count[data-count="' + countKey + '"]');
                    $span.text('(' + count + ')');
                }
            });
        }
        
        console.log('DMS Inventory: Filter counts updated', response);
    }

    /**
     * Scroll to top of inventory grid
     */
    function scrollToTop() {
        const $root = $('#dms-inventory-root');
        if ($root.length) {
            $('html, body').animate({
                scrollTop: $root.offset().top - 100
            }, 300);
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initialize on document ready
    $(document).ready(function () {
        console.log('📄 Document ready - calling init()');
        init();
    });

    // Also initialize on Elementor frontend init (for editor preview)
    $(window).on('elementor/frontend/init', function () {
        console.log('🎨 Elementor frontend init');
        if (typeof elementorFrontend !== 'undefined') {
            elementorFrontend.hooks.addAction('frontend/element_ready/dms_inventory_filtered.default', function ($scope) {
                console.log('🎨 Elementor widget ready - calling init()');
                init();
            });
        }
    });

    // Log script load
    console.log('✅ DMS Inventory Filtered script loaded');

})(jQuery);
