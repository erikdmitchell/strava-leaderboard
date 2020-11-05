// Project configuration
var buildInclude = [
        // include common file types
        '**/*.php',
        '**/*.html',
        '**/*.css',
        '**/*.js',
        '**/*.svg',
        '**/*.ttf',
        '**/*.otf',
        '**/*.eot',
        '**/*.woff',
        '**/*.woff2',
        '**/*.png',
        
        // include specific files and folders
        //'screenshot.png',
        'readme.txt',
        '/{vendor,vendor/**/*}',

        // exclude files and folders
        '!./composer.json', 
        '!./composer.lock',
        '!./gulpfile.js',
        '!./{node_modules,node_modules/**/*}',
        '!./package.json',
        '!./phpcs.ruleset.xml',
        '!./{sass,sass/**/*}',
        '!./.stylelintrc',
        //'!./{vendor,vendor/**/*}',
        '!svn/**'
    ];
    
var phpSrc = [
        '**/*.php', // Include all files    
        '!node_modules/**/*', // Exclude node_modules
        '!vendor/**' // Exclude vendor   
    ];

var cssInclude = [
        // include css
        '**/*.css',

        // exclude files and folders
        '!**/*.min.css',
        '!node_modules/**/*',
        '!style.css',
        '!inc/css/*',
        '!vendor/**'
    ];
    
var jsInclude = [
        // include js
        '**/*.js',

        // exclude files and folders
        '!**/*.min.js',
        '!node_modules/**/*',
        '!vendor/**',
        '!**/gulpfile.js',
        '!inc/js/html5shiv.js',
        '!inc/js/respond.js',             
    ];    

// Load plugins
const gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'), // Autoprefixing magic
    minifycss = require('gulp-uglifycss'),
    filter = require('gulp-filter'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    newer = require('gulp-newer'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    runSequence = require('run-sequence'),
    gulpsass = require('gulp-sass'),
    plugins = require('gulp-load-plugins')({
        camelize: true
    }),
    ignore = require('gulp-ignore'), // Helps with ignoring files and directories in our run tasks
    plumber = require('gulp-plumber'), // Helps prevent stream crashing on errors
    cache = require('gulp-cache'),
    sourcemaps = require('gulp-sourcemaps'),
    jshint = require('gulp-jshint'), // JSHint plugin
    stylish = require('jshint-stylish'), // JSHint Stylish plugin
    stylelint = require('gulp-stylelint'), // stylelint plugin
    gulpphpcs = require('gulp-phpcs'), // Gulp plugin for running PHP Code Sniffer.
    gphpcbf = require('gulp-phpcbf'), // PHP Code Beautifier
    gutil = require('gulp-util'), // gulp util
    gzip = require('gulp-zip'), // gulp zip
    beautify = require('gulp-jsbeautifier'),
    cssbeautify = require('gulp-cssbeautify');

/**
 * Styles
 */
 
// compile sass
function sass(done) {
  return (
    gulp.src('./sass/*.scss')
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(gulpsass({
            errLogToConsole: true,
            outputStyle: 'expanded',
        }))
        .pipe(sourcemaps.write({
            includeContent: false
        }))
        .pipe(sourcemaps.init({
            loadMaps: true
        }))
        .pipe(autoprefixer('last 2 version', '> 1%', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1', 'ios 6', 'android 4'))
        .pipe(sourcemaps.write('.'))
        .pipe(plumber.stop())
        .pipe(gulp.dest('./assets/css/'))
        
  );
  done();
}

// minify all css
function mincss(done) {
  return (
    gulp.src(cssInclude)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(sourcemaps.write({
            includeContent: false
        }))
        .pipe(sourcemaps.init({
            loadMaps: true
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(plumber.stop())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(minifycss({
            maxLineLen: 80
        }))
        .pipe(gulp.dest('./'))
        
  );
  done();
}

// css linting with Stylelint.
function lintcss(done) {
  return (
    gulp.src(cssInclude)
        .pipe(stylelint({
          reporters: [
            {formatter: 'string', console: true}
          ]
        }))
  );
  done();
}

// make pretty
function beautifycss(done) {
  return (
    gulp.src(cssInclude)
        .pipe(cssbeautify())
        .pipe(gulp.dest('./'))
  );
  done();
}

/**
 * Scripts
 */

// min all js files
function scripts() {
  return (
    gulp.src(jsInclude)
      .pipe(rename({
          suffix: '.min'
      }))
      .pipe(uglify())
      .pipe(gulp.dest("./"))
  );
}

// js linting with JSHint.
function lintjs(done) {
  return (
    gulp.src(jsInclude)
        .pipe(jshint())
        .pipe(jshint.reporter(stylish))
  );
  done();
}

// make pretty
function beautifyjs(done) {
  return (
    gulp.src(jsInclude)
        .pipe(beautify())
        .pipe(gulp.dest('./'))
  );
  done();
}

/**
 * PHP
 */

// PHP Code Sniffer.
function phpcs(done) {
  return (
    gulp.src(phpSrc)
        .pipe(gulpphpcs({
            bin: 'vendor/bin/phpcs',
            standard: './phpcs.ruleset.xml',
            warningSeverity: 0
        }))
        .pipe(gulpphpcs.reporter('log'))
  );
  done();
}

// PHP Code Beautifier.
function phpcbf(done) {
  return (
    gulp.src(phpSrc)
        .pipe(gphpcbf({
            bin: 'vendor/bin/phpcbf',
            standard: './phpcs.ruleset.xml',
            warningSeverity: 0
        }))       
        .on('error', gutil.log)
        .pipe(gulp.dest('./'))
  );
  done();
}

/**/

// Watch files
function watchFiles() {
  gulp.watch('./sass/**/*', sass);
  gulp.watch('./js/**/*.js', js);
}

// gulp zip
function zip(done) {
  return (
    gulp.src(buildInclude)
        .pipe(gzip('strava-leaderboard.zip'))
        .pipe(gulp.dest('./../'))
  );
  done();
}

// define complex tasks
const styles = gulp.series(sass, mincss); // Styles task
const js = gulp.series(scripts); // compile and minimize js
const build = gulp.series(styles, scripts, zip); // Package Distributable
const watch = gulp.parallel(styles, scripts, watchFiles); // Watch Task

// export tasks
exports.sass = sass;
exports.mincss = mincss;
exports.lintcss = lintcss;
exports.beautifycss = beautifycss;
exports.styles = styles;
exports.js = js;
exports.lintjs = lintjs;
exports.beautifyjs = beautifyjs;
exports.phpcs = phpcs;
exports.phpcbf = phpcbf;
exports.zip = zip;
exports.build = build;
exports.watch = watch;