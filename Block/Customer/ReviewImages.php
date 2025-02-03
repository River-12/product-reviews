<?php

namespace Riverstone\ProductReviews\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;
use Riverstone\ProductReviews\Model\ReviewManagementFactory;

class ReviewImages extends Template
{
    protected $reviewManagementFactory;
    protected $urlInterface;

    public function __construct(
        Template\Context $context,
        ReviewManagementFactory $reviewManagementFactory,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->reviewManagementFactory = $reviewManagementFactory;
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function getMediaCollection()
    {
        $reviewId = $this->getRequest()->getParam('id');
        if ($reviewId) {
            return $this->reviewManagementFactory->create()->getCollection()
                ->addFieldToFilter('review_id', $reviewId);
        }
        return null;
    }

    public function getReviewMediaUrl()
    {
        return $this->urlInterface->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'media/river/review_images';
    }
}