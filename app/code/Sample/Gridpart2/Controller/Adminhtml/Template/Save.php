<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sample\Gridpart2\Controller\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Sample\Gridpart2\Controller\Adminhtml\Template
{
    /**
     * Save Newsletter Template
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/template'));
        }
   
         $template = $this->_objectManager->create('Sample\Gridpart2\Model\Template');
        $id = (int)$request->getParam('id');

        if ($id) {
            $template->load($id);
        }

        try {
            $data = $request->getParams();
           
             if (isset($_FILES['background']) && $_FILES['background']['name'] != '') {
                try {
                    
                    $uploader = $this->_objectManager->get('Sample\Gridpart2\Model\Theme\Upload');
                    $backgroundModel = $this->_objectManager->get('Sample\Gridpart2\Model\Theme\Background');
                    $data['background'] = $uploader->uploadFileAndGetName('background', $backgroundModel->getBaseDir(), $data);
                    
                
                } catch (Exception $e) {
                    
                    $data['background'] = $_FILES['background']['name'];
                    
                }
            } else {
                $data['background'] = $data['background']['value'];
            }
                
            
            
            $template->setData('name',
                $request->getParam('name')
            )->setData('background',
                $data['background']
            )->setData('stylecolor',
                $request->getParam('stylecolor')
            )->setData('textcolor',
                $request->getParam('textcolor')
            )->setData('status',
                $request->getParam('status')
            );

           

            $template->save();

            $this->messageManager->addSuccess(__('The gift card template has been saved.'));
            $this->_getSession()->setFormData(false);

            
        } catch (LocalizedException $e) {
            
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
        } catch (\Exception $e) {
            
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
