<?php
$pathComposerSetup = 'setup/composer-setup.php';
if (is_file($pathComposerSetup)) {
    unlink($pathComposerSetup);
}

if (!copy('https://getcomposer.org/installer', $pathComposerSetup)) {
    echo 'Not able to download composer';
    return;
}

if (hash_file('SHA384', $pathComposerSetup) === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') {
    include($pathComposerSetup);
    process();

}
else {
    echo 'Installer corrupt';
}
unlink($pathComposerSetup);
