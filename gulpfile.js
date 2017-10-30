var gulp      = require('gulp'), // Подключаем Gulp
    concat      = require('gulp-concat'), // Подключаем gulp-concat (для конкатенации файлов)
    uglify      = require('gulp-uglifyjs'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano     = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename      = require('gulp-rename'); // Подключаем библиотеку для переименования файлов
    
   
    
  gulp.task('js', function() {
    return gulp.src([ // Берем все необходимые библиотеки
        'assets/js/jquery/jquery.js',  
        'assets/js/jquery/jquery.form.js',  
        'assets/js/bootstrap.js',    
        'assets/js/jquery/picker.js',  
        'assets/js/jquery/picker.date.js',  
        'assets/js/jquery/picker.time.js',  
        'assets/js/jquery/ru_RU.js',  
        'assets/js/bootstrap3-typeahead.js',            
        'assets/js/zippy.js'    
        ])
        .pipe(concat('allzippy.min.js')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});

  gulp.task('css', function() {
    return gulp.src([ // Берем все необходимые библиотеки

        'assets/css/bootstrap.css',    
        'assets/css/font-awesome.css',    
  
        'assets/css/classic.css',    
        'assets/css/classic.date.css',    
        'assets/css/classic.time.css'      
         
          
        ])
        .pipe(concat('allzippy.min.css')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('assets/css')); // Выгружаем в папку app/js
});


  gulp.task('zcljs', function() {
    return gulp.src([ // Берем все необходимые библиотеки
        'assets/js/bootstrap-tags.js',  
        'assets/js/bootstrap-treeview.js',  
        'zcl/bt/bootstrap-datepicker.js',  
        'zcl/bt/bootstrap-datetimepicker.js'  
             ])
        .pipe(concat('allzcl.min.js')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});

  gulp.task('zclcss', function() {
    return gulp.src([ // Берем все необходимые библиотеки

        'assets/css/bootstrap-tags.css',     
        'assets/css/bootstrap-treeview.css',     
         'zcl/bt/bootstrap-datepicker.css',  
        'zcl/bt/bootstrap-datetimepicker.css'       
          
        ])
        .pipe(concat('allzcl.min.css')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('assets/css')); // Выгружаем в папку app/js
});

 