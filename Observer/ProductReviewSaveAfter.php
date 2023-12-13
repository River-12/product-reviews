<?php

namespace Riverstone\ProductReviews\Observer;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Riverstone\ProductReviews\Model\ReviewManagementFactory;

class ProductReviewSaveAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ReviewMediaFactory
     */
    protected $reviewManagementFactory;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList::MEDIA
     */
    protected $mediaDirectory;
    /**
     * @var UploaderFactory
     */
    protected $fileUploaderFactory;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var Timezone
     */
    protected $timezone;

    /**
     * @param RequestInterface $request
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     * @param ReviewManagementFactory $reviewManagementFactory
     * @param  Session Session
     * @param  CollectionFactory  $collectionFactory
     * @param  Timezone $timezone
     */
    public function __construct(
        RequestInterface $request,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        ReviewManagementFactory $reviewManagementFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\Timezone $timezone
    ) {
        $this->request = $request;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->customerSession=$customerSession;
        $this->timezone=$timezone;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->reviewManagementFactory = $reviewManagementFactory;
    }

    /**
     * Executed after a product review is saved
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $reviewId = $observer->getEvent()->getObject()->getReviewId();
        $media = $this->request->getFiles('review_media');
        $imageTarget = $this->mediaDirectory->getAbsolutePath('river/review_images');
        if ($media) {
            try {
                $uploadImages=[];
                foreach ($media as $i => $file) {
                    $uploader = $this->fileUploaderFactory->create(['fileId' => 'review_media[' . $i . ']']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $targetDirectory = $imageTarget;
                    $result = $uploader->save($targetDirectory);
                    $uploadImages[]=$result['file'];
                }
                $customer = $this->customerSession;
                $authorName = $customer->isLoggedIn() ? $customer->getCustomer()->getName() : 'Guest';
                $customerId = $customer->isLoggedIn() ? $customer->getCustomer()->getId() : 'Guest';
                $time = $this->timezone;
                $reviewMedia = $this->reviewManagementFactory->create();
                $reviewMedia->setReviewId($reviewId);
                $reviewMedia->setPostBy($authorName);
                $reviewMedia->setCustomerId($customerId);
                $reviewMedia->setStatus(1);
                $reviewMedia->setCreatedAt(date('Y-m-d H:i:s', $time->scopeTimeStamp()));
                $reviewMedia->setImage(implode(',', $uploadImages));
                $reviewMedia->save();
            } catch (Exception $e) {
                if ($e->getCode() == 0) {
                    $this->messageManager->addError("Something went wrong while saving review attachment(s).");
                }
            }
        }
    }
}
