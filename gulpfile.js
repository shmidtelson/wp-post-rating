// Include gulp
let gulp = require('gulp');
// Include plugins
let sass = require('gulp-sass');
let rename = require('gulp-rename');
let cleanCSS = require('gulp-clean-css');
let uglify = require('gulp-uglify');
let autoprefixer = require('gulp-autoprefixer');
let path = require('path');

sass.compiler = require('node-sass');

gulp.task('sass', function () {
    return gulp.src('./assets/sass/**/*.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('js', function() {
    return gulp.src(['./assets/js/*.js', '!./assets/js/*.min.js'])
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/js/min'))
});

gulp.task('watch', function() {
    gulp.watch(['./assets/sass/*.scss','./assets/js/*.js'], ['sass', 'js']);  // Watch all the .less files, then run the less task
});

gulp.task('default', ['watch']); // Default will run the 'entry' watch task

