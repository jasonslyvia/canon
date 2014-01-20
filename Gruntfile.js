module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    filerev: {
        js: {
          src: '.tmp/js/*.js',
          dest: 'js'
        },
        css: {
          src: ['.tmp/css/*.css'],
          dest: 'css'
        }
    },

    copy: {
      build: {
        files: [
        {
          expand: true,
          src: ["css/*.css"],
          dest: ".tmp"
        },
        {
          expand: true,
          src: ["js/*.js"],
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
      dist: {
        files: [{
          expand: true,
          cwd: 'css',
          src: ['*.css'],
          dest: '.tmp/css',
          ext: '.min.css'
        }]
      },

      specialStyle: {
        files: {
          "style.css": "style-src.css"
        }
      }
    },

    clean: {
      beforeBuild: ['js/*.min.js', 'css/*.min.css', 'js/*.????????.js', 'css/*.????????.css'],
      afterBuild: ['.tmp']
    },

    linkRev: {
      dist: {
        src: ['header-src.php', 'footer-src.php'],
        options: {
          jsPath: '/wp-content/themes/canon/js/',
          cssPath: '/wp-content/themes/canon/css/',
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

  grunt.registerMultiTask('linkRev', 'link filereved assets to certain path', function(){
    var options = this.options();
    var jsPath = options.jsPath;
    var cssPath = options.cssPath;

    //简化filerev保存的文件名修改信息，去掉多余的路径信息，只保留新旧文件名
    //形如 {oldFileName.css : newFileName.css}
    var newFiles = {};
    for(var i in grunt.filerev.summary){
      var newKey = i.split('/').pop();
      newKey = newKey.replace(/\.min/i, '');
      var newValue = grunt.filerev.summary[i].split('/').pop();

      newFiles[newKey] = newValue;
    }

    //针对每一个文件进行遍历，依次进行正则匹配
    this.files.forEach(function(file){
      //首先确保文件存在
      file.src.filter(function(filePath){
        if (!grunt.file.exists(filePath)) {
          grunt.log.warn('source file ' + filePath + ' not exist');
          return false;
        }
        else{
          return true;
        }
      }).forEach(function(filePath){

        //获得文件内容
        var content = grunt.file.read(filePath);
        var cssRegExp = /(<link.*?href=[\'\"])(.*?)([\'\"][^>]*>)/gi;
        var jsRegExp = /(<script.*?src=[\'\"])(.*?)([\'\"][^>]*>)/gi;
        var concat = options.concat;
        var updated = false;

        //首先匹配出当前文件中的所有css引用
        var match = cssRegExp.exec(content);
        while(match){
          var css = match[2].split('/').pop();

          //若匹配filerev过的文件名
          if (newFiles.hasOwnProperty(css)) {

            content = content.replace(new RegExp(match[0], "i"), match[1] + cssPath + newFiles[css] + match[3]);
            updated = true;
          }

          match = cssRegExp.exec(content);
        }

        //其次匹配当前文件中国年的所有js引用
        match = jsRegExp.exec(content);
        while(match){
          var js = match[2].split('/').pop();

          if (newFiles.hasOwnProperty(js)) {
            content = content.replace(new RegExp(match[0], "i"), match[1] + jsPath + newFiles[js] + match[3]);
            updated = true;
          }

          match = jsRegExp.exec(content);
        }

        //如果替换了新的引用，则再次写入文件
        if (updated) {
          filePath = filePath.replace(/-src/i, '');
          grunt.file.write(filePath, content);
        }
      });
    });
  });

  grunt.registerTask('appendWordpressStyleInfo', function(){

  });

  grunt.registerTask('dist', ['clean:beforeBuild',
                                 'concat',
                                 'uglify',
                                 'cssmin',
                                 'filerev',
                                 'clean:afterBuild',
                                 'linkRev'
                                 ]);

  grunt.registerTask('build', ['clean',
                                'copy:build',
                                'filerev',
                                'linkRev']);

  grunt.registerTask('clean-all', ['clean']);

};