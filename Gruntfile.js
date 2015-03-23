module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concat: {
            options: {
                separator: ';'
            },
            ui: {
                src: [
                    'public/js/src/sharp.ui.js',
                    'public/js/src/sharp.conditional_display.js'
                ],
                dest: 'public/js/<%= pkg.name %>.ui.js'
            },
            advancedsearch: {
                src: [
                    'public/bower_components/selectize/dist/js/selectize.js',
                    'public/js/src/advancedsearch/sharp.adv.js',
                    'public/js/src/advancedsearch/sharp.adv.tags.js'
                ],
                dest: 'public/js/<%= pkg.name %>.advancedsearch.js'
            },
            form: {
                src: [
                    // Tags, ref
                    'public/bower_components/microplugin/src/microplugin.js',
                    'public/bower_components/sifter/sifter.js',
                    'public/bower_components/selectize/dist/js/selectize.js',
                    // Date
                    'public/bower_components/datetimepicker/jquery.datetimepicker.js',
                    // Markdown
                    //'public/bower_components/leptureeditor/src/intro.js',
                    //'public/bower_components/leptureeditor/src/editor.js',
                    //'public/bower_components/leptureeditor/vendor/codemirror.js',
                    //'public/bower_components/leptureeditor/vendor/markdown.js',
                    //'public/bower_components/leptureeditor/docs/marked.js',
                    'public/bower_components/mirrormark/dist/js/mirrormark.package.js',
                    // Upload
                    'public/bower_components/jquery-file-upload/js/jquery.iframe-transport.js',
                    'public/bower_components/jquery-file-upload/js/jquery.fileupload.js',
                    // Image crop
                    'public/bower_components/imgareaselect/jquery.imgareaselect.dev.js',
                    // Sharp
                    'public/js/src/sharp.form.js',
                    'public/js/src/sharp.date.js',
                    'public/js/src/sharp.embed.js',
                    'public/js/src/sharp.markdown.js',
                    'public/js/src/sharp.tags.js',
                    'public/js/src/sharp.ref.js',
                    'public/js/src/sharp.refSublistItem.js',
                    'public/js/src/sharp.upload.js',
                    'public/js/src/sharp.imagecrop.js',
                    'public/js/src/sharp.list.js'
                ],
                dest: 'public/js/<%= pkg.name %>.form.js'
            }
        },

        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'public/js/<%= pkg.name %>.ui.min.js': ['<%= concat.ui.dest %>'],
                    'public/js/<%= pkg.name %>.advancedsearch.min.js': ['<%= concat.advancedsearch.dest %>'],
                    'public/js/<%= pkg.name %>.form.min.js': ['<%= concat.form.dest %>']
                }
            }
        },

        less: {
            development: {
                options: {
                    paths: ["public/css/less"]
                },
                files: {
                    "public/css/sharp.css": "public/css/less/main.less"
                }
            }
        },

        cssmin: {
            target: {
                files: {
                    'public/css/sharp.min.css': [
                        'public/bower_components/mirrormark/dist/css/mirrormark.package.css',
                        'public/css/sharp.css'
                    ]
                }
            }
        },

        watch: {
            js: {
                files: [
                    '<%= concat.ui.src %>',
                    '<%= concat.advancedsearch.src %>',
                    '<%= concat.form.src %>'
                ],
                tasks: ['concat', 'uglify']
            },

            css: {
                files: ['public/css/less/**/*.less'],
                tasks: ['less']
            },

            mincss: {
                files: ['public/css/sharp.css'],
                tasks: ['cssmin']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['concat', 'uglify', 'less', 'mincss']);

};