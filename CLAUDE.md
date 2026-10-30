# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Ocean Extra is the free companion plugin for the OceanWP theme (text domain `ocean-extra`, global prefix `oe`, WP 5.9+, PHP 7.4+ per `readme.txt`). It adds the OceanWP Panel panes, per-post settings (metabox and block editor sidebar), widgets, shortcodes, menu icons, preloader, the onboarding wizard with demo import, and it ships the Freemius SDK that the theme and every premium Ocean extension use.

## Commands

There are no automated tests in this repo.

PHP linting (needs `composer install` first; `npm run lint:php` just calls `composer lint:php`):

```bash
composer lint:php       # phpcs (phpcs.xml.dist) + parallel-lint + PHPCompatibilityWP for PHP 5.6+
composer lint:wpcs      # phpcs only
composer lint:summary   # parallel-lint only (syntax check)
vendor/bin/phpcs path/to/file.php   # lint one file
```

`phpcs.xml.dist` uses WordPress-Extra, WordPress-Docs and PHPCompatibility, and excludes `assets/`, `sass/`, `build/` and `vendor/`.

Translations: `composer make-pot` (writes `languages/ocean-extra.pot`), `composer update-mo` (merges and compiles the es_ES and fr_FR files).

Assets (Grunt, needs `npm install`):

```bash
npx grunt           # browserify + uglify + sass + autoprefixer + cssmin (all built assets)
npx grunt watch     # recompiles sass/**/*.scss only
npx grunt build     # copies the plugin into build/ocean-extra/ (excludes dev files)
npx grunt package   # zips build/ocean-extra/ into build/ocean-extra.zip
```

## Built assets are committed

Both sources and built files are in git, and PHP enqueues the built file, so after editing a source run `npx grunt` and commit both. The full source-to-output map is in `Gruntfile.js`. Main cases:

- `sass/widgets.scss` (imports `sass/components/_widget-*.scss`) compiles to `assets/css/widgets.css`, the front-end widget stylesheet.
- `includes/**/js/foo.js` is uglified to `foo.min.js`, and `foo.css` to `foo.min.css`. Most enqueues load the `.min` file.
- `includes/customizer/assets/script.js` uses `require("./svg")`, so it goes through browserify and babel into `script.min.js`.
- Exception: the theme panel enqueues `includes/themepanel/assets/js/theme-panel.js` directly (not the `.min` version).

The onboarding wizard (`includes/onboarding/`) and the block editor post settings sidebar (`includes/post-settings/`) are React apps with their own `package.json` (`@wordpress/scripts`, `npm run build|start|lint:js`). Their source folders (`oe-sw-src/`, `oe-ps-src/`) are gitignored and are not in this repo. Only the compiled output is committed (`includes/onboarding/assets/dist/`, `includes/post-settings/assets/`), so those UIs cannot be rebuilt from this clone. Do not hand-edit the compiled bundles.

## Load order and theme gating

`ocean-extra.php` creates the `Ocean_Extra` singleton when the plugin file loads. Almost everything is gated on the active theme being OceanWP or a child of it (`'OceanWP' == $theme->name || 'oceanwp' == $theme->template`). With any other theme, only a few generic hooks are registered (shortcodes in text widgets and excerpts, version logging, an outdated-theme notice, the Freemius hook below), so test with `oceanwp` or `oceanwp-child-theme` active.

1. Constructor (plugin load): defines `OE_URL`, `OE_PATH`, `OE_VERSION`, `OE_FILE_PATH`. If OceanWP is active it loads the old panel class (`includes/panel/theme-panel.php`, which holds `Ocean_Extra_Theme_Panel::get_setting()`), My Library (`includes/panel/library.php`, skipped when option `oe_library_active_status` is `no`), menu icons, onboarding (`includes/onboarding/start.php`), the new panel panes (`includes/themepanel/theme-panel.php`), preloader, and `includes/compatibility/ocean.php` (only when the theme version is above 3.3.3). It also hooks widgets on `widgets_init`.
2. `init` (`Ocean_Extra::setup()`): loads utils, Butterbean metabox, post settings, shortcodes, dashboard, notices, Adobe Fonts, customizer additions, and the admin bar notifications (only while option `oe_notification_active_status` is `no`, which is the default).

Widgets are files in `includes/widgets/<slug>.php`, loaded from the `ocean_custom_widgets` filter list. The theme panel's widget toggles remove entries from that list.

The OceanWP Panel page (`toplevel_page_oceanwp`) belongs to the theme (`themes/oceanwp/inc/themepanel/`). This plugin fills in panes through `oceanwp_theme_panel_pane_<slug>` filters in `Ocean_Extra_New_Theme_Panel` and saves them through `wp_ajax_oceanwp_cp_*` actions.

## Where settings live

- `oe_panels_settings`: customizer panel on/off array (read with `Ocean_Extra_Theme_Panel::get_setting()`, missing keys default to on).
- `oe_widgets_settings`, `oe_metabox_posttypes_settings`: which widgets load and which post types get the metabox.
- Single-value toggles: `oe_library_active_status`, `oe_svg_support_active_status`, `oe_install_demos_active`, `oe_notification_active_status`.
- Integrations: `owp_mailchimp_api_key`, `owp_mailchimp_list_id`, `owp_adobe_fonts_integration*`, `oe-adobe-fonts`.
- Onboarding state: `owp_onboarding_completed`.
- Version tracking: `ocean-extra-version` (on activation), `ocean-extra-installed-version` (first install).
- Per-post settings are `ocean_*` post meta, defined in `includes/post-settings/defaults.php` and registered with `show_in_rest`. The block editor uses the React sidebar. The classic editor falls back to the Butterbean metabox (`includes/metabox/`). Both read and write the same meta keys.
- Most design options are OceanWP theme mods (`get_theme_mod( 'ocean_*' )`), owned by the theme.

The onboarding wizard uses REST namespace `oceanwp/v1` (`includes/onboarding/class/rest.php`) and pulls demo lists from `https://demos.oceanwp.org/1wizard-installation/` (with a fallback URL), caching them in the `ocean_onboarding_template_data` transient.

## Freemius (parent SDK for premium extensions)

- The SDK lives in `includes/freemius/` (vendored, currently 2.13.5). It is updated by replacing the folder (see the "freemius sdk update" commits). Don't edit it.
- `owp_fs()` is **not** defined here. The OceanWP theme's `functions.php` defines it, only if `Ocean_Extra` exists and `includes/freemius/start.php` is present. It inits Freemius as the theme product (id 3752, bundle 3767, anonymous mode) and then fires `do_action( 'owp_fs_loaded' )`. Without this plugin, the theme has no Freemius.
- On `owp_fs_loaded`, this plugin loads `includes/client-migration/edd.php` (EDD to Freemius license migration). That file defines `OceanWP_EDD_License_Key`, which lists the known premium add-ons in `$paid_addons` and `$separate_addons` (class name, Freemius ID, `*_fs` function name), and `OceanWP_EDD_Addon_Migration`, which sets each add-on's Freemius parent to the OceanWP theme. `owp_fs_is_submenu_visible()` in `ocean-extra.php` uses those lists to hide the Pricing submenu when an add-on has a valid license.
- Premium extensions (for example `ocean-sticky-header`) define their own `<slug>_fs()` with `OceanWP_EDD_Addon_Migration::instance( '<slug>_fs' )->init_sdk(...)`, hook their init to `owp_fs_loaded` (or run it right away if that already fired), and require `class_exists( 'Ocean_Extra' )`. They only start their features when `can_use_premium_code__premium_only()` is true, so on a local dev site without a valid license they load but do nothing.
- Migration can be turned off with `WP_FS__MIGRATION_OFF` or run in the same request with `WP_FS__MIGRATION_DEBUG` (both constants set to `true`).

## Releases

Branches are `rc-X.Y.Z`, merged into `master` by PR. A release bumps `Version` in the `ocean-extra.php` header and `Stable tag` in `readme.txt`, and adds the entry to both `changelog.txt` and the `== Changelog ==` section of `readme.txt` (format `= 2.6.3 - OCT 13 2026 =` followed by `- Added:` / `- Improved:` / `- Fixed:` / `- Updated:` lines).
