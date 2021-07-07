var gulp = require('gulp'), // Подключаем Gulp
    concat = require('gulp-concat'), // Подключаем gulp-concat (для конкатенации файлов)
    uglify = require('gulp-uglifyjs'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename = require('gulp-rename')  // Подключаем библиотеку для переименования файлов
   ;



gulp.task('js', function () {
    return gulp.src([ // Берем все необходимые библиотеки
 
 
 
        'assets/js/bootstrap3-typeahead.js',
        'assets/js/bootstrap-tags.js',
        'assets/js/bootstrap-treeview.js',
   
        'assets/js/zippy.js'

    ])
        .pipe(concat('zippy-bundle.js')) // Собираем их в кучу в новом файле libs.min.js
      //  .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});


gulp.task('jsua', function () {
    return gulp.src([ // Берем все необходимые библиотеки
 
        'assets/js/bootstrap3-typeahead.js',
        'assets/js/bootstrap-tags.js',
        'assets/js/bootstrap-treeview.js',
 
        'assets/js/zippy.js'

    ])
        .pipe(concat('zippy-bundle-ua.min.js')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});

gulp.task('css', function () {
    return gulp.src([ // Берем все необходимые библиотеки

        
        'assets/css/fontawesome-all.css',

   
        'assets/css/bootstrap-tags.css',
        'assets/css/bootstrap-treeview.css',
  


    ])
        .pipe(concat('zippy-bundle.css')) // Собираем их в кучу в новом файле libs.min.js
       // .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('assets/css')); // Выгружаем в папку app/js
});


/*
npm install --global gulp
    
npm install gulp --save-dev
 npm install gulp-concat
 npm install gulp-uglifyjs
 npm install gulp-cssnano
 npm install gulp-rename
 
 gulp.js
 gulp.css
 
*/
 
 
 

 