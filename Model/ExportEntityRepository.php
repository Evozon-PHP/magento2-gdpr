<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Model;

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Opengento\Gdpr\Api\Data\ExportEntityInterface;
use Opengento\Gdpr\Api\Data\ExportEntityInterfaceFactory;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterface;
use Opengento\Gdpr\Api\Data\ExportEntitySearchResultsInterfaceFactory;
use Opengento\Gdpr\Api\ExportEntityRepositoryInterface;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity as ExportEntityResource;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity\Collection;
use Opengento\Gdpr\Model\ResourceModel\ExportEntity\CollectionFactory;

final class ExportEntityRepository implements ExportEntityRepositoryInterface
{
    /**
     * @var ExportEntityResource
     */
    private $exportEntityResource;

    /**
     * @var ExportEntityInterfaceFactory
     */
    private $exportEntityFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExportEntitySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var ExportEntityInterface[]
     */
    private $instances = [];

    /**
     * @var ExportEntityInterface[]
     */
    private $instancesByEntity = [];

    public function __construct(
        ExportEntityResource $exportEntityResource,
        ExportEntityInterfaceFactory $exportEntityFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ExportEntitySearchResultsInterfaceFactory $searchResultsFactory,
        Filesystem $filesystem
    ) {
        $this->exportEntityResource = $exportEntityResource;
        $this->exportEntityFactory = $exportEntityFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->fileSystem = $filesystem;
    }

    public function save(ExportEntityInterface $entity): ExportEntityInterface
    {
        try {
            $this->exportEntityResource->save($entity);
            $this->register($entity);
        } catch (Exception $e) {
            throw new CouldNotSaveException(new Phrase('Could not save the entity.'), $e);
        }

        return $entity;
    }

    public function getById(int $entityId, bool $forceReload = false): ExportEntityInterface
    {
        if ($forceReload || !isset($this->instances[$entityId])) {
            /** @var ExportEntityInterface $entity */
            $entity = $this->exportEntityFactory->create();
            $this->exportEntityResource->load($entity, $entityId, ExportEntityInterface::ID);

            if (!$entity->getExportId()) {
                throw NoSuchEntityException::singleField(ExportEntityInterface::ID, $entityId);
            }

            $this->register($entity);
        }

        return $this->instances[$entityId];
    }

    public function getByEntity(int $entityId, string $entityType, bool $forceReload = false): ExportEntityInterface
    {
        if ($forceReload || !isset($this->instancesByEntity[$entityType . '_' . $entityId])) {
            /** @var ExportEntityInterface $entity */
            $entity = $this->exportEntityFactory->create();
            $this->exportEntityResource->load(
                $entity,
                [$entityId, $entityType],
                [ExportEntityInterface::ENTITY_ID, ExportEntityInterface::ENTITY_TYPE]
            );

            if (!$entity->getExportId()) {
                throw NoSuchEntityException::singleField(ExportEntityInterface::ID, $entityId);
            }

            $this->register($entity);
        }

        return $this->instancesByEntity[$entityType . '_' . $entityId];
    }

    public function getList(SearchCriteriaInterface $searchCriteria): ExportEntitySearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ExportEntitySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(ExportEntityInterface $entity): bool
    {
        try {
            $this->fileSystem->getDirectoryWrite(DirectoryList::TMP)->delete($entity->getFilePath());
            $this->remove($entity);
            $this->exportEntityResource->delete($entity);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                new Phrase('Could not delete entity with id "%1".', [$entity->getExportId()]),
                $e
            );
        }

        return true;
    }

    private function register(ExportEntityInterface $entity): void
    {
        $this->instances[$entity->getExportId()] = $entity;
        $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()] = $entity;
    }

    private function remove(ExportEntityInterface $entity): void
    {
        unset(
            $this->instances[$entity->getExportId()],
            $this->instancesByEntity[$entity->getEntityType() . '_' . $entity->getEntityId()]
        );
    }
}
