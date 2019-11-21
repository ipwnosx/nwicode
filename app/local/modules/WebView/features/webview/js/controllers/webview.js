angular
    .module('starter')
    .controller('WebViewViewController', function ($cordovaBarcodeScanner, $cordovaClipboard, $ionicHistory, $scope,
                                                      $timeout, $translate, $window, Dialog, $stateParams, $location,
                                                      $sce, $state, Application, WebView, Loader) {
        $scope.is_loading = true;
        Loader.show();

        /** Default Main URL */
        $scope.main_url = '';
        $scope.loading_spinner_delay = 5000;

        WebView.value_id = $stateParams.value_id;
        $scope.scan_protocols = ['tel:', 'http:', 'https:', 'geo:', 'smsto:', 'mailto:', 'ctc:'];
        $scope.is_protocol_found = false;
        $scope.webview = null;
        $scope.ios_styles = {
            'height': '100%'
        };
        $scope.title_styles = {};
        $scope.is_scanner_opened = false;

        $scope.loadContent = function () {
            $scope.title_styles = {
                'left': '0px',
                'right': '0px'
            };
            WebView
                .find()
                .then(function (data) {
                    if(data.webview.url === undefined || typeof data.webview.url === "undefined")
                    {
                        $scope.is_loading = false;
                        Loader.hide();
                        $scope.goToHomePage();
                    }
                    $scope.webview = data.webview;
                    $scope.loading_spinner_delay = parseInt($scope.webview.loading_spinner_delay) * 1000;
                    // Set main URL!
                    $scope.main_url = $scope.webview.url;

                    $scope.webview.url = $sce.trustAsResourceUrl($scope.webview.url);
                    $scope.webview.is_qr_ean = parseInt($scope.webview.is_qr_ean, 10);
                    $scope.page_title = data.page_title;
                    $scope.webview.icon = (data.webview.icon) ? IMAGE_URL + 'images/application/' + data.webview.icon : false;

                    if (ionic.Platform.isIOS()) {
                        $scope.ios_styles = {
                            'overflow-y': 'auto',
                            '-webkit-overflow-scrolling': 'touch',
                            '-webkit-transform': 'translate3d(0, 0, 0)',
                            'position': 'sticky',
                            'position': '-webkit-sticky',
                            'height': '100%'
                        };
                    }
                }).then(function () {
                });
            };

        $scope.goToFrameMainPage = function () {
            var current_url = $sce.getTrustedResourceUrl($scope.webview.url);
            if (current_url === $scope.main_url) {
                $scope.webview.url = $sce.trustAsResourceUrl(current_url + ' ');
            } else {
                $scope.webview.url = $sce.trustAsResourceUrl($scope.main_url);
            }
        };

        $scope.goToHomePage = function () {
            $state.go('home');
        };

        $scope.openScanner = function () {
            $cordovaBarcodeScanner.scan().then(function (barcodeData) {
                if (!barcodeData.cancelled && barcodeData.text !== '' && !$scope.is_scanner_opened) {
                    $scope.is_scanner_opened = true;
                    $timeout(function () {
                        for (var i = 0; i < $scope.scan_protocols.length; i++) {
                            if (barcodeData.text.toLowerCase().indexOf($scope.scan_protocols[i]) == 0) {

                                if ($scope.scan_protocols[i] == "http:" || $scope.scan_protocols[i] == "https:") {
                                    $window.open(barcodeData.text, "_blank", "location=yes");
                                } else {
                                    var content_url = barcodeData.text;

                                    // SMSTO:
                                    if ($scope.scan_protocols[i] == "smsto:" && ionic.Platform.isIOS()) {
                                        content_url = url.replace(/(smsto|SMSTO):/, "sms:").replace(/([0-9]):(.*)/, "$1");
                                        // GEO:
                                    } else if ($scope.scan_protocols[i] === 'geo:' && ionic.Platform.isIOS()) {
                                        content_url = url.replace(/(geo|GEO):/, 'https://maps.apple.com/?q=');
                                    }

                                    $window.open(content_url, '_blank', 'location=no');
                                }

                                $scope.is_protocol_found = true;
                                $scope.is_scanner_opened = false;
                                break;
                            } else if ($scope.scan_protocols[i] === 'ctc:') {
                                var buttons = ['OK'];
                                Dialog
                                    .confirm('Scan result', barcodeData.text, buttons, 'text-center')
                                    .then(function (res) {
                                        var tmpString = $scope.webview.query_string.replace('@@qr_ean@@', barcodeData.text);
                                        $scope.webview.url = $sce.trustAsResourceUrl(tmpString);
                                    });
                                $scope.is_scanner_opened = false;
                                $scope.is_protocol_found = true;
                                break;
                            }
                        }

                        if (!$scope.is_protocol_found) {
                            Dialog.alert('Scan result', barcodeData.text, 'OK');
                            $scope.is_scanner_opened = false;
                        }
                    });
                }
            }, function (error) {
                Dialog.alert('Error', 'An error occurred while reading the code.', 'OK');
                $scope.is_scanner_opened = false;
            });
        };
        
        window.iframeLoaded = function() {
            $timeout(function(){
                $scope.is_loading = false;
                Loader.hide();
            }, $scope.loading_spinner_delay);
        };

        $scope.loadContent();
});
