# XrDebug

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

<p align="center"><img alt="XR" src="xr.svg" width="40%"></p>

https://user-images.githubusercontent.com/20590102/153045551-619c74cc-c4ae-41da-b7b6-bd8733e623a2.mp4

🦄 [View demo](https://user-images.githubusercontent.com/20590102/153045551-619c74cc-c4ae-41da-b7b6-bd8733e623a2.mp4)

[![Build](https://img.shields.io/github/actions/workflow/status/chevere/xr/test.yml?branch=0.7&style=flat-square)](https://github.com/chevere/xr/actions)
![Code size](https://img.shields.io/github/languages/code-size/chevere/xr?style=flat-square)
[![Apache-2.0](https://img.shields.io/github/license/chevere/xr?style=flat-square)](LICENSE)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%209-blueviolet?style=flat-square)](https://phpstan.org/)

[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=alert_status)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![Security Rating](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=security_rating)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=coverage)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![Technical Debt](https://sonarcloud.io/api/project_badges/measure?project=chevere_xr&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chevere_xr)
[![CodeFactor](https://www.codefactor.io/repository/github/chevere/xr/badge)](https://www.codefactor.io/repository/github/chevere/xr)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/89c64d17be684818b21d44c658c735d0)](https://www.codacy.com/gh/chevere/xr/dashboard)

[XrDebug](https://xr-docs.chevere.org/) is a dump debug utility for PHP. No extras required, **debug** your PHP code **anywhere**. It uses a ReactPHP SSE server to provide a web-based debug application.

## Quick start

* Install using [Composer](https://getcomposer.org/)

```sh
composer require --dev chevere/xr
```

* Run the XrDebug [server](https://xr-docs.chevere.org/server/)

```sh
docker run -t --init --rm -p 27420:27420 ghcr.io/chevere/xr-server
```

<p align="center">
    <img alt="XR light" src=".screen/xr-0.1.3-light-welcome.png">
    <img alt="XR light" src=".screen/xr-0.1.3-dark-welcome.png">
</p>

## Documentation

👉 [xr-docs.chevere.org](https://xr-docs.chevere.org)

## Features

* 🔏 Signed requests (Ed25519)
* 💎 End-to-end encryption (AES-GCM AE)
* ✨ Dump n arguments with [VarDump](https://chevere.org/packages/var-dump.html) driven variable highlight
* 🐘 One-click PHP server run (no extras required)
* 👻 Filter messages by [Topics](https://xr-docs.chevere.org/helpers/xr.html#topic) and [Emotes](https://xr-docs.chevere.org/helpers/xr.html#emote)
* ✍️ Re-name "XrDebug" to anything you want
* 🏁 Resume, Pause, Stop and Clear debug window controls
* 🥷 Keyboard shortcuts (Resume **R**, Pause **P**, Stop **S** and Clear **C**)
* 😊 Export dump output to clipboard or as PNG image
* 📟 Generates dump [backtrace](https://xr-docs.chevere.org/helpers/xr.html#flags)
* ⏸ [Pause](https://xr-docs.chevere.org/helpers/xri.html#pause) and resume your code execution
* 🌚 Dark / 🌝 Light mode follows your system preferences
* 👽 Ephemeral, it doesn't store any persistent data
* 🍒 Portable & HTML based (save page, search, etc.)
* 🔥 Uses [FiraCode](https://github.com/tonsky/FiraCode) font for displaying _beautiful looking dumps_ ™
* 😅 Handle exceptions (hook or replace your existing handler)

<p align="center">
    <img alt="XR dark demo" src=".screen/xr-0.1.3-dark-demo.png">
</p>

## License

Copyright 2023 [Rodolfo Berrios A.](https://rodolfoberrios.com/)

XR is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
