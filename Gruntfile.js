module.exports = function (grunt) {
    const sass = require("sass");

    // require it at the top and pass in the grunt instance
    require("time-grunt")(grunt);

    // Load all Grunt tasks
    require("jit-grunt")(grunt, {});

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        browserify: {
            prod: {
              files: {
                "includes/customizer/assets/script.min.js": "includes/customizer/assets/script.js",
              },
              options: {
                transform: [["babelify", { presets: ["@babel/preset-env"] }]],
              },
            },
            dev: {
              files: {
                "includes/customizer/assets/script.min.js": "includes/customizer/assets/script.js",
              },
              options: {
                transform: [["babelify", { presets: ["@babel/preset-env"] }]],
              },
            },
        },

        // Concat and Minify our js.
        uglify: {
            dev: {
                files: {
                    "includes/menu-icons/js/admin.min.js": "includes/menu-icons/js/admin.js",
                    "includes/metabox/assets/js/metabox.min.js": "includes/metabox/assets/js/metabox.js",
                    "includes/panel/assets/js/scripts.min.js": "includes/panel/assets/js/scripts.js",
                    "includes/panel/assets/js/demos.min.js": "includes/panel/assets/js/demos.js",
                    "includes/wizard/assets/js/wizard.min.js": "includes/wizard/assets/js/wizard.js",
                    "includes/metabox/controls/assets/js/select2.full.min.js": "includes/metabox/controls/assets/js/select2.full.js",
                    "includes/metabox/controls/assets/js/butterbean.min.js": "includes/metabox/controls/assets/js/butterbean.js",
                    "includes/widgets/js/insta-admin.min.js": "includes/widgets/js/insta-admin.js",
                    "includes/widgets/js/mailchimp.min.js": "includes/widgets/js/mailchimp.js",
                    "includes/widgets/js/flickr.min.js": "includes/widgets/js/flickr.js",
                    "includes/widgets/js/share.min.js": "includes/widgets/js/share.js",
                    "includes/shortcodes/js/shortcode.min.js": "includes/shortcodes/js/shortcode.js",
                    "includes/preloader/assets/js/preloader.min.js": "includes/preloader/assets/js/preloader.js",
                    "includes/customizer/assets/script.min.js": "includes/customizer/assets/script.min.js",
                    "includes/preloader/assets/js/customize-preview.min.js": "includes/preloader/assets/js/customize-preview.js",
                    "includes/themepanel/assets/js/theme-panel.min.js": "includes/themepanel/assets/js/theme-panel.js",
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
                    "includes/preloader/assets/css/preloader.min.css": "includes/preloader/assets/css/preloader.css",
                    "includes/customizer/assets/style.min.css": "includes/customizer/assets/style.css",
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
                    "includes/preloader/assets/css/preloader.css": "includes/preloader/assets/css/preloader.scss",
                    "includes/customizer/assets/style.css": "includes/customizer/assets/style.scss",
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
                    "includes/preloader/assets/css/preloader.css": "includes/preloader/assets/css/preloader.css",
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
                    "!includes/post-settings/node_modules/**",
                    "!includes/post-settings/src/**",
                    "!build/**",
                    "!src/**",
                    "!.git/**",
                    "!vendor/**",
                    "!composer.json",
                    "!composer.lock",
                    "!Gruntfile.js",
                    "!package.json",
                    "!package-lock.json",
                    "!includes/post-settings/package.json",
                    "!includes/post-settings/package-lock.json",
                    "!.tern-project",
                    "!.gitignore",
                    "!.jshintrc",
                    "!.DS_Store",
                    "!*.map",
                    "!**/*.map",
                    "!**/Gruntfile.js",
                    "!**/package.json",
                    "!**/package-lock.json",
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
    });

    // Dev task
    grunt.registerTask("default", [ "browserify:prod", "browserify:dev", "uglify:dev", "sass:dist", "autoprefixer:main", "cssmin:prod"]);

    // Production task
    grunt.registerTask("build", ["copy"]);

    // Package task
    grunt.registerTask("package", ["compress"]);
};
