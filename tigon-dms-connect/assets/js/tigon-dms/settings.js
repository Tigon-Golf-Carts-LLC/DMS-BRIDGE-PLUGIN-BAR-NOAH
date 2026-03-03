import { global } from './globals.js';

jQuery(document).ready(() => {
    var activeInput;
    var inputIndex;

    jQuery(".body").attr('style', "display:flex;flex-direction:column;");

    jQuery(".form input").click(e => {
        activeInput = jQuery(e.target);
    });

    jQuery(".form input").blur(e => {
        inputIndex = e.target.selectionEnd;
    });

    jQuery(".form input").on("input", e => {
        var regex = /{.+?}/g;
        let match;
        var values = []; 
        while ((match = regex.exec(e.target.value)) !== null) {
            values.push({"value":match[0], "index":match.index});
        }

        console.log(values);
    });

    var dmsProps = jQuery.ajax({
        dataType: 'json',
        url: global.ajaxurl,
        data: { action: "tigon_dms_get_dms_props" },
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
            "file_source": jQuery("#txt-file-source").val()
        }

        jQuery.ajax({
            dataType: 'json',
            url: global.ajaxurl,
            data: { action: "tigon_dms_save_settings", data: settings }
        }).then(response => {
            location.reload();
        });
    });

    if(window.location.hash) {
        var hash = window.location.hash.substring(1);
        if(hash == "general") {
            jQuery("#general-tab").addClass("active");
            jQuery("#general").attr('style', 'display:flex;');
        }
        if(hash == "schema") {
            jQuery("#schema-tab").addClass("active");
            jQuery("#schema").attr('style', 'display:flex;');
        }
    } else {
        jQuery("#general-tab").addClass("active");
        jQuery("#general").attr('style', 'display:flex;');
    }


    jQuery("#general-tab").click(e => {
        jQuery(".tigon-dms-tab").removeClass("active");
        jQuery("#general-tab").addClass("active");
        jQuery(".tabbed-panel .action-box").attr('style', 'display:none;');
        jQuery("#general").attr('style', 'display:flex;');

        history.replaceState(undefined, '', "#general")
    });


    jQuery("#schema-tab").click(e => {
        jQuery(".tigon-dms-tab").removeClass("active");
        jQuery("#schema-tab").addClass("active");
        jQuery(".tabbed-panel .action-box").attr('style', 'display:none;');
        jQuery("#schema").attr('style', 'display:flex;');

        history.replaceState(undefined, '', "#schema")
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
            url: global.ajaxurl,
            method: "POST",
            dataType: "json",
            data: { action: "tigon_dms_get_full_schema" }
        }).done(function (res) {
            if (res.success) {
                status.text("Merged schema from " + res.data.cartCount + " carts.");
                output.empty();
                renderSchemaTree(res.data.schema, output[0]);
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
                var type = detectType(value._v);
                var samples = formatSamples(value._v, 6);
                li.className += " schema-leaf";
                li.innerHTML =
                    '<span class="schema-key">' + esc(key) + '</span> ' +
                    '<span class="schema-type schema-type-' + type.replace("?","") + '">' + type + '</span> ' +
                    '<span class="schema-samples">' + samples + '</span>';
            } else {
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
});