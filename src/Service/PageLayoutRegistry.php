<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Service;

use InvalidArgumentException;

final class PageLayoutRegistry
{
    /**
     * @param array<string, array{label: string, path: string}> $layouts
     */
    public function __construct(
        private readonly array $layouts,
    ) {
    }

    /**
     * @return array<string, array{label: string, path: string}>
     */
    public function getLayouts(): array
    {
        return $this->layouts;
    }

    /**
     * @return array<string, string>
     */
    public function getLayoutChoices(): array
    {
        $choices = [];
        foreach ($this->layouts as $name => $config) {
            $choices[$config['label']] = $name;
        }

        return $choices;
    }

    public function getLayoutPath(?string $name): string
    {
        $name = $name ?: 'simple';

        if (isset($this->layouts[$name])) {
            return $this->layouts[$name]['path'];
        }

        throw new InvalidArgumentException(\sprintf('Layout "%s" not found', $name));
    }
}
