angular.module("starter").controller("FollowusViewController", function ($scope, $stateParams, Followus,Loader) {
    angular.extend($scope, {
        is_loading  : true,
        value_id    : $stateParams.value_id
    });
	
	$scope.value_id = $stateParams.value_id;
	Followus.value_id = $stateParams.value_id;
	Loader.show();
	$scope.is_loading = true;
	console.log("FollowusViewController fired!");
	$scope.data = {};
	$scope.title = "";
	$scope.links_list_chunks = [];
	$scope.loadContent = function () {
	
		var fa = document.createElement("script"); // visual effet on touch
		fa.type = "text/javascript";
		fa.src = "modules/followus/libraries/fa5svg.js";
		fa.onload = function() {
			console.log("FontAwesome load complete.");
			console.log("Do Followus.find();");
			Followus.find().then(function (data) {
				console.log("Followus.find return:");
				console.log(data);
				$scope.title = data.followus.title;
				$scope.data = data.followus;
				
				$scope.links_list_chunks = $scope.chunkArray(data.lines,data.followus.columns);	
				console.log($scope.links_list_chunks);
				
			}).then(function () {
				$scope.is_loading = false;
				
				Loader.hide();
			});

		};
		document.body.appendChild(fa);
	}
	
	
	$scope.chunkArray = function(myArray, chunk_size){
		var results = [];
		
		while (myArray.length) {
			results.push(myArray.splice(0, chunk_size));
		}
		
		return results;
	}	
	
	$scope.openLink = function(link) {
		console.log("Open Link clicked:");
		console.log(link);
		window.open(link.url, '_system', 'location=yes');
	}
	
	$scope.loadContent();
});