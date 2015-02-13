// Include gulp and gulp-sass modules
var gulp = require('gulp');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

// gulp task to build a minified CSS file
gulp.task('sass', function () { 
	gulp.src('./build/scss/main.scss')
		.pipe(sass({
			outputStyle: 'compressed'
		}))
		.pipe(gulp.dest('./assets/css'));
});

gulp.task('concat-js', function() {
  gulp.src('./build/js/**/*.js')
    .pipe(concat('main.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./assets/js'))
});

// gulp task to watch for changes to .scss files
gulp.task('watch', function() {
	gulp.watch(['./build/scss/**/*.scss'], ['sass']);
	gulp.watch('./build/js/**/*.js', ['concat-js']);
});

// When we type gulp, run the sass task, throw all the JS into one file and then keep an eye out for changes
gulp.task('default', ['sass', 'concat-js', 'watch'], function () {});