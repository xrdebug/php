# xr

> 🔔 Subscribe to the [newsletter](https://chv.to/chevere-newsletter) to don't miss any update regarding Chevere.

<p align="center"><img alt="xrDebug" src="xr.svg" width="40%"></p>

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

`xr` is the PHP client library for [xrDebug](https://xr-docs.chevere.org/).

## Quick start

* Install using [Composer](https://getcomposer.org/)

```sh
composer require --dev chevere/xr
```

* Run the xrDebug [server](https://xr-docs.chevere.org/server/)

```sh
docker run -t --init --rm -p 27420:27420 ghcr.io/chevere/xrdebug
```

* Debug your code using [helpers](https://xr-docs.chevere.org/helpers/)

```php
xr($var, 'Hola, mundo!');
xri()->pause();
```

## Documentation

Documentation available at [xr-docs.chevere.org](https://xr-docs.chevere.org/).

## License

Copyright 2023 [Rodolfo Berrios A.](https://rodolfoberrios.com/)

xrDebug is licensed under the Apache License, Version 2.0. See [LICENSE](LICENSE) for the full license text.

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
