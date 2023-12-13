<?php

namespace Riverstone\ProductReviews\Controller\Product;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Riverstone\ProductReviews\Model\ReviewManagementFactory as ReviewFactory;

class Image extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var ReviewFactory
     */
    protected $managementFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ReviewFactory $managementFactory
     */
    public function __construct(
        Context       $context,
        JsonFactory   $jsonFactory,
        ReviewFactory $managementFactory
    ) {
        $this->managementFactory = $managementFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }
    /**
     * Save Review media
     *
     * @return ResponseInterface|Json|ResultInterface
     */
    public function execute()
    {
        $response = $this->jsonFactory->create();

        $mediaId = $this->getRequest()->getParam('media_id');
        $imagePathToDelete = $this->getRequest()->getParam('image_path');
        try {
            $mediaModel = $this->managementFactory->create();
            $media = $mediaModel->load($mediaId);

            if ($media->getId()) {
                $imagePaths = explode(',', $media->getImage());

                $updatedImagePaths = array_filter($imagePaths, function ($path) use ($imagePathToDelete) {
                    return trim($path) !== $imagePathToDelete;
                });
                $media->setImage(implode(',', $updatedImagePaths));
                $media->save();

                $response->setData(['success' => true]);
            } else {
                $response->setData(['success' => false, 'error' => 'Image not found']);
            }
        } catch (Exception $e) {
            $response->setData(['success' => false, 'error' => $e->getMessage()]);
        }

        return $response;
    }
}
