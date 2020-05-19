<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AMFDev\AMFReview\Block\Product\View;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * Detailed Product Reviews
 *
 * @api
 * @since 100.0.2
 */
class ListView extends \Magento\Review\Block\Product\View\ListView
{

        protected $helperData;
    
        //AMFDev: there is no constructor in \Magento\Review\Block\Product\View\ListView
        //so this is effectively grandparent::construct (on \Magento\Review\Block\Product\View)
        //in order to add our helper function
        
        public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = [],
        \AMFDev\AMFReview\Helper\Data $helperData
        
    ) {
        $this->helperData = $helperData;
        
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
    }
    
    
    /**METHOD IS FROM PARENT - \Magento\Review\Block\Product\View - having to override at end of the inheritance chain
     * Get collection of reviews - overriding core to remove per-store check
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        
        //AMFDev: if enabled set store ID to default to show all reviews within the PDP Reviews tab (Ajax loaded)
    
        if($this->helperData->getGeneralConfig('show_reviews_from_all_stores')) {
            $storeId = 0;
        } else {
            $storeId = $this->_storeManager->getStore()->getId();
        }
        
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $storeId
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getProduct()->getId()
            )->setDateOrder();
        }
        return $this->_reviewsCollection;
    }
}
