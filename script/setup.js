const exec = require('child_process').exec;
const fs = require('fs');
let php = false;

function createParam() {
    fs.createReadStream('config/param.yml.dist').pipe(fs.createWriteStream('config/param.yml'));
    console.log('param.yml created !');
    console.log('Done !');
}

function downloadComposer() {
    if (!php) {
        console.error('PHP not found !');
        return;
    }

    console.log('PHP found !');
    console.log('Download composer.phar...');
    exec('php script/downloadComposer.php', function (error, stdout) {
        if (!error) {
            fs.unlinkSync('script/composer-setup.php');
            installVendors();
            return;
        }
        console.error('Unable to download composer.phar');
    });
}

function installVendors() {
    if (!fs.existsSync('composer.phar')) {
        console.error('composer.phar not found');
        return;
    }

    console.log('Install vendors... \n');
    exec('php composer.phar install', function (error) {
        if (!error) {
            fs.unlinkSync('composer.phar');
            createParam();
        }
    });
}

// Install npm dependencies
console.log('npm install... \n');
exec('npm i', function (error, stdout, stderr) {
    console.log(stdout, stderr);
    if (!error) {
        exec('php --version',  function (error) {
            if (!error) {
                php = true;
            }
            downloadComposer();
        });
    }
})

