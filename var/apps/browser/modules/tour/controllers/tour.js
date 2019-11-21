App.config(function($stateProvider) {
    $stateProvider.state('firsttour', {
        url: BASE_PATH + "/firsttour/start",
        cache: false,
        controller: 'TourController',
        templateUrl: "modules/tour/templates/l1/tour.html"
    });
}).controller('TourController', function($timeout,$log,$scope,$rootScope,$ionicModal,Modal,$window,$http,AUTH_EVENTS,Url,HomepageLayout,$state,$location,Application,$ionicHistory,Customer) {
    $rootScope.$on('$locationChangeStart', function(event, toState, toStateParams, fromState, fromStateParams) {
        if(!$scope.lastslide) {
            event.preventDefault();
        }
    });
    $rootScope.$on('$stateChangeStart', function(event, toState, toStateParams, fromState, fromStateParams) {
        if(!$scope.lastslide) {
            event.preventDefault();
        }
    });
    $scope.tour_modal = null;
    $scope.title_color = "";
    $scope.subtitle_color = "";
    $scope.show_registration_slide = 0;
    $scope.show_auth_button = 0;
    $scope.tour_header = '';
    $scope.tour_slide_1 = '';
    $scope.tour_slide_2 = '';
    $scope.tour_slide_3 = '';
    $scope.tour_slide_4 = '';
    $scope.tour_slide_5 = '';
    $scope.tour_title_1 = '';
    $scope.tour_title_2 = '';
    $scope.tour_title_3 = '';
    $scope.tour_title_4 = '';
    $scope.tour_title_5 = '';
    $scope.tour_subtitle_1 = '';
    $scope.tour_subtitle_2 = '';
    $scope.tour_subtitle_3 = '';
    $scope.tour_subtitle_4 = '';
    $scope.tour_subtitle_5 = '';
    $scope.lastslide = '';
    if (!Customer.isLoggedIn()) $scope.show_auth_button = 1;
    $scope.goHome = function() {
        $timeout(function() {
            $state.go('home');
        }, 50);
    };
    $scope.close_tour_modal = function() {
        $scope.lastslide = 1;
        $scope.goHome();
    };
    $scope.openLoginForm = function() {
        Customer.display_account_form = false;
        Customer.loginModal($scope, function() {
        });        
    };
    $rootScope.$on(AUTH_EVENTS.loginSuccess, function() {
        $scope.show_auth_button = 0;
    });
    $rootScope.$on(AUTH_EVENTS.logoutSuccess, function() {
        $scope.show_auth_button = 1;
    });
    $http({
        method: 'GET',
        url: Url.get('application/mobile_data/gettoursettings'),
        cache: true,
        responseType:'json'
    }).success(function(data) {
        $scope.data = data;
        $scope.tour_header = data.tour_header;
        $scope.tour_tbs = data.tour_tbs;
        $scope.tour_lbt = data.tour_lbt;
        $scope.tour_tbc = data.tour_tbc;
        $scope.tour_slide_1 = data.tour_slide_1;
        $scope.tour_slide_2 = data.tour_slide_2;
        $scope.tour_slide_3 = data.tour_slide_3;
        $scope.tour_slide_4 = data.tour_slide_4;
        $scope.tour_slide_5 = data.tour_slide_5;
        $scope.tour_title_1 = data.tour_title_1;
        $scope.tour_title_2 = data.tour_title_2;
        $scope.tour_title_3 = data.tour_title_3;
        $scope.tour_title_4 = data.tour_title_4;
        $scope.tour_title_5 = data.tour_title_5;
        $scope.tour_subtitle_1 = data.tour_subtitle_1;
        $scope.tour_subtitle_2 = data.tour_subtitle_2;
        $scope.tour_subtitle_3 = data.tour_subtitle_3;
        $scope.tour_subtitle_4 = data.tour_subtitle_4;
        $scope.tour_subtitle_5 = data.tour_subtitle_5;
        $scope.title_color = "--ion-color-"+data.tour_title_color;
        if (data.tour_title_color_type=="tint" || data.tour_title_color_type=="shade") $scope.title_color = $scope.title_color + "-" + data.tour_title_color_type;
        $scope.subtitle_color = "--ion-color-"+data.tour_subtitle_color;
        if (data.tour_subtitle_color_type=="tint" || data.tour_subtitle_color_type=="shade") $scope.subtitle_color = $scope.subtitle_color + "-" + data.tour_subtitle_color_type;
        
        $timeout(function() {
            $scope.myswiper = new Swiper('.swiper-container', {
              pagination: {
                el: '.swiper-pagination',
              },
            });
            $scope.myswiper.on('slideChange', function () {
                if ($scope.myswiper.isEnd) {
                    $timeout(function() {
                        $scope.lastslide = $scope.myswiper.activeIndex;
                    }, 50);
                    $scope.myswiper.allowSlideNext = false ;
                } else {
                    $scope.myswiper.allowSlideNext = true ;
                }
                if ($scope.myswiper.isBeginning) {
                    $scope.myswiper.allowSlidePrev = false ;
                } else {
                    $scope.myswiper.allowSlidePrev = true ;
                }
            });
            $scope.restartTour = function() {
                $scope.myswiper.slideTo(0, 0, false);
            };
        }, 200);
    });
});