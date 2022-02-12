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

use function Chevere\Message\message;

foreach (['/', '/../../../'] as $path) {
    $autoload = __DIR__ . $path . 'vendor/autoload.php';
    if (stream_resolve_include_path($autoload)) {
        require $autoload;

        break;
    }
}

xri()->pause();
xrr('😘 Hola, mundo!');
sleep(2);
xr(
    👉: [
        1 => 'chevere/xr is a debugger which',
        2 => 'runs a PHP message server!'
    ],
    t: 'hello-world',
    e: '🐘'
);
sleep(4);
$message = message('Did you heard about %package%?')
    ->code('%package%', 'spatie/ray');
xr(
    inspiration: $message,
    t: 'hello-world',
    e: '😎',
    f: XR_BACKTRACE
);
sleep(4);
xr(
    ✨: new class() {
        public array $ohhh = [
            'XR' => 'is another take on the server debug concept',
            'built' => 'on top of ReactPHP.'
        ];
    },
    t: 'hello-world',
    e: '✨🐘',
);
sleep(4);
xr(
    feat: 'Edit the title by clicking on "XR Session".',
    t: 'how-to',
    e: '✍️'
);
sleep(4);
xr(
    feat: 'Filter by clicking a topic (how-to button) or emote (👻 emoji).',
    t: 'how-to',
    e: '👻'
);
sleep(4);
xr(
    feat: 'Copy the file path by clicking on ' . basename(__FILE__) . ':' . (string) (__LINE__ + 2) . ' here below.',
    t: 'how-to',
    e: '📎'
);
sleep(4);
xr(
    feat: 'Dark/light mode follows your system preferences.',
    t: 'how-to',
    e: '🌚🌝'
);
sleep(4);
xrr(
    'Enjoy <b>chevere/xr</b>',
    e: '😊'
);
