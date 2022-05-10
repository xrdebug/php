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

namespace Chevere\Xr;

use Chevere\Filesystem\File;
use Chevere\Filesystem\Interfaces\DirInterface;

final class Build
{
    private string $html;

    public function __construct(
        private DirInterface $dir,
        private string $version,
        private string $codename,
    ) {
        $dir->assertExists();
        $file = new File($dir->path()->getChild('index.html'));
        $this->html = $file->getContents();
        $this->replace('%version%', $this->version);
        $this->replace('%codename%', $this->codename);
        $this->replaceIcons('svg', 'image/svg+xml');
        $this->replaceIcons('png', 'image/png');
        $this->replaceStyles();
        $this->replaceFont('fonts/firacode/firacode-regular.woff', 'font/woff');
        $this->replaceScripts();
    }

    public function html(): string
    {
        return $this->html;
    }

    public function replaceStyles(): void
    {
        preg_match_all(
            '#<link rel="stylesheet".*(href=\"(.*)\")>#',
            $this->html,
            $files
        );
        foreach ($files[0] as $pos => $match) {
            $fileMatch = new File($this->dir->path()->getChild($files[2][$pos]));
            $replace = '<style media="all">' . $fileMatch->getContents() . '</style>';
            $this->replace($match, $replace);
        }
    }

    public function replaceScripts(): void
    {
        preg_match_all("#<script .*(src=\"(.*)\")><\/script>#", $this->html, $files);
        foreach ($files[0] as $pos => $match) {
            $fileMatch = new File($this->dir->path()->getChild($files[2][$pos]));
            $replace = str_replace(' ' . $files[1][$pos], '', $match);
            $replace = str_replace(
                "></script>",
                '>'
                    . $fileMatch->getContents()
                    . "</script>",
                $replace
            );
            $this->replace($match, $replace);
        }
    }

    public function replaceIcons(string $extension, string $mime): void
    {
        preg_match_all(
            '#="(icon\.' . $extension . ')"#',
            $this->html,
            $files
        );
        foreach ($files[0] as $pos => $match) {
            $fileMatch = new File($this->dir->path()->getChild($files[1][$pos]));
            $replace = '="data:' . $mime . ';base64,'
                . base64_encode($fileMatch->getContents())
                . '"';
            $this->replace($match, $replace);
        }
    }

    public function replaceFont(string $font, string $mime): void
    {
        $fileMatch = new File($this->dir->path()->getChild($font));
        $replace = 'url(data:' . $mime . ';base64,'
                . base64_encode($fileMatch->getContents())
                . ')';
        $this->replace(
            "url('$font')",
            $replace
        );
    }

    private function replace(string $search, string $replace): void
    {
        $this->html = str_replace($search, $replace, $this->html);
    }
}
