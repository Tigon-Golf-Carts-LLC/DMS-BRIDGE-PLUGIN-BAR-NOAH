/**
 * DMS WooCommerce Product Page Injection
 * 
 * 1. Injects DMS cart images into the Product Image gallery widget
 * 2. Injects window sticker image above the product tabs
 * 3. Moves Extended Warranty add-on before the Add to Cart button
 * 4. Adds "Quantity" label next to quantity fields in addons
 */

(function() {
    'use strict';

    // Ensure we only run once
    if (window.dmsWooInjected) {
        return;
    }
    window.dmsWooInjected = true;

    // Check if data is available
    if (typeof dmsWooData === 'undefined' || !dmsWooData.cartId) {
        return;
    }

    const cartId = dmsWooData.cartId;
    const imageUrls = dmsWooData.imageUrls || [];
    // Local WordPress attachment URLs (already downloaded during sync)
    const localImageUrls = dmsWooData.localImageUrls || [];
    const hasLocalImages = localImageUrls.length > 0;

    // Get S3 URLs from API service (with fallback) — only used for window stickers
    // and as a last-resort fallback when no local images exist
    const s3CartBase = (window.DMSApiService && window.DMSApiService.s3Urls)
        ? window.DMSApiService.s3Urls.carts
        : 'https://s3.amazonaws.com/test.docs.s3/carts/'; // Fallback
    const s3StickerBase = (window.DMSApiService && window.DMSApiService.s3Urls)
        ? window.DMSApiService.s3Urls.windowStickers
        : 'https://s3.amazonaws.com/prod.docs.s3/cart-window-stickers/'; // Fallback
    const windowStickerUrl = s3StickerBase + cartId + '.pdf';

    /**
     * Find the WooCommerce Product Image gallery container
     */
    function findProductImageContainer() {
        // Try various selectors for WooCommerce product image areas
        const selectors = [
            '.woocommerce-product-gallery',
            '.product-images',
            '.woocommerce-product-gallery__wrapper',
            '.product .images',
            '.single-product-image',
            '.product-image-wrapper',
            '.elementor-widget-woocommerce-product-images',
            '.elementor-widget-woocommerce-product-images .elementor-widget-container',
            '.product-gallery',
            '.flex-viewport',
            '.woocommerce-product-gallery__image'
        ];

        for (const selector of selectors) {
            const el = document.querySelector(selector);
            if (el) {
                return el;
            }
        }

        return null;
    }

    /**
     * Find the Add to Cart button
     */
    function findAddToCartButton() {
        const selectors = [
            'button.single_add_to_cart_button',
            '.single_add_to_cart_button',
            'button[name="add-to-cart"]',
            '.add_to_cart_button',
            'form.cart button[type="submit"]',
            '.woocommerce-variation-add-to-cart button',
            '.cart button.button'
        ];

        for (const selector of selectors) {
            const el = document.querySelector(selector);
            if (el) {
                return el;
            }
        }

        return null;
    }

    /**
     * Find Extended Warranty add-on element
     */
    function findExtendedWarrantyAddon() {
        // First try data attribute
        let warranty = document.querySelector('[data-addon="extended-warranty"]');
        if (warranty) {
            return warranty;
        }

        // Fallback: search by label text
        const labels = document.querySelectorAll('.wc-pao-addon-name, .product-addon label, .wc-pao-addon-wrap label');
        for (const label of labels) {
            const text = label.textContent.toLowerCase();
            if (text.includes('extended warranty') || text.includes('warranty')) {
                const addonWrap = label.closest('.wc-pao-addon-wrap, .product-addon, .wc-pao-addon');
                if (addonWrap) {
                    return addonWrap;
                }
            }
        }

        // Try finding by heading
        const headings = document.querySelectorAll('.wc-pao-addon-name');
        for (const heading of headings) {
            const text = heading.textContent.toLowerCase();
            if (text.includes('extended warranty') || text.includes('warranty')) {
                const addonWrap = heading.closest('.wc-pao-addon-wrap, .product-addon, .wc-pao-addon');
                if (addonWrap) {
                    return addonWrap;
                }
            }
        }

        return null;
    }

    /**
     * Inject cart images into the Product Image gallery.
     *
     * When WooCommerce already has locally-downloaded images attached to the
     * product (featured + gallery), we leave them alone — no injection needed.
     * The DMS gallery injection only runs as a fallback when no local images
     * exist but we still have S3 filenames from the DMS payload.
     */
    function injectProductImages() {
        // If WooCommerce already has local images attached, skip injection
        // entirely — the native gallery works fine with downloaded images.
        if (hasLocalImages) {
            console.log('DMS: Local WooCommerce images present — skipping S3 gallery injection');
            return true;
        }

        if (!imageUrls.length) {
            return false;
        }

        const galleryContainer = findProductImageContainer();
        if (!galleryContainer) {
            console.log('DMS: Product image container not found');
            return false;
        }

        // Check if already injected
        if (document.querySelector('.dms-product-gallery')) {
            return true;
        }

        // Fallback: no local images, use S3 URLs from DMS payload
        console.log('DMS: No local images — injecting S3 gallery as fallback');

        // Build the gallery HTML
        let html = '<div class="dms-product-gallery">';

        // Main image (first image selected by default)
        const mainImageUrl = s3CartBase + imageUrls[0];
        html += '<div class="dms-product-main-image">';
        html += '<a href="' + mainImageUrl + '" target="_blank">';
        html += '<img src="' + mainImageUrl + '" alt="Cart Image" id="dms-main-product-image">';
        html += '</a>';
        html += '</div>';

        // ALL images as thumbnails (including first one)
        html += '<div class="dms-product-thumbnails">';
        imageUrls.forEach(function(imgFilename, index) {
            const fullUrl = s3CartBase + imgFilename;
            html += '<img src="' + fullUrl + '" alt="Image ' + (index + 1) + '" ';
            html += 'class="dms-product-thumb' + (index === 0 ? ' active' : '') + '" ';
            html += 'data-full-url="' + fullUrl + '">';
        });
        html += '</div>';

        html += '</div>';

        // Create element
        const galleryEl = document.createElement('div');
        galleryEl.innerHTML = html;

        // Hide original WooCommerce gallery content (but keep container for layout)
        const originalImages = galleryContainer.querySelectorAll('.woocommerce-product-gallery__image, .flex-viewport, .flex-control-thumbs, figure, .woocommerce-product-gallery__wrapper');
        originalImages.forEach(function(el) {
            el.style.display = 'none';
        });

        // Insert at the beginning of the gallery container
        galleryContainer.insertBefore(galleryEl.firstChild, galleryContainer.firstChild);

        // Add click handlers for thumbnails
        document.querySelectorAll('.dms-product-thumb').forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                const fullUrl = this.getAttribute('data-full-url');
                const mainImg = document.getElementById('dms-main-product-image');
                if (mainImg && fullUrl) {
                    mainImg.src = fullUrl;
                    mainImg.parentElement.href = fullUrl;

                    // Update active state
                    document.querySelectorAll('.dms-product-thumb').forEach(function(t) {
                        t.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });

        // Add zoom functionality to main image
        initImageZoom();

        console.log('DMS: Product images injected from S3 fallback');
        return true;
    }

    /**
     * Initialize hover zoom effect on main product image
     */
    function initImageZoom() {
        const container = document.querySelector('.dms-product-main-image');
        const img = document.getElementById('dms-main-product-image');
        
        if (!container || !img) {
            return;
        }

        // Prevent default link behavior on hover (only open on click outside zoom)
        const link = img.parentElement;
        if (link && link.tagName === 'A') {
            link.style.cursor = 'zoom-in';
        }

        // Create zoom lens overlay
        const zoomOverlay = document.createElement('div');
        zoomOverlay.className = 'dms-zoom-overlay';
        container.appendChild(zoomOverlay);

        // Mouse move handler for zoom effect
        container.addEventListener('mousemove', function(e) {
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Calculate percentage position
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            
            // Update zoom overlay background position
            zoomOverlay.style.backgroundImage = 'url(' + img.src + ')';
            zoomOverlay.style.backgroundPosition = xPercent + '% ' + yPercent + '%';
            zoomOverlay.style.opacity = '1';
            zoomOverlay.style.visibility = 'visible';
            
            // Hide original image on zoom
            img.style.opacity = '0';
        });

        // Mouse enter handler
        container.addEventListener('mouseenter', function() {
            zoomOverlay.style.backgroundImage = 'url(' + img.src + ')';
        });

        // Mouse leave handler
        container.addEventListener('mouseleave', function() {
            zoomOverlay.style.opacity = '0';
            zoomOverlay.style.visibility = 'hidden';
            img.style.opacity = '1';
        });

        // Update zoom image when thumbnail is clicked
        document.querySelectorAll('.dms-product-thumb').forEach(function(thumb) {
            thumb.addEventListener('click', function() {
                const fullUrl = this.getAttribute('data-full-url');
                if (fullUrl) {
                    zoomOverlay.style.backgroundImage = 'url(' + fullUrl + ')';
                }
            });
        });
    }

    /**
     * Create window sticker HTML (canvas for PDF rendering)
     */
    function createWindowStickerHTML() {
        let html = '<div class="dms-sticker-section">';
        html += '<div class="dms-sticker-frame">';
        html += '<canvas id="dms-sticker-canvas"></canvas>';
        html += '<div id="dms-sticker-loading">Loading window sticker...</div>';
        html += '</div>';
        html += '</div>';

        return html;
    }

    /**
     * Load PDF.js and render the window sticker as an image
     */
    function renderPDFAsImage() {
        const canvas = document.getElementById('dms-sticker-canvas');
        const loadingDiv = document.getElementById('dms-sticker-loading');
        
        if (!canvas) {
            return;
        }

        // Load PDF.js from CDN
        if (typeof pdfjsLib === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
            script.onload = function() {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                loadAndRenderPDF(canvas, loadingDiv);
            };
            document.head.appendChild(script);
        } else {
            loadAndRenderPDF(canvas, loadingDiv);
        }
    }

    /**
     * Load and render PDF to canvas (high quality)
     */
    function loadAndRenderPDF(canvas, loadingDiv) {
        pdfjsLib.getDocument(windowStickerUrl).promise.then(function(pdf) {
            // Get first page
            pdf.getPage(1).then(function(page) {
                // Use higher scale for better quality (2x resolution)
                const containerWidth = canvas.parentElement.offsetWidth || 600;
                const viewport = page.getViewport({ scale: 1 });
                const displayScale = containerWidth / viewport.width;
                const renderScale = displayScale * 2; // 2x for high DPI/retina quality
                const scaledViewport = page.getViewport({ scale: renderScale });

                // Set canvas size (high res)
                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;
                
                // Scale down display size via CSS
                canvas.style.width = containerWidth + 'px';
                canvas.style.height = (containerWidth * viewport.height / viewport.width) + 'px';

                // Render page to canvas
                const context = canvas.getContext('2d');
                page.render({
                    canvasContext: context,
                    viewport: scaledViewport
                }).promise.then(function() {
                    // Hide loading message
                    if (loadingDiv) {
                        loadingDiv.style.display = 'none';
                    }
                    
                    // Make canvas clickable
                    canvas.style.cursor = 'pointer';
                    canvas.title = 'Click to view full size';
                    canvas.addEventListener('click', function() {
                        openStickerPopup(canvas);
                    });
                    
                    console.log('DMS: Window sticker rendered as high-quality image');
                });
            });
        }).catch(function(error) {
            console.log('DMS: Error loading PDF:', error);
            if (loadingDiv) {
                loadingDiv.textContent = 'Could not load window sticker';
            }
        });
    }

    /**
     * Open sticker in a popup/lightbox
     */
    function openStickerPopup(canvas) {
        // Create popup overlay
        const overlay = document.createElement('div');
        overlay.className = 'dms-sticker-popup-overlay';
        overlay.innerHTML = `
            <div class="dms-sticker-popup">
                <button class="dms-sticker-popup-close">&times;</button>
                <div class="dms-sticker-popup-content">
                    <img src="${canvas.toDataURL('image/png')}" alt="Window Sticker">
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        
        // Close on overlay click
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay || e.target.classList.contains('dms-sticker-popup-close')) {
                closePopup();
            }
        });
        
        // Close on ESC key
        function handleEsc(e) {
            if (e.key === 'Escape') {
                closePopup();
            }
        }
        document.addEventListener('keydown', handleEsc);
        
        function closePopup() {
            document.body.removeChild(overlay);
            document.body.style.overflow = '';
            document.removeEventListener('keydown', handleEsc);
        }
    }

    /**
     * Find the WooCommerce product tabs container
     */
    function findProductTabsContainer() {
        const selectors = [
            '.woocommerce-tabs',
            '.wc-tabs-wrapper',
            '.product-tabs',
            '.woocommerce-product-tabs',
            '#woocommerce-tabs',
            '[class*="tabs"]'
        ];

        for (const selector of selectors) {
            const el = document.querySelector(selector);
            if (el) {
                return el;
            }
        }

        return null;
    }

    /**
     * Inject window sticker above the product tabs
     */
    function injectWindowSticker() {
        // Check if already injected
        if (document.querySelector('.dms-sticker-section')) {
            return true;
        }

        // Find the product tabs container
        const tabsContainer = findProductTabsContainer();
        
        if (!tabsContainer) {
            console.log('DMS: Product tabs container not found for sticker');
            // Fallback: try finding add to cart button
            const addToCartBtn = findAddToCartButton();
            if (addToCartBtn) {
                const stickerWrapper = document.createElement('div');
                stickerWrapper.className = 'dms-sticker-wrapper';
                stickerWrapper.innerHTML = createWindowStickerHTML();
                addToCartBtn.parentNode.insertBefore(stickerWrapper, addToCartBtn);
                renderPDFAsImage();
                console.log('DMS: Window sticker injected (fallback to Add to Cart)');
                return true;
            }
            return false;
        }

        // Create wrapper for the sticker
        const stickerWrapper = document.createElement('div');
        stickerWrapper.className = 'dms-sticker-wrapper';
        stickerWrapper.innerHTML = createWindowStickerHTML();

        // Insert before the tabs container
        tabsContainer.parentNode.insertBefore(stickerWrapper, tabsContainer);

        // Render the PDF as an image
        renderPDFAsImage();

        console.log('DMS: Window sticker injected above product tabs');
        return true;
    }

    /**
     * Add Quantity label next to quantity fields in addons
     */
    function addQuantityLabels() {
        // Find all quantity input fields (including hidden ones)
        // Target: input.qty (class="input-text qty text")
        // Search in both addon containers and main product area
        let addedCount = 0;
        
        // Function to process a quantity input
        function processQuantityInput(quantityInput, container) {
            // Check if label already added
            const parent = quantityInput.parentElement;
            if (parent.querySelector('.dms-quantity-label')) {
                return false;
            }

            // If input is hidden, change it to number type to make it visible
            if (quantityInput.type === 'hidden') {
                quantityInput.type = 'number';
                quantityInput.style.display = 'inline-block';
                quantityInput.style.visibility = 'visible';
                quantityInput.style.opacity = '1';
            }

            // Create label element
            const label = document.createElement('span');
            label.className = 'dms-quantity-label';
            label.textContent = 'Quantity';
            label.style.display = 'inline-block';
            label.style.visibility = 'visible';
            label.style.opacity = '1';

            // Insert label right before the quantity input
            quantityInput.parentNode.insertBefore(label, quantityInput);
            
            return true;
        }
        
        // First, try to find quantity fields in addon containers (both hidden and number types)
        const addonContainers = document.querySelectorAll('.wc-pao-addon-wrap, .product-addon, .wc-pao-addon');
        addonContainers.forEach(function(container) {
            const quantityInput = container.querySelector('input.qty[name="quantity"]');
            
            if (quantityInput && processQuantityInput(quantityInput, container)) {
                addedCount++;
            }
        });
        
        // Also search in main product form area (.quantity divs)
        const quantityDivs = document.querySelectorAll('.quantity');
        quantityDivs.forEach(function(quantityDiv) {
            // Skip if inside an addon container (already processed)
            if (quantityDiv.closest('.wc-pao-addon-wrap, .product-addon, .wc-pao-addon')) {
                return;
            }
            
            const quantityInput = quantityDiv.querySelector('input.qty[name="quantity"]');
            
            if (quantityInput && processQuantityInput(quantityInput, quantityDiv)) {
                addedCount++;
            }
        });

        if (addedCount > 0) {
            console.log('DMS: Added ' + addedCount + ' quantity label(s)');
            return true;
        }

        return false;
    }

    /**
     * Move Extended Warranty before Add to Cart button
     */
    function moveExtendedWarranty() {
        const warrantyAddon = findExtendedWarrantyAddon();
        const addToCartBtn = findAddToCartButton();

        if (!warrantyAddon) {
            console.log('DMS: Extended Warranty addon not found');
            return false;
        }

        // Check if already moved
        if (warrantyAddon.classList.contains('dms-warranty-moved')) {
            return true;
        }

        // Mark and style it
        warrantyAddon.classList.add('dms-extended-warranty-addon', 'dms-warranty-moved');
        warrantyAddon.setAttribute('data-addon', 'extended-warranty');

        // Always insert immediately before the Add to Cart container/button
        if (!addToCartBtn) {
            console.log('DMS: Add to Cart button not found; cannot reposition Extended Warranty');
            return false;
        }

        const addToCartContainer =
            addToCartBtn.closest('.woocommerce-variation-add-to-cart, form.cart, .cart-wrapper, .quantity-wrapper') || addToCartBtn;

        addToCartContainer.parentNode.insertBefore(warrantyAddon, addToCartContainer);

        console.log('DMS: Extended Warranty moved before Add to Cart');
        return true;
    }

    /**
     * Main injection function
     */
    function injectAll() {
        let imagesOk = injectProductImages();
        let stickerOk = injectWindowSticker();
        let warrantyOk = moveExtendedWarranty();
        let quantityLabelsOk = addQuantityLabels();

        return imagesOk && stickerOk;
    }

    /**
     * Initialize with retry logic
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attemptInjection);
        } else {
            attemptInjection();
        }
    }

    /**
     * Attempt injection with retries
     */
    function attemptInjection() {
        let attempts = 0;
        const maxAttempts = 15;
        const interval = 500;

        function tryInject() {
            attempts++;

            const success = injectAll();

            if (!success && attempts < maxAttempts) {
                setTimeout(tryInject, interval);
            }
        }

        // Initial delay to let page render
        setTimeout(tryInject, 300);
    }

    // Start
    init();

})();
