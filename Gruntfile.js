module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            options: {
                separator: ';'
            },
            ui: {
                src: [
                    'resources/assets/bower_components/jquery/dist/jquery.js',
                    'resources/assets/bower_components/bootstrap/dist/js/bootstrap.js',
                    'resources/assets/bower_components/dragula.js/dist/dragula.js',
                    'resources/assets/bower_components/sweetalert/dist/sweetalert.min.js',
                    'resources/assets/js/sharp.ui.js',
                    'resources/assets/js/sharp.commands.js',
                    'resources/assets/js/sharp.conditional_display.js'
                ],
                dest: 'resources/assets/<%= pkg.name %>.ui.js'
            },
            form: {
                src: [
                    // Tags, ref
                    'resources/assets/bower_components/microplugin/src/microplugin.js',
                    'resources/assets/bower_components/sifter/sifter.js',
                    'resources/assets/bower_components/selectize/dist/js/selectize.js',
                    // Date
                    'resources/assets/bower_components/datetimepicker/jquery.datetimepicker.js',
                    // Markdown
                    'resources/assets/bower_components/mirrormark/dist/js/mirrormark.package.js',
                    // Upload
                    'resources/assets/bower_components/dropzone/dist/dropzone.js',
                    // Image crop
                    'resources/assets/bower_components/imgareaselect/jquery.imgareaselect.dev.js',
                    // Sharp
                    'resources/assets/js/sharp.form.js',
                    'resources/assets/js/sharp.date.js',
                    'resources/assets/js/sharp.markdown.js',
                    'resources/assets/js/sharp.tags.js',
                    'resources/assets/js/sharp.ref.js',
                    'resources/assets/js/sharp.refSublistItem.js',
                    'resources/assets/js/sharp.upload.js',
                    'resources/assets/js/sharp.imagecrop.js',
                    'resources/assets/js/sharp.customSearch.js',
                    'resources/assets/js/sharp.list.js'
                ],
                dest: 'resources/assets/<%= pkg.name %>.form.js'
            }
        },

        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'resources/assets/<%= pkg.name %>.ui.min.js': ['<%= concat.ui.dest %>'],
                    'resources/assets/<%= pkg.name %>.form.min.js': ['<%= concat.form.dest %>']
                }
            }
        },

        less: {
            development: {
                options: {
                    paths: [
                        "resources/assets/less"
                    ]
                },
                files: {
                    "resources/assets/sharp.css": "resources/assets/less/main.less"
                }
            }
        },

        cssmin: {
            target: {
                files: {
                    'resources/assets/sharp.min.css': [
                        'resources/assets/bower_components/mirrormark/dist/css/mirrormark.package.css',
                        'resources/assets/bower_components/sweetalert/dist/sweetalert.css',
                        'resources/assets/bower_components/dragula.js/dist/dragula.css',
                        'resources/assets/sharp.css'
                    ]
                }
            }
        },

        copy: {
            main: {
                files: [{
                    expand: true,
                    flatten: true,
                    src: ['resources/assets/bower_components/fontawesome/fonts/**'],
                    dest: 'resources/assets/fonts/',
                    filter: 'isFile'
                }]
            }
        },

        watch: {
            js: {
                files: [
                    '<%= concat.ui.src %>',
                    '<%= concat.form.src %>'
                ],
                tasks: ['concat', 'uglify']
            },

            css: {
                files: ['resources/assets/less/**/*.less'],
                tasks: ['less']
            },

            mincss: {
                files: ['resources/assets/sharp.css'],
                tasks: ['cssmin']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.registerTask('default', ['concat', 'uglify', 'less', 'cssmin', 'copy']);

};