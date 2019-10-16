<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Opengento\Gdpr\Api\Data\EraseEntityInterface;
use Opengento\Gdpr\Api\Data\EraseEntitySearchResultsInterface;

/**
 * @api
 */
interface EraseEntityRepositoryInterface
{
    /**
     * Save erase entity scheduler
     *
     * @param EraseEntityInterface $entity
     * @return EraseEntityInterface
     * @throws CouldNotSaveException
     */
    public function save(EraseEntityInterface $entity): EraseEntityInterface;

    /**
     * Retrieve erase entity scheduler by ID
     *
     * @param int $entityId
     * @return EraseEntityInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): EraseEntityInterface;

    /**
     * Retrieve erase entity scheduler by entity
     *
     * @param int $entityId
     * @param string $entityType
     * @return EraseEntityInterface
     * @throws NoSuchEntityException
     */
    public function getByEntity(int $entityId, string $entityType): EraseEntityInterface;

    /**
     * Retrieve erase entity schedulers list by search filter criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return EraseEntitySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): EraseEntitySearchResultsInterface;

    /**
     * Delete erase entity scheduler
     *
     * @param EraseEntityInterface $entity
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function delete(EraseEntityInterface $entity): bool;
}
