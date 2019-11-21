angular.module("starter").factory('Followus', function($pwaRequest) {
    var factory = {
        value_id        : null,
        extendedOptions : {}
    };
	
    factory.find = function() {

        if(!this.value_id) {
            return $pwaRequest.reject("[Followus::find] missing value_id.");
        }

        return $pwaRequest.get("followus/mobile_view/find", { 
            urlParams: {
                value_id: this.value_id
            },
			cache: false,
			refresh: true
        });
    };	
    return factory;
});