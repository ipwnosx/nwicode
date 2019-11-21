/**
 * QiwimobilcartController
 */
App.config(function($stateProvider, HomepageLayoutProvider) {

    $stateProvider.state('walletpaypal-payment', {
        url: BASE_PATH+"/walletpaypalps/mobile_walletpaypal/find/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/amount/:amount",
        controller: 'WalletPaypalPSController',
        templateUrl: "modules/walletpaypalps/templates/l1/payment.html"
    });
    $stateProvider.state('walletpaypal-payment-result', {
        url: BASE_PATH+"/walletpaypalps/mobile_paypal/result/value_id/:value_id/wallet_id/:wallet_id/wallet_customer_id/:wallet_customer_id/status/:status",
        controller: 'WalletPaypalPSResultController',
        templateUrl: "modules/walletpaypalps/templates/l1/payment.html"
    });	

}).controller('WalletPaypalPSController', function ($scope, $state, $stateParams,$timeout, $translate, WalletPaypalPSFactory,Loader,$window, Dialog,$ionicHistory) {
    
	console.log("WalletPaypalPSController fired!");
	
	$scope.data = {};
	WalletPaypalPSFactory.value_id = $stateParams.value_id;
	$scope.value_id = $stateParams.value_id;
	$scope.data.value_id = $stateParams.value_id;
	$scope.data.amount = $stateParams.amount;
	$scope.data.wallet_id = $stateParams.wallet_id;
	$scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = true;
	$scope.old_style = true;
	console.log("WalletPaypalPSController:");
	console.log($scope.data);
	Loader.show();
	WalletPaypalPSFactory.CreateForm($scope.data).then(function (formData) {
		console.log("WalletPaypalPSFactory.CreateForm return:");
		console.log(formData);
		Loader.hide();
		if (formData.success) $window.location = formData.payment_url;
		else {
			Dialog.alert($translate.instant("Error"), formData.error_paypal.error_description,"OK") .then(function () {
				$ionicHistory.nextViewOptions({
					historyRoot: true,
					disableAnimate: false
				});
			$state.go('home');
			});
		}

	}, function (error_data) {
		console.log("WalletPaypalPSFactory.CreateForm return ERROR:");
		console.log(error_data);	
		Loader.hide();
		Dialog.alert($translate.instant("Error"), error_data.message,"OK");
		$ionicHistory.nextViewOptions({
			historyRoot: true,
			disableAnimate: false
		});
		$state.go('home');
	});	

}).controller('WalletPaypalPSResultController', function ($scope, $state, $stateParams,$timeout, WalletPaypalPSFactory,Loader,$window, Dialog,HomepageLayout, $ionicHistory) {
     angular.extend($scope, {
        value_id: $stateParams.value_id,
        layout: HomepageLayout
    });   
	console.log("WalletPaypalPSResultController fired!");
	
	$scope.data = {};
	WalletPaypalPSFactory.value_id = $stateParams.value_id;
	$scope.value_id = $stateParams.value_id;
	$scope.data.value_id = $stateParams.value_id;
	$scope.data.status = $stateParams.status;
	$scope.data.wallet_id = $stateParams.wallet_id;
	$scope.data.wallet_customer_id = $stateParams.wallet_customer_id;
    $scope.is_loading = false;
	$scope.old_style = true;	
	console.log("WalletPaypalPSResultController:");
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