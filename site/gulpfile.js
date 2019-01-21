// - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Plugins
// - - - - - - - - - - - - - - - - - - - - - - - - - - -
var gulp = require('gulp'),
    rename = require('gulp-rename'),
    filesize = require('gulp-filesize'),
    watch = require('gulp-watch'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    sass = require('gulp-ruby-sass'),
    csscomb = require('gulp-csscomb'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    babel = require('gulp-babel'),
    util = require('gulp-util'),
    prettyError = require('gulp-prettyerror');

var symdiff = require('gulp-symdiff'),
    html = require('symdiff-html'),
    css = require('symdiff-css');

// - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Paths
// - - - - - - - - - - - - - - - - - - - - - - - - - - -

var config =
{
    //
    // Paths to everything
    //
    paths:
    {
        output: 'web/assets/',
        frontend: 'src/XIVDB/Resources/frontend/',
        tooltips: 'src/XIVDB/Resources/tooltips/',
        manager: 'src/XIVDB/Resources/manager/',
        dashboard: 'src/XIVDB/Resources/dashboard/',
        libs: 'src/XIVDB/Resources/libs/',

        checktwig: 'src/XIVDB/Resources/Views/**/*.twig',
        checkcss: 'web/assets/*.css',
    },

    //
    // Tasks to watch
    //
    tasks:
    {
        //
        // Main frontend js and styles
        //
        frontend: {
            js: {
                watch: ['js/**/*.js'],
                output: 'frontend.min.js',
            },
            css: {
                watch: ['scss/**/*.scss'],
                output: 'frontend.min.css',
                src: 'scss/main.scss',
            }
        },

        //
        // Main frontend js and styles
        //
        dashboard: {
            js: {
                watch: ['js/**/*.js'],
                output: 'dashboard.min.js',
            },
            css: {
                watch: ['scss/**/*.scss'],
                output: 'dashboard.min.css',
                src: 'scss/main.scss',
            }
        },

        //
        // Tooltip js and styles
        //
        tooltips: {
            js: {
                watch: ['js/*.js'],
                output: 'tooltips.js',
            },
            css: {
                watch: ['scss/**/*.scss'],
                output: 'tooltips.css',
                src: 'scss/main.scss',
            }
        },

        //
        // Bundled libraries
        //
        libs: {
            js: {
                watch: ['js/*.js'],
                output: 'libs.min.js',
            }
        },

        //
        // Admin javascript
        //
        manager: {
            js: {
                watch: ['js/*.js'],
                output: 'manager.min.js',
            }
        }
    }
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Functions
// - - - - - - - - - - - - - - - - - - - - - - - - - - -

var compileStyles = function(input, output, options)
{
    var savePath = (options && typeof options.saveto !== 'undefined') ? options.saveto : config.paths.output;
    var file = sass(input);

    file.pipe(prettyError())
        .pipe(csscomb())
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(filesize())
        .pipe(minifycss())
        .pipe(rename(output))
        .pipe(filesize())
        .pipe(gulp.dest(savePath));


    return file;
}

var compileScripts = function(input, output, options)
{
    var savePath = (options && typeof options.saveto !== 'undefined') ? options.saveto : config.paths.output;
    var file = gulp.src(input).pipe(prettyError()).pipe(concat(output));

    // options passed to the compile
    if (options) {
        // if to babel
        if (typeof options.babel !== 'undefined') {
            file.pipe(babel({
                blacklist: ["useStrict"],
                compact: false,
            }));
        }

        if (typeof options.minify !== 'undefined') {
            file.pipe(uglify());
        }
    }

    file.pipe(gulp.dest(savePath));
    return file;
}

function swallowError (error) {
    // If you want details of the error in the console
    console.log(error.toString())
    this.emit('end');
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Tasks
// - - - - - - - - - - - - - - - - - - - - - - - - - - -

//
// Frontend
//
var frontendScripts = config.paths.frontend + config.tasks.frontend.js.watch,
    frontendScriptsOutput = config.tasks.frontend.js.output,
    frontendStyles = config.paths.frontend + config.tasks.frontend.css.watch;
    frontendStylesInput = config.paths.frontend + config.tasks.frontend.css.src;
    frontendStylesOutput = config.tasks.frontend.css.output,

gulp.task('frontend-styles', function() { return compileStyles(frontendStylesInput, frontendStylesOutput); });
gulp.task('frontend-scripts', function() { return compileScripts(frontendScripts, frontendScriptsOutput, { babel: true }); });

//
// Dashboard
//
var dashboardScripts = config.paths.dashboard + config.tasks.dashboard.js.watch,
    dashboardScriptsOutput = config.tasks.dashboard.js.output;
    dashboardStyles = config.paths.dashboard + config.tasks.dashboard.css.watch;
    dashboardStylesInput = config.paths.dashboard + config.tasks.dashboard.css.src;
    dashboardStylesOutput = config.tasks.dashboard.css.output,

gulp.task('dashboard-styles', function() { return compileStyles(dashboardStylesInput, dashboardStylesOutput); });
gulp.task('dashboard-scripts', function() { return compileScripts(dashboardScripts, dashboardScriptsOutput, { babel: true }); });

//
// Manager
//
var managerScripts = config.paths.manager + config.tasks.manager.js.watch,
    managerScriptsOutput = config.tasks.manager.js.output;

gulp.task('manager-scripts', function() { return compileScripts(managerScripts, managerScriptsOutput, { babel: true }); });


//
// Tooltips
//
var tooltipsScripts = config.paths.tooltips + config.tasks.tooltips.js.watch,
    tooltipsScriptsOutput = config.tasks.tooltips.js.output,
    tooltipsStyles = config.paths.tooltips + config.tasks.tooltips.css.watch;
    tooltipsStylesInput = config.paths.tooltips + config.tasks.tooltips.css.src;
    tooltipsStylesOutput = config.tasks.tooltips.css.output,

gulp.task('tooltips-styles', function() { return compileStyles(tooltipsStylesInput, tooltipsStylesOutput, { saveto: 'web/' }); });
gulp.task('tooltips-scripts', function() {
    compileScripts(tooltipsScripts, tooltipsScriptsOutput.replace('.js', '.min.js'), { saveto: 'web/', babel: true, minify: true });
    return compileScripts(tooltipsScripts, tooltipsScriptsOutput, { saveto: 'web/', babel: true });
});


//
// Libs
//
var libsScripts = config.paths.libs + config.tasks.libs.js.watch,
    libsScriptsOutput = config.tasks.libs.js.output;

gulp.task('libs-scripts', function() { return compileScripts(libsScripts, libsScriptsOutput); });

//
// Check unusued css classes
//
gulp.task('check-css-classes', function() {
    gulp.src([config.paths.checkcss, config.paths.checktwig])
        .pipe(symdiff({
            templates: [html],
            css: [css],
            ignore: [/^ignore/]
        })
        .on('error', function() {
            process.exit(1);
        }));
});

// - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Commands
// - - - - - - - - - - - - - - - - - - - - - - - - - - -

gulp.task('default', function() {
    gulp.watch(frontendStyles, ['frontend-styles']);
    gulp.watch(tooltipsStyles, ['tooltips-styles']);
    gulp.watch(frontendScripts, ['frontend-scripts']);
    gulp.watch(dashboardScripts, ['dashboard-scripts']);
    gulp.watch(tooltipsScripts, ['tooltips-scripts']);
    gulp.watch(dashboardStyles, ['dashboard-styles']);
    gulp.watch(libsScripts, ['libs-scripts']);

    gulp.start('dist');
});

gulp.task('dist', function() {
    compileStyles(frontendStylesInput, frontendStylesOutput);
    compileStyles(dashboardStylesInput, dashboardStylesOutput);
    compileScripts(frontendScripts, frontendScriptsOutput, { babel: true, minify: true });
    compileScripts(dashboardScripts, dashboardScriptsOutput, { babel: true, minify: true });
    compileStyles(tooltipsStylesInput, tooltipsStylesOutput, { saveto: 'web/' });
    compileScripts(tooltipsScripts, tooltipsScriptsOutput, { saveto: 'web/', babel: true, minify: true });
    compileScripts(libsScripts, libsScriptsOutput, { minify: true });
});
