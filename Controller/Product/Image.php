<?php

namespace Riverstone\ProductReviews\Controller\Product;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Riverstone\ProductReviews\Model\ReviewManagementFactory as ReviewFactory;

class Image extends Action
{
    protected $jsonFactory;
    protected $managementFactory;

    public function __construct(
        Context       $context,
        JsonFactory   $jsonFactory,
        ReviewFactory $managementFactory
    ) {
        $this->managementFactory = $managementFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = $this->jsonFactory->create();

        $mediaId = $this->getRequest()->getParam('media_id');
        $imagePathToDelete = $this->getRequest()->getParam('image_path');

        try {
            $mediaModel = $this->managementFactory->create();
            $media = $mediaModel->load($mediaId);

            if ($media->getId()) {
                if ($imagePathToDelete) {
                    $imagePaths = explode(',', $media->getImage());
                    $updatedImagePaths = array_filter($imagePaths, function ($path) use ($imagePathToDelete) {
                        return trim($path) !== $imagePathToDelete;
                    });
                    $media->setImage(implode(',', $updatedImagePaths));
                }

                $media->save();
                $response->setData(['success' => true]);
            } else {
                $response->setData(['success' => false, 'error' => 'Media not found']);
            }
        } catch (Exception $e) {
            $response->setData(['success' => false, 'error' => $e->getMessage()]);
        }

        return $response;
    }
}
