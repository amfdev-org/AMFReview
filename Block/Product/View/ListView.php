<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AMFDev\AMFReview\Block\Product\View;

/**
 * Detailed Product Reviews
 *
 * @api
 * @since 100.0.2
 */
class ListView extends \Magento\Review\Block\Product\View\ListView
{

    
    /**METHOD IS FROM PARENT - \Magento\Review\Block\Product\View
     * Get collection of reviews - overriding core to remove per-store check
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                0 //AMFDEV: changed from $this->_storeManager->getStore()->getId()
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
