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

use function Chevere\Components\Message\message;

require __DIR__ . '/../vendor/autoload.php';

xr(
    greet: '🇨🇱 Hola, mundo!',
    t: 'hello-world',
    f: '😊',
);
sleep(2);
xr(
    👉: [
        1 => 'chevere/xr is a debugger which',
        2 => 'runs a PHP message server!'
    ],
    t: 'hello-world',
    f: '🐘'
);
sleep(4);
$message = message('Did you heard about %package%?')
    ->code('%package%', 'spatie/ray');
xr(
    inspiration: $message,
    message: $message->toString(),
    t: 'hello-world',
    f: '😎',
    b: true
);
sleep(4);
xr(
    ✨: new class() {
        public array $ohhh = [
            'XR is another take on the server debug concept',
            'built on top of 👏 ReactPHP.'
        ];
    },
    t: 'hello-world',
    f: '✨🐘'
);
sleep(4);
xr(
    feat: 'Edit the title by clicking on "XR Session".',
    t: 'how-to',
    f: '✍️'
);
sleep(4);
xr(
    feat: 'Filter by clicking a topic (how-to button) or flair (👻 emoji).',
    t: 'how-to',
    f: '👻'
);
sleep(4);
xr(
    feat: 'Copy the file path by clicking on ' . basename(__FILE__) . ':' . (string) (__LINE__ + 2) . ' here below.',
    t: 'how-to',
    f: '📎'
);
sleep(4);
xr(
    feat: 'Dark/light mode follows your system preferences.',
    t: 'how-to',
    f: '🌚🌝'
);
sleep(4);
xr(
    bye: 'Enjoy XR!',
    f: '😊'
);
