<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AMFDev\AMFReview\Model\ResourceModel\Review;

/**
 * Review collection resource model
 *
 * @api
 * @since 100.0.2
 */
class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Review table
     *
     * @var string
     */
    protected $_reviewTable = null;

    /**
     * Review detail table
     *
     * @var string
     */
    protected $_reviewDetailTable = null;

    /**
     * Review status table
     *
     * @var string
     */
    protected $_reviewStatusTable = null;

    /**
     * Review entity table
     *
     * @var string
     */
    protected $_reviewEntityTable = null;

    /**
     * Review store table
     *
     * @var string
     */
    protected $_reviewStoreTable = null;

    /**
     * Add store data flag
     * @var bool
     */
    protected $_addStoreDataFlag = false;

    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Rating option model
     *
     * @var \Magento\Review\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    protected $helperData;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \AMFDev\AMFReview\Helper\Data $helperData
    ) {
        $this->_reviewData = $reviewData;
        $this->_voteFactory = $voteFactory;
        $this->_storeManager = $storeManager;
        $this->helperData = $helperData;

        parent::__construct(
                            $entityFactory,
                            $logger,
                            $fetchStrategy,
                            $eventManager,
                            $reviewData,
                            $voteFactory,
                            $storeManager,
                            $connection,
                            $resource
                            );
    }

   /**
     * Add rate votes - overriding core to remove per-store check
     *
     * @return $this
     */
    public function addRateVotes()
    {
        
        //AMFDev: if enabled set store ID to default to show all ratings from all stores - had to customise the collection
        if($this->helperData->getGeneralConfig('show_reviews_from_all_stores')) {
            $storeId = 0;
        } else {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        
        foreach ($this->getItems() as $item) {
            $votesCollection = $this->_voteFactory->create()->getResourceCollection()->setReviewFilter(
                $item->getId()
            )->setStoreFilter(
                $storeId
            )->addRatingInfo(
                $storeId
            )->load();
            $item->setRatingVotes($votesCollection);
        }

        return $this;
        }

    
}
