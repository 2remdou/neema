var gulp = require('gulp');
var gutil = require('gulp-util');
var bower = require('bower');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var minifyCss = require('gulp-minify-css');
var rename = require('gulp-rename');
var sh = require('shelljs');

var paths = {
  sass: ['./scss/**/*.scss']
};

gulp.task('default', ['sass','app']);

gulp.task('sass', function(done) {
  gulp.src('./scss/ionic.app.scss')
    .pipe(sass())
    .on('error', sass.logError)
    .pipe(gulp.dest('./www/css/'))
    .pipe(minifyCss({
      keepSpecialComments: 0
    }))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(gulp.dest('./www/css/'))
    .on('end', done);
});

gulp.task('watch', function() {
  gulp.watch(
      [
        //paths.sass,
        'www/lib/service',
        'www/js/**/*.js'
      ],
      ['app']);
      //['sass']);
});


gulp.task('install', ['git-check'], function() {
  return bower.commands.install()
    .on('log', function(data) {
      gutil.log('bower', gutil.colors.cyan(data.id), data.message);
    });
});

gulp.task('app',function () {
    return gulp.src([
        'www/lib/ionic/js/ionic.bundle.min.js',
        'www/lib/lodash/dist/lodash.min.js',
        'www/lib/ngCordova/dist/ng-cordova.min.js',
        'www/lib/angular-permission/dist/angular-permission.min.js',
        'www/lib/angular-permission/dist/angular-permission-ui.min.js',
        'www/lib/angular-messages/angular-messages.min.js',
        'www/lib/restangular/dist/restangular.min.js',
        'www/lib/angular-jwt/dist/angular-jwt.min.js',
        'www/lib/angular-timer/dist/angular-timer.min.js',
        'www/lib/moment/min/moment.min.js',
        'www/lib/moment/min/locales.min.js',
        'www/lib/humanize-duration/humanize-duration.js',
        'www/js/**/*.js',
        'www/lib/service'
    ])
        .pipe(concat('lib.js'))
        .pipe(gulp.dest('www/lib/'));
});

gulp.task('git-check', function(done) {
  if (!sh.which('git')) {
    console.log(
      '  ' + gutil.colors.red('Git is not installed.'),
      '\n  Git, the version control system, is required to download Ionic.',
      '\n  Download git here:', gutil.colors.cyan('http://git-scm.com/downloads') + '.',
      '\n  Once git is installed, run \'' + gutil.colors.cyan('gulp install') + '\' again.'
    );
    process.exit(1);
  }
  done();
});


//cp ../neema-server/web/bundles/service.js www/lib/

//grep -rl app. .|xargs sed -i "" 's/app.//g'(chercher et remplacer "app." dans le repertoire courant par '')