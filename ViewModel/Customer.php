<?php

namespace Riverstone\ProductReviews\ViewModel;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Riverstone\ProductReviews\Model\ReviewManagement;
use Riverstone\ProductReviews\Model\ReviewManagementFactory;

class Customer implements ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Riverstone\ProductReviews\Model\ReviewManagementFactory
     */
    protected $reviewManagementFactory;
    /**
     * @param CustomerSession $customerSession
     * @param ReviewManagementFactory $reviewManagementFactory
     */
    public function __construct(CustomerSession $customerSession, ReviewManagementFactory $reviewManagementFactory)
    {
        $this->reviewManagementFactory = $reviewManagementFactory;
        $this->customerSession = $customerSession;
    }
    /**
     * To get the customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
    /**
     * To get Customer Group
     *
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerGroupId()
    {
        return $this->customerSession->getCustomerGroupId();
    }
    /**
     * Load Review ID's
     *
     * @return ReviewManagement
     */
    public function getImages()
    {
        return $this->reviewManagementFactory->create();
    }
}
