<?php
/**
 * @link https://github.com/illuminatech
 * @copyright Copyright (c) 2019 Illuminatech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Illuminatech\UrlTrailingSlash\Testing;

use Illuminate\Support\Str;

/**
 * AllowsUrlTrailingSlash allows creation of URLs with trailing slash for the unit and feature tests.
 *
 * This trait should be used within test case class, which already use {@see \Illuminate\Foundation\Testing\Concerns\MakesHttpRequests} trait.
 * For example:
 *
 * ```php
 * namespace Tests;
 *
 * use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
 * use Illuminatech\UrlTrailingSlash\Testing\AllowsUrlTrailingSlash;
 *
 * abstract class TestCase extends BaseTestCase
 * {
 *     use CreatesApplication;
 *     use AllowsUrlTrailingSlash;
 * }
 * ```
 *
 * @mixin \Illuminate\Foundation\Testing\Concerns\MakesHttpRequests
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
trait AllowsUrlTrailingSlash
{
    /**
     * {@inheritdoc}
     */
    protected function prepareUrlForRequest($uri)
    {
        $result = parent::prepareUrlForRequest($uri);

        if (Str::endsWith($uri, '/')) {
            $result .= '/';
        }

        return $result;
    }
}
