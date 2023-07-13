let gulp = require("gulp");
let sass = require('gulp-sass')(require('sass'));
let autoprefixer = require("gulp-autoprefixer");
let concat = require("gulp-concat");
let uglify = require('gulp-uglify-es').default;
let csso = require('gulp-csso');
let rename = require('gulp-rename');

var css = {
  src: 'components/**/*.scss',
  dest: 'assets/src/css',
  filename: 'custom.scss'
};

var js = {
  src: 'assets/js/src/**/*.js',
  dest: 'assets/js/dist',
  filename: 'script.js'
};

function style() {
   cssBundle()
  return (
    gulp
      .src(css.src)
      .pipe(concat(css.filename))
      .pipe(sass())
      .on("error", sass.logError)
      .pipe(gulp.dest(css.dest))
      .pipe(cssBundle())
  );
}

function script() {
  return (
    gulp
      .src(js.src)
      .pipe(concat(js.filename))
      .pipe(gulp.dest(js.dest))
      .pipe(uglify())
      .pipe(rename({extname: '.min.js'}))
      .pipe(gulp.dest(js.dest))
      .run(cssBundle)
  );
}

function watch(){
  gulp.watch(css.src, style);
  gulp.watch(js.src, script);
}

function cssBundle(){
   return (
      gulp
         .src([
            'assets/dist/css/styles.css',
            'assets/src/css/custom.css'
         ])
         .pipe(concat('htlf.css'))
         .pipe(csso())
         .pipe(gulp.dest('assets/dist/css'))
   )
}

exports.css = style;
exports.js = script;
exports.default = watch;
exports.cssBundle = cssBundle;