<?php

/**
 * Class WebView_Mobile_ViewController
 */
class WebView_Mobile_ViewController extends Application_Controller_Mobile_Default
{
    /**
     *
     */
    public function findAction()
    {
        try {
            $option = $this->getCurrentOptionValue();
            if ($option) {
				$migaIframe = (new WebView_Model_WebView())
                    ->find($option->getId(), 'value_id');

				if (!$migaIframe->getId()) {
				    throw new Nwicode_Exception(__('The requested webview doesnt exists.'));
                }

                $payload = [
                    'success' => true,
                    'webview' => $migaIframe->getData(),
                    'page_title' => $option->getTabbarName()
                ];
            } else {
                throw new Nwicode_Exception(__('An error occurred during process. Please try again later.'));
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
