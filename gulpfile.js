var gulp = require( "gulp" );
var sass = require('gulp-sass')(require('sass'));
var prefixer = require( "gulp-autoprefixer" );
var pxtorem  = require( "gulp-pxtorem" );
var sourcemaps = require("gulp-sourcemaps");
var path = require('path');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var del = require('del');
var cssmin = require('gulp-cssmin');

gulp.task('page-sass', function () {
    return gulp.src(
        "assets/sass/page/*.scss"
    )
    .pipe( sass().on( "error", sass.logError ) )
    .pipe( prefixer( "last 2 versions" ) )
    .pipe(sourcemaps.write('./'))
    .pipe(cssmin())
    .pipe(rename(function (file) {
        let parentFolder = path.dirname(file.dirname)
        file.dirname = path.join(parentFolder, 'compiled');
    }))
    // .pipe( pxtorem( {
    //     rootValue:16,
    //     propList: ['font', 'font-size', 'line-height', 'letter-spacing','padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'width', 'height','border','border-radius', 'border-width', 'border-top', 'border-left', 'border-right', 'border-bottom', 'max-width', 'min-width', 'left', 'top', 'bottom', 'right'],
    // } ) )
    .pipe( gulp.dest( "assets/css" ) );
});

gulp.task( "watch", function(){
    gulp.watch( "assets/sass/page/*.scss", gulp.series(["page-sass" ]) );
});
