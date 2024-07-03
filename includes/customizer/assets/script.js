// document.addEventListener("DOMContentLoaded", function () {
//     var api = wp.customize;
//     var processedSections = {};

//     if (typeof wp !== 'undefined' && wp.customize && !oceanSectionCustomize.isOE) {
//         return;
//     }

//     // Function to add an icon to an existing section
//     function addIconToCustomizerSection(sectionId, iconClass) {
//         api.bind('pane-contents-reflowed', function () {
//             api.section.each(function (section) {
//                 if (section && sectionId === section.id && !processedSections[sectionId]) {
//                     var sectionTitle = document.querySelector('#accordion-section-' + section.id + ' > .accordion-section-title');
//                     if (sectionTitle) {
//                         var span = document.createElement('span');
//                         span.className = 'ocean-customizer-section-icon';
//                         span.innerHTML = iconClass;
//                         sectionTitle.insertBefore(span, sectionTitle.firstChild);
//                         processedSections[sectionId] = true;
//                     }
//                 }
//             });
//         });
//     }

//     addIconToCustomizerSection('ocean_typography', oceanSectionCustomize.sectionIcons.typo);
//     addIconToCustomizerSection('ocean_colors', oceanSectionCustomize.sectionIcons.color);
//     addIconToCustomizerSection('ocean_styles_and_settings', oceanSectionCustomize.sectionIcons.siteSetting);
//     addIconToCustomizerSection('ocean_site_page_settings', oceanSectionCustomize.sectionIcons.pageSetting);
//     addIconToCustomizerSection('ocean_topbar', oceanSectionCustomize.sectionIcons.topbar);
//     addIconToCustomizerSection('ocean_header', oceanSectionCustomize.sectionIcons.header);
//     addIconToCustomizerSection('ocean_blog', oceanSectionCustomize.sectionIcons.blog);
//     addIconToCustomizerSection('ocean_sidebar', oceanSectionCustomize.sectionIcons.sidebar);
//     addIconToCustomizerSection('ocean_site_layout_section', oceanSectionCustomize.sectionIcons.siteLayout);
//     addIconToCustomizerSection('ocean_site_icon_section', oceanSectionCustomize.sectionIcons.siteIcon);
//     addIconToCustomizerSection('ocean_site_button_section', oceanSectionCustomize.sectionIcons.siteButton);
//     addIconToCustomizerSection('ocean_site_forms_section', oceanSectionCustomize.sectionIcons.siteForm);
//     addIconToCustomizerSection('ocean_scroll_to_top_section', oceanSectionCustomize.sectionIcons.siteScrollToTop);
//     addIconToCustomizerSection('ocean_site_pagination_section', oceanSectionCustomize.sectionIcons.sitePagination);
//     addIconToCustomizerSection('ocean_section_page_search_result', oceanSectionCustomize.sectionIcons.sitePageSearchResult);
//     addIconToCustomizerSection('ocean_section_page_404_error', oceanSectionCustomize.sectionIcons.sitePage404);
//     addIconToCustomizerSection('ocean_footer_widgets', oceanSectionCustomize.sectionIcons.footerWidget);
//     addIconToCustomizerSection('ocean_footer_bottom', oceanSectionCustomize.sectionIcons.footerCopyright);
// });


document.addEventListener("DOMContentLoaded", function () {
    var api = wp.customize;
    var processedSections = {};

    if (typeof wp === 'undefined' || !wp.customize || !oceanSectionCustomize.isOE) {
        return;
    }

    // Configuration object to specify which sections get icons and/or classes
    var sectionConfig = {
        ocean_typography: { iconClass: oceanSectionCustomize.sectionIcons.typo, className: 'ocean-customizer-section section-core-group' },
        ocean_colors: { iconClass: oceanSectionCustomize.sectionIcons.color, className: 'ocean-customizer-section section-core-group' },
        ocean_styles_and_settings: { iconClass: oceanSectionCustomize.sectionIcons.siteSetting, className: 'ocean-customizer-section section-layout-group' },
        ocean_site_page_settings: { iconClass: oceanSectionCustomize.sectionIcons.pageSetting, className: 'ocean-customizer-section section-layout-group' },
        ocean_popup_login_settings: { className: 'ocean-customizer-section section-header-group is-pro' },
        ocean_topbar: { iconClass: oceanSectionCustomize.sectionIcons.topbar, className: 'ocean-customizer-section section-header-group' },
        ocean_header: { iconClass: oceanSectionCustomize.sectionIcons.header, className: 'ocean-customizer-section section-header-group' },
        ocean_sticky_header_settings: { className: 'ocean-customizer-section section-header-group is-pro' },
        ocean_blog: { iconClass: oceanSectionCustomize.sectionIcons.blog, className: 'ocean-customizer-section section-blog-group' },
        ocean_portfolio_settings: { className: 'ocean-customizer-section section-blog-group is-pro' },
        ocean_social_sharing_settings: { className: 'ocean-customizer-section section-blog-group is-free' },
        ocean_sidebar: { iconClass: oceanSectionCustomize.sectionIcons.sidebar, className: 'ocean-customizer-section section-sidebar-group' },
        ocean_side_panel_settings: { className: 'ocean-customizer-section section-sidebar-group is-pro' },
        ocean_site_layout_section: { iconClass: oceanSectionCustomize.sectionIcons.siteLayout, className: 'ocean-customizer-section' },
        ocean_site_icon_section: { iconClass: oceanSectionCustomize.sectionIcons.siteIcon, className: 'ocean-customizer-section' },
        ocean_site_button_section: { iconClass: oceanSectionCustomize.sectionIcons.siteButton, className: 'ocean-customizer-section' },
        ocean_site_forms_section: { iconClass: oceanSectionCustomize.sectionIcons.siteForm, className: 'ocean-customizer-section' },
        ocean_scroll_to_top_section: { iconClass: oceanSectionCustomize.sectionIcons.siteScrollToTop, className: 'ocean-customizer-section' },
        ocean_site_pagination_section: { iconClass: oceanSectionCustomize.sectionIcons.sitePagination, className: 'ocean-customizer-section' },
        ocean_section_page_search_result: { iconClass: oceanSectionCustomize.sectionIcons.sitePageSearchResult, className: 'ocean-customizer-section' },
        ocean_section_page_404_error: { iconClass: oceanSectionCustomize.sectionIcons.sitePage404, className: 'ocean-customizer-section' },
        ocean_footer_widgets: { iconClass: oceanSectionCustomize.sectionIcons.footerWidget, className: 'ocean-customizer-section section-footer-group' },
        ocean_footer_bottom: { iconClass: oceanSectionCustomize.sectionIcons.footerCopyright, className: 'ocean-customizer-section section-footer-group' },
        ocean_sticky_footer_settings: { className: 'ocean-customizer-section section-footer-group is-pro' },
        ocean_footer_callout_settings: {  className: 'ocean-customizer-section section-footer-group is-pro' },
        ocean_cookie_notice_settings: { className: 'ocean-customizer-section section-footer-group is-pro' },
        ocean_seo_settings: { iconClass: oceanSectionCustomize.sectionIcons.seo, className: 'ocean-customizer-section section-perf-group' },
        ocean_site_performance: { iconClass: oceanSectionCustomize.sectionIcons.performance, className: 'ocean-customizer-section section-perf-group' },
        ocean_preloader: { iconClass: oceanSectionCustomize.sectionIcons.preloader, className: 'ocean-customizer-section section-perf-group' },
        ocean_modal_window_settings: { className: 'ocean-customizer-section section-free-group is-free' },
        ocean_woocommerce_settings: { iconClass: oceanSectionCustomize.sectionIcons.ecommerce, className: 'ocean-customizer-section section-ecom-group' },
        ocean_woo_popup_settings: { className: 'ocean-customizer-section section-ecom-group is-pro' },
        ocean_edd_settings: { iconClass: oceanSectionCustomize.sectionIcons.ecommerce, className: 'ocean-customizer-section section-ecom-group' },
        ocean_product_sharing_settings: { className: 'ocean-customizer-section section-ecom-group is-free' },
        ocean_learndash_settings: { className: 'ocean-customizer-section section-lms-group' },
        ocean_lifterlms_settings: { className: 'ocean-customizer-section section-lms-group' },
        ocean_custom_code_panel: { iconClass: oceanSectionCustomize.sectionIcons.customCode, className: 'ocean-customizer-section section-extra-group' },
        ocean_info: { className: 'ocean-customizer-section section-extra-group' }
    };

    // Function to add an icon and/or class to an existing section
    function customizeSection(sectionId, config) {
        api.section(sectionId, function (section) {
            if (!processedSections[sectionId]) {
                var sectionElement = document.querySelector('#accordion-section-' + sectionId);
                if (sectionElement) {
                    if (config.className) {
                        var classes = config.className.split(' ');
                        classes.forEach(function(className) {
                            sectionElement.classList.add(className.trim());
                        });
                    }
                    if (config.iconClass) {
                        var sectionTitle = sectionElement.querySelector('.accordion-section-title');
                        if (sectionTitle) {
                            var span = document.createElement('span');
                            span.className = 'ocean-customizer-section-icon';
                            span.innerHTML = config.iconClass;
                            sectionTitle.insertBefore(span, sectionTitle.firstChild);
                        }
                    }
                    processedSections[sectionId] = true;
                }
            }
        });
    }

    // Apply configurations to sections
    for (var sectionId in sectionConfig) {
        if (sectionConfig.hasOwnProperty(sectionId)) {
            customizeSection(sectionId, sectionConfig[sectionId]);
        }
    }

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

