module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    filerev: {
        js: {
          src: '.tmp/js/*.js',
          dest: 'js-dist'
        },
        css: {
          src: ['.tmp/css/*.css'],
          dest: 'css-dist'
        }
    },

    copy: {
      dist: {
        files: [
        {
          expand: true,
          src: ["css/*.css"],
          dest: ".tmp"
        },
        {
          expand: true,
          src: ["js/*.js", "!js/common.js", "!js/grid.js"],
          dest: ".tmp"
        }]
      }
    },

    concat: {
      dist: {
        src: ['js/common.js', 'js/grid.js'],
        dest: 'js/main.js'
      }
    },

    uglify: {
      dist: {
        files: [{
          expand: true,
          cwd: 'js',
          src: ['*.js'],
          dest: '.tmp/js',
          ext: '.min.js'
        }]
      }
    },

    cssmin: {
      //处理普通css文件
      dist: {
        files: [{
          expand: true,
          cwd: 'css',
          src: ['*.css'],
          dest: '.tmp/css',
          ext: '.min.css'
        }]
      },

      //处理style.css
      specialStyle: {
        options: {
          banner: '/*\n'+
                      'Theme Name: Canon\n'+
                      'Theme URI: http://xiaoshelang.ppios.com/\n'+
                      'Description: 小摄郎 Wordpress 主题\n'+
                      'Author: YangSen\n'+
                      'Author URI: http://undefinedblog.com/\n'+
                      'Version: 1.0\n'+
                      '私人主题，非开源，保留所有权利。\n'+
                      'Private theme, not open-sourced, all rights reserved.\n'+
                  '*/\n'
        },
        files: {
          ".tmp/css/style.min.css": "style-src.css"
        }
      }
    },

    clean: {
      beforeBuild: ['js-dist',
                    'css-dist'],
      afterBuild: ['.tmp']
    },

    wpreplace: {
      dist: {
        src: ['header-src.php', 'footer-src.php'],
        options: {
          templatePath: '/wp-content/themes/canon/',
          jsPath: 'js-dist',
          cssPath: 'css-dist',
          concat: [{
            src: ['common.js', 'grid.js'],
            dest: ['main.js']
          }]
        }
      }
    }


  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-filerev');
  grunt.loadNpmTasks('grunt-wp-replace');

  grunt.registerTask('dist', ['clean:beforeBuild',
                                 'concat',
                                 'uglify',
                                 'cssmin',
                                 'filerev',
                                 'clean:afterBuild',
                                 'wpreplace'
                                 ]);

  grunt.registerTask('build', ['clean',
                                'copy',
                                'filerev',
                                'linkRev']);

  grunt.registerTask('clean-all', ['clean']);

};