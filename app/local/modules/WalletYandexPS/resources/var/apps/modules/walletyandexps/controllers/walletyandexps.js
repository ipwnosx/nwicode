/**
 * QiwimobilcartController
 */
App.config(function($stateProvider, HomepageLayoutProvider) {

    $stateProvider.state('walletyandex-payment', {
        url: BASE_PATH+"/walletyandexps/mobile_walletyandex/find/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/amount/:amount",
        controller: 'WalletYandexPSController',
        templateUrl: "modules/walletyandexps/templates/l1/payment.html"
    });
    $stateProvider.state('walletyandex-payment-result', {
        url: BASE_PATH+"/walletyandexps/mobile_yandex/result/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/status/:status",
        controller: 'WalletYandexPSResultController',
        templateUrl: "modules/walletyandexps/templates/l1/payment.html"
    });	

}).controller('WalletYandexPSController', function ($scope, $state, $stateParams,$timeout, $translate, WalletYandexPSFactory,Loader,$window, Dialog,$ionicHistory) {
    
	console.log("WalletYandexPSController fired!");
	
	$scope.data = {};
	WalletYandexPSFactory.value_id = $stateParams.value_id;
	$scope.value_id = $stateParams.value_id;
	$scope.data.value_id = $stateParams.value_id;
	$scope.data.amount = $stateParams.amount;
	$scope.data.wallet_id = $stateParams.wallet_id;
	$scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = true;
	$scope.old_style = true;
	console.log("WalletYandexPSController:");
	console.log($scope.data);
	Loader.show();
	WalletYandexPSFactory.CreateForm($scope.data).then(function (formData) {
		console.log("WalletYandexPSFactory.CreateForm return:");
		console.log(formData);
		//Loader.hide();
		if (formData.success) $window.location = formData.payment_url;
		else {
			Loader.hide();
			$scope.is_loading = false;
			Dialog.alert($translate.instant("Error"), formData.error_yandex.code+"<br>"+formData.error_yandex.description,"OK") .then(function () {
				$ionicHistory.nextViewOptions({
					historyRoot: true,
					disableAnimate: false
				});
			$state.go('home');
			});
		}

	}, function (error_data) {
		console.log("WalletYandexPSFactory.CreateForm return ERROR:");
		console.log(error_data);	
		Loader.hide();
		Dialog.alert($translate.instant("Error"), error_data.message,"OK");
		$ionicHistory.nextViewOptions({
			historyRoot: true,
			disableAnimate: false
		});
		$state.go('home');
	});	

}).controller('WalletYandexPSResultController', function ($scope, $state, $stateParams,$timeout, WalletYandexPSFactory,Loader,$window, Dialog,HomepageLayout, $ionicHistory) {
     angular.extend($scope, {
        value_id: $stateParams.value_id,
        layout: HomepageLayout
    });   
	console.log("WalletYandexPSResultController fired!");
	
	$scope.data = {};
	WalletYandexPSFactory.value_id = $stateParams.value_id;
	$scope.value_id = $stateParams.value_id;
	$scope.data.value_id = $stateParams.value_id;
	$scope.data.status = $stateParams.status;
	$scope.data.wallet_id = $stateParams.wallet_id;
	$scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = false;
	$scope.old_style = true;	
	console.log("WalletYandexPSResultController:");
	console.log($scope.data);
	
	$scope.closeWindow = function() {
		$ionicHistory.nextViewOptions({
			historyRoot: true,
			disableAnimate: false
		});	
		$state.go('home').then(function () {
			$state.go('wallet-view', {
				value_id: $scope.value_id
			});
		});		
	
	}

});