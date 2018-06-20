"use strict";

const composer = require('gulp-composer');
const gulp = require('gulp');
const notify = require('gulp-notify');
const path = require('path');
const phpcs = require('gulp-phpcs');
const phpunit = require('gulp-phpunit');
const Q = require('q');
const spawnSync = require('child_process').spawnSync;
const through = require('through2');

const PHP_DIRS = ['src/', 'test/Tests/'];
const PHP_FILES = ['src/**/*.php', 'test/Tests/**/*.php'];

const watchConfig = {interval: 1000, usePolling: true};

// Composer install
gulp.task("composer", function () {
    return composer("install", {
        "bin": "php composer.phar",
        "working-dir": ".",
        "self-install": false
    });
});

function runPHPUnit(options) {
    let bin = path.resolve('vendor', 'bin', 'phpunit');
    let defaults = {
        debug: false,
        notify: true,
        statusLine: true,
        configurationFile: 'phpunit.xml',
        noCoverage: false
    };

    let _options = Object.assign({}, defaults, options);

    return gulp.src('phpunit.xml')
        .pipe(phpunit(bin, _options))
        .on('error', notify.onError({
            title: 'PHPUnit tests failed',
            message: 'PHPUnit tests failed'
        }));
}

// PHPUnit unit tests
gulp.task('phpunit', () => runPHPUnit({noCoverage: true}));
gulp.task('phpunit-coverage', () => runPHPUnit({}));

// Validate files using PHP Code Sniffer
gulp.task('phpcs', function () {
    return gulp.src(PHP_FILES)
        .pipe(phpcs({
            bin: 'vendor/bin/phpcs',
            standard: ['PSR1', 'PSR2', 'PEAR'],
            warningSeverity: 0,
            report: 'full'
        }))
        .pipe(phpcs.reporter('log'))
        .pipe(phpcs.reporter('fail', {failOnFirst: false}));
});

// Validate files using PHPstan
gulp.task('phpstan', function () {
    let phpstan = path.resolve('vendor', 'phpstan', 'phpstan', 'bin', 'phpstan');
    let args = [phpstan, 'analyze', '--no-progress', '--level=max'];

    let fileList = [];
    let deferred = Q.defer();

    gulp.src(PHP_DIRS)
        .pipe(through.obj(function (file, encoding, cb) {
            fileList.push(file.path);
            cb(null);
        }))
        .pipe(gulp.dest('./temp/'))
        .on('end', function () {
            Array.prototype.push.apply(args, fileList);

            let result = spawnSync('php', args, {cwd: '.', stdio: ['pipe', process.stdout, process.stderr]});
            if (result.status === 0) {
                deferred.resolve('PHPStan success');
            } else {
                deferred.reject(new Error('PHPStan failed: ' + result.status));
            }
        });

    return deferred.promise;
});

gulp.task('lintphp', gulp.parallel('phpcs', 'phpstan') );
gulp.task('watchlint', () => gulp.watch(PHP_FILES, watchConfig, gulp.series('lintphp')));
gulp.task('watchunit', () => gulp.watch(PHP_FILES, watchConfig, gulp.series('phpunit')));
gulp.task('watchcoverage', () => gulp.watch(PHP_FILES, watchConfig, gulp.series('phpunit-coverage')));
gulp.task('clean', (callback) => callback());
gulp.task('build', gulp.series('clean', 'lintphp'));
gulp.task('default', gulp.series('build'));
