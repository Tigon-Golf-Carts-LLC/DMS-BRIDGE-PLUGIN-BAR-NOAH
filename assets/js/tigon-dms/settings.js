jQuery(document).ready(function () {
    var ajaxurl = (typeof globals !== 'undefined' && globals.ajaxurl)
        ? globals.ajaxurl
        : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');

    var activeInput;
    var inputIndex;

    jQuery(".body").attr('style', "display:flex;flex-direction:column;");

    jQuery(".form input").click(function (e) {
        activeInput = jQuery(e.target);
    });

    jQuery(".form input").blur(function (e) {
        inputIndex = e.target.selectionEnd;
    });

    jQuery(".form input").on("input", function (e) {
        var regex = /{.+?}/g;
        var match;
        var values = [];
        while ((match = regex.exec(e.target.value)) !== null) {
            values.push({"value":match[0], "index":match.index});
        }

        console.log(values);
    });

    jQuery.ajax({
        dataType: 'json',
        url: ajaxurl,
        data: { action: "tigon_dms_get_dms_props", nonce: globals.nonce },
        complete: function(res) {
            jQuery("#dms-schema").html(res.responseText);
            jQuery(".caret").click(e => {
                jQuery(e.target).toggleClass("caret-down");
                jQuery(".nested", jQuery(e.target).parent()).first().toggleClass("active");
            });

            jQuery(".dms-value").click(e => {
                var data = e.target.getAttribute("code");
                activeInput.val((index, val) => {
                    newVal = val.substring(0, inputIndex) + data + val.substring(inputIndex);
                    return newVal;
                });
                activeInput.focus();
                document.activeElement.selectionStart = inputIndex + data.length;
                document.activeElement.selectionEnd = inputIndex + data.length;
                activeInput.trigger("input");
            });
        }
    });

    jQuery(".tigon_dms_save").click(e => {
        jQuery(".tigon_dms_action button").prop('disabled', true);
        var settings = {
            "github_token": jQuery("#txt-github-token").val(),
            "dms_url": jQuery("#txt-url").val(),
            "user_token": jQuery("#txt-api-key").val(),
            "file_source": jQuery("#txt-file-source").val(),
            // Schema templates
            "schema_name": jQuery("#schema-name").val(),
            "schema_slug": jQuery("#schema-slug").val(),
            "schema_image_name": jQuery("#schema-image-name").val(),
            "schema_monroney_name": jQuery("#schema-monroney-name").val(),
            "schema_description": jQuery("#schema-description").val(),
            "schema_short_description": jQuery("#schema-short-description").val(),
            // Cloudflare credentials (blank token = keep the saved value)
            "cf_zone_id": jQuery("#cf-zone-id").val(),
            "cf_api_token": jQuery("#cf-api-token").val()
        }

        jQuery.ajax({
            dataType: 'json',
            url: ajaxurl,
            data: { action: "tigon_dms_save_settings", nonce: globals.nonce, data: settings }
        }).then(response => {
            location.reload();
        });
    });

    // ── Tab switching ────────────────────────────────────────────────
    var tabIds = ["general", "urls", "endpoints", "schema", "locations", "cloudflare"];

    function activateTab(id) {
        jQuery(".tigon-dms-tab").removeClass("active");
        jQuery("#" + id + "-tab").addClass("active");
        jQuery(".tabbed-panel .action-box").attr("style", "display:none;");
        jQuery("#" + id).attr("style", "display:flex;");
        history.replaceState(undefined, "", "#" + id);
    }

    var hash = window.location.hash ? window.location.hash.substring(1) : "general";
    if (tabIds.indexOf(hash) === -1) hash = "general";
    activateTab(hash);

    tabIds.forEach(function (id) {
        jQuery("#" + id + "-tab").click(function () { activateTab(id); });
    });

    // ── Full Payload Schema ──────────────────────────────────────────
    jQuery("#fetch-schema-btn").click(function () {
        var btn = jQuery(this);
        var status = jQuery("#schema-status");
        var output = jQuery("#schema-output");

        btn.prop("disabled", true);
        status.text("Fetching carts from DMS API…");
        output.html('<p style="color:#888;text-align:center;padding:2rem 0;">Loading…</p>');

        jQuery.ajax({
            url: ajaxurl,
            method: "POST",
            dataType: "json",
            data: { action: "tigon_dms_get_full_schema", nonce: globals.nonce }
        }).done(function (res) {
            if (res.success) {
                status.text("Merged schema from " + res.data.cartCount + " carts.");
                output.empty();
                renderSchemaTree(res.data.schema, output[0]);
                // Attach caret toggle handlers
                output.find(".schema-caret").click(function () {
                    jQuery(this).toggleClass("schema-caret-open");
                    jQuery(this).next(".schema-nested").toggleClass("schema-nested-open");
                });
            } else {
                status.text("");
                output.html('<p style="color:#cf1010;text-align:center;padding:2rem 0;">' + (res.data && res.data.message ? res.data.message : 'Unknown error') + '</p>');
            }
        }).fail(function (xhr) {
            status.text("");
            output.html('<p style="color:#cf1010;text-align:center;padding:2rem 0;">Request failed (' + xhr.status + '). Check console for details.</p>');
        }).always(function () {
            btn.prop("disabled", false);
        });
    });

    function renderSchemaTree(schema, container) {
        var ul = document.createElement("ul");
        ul.className = "schema-tree";

        Object.keys(schema).forEach(function (key) {
            var value = schema[key];
            var li = document.createElement("li");
            li.className = "schema-node";

            if (value && value._v !== undefined) {
                // Leaf node
                var type = detectType(value._v);
                var samples = formatSamples(value._v, 6);
                li.className += " schema-leaf";
                li.innerHTML =
                    '<span class="schema-key">' + esc(key) + '</span> ' +
                    '<span class="schema-type schema-type-' + type.replace("?","") + '">' + type + '</span> ' +
                    '<span class="schema-samples">' + samples + '</span>';
            } else {
                // Object node — collapsible
                var count = Object.keys(value).length;
                var caret = document.createElement("span");
                caret.className = "schema-caret schema-caret-open";
                caret.innerHTML =
                    '<span class="schema-key">' + esc(key) + '</span> ' +
                    '<span class="schema-type schema-type-object">object</span> ' +
                    '<span class="schema-count">' + count + ' fields</span>';
                li.appendChild(caret);

                var nested = document.createElement("div");
                nested.className = "schema-nested schema-nested-open";
                renderSchemaTree(value, nested);
                li.appendChild(nested);
            }

            ul.appendChild(li);
        });

        container.appendChild(ul);
    }

    function detectType(values) {
        var types = {};
        values.forEach(function (v) {
            if (v === null || v === undefined) types["null"] = true;
            else if (typeof v === "boolean") types["boolean"] = true;
            else if (typeof v === "number") types["number"] = true;
            else types["string"] = true;
        });
        var keys = Object.keys(types);
        if (keys.length === 0) return "null";
        if (keys.length === 1) return keys[0];
        if (keys.length === 2 && types["null"]) {
            return keys.filter(function (k) { return k !== "null"; })[0] + "?";
        }
        return "mixed";
    }

    function formatSamples(values, max) {
        if (!values.length) return "<em>empty</em>";
        var shown = values.slice(0, max);
        var parts = shown.map(function (v) {
            if (v === null) return "<em>null</em>";
            if (v === true) return "<em>true</em>";
            if (v === false) return "<em>false</em>";
            if (typeof v === "number") return '<span class="schema-num">' + v + "</span>";
            var s = String(v);
            if (s.length > 40) s = s.substring(0, 37) + "…";
            return '<span class="schema-str">&quot;' + esc(s) + '&quot;</span>';
        });
        var html = parts.join(", ");
        if (values.length > max) html += " <em>…+" + (values.length - max) + " more</em>";
        return html;
    }

    function esc(str) {
        return String(str).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    // ── Cloudflare tab: live wp-config.php snippet + optional auto-write ──
    (function () {
        var zoneEl  = document.getElementById("cf-zone-id");
        var tokenEl = document.getElementById("cf-api-token");
        var snippet = document.getElementById("cf-wpconfig-snippet");
        if (!snippet) return;

        var savedZone = snippet.getAttribute("data-saved-zone") || "";

        function clean(value, allowDash) {
            var re = allowDash ? /[^A-Za-z0-9_\-]/g : /[^A-Za-z0-9]/g;
            return String(value || "").replace(re, "");
        }

        function buildSnippet() {
            var zone  = clean(zoneEl && zoneEl.value ? zoneEl.value : savedZone, false) || "your-zone-id";
            var token = clean(tokenEl && tokenEl.value, true) || "PASTE_YOUR_API_TOKEN_HERE";
            snippet.textContent =
                "define( 'TIGON_CF_ZONE_ID', '" + zone + "' );\n" +
                "define( 'TIGON_CF_API_TOKEN', '" + token + "' );";
        }

        buildSnippet();
        if (zoneEl)  zoneEl.addEventListener("input", buildSnippet);
        if (tokenEl) tokenEl.addEventListener("input", buildSnippet);

        var writeBtn    = document.getElementById("cf-write-wpconfig");
        var writeStatus = document.getElementById("cf-write-status");
        if (writeBtn) writeBtn.addEventListener("click", function () {
            if (!window.confirm("This edits wp-config.php directly. The plugin restores the file if anything looks wrong, but proceed only if it is writable. Continue?")) {
                return;
            }
            writeBtn.disabled = true;
            writeStatus.style.color = "#666";
            writeStatus.textContent = "Writing…";
            jQuery.ajax({
                url: ajaxurl,
                method: "POST",
                dataType: "json",
                data: {
                    action: "tigon_dms_write_cf_wpconfig",
                    nonce: globals.nonce,
                    // Blank fields fall back to the saved settings server-side.
                    data: {
                        cf_zone_id: zoneEl ? zoneEl.value.trim() : "",
                        cf_api_token: tokenEl ? tokenEl.value.trim() : ""
                    }
                }
            }).done(function (res) {
                if (res && res.success) {
                    writeStatus.style.color = "#1f7a3d";
                    writeStatus.textContent = "Written to wp-config.php. Reloading…";
                    setTimeout(function () { location.reload(); }, 1200);
                } else {
                    writeStatus.style.color = "#cf1010";
                    writeStatus.textContent = (res && res.data) ? res.data : "Write failed.";
                }
            }).fail(function () {
                writeStatus.style.color = "#cf1010";
                writeStatus.textContent = "Request failed.";
            }).always(function () {
                writeBtn.disabled = false;
            });
        });
    })();
});