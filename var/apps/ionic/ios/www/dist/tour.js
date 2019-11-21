App.run(function($window,$http,Url,$state) {
    $http({
        method: 'GET',
        url: Url.get('application/mobile_data/gettoursettings'),
        cache: false,
        responseType:'json'
    }).success(function(data) {
        if (data.enable_tour=="1" && $window.localStorage.getItem("tour_uid")!=data.tour_uid) {
            console.log("Tour showing.");
            $window.localStorage.setItem("tour_uid",data.tour_uid);
            $state.go("firsttour");
        } else {
            console.log("Tour disabled or skipped:"+$window.localStorage.getItem("tour_uid"));
        }
    });
});