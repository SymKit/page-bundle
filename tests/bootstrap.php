<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

// Load PHPStan stubs for optional Symkit packages so unit tests can run without them.
if (!interface_exists('Symkit\MetadataBundle\Contract\PageContextBuilderInterface', false)) {
    require_once __DIR__.'/../.phpstan-stubs/symkit-stubs.php';
}
