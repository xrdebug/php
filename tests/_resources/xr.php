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

return [
    'isEnabled' => false,
    'isHttps' => false,
    'host' => 'test',
    'port' => 1234,
    'key' => <<<ED25519
    -----BEGIN PRIVATE KEY-----
    MC4CAQAwBQYDK2VwBCIEIFeJCk3D4oi5vuIo+nuGR0dcu7itYM7n5G0FbBs/ZDhd
    -----END PRIVATE KEY-----
    ED25519,
];
