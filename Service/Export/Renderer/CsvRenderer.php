<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Export\Renderer;

use Opengento\Gdpr\Service\Export\AbstractRenderer;
use Opengento\Gdpr\Service\Export\RendererInterface;
use Magento\Framework\Filesystem;

/**
 * Class CsvRenderer
 */
final class CsvRenderer extends AbstractRenderer implements RendererInterface
{
    /**
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        parent::__construct($filesystem, 'csv');
    }
    /**
     * @inheritdoc
     */
    public function render(array $data): string
    {
        $csv = '';
        foreach ($data as $key => $value) {
            $csv .= \is_array($value)
                ? $key . ',' . \rtrim($this->render($value), ',') . \PHP_EOL
                : '"' . \str_replace('"', '""', $value) . '",';
        }
        return $csv;
    }
}
