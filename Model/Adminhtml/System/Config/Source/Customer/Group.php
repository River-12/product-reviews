<?php

namespace Riverstone\ProductReviews\Model\Adminhtml\System\Config\Source\Customer;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class Group implements ArrayInterface
{
    /**
     * phpcs:disable
     */
    protected $_options;

    /**
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct(CollectionFactory $groupCollectionFactory)
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * Return customer Group Collection
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
