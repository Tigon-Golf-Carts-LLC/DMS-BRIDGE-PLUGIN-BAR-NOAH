import {serialize, unserialize} from './node_modules/php-serialize/index.js';
import { Database_Object } from './database-object.js';
import { global } from './globals.js';

jQuery(document).ready(() => {
    jQuery(".body").attr('style', "display:flex;");

    jQuery(document).ready(function () {
        let query = '{"isUsed":true}';
        let endpoint = '/chimera/lookup';
        let dmsQuery = jQuery.ajax({
            type: "post",
            dataType: "json",
            url: global.ajaxurl,
            data: { action: "tigon_dms_query", query: query, endpoint: endpoint }
        });

        dmsQuery.then(cartList => {
            cartList.forEach(cart => {
                if (cart.serialNo) jQuery('#serial').append('<option value="' + cart.serialNo + '">');
                if (cart.vinNo) jQuery('#vin').append('<option value="' + cart.vinNo + '">');
            });
        });
    });

    jQuery("#chk-all-carts").on("change", e => {
        let checked = jQuery("#chk-all-carts").is(":checked");
        jQuery("#warning").attr('style', checked ? 'display:block;' : 'display:none');
        jQuery("#used button").html(checked ? 'Import All' : 'Import Selected');
        jQuery(".input-list").prop("disabled", checked);
    });

    jQuery(".tigon_dms_action").click(e => {
        jQuery(".tigon_dms_action button").prop('disabled', true);
        jQuery("#progress-bar").width(0);
        jQuery("#result-separator").attr('style', 'display:none;');
        jQuery("#result").html('');
        jQuery('#errors').html('');
    });

    function runPostImport() {
        return jQuery.ajax({
            type: "post",
            dataType: "json",
            url: global.ajaxurl,
            data: { action: "tigon_dms_post_import", query: query, endpoint: endpoint }
        });
    }

    jQuery("#used").click(e => {
        e.preventDefault();
        let totalCarts = 0;
        let count = 0;
        jQuery("#progress-text").html('Getting Carts...');


        // Function process cart to import format, callback update progress bar
        let usedConvert = function (targetCart) {
            let _id = targetCart._id;
            return jQuery.ajax({
                dataType: 'json',
                url: global.ajaxurl,
                data: { action: "tigon_dms_ajax_import_convert", data: JSON.stringify(targetCart) }
            })
                .catch(e => {
                    console.log(e);
                })
                .then(convertedCart => {
                    convertedCart = unserialize(convertedCart.data, {'Tigon\\DmsConnect\\Admin\\Database_Object': Database_Object});
                    if(convertedCart.hasOwnProperty("data")) {
                        convertedCart.data._id = _id;
                    }
                    count++;
                    jQuery("#progress-bar").width((Math.round(1000 * count / totalCarts) / 10) + '%');
                    jQuery("#progress-text").html('ID: ' + convertedCart.data.postmeta._sku.meta_value);
                    return Promise.resolve(convertedCart);
                });
        };

        // Function send cart to wc importer (create), callback update progress bar
        let createCart = function (targetCart) {
            let result = jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: global.ajaxurl,
                data: { action: "tigon_dms_ajax_import_create", data: targetCart }
            })
                .then(createdCart => {
                    count++;
                    jQuery("#progress-bar").width((Math.round(1000 * count / totalCarts) / 10) + '%');
                    jQuery("#progress-text").html('Created Product: ' + createdCart.pid);
                    return Promise.resolve(createdCart);
                });

            return result;
        };

        // Function send cart to wc importer (update), callback update progress bar
        let updateCart = function (targetCart) {
            let result = jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: global.ajaxurl,
                data: { action: "tigon_dms_ajax_import_update", data: targetCart }
            })
                .then(updatedCart => {
                    count++;
                    jQuery("#progress-bar").width((Math.round(1000 * count / totalCarts) / 10) + '%');
                    jQuery("#progress-text").html('Updated Product: ' + updatedCart.pid);
                    return Promise.resolve(updatedCart);
                });

            return result;
        };

        // Function send cart to wc importer (delete), callback update progress bar
        let deleteCart = function (targetCart) {
            let result = jQuery.ajax({
                type: "post",
                dataType: 'json',
                url: global.ajaxurl,
                data: { action: "tigon_dms_ajax_import_delete", data: targetCart }
            })
                .then(deletedCart => {
                    count++;
                    jQuery("#progress-bar").width((Math.round(1000 * count / totalCarts) / 10) + '%');
                    jQuery("#progress-text").html('Deleted Product: ' + deletedCart.post);
                    return Promise.resolve(deletedCart);
                });

            return result;
        };

        let usedCarts = function () {
            // Used cart fetch and convert
            let serial = jQuery("#txt-serial").val();
            let vin = jQuery("#txt-vin").val();
            let batch = jQuery("#chk-all-carts").is(":checked");
            let query;
            let endpoint = '/chimera/lookup';

            if (batch) {
                query = '{"isUsed":true}';
            } else {
                if (vin) query = '{"vinNo":"' + vin + '"}';
                else if (serial) query = '{"serialNo":"' + serial + '"}';
                else {
                    jQuery("#progress-text").html('No Cart Selected');
                    jQuery(".tigon_dms_action button").prop('disabled', false);
                    return;
                }
            }

            // Get carts list from DMS
            let dmsQuery = jQuery.ajax({
                type: "post",
                dataType: "json",
                url: global.ajaxurl,
                data: { action: "tigon_dms_query", query: query, endpoint: endpoint }
            });

            // Create array of convert promises, return Promise.all of array
            return dmsQuery.then(dmsCarts => {
                jQuery("#progress-text").html('Beginning Conversion...');
                totalCarts = 0;
                let conversionRequests = [];
                dmsCarts.forEach(dmsCart => {
                    conversionRequests.push(usedConvert(dmsCart));
                    totalCarts++;
                });
                totalCarts *= 2;

                return Promise.allSettled(conversionRequests);

            });
        };

        let allProcessed = usedCarts();

        // Create array of import promises, return Promise.allSettled of array
        let allImported = allProcessed.then(processedCartPromises => {
            jQuery("#progress-text").html('Preparing to Import...');

            const createRequests = [];
            const updateRequests = [];

            processedCartPromises.forEach(processedCartPromise => {
                if(processedCartPromise.status == "fulfilled") {
                    let processedCart = processedCartPromise.value;
                    let serializedCart = serialize(processedCart, {'Tigon\\DmsConnect\\Admin\\Database_Object': Database_Object});
                    if (processedCart.data.method == "create" && !processedCart.data.posts.hasOwnProperty("ID")) {
                        createRequests.push(createCart(serializedCart));
                    } else if (processedCart.data.method == "update" && processedCart.data.posts.hasOwnProperty("ID")) {
                        updateRequests.push(updateCart(serializedCart));
                    } else if (processedCart.data.method == "delete" && processedCart.data.posts.hasOwnProperty("ID") && processedCart.data.posts.ID) {
                        updateRequests.push(deleteCart(serializedCart));
                    }
                    else {
                        jQuery('#errors').append('<div>Not marked for import: ' + processedCart.data.postmeta._sku.meta_value + '</div>')
                        count++;
                    }
                }
            });

            return Promise.all([
                Promise.allSettled(createRequests),
                Promise.allSettled(updateRequests)
            ]);
        });

        allImported.catch(response => {
            jQuery("#progress-text").html('Import Complete');
            jQuery("#progress-bar").width('100%');
            jQuery(".tigon_dms_action button").prop('disabled', false);
            jQuery("#result-separator").attr('style', 'display:block;');
            jQuery("#result").html('Error');
            jQuery(".tigon_dms_action button").prop('disabled', false);
        });

        // Display import results
        allImported.then(([create, update]) => {
            const response = { create: create, update: update };
            let createIds = [];
            let updateIds = [];
            let deleteIds = [];
            if (response.hasOwnProperty("create")) response.create.forEach((item) => {
                var value = item?.value??{};
                if(item.hasOwnProperty("pid")) {
                    createIds.push(` <a href=${global.siteurl}/wp-admin/post.php?post=${value.pid}&action=edit&classic-editor target=_blank>${value.pid}</a>`);
                } else {
                    jQuery('#errors').append('<div>' + JSON.stringify(item) + '</div>')
                }
            });
            if (response.hasOwnProperty("update")) response.update.forEach((item) => {
                var value = item?.value??{};
                if(value.hasOwnProperty("pid")) {
                    updateIds.push(` <a href=${global.siteurl}/wp-admin/post.php?post=${value.pid}&action=edit&classic-editor target=_blank>${value.pid}</a>`);
                } else {
                    jQuery('#errors').append('<div>Post-import error: ' + JSON.stringify(item) + '</div>')
                }
            });
            if (response.hasOwnProperty("delete")) response.delete.forEach((item) => {
                var value = item?.value??{};
                if(value.hasOwnProperty("post")) {
                    deleteIds.push(` ${value.post}`);
                } else {
                    jQuery('#errors').append('<div>' + JSON.stringify(item) + '</div>')
                }
            });
            let ids = { "create": createIds.toString(), "update": updateIds.toString(), "delete": deleteIds.toString() };
            jQuery("#progress-text").html('Import Complete');
            jQuery("#progress-bar").width('100%');
            jQuery(".tigon_dms_action button").prop('disabled', false);
            jQuery("#result-separator").attr('style', 'display:block;');
            jQuery("#result").html(`
                <div class="result-item">
                    <div>Created: ${ids.create}</div>
                    <div>Updated: ${ids.update}</div>
                    <div>Deleted: ${ids.delete}</div>
                </div>`
            );
            runPostImport();
        });
    });

    jQuery("#new").click(function(e) {
        e.preventDefault();
        let totalCarts = 0;
        let count = 0;
        jQuery("#progress-text").html('Getting Carts...');

        let forcedFields = listForced();

        let query = '{"isUsed":false, "needOnWebsite": true, "isInBoneyard":false, "isInStock":true}';
        let endpoint = '/chimera/lookup';

        async function importNew(data) {
            let query = jQuery.ajax({
                type: "post",
                dataType: "json",
                url: global.ajaxurl,
                data: {
                    action: "tigon_dms_ajax_import_new",
                    data: JSON.stringify(data),
                    forced: JSON.stringify(forcedFields)
                }
			});
            return query.then(async (cart) => {
                count++;
                jQuery("#progress-bar").width((Math.round(1000 * count / totalCarts) / 10) + '%');
                jQuery("#progress-text").html('Created Product: ' + cart.pid);
                return Promise.resolve(cart);
            });
        }

        let dmsQuery = jQuery.ajax({
            type: "post",
            dataType: "json",
            url: global.ajaxurl,
            data: { action: "tigon_dms_query", query: query, endpoint: endpoint }
        });

        let allImported = dmsQuery.then(dmsCarts => {
            dmsCarts = dmsCarts.filter((cart) => {
                return !cart.isInBoneyard && cart.isInStock && cart.advertising.needOnWebsite
            });
            dmsCarts = dmsCarts.filter((cart) => {
                return !cart.serialNo.toUpperCase().includes("DELETE") && !cart.vinNo.toUpperCase().includes("DELETE")
            });
            dmsCarts = dmsCarts.filter((obj1, i, arr) => 
                arr.findIndex(obj2 => {
                    let url = obj2.advertising?.websiteUrl === obj1.advertising?.websiteUrl;
                    let make = obj2.cartType?.make === obj1.cartType?.make;
                    let model = obj2.cartType?.model === obj1.cartType?.model;
                    let year = obj2.cartType?.year === obj1.cartType?.year;
                    let cartColor = obj2.cartAttributes?.cartColor === obj1.cartAttributes?.cartColor;
                    let seatColor = obj2.cartAttributes?.seatColor === obj1.cartAttributes?.seatColor;
                    let location = obj2.cartLocation?.locationId === obj1.cartLocation?.locationId;
                    return make && model && year && cartColor && seatColor && location;
                }) === i
            );


            totalCarts = dmsCarts.length;
            const responses = [];

            async function recursiveImport() {
                if(dmsCarts.length > 0) {
                    var thisCart = dmsCarts.pop();
                    return await importNew(thisCart).then((cart) => {
                        responses.push(cart);
                        return recursiveImport(dmsCarts);
                    });
                } else {
                    return responses;
                }
            }

            return Promise.resolve(recursiveImport());
        });

        // Display import results
        allImported.then((responses) => {
            const pids = [];
            responses.forEach(response => {
                pids.push(` <a href=${global.siteurl}/wp-admin/post.php?post=${response.pid}&action=edit&classic-editor target=_blank>${response.pid}</a>`);
            });
            jQuery("#progress-text").html('Import Complete');
            jQuery("#progress-bar").width('100%');
            jQuery(".tigon_dms_action button").prop('disabled', false);
            jQuery("#result-separator").attr('style', 'display:block;');
            jQuery("#result").html(`
                <div class="result-item">
                    <div>PIDs: ${pids}</div>
                </div>`
            );
            runPostImport();
        });
    });

    jQuery("#recrop-all").click(e => {
        e.preventDefault();
        const nonce = jQuery("#recrop-panel").data('nonce');
        const batchSize = 10;
        let offset = 0;
        let totals = { processed: 0, skipped: 0, errors: 0 };

        jQuery(".tigon_dms_action button").prop('disabled', true);
        jQuery("#progress-bar").width(0);
        jQuery("#result-separator").attr('style', 'display:none;');
        jQuery("#result").html('');
        jQuery("#errors").html('');
        jQuery("#progress-text").html('Starting primary image recrop...');

        function finish(message) {
            jQuery("#progress-bar").width('100%');
            jQuery("#progress-text").html(message);
            jQuery(".tigon_dms_action button").prop('disabled', false);
            jQuery("#result-separator").attr('style', 'display:block;');
            jQuery("#result").html(
                '<div class="result-item">' +
                '<div>Cropped: ' + totals.processed + '</div>' +
                '<div>Skipped: ' + totals.skipped + '</div>' +
                '<div>Errors: ' + totals.errors + '</div>' +
                '</div>'
            );
        }

        function processBatch() {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: global.ajaxurl,
                data: {
                    action: "tigon_dms_recrop_primary_images",
                    nonce: nonce,
                    offset: offset,
                    batch_size: batchSize
                }
            }).then(response => {
                if (!response || !response.success) {
                    jQuery('#errors').append('<div>Batch failed at offset ' + offset + '</div>');
                    finish('Recrop failed');
                    return;
                }

                const data = response.data;
                totals.processed += data.processed || 0;
                totals.skipped   += data.skipped   || 0;
                totals.errors    += data.errors    || 0;

                const total = data.total || 0;
                const seen = data.offset || 0;
                const pct = total > 0 ? Math.min(100, Math.round((seen / total) * 1000) / 10) : 100;
                jQuery("#progress-bar").width(pct + '%');
                jQuery("#progress-text").html(
                    'Cropping primary images... ' + seen + ' / ' + total +
                    ' (' + data.width + '×' + data.height + ')'
                );

                if (data.done) {
                    finish('Primary image recrop complete');
                    return;
                }

                offset = data.offset;
                processBatch();
            }).catch(() => {
                jQuery('#errors').append('<div>Network error during recrop at offset ' + offset + '</div>');
                finish('Recrop failed');
            });
        }

        processBatch();
    });

    jQuery("#used-tab").click(e => {
        jQuery(".tigon-dms-tab").removeClass("active");
        jQuery("#used-tab").addClass("active");
        jQuery(".tabbed-panel .action-box").attr('style', 'display:none;');
        jQuery("#used-panel").attr('style', 'display:flex;');
    });

    jQuery("#new-tab").click(e => {
        jQuery(".tigon-dms-tab").removeClass("active");
        jQuery("#new-tab").addClass("active");
        jQuery(".tabbed-panel .action-box").attr('style', 'display:none;');
        jQuery("#new-panel").attr('style', 'display:flex;');
    });

    jQuery('.cb-container').on('click', e => {
        e.preventDefault();
        e.stopPropagation();
        let checkbox = jQuery(e.currentTarget).children('input').first();
        let state = checkbox.prop('checked');
        checkbox.prop('checked', !state);
        jQuery(e.currentTarget).toggleClass("active");

        if(state && jQuery('.cb-container.top-level').hasClass('active')) {
            jQuery('.cb-container.top-level').removeClass('active');
            jQuery('.cb-container.top-level input').prop('checked', false);
        }

        if(!state) {
            let cumulative = true;
            jQuery('.import-field').each(function() {
                cumulative = jQuery(this).hasClass('active');
                if(!cumulative) return false;
            });

            if(cumulative) {
                jQuery('.cb-container.top-level').addClass('active');
                jQuery('.cb-container.top-level input').prop('checked', true);
            }
        } 
    });

    jQuery('.cb-container.top-level').click (e => {
        let state = jQuery(e.currentTarget).hasClass('active');
        jQuery('.import-field').each(function() {
            jQuery(this).children('input').first().prop('checked', state);
            if(state) {
                jQuery(this).addClass('active');
            } else {
                jQuery(this).removeClass('active');
            }
        });
    });

    jQuery(".import-fields-head").click(e => {
        let caret = jQuery(e.currentTarget).children('.caret').first();
        caret.toggleClass("caret-down");

        jQuery(".checkbox-list").each(function() {
            jQuery(this).toggleClass('hidden');
        });
    });

    function listForced() {
        let strings = [];
        jQuery('.import-field.active').each(function() {
            strings.push(this.attributes.constant.value);
        });
        return strings;
    }
});