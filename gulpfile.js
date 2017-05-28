var gulp      = require('gulp'), // Подключаем Gulp
    concat      = require('gulp-concat'), // Подключаем gulp-concat (для конкатенации файлов)
    uglify      = require('gulp-uglifyjs'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano     = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename      = require('gulp-rename'); // Подключаем библиотеку для переименования файлов
    
 var less = require('gulp-less');    
    
  gulp.task('js', function() {
    return gulp.src([ // Берем все необходимые библиотеки
        'vendor/leon-mbs/zippy/assets/js/jquery/jquery.js',  
      //  'vendor/leon-mbs/zippy/assets/js/jquery/jquery.cookie.js',  
        'vendor/leon-mbs/zippy/assets/js/jquery/jquery.form.js',  
        'vendor/leon-mbs/zippy/assets/js/bootstrap.js',    
        'vendor/leon-mbs/zippy/assets/js/jquery/picker.js',  
        'vendor/leon-mbs/zippy/assets/js/jquery/picker.date.js',  
        'vendor/leon-mbs/zippy/assets/js/jquery/picker.time.js',  
        'vendor/leon-mbs/zippy/assets/js/jquery/ru_RU.js',  
        'vendor/leon-mbs/zippy/assets/js/bootstrap3-typeahead.js',            
        'vendor/leon-mbs/zippy/assets/js/zippy.js'    
        ])
        .pipe(concat('allzippy.min.js')) // Собираем их в кучу в новом файле libs.min.js
           .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('vendor/leon-mbs/zippy/assets/js')); // Выгружаем в папку app/js
});

  gulp.task('css', function() {
    return gulp.src([ // Берем все необходимые библиотеки

        'vendor/leon-mbs/zippy/assets/css/bootstrap.css',    
        'vendor/leon-mbs/zippy/assets/css/font-awesome.css',    
  
        'vendor/leon-mbs/zippy/assets/css/classic.css',    
        'vendor/leon-mbs/zippy/assets/css/classic.date.css',    
        'vendor/leon-mbs/zippy/assets/css/classic.time.css'      
         
          
        ])
        .pipe(concat('allzippy.min.css')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('vendor/leon-mbs/zippy/assets/css')); // Выгружаем в папку app/js
});


  gulp.task('zcljs', function() {
    return gulp.src([ // Берем все необходимые библиотеки
        'vendor/leon-mbs/zippy/assets/js/bootstrap-tags.js',  
        'vendor/leon-mbs/zippy/assets/js/bootstrap-treeview.js',  
        'vendor/leon-mbs/zippy/zcl/bt/bootstrap-datepicker.js',  
        'vendor/leon-mbs/zippy/zcl/bt/bootstrap-datetimepicker.js'  
             ])
        .pipe(concat('allzcl.min.js')) // Собираем их в кучу в новом файле libs.min.js
       //      .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('vendor/leon-mbs/zippy/assets/js')); // Выгружаем в папку app/js
});

  gulp.task('zclcss', function() {
    return gulp.src([ // Берем все необходимые библиотеки

        'vendor/leon-mbs/zippy/assets/css/bootstrap-tags.css',     
        'vendor/leon-mbs/zippy/assets/css/bootstrap-treeview.css',     
         'vendor/leon-mbs/zippy/zcl/bt/bootstrap-datepicker.css',  
        'vendor/leon-mbs/zippy/zcl/bt/bootstrap-datetimepicker.css'       
          
        ])
        .pipe(concat('allzcl.min.css')) // Собираем их в кучу в новом файле libs.min.js
       //  .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('vendor/leon-mbs/zippy/assets/css')); // Выгружаем в папку app/js
});

 gulp.task('less', function() {
    return gulp.src([ // Берем все необходимые библиотеки

        'assets/css/LESS/style.less'     
       
          
        ])
        .pipe(less())
        .pipe(concat('homer.css')) // Собираем их в кучу в новом файле libs.min.js
       //  .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('assets/css')); // Выгружаем в папку app/js
});