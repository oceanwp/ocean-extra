# Ocean Extra - Features Documentation

Complete documentation of all features available in Ocean Extra.

---

## Table of Contents

1. [Plugin Overview](#plugin-overview)
2. [License & Availability](#license--availability)
3. [OceanWP Panel](#oceanwp-panel)
4. [Setup Wizard](#setup-wizard)
5. [Demo Import](#demo-import)
6. [Page Settings (Metabox and Block Sidebar)](#page-settings-metabox-and-block-sidebar)
7. [My Library](#my-library)
8. [Widgets](#widgets)
9. [Menu Icons](#menu-icons)
10. [Site Preloader](#site-preloader)
11. [Fonts and Performance](#fonts-and-performance)
12. [Custom CSS/JS](#custom-cssjs)
13. [Open Graph Meta Tags](#open-graph-meta-tags)
14. [Admin Extras](#admin-extras)
15. [Premium Extensions Integration](#premium-extensions-integration)
16. [Admin Pages](#admin-pages)
17. [Display & Developer API](#display--developer-api)

---

## Plugin Overview

**Ocean Extra** is the free companion plugin for the OceanWP theme. It adds the OceanWP Panel settings, per-page settings, widgets, shortcodes, templates, a setup wizard with demo import, and the Freemius SDK that the theme and the premium Ocean extensions use for licensing.

| Property | Value |
|----------|-------|
| Version | 2.6.3 |
| Minimum WordPress | 5.9 |
| Minimum PHP | 7.4 (from `readme.txt`) |
| Tested up to | 7.1 |
| Text Domain | `ocean-extra` |
| Main class | `Ocean_Extra` (singleton, `Ocean_Extra()` helper) |
| Requires | OceanWP theme (or a child theme of it), header "OceanWP requires at least: 4.2.2". WooCommerce only for the Woo shortcodes. Elementor, Beaver Builder or SiteOrigin are optional for template rendering. |
| Distribution | Free on WordPress.org |

Almost every feature loads only when the active theme is OceanWP or a child theme whose template is `oceanwp`. With another theme the plugin does nearly nothing (the readme FAQ says the same).

### Key Capabilities

- OceanWP Panel panes: Customizer panel toggles, Customizer search, reset, import and export, integrations, Extra Settings and System Info
- Setup Wizard for new sites (branding, colors, typography, child theme, template import)
- Install Demo page to import full website templates from `demos.oceanwp.org`
- Per-post and per-page layout, header, title, footer and styling settings (classic metabox and block editor sidebar)
- My Library templates (`oceanwp_library` post type) with a shortcode
- 16 widgets, 15 shortcodes, menu icons, site preloader
- Local Google Fonts, Adobe Fonts (Typekit), SVG uploads, Custom JS
- Open Graph and X (Twitter) card meta tags
- Ships the Freemius SDK and the EDD to Freemius license migration code used by premium Ocean extensions

---

## License & Availability

Ocean Extra itself is free. The readme FAQ states: "there is no premium version of the Ocean Extra plugin itself."

It has no Freemius product of its own. It ships the Freemius SDK (`includes/freemius/`, version 2.13.5) and the OceanWP theme uses it to register the theme product (Freemius ID 3752, bundle ID 3767) through `owp_fs()`. Premium extensions register as add-ons of that theme product. See [Premium Extensions Integration](#premium-extensions-integration).

Pricing and bundle tiers are not listed here. The bundle page (`https://oceanwp.org/core-extensions-bundle/`) could not be fetched to confirm them.

### What Needs a Premium License

| Feature | Ocean Extra alone | Needs premium extension or license |
|---------|-------------------|-------------------------------------|
| OceanWP Panel panes listed in this document | ✓ | ✗ |
| Free website templates in the Setup Wizard and Install Demo | ✓ | ✗ |
| Pro website templates | ✗ | ✓ (the wizard shows a "Premium Template" notice and a "Get Pro Bundle?" link when no license is detected) |
| Cloudflare Turnstile pane | ✗ | ✓ (shown only with Ocean Popup Login 2.2.1+ or Ocean Elementor Widgets 2.4.7+) |
| White Label settings save | ✗ | ✓ (needs Ocean White Label) |
| Premium extension features (Sticky Header, Side Panel, and others) | ✗ | ✓ (each extension starts its features only when `can_use_premium_code__premium_only()` is true) |

The readme FAQ adds that demo content built with premium extensions is not displayed unless you have a premium license.

---

## OceanWP Panel

The OceanWP Panel page (`admin.php?page=oceanwp`, menu "OceanWP") belongs to the OceanWP theme. Ocean Extra fills many of its panes through `oceanwp_theme_panel_pane_<slug>` filters (class `Ocean_Extra_New_Theme_Panel`, `includes/themepanel/theme-panel.php`). Settings save over AJAX without a page reload.

| Theme Panel section | Panes added by Ocean Extra |
|---------------------|----------------------------|
| Home | Quick Settings |
| Customizer | Customizer Search, Customizer Controls, Import/Export, Reset |
| Install Demos | Install Demos switcher (and the legacy catalog, see below) |
| Integration | SVG Support, Google Maps, Google reCAPTCHA, Cloudflare Turnstile, MailChimp, Adobe Fonts |
| Extra Settings | Meta-box Settings, My Library, WordPress Widgets |
| System Info | System report |

### Home: Quick Settings

Cards with a "Go Now" button that open the Customizer focused on a panel, section or control. The card list comes from the theme's `owp_get_customizer_options()`.

### Customizer Pane

| Setting | Description | Default |
|---------|-------------|---------|
| Customizer Search | Turns the theme's Customizer search on or off. Stored as `customizer-search` inside `oe_panels_settings`. | Off |
| Customizer Controls | One switch per Customizer panel. Turning a panel off stops the theme from loading it. With OceanWP 4.0+: Site Style & Settings, Colors, Site Page Settings, Site Performance, SEO. Before 4.0: General. Always: Typography, Top Bar, Header, Blog, Sidebar, Footer Widgets, Footer Bottom, Custom CSS/JS. Stored in `oe_panels_settings`. | All on |
| Import Customizer Styling | Upload a `.dat` file made by Export. Applies every theme mod in it and the WordPress Additional CSS. | |
| Export Customizer Styling | Downloads all theme mods (widget and sidebar data skipped) plus Additional CSS as `<site>-oceanwp-export.dat`. The name can be changed with `ocean_export_filename`. | |
| Reset Customizer Settings | Deletes the `theme_mods_<theme name>` option, which resets all Customizer settings to theme defaults. | |

### Install Demos Pane

| Setting | Description | Default |
|---------|-------------|---------|
| Install Demos switch | Saves `oe_install_demos_active` (`yes`/`no`) and hides or shows the legacy demo catalog. | Yes |

The legacy catalog (`OceanWP_Demos`, `includes/panel/demos.php`) is no longer loaded by Ocean Extra (its `require` is commented out), so the theme does not print the catalog. Demo import now runs from the [Install Demo](#demo-import) page. The switch does not affect that page.

### Integration Pane

All integration fields save through `oceanwp_cp_save_integrations_settings` into options named `owp_<field>`.

| Setting | Option | Description |
|---------|--------|-------------|
| SVG Support | `oe_svg_support_active_status` | Allows SVG uploads to the Media Library. Uploaded SVGs are sanitized with the bundled `enshrined/svg-sanitize` library. If that library is missing the option is forced back to `no` and an admin notice is shown. Default `no`. |
| Google Maps API Key | `owp_google_map_api` | Used by the Google Map widget of Ocean Elementor Widgets. |
| reCAPTCHA version | `owp_recaptcha_version` | "Use default" (v2) or "Use reCAPTCHA v3". |
| reCAPTCHA v2 Site Key / Secret Key | `owp_recaptcha_site_key`, `owp_recaptcha_secret_key` | Used by the Ocean Elementor Widgets login, register and lost password widgets. |
| reCAPTCHA v3 Site Key / Secret Key | `owp_recaptcha3_site_key`, `owp_recaptcha3_secret_key` | Same, for v3. |
| Turnstile Site Key / Secret Key | `owp_turnstile_site_key`, `owp_turnstile_secret_key` | Used by Ocean Elementor Widgets and Ocean Popup Login. Pane shown only when one of those plugins is new enough. |
| Turnstile Render Method | `owp_turnstile_render_method` | Implicit or Explicit. |
| Turnstile Theme | `owp_turnstile_theme` | Auto, Light or Dark. |
| MailChimp API Key / Audience ID | `owp_mailchimp_api_key`, `owp_mailchimp_list_id` | Used by the MailChimp widget here and by newsletter widgets in Ocean Elementor Widgets, Ocean eCommerce and Ocean Popup Login. |
| Enable Adobe Fonts | `owp_adobe_fonts_integration` | Turns the Adobe Fonts module on. |
| Project ID | `owp_adobe_fonts_integration_project_id` | Adobe Fonts (Typekit) kit ID. On save the ID is checked against `typekit.com/api/v1/json/kits/<id>/published`. A wrong ID returns "Project ID is wrong." The font list is cached in `oe-adobe-fonts`. |
| Enable for Customizer | `owp_adobe_fonts_integration_enable_customizer` | Adds the kit fonts to the theme's Customizer font lists and loads the kit CSS in the block editor. |
| Enable for Elementor | `owp_adobe_fonts_integration_enable_elementor` | Adds an Adobe Fonts group to Elementor's font picker. |

When either Adobe Fonts mode is on, the kit stylesheet (`use.typekit.net/<id>.css`) loads on the front end.

### Extra Settings Pane

| Setting | Option | Description | Default |
|---------|--------|-------------|---------|
| Meta-box Settings | `oe_metabox_posttypes_settings` | One switch per post type that gets the Page Settings metabox: post, page, product, `elementor_library`, plus `ae_global_templates` when AnyWhere Elementor is active. | All on until first save |
| My Library | `oe_library_active_status` | Turns the [My Library](#my-library) post type on or off. | `yes` |
| WordPress Widgets | `oe_widgets_settings` | One switch per Ocean widget, plus a bulk switch. A turned off widget is not registered. | All on until first save |

### System Info Pane

A "Get System Report" button and tables for WordPress Environment, Theme, Server Environment (PHP limits, MySQL, PHP extensions, HTTP requests, communication with oceanwp.org) and Active Plugins. Data comes from `OceanWP_Theme_Panel_System_Status::compile_system_status()`.

### Admin Settings (theme pane, Ocean Extra options)

The theme's Admin Settings pane saves options that Ocean Extra reads:

| Option | Effect in Ocean Extra |
|--------|-----------------------|
| `oe_notification_active_status` | The OceanWP admin bar menu and notifications load only while this is `no` (default `no`). |
| `oe_display_front_end_style_editor_active_status` | Site-wide default for the per-post `ocean_front_end_style_editor` meta. |

---

## Setup Wizard

A React wizard for new sites (`OE_Onboarding_Wizard`, `includes/onboarding/setup-wizard/`). It appears as **OceanWP > Setup Wizard** for users with `manage_options`. The app is mounted in the wp-admin footer and opened from that menu link (`#setup-wizard`).

It is hidden once `owp_onboarding_completed` or `oceanwp_plugin_notice_permanently_dismissed` is set. On sites that update from an older Ocean Extra version (installed version lower than current), both flags are set automatically and the wizard never shows.

### Steps

The steps below come from the compiled app's screens (the React source is not in the repo).

| Step | What the site owner does |
|------|--------------------------|
| How do you want to build your site | Choose From Scratch or From Website Templates, then New Site or Existing Website. |
| Prepare for import (Existing Website) | Optional cleanup: delete pages, posts, media and menus, reset Customizer settings, disable all plugins except Ocean Extra, install the child theme. |
| Select a template | Browse and search the remote template list by builder and category. Pro templates carry a "Pro" badge. |
| Choose content | Pick Site Content (pages, posts, menus, templates, media), Customizer Styling, Widgets and the WPForms contact form. See required, optional and suggested plugins and install them. |
| Branding | Site title, tagline, site icon, logo, retina logo and mobile logo. |
| Colors | Pick a palette or keep the template colors. Saves background, primary, primary hover, border, link and link hover colors as theme mods. |
| Typography | Pick a preset or keep the defaults. Saves `headings_typography` and `body_typography`. |
| Child Theme | Install and activate `oceanwp-child-theme-master` (downloaded from `downloads.oceanwp.org`, or generated locally as a fallback). |
| Finish | Optional email subscription (sent to OceanWP's Mautic form), then "Finish and Launch My Website", which sets `owp_onboarding_completed`. |

The wizard also stores the chosen light or dark UI mode in the `oe_onboarding_color_mode` transient (30 days).

---

## Demo Import

The **OceanWP > Install Demo** page (`OE_Install_Demo`) opens the same React template browser without the wizard steps. It is always available to users with `manage_options`.

| Item | Behavior |
|------|----------|
| Template list | Fetched from `https://demos.oceanwp.org/1wizard-installation/demos.json`, with a fallback URL, cached for one day in `ocean_onboarding_template_data`. A "sync" REST call forces a refresh. |
| Plugin list | Fetched from `demo-plugins.json` on the same host, cached for one day in `ocean_onboarding_plugin_data`. |
| Import parts | `content` (`sample-data.xml`), `customizer` (`oceanwp-export.dat`), `widgets` (`widgets.wie`), `form` (`form.json`, WPForms). Each part downloads from `https://demos.oceanwp.org/<slug>/` into `uploads/sample-data/` and is deleted after import. |
| After import | Maps menus to theme locations, sets the front page and posts page by title, sets posts per page, sets Elementor container width and turns off Elementor color and typography schemes and local Google Fonts. For shop templates with WooCommerce active it assigns the WooCommerce pages and image sizes. |
| Pro templates | When the template plan is `pro` and the Freemius pricing page is visible (no detected license), the app shows the Premium Template notice and a Get Pro Bundle link. |

---

## Page Settings (Metabox and Block Sidebar)

Per-post control over layout, header, title, footer and styling, independent from the global Customizer settings.

- **Block editor:** a sidebar named "Page Settings & Styling" (`oceanwp-settings` plugin), built from `includes/post-settings/`.
- **Classic editor:** a Butterbean metabox (`includes/metabox/metabox.php`), loaded only when the block editor is not in use.

Both read and write the same `ocean_*` post meta keys. They require the `manage_options` capability (filter `ocean_main_metaboxes_capabilities`). They appear on post, page, product, `oceanwp_library`, `elementor_library` and `ae_global_templates`, limited by the Meta-box Settings switches. A REST route resets one post's settings to defaults.

| Tab | Settings |
|-----|----------|
| Main | Content Layout (Default, Right Sidebar, Left Sidebar, Full Width, 100% Full Width, Both Sidebars), Both Sidebars style and widths, Sidebar, Second Sidebar, Paddings, Custom Body Class |
| Shortcodes | Shortcode before or after the Top Bar, Header, Title, Footer Widgets and Footer Bottom (10 fields) |
| Header | Display Top Bar, Display Header, Header Style (Minimal, Transparent, Top Menu, Full Screen, Center, Medium, Vertical, Custom Header), Left Menu (Center style), Custom Header template from My Library |
| Logo | Logo, Retina Logo, max width and max height for desktop, tablet and mobile |
| Menu | Main Navigation Menu, menu typography, link colors and backgrounds (normal, hover, current), Simple Social colors |
| Title | Display Page Title, Display Heading, Custom Title, Subheading, Title Style, background color or image (position, attachment, repeat, size), height, overlay opacity and color |
| Breadcrumbs | Display Breadcrumbs, text, separator and link colors |
| Footer | Display Footer Widgets Area, Display Copyright Area, Custom Footer template from My Library |
| Post | oEmbed URL, Self Hosted media, Embed Code, Link and Link Target (link format), Quote and Quote Link (quote format) |

A separate **Gallery** metabox (`ocean_gallery_id`, `ocean_gallery_link_images`) adds gallery images and a lightbox option to posts.

Per-post `ocean_front_end_style_editor` (`yes`/`no`) overrides the site-wide front-end style editor setting.

`wpml-config.xml` tells WPML to copy most of these meta keys once when a translation is created.

---

## My Library

Custom templates for headers, footers, preloaders, premium extensions (the panel description names Ocean Footer Callout and Ocean Hooks) and shortcodes. Built with any editor or page builder.

| Item | Detail |
|------|--------|
| Where | **OceanWP > My Library** (`edit.php?post_type=oceanwp_library`) |
| Post type | `oceanwp_library`, shown in REST, not searchable, no rewrite. Supports title, editor, thumbnail, author, Elementor, custom fields. |
| On/off | Extra Settings > My Library (`oe_library_active_status`) |
| Shortcode | Each template shows `[oceanwp_library id="123"]` in a side metabox and in a "Shortcode" list column. |
| Front end | Single template URLs redirect to the home page for users who cannot edit them. |

The shortcode renders only published `oceanwp_library` posts. It uses Elementor, Beaver Builder or SiteOrigin when the template was built with them, otherwise the post content. A template cannot render itself recursively.

---

## Widgets

Sixteen widgets for sidebars and footers (**Appearance > Widgets**). Each can be turned off in Extra Settings > WordPress Widgets. The list is filterable with `ocean_custom_widgets`.

| Widget | ID | Main settings |
|--------|----|---------------|
| About Me | `ocean_about_me` | Title, image, name, description, social links with style, target and nofollow |
| Contact Info | `ocean_contact_info` | Address, phone, mobile, fax, email, website, Skype, each with title, icon class and optional link |
| Custom Header Logo | `ocean_custom_header_logo` | Position. Shows the logo for the Custom Header style. |
| Custom Header Nav | `ocean_custom_header_nav` | Position. Shows the main menu for the Custom Header style. |
| Custom Links | `ocean_custom_links` | Up to 15 links, target, nofollow |
| Custom Menu | `ocean_custom_menu` | Menu, position, dropdown style, font size, line height, letter spacing, text transform, padding, link colors |
| Facebook Like Box | `ocean_facebook` | Page URL, width, height, color scheme (Facebook embed) |
| Flickr | `ocean_flickr` | Flickr ID, number of images |
| Instagram | `ocean_instagram` | @username or #tag, number, columns, size, header and follow button options. Reads the public Instagram page and caches it for 2 hours (`ocean_instagram_cache_time`). |
| MailChimp | `ocean_mailchimp` | Placeholder, submit text, input size, GDPR label. Uses the MailChimp keys from the Integration pane and subscribes over AJAX. |
| Recent Posts | `ocean_recent_posts` | Number, post type, taxonomy and terms, order and order by |
| Social Icons | `ocean_social` | Profiles, style, size, colors, border radius, target, nofollow |
| Social Share | `ocean_social_share` | Style, show names, X username, minimal style colors |
| Tags Cloud | `ocean_tags` | Colors, padding, typography |
| X | `ocean_twitter` | Username, width, height, timeline theme (loads `platform.twitter.com/widgets.js`) |
| Video | `ocean_video` | Video URL (oEmbed), description |

The widget stylesheet `assets/css/widgets.css` loads on the front end unless the theme mod `ocean_load_widgets_stylesheet` is `disabled`. `rtl.css` loads on RTL sites.

---

## Menu Icons

Adds an icon to any menu item in **Appearance > Menus** (`OE_Menu_Icons`, `includes/menu-icons/`).

| Setting | Description | Default |
|---------|-------------|---------|
| Icon Types (global) | Which icon sets the picker offers: Dashicons, Elusive, FontAwesome Regular, FontAwesome Brand, FontAwesome Solid, Foundation, Genericons, Simple Line Icons, plus Image and SVG. | FontAwesome and Simple Line Icons |
| Hide Text | Hide the menu item label | No |
| Position | Before, After or Below the label | Before |
| Vertical Align | Super, Top, Text Top, Middle, Baseline, Text Bottom, Bottom, Sub | Middle |
| Font Size | Icon size (em) | 1.2 |
| SVG Width | Width for SVG icons (em) | 1 |
| Image Size | WordPress image size for image icons | thumbnail |

Global settings are stored in the `oe-icons` option. Per-item icons are stored in `oe-icons` post meta on the menu item. The mega menu named in the readme is implemented in the OceanWP theme's menu walker, not in this plugin.

---

## Site Preloader

A loading screen shown until the page loads (`includes/preloader/`). Controls live in **Customizer > Site Preloader**. All values are theme mods.

| Setting | Theme mod | Description | Default |
|---------|-----------|-------------|---------|
| Enable Site Preloader | `ocean_preloader_enable` | Turns the preloader on | Off |
| Preloader Type | `ocean_preloader_type` | Default or Custom | Default |
| Select Template | `ocean_preloader_template` | (Custom type) A My Library template to show | None |
| Elementor Flickers/FOUC | `ocean_preloader_elementor_fouc` | (Custom type) Fix flicker for Elementor templates | On |
| Icon Type | `ocean_preloader_icon_type` | CSS, Image, Logo or SVG | CSS |
| Preloader Icon | `ocean_preloader_default_icon` | Roller, Circle, Ring, Dual Ring, Ripple Plain, Ripple Circle, Heart, Ellipsis, Spinner Line, Spinner Dot and more | Roller |
| Image / Upload SVG | `ocean_preloader_icon_image`, `ocean_preloader_icon_svg` | Icon source for the Image and SVG types | |
| Size | `ocean_preloader_image_size` | Icon size (px) | 100 |
| Content | `ocean_preloader_content` | Text under the icon | "Site is Loading, Please wait..." |
| Container Width | `ocean_preloader_container_width` (+ tablet, mobile) | Width of the content box | |
| Typography and Colors | `ocean_preloader_after_content_typography`, `ocean_preloader_icon_color`, `ocean_preloader_overlay_color` | Text typography, icon color, overlay color | |

---

## Fonts and Performance

`includes/compatibility/ocean.php` loads only with OceanWP newer than 3.3.3. The switches are theme Customizer options. Ocean Extra does the work.

| Theme mod | Behavior |
|-----------|----------|
| `ocean_local_google_font` | Downloads Google Fonts used by the theme into `uploads/oceanwp-webfonts/` and serves them locally. Format from `ocean_local_google_font_format` (default `ttf`). |
| `ocean_local_elementor_google_font` | Same for Google Fonts loaded by Elementor. |
| `ocean_preload_local_google_font` | Adds `rel="preload"` to the local font stylesheets. |
| `ocean_customzer_styling` = `file` | Writes the Customizer CSS to `uploads/oceanwp/custom-style.css` instead of printing it in the head. |

---

## Custom CSS/JS

Adds a **Customizer > Custom CSS/JS** section (loaded while the Custom CSS/JS panel switch is on).

| Setting | Description |
|---------|-------------|
| Additional CSS | The core WordPress Additional CSS control, moved into this section. |
| Custom JS (`ocean_custom_js`) | JavaScript without `<script>` tags. Added inline after the theme's `oceanwp-main` script. A reload is needed to see changes. |

JS added through the `ocean_footer_js` filter is minified and printed in the footer.

---

## Open Graph Meta Tags

When the theme mod `ocean_open_graph` is on (theme SEO options, OpenGraph group), Ocean Extra prints Open Graph and X card tags in the head. Nothing is printed when Yoast SEO or Rank Math is active.

| Theme mod | Used for |
|-----------|----------|
| `ocean_open_graph` | Turns the tags on. Default off. |
| `ocean_facebook_page_url` | `article:author` and `article:publisher` |
| `ocean_facebook_appid` | `fb:app_id` |
| `ocean_twitter_handle` | `twitter:site` and `twitter:creator` |

Tags include type, title, description, image with size, URL (filter `ocean_seo_opengraph_tag_url`), site name, published and modified times, tags and section. Jetpack's own Open Graph tags are turned off while these print.

---

## Admin Extras

| Feature | Behavior |
|---------|----------|
| Admin bar menu | An "OceanWP" admin bar menu with Notifications, Panel, System Info, Community and Documentation links. Shown while `oe_notification_active_status` is `no`. Access filter: `ocean_adminbarmenu_has_access`. |
| Notifications | Pulls notices from `notifi.oceanwp.org` into the `ocean_notifications` option via a scheduled event. Users can dismiss single notices. |
| Dashboard widget | "OceanWP Overview" with the oceanwp.org feed. Hidden when the `oceanwp_news_enabled` filter returns true. |
| For OceanWP plugin tab | A "For OceanWP" tab in **Plugins > Add New** listing plugins by the `oceanwp` author, with AJAX install and activate. Filter: `oceanwp_show_plugin_tab`. |
| Plugin update message | Extra text under the Ocean Extra update notice on the Plugins page. |
| Rating notice | Shown 240 hours after install until dismissed (`ocean_extra_dismiss_rating_notice`). |
| Upgrade notice | Shown on Plugins and the OceanWP Panel when no premium license is detected, only after onboarding is completed and the theme's plugin notice was dismissed, until dismissed (`owp_dismiss_sticky_notice`). |
| Outdated theme notice | Asks to update when OceanWP is older than 3.3.0. |
| Customizer | Adds an "OceanWP Info" section with documentation and support links, and replaces the Freemius upsell section with "View paid features". |

---

## Premium Extensions Integration

Premium Ocean extensions depend on Ocean Extra in four ways.

### 1. Licensing through Freemius

| Step | Code |
|------|------|
| The theme defines `owp_fs()` only if `Ocean_Extra` exists and `includes/freemius/start.php` is present. It then fires `owp_fs_loaded`. | `themes/oceanwp/functions.php` |
| On `owp_fs_loaded`, Ocean Extra loads the EDD to Freemius migration code. | `owp_include_client_migration()` in `ocean-extra.php` |
| Each extension defines `<slug>_fs()` with `OceanWP_EDD_Addon_Migration::instance( '<slug>_fs' )->init_sdk(...)`, which sets the OceanWP theme as its Freemius parent. | `includes/client-migration/edd.php` |
| Each extension waits for `owp_fs_loaded` (or runs right away if it already fired) and requires `class_exists( 'Ocean_Extra' )`. | e.g. `ocean-sticky-header.php` |
| Old EDD licenses from oceanwp.org migrate to Freemius (bundle first, otherwise per add-on). Turn off with `WP_FS__MIGRATION_OFF`. Run in the same request with `WP_FS__MIGRATION_DEBUG`. | `includes/client-migration/` |
| The Freemius Pricing submenu is hidden when an add-on has a valid bundle or separate license. | `owp_fs_is_submenu_visible()` |

Add-ons known to the migration code (`OceanWP_EDD_License_Key`):

| Group | Add-ons |
|-------|---------|
| `$paid_addons` (bundle) | Cookie Notice, Elementor Widgets, Footer Callout, Full Screen, Hooks, Instagram, Popup Login, Portfolio, Pro Demos, Side Panel, Sticky Footer, Sticky Header, White Label, Woo Popup |
| `$separate_addons` | Ocean Treasure Box (`Ocean_eCommerce`), Ocean Gutenberg Blocks, Ocean Site Booster |

### 2. OceanWP Panel

Extensions add their own panes and sections with `oceanwp_theme_panel_pane_<slug>` and `oceanwp_theme_panel_sections` (for example Ocean Pro Demos, Ocean Elementor Widgets, Ocean Gutenberg Blocks, Ocean White Label). White Label settings save through Ocean Extra's `oceanwp_cp_save_integrations_settings` handler when `Ocean_White_Label` exists.

### 3. Page Settings

Extensions add their own per-post fields with `butterbean_register` (classic metabox tab) and `ocean_post_setting_meta` (block sidebar meta). Ocean Sticky Header, Side Panel, Hooks, Footer Callout, Full Screen, Portfolio, Modal Window, Posts Slider and the Ocean eCommerce sticky header module use one or both.

### 4. Shared Settings and Templates

The Integration keys (MailChimp, reCAPTCHA, Google Maps, Turnstile) are read by premium extensions. My Library templates are used as content by extensions such as Footer Callout and Hooks.

---

## Admin Pages

| Page | Location | Description |
|------|----------|-------------|
| OceanWP Panel panes | **OceanWP** (theme page) | Panes listed in [OceanWP Panel](#oceanwp-panel) |
| My Library | **OceanWP > My Library** | `oceanwp_library` list and editor |
| Install Demo | **OceanWP > Install Demo** | React template browser and importer |
| Setup Wizard | **OceanWP > Setup Wizard** | Shown until onboarding is completed or dismissed |
| For OceanWP | **Plugins > Add New > For OceanWP** | OceanWP plugin list |
| Menu icon settings | **Appearance > Menus** | Global icon settings and per-item picker |

---

## Display & Developer API

### Shortcodes

| Shortcode | Attributes (default) | Description |
|-----------|----------------------|-------------|
| `[oceanwp_library]` | `id` | Renders a My Library template |
| `[oceanwp_logo]` | `position` (`left`) | Site logo |
| `[oceanwp_nav]` | `position` (`left`) | Main menu |
| `[oceanwp_date]` | `year` | Current year. With `year` set, prints `<year> - <current year>`. |
| `[oceanwp_search]` | `width`, `height`, `placeholder` (Search), `btn_icon` (`icon-magnifier`), `post_type` (`any`) | Search form |
| `[oceanwp_site_url]` | `target` (`self`) | Link to the home page |
| `[oceanwp_login]` | `custom_url`, `login_text` (Login), `logout_text` (Log Out), `target` (`self`), `logout_redirect` | Login or logout link |
| `[oceanwp_current_user]` | `text` (Welcome back), `display` (`display_name`) | Current user's name. `display` accepts `display_name`, `first_name`, `last_name`, `nickname`, `user_firstname`, `user_lastname`. `user_email` and `user_login` are filled in by JavaScript after the page loads (AJAX `oceanwp_current_user_data`). Other values fall back to `display_name`. |
| `[oceanwp_woo_cart]` | `class`, `style` (`drop_down`), `custom_link`, `total` (false), `cart_style` (`compact`), `hide_if_empty` (false), `color`, `hover_color`, `count_color`, `count_hover_color` | WooCommerce cart icon |
| `[oceanwp_woo_total_cart]` | | Cart total |
| `[oceanwp_woo_cart_items]` | | Cart item count |
| `[oceanwp_woo_free_shipping_left]` | `content` ("Buy for %left_to_free% more and get free shipping"), `content_reached` ("You have Free delivery!"), `multiply_by` (1) | Amount left for free shipping, refreshed by AJAX on cart changes |
| `[oceanwp_breadcrumb]` | `class`, `color`, `hover_color` | Theme breadcrumbs |
| `[oceanwp_last_modified]` | `olm_text` (Last Updated on:), `olm_date_format` | Post modified date |
| `[oceanwp_icon]` | `icon`, `class`, `title`, `desc`, `area_hidden` (true), `fallback` (false) | Theme SVG icon |

Shortcodes also run in text widgets and excerpts (`widget_text`, `the_excerpt`).

### Widgets

See [Widgets](#widgets). There are no Gutenberg blocks or Elementor widgets in this plugin. Those come from Ocean Gutenberg Blocks and Ocean Elementor Widgets.

### Key Filters

| Hook | Description |
|------|-------------|
| `ocean_custom_widgets` | List of widget slugs to load |
| `ocean_main_metaboxes_post_types` | Post types that get Page Settings |
| `ocean_main_metaboxes_capabilities` | Capability for Page Settings (default `manage_options`) |
| `ocean_gallery_metabox_post_types` | Post types that get the Gallery metabox |
| `ocean_post_setting_meta` | Add or change block sidebar meta definitions |
| `ocean_post_settings_data_choices` | Choices used by the block sidebar |
| `oe_get_all_meta` | All Page Settings meta definitions |
| `ocean_title_styles` | Page title style choices |
| `oe_theme_panels` / `oe_default_panels` | Customizer panels that can be toggled, and their defaults |
| `ocean_integrations_settings` | Integration settings arrays |
| `ocean_export_filename` | Customizer export file name |
| `ocean_library_args` / `ocean_my_library_text` | My Library post type args and menu label |
| `ocean_onboarding_localize` | Data passed to the wizard and Install Demo apps |
| `ocean_onboarding_wizard_options` | Wizard default options |
| `ocean_footer_js` | JS printed (minified) in the footer |
| `ocean_seo_opengraph_tag_url` | `og:url` value |
| `ocean_instagram_cache_time` | Instagram cache time (default 2 hours) |
| `ocean_mailchimp_api_args` | MailChimp API request args |
| `ocean_social_widget_profiles` / `ocean_social_share_buttons` | Social widget profiles and share buttons |
| `ocean_preloader_icon_list`, `ocean_preloader_icon_html`, `ocean_preloader_image`, `ocean_preloader_template_content` | Preloader output |
| `oe_menu_icons_types`, `oe_menu_icons_settings_defaults`, `oe_menu_icons_settings_fields` | Menu Icons types and settings |
| `ocean_adminbarmenu_has_access` | Who sees the admin bar menu |
| `oceanwp_news_enabled` | Return true to hide the dashboard widget |
| `oceanwp_show_plugin_tab` | Show the For OceanWP plugin tab |
| `ocean_affiliate_ref` | Affiliate ref added to upgrade links |

### Key Actions

| Hook | Description |
|------|-------------|
| `owp_fs_loaded` | Fired by the theme after `owp_fs()` starts. Ocean Extra and premium extensions hook here. |
| `owp_fs_client_migration_loaded` | Fired when no bundle license is stored, so each add-on can try its own migration |
| `ocean_preloader_before`, `ocean_preloader`, `ocean_preloader_after` | Around the preloader markup |
| `oe_menu_icons_loaded` | After Menu Icons starts |
| `butterbean_register` | Register extra metabox tabs and controls |

### REST API

Namespace `oceanwp/v1`. "Edit" means `WP_REST_Server::EDITABLE` (POST, PUT or PATCH). The plugin install, activate and status routes need `install_plugins`. All other routes need `manage_options`.

| Route | Method | Description |
|-------|--------|-------------|
| `/onboarding/options` | Edit | Save wizard branding, colors and typography |
| `/onboarding/reset-site` | Edit | Run the chosen cleanup actions |
| `/onboarding/install-plugin`, `/onboarding/activate-plugin` | Edit | Install or activate a plugin |
| `/onboarding/plugin-status` | Edit | Status of a list of plugins |
| `/onboarding/get-templates` | GET | Cached template list |
| `/onboarding/sync-templates` | Edit | Refetch the template list |
| `/onboarding/select-template` | Edit | Store the chosen template in `ocean_installing_template_data` |
| `/onboarding/get-plugins` | GET | Template plugin list |
| `/onboarding/finish-setup/` | Edit | Set `owp_onboarding_completed` |
| `/onboarding/color-mode/` | Edit | Save the wizard light or dark mode |
| `/option-reset-current/<post_id>` | Edit | Reset one post's Page Settings |

### AJAX Actions

| Action | Description |
|--------|-------------|
| `oceanwp_cp_save_customizer_settings`, `oceanwp_cp_save_panel_settings`, `oceanwp_cp_save_single_option`, `oceanwp_cp_save_integrations_settings` | Save OceanWP Panel settings |
| `oceanwp_cp_customizer_reset`, `oceanwp_cp_customizer_export`, `oceanwp_cp_customizer_import` | Customizer reset, export and import |
| `oceanwp_cp_child_theme_install` | Install the child theme |
| `oceanwp_cp_system_status` | System report data |
| `download_template_data`, `oceanwp_onboarding_import_data`, `oceanwp_onboarding_after_import` | Demo import steps |
| `oe_mautic_subscribe` (also logged out) | Wizard email subscription |
| `oceanwp_mailchimp_request` (also logged out) | MailChimp widget subscribe |
| `oceanwp_current_user_data` | Fill email or login for `[oceanwp_current_user]` |
| `update_oceanwp_woo_free_shipping_left_shortcode` (also logged out) | Refresh the free shipping shortcode |
| `oe_menu_icons_update_settings` | Save Menu Icons settings |
| `ocean_notification_block` | Dismiss a notification |
| `oceanwp_install_plugin`, `oceanwp_activate_plugin` | For OceanWP plugin tab |

### Data Storage

| Type | Keys |
|------|------|
| Options | `oe_panels_settings`, `oe_widgets_settings`, `oe_metabox_posttypes_settings`, `oe_library_active_status`, `oe_svg_support_active_status`, `oe_install_demos_active`, `owp_*` integration keys, `oe-adobe-fonts`, `oe-icons`, `ocean_notifications`, `owp_onboarding_completed`, `ocean_installing_template_data`, `ocean-extra-version`, `ocean-extra-installed-version`, `ocean_extra_installed_time`, notice dismiss flags |
| Theme mods | `ocean_preloader_*`, `ocean_custom_js`, and the theme mods set by the wizard and demo import |
| Post meta | `ocean_*` Page Settings keys, `ocean_gallery_id`, `ocean_gallery_link_images`, `oe-icons` on menu items |
| Post type | `oceanwp_library` |
| Transients | `ocean_onboarding_template_data`, `ocean_onboarding_plugin_data`, `oe_onboarding_color_mode`, `ocean-insta-*` |
| Files | `uploads/oceanwp-webfonts/`, `uploads/oceanwp/custom-style.css`, `uploads/sample-data/` (temporary) |

### Uninstall

Ocean Extra has no uninstall routine (no `uninstall.php` or uninstall hook of its own). Deleting the plugin leaves its options, theme mods, post meta, My Library posts and uploaded font and CSS files in place.

---

*Ocean Extra - The free companion plugin that powers the OceanWP Panel, templates and premium extensions*
