var gulp = require('gulp');
var sass = require('gulp-sass');
var copy = require('gulp-copy');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');

gulp.task('sass', function () {
    gulp.src('./app/Resources/public/sass/master.scss')
        .pipe(sass({sourceComments: 'map'}))
        .pipe(uglifycss({
            "max-line-len": 80
        }))
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
            './app/Resources/public/js/common.js',
            './src/Illuminati/CartBundle/Resources/public/js/checkout.js',
            './src/Illuminati/UserBundle/Resources/public/js/userBundle.js',
            './src/Illuminati/OrderBundle/Resources/public/js/orderBundle.js'

    ])
        .pipe(concat('all.js'))
        .pipe(uglify())
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
            './src/Illuminati/CartBundle/Resources/public/sass/cart.scss',
            './src/Illuminati/MainBundle/Resources/public/sass/mainBundle.scss'
        ], ['sass'])
        .on('change', onChange);

    // JavaScripts
    gulp.watch([
            './app/Resources/public/js/common.js',
            './src/Illuminati/CartBundle/Resources/public/js/checkout.js',
            './src/Illuminati/OrderBundle/Resources/public/js/orderBundle.js'
        ], ['js'])
        .on('change', onChange);
});

gulp.task('default', ['sass', 'fonts', 'js']);