var gulp = require('gulp'), // Подключаем Gulp
    concat = require('gulp-concat'), // Подключаем gulp-concat (для конкатенации файлов)
    uglify = require('gulp-uglifyjs'), // Подключаем gulp-uglifyjs (для сжатия JS)
    cssnano = require('gulp-cssnano'), // Подключаем пакет для минификации CSS
    rename = require('gulp-rename'); // Подключаем библиотеку для переименования файлов


gulp.task('js', function () {
    return gulp.src([ // Берем все необходимые библиотеки
        //  'assets/js/jquery/jquery.js',
        'assets/js/jquery/jquery.form.js',
        //  'assets/js/bootstrap.js',
        'assets/js/jquery/picker.js',
        'assets/js/jquery/picker.date.js',
        'assets/js/jquery/picker.time.js',
        'assets/js/jquery/ru_RU.js',
        'assets/js/bootstrap3-typeahead.js',
        'assets/js/bootstrap-tags.js',
        'assets/js/bootstrap-treeview.js',
        'assets/js/bootstrap-datepicker.js',
        'assets/js/bootstrap-datetimepicker.js',
        'assets/js/zippy.js'

    ])
        .pipe(concat('zippy-bundle.min.js')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});


gulp.task('jsua', function () {
    return gulp.src([ // Берем все необходимые библиотеки
        //  'assets/js/jquery/jquery.js',
        'assets/js/jquery/jquery.form.js',
        //  'assets/js/bootstrap.js',
        'assets/js/jquery/picker.js',
        'assets/js/jquery/picker.date.js',
        'assets/js/jquery/picker.time.js',
        'assets/js/jquery/ua_UA.js',
        'assets/js/bootstrap3-typeahead.js',
        'assets/js/bootstrap-tags.js',
        'assets/js/bootstrap-treeview.js',
        'assets/js/bootstrap-datepicker-ua.js',
        'assets/js/bootstrap-datetimepicker-ua.js',
        'assets/js/zippy.js'

    ])
        .pipe(concat('zippy-bundle-ua.min.js')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('assets/js')); // Выгружаем в папку app/js
});

gulp.task('css', function () {
    return gulp.src([ // Берем все необходимые библиотеки

        //'assets/css/bootstrap.css',    
        'assets/css/fontawesome-all.css',

        'assets/css/classic.css',
        'assets/css/classic.date.css',
        'assets/css/classic.time.css',
        'assets/css/bootstrap-tags.css',
        'assets/css/bootstrap-treeview.css',
        'assets/css/bootstrap-datepicker.css',
        'assets/css/bootstrap-datetimepicker.css'


    ])
        .pipe(concat('zippy-bundle.min.css')) // Собираем их в кучу в новом файле libs.min.js
        .pipe(cssnano()) // Сжимаем   файл
        .pipe(gulp.dest('assets/css')); // Выгружаем в папку app/js
});

 
 
 

 