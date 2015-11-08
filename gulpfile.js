var gulp = require('gulp');
var sass = sass = require('gulp-sass');
var copy = copy = require('gulp-copy');
var concat = require('gulp-concat');

gulp.task('sass', function () {
    gulp.src('./app/Resources/public/sass/master.scss')
        .pipe(sass({sourceComments: 'map'}))
        .pipe(gulp.dest('./web/assets/'));
});

gulp.task('fonts', function () {
    return gulp.src('./web/assets/vendor/bootstrap-sass/assets/fonts/bootstrap/*')
        .pipe(copy('./web/assets/fonts', {prefix: 7}));
});

gulp.task('js', function() {
    return gulp.src([
        './web/assets/vendor/jquery/dist/jquery.min.js',
        './web/assets/vendor/bootstrap-sass/assets/javascripts/bootstrap.min.js',
        './app/Resources/public/js/common.js'
    ])
        .pipe(concat('all.js'))
        .pipe(gulp.dest('./web/assets/'));
});

// file to watch if changes would be made
gulp.task('watch', function () {

    var onChange = function (event) {
        console.log('File '+event.path+' has been '+event.type);
    };

    // sass
    gulp.watch([
        './app/Resources/public/sass/master.scss',
        './app/Resources/public/sass/common.scss',
        './src/Illuminati/ProductBundle/Resources/public/sass/product.scss',
    ], ['sass'])
        .on('change', onChange);

    // JavaScripts
    gulp.watch([
        './app/Resources/public/js/common.js'
    ], ['js'])
        .on('change', onChange);
});

gulp.task('default', ['sass', 'fonts', 'js']);