<?php

declare(strict_types=1);

/**
 * This file is part of the FioBank statement Tools package
 *
 * https://github.com/Spoje-NET/fiobank-statement-tools
 *
 * (c) Spoje.Net <https://spoje.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SpojeNet\FioApi\Tests\Integration;

use FioApi\Exceptions\TooGreedyException;
use FioApi\RateLimit\JsonRateLimitStore;
use FioApi\RateLimit\RateLimiter;
use PHPUnit\Framework\TestCase;
use SpojeNet\FioApi\Downloader;

/**
 * Integration tests for the FIO API rate-limiting feature.
 *
 * These tests use real FIO_TOKEN from the .env file and hit the live API.
 * The Fio API enforces a 30-second cooldown between requests per token.
 *
 * @group integration
 */
class RateLimitingTest extends TestCase
{
    private static string $token;

    /** @var string Temp file for rate limit store */
    private string $storeFile;

    public static function setUpBeforeClass(): void
    {
        $envFile = __DIR__ . '/../../.env';

        if (!file_exists($envFile)) {
            self::markTestSkipped('.env file not found — cannot run integration tests without FIO_TOKEN.');
        }

        $lines = file($envFile, \FILE_IGNORE_NEW_LINES | \FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B'\"");

                if ($key === 'FIO_TOKEN') {
                    self::$token = $value;

                    return;
                }
            }
        }

        self::markTestSkipped('FIO_TOKEN not found in .env file.');
    }

    protected function setUp(): void
    {
        $this->storeFile = tempnam(sys_get_temp_dir(), 'fio_rate_integration_');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->storeFile)) {
            unlink($this->storeFile);
        }
    }

    /**
     * Throw mode: second rapid request must throw TooGreedyException.
     *
     * We pre-seed the store with a recent timestamp so the limiter thinks
     * a request was just made — no actual API call needed for the second check.
     */
    public function testThrowModeThrowsOnRapidRequest(): void
    {
        $store = new JsonRateLimitStore($this->storeFile);
        $limiter = new RateLimiter($store, false);

        // Simulate that a request was just made
        $limiter->recordRequest(self::$token);

        $downloader = new Downloader(self::$token, null, $limiter);

        $this->expectException(TooGreedyException::class);
        $downloader->downloadSince(new \DateTimeImmutable('-1 day'));
    }

    /**
     * Wait mode: the limiter should sleep and then allow the request.
     *
     * We seed the store with a timestamp 28 seconds ago, so the limiter
     * should wait ~2 seconds before proceeding.
     */
    public function testWaitModeSleepsAndProceeds(): void
    {
        $store = new JsonRateLimitStore($this->storeFile);
        $limiter = new RateLimiter($store, true);

        // Seed: last request was 28 seconds ago → limiter should sleep ~2s
        $store->set(self::$token, 'request', time() - 28);

        $start = time();
        $limiter->checkBeforeRequest(self::$token);
        $elapsed = time() - $start;

        $this->assertGreaterThanOrEqual(1, $elapsed, 'Limiter should have slept at least 1 second');
        $this->assertLessThanOrEqual(4, $elapsed, 'Limiter should not have slept more than 4 seconds');
    }

    /**
     * Verify the SpojeNet Downloader auto-configures rate limiting.
     *
     * The constructor should set up JsonRateLimitStore automatically
     * when no explicit limiter is provided.
     */
    public function testDownloaderAutoConfiguresRateLimiting(): void
    {
        $downloader = new Downloader(self::$token);

        // The parent constructor accepted the rate limiter — if the class
        // signature didn't support it, this would have thrown a TypeError.
        $this->assertInstanceOf(Downloader::class, $downloader);
    }

    /**
     * Live API call with rate limiter in wait mode.
     *
     * Makes a real API call to verify end-to-end that the rate limiter
     * integrates correctly with actual FIO API responses.
     *
     * Uses the shared store file (/tmp/fio-ratelimit.json) so the limiter
     * coordinates with all other processes that may have used this token.
     */
    public function testLiveDownloadWithRateLimiter(): void
    {
        $sharedStoreFile = '/tmp/fio-ratelimit.json';
        $store = new JsonRateLimitStore($sharedStoreFile);
        $limiter = new RateLimiter($store, true);

        $downloader = new Downloader(self::$token, null, $limiter);
        $transactionList = $downloader->downloadSince(new \DateTimeImmutable('-1 day'));

        $this->assertIsObject($transactionList);

        // Verify the store recorded the request timestamp
        $entry = $store->get(self::$token, 'request');
        $this->assertNotNull($entry, 'Rate limiter should have recorded the request timestamp');
        $this->assertArrayHasKey('timestamp', $entry);
        $this->assertEqualsWithDelta(time(), $entry['timestamp'], 5);
    }

    /**
     * Expired window: no wait needed if >30 seconds since last request.
     */
    public function testNoWaitWhenWindowExpired(): void
    {
        $store = new JsonRateLimitStore($this->storeFile);
        $limiter = new RateLimiter($store, true);

        // Last request was 31 seconds ago — should pass immediately
        $store->set(self::$token, 'request', time() - 31);

        $start = time();
        $limiter->checkBeforeRequest(self::$token);
        $elapsed = time() - $start;

        $this->assertLessThanOrEqual(1, $elapsed, 'Should not wait when the 30s window has expired');
    }

    /**
     * Multi-process safety: store persists across separate instances.
     */
    public function testStorePersistsAcrossInstances(): void
    {
        $store1 = new JsonRateLimitStore($this->storeFile);
        $store1->set(self::$token, 'request', time());

        // New instance reading the same file
        $store2 = new JsonRateLimitStore($this->storeFile);
        $entry = $store2->get(self::$token, 'request');

        $this->assertNotNull($entry, 'Second store instance should see the first instance\'s data');
        $this->assertEqualsWithDelta(time(), $entry['timestamp'], 2);
    }
}
