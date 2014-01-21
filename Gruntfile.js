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

    linkRev: {
      dist: {
        src: ['header-src.php', 'footer-src.php'],
        options: {
          templatePath: '/wp-content/themes/canon/',
          jsPath: '/wp-content/themes/canon/js-dist/',
          cssPath: '/wp-content/themes/canon/css-dist/',
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
    var templatePath = options.templatePath;

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
        var jsRegExp = /(<script.*?src=[\'\"])(.*?)([\'\"][^>]*?>\s*<\/script>)/gi;
        var concat = options.concat;
        var updated = false;
        var cssCount = 0,
            jsCount = 0;

        //首先匹配出当前文件中的所有css引用
        var match = cssRegExp.exec(content);
        while(match){
          var css = match[2].split('/').pop();
          //若匹配filerev过的文件名
          if (newFiles.hasOwnProperty(css)) {
            var nCss = newFiles[css];

            //若是style.css，
            //将其从css-dist文件夹移到根目录下以便wordpress识别该主题
            if (css.match(/^style\.css$/i)) {
              grunt.file.copy('css-dist/'+nCss, nCss, {encoding: 'utf8'});
              content = content.replace(new RegExp(match[0], "i"), match[1] + templatePath + nCss + match[3]);
              grunt.log.subhead('style.css found! this is a Wordpress theme in no doubt!');
            }
            else{
              content = content.replace(new RegExp(match[0], "i"), match[1] + cssPath + nCss + match[3]);
            }
            updated = true;
            cssCount++;
            grunt.log.debug('css replace: ' + css + ' : ' + nCss);
          }
          match = cssRegExp.exec(content);
        }

        //其次匹配当前文件中的所有js引用
        //WARN：若文件中多次出现同一js的引入，同时存在concat的js
        //      则可能出现将多行src js替换为1个concat后的js后，
        //      正则匹配时跳过了若干个字符，参考js regexp.lastIndex
        while((match = jsRegExp.exec(content)) !== null){
          var js = match[2].split('/').pop();

          //若该js被引用
          if (newFiles.hasOwnProperty(js)) {

            //判断其是否被concat
            var concated = concat.every(function(el, i){
              //若被concat
              if(el.src.join(' ').indexOf(js) !== -1){
                //若已替换则直接删除本条引用
                if (el.replaced) {
                  content = content.replace(new RegExp(match[0], "i"), '');
                  //改变lastIndex确保匹配到文件中的每一个字符
                  jsRegExp.lastIndex -= match[0].length;
                  grunt.log.debug('remove concat src file ' + js);
                }
                //否则将concat后对应的文件替换，同时添加已替换属性
                else{
                  //若指定的concat后的文件不存在
                  if (!grunt.file.exists('js-dist/'+newFiles[el.dest])) {
                    grunt.fail.warn('concat file '+el.dest+' for '+el.src.join(',')+' does not exist!');
                  }

                  var concatedJs = match[1] + jsPath + newFiles[el.dest] + match[3];
                  content = content.replace(new RegExp(match[0], "i"), concatedJs);
                  //改变lastIndex确保匹配到文件中的每一个字符
                  jsRegExp.lastIndex -= (match[0].length - concatedJs.length);
                  el.replaced = true;

                  grunt.log.debug('replace concat file '+newFiles[el.dest]+' for '+js);

                  jsCount++;
                  updated = true;
                }

                //不考虑a,b被concat为x，同时a,d被concat为y的情况
                //不再继续遍历concat
                return false;
              }
              return true;
            });

            //concated为true说明concat.every全部return true，即
            //该js并未被concat
            //因此直接替换为filerev后的文件即可
            if (concated) {
              content = content.replace(new RegExp(match[0], "i"), match[1] + jsPath + newFiles[js] + match[3]);
              updated = true;
              jsCount++;
              grunt.log.debug('js replace: ' + js + ' : ' + newFiles[js]);
            }
          }

        }

        //如果替换了新的引用，则再次写入文件
        if (updated) {
          grunt.log.ok(cssCount + ' css and ' + jsCount + ' js are replaced in '+filePath);
          filePath = filePath.replace(/-src/i, '');
          grunt.file.write(filePath, content);
        }
      });
    });

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
                                'copy',
                                'filerev',
                                'linkRev']);

  grunt.registerTask('clean-all', ['clean']);

};