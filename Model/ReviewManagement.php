<?php

namespace Riverstone\ProductReviews\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;

class ReviewManagement extends AbstractModel
{
    /**
     * @param Context $context
     * @param Registry $registry
     * @param UrlInterface $url
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context          $context,
        Registry         $registry,
        UrlInterface     $url,
        AbstractResource $resource = null,
        AbstractDb       $resourceCollection = null,
        array            $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Load ReviewManagement
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Riverstone\ProductReviews\Model\ResourceModel\ReviewManagement'); //phpcs:disable
    }
}
