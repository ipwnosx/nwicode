/*global
 App, angular, BASE_PATH
 */
angular.module("starter").controller("ZopimChatViewController", function ($log, $sce, $scope, $stateParams, $timeout, ZopimChat,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("ZopimChatViewController fired");
	
	$scope.value_id = $stateParams.value_id;
	ZopimChat.value_id= $stateParams.value_id;
	$scope.page_title = "";
	$scope.customer_name = "";
	$scope.customer_email = "";
	$scope.html_string = "";
	$scope.is_loading = true;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("ZopimChat loadContent.");
		ZopimChat.find().then(function (data) {
			console.log("ZopimChat.find:");
			console.log(data);
			$scope.page_title = data.chat.title;
			$scope.is_loading = false;
			Loader.hide();			
	
			$timeout(function() {
				try {
					var iframe = document.querySelector('#zopimchat-view');
					iframe = iframe.contentWindow || iframe.contentDocument.document || iframe.contentDocument;
					iframe.document.open();
					iframe.document.write(data.html);
					iframe.document.close();
				} catch(e) {
					$log.error(e);
				}
				$scope.is_loading = false;
			}, 200);			
			
			
			//Load chat script

            //html_string = '<script>var zapimJS = document.createElement("script");';
            //html_string = html_string + 'zapimJS.type = "text/javascript";';
            //html_string = html_string + 'zapimJS.src = "https://v2.zopim.com/?'+data.chat.code+'";';
            //html_string = html_string + 'zapimJS.onload = function() {';
			//html_string = html_string + '	console.log("ZopimChat loaded");';
			//html_string = html_string + '	$zopim(function() {';
			//html_string = html_string + '		$zopim.livechat.setName("'+data.customer.firstname+' '+data.customer.lastname'");';
			//html_string = html_string + '		$zopim.livechat.setEmail("'+data.customer.email+'");';
			//html_string = html_string + '		$zopim.livechat.window.setSize("large");';
			//html_string = html_string + '		$zopim.livechat.window.setOffsetVertical(50);';
			//html_string = html_string + '		$zopim.livechat.window.show();';
			//html_string = html_string + '	});';
			//html_string = html_string + '	};';
			//	document.querySelectorAll('[data-foo]');
			//document.getElementById("zopimchat-view").src = "data:text/html;charset=utf-8," + escape(html_string);
		
            //};
			//var mydiv=document.getElementById("zopimchat-view"); 
            //mydiv.appendChild(zapimJS);
			
		}).then(function () {
			//$scope.is_loading = false;
			//Loader.hide();
		});
			
    };
    
	$scope.loadContent();
	
	
/*
    window.$zopim || (function(d, s) {
        var z = $zopim = function(c) {
                z._.push(c)
            },
            $ = z.s =
            d.createElement(s),
            e = d.getElementsByTagName(s)[0];
        z.set = function(o) {
            z.set.
            _.push(o)
        };
        z._ = [];
        z.set._ = [];
        $.async = !0;
        $.setAttribute("charset", "utf-8");
        $.src = "https://v2.zopim.com/?6WfaCtnF1Eo10xN1M31M5iGCHUsUJnSf";
        z.t = +new Date;
        $.
        type = "text/javascript";
        e.parentNode.insertBefore($, e)
    })(document, "script");
*/
});