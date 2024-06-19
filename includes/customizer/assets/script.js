document.addEventListener("DOMContentLoaded", function () {
    var api = wp.customize;
    var processedSections = {};

    if (typeof wp !== 'undefined' && wp.customize && !oceanSectionCustomize.isOE) {
        return;
    }

    // Function to add an icon to an existing section
    function addIconToCustomizerSection(sectionId, iconClass) {
        api.bind('pane-contents-reflowed', function () {
            api.section.each(function (section) {
                if (section && sectionId === section.id && !processedSections[sectionId]) {
                    var sectionTitle = document.querySelector('#accordion-section-' + section.id + ' > .accordion-section-title');
                    if (sectionTitle) {
                        var span = document.createElement('span');
                        span.className = 'ocean-customizer-section-icon';
                        span.innerHTML = iconClass;
                        sectionTitle.insertBefore(span, sectionTitle.firstChild);
                        processedSections[sectionId] = true;
                    }
                }
            });
        });
    }

    addIconToCustomizerSection('ocean_typography', oceanSectionCustomize.sectionIcons.typo);
    addIconToCustomizerSection('ocean_colors', oceanSectionCustomize.sectionIcons.color);
    addIconToCustomizerSection('ocean_styles_and_settings', oceanSectionCustomize.sectionIcons.siteSetting);
    addIconToCustomizerSection('ocean_site_page_settings', oceanSectionCustomize.sectionIcons.pageSetting);
    addIconToCustomizerSection('ocean_topbar', oceanSectionCustomize.sectionIcons.topbar);
    addIconToCustomizerSection('ocean_header', oceanSectionCustomize.sectionIcons.header);
    addIconToCustomizerSection('ocean_blog', oceanSectionCustomize.sectionIcons.blog);
    addIconToCustomizerSection('ocean_sidebar', oceanSectionCustomize.sectionIcons.sidebar);
    addIconToCustomizerSection('ocean_site_layout_section', oceanSectionCustomize.sectionIcons.siteLayout);
    addIconToCustomizerSection('ocean_site_icon_section', oceanSectionCustomize.sectionIcons.siteIcon);
    addIconToCustomizerSection('ocean_site_button_section', oceanSectionCustomize.sectionIcons.siteButton);
    addIconToCustomizerSection('ocean_site_forms_section', oceanSectionCustomize.sectionIcons.siteForm);
    addIconToCustomizerSection('ocean_scroll_to_top_section', oceanSectionCustomize.sectionIcons.siteScrollToTop);
    addIconToCustomizerSection('ocean_site_pagination_section', oceanSectionCustomize.sectionIcons.sitePagination);
    addIconToCustomizerSection('ocean_section_page_search_result', oceanSectionCustomize.sectionIcons.sitePageSearchResult);
    addIconToCustomizerSection('ocean_section_page_404_error', oceanSectionCustomize.sectionIcons.sitePage404);
    addIconToCustomizerSection('ocean_footer_widgets', oceanSectionCustomize.sectionIcons.footerWidget);
    addIconToCustomizerSection('ocean_footer_bottom', oceanSectionCustomize.sectionIcons.footerCopyright);
});
