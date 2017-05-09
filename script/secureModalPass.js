let crypto;
try {
    crypto = require('crypto');
} catch (err) {
    console.error('crypto support is disabled !');
}

const readline = require('readline');
const yaml = require('node-yaml');
const fs = require('fs');
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});
const hash = crypto.createHash('sha256');

function updateParam(hashStr) {
    const rootPathParam = 'config/param.yml';
    const relativePathParam = '../config/param.yml';
    if (!fs.existsSync(rootPathParam)) {
        console.error('param.yml do not exist !\n');
        return;
    }

    let yamlData = yaml.readSync(relativePathParam);
    console.log('Updating param.yml... \n');
    yamlData.passModalHash = hashStr;
    yaml.writeSync(relativePathParam, yamlData);
    console.log('Done ! \n');
}

rl.question('Set modal password : ', (answer) => {
    hash.write(answer);
    console.log('\n');
    updateParam(hash.digest('hex'));
    rl.close();
});
