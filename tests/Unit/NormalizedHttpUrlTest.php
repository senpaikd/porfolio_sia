<?php

namespace Tests\Unit;

use App\Casts\NormalizedHttpUrl;
use App\Models\Project;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizedHttpUrlTest extends TestCase
{
    public function test_blank_urls_normalize_to_null(): void
    {
        $cast = new NormalizedHttpUrl;

        $this->assertNull($cast->set(new Project, 'url', '   ', []));
    }

    public function test_http_urls_are_normalized(): void
    {
        $cast = new NormalizedHttpUrl;

        $this->assertSame('https://example.com/Path?q=1', $cast->set(new Project, 'url', ' HTTPS://EXAMPLE.COM/Path?q=1 ', []));
    }

    #[DataProvider('invalidUrls')]
    public function test_unsafe_urls_are_rejected(string $url): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new NormalizedHttpUrl)->set(new Project, 'url', $url, []);
    }

    public static function invalidUrls(): array
    {
        return [
            ['javascript:alert(1)'],
            ['data:text/html,test'],
            ['ftp://example.com/file'],
            ['https://user:pass@example.com'],
            ['not a url'],
        ];
    }
}
