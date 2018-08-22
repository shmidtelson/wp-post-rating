// Include gulp
let gulp = require('gulp');
// Include plugins
let less = require('gulp-less');
let rename = require('gulp-rename');
let cleanCSS = require('gulp-clean-css');
let uglify = require('gulp-uglify');
let path = require('path');

gulp.task('less', function () {
    return gulp.src('./assets/less/**/*.less')
        .pipe(less({
            paths: [ path.join(__dirname, 'less', 'includes') ]
        }))
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/css'));
});

gulp.task('js', function() {
    return gulp.src('./assets/js/wp-post-rating.js')
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/js'))
});

gulp.task('watch', function() {
    gulp.watch(['./**/*.less','./**/*g.js'], ['less', 'js']);  // Watch all the .less files, then run the less task
});

gulp.task('default', ['watch']); // Default will run the 'entry' watch task

