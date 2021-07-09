// Include gulp
const gulp = require('gulp');
// Include plugins
const sass = require('gulp-sass')(require('node-sass'));
const rename = require('gulp-rename');
const cleanCSS = require('gulp-clean-css');
const uglify = require('gulp-uglify');
const autoprefixer = require('gulp-autoprefixer');
const babel = require('gulp-babel');

// sass.compiler = require('node-sass');

gulp.task('sass', () => {
    return gulp.src('./assets/sass/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('js', () => {
    return gulp.src(['./assets/js/*.js', '!./assets/js/*.min.js'])
        .pipe(babel())
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/js/min'))
    }
);

gulp.task('watch', () => {
    gulp.watch(['./assets/sass/*.scss', './assets/js/*.js'], gulp.series('sass', 'js'));  // Watch all the .less files, then run the less task
});

gulp.task('default', ); // Default will run the 'entry' watch task

