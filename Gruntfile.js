module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-contrib-copy');

    // Configuration de Grunt
    grunt.initConfig({
        copy: {
            main: {
                files: [
                    // makes all src relative to cwd
                    {expand: true, cwd: 'bower/fullcalendar/dist/', src: ['fullcalendar.min.css'], dest: 'Resources/public/css/fullcalendar'},
                    {expand: true, cwd: 'bower/fullcalendar/dist/', src: ['fullcalendar.min.js'], dest: 'Resources/public/js/fullcalendar'},
                    {expand: true, cwd: 'bower/fullcalendar/dist/lang/', src: '**', dest: 'Resources/public/js/fullcalendar/lang'},
                    {expand: true, cwd: 'bower/jquery/dist/', src: ['jquery.min.js'], dest: 'Resources/public/js/jquery'},
                    {expand: true, cwd: 'bower/moment/min/', src: ['moment-with-locales.min.js'], dest: 'Resources/public/js/moment'},
                ]
            }
        },
    });

    // Définition des tâches Grunt
    grunt.registerTask('default', ['upgrade']);
    grunt.registerTask('upgrade', ['copy:main']);
};