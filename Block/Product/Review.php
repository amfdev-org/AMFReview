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
    
    protected $helperData;

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
        array $data = [],
        \AMFDev\AMFReview\Helper\Data $helperData
    ) {
        $this->_coreRegistry = $registry;
        $this->_reviewsColFactory = $collectionFactory;
        $this->helperData = $helperData;
         
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
        
        //AMFDev: if enabled set store ID to count up the number of reviews to show on the PDP Reviews tab
        if($this->helperData->getGeneralConfig('show_reviews_from_all_stores')) {
            $storeId = 0;
        } else {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        
        $collection = $this->_reviewsColFactory->create()->addStoreFilter(
            $storeId 
        )->addStatusFilter(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->addEntityFilter(
            'product',
            $this->getProductId()
        );

        return $collection->getSize();
    }

}
