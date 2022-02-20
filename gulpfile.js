let gulp = require("gulp");
let htmlmin = require("gulp-htmlmin");

let sass = require("gulp-sass");
let sassOptions = {
  outputStyle: "compressed",
  includePaths: "node_modules/bootstrap-sass/assets/stylesheets/",
  outFile: 'style.min.css'
};

let gulpCopy = require("gulp-copy");
let sourceFiles = [
  "*.js"
];

const imagemin = require('gulp-imagemin');
let rename = require('gulp-rename');

gulp.task('images', () =>
	 gulp.src('assets/**/*')
		.pipe(imagemin())
		.pipe(gulp.dest('dist/assets'))
);

gulp.task("minify-html", function() {
  return gulp
    .src(["./*.htm", "./sobre-mim/*.htm"])
    .pipe(htmlmin({ collapseWhitespace: true }))
    .pipe(gulp.dest("dist"));
});

gulp.task("copy-files", () => {
  return gulp.src(sourceFiles).pipe(gulpCopy("dist"));
});

gulp.task("sass", function() {
  return gulp
    .src("./assets/scss/main.scss")
    .pipe(sass(sassOptions).on("error", sass.logError))
    .pipe(rename('style.min.css'))
    .pipe(gulp.dest("./dist/assets/"));
});

gulp.task("build", [], function() {
  gulp.run("minify-html", "sass",'images', "copy-files");
});

gulp.task("watch", function() {
  gulp.watch("assets/**/*.scss", ['build']);
  gulp.watch("*.htm", ['build']);
  gulp.watch("*.js", ['build']);
});
