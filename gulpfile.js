    /**
 * Created by mdoutoure on 18/11/2015.
 */
var gulp = require('gulp'),
    concat = require('gulp-concat'),
    concatCss = require('gulp-concat-css');

gulp.task('concatJs',function () {
    return gulp.src([
        'web/bundles/jquery/dist/jquery.min.js',
        'web/bundles/angular/angular.min.js',
        'web/bundles/bootstrap/dist/js/bootstrap.min.js',
        'web/bundles/angular-ui-router/release/angular-ui-router.min.js',
        'web/bundles/lodash/dist/lodash.min.js',
        'web/bundles/restangular/dist/restangular.min.js',
        'web/bundles/angular-cookies/angular-cookies.min.js',
        'web/bundles/angular-ui-notification/dist/angular-ui-notification.min.js',
        'web/bundles/spin.js/spin.js',
        'web/bundles/angular-spinner/angular-spinner.min.js',
        'web/bundles/angular-permission/dist/angular-permission.min.js',
        'web/bundles/angular-jwt/dist/angular-jwt.min.js',
        'web/bundles/es5-shim/es5-shim.min.js',
        'web/bundles/es5-shim/es5-sham.min.js',
        'web/bundles/angular-file-upload/dist/angular-file-upload.min.js',
        'web/bundles/angular-sanitize/angular-sanitize.min.js',
        'web/bundles/ui-select/dist/select.min.js',
        'web/bundles/angular-modal-service/dst/angular-modal-service.min.js',
        'web/js/**/*.js'
    ])
        .pipe(concat('all.js'))
        .pipe(gulp.dest('web/bundles/'));
});
//

gulp.task('concatCss',function(){
    return gulp.src([
        'web/bundles/bootstrap/dist/css/bootstrap.min.css',
        'web/bundles/angular-ui-notification/dist/angular-ui-notification.min.css',
        'web/bundles/font-awesome/font-awesome.min.css',
        'web/bundles/ui-select/dist/select.min.css',

        'web/css/*.css',
    ])
        .pipe(concatCss('all.css',{rebaseUrls:false}))
        .pipe(gulp.dest('web/bundles/'));
});

    //aws s3 cp web/bundles/compiled/all.js s3://nicetruc/js/
    //aws s3 cp web/client/app/views/css/all.css s3://nicetruc/css/

gulp.task('default', function() {
    gulp.start('concatJs', 'concatCss');
});

gulp.task('watch', function() {
    gulp.watch([
            'web/css/*.css',
            'web/js/**/*.js'
        ],
        //['clean'],
        ['default']
    );
});