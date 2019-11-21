angular.module("starter").factory('ZopimChat', function($pwaRequest) {
    var factory = {
        value_id: null
    };

    factory.find = function(product_id) {

        if(!this.value_id) {
            return $pwaRequest.reject("[ZopimChat::find] missing value_id.");
        }

        return $pwaRequest.get("zopimchat/mobile_view/find", {
            urlParams: {
                value_id: this.value_id
            },
			cache: false,
			refresh: true
        });
    };
    return factory;
});