<?php

/**
 * Class WebView_ApplicationController
 */
class WebView_ApplicationController extends Application_Controller_Default
{
    /**
     * Simple edit post, validator
     */
    public function editpostAction()
    {
        $request = $this->getRequest();
        $values = $request->getPost();

        try {
            $form = new WebView_Form_WebView();
            if ($form->isValid($values)) {
                $optionValue = $this->getCurrentOptionValue();

                $webview = (new WebView_Model_WebView())
                    ->find($values['value_id'], 'value_id');

                $webview->setData($values);

                if ($values['icon'] === '_delete_') {
                    $webview->setData('icon', '');
                } else if (file_exists(Core_Model_Directory::getBasePathTo('images/application' . $values['icon']))) {
                    // Nothing changed, skip!
                } else {
                    $background = Nwicode_Feature::moveUploadedFile(
                        $this->getCurrentOptionValue(),
                        Core_Model_Directory::getTmpDirectory() . '/' . $values['icon']);
                    $webview->setData('icon', $background);
                }

                $webview->save();

                // Update touch date, then never expires (until next touch)!
                $optionValue
                    ->touch()
                    ->expires(-1);

                $payload = [
                    'success' => true,
                    'message' => __('Success.'),
                ];
            } else {
                $payload = [
                    'error' => true,
                    'message' => $form->getTextErrors(),
                    'errors' => $form->getTextErrors(true)
                ];
            }
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
}
