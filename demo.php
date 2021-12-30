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

require __DIR__ . '/vendor/autoload.php';

xr(['Hola, mundo!', new stdClass()]);
sleep(1);
xr(f: '🤔');
xr(t: 'Win');
sleep(1);
xr(
    getrusage(),
    f: '😎',
    t: 'Epic win!'
);
sleep(1);
xr($_SERVER);
