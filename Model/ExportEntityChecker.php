<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\ExportEntityCheckerInterface;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;

/**
 * Class ExportEntityChecker
 */
final class ExportEntityChecker implements ExportEntityCheckerInterface
{
    /**
     * @var \Opengento\Gdpr\Api\ExportEntityRepositoryInterface
     */
    private $exportEntityRepository;

    /**
     * @param \Opengento\Gdpr\Api\ExportEntityRepositoryInterface $exportEntityRepository
     */
    public function __construct(
        ExportEntityRepositoryInterface $exportEntityRepository
    ) {
        $this->exportEntityRepository = $exportEntityRepository;
    }

    /**
     * @inheritdoc
     */
    public function exists(int $entityId, string $entityType): bool
    {
        try {
            return (bool) $this->exportEntityRepository->getByEntity($entityId, $entityType)->getExportId();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function isExported(int $entityId, string $entityType): bool
    {
        try {
            $entity = $this->exportEntityRepository->getByEntity($entityId, $entityType);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $entity->getExportedAt() !== null && $entity->getFilePath() !== null;
    }
}