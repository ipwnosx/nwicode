angular
    .module('starter')
    .factory('WebView', function ($pwaRequest) {
        /**
         * @type {{}}
         */
        var factory = {
            value_id: null
        };

        /**
         * @return Promise
         */
        factory.find = function () {
            if (!this.value_id) {
                return;
            }

            return $pwaRequest.get('webview/mobile_view/find', {
                urlParams: {
                    value_id: this.value_id
                }
            });
        };

        return factory;
    });
