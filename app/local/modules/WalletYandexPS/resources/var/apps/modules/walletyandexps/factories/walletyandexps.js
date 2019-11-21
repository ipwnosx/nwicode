App.factory('WalletYandexPSFactory', function($http, Url, $session, $window,$pwaRequest) {

    var factory = {};
    factory.value_id = null;

	
    factory.CreateForm = function(data) {

        if(!this.value_id) {
            return $pwaRequest.reject("[WalletYandexPS::createform] missing value_id.");
        }

        return $pwaRequest.post("walletyandexps/mobile_yandex/createform", {
            urlParams: {
                value_id: this.value_id
            },
			data: data,
			cache: false,
			refresh: true
        });
    };	
	
    return factory;
}); 