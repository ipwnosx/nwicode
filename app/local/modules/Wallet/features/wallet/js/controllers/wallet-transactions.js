/*global
 App, angular, BASE_PATH
 */
angular.module("starter").controller("WalletTransactionsController", function ($log, $sce, $scope, $stateParams, $timeout, Wallet,Loader,Customer) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
    
	console.log("WalletTransactionsController fired");

	$scope.wallet_customer_id = $stateParams.wallet_customer_id;
	console.log("wallet_customer_id: "+$stateParams.wallet_customer_id);
	
	$scope.value_id = $stateParams.value_id;
	Wallet.value_id= $stateParams.value_id;
	$scope.customer_email = "";
	$scope.data = {};
	$scope.is_loading = true;
	$scope.old_style = true;
	Loader.show();

    
	$scope.loadContent = function () {
		console.log("Wallet loadContent.");
		Wallet.findTransactions($scope.wallet_customer_id).then(function (data) {
			console.log("Wallet.findTransactions:");
			console.log(data);
			$scope.data = data;
			
		}).then(function () {
			$scope.is_loading = false;
			Loader.hide();
		});
			
    };
    
	$scope.loadContent();


});