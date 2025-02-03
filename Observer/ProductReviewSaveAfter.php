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
    protected $request;
    protected $reviewManagementFactory;
    protected $mediaDirectory;
    protected $fileUploaderFactory;
    protected $customerSession;
    protected $timezone;

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
        $this->customerSession = $customerSession;
        $this->timezone = $timezone;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->reviewManagementFactory = $reviewManagementFactory;
    }

    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/customAgalya.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $reviewId = $observer->getEvent()->getObject()->getReviewId();
        $media = $this->request->getFiles('review_media');
        $video = $this->request->getFiles('video_file'); // Get video file
        $logger->info($video);
        $imageTarget = $this->mediaDirectory->getAbsolutePath('river/review_images');
        $videoTarget = $this->mediaDirectory->getAbsolutePath('river/review_videos'); // Video target directory

        try {
            $uploadImages = [];
            if ($media) {
                foreach ($media as $i => $file) {
                    $uploader = $this->fileUploaderFactory->create(['fileId' => 'review_media[' . $i . ']']);
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(true);
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
                    $result = $uploader->save($imageTarget);
                    $uploadImages[] = $result['file'];
                }
            }

            $uploadedVideo = null;
            if ($video && $video['size'] > 0) {
                $logger->info('Hello World!');
                $videoUploader = $this->fileUploaderFactory->create(['fileId' => 'video_file']);
                $videoUploader->setAllowRenameFiles(true);
                $videoUploader->setFilesDispersion(true);
                $videoUploader->setAllowCreateFolders(true);
                $videoUploader->setAllowedExtensions(['mp4', 'avi', 'mov']); // Allowed video extensions
                $videoResult = $videoUploader->save($videoTarget);
                $uploadedVideo = $videoResult['file'];
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
            $reviewMedia->setVideo($uploadedVideo); 
            $reviewMedia->save();
        } catch (Exception $e) {
            if ($e->getCode() == 0) {
                $this->messageManager->addError("Something went wrong while saving review attachment(s).");
            }
        }
    }
}
