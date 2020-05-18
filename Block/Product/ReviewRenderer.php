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
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'Magento_Review::helper/summary.phtml',
        self::SHORT_VIEW => 'Magento_Review::helper/summary_short.phtml',
    ];

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
        \Magento\Store\Api\StoreRepositoryInterface $storeRepositoryInterface
    ) {
        $this->_reviewFactory = $reviewFactory;
        $this->reviewSummaryFactory = $reviewSummaryFactory ??
            ObjectManager::getInstance()->get(ReviewSummaryFactory::class);
        $this->_storeRepositoryInterface = $storeRepositoryInterface;
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
    ) {
                  
        //get all stores except admin, since we want to include all reviews
        //NOT USING FOR NOW
        /*$storesList = $this->_storeRepositoryInterface->getList();
        foreach ($storesList as $store) {
            if($store->getId() != 0) {
                $storeIds[] = $store->getId();
            }
        }*/
 
        /* AMFDev: added check for zero value:
            see the observer at: module-review/Observer/CatalogProductListCollectionAppendSummaryFieldsObserver.php
            which uses method: module-review/Model/ResourceModel/Review/Summary.php
            this sets review_summary to 0 if it's null in the DB, so i don't understand why this wasn't added in the first place
            this doesn't seem to affect the PDP, only the PLP
        */
        
        if ($product->getRatingSummary() === null || $product->getRatingSummary() == 0) { // AMFDev: previously: $product->getRatingSummary() === null
            $this->reviewSummaryFactory->create()->appendSummaryDataToObject(
                $product,
                0
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
    
    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary();
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getProduct()->getReviewsCount();
    }

    /**
     * Get review product list url
     *
     * @param bool $useDirectLink allows to use direct link for product reviews page
     * @return string
     */
    public function getReviewsUrl($useDirectLink = false)
    {
        $product = $this->getProduct();
        if ($useDirectLink) {
            return $this->getUrl(
                'review/product/list',
                ['id' => $product->getId(), 'category' => $product->getCategoryId()]
            );
        }
        return $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
    }


 
}
