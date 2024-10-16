"use strict";

var _svg = require("./svg");

document.addEventListener("DOMContentLoaded", function () {
    var api = wp.customize;
    var processedSections = {};

    if (typeof wp === 'undefined' || !wp.customize || !oceanSectionCustomize.isOE) {
        return;
    }

    var sectionConfig = {
        ocean_typography: {
          iconClass: _svg.typo,
          className: 'ocean-customizer-section section-core-group'
        },
        ocean_colors: {
          iconClass: _svg.color,
          className: 'ocean-customizer-section section-core-group'
        },
        ocean_styles_and_settings: {
          iconClass: _svg.siteSetting,
          className: 'ocean-customizer-section section-layout-group'
        },
        ocean_site_page_settings: {
          iconClass: _svg.pageSetting,
          className: 'ocean-customizer-section section-layout-group'
        },
        ocean_popup_login_settings: {
          iconClass: _svg.popupLogin,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_topbar: {
          iconClass: _svg.topbar,
          className: 'ocean-customizer-section section-header-group'
        },
        ocean_header: {
          iconClass: _svg.header,
          className: 'ocean-customizer-section section-header-group'
        },
        ocean_sticky_header_settings: {
          iconClass: _svg.stickyHeader,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_blog: {
          iconClass: _svg.blog,
          className: 'ocean-customizer-section section-blog-group'
        },
        ocean_portfolio_settings: {
          iconClass: _svg.portfolio,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_social_sharing_settings: {
          iconClass: _svg.socialSharing,
          className: 'ocean-customizer-section section-free-group is-free'
        },
        ocean_sidebar: {
          iconClass: _svg.sidebar,
          className: 'ocean-customizer-section section-sidebar-group'
        },
        ocean_side_panel_settings: {
          iconClass: _svg.sidePanel,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_site_layout_section: {
          iconClass: _svg.siteLayout,
          className: 'ocean-customizer-section'
        },
        ocean_site_icon_section: {
          iconClass: _svg.siteIcon,
          className: 'ocean-customizer-section'
        },
        ocean_site_button_section: {
          iconClass: _svg.siteButton,
          className: 'ocean-customizer-section'
        },
        ocean_site_forms_section: {
          iconClass: _svg.siteForm,
          className: 'ocean-customizer-section'
        },
        ocean_scroll_to_top_section: {
          iconClass: _svg.siteScrollToTop,
          className: 'ocean-customizer-section'
        },
        ocean_site_pagination_section: {
          iconClass: _svg.sitePagination,
          className: 'ocean-customizer-section'
        },
        ocean_section_page_search_result: {
          iconClass: _svg.sitePageSearchResult,
          className: 'ocean-customizer-section'
        },
        ocean_section_page_404_error: {
          iconClass: _svg.sitePage404,
          className: 'ocean-customizer-section'
        },
        ocean_footer_widgets: {
          iconClass: _svg.footerWidget,
          className: 'ocean-customizer-section section-footer-group'
        },
        ocean_footer_bottom: {
          iconClass: _svg.footerCopyright,
          className: 'ocean-customizer-section section-footer-group'
        },
        ocean_sticky_footer_settings: {
          iconClass: _svg.stickyFooter,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_footer_callout_settings: {
          iconClass: _svg.footerCallout,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_cookie_notice_settings: {
          iconClass: _svg.cookieNotice,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_seo_settings: {
          iconClass: _svg.seo,
          className: 'ocean-customizer-section section-perf-group'
        },
        ocean_site_performance: {
          iconClass: _svg.performance,
          className: 'ocean-customizer-section section-perf-group'
        },
        ocean_preloader: {
          iconClass: _svg.preloader,
          className: 'ocean-customizer-section section-perf-group'
        },
        ocean_modal_window_settings: {
          iconClass: _svg.modalWindow,
          className: 'ocean-customizer-section section-free-group is-free'
        },
        ocean_woocommerce_settings: {
          iconClass: _svg.ecommerce,
          className: 'ocean-customizer-section section-ecom-group'
        },
        ocean_woo_popup_settings: {
          iconClass: _svg.wooPopup,
          className: 'ocean-customizer-section section-pro-group is-pro'
        },
        ocean_edd_settings: {
          iconClass: _svg.ecommerce,
          className: 'ocean-customizer-section section-ecom-group'
        },
        ocean_product_sharing_settings: {
          iconClass: _svg.productSharing,
          className: 'ocean-customizer-section section-free-group is-free'
        },
        ocean_learndash_settings: {
          className: 'ocean-customizer-section section-lms-group'
        },
        ocean_lifterlms_settings: {
          className: 'ocean-customizer-section section-lms-group'
        },
        ocean_custom_code_panel: {
          iconClass: _svg.customCode,
          className: 'ocean-customizer-section section-extra-group'
        },
        ocean_info: {
          iconClass: _svg.themeInfo,
          className: 'ocean-customizer-section section-extra-group'
        }
    };

    function customizeSection(sectionId, config) {

        if (processedSections[sectionId]) return;

        api.section(sectionId, function (section) {
            var sectionElement = document.querySelector('#accordion-section-' + sectionId);
            if (!sectionElement) return;

            var classes = config.className ? config.className.split(' ') : [];
            var sectionTitle = sectionElement.querySelector('.accordion-section-title');

            var fragment = document.createDocumentFragment();

            classes.forEach(function (className) {
                sectionElement.classList.add(className.trim());
            });

            if (config.iconClass && sectionTitle) {
                var span = document.createElement('span');
                span.className = 'ocean-customizer-section-icon';
                span.innerHTML = config.iconClass;
                sectionTitle.insertBefore(span, sectionTitle.firstChild);
            }

            processedSections[sectionId] = true;
        });
    }

    Object.keys(sectionConfig).forEach(function (sectionId) {
        customizeSection(sectionId, sectionConfig[sectionId]);
    });

    function addLastItemClassToSectionGroups() {
        var sectionGroups = [
            'section-core-group',
            'section-layout-group',
            'section-header-group',
            'section-blog-group',
            'section-sidebar-group',
            'section-footer-group',
            'section-perf-group',
            'section-free-group',
            'section-pro-group',
            'section-ecom-group',
            'section-lms-group',
            'section-extra-group'
        ];

        sectionGroups.forEach(function (groupClass) {
            var elements = document.querySelectorAll('.' + groupClass);
            if (elements.length > 0) {
                elements[elements.length - 1].classList.add('last-item');
            }
        });
    }

    function checkAndAddLastItemClass() {
        var elements = document.querySelectorAll('.section-core-group, .section-layout-group, .section-header-group, .section-blog-group, .section-sidebar-group, .section-footer-group, .section-perf-group, .section-free-group, .section-pro-group, .section-ecom-group, .section-lms-group, .section-extra-group');

        if (elements.length === 0) {
            setTimeout(checkAndAddLastItemClass, 100);
        } else {
            addLastItemClassToSectionGroups();
        }
    }

    wp.customize.bind('ready', function () {
        checkAndAddLastItemClass();
    });
});

