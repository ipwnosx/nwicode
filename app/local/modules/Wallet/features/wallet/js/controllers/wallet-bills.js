/*global
 App, angular, BASE_PATH
 */
angular.module("starter").controller("WalletBillsController", function ($log, $sce, $state, $scope, $stateParams, $timeout, Wallet,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("WalletBillsController fired");

	$scope.wallet_customer_id = $stateParams.wallet_customer_id;
	console.log("wallet_customer_id: "+$stateParams.wallet_customer_id);
	
	$scope.value_id = $stateParams.value_id;
	Wallet.value_id= $stateParams.value_id;
	$scope.customer_email = "";
	$scope.data = {};
	$scope.customer = {};
	$scope.is_loading = true;
	$scope.old_style = true;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("Wallet loadContent.");
		Wallet.findBills($scope.wallet_customer_id).then(function (data) {
			console.log("Wallet.findBills:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});
			
    };
    
	$scope.loadContent();

	
	$scope.openBill = function(t) {
	
		$state.go('wallet-bill', {
			value_id: $scope.value_id,
			wallet_customer_id:$scope.wallet_customer_id,
			wallet_bill_id: t.id
		});	
	
	}

}).controller("WalletBillController", function ($log, $sce, $scope,$state, $stateParams, $window, $timeout, Wallet,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("WalletBillController fired");

	$scope.wallet_customer_id = $stateParams.wallet_customer_id;
	console.log("wallet_customer_id: "+$stateParams.wallet_customer_id);
	
	$scope.value_id = $stateParams.value_id;
	$scope.wallet_bill_id = $stateParams.wallet_bill_id;
	Wallet.value_id= $stateParams.value_id;

	$scope.data = {};
	$scope.customer = {};
	$scope.wallet = {};
	$scope.is_loading = true;
	$scope.old_style = true;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("Wallet loadContent.");
		Wallet.findBill($scope.wallet_bill_id).then(function (data) {
			console.log("Wallet.findBill:");
			console.log(data);
			$scope.data = data.bill;
			$scope.customer = data.customer;
			$scope.wallet = data.wallet;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});
			
    };
    
	$scope.loadContent();

	$scope.acceptBill  = function (t) {
		console.log("acceptBill clicked:");
		console.log(t);
		Loader.show();
		$scope.is_loading = true;
		Wallet.acceptBill(t.id).then(function (data) {
			console.log("Wallet.acceptBill:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
			$window.history.go(-1);
		});		
	}
	
	$scope.cancelBill = function (t) {
		console.log("cancelBill clicked:");
		console.log(t);
		Loader.show();
		$scope.is_loading = true;
		Wallet.cancelBill(t.id).then(function (data) {
			console.log("Wallet.cancelBill:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
			$window.history.go(-1);
		});			
	}

});