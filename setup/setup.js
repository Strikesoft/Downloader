const exec = require('child_process').exec;
const EventEmitter = require('events');
let php = false;

class Emitter extends EventEmitter {}
const myEmitter = new Emitter();

function createParam() {
    if (!php) {
        return;
    }

    exec('php -r "copy(\'config/param.yml.dist\', \'config/param.yml\');"', function (error) {
        if (error) {
            console.error('Unable to create param.yml');
            return;
        }
        console.log('param.yml created ! \n');
    })
}

exec('php --version',  function (error) {
   if (!error) {
       php = true;
   }
    createParam();
    myEmitter.emit('php-check');
});

myEmitter.once('php-check', function () {
    if (!php) {
        console.error('PHP not found !');
        return;
    }

    console.log('PHP found ! \n');
    console.log('Download composer.phar \n');
    exec('php setup/downloadComposer.php', function (error, stdout) {
        if (!error) {
            console.log(stdout + '\n');
            myEmitter.emit('install-vendor');
            return;
        }
        console.error('Unable to download composer.phar');
    });
});

myEmitter.once('install-vendor', function () {
    console.log('Install vendor... \n');
    exec('php composer.phar install', function (error) {
        if (!error) {
            console.log('Done !');
        }
    })
});
