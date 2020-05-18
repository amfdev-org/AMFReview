<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AMFDev\AMFReview\Block\Product;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Product Review Tab
 *
 * @api
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Review extends \Magento\Review\Block\Product\Review
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct(
                            $context,
                            $registry,
                            $collectionFactory,
                            $data
                            );

        $this->setTabTitle();
    }


    /**
     * Get size of reviews collection - overriding to remove per-store check
     *
     * @return int
     */
    public function getCollectionSize()
    {
        $collection = $this->_reviewsColFactory->create()->addStoreFilter(
            0  //AMFDev: changed from $this->_storeManager->getStore()->getId()
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProductId()
        );

        return $collection->getSize();
    }

}
