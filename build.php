<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use function Chevere\Filesystem\dirForPath;
use function Chevere\Filesystem\fileForPath;
use Chevere\Xr\XrBuild;
use samejack\PHP\ArgvParser;

require __DIR__ . '/vendor/autoload.php';

$options = (new ArgvParser())->parseConfigs();
if (!isset($options['v'], $options['n'])) {
    echo "Provide version (-v) and name (-n)\n";
    die(255);
}
echo "👉 Building ";
echo strtr('[version %v] [codename %c]', [
    '%v' => $options['v'],
    '%c' => $options['n'],
]) . "\n";
$build = new XrBuild(
    dirForPath(__DIR__ . '/app/src'),
    $options['v'],
    $options['n']
);
$app = fileForPath(__DIR__ . '/app/build/en.html');
$app->removeIfExists();
$app->create();
$app->put($build->html());
echo "* Done!\n";
