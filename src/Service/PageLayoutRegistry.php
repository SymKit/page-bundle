<?php

declare(strict_types=1);

namespace Symkit\PageBundle\Service;

use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PageLayoutRegistry
{
    /**
     * @param array<string, array{label: string, path: string}> $layouts
     */
    public function __construct(
        private array $layouts,
        private TranslatorInterface $translator,
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
            $label = $this->translator->trans($config['label'], [], 'SymkitPageBundle');
            $choices[$label] = $name;
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
