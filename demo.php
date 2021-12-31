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

require __DIR__ . '/vendor/autoload.php';

xr(greet: 'Hola, mundo!', t: 'hello-world', f: '😊');
sleep(2);
xr(
    explain: [
        1 => 'chevere/xr is a debugger which',
        2 => 'runs a message server!'
    ],
    t: 'hello-world',
    f: '🤓'
);
sleep(4);
xr(
    message('Did you heard about %inspiration%?')
        ->code('%inspiration%', 'spatie/ray'),
    t: 'hello-world',
    f: '🕶'
);
sleep(4);
xr(
    tagline: new class() {
        public array $ohhh = [
            'XR is another take on the spatie/ray concept...',
            '...But built on top of 👏ReactPHP.'
        ];
    },
    t: '5 ☆ Jumbitos',
    f: '🐘🐘🐘🐘🐘'
);
sleep(4);
xr(
    read: '^^^ This is how you use the XR debugger.',
    t: 'how-to',
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
    feat: 'Open a new window/tab to spawn a new XR Session.',
    t: 'how-to',
    f: '🆕'
);
sleep(4);
xr(
    bye: 'Hope you enjoy XR!',
    f: '😊'
);
