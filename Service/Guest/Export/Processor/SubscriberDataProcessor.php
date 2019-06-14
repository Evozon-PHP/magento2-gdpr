<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service\Guest\Export\Processor;

use Magento\Sales\Api\Data\OrderInterface;
use Opengento\Gdpr\Model\Entity\DataCollectorInterface;
use Opengento\Gdpr\Model\Newsletter\SubscriberFactory;

/**
 * Class SubscriberDataProcessor
 */
final class SubscriberDataProcessor extends AbstractDataProcessor
{
    /**
     * @var \Opengento\Gdpr\Model\Newsletter\SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param \Opengento\Gdpr\Model\Newsletter\SubscriberFactory $subscriberFactory
     * @param \Opengento\Gdpr\Model\Entity\DataCollectorInterface $dataCollector
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        DataCollectorInterface $dataCollector
    ) {
        $this->subscriberFactory = $subscriberFactory;
        parent::__construct($dataCollector);
    }

    /**
     * @inheritdoc
     */
    public function execute(OrderInterface $order, array $data): array
    {
        /** @var \Opengento\Gdpr\Model\Newsletter\Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($order->getCustomerEmail());
        $data['subscriber'] = $this->collectData($subscriber);

        return $data;
    }
}
