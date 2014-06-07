module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        autoprefixer: {
            dist: {
                files: {
                    'css/style.css': 'css/style.css',
                }
            },
        },
        sass: {
            options: {
                includePaths: ['bower_components/foundation/scss']
            },
            dist: {
                options: {
                    outputStyle: 'compressed'
                    //outputStyle: 'compact'
                },
                files: {
                    'css/style.css': 'scss/style.scss'
                }
            }
        },
        watch: {
            sass: {
                files: ['scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer'],
            },
            livereload: {
                files: ['**/*.php', 'css/style.css', '**/*.js'],
                options: {
                    livereload: true
                }
            }
        }
    });

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.registerTask('build', ['sass']);
    grunt.registerTask('default', ['build','watch']);
};
