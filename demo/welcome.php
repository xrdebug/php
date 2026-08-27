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

require_once 'autoload.php';

xrr('👋 Welcome to xrDebug!');
xri()->pause();
xr(
    🤓: 'xrDebug is a lightweight debugger.',
    t: 'hello-world',
    e: '🐘'
);
sleep(5);
xr(
    👆: 'Edit session title by clicking on "xrDebug" up there.',
    t: 'how-to',
    e: '😜'
);
sleep(5);
xr(
    😉: 'Use keyboard keys to Resume (R), pause (P), stop (S) and clear (C) the debug session.',
    t: 'how-to',
    e: '🕹️'
);
sleep(5);
xr(
    ✅: 'Clicking a topic (how-to button) or emote (👻 emoji) will apply filtering.',
    🤤: 'Filters will appear on bottom, select to toggle.',
    t: 'how-to',
    e: '👻'
);
sleep(5);
xr(
    👈: 'Delete, copy and export with these buttons.',
    t: 'how-to',
    e: '😯'
);
sleep(5);
xr(
    👇: 'Open caller file path by clicking on ' . basename(__FILE__) . ':' . (string) (__LINE__ - 1) . ' here below.',
    t: 'how-to',
    e: '📎'
);
sleep(5);
xr(
    💅: 'Dark/light mode follows your system preferences.',
    t: 'how-to',
    e: '🌚🌝'
);
sleep(5);
xrr(
    '🎉 Enjoy <b>xrDebug</b>',
    e: '😊'
);
