<?php

class ZopimChat_Mobile_ViewController extends Application_Controller_Mobile_Default {


    public function findAction() {

		$data['dd']="1";
        if($value_id = $this->getRequest()->getParam('value_id')) {

            try {
			
				$option = $this->getCurrentOptionValue();
				$chat = (new ZopimChat_Model_ZopimChat())->find(["value_id" => $value_id]);
				$customer = $this->getSession()->getCustomer();

				$html_old = '<html>
					<head>
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<meta content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" name="viewport" />
						<meta content="black" name="apple-mobile-web-app-status-bar-style" />
						<meta content="IE=8" http-equiv="X-UA-Compatible" />
						<style type="text/css">            
							html, body { margin:0; padding:0; border:none; }            
							html { overflow: scroll;  }            
							body { font-size: 15px; width: 100%; height: 100%; overflow: auto; -webkit-user-select : none; -webkit-text-size-adjust : none; -webkit-touch-callout: none; line-height:1; background-color:white; }
						</style>
					</head>
					<body>						
						<script>
							var zapimJS = document.createElement("script");
							zapimJS.type = "text/javascript";
							zapimJS.src = "https://v2.zopim.com/?'.$chat->getCode().'";
							zapimJS.onload = function() {
								console.log("ZopimChat loaded");
								$zopim(function() {
									$zopim.livechat.window.setSize("large");
									$zopim.livechat.window.show();';
								
									if ($customer->getId()) {
										$html_old.='$zopim.livechat.setName("'.trim($customer->getFirstname().' '.$customer->getLastname()).'");';
										$html_old.='$zopim.livechat.setEmail("'.trim($customer->getEmail()).'");';
									}
				
								
								$html_old.='});
							}
							document.head.appendChild(zapimJS);
						</script>
					<style>
						.meshim_widget_widgets_TitleBar.ltr .icons {display: none !important;}
					</style>
					</body>
					</html>';
					
				$html = '<html>
					<head>

						<style type="text/css">            
							html, body { margin:0; padding:0; border:none; }            
							html { overflow: scroll;  }            
							body { font-size: 15px; width: 100%; height: 100%; overflow: auto; -webkit-user-select : none; -webkit-text-size-adjust : none; -webkit-touch-callout: none; line-height:1; background-color:white; }
						</style>
						<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key='.$chat->getCode().'"> </script>
					
					</head>
					<body>						
					<script type="text/javascript">
zE("webWidget", "prefill", {
  name: {
    value: "'.trim($customer->getFirstname().' '.$customer->getLastname()).'",
    readOnly: true // optional
  },
  email: {
    value: "'.trim($customer->getEmail()).'",
    readOnly: true // optional
  }
});
zE("webWidget", "identify", {
  name: "'.trim($customer->getFirstname().' '.$customer->getLastname()).'",
  email: "'.trim($customer->getEmail()).'"
});
					zE("webWidget", "open");
					</script>
					<style>
					.u-userHeaderButtonColorMobile {display: none !important;}
					</style>
					</body>
					</html>';					
		
                $data = array(
                    "chat" => $chat->getData(),
					"customer" =>$customer->getData(),
					"html"=>$html
                );
            }
            catch(Exception $e) {
                $data = array('error' => 1, 'message' => $e->getMessage());
            }

        }else{
            $data = array('error' => 1, 'message' => 'An error occurred during process. Please try again later.');
        }

        $this->_sendHtml($data);

    }

}