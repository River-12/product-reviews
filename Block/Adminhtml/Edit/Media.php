<?php

namespace Riverstone\ProductReviews\Block\Adminhtml\Edit;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\UrlInterface;
use Riverstone\ProductReviews\Model\ReviewManagementFactory;

class Media extends Template
{
    /**
     * @var ReviewManagementFactory
     */
    protected $reviewManagementFactory;

    /**
     * @param Context $context
     * @param ReviewManagementFactory $reviewManagementFactory
     */
    public function __construct(Context $context, ReviewManagementFactory $reviewManagementFactory)
    {
        $this->reviewManagementFactory = $reviewManagementFactory;
        $this->setTemplate("media.phtml");
        parent::__construct($context);
    }

    /**
     * Get media collection for a review
     *
     * @return AbstractDb|AbstractCollection|null
     * phpcs:disable
     */
    public function getMediaCollection()
    {
        $thisReviewMediaCollection = $this->reviewManagementFactory->create()->getCollection()->addFieldToFilter('review_id', $this->getRequest()->getParam('id'));
        return $thisReviewMediaCollection;
    }
    /** phpcs:enable */
    /**
     * Get review_images directory path
     *
     * @return string
     * @throws NoSuchEntityException
     * phpcs:disable
     */
    public function getReviewMediaUrl()
    {
        $reviewMediaDirectoryPath = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'river/review_images';
        return $reviewMediaDirectoryPath;
    }
    /** phpcs:enable */
}
