module.exports = function (grunt) {
    const sass = require("node-sass");

    // require it at the top and pass in the grunt instance
    require("time-grunt")(grunt);

    // Load all Grunt tasks
    require("jit-grunt")(grunt, {
        makepot: "grunt-wp-i18n",
    });

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        // Concat and Minify our js.
        uglify: {
            dev: {
                files: {
                    "includes/menu-icons/js/admin.min.js": "includes/menu-icons/js/admin.js",
                    "includes/metabox/assets/js/metabox.min.js": "includes/metabox/assets/js/metabox.js",
                    "includes/panel/assets/js/scripts.min.js": "includes/panel/assets/js/scripts.js",
                    "includes/panel/assets/js/demos.min.js": "includes/panel/assets/js/demos.js",
                    "includes/wizard/assets/js/wizard.min.js": "includes/wizard/assets/js/wizard.js",
                    "includes/metabox/controls/assets/js/select2.full.min.js":
                        "includes/metabox/controls/assets/js/select2.full.js",
                    "includes/metabox/controls/assets/js/butterbean.min.js":
                        "includes/metabox/controls/assets/js/butterbean.js",
                    "includes/widgets/js/insta-admin.min.js": "includes/widgets/js/insta-admin.js",
                    "includes/widgets/js/mailchimp.min.js": "includes/widgets/js/mailchimp.js",
                    "includes/widgets/js/share.min.js": "includes/widgets/js/share.js",
                    "includes/shortcodes/js/shortcode.min.js": "includes/shortcodes/js/shortcode.js",
                },
            },
        },

        // Minify CSS
        cssmin: {
            options: {
                shorthandCompacting: false,
                roundingPrecision: -1,
                keepSpecialComments: 0,
            },
            prod: {
                files: {
                    "assets/css/admin.min.css": "assets/css/admin.css",
                    "includes/menu-icons/css/admin.min.css": "includes/menu-icons/css/admin.css",
                    "includes/panel/assets/css/import-export.min.css": "includes/panel/assets/css/import-export.css",
                    "includes/panel/assets/css/demos.min.css": "includes/panel/assets/css/demos.css",
                    "includes/panel/assets/css/extensions.min.css": "includes/panel/assets/css/extensions.css",
                    "includes/panel/assets/css/panel.min.css": "includes/panel/assets/css/panel.css",
                    "includes/panel/assets/css/licenses.min.css": "includes/panel/assets/css/licenses.css",
                    "includes/panel/assets/css/scripts.min.css": "includes/panel/assets/css/scripts.css",
                    "includes/panel/assets/css/sticky-notice.min.css": "includes/panel/assets/css/sticky-notice.css",
                    "includes/panel/assets/css/notice.min.css": "includes/panel/assets/css/notice.css",
                    "includes/wizard/assets/css/style.min.css": "includes/wizard/assets/css/style.css",
                    "includes/wizard/assets/css/rtl.min.css": "includes/wizard/assets/css/rtl.css",
                    "includes/metabox/controls/assets/css/butterbean.min.css":
                        "includes/metabox/controls/assets/css/butterbean.css",
                },
            },
        },

        // Compile our sass.
        sass: {
            dist: {
                options: {
                    implementation: sass,
                    outputStyle: "compressed",
                    sourceMap: false,
                },
                files: {
                    "assets/css/widgets.css": "sass/widgets.scss",
                },
            },
        },

        // Autoprefixer.
        autoprefixer: {
            options: {
                browsers: ["last 8 versions", "ie 8", "ie 9"],
            },
            main: {
                files: {
                    "assets/css/widgets.css": "assets/css/widgets.css",
                },
            },
        },

        // Newer files checker
        newer: {
            options: {
                override: function (detail, include) {
                    if (detail.task === "php" || detail.task === "sass") {
                        include(true);
                    } else {
                        include(false);
                    }
                },
            },
        },

        // Watch for changes.
        watch: {
            options: {
                livereload: true,
                spawn: false,
            },
            scss: {
                files: ["sass/**/*.scss"],
                tasks: ["newer:sass:dist", "newer:autoprefixer:main"],
            },
        },

        // Copy the theme into the build directory
        copy: {
            build: {
                expand: true,
                src: [
                    "**",
                    "!node_modules/**",
                    "!build/**",
                    "!.git/**",
                    "!Gruntfile.js",
                    "!package.json",
                    "!package-lock.json",
                    "!.tern-project",
                    "!.gitignore",
                    "!.jshintrc",
                    "!.DS_Store",
                    "!*.map",
                    "!**/*.map",
                    "!**/Gruntfile.js",
                    "!**/package.json",
                    "!**/*~",
                ],
                dest: "build/<%= pkg.name %>/",
            },
        },

        // Compress build directory into <name>.zip
        compress: {
            build: {
                options: {
                    mode: "zip",
                    archive: "./build/<%= pkg.name %>.zip",
                },
                expand: true,
                cwd: "build/<%= pkg.name %>/",
                src: ["**/*"],
                dest: "<%= pkg.name %>/",
            },
        },

        makepot: {
            target: {
                options: {
                    domainPath: "/languages/", // Where to save the POT file.
                    exclude: [
                        // Exlude folder.
                        "build/.*",
                        "assets/.*",
                        "readme/.*",
                        "sass/.*",
                        "bower_components/.*",
                        "node_modules/.*",
                    ],
                    potFilename: "<%= pkg.name %>.pot", // Name of the POT file.
                    type: "wp-plugin", // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true, // Whether the POT-Creation-Date should be updated without other changes.
                    processPot: function (pot, options) {
                        pot.headers["plural-forms"] = "nplurals=2; plural=n != 1;";
                        pot.headers["last-translator"] = "OceanWP\n";
                        pot.headers["language-team"] = "OceanWP\n";
                        pot.headers["x-poedit-basepath"] = "..\n";
                        pot.headers["x-poedit-language"] = "English\n";
                        pot.headers["x-poedit-country"] = "UNITED STATES\n";
                        pot.headers["x-poedit-sourcecharset"] = "utf-8\n";
                        pot.headers["x-poedit-searchpath-0"] = ".\n";
                        pot.headers["x-poedit-keywordslist"] =
                            "_esc_attr__;esc_attr_x;esc_attr_e;esc_html__;esc_html_e;esc_html_x;__;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;_n_noop:1,2;_c;_nc:4c,1,2;_x:1,2c;_ex:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;\n";
                        pot.headers["x-textdomain-support"] = "yes\n";
                        return pot;
                    },
                },
            },
        },
    });

    // Dev task
    grunt.registerTask("default", ["uglify:dev", "sass:dist", "autoprefixer:main", "cssmin:prod"]);

    // Production task
    grunt.registerTask("build", ["copy"]);

    // Package task
    grunt.registerTask("package", ["compress"]);
};
