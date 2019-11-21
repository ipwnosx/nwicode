/*global
 App, angular, BASE_PATH
 */
angular.module("starter").controller("WalletPayoutsController", function ($log, $sce, $scope, Modal, $stateParams, $timeout, Wallet,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("WalletPayoutsController fired");

	$scope.wallet_customer_id = $stateParams.wallet_customer_id;
	console.log("wallet_customer_id: "+$stateParams.wallet_customer_id);
	
	
	$scope.value_id = $stateParams.value_id;
	Wallet.value_id= $stateParams.value_id;
	$scope.customer_email = "";
	$scope.data = {};
	$scope.payout_modal = null;
	$scope.is_loading = true;
	$scope.old_style = true;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("Wallet loadContent.");
		Wallet.findPayouts($scope.wallet_customer_id).then(function (data) {
			console.log("Wallet.findPayouts:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});
			
    };
    
	$scope.loadContent();

	$scope.openPayout = function(t) {
		console.log("openPayout clicked:");
		console.log(t);
		
		Modal.fromTemplateUrl('features/wallet/assets/templates/l1/payout.html', {
		  scope: $scope,
		  animation: 'none'
		}).then(function(modal) {
			$scope.t = t;
			$scope.payout_modal = modal;
			$scope.payout_modal.show();
		
		});			
	
	};
	
	$scope.cancelRequest = function(t) {
		console.log("cancelRequest clicked:");
		console.log(t);
		Loader.show();
		$scope.is_loading = true;
		Wallet.cancelPayout(t.id).then(function (data) {
			console.log("Wallet.cancelPayout:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.payout_modal.hide();
			$scope.is_loading = false;
			Loader.hide();
		});		
	};
	
});