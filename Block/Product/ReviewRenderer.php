<?php
/**
 * Review renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AMFDev\AMFReview\Block\Product;

use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Review\Model\ReviewSummaryFactory;
use Magento\Review\Observer\PredispatchReviewObserver;
use Magento\Store\Api\StoreRepositoryInterface;
/**
 * Class ReviewRenderer
 */
class ReviewRenderer extends \Magento\Review\Block\Product\ReviewRenderer
{

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @var ReviewSummaryFactory
     */
    private $reviewSummaryFactory;
    
    public $_storeRepositoryInterface;

    protected $helperData;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param array $data
     * @param ReviewSummaryFactory $reviewSummaryFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = [],
        ReviewSummaryFactory $reviewSummaryFactory = null,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface,
        \AMFDev\AMFReview\Helper\Data $helperData
    ) {
        $this->_reviewFactory = $reviewFactory;
        $this->reviewSummaryFactory = $reviewSummaryFactory ??
            ObjectManager::getInstance()->get(ReviewSummaryFactory::class);
        $this->_storeRepositoryInterface = $storeRepositoryInterface;
        $this->helperData = $helperData;
        parent::__construct(
                            $context,
                            $reviewFactory,
                            $data,
                            $reviewSummaryFactory
                            );
    }

    /**
     * Get review summary html - overriding to remove the per-store check
     *
     * @param Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    ) 
        
    {
        //AMFDev: if enabled, set storeID to default to capture all reviews
        if($this->helperData->getGeneralConfig('show_reviews_from_all_stores')) {
            $storeId = 0;
        } else {
            $storeId = $this->_storeManager->getStore()->getId();
        }  
                          
        /* AMFDev: added check for zero value:
            see the observer at: module-review/Observer/CatalogProductListCollectionAppendSummaryFieldsObserver.php
            which uses method: module-review/Model/ResourceModel/Review/Summary.php
            this sets review_summary to 0 if it's null in the DB, so i don't understand why this wasn't added in the first place
            this doesn't seem to affect the PDP, only the PLP
        */
        
        if ($product->getRatingSummary() === null || $product->getRatingSummary() == 0) { // AMFDev: previously: $product->getRatingSummary() === null
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $product,
                $storeId
            );
        }

        // AMFDev: see previous comment, same applies here
        if ((null === $product->getRatingSummary() || $product->getRatingSummary() == 0) && !$displayIfNoReviews) { // AMFDev: previously: $product->getRatingSummary() === null
            return '';
        }
        
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = self::DEFAULT_VIEW;
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        $this->setProduct($product);
        
        return $this->toHtml();
   
    }
    

 
}
