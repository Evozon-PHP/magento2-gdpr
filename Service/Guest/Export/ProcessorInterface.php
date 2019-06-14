<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Guest\Export;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface ProcessorInterface
 * @api
 */
interface ProcessorInterface
{
    /**
     * Execute the export processor for the guest order
     * It allows to retrieve the related data as an array.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param array $data
     * @return array
     */
    public function execute(OrderInterface $order, array $data): array;
}
