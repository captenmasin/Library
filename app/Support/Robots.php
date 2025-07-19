<?php

namespace App\Support;

class Robots
{
    protected array $lines = [];

    protected static bool $shouldIndex = true;

    public function generate(): string
    {
        return implode(PHP_EOL, $this->lines);
    }

    public function addSitemap($sitemap): void
    {
        $this->addLine("Sitemap: $sitemap");
    }

    public function addUserAgent($userAgent): void
    {
        $this->addLine("User-agent: $userAgent");
    }

    public function addHost($host): void
    {
        $this->addLine("Host: $host");
    }

    public function addDisallow($directories): void
    {
        $this->addRuleLine($directories, 'Disallow');
    }

    public function addAllow($directories): void
    {
        $this->addRuleLine($directories, 'Allow');
    }

    public function addRuleLine($directories, string $rule): void
    {
        foreach ((array) $directories as $directory) {
            $this->addLine("$rule: $directory");
        }
    }

    public function addComment($comment): void
    {
        $this->addLine("# $comment");
    }

    public function addSpacer(): void
    {
        $this->addLine('');
    }

    public function addLine(string $line): void
    {
        $this->lines[] = $line;
    }

    protected function addLines($lines): void
    {
        foreach ((array) $lines as $line) {
            $this->addLine($line);
        }
    }

    public function reset(): void
    {
        $this->lines = [];
    }

    public function setShouldIndexCallback(callable $callback): void
    {
        self::$shouldIndex = $callback;
    }

    public function shouldIndex(): bool
    {
        return config('robots.enabled');
    }

    public function metaTag(): string
    {
        return '<meta name="robots" content="'.($this->shouldIndex() ? 'index, follow' : 'noindex, nofollow').'">';
    }
}
