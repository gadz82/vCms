// Load Grunt
module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Tasks
        sass: { // Begin Sass Plugin
            dist: {
                options: {
                    sourcemap: 'none'
                },
                files: [{
                    expand: true,
                    cwd: 'sass',
                    src: ['*.scss'],
                    dest: 'css/compiled',
                    ext: '.css'
                },
                {
                    expand: true,
                    cwd: 'sass/shortcodes',
                    src: ['*.scss'],
                    dest: 'css/compiled/shortcodes',
                    ext: '.css'
                }]
            }
        },
        watch: { // Compile everything into one task with Watch Plugin
            css: {
                files: ['sass/*.scss', 'sass/shortcodes/*.scss'],
                tasks: ['sass']
            }
        }
    });
    // Load Grunt plugins
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Register Grunt tasks
    grunt.registerTask('default', ['watch']);
};