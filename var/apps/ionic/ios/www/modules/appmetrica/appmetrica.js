App.controller('AppmetricaController', function($log,$http,Url,$window,$rootScope) {
});
App.run(function($injector,$ionicConfig,$ionicHistory,$ionicPlatform,$ionicPopup,$location,$log,$ocLazyLoad,$pwaRequest,$q,$rootScope,$session,$state,
$templateCache,$timeout,$translate,$window, Analytics,Application,Customer,Dialog,Pages,Push,PushService,SB,$http,Url) {
    $http({
        method: 'GET',
        url: Url.get('application/mobile_data/findappmetricakey'),
        cache: false,
        responseType:'json'
    }).success(function(data) {
         if (data.appmetrica_key) {
            $ionicPlatform.ready(function() {
                var configuration = {
                    apiKey: data.appmetrica_key,
                    trackLocationEnabled: true,
                    handleFirstActivationAsUpdateEnabled: true,
                    sessionTimeout: 15
                };
                window.appMetrica.activate(configuration);
                
                if ($rootScope.isNativeApp) {
                    if (!$window.localStorage.getItem("first_running")) {
                        $window.localStorage.setItem("first_running", "true");
                        window.appMetrica.reportEvent($translate.instant("First running"));
                    }
                    else
                    {
                        window.appMetrica.reportEvent($translate.instant("Reopening application"));
                    }
                }
                
                $rootScope.$on('$stateChangeSuccess', function (event) {
                    window.appMetrica.reportEvent(arguments[1].url, arguments[2]);
                });
            });    
        };
    });
});