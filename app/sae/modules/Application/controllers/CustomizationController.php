<?php

/**
 * Class Application_CustomizationController
 */
class Application_CustomizationController extends Application_Controller_Default
{
    /**
     *
     */
    public function indexAction()
    {

        $resource = new Acl_Model_Resource();
        $resources = $resource->findAll([new Zend_Db_Expr('code LIKE \'editor_%\' AND url IS NOT NULL')]);

        foreach ($resources as $resource) {
            if ($this->_canAccess($resource->getCode())) {
                $url = rtrim(trim($resource->getData('url')), '*');
                $this->_redirect($url);
            }
        }

        $this->_redirect('application/customization_design_style/edit');
    }

    /**
     * @throws Zend_Layout_Exception
     * @throws Zend_Session_Exception
     */
    public function checkAction()
    {
        if ($this->getRequest()->isPost()) {
            $adminCanPublish = $this->getSession()
                ->getAdmin()
                ->canPublishThemself();

            $errors = $this->getApplication()
                ->isAvailableForPublishing($adminCanPublish);

            if (!empty($errors)) {
                array_unshift($errors, __('In order to publish your application, we need:'));
                $message = join('<br />- ', $errors);

                $html = [
                    'message' => $message,
                    'message_button' => 1,
                    'message_loader' => 1
                ];
            }

            $this->getResponse()->setBody(Zend_Json::encode($html))->sendResponse();
            die;
        }
    }

    /**
     * @param $nodeName
     * @param $title
     * @return Nwicode_Layout|Nwicode_Layout_Email
     * @throws Zend_Layout_Exception
     */
    public function baseEmail($nodeName,
                              $title)
    {
        $layout = new Nwicode\Layout();
        $layout = $layout->loadEmail('application', $nodeName);
        $layout
            ->setContentFor('base', 'email_title', __('Publication request') . ' - ' . $title)
            ->setContentFor('footer', 'show_legals', true)
        ;

        return $layout;
    }
}
