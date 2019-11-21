/*global
    App, BASE_URL, Label, angular
 */
App.config(function($routeProvider) {

    $routeProvider.when(BASE_URL+"/backoffice/advanced_module", {
        controller: 'BackofficeAdvancedController',
        templateUrl: BASE_URL+"/backoffice/advanced_module/template"
    }).when(BASE_URL+"/backoffice/advanced_configuration", {
        controller: 'BackofficeAdvancedConfigurationController',
        templateUrl: BASE_URL+"/backoffice/advanced_configuration/template"
    }).when(BASE_URL+"/backoffice/advanced_tools", {
        controller: 'BackofficeAdvancedToolsController',
        templateUrl: BASE_URL+"/backoffice/advanced_tools/template"
    }).when(BASE_URL+"/backoffice/advanced_cron", {
        controller: 'BackofficeAdvancedCronController',
        templateUrl: BASE_URL+"/backoffice/advanced_cron/template"
    });

}).controller("BackofficeAdvancedController", function($log, $scope, $interval, Header, Advanced, FileUploader, Url, Backoffice, $timeout, Label) {

    $scope.header = new Header();
    $scope.header.button.left.is_visible = false;
    $scope.header.loader_is_visible = false;
    $scope.content_loader_is_visible = true;
    $scope.show_release_note = false;
    $scope.detetion_log = true;
    $scope.show_only = false;
    $scope.installmodule = "";
    $scope.checking_module = false;
    $scope.installation_progress = 0;
    $scope.package_full = null;
    $scope.words = null;
    $scope.version = null;
    
    $scope.package_details = {
        is_visible: false,
        name: null,
        version: null,
        description: null
    };
    
    $scope.check_for_updates = {
        check: true,
        download: false,
        next_version: null,
        loader_is_visible: false
    };
    
    $scope.permissions = {
        is_visible: false,
        progress: 0,
        interval_id: null,
        running: false,
        success: false,
        error: false,
        error_message: null
    };
    
    $scope.installation = {

        copy: {
            is_visible: false,
            progress: 0,
            interval_id: null,
            running: false,
            success: false,
            error: false
        },
        install: {
            is_visible: false,
            progress: 0,
            interval_id: null,
            running: false,
            success: false,
            error: false
        }
    };

    $scope.uploader = new FileUploader({
        url: Url.get('installer/backoffice_module/upload')
    });

    $scope.uploader.filters.push({
        name: 'limit',
        fn: function (item, options) {
            return this.queue.length < 1;
        }
    });
    
    Advanced.loadData().success(function(data) {
        $scope.header.title = data.title;
        $scope.header.icon = data.icon;
        base_url = data.base_url;
        $scope.words = data.words;
        $scope.version = data.version;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });

    $scope.content_loader_is_visible = true;
    Advanced.findAll().success(function(data) {
        $scope.modules = data.modules;
        $scope.core_modules = data.core_modules;
        $scope.layouts = data.layouts;
        $scope.templates = data.templates;
        $scope.features = data.features;
        $scope.icons = data.icons;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });
    
    
    
    $scope.clearCache = function () {
        $scope.content_loader_is_visible = true;
        Advanced.clearCache().success(function (data) {
            $scope.message.setText(data.message)
            .isError(false)
            .show();
        }).finally(function () {
            $scope.content_loader_is_visible = false;
            $timeout(function () {
                location.reload();
            }, 1500);
        });
    };

    $scope.deleteModule = function (module_name, module_version) {
        swal({
            html: true,
            title: $scope.words.deleteTitle,
            type: 'prompt',
            text: $scope.words.deleteMessage.replace(/#module_version#/g, module_version),
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            confirmButtonColor: '#ff3a2e',
            confirmButtonText: $scope.words.confirmDelete,
            cancelButtonText: $scope.words.cancelDelete,
            buttons: {
                confirm: {
                    value: ''
                }
            }
        }, function (value) {
            if (value == module_version) {
                    $scope.content_loader_is_visible = true;
                    Advanced.deleteModule(module_name, module_version).success(function (data) {
                        message = data.message;
                        $scope.message.setText(message)
                            .isError(false)
                            .show();
                    }).error(function (data) {
                        $scope.message.setText(message)
                            .isError(true)
                            .show();
                    }).finally(function () {
                        $scope.content_loader_is_visible = false;
                        $scope.clearCache();
                    });
                swal.close();
                return true;
            }else{
                
                swal.showInputError("<code>The entered text mismatch</code>");
            }
        });
        
    };

    $scope.moduleAction = function (module, action) {
        $scope.form_loader_is_visible = true;

        Advanced.moduleAction(module, action).success(function (data) {
            var message = '';
            if (angular.isObject(data) && angular.isDefined(data.message)) {
                message = data.message;
                $scope.message.isError(false);
            }

            $scope.message.setText(message)
                .show()
            ;
        }).error(function (data) {
            var message = '';
            if (angular.isObject(data) && angular.isDefined(data.message)) {
                message = data.message;
            }

            $scope.message.setText(message)
                .isError(true)
                .show()
            ;
        }).finally(function () {
            $scope.form_loader_is_visible = false;
        });
    };
    
    $scope.checkForUpdatesmodule = function () {
        $scope.check_for_updates.loader_is_visible = true;

        Advanced.checkForUpdatesmodule().success(function(data) {
            $scope.check_for_updates.check = false;
            if(data.url) {
                $scope.check_for_updates.next_version = data.version;
                $scope.check_for_updates.download = true;
            } else if(data.message) {
                $scope.message.setText(data.message)
                    .isError(false)
                    .show()
                ;
                $scope.check_for_updates.no_updates_available = true;
            }

        }).error(function(data) {
            $scope.message.setText(data.message)
                .isError(true)
                .show()
            ;
        }).finally(function() {
            $scope.check_for_updates.loader_is_visible = false;
        });
    };

    $scope.downloadUpdatemodule = function (moduleUpdatepathurl, moduleVersion, moduleName) {
        $scope.check_for_updates.loader_is_visible = true;
        Advanced.downloadUpdatemodule(moduleUpdatepathurl, moduleVersion, moduleName).success(function(data) {
            $scope.package_full = data;

            if (data.filename) {
                $scope.package_details = data.package_details;
                $scope.showPackageDetails();
                $scope.installmodule = moduleName;
                Advanced.filename = data.filename;
                $scope.check_for_updates.check = false;
            } else if(data.message) {
                $scope.message.setText(data.message)
                    .isError(false)
                    .show()
                ;
                $scope.check_for_updates.no_updates_available = true;
            } else {
                $scope.message.setText(Label.uploader.error.general)
                    .isError(true)
                    .show()
                ;
            }
        }).error(function(data) {
            $scope.message.setText(data.message)
                .isError(true)
                .show()
            ;
        }).finally(function() {
            $scope.check_for_updates.loader_is_visible = false;
        });
    };

    $scope.close = function () {
        $scope.show_only = false;
        $scope.show_release_note = false;
    };

    $scope.dismissInstall = function () {
        $scope.show_release_note = false;
    };
    
    $scope.confirmInstall = function () {
        var willContinue = true;
        try {
            if ($scope.package_full.release_note.is_major !== undefined &&
                $scope.package_full.release_note.is_major) {
                $scope.show_release_note = false;
                willContinue = $scope.confirmMajorUpdate(function () {
                    $scope.checkPermissions();
                });
            }
        } catch (e) {
            // Nope!
            console.error('Caught error.', e.message);
            $scope.show_release_note = true;
        }

        if (!willContinue) {
            console.info('Aborted installation.');
            return;
        }

        $scope.show_release_note = false;
        $scope.checkPermissions();
    };

    $scope.showReleasenote = function (show_only) {
        if (typeof show_only !== 'undefined') {
            $scope.show_release_note = true;
            $scope.show_only = true;
            document.getElementById('informations').src = $scope.package_full.release_note.url;
        } else if ($scope.package_full.release_note.show) {
            $scope.show_only = false;
            $scope.show_release_note = true;
            document.getElementById('informations').src = $scope.package_full.release_note.url;
        } else {
            $scope.checkPermissions();
        }
    };

    $scope.showPackageDetails = function () {
        $scope.package_details.is_visible = true;
    };

    $scope.checkPermissions = function () {
        $scope.permissions.is_visible = true;
        $scope.permissions.running = true;
        $scope.permissions.success = false;
        $scope.permissions.error = false;
        $scope.permissions.error_message = null;
        $scope.permissions.progress = 0;

        $scope.permissions.interval_id = $interval(function () {
            $scope.permissions.progress += 5;
        }, 500, 18);

        Advanced.checkPermissions().success(function (data) {
            if (angular.isObject(data) && data.success) {

                $scope.permissions.success = true;
                $scope.showModuleInstallation();
                $scope.copyModule();

            } else {
                $scope.message.setText(Label.uploader.error.general)
                    .isError(true)
                    .show();

                $scope.permissions.error = true;
            }
        }).error(function (data) {
            if (!data || !data.message) {
                // Seems we got an issue, try to rebuild manifest once
                Backoffice.clearCache("app_manifest")
                .success(function (data) {
                    $scope.content_loader_is_visible = true;
                    $scope.message.setText(data.message)
                        .isError(false)
                        .show()
                    ;
                }).finally(function () {
                    $scope.installation.install.running = false;
                    $timeout(function () {
                        location.reload();
                    }, 1500);
                });
            } else {
                $scope.permissions.error_message = data.message;
                $scope.permissions.error = true;
            }
        }).finally(function () {
            $interval.cancel($scope.permissions.interval_id);
            $scope.permissions.running = false;
            $scope.permissions.progress = 100;
        });
    };

    $scope.increaseProgressBar = function (state, step) {
        step = (typeof step === 'undefined') ? 5 : step;
        $scope.installation[state].progress += step;
    };

    $scope.copyModule = function () {
        if (!Advanced.filename) {
            $scope.message
                .setText('An error occurred while trying to install the module. Please, reload the page and try again.')
                .isError(true)
                .show();
            return;
        }

        $scope.installation.copy.progress = 0;
        $scope.installation.copy.success = false;
        $scope.installation.copy.error = false;
        $scope.installation.copy.running = true;

        $scope.installation.copy.interval_id = $interval(function () {
            $scope.increaseProgressBar('copy');
        }, 500, 18);

        Advanced.copy().success(function (data) {
            if (angular.isObject(data) && data.success) {
                $scope.installation.copy.success = true;
                $scope.installModule();
            } else {
                $scope.message
                    .setText(Label.uploader.error.general)
                    .isError(true)
                    .show();

                $scope.installation.copy.error = true;
            }
        }).error(function (data) {
            $scope.message
                .setText()
                .isError(true)
                .show();

            $scope.installation.copy.error = true;
        }).finally(function () {
            $interval.cancel($scope.installation.copy.interval_id);
            $scope.installation.copy.running = false;
            $scope.installation.copy.progress = 100;
        });

    };

    $scope.installRetry = 0;
    $scope.installPoller = function () {
        Advanced.install().success(function (data) {
            if ($scope.installRetry < 5 &&
                angular.isObject(data) &&
                data.success && angular.isDefined(data.reached_timeout)) {
                // Continue update, recall itself!
                $scope.installPoller();
                $scope.installRetry += 1;
            } else if (angular.isObject(data) && data.success) {
                $scope.message
                    .setText(data.message)
                    .isError(false)
                    .show();

                $scope.installation.install.success = true;
                $scope.content_loader_is_visible = true;

                if (($scope.package_details !== undefined) &&
                    ($scope.package_details.restore_apps !== undefined) &&
                    ($scope.package_details.restore_apps === true)) {
                    AdvancedTools.restoreapps()
                        .finally(function () {
                            Backoffice.clearCache('app_manifest')
                                .success(function (manifestData) {
                                    $scope.content_loader_is_visible = true;
                                    $scope.message.setText(manifestData.message)
                                        .isError(false)
                                        .show()
                                    ;
                                }).finally(function () {
                                    $scope.installation.install.running = false;
                                    $timeout(function () {
                                        location.reload();
                                    }, 1500);
                                });
                        });
                } else {
                    Backoffice.clearCache('app_manifest')
                        .success(function (data) {
                            $scope.content_loader_is_visible = true;
                            $scope.message.setText(data.message)
                                .isError(false)
                                .show()
                            ;
                        }).finally(function () {
                            $scope.installation.install.running = false;
                            $timeout(function () {
                                location.reload();
                            }, 1500);
                        });
                }
            } else {
                $scope.message
                    .setText(Label.uploader.error.general)
                    .isError(true)
                    .show();

                $scope.installation.install.error = true;
            }
        }).error(function (data) {
            var message = ((data !== undefined) && (data.message !== undefined)) ?
                data.message : 'An unknown error occurred.';

            $scope.message
                .setText(message)
                .isError(true)
                .show();

            $scope.installation.install.error = true;
        }).finally(function () {
            $interval.cancel($scope.installation.install.interval_id);
            $scope.content_loader_is_visible = true;
            $scope.installation.install.running = false;
            $scope.installation.install.progress = 100;
        });
    };

    $scope.installModule = function () {
        $scope.installRetry = 0;
        $scope.installation.install.is_visible = true;
        $scope.installation.install.progress = 0;
        $scope.installation.install.success = false;
        $scope.installation.install.error = false;
        $scope.installation.install.running = true;

        $interval(function() {
            $scope.increaseProgressBar('install');
        }, 500, 1);

        $scope.installation.install.interval_id = $interval(function () {
            $scope.increaseProgressBar('install', 1);
        }, 500, 90);

        /** Installation poller with multiple retries. */
        $scope.installPoller();
    };

    $scope.showModuleInstallation = function () {
        $scope.installation.copy.is_visible = true;
    };

    $scope.toggleLoader = function () {
        $scope.header.loader_is_visible = !$scope.header.loader_is_visible;
    };

    $scope.confirmMajorUpdate = function (nextAction) {
        $scope.confirmKey = $scope.words.confirmKey;
        var majorMessage = $scope.words.majorMessage
            .replace(/#VERSION#/gmi, $scope.package_details.version);

        swal({
            html: true,
            title: $scope.words.titleMajor,
            type: 'prompt',
            text: majorMessage,
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            confirmButtonColor: '#ff3a2e',
            confirmButtonText: $scope.words.confirmDelete,
            cancelButtonText: $scope.words.cancelDelete,
            buttons: {
                confirm: {
                    value: ''
                }
            }
        }, function (value) {
            if (value === $scope.confirmKey.replace(/#VERSION#/gmi, $scope.package_details.version)) {
                swal.close();
                nextAction();
                return true;
            }

            swal.showInputError('<code>' + $scope.words.mismatch + '</code>');
            return false;
        });
    };

    $scope.toggleFeature = function (feature) {
        $scope.form_loader_is_visible = true;
        Advanced
            .toggleFeature(feature.id, feature.is_enabled)
            .success(function (data) {
                var message = '';
                if (angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                    $scope.message.isError(false);
                }

                $scope.message.setText(message)
                    .show()
                ;
            }).error(function (data) {
                var message = '';
                if (angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                }

                $scope.message.setText(message)
                    .isError(true)
                    .show()
                ;
            }).finally(function () {
                $scope.form_loader_is_visible = false;
            });
    };
}).controller("BackofficeAdvancedConfigurationController", function($log, $http, $scope, $timeout, $interval, $window,
                                                                    Label, Header, AdvancedConfiguration, FileUploader,
                                                                    Url, AdvancedTools) {

    $scope.header = new Header();
    $scope.header.button.left.is_visible = false;
    $scope.header.loader_is_visible = false;
    $scope.content_loader_is_visible = true;
    $scope.hostname = "";
    $scope.show_upload = false;
    $scope.report = {
        message: ""
    };

    AdvancedConfiguration.loadData().success(function(data) {
        $scope.header.title = data.title;
        $scope.header.icon = data.icon;
        $scope.hostname = data.hostname;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });

    $scope.content_loader_is_visible = true;
    AdvancedConfiguration.findAll().success(function(data) {
        $scope.configs = data;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });

    $scope.save = function() {

        $scope.form_loader_is_visible = true;

        AdvancedConfiguration.save($scope.configs).success(function(data) {

            $scope.message.onSuccess(data);

        }).error(function(data) {

            $scope.message.onError(data);

        }).finally(function() {
            $scope.form_loader_is_visible = false;

            $timeout(function() {
                location.reload();
            }, 3000);
        });
    };

    $scope.show_force = false;

    $scope.all_messages = null;

    $scope.submitReport = function() {

        AdvancedConfiguration.submitReport($scope.report.message)
            .success(function() {
                $scope.report.message = "";
                $window.alert("Thanks for your report.");
            })
            .error(function() {
                $window.alert("An error occurred while submit your report, please try again.");
            });

    };

    $scope.testSsl = function() {
        $scope.content_loader_is_visible = true;

        AdvancedConfiguration
            .testSsl()
            .success(function(data) {
                $scope.message.onSuccess(data);
                $scope.configs.testssl = data;
            })
            .error(function(data) {
                $scope.message.onError(data);
                $scope.configs.testssl = data;
            })
            .finally(function() {
                $scope.content_loader_is_visible = false;
            });
    };

    $scope.generateSsl = function(hostname, force) {

        if((/^https/i).test($window.location.protocol)) {
            return $window.alert("You must run request from HTTP.");
        }

        if(!$window.confirm("Are you sure ?")) {
            return;
        }

        $scope.content_loader_is_visible = true;

        AdvancedConfiguration.save($scope.configs).success(function(data) {

            $scope.message.onSuccess(data);

            /** When setting are ok, go for SSL */
            AdvancedConfiguration.generateSsl($scope.configs.current_domain, force).success(function(data) {

                /** Now if it's ok, it's time for Panel  */
                $scope.message.onSuccess(data);

                $scope.all_messages = data.all_messages;

                $log.info("SSL Ok, time to push to panel.");

                if($scope.configs.cpanel_type.value == "plesk") {
                    /** Plesk is tricky, if you remove the old certificate, it' reloading ... */
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/clearplesk/hostname/'+$scope.configs.current_domain,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.message.onUnknown(response.data);
                        $scope.pollerRemovePlesk();
                    }, function (response) {
                        $scope.message.onUnknown(response.data);
                        $scope.pollerRemovePlesk();
                    });
                } else if($scope.configs.cpanel_type.value == "self") {
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/sendtopanel/hostname/'+$scope.configs.current_domain,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.poller('backoffice/advanced_configuration/checkhttp');
                    }, function (response) {
                        $scope.poller('backoffice/advanced_configuration/checkhttp');
                    });
                } else {
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/sendtopanel/hostname/'+$scope.configs.current_domain,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.poller('backoffice/advanced_configuration/checkssl');
                    }, function (response) {
                        $scope.poller('backoffice/advanced_configuration/checkssl');
                    });
                }

            }).error(function(data) {

                $scope.message.onError(data);
                $scope.content_loader_is_visible = false;

            }).finally(function() {});


        }).error(function(data) {

            $scope.message.onError(data);

        }).finally(function() {

        });

        return false;
    };

    $scope.uploadToPanel = function(hostname) {

        if((/^https/i).test($window.location.protocol)) {
            return $window.alert("You must run upload from HTTP.");
        }

        if(!$window.confirm("Are you sure ?")) {
            return;
        }

        $scope.content_loader_is_visible = true;

        AdvancedConfiguration.save($scope.configs)
            .success(function(data) {

                $scope.message.onSuccess(data);

                $log.info("SSL Ok, time to push to panel.");

                if($scope.configs.cpanel_type.value == "plesk") {
                    /** Plesk is tricky, if you remove the old certificate, it' reloading ... */
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/clearplesk/hostname/'+hostname,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.message.onUnknown(response.data);
                        $scope.pollerRemovePlesk();
                    }, function (response) {
                        $scope.message.onUnknown(response.data);
                        $scope.pollerRemovePlesk();
                    });
                } else if($scope.configs.cpanel_type.value == "self") {
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/sendtopanel/hostname/'+hostname,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.poller('backoffice/advanced_configuration/checkhttp');
                    }, function (response) {
                        $scope.poller('backoffice/advanced_configuration/checkhttp');
                    });
                } else {
                    $http({
                        method: 'GET',
                        url: 'backoffice/advanced_configuration/sendtopanel/hostname/'+hostname,
                        cache: false,
                        responseType:'json'
                    }).then(function (response) {
                        // This may never occurs but well .. :)
                        $scope.poller('backoffice/advanced_configuration/checkssl');
                    }, function (response) {
                        $scope.poller('backoffice/advanced_configuration/checkssl');
                    });
                }

            }).error(function(data) {

                $scope.message.onError(data);
            }).finally(function() {

                $scope.content_loader_is_visible = true;
            });

        return false;
    };

    $scope.poller = function(url) {
        var times = 0;
        var poller = $interval(function() {

            /** We hit the timeout, show an error */
            if(times++ > 10) {
                times = 0;
                $interval.cancel(poller);
                poller = undefined;

                $log.info("#01-Error: timeout reloading panel.");
                $scope.message.information($scope.all_messages.https_unreachable);
                $scope.content_loader_is_visible = false;
            }

            $log.info("#02-Retrying: n"+times+" poll.");

            $http({
                method: 'GET',
                url: url,
                cache: false,
                responseType:'json'
            }).then(function successCallback(response) {
                /** Clear poller on success */
                $interval.cancel(poller);
                poller = undefined;

                /** Try to get HTTPS for redirect. */
                if(typeof response.data.https_url != "undefined") {
                    $log.info('typeof response.data.https_url != "undefined"');
                    location = response.data.https_url+"/backoffice/advanced_configuration";
                } else {
                    $log.info('location.reload()');
                    location.reload();
                }

            }, function errorCallback(response) {
                $log.info("#03-Retry: not reachable yet.");
            });

            /**.Showing wait message */
            $scope.message.information($scope.all_messages.polling_reload);
        }, 3000);
    };

    $scope.pollerRemovePlesk = function() {
        var times = 0;
        var poller = $interval(function() {

            /** We hit the timeout, show an error */
            if(times++ > 10) {
                times = 0;
                $interval.cancel(poller);
                poller = undefined;

                $log.info("#01-Error: timeout reloading panel.");
                $scope.message.information($scope.all_messages.https_unreachable);
                $scope.content_loader_is_visible = false;
            }

            $log.info("#02-Retrying: n"+times+" poll.");

            $http({
                method: 'GET',
                url: 'backoffice/advanced_configuration/checkhttp',
                cache: false,
                responseType:'json'
            }).then(function (response) {
                /** Clear poller on success */
                $interval.cancel(poller);
                poller = undefined;

                /** Now it's ok, do the same as without plesk */
                $http({
                    method: 'GET',
                    url: 'backoffice/advanced_configuration/installplesk',
                    cache: false,
                    responseType:'json'
                }).then(function (response) {
                    // This may never occurs but well .. :)
                    if(angular.isObject(response.data) && angular.isDefined(response.data.error)) {
                        // Abort
                        $scope.message.onUnknown(response.data);
                    } else {
                        $scope.pollerInstallPlesk();
                    }
                }, function errorCallback(response) {
                    if(angular.isObject(response.data) && angular.isDefined(response.data.error)) {
                        // Abort
                        $scope.message.onUnknown(response.data);
                    } else {
                        $scope.pollerInstallPlesk();
                    }
                    $scope.pollerInstallPlesk();
                });

            }, function (response) {
                $log.info("#03-Retry: not reachable yet.");
            });

            /**.Showing wait message */
            $scope.message.information($scope.all_messages.polling_reload);
        }, 3000);
    };

    $scope.pollerInstallPlesk = function() {
        var times = 0;
        var poller = $interval(function() {

            /** We hit the timeout, show an error */
            if(times++ > 10) {
                times = 0;
                $interval.cancel(poller);
                poller = undefined;

                $log.info("#01-Error: timeout reloading panel.");
                $scope.message.information($scope.all_messages.https_unreachable);
                $scope.content_loader_is_visible = false;
            }

            $log.info("#02-Retrying: n"+times+" poll.");

            $http({
                method: 'GET',
                url: 'backoffice/advanced_configuration/checkhttp',
                cache: false,
                responseType:'json'
            }).then(function successCallback(response) {
                /** Clear poller on success */
                $interval.cancel(poller);
                poller = undefined;

                /** Now it's ok, do the same as without plesk */
                $http({
                    method: 'GET',
                    url: 'backoffice/advanced_configuration/sendtopanel',
                    cache: false,
                    responseType:'json'
                }).then(function (response) {
                    // This may never occurs but well .. :)
                    $scope.poller('backoffice/advanced_configuration/checkssl');
                }, function (response) {
                    $scope.poller('backoffice/advanced_configuration/checkssl');
                });

            }, function (response) {
                $log.info("#03-Retry: not reachable yet.");
            });

            /**.Showing wait message */
            $scope.message.information($scope.all_messages.polling_reload);
        }, 3000);
    };

    $scope.form = {
        hostname: "",
        cert_path: "",
        ca_path: "",
        private_path: "",
        fullchain_path: "",
        upload: "0"
    };

    $scope.uploaders = [
        {type : "cert_path",    uploader : "cert_path"},
        {type : "ca_path",      uploader : "ca_path"},
        {type : "private_path", uploader : "private_path"},
        {type : "fullchain_path", uploader : "fullchain_path"}
    ];

    for (var i = 0; i < $scope.uploaders.length; i++) {
        var code = $scope.uploaders[i].uploader;
        $scope[code] = new FileUploader({
            url: Url.get("backoffice/advanced_configuration/uploadcertificate?code="+code)
        });

        $scope[code].filters.push({
            name: 'limit',
            fn: function(item, options) {
                return this.queue.length < 1;
            }
        });

        $scope[code].onWhenAddingFileFailed = function(item, filter, options) {
            if(filter.name == "limit") {
                $scope.message.setText(Label.uploader.error.only_one_at_a_time).isError(true).show();
            }
        };

        $scope[code].onAfterAddingFile = function(item, filter, options) {
            item.upload();
        };

        $scope[code].onSuccessItem = function(fileItem, response, status, headers) {
            if(angular.isObject(response) && response.success) {

                $scope.form[response.code] = response.tmp_path;
                $log.info($scope.form);

            } else {
                $scope.message.setText(Label.uploader.error.general)
                    .isError(true)
                    .show()
                ;
            }
        };

        $scope[code].onErrorItem = function(fileItem, response, status, headers) {
            $scope.message.setText(response.message)
                .isError(true)
                .show()
            ;
        };
    }

    $scope.disable_form = false;
    $scope.createCertificate = function() {
        if(!$scope.disable_form) {
            $scope.disable_form = true;

            AdvancedConfiguration.createCertificate($scope.form).success(function(data) {

                var message = Label.save.error;
                if(angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                    $scope.message.isError(false);
                } else {
                    $scope.message.isError(true);
                }
                $scope.message.setText(message)
                    .show()
                ;

                $scope.configs.certificates = data.certificates;

                $scope.form = {
                    hostname: "",
                    cert_path: "",
                    ca_path: "",
                    private_path: "",
                    fullchain_path: "",
                    upload: "0"
                };

            }).error(function(data) {

                var message = Label.save.error;
                if(angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                }

                $scope.message.setText(message)
                    .isError(true)
                    .show()
                ;

            }).finally(function() {
                $scope.form_loader_is_visible = false;
                $scope.disable_form = false;
            });
        }
    };

    $scope.removeCertificate = function(confirm, id) {
        if(window.confirm(confirm)) {
            AdvancedConfiguration.removeCertificate(id).success(function(data) {

                var message = Label.save.error;
                if(angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                    $scope.message.isError(false);
                } else {
                    $scope.message.isError(true);
                }
                $scope.message.setText(message)
                    .show()
                ;

                $scope.configs.certificates = data.certificates;

            }).error(function(data) {

                var message = Label.save.error;
                if(angular.isObject(data) && angular.isDefined(data.message)) {
                    message = data.message;
                }

                $scope.message.setText(message)
                    .isError(true)
                    .show()
                ;

            }).finally(function() {
                $scope.form_loader_is_visible = false;
            });
        }
    };

    $scope.migrate_sessions = function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (!window.confirm('You are about to migrate all MySQL to the configured Redis server, all existing sessions in Redis will be replaced, are you sure ?')) {
            return;
    }
        $scope.content_loader_is_visible = true;
        AdvancedTools.migrateSessions()
            .success(function (data) {
                $scope.message.setText(data.message)
                    .isError(false)
                    .show();
            })
            .error(function (errorData) {
                $scope.message
                    .setText(errorData.message)
                    .isError(true)
                    .show();
                $scope.content_loader_is_visible = false;
            })
            .finally(function () {
                $scope.content_loader_is_visible = false;
            });
    };



}).controller("BackofficeAdvancedToolsController", function($log, $scope, $interval, Header, AdvancedTools, Backoffice) {

    $scope.header = new Header();
    $scope.header.button.left.is_visible = false;
    $scope.header.loader_is_visible = false;
    $scope.content_loader_is_visible = true;

    AdvancedTools.loadData().success(function(data) {
        $scope.header.title = data.title;
        $scope.header.icon = data.icon;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });

    $scope.content_loader_is_visible = true;

    $scope.test_integrity = function() {
        $scope.content_loader_is_visible = true;
        AdvancedTools.runtest()
            .success(function(data) {
                $scope.integrity_result = data;
            }).finally(function() {

            $scope.content_loader_is_visible = false;
        });
    };

    $scope.restore_apps = function() {

        if (!window.confirm('You are about to restore apps sources, are you sure ?')) {
            return;
        }

        $scope.content_loader_is_visible = true;
        AdvancedTools.restoreapps()
            .success(function(data) {
                Backoffice.clearCache('app_manifest')
                    .success(function (manifestData) {
                        $scope.message.setText(manifestData.message)
                            .isError(false)
                            .show()
                        ;
                    }).finally(function() {
                        $scope.content_loader_is_visible = false;
                    });
            })
            .error(function (errorData) {
                $scope.message
                    .setText(errorData.message)
                    .isError(false)
                    .show();
                $scope.content_loader_is_visible = false;
            })
            .finally(function () {
                $scope.content_loader_is_visible = false;
            });
    };

    $scope.migrate_sessions = function () {
        if (!window.confirm('You are about to migrate all MySQL to the configured Redis server, all existing sessions in Redis will be replaced, are you sure ?')) {
            return;
        }
        $scope.content_loader_is_visible = true;
        AdvancedTools.migrateSessions()
            .success(function (data) {
                $scope.message.setText(data.message)
                    .isError(false)
                    .show();
            })
            .error(function (errorData) {
                $scope.message
                    .setText(errorData.message)
                    .isError(true)
                    .show();
                $scope.content_loader_is_visible = false;
            })
            .finally(function () {
                $scope.content_loader_is_visible = false;
            });
    };

}).controller("BackofficeAdvancedCronController", function($log, $scope, $interval, $timeout, Backoffice, Header,
                                                           AdvancedConfiguration, AdvancedCron) {

    $scope.header = new Header();
    $scope.header.button.left.is_visible = false;
    $scope.header.loader_is_visible = false;
    $scope.content_loader_is_visible = true;

    AdvancedCron.loadData().success(function(data) {
        $scope.header.title = data.title;
        $scope.header.icon = data.icon;
    }).finally(function() {
        $scope.content_loader_is_visible = false;
    });

    $scope.content_loader_is_visible = true;

    AdvancedConfiguration.findAll().success(function(data) {
        $scope.configs = data;
    }).finally(function() {});

    $scope.loadContent = function () {
        AdvancedCron.findAll().success(function(data) {
            $scope.system_tasks = data.system_tasks;
            $scope.tasks = data.tasks;
            $scope.apk_queue = data.apk_queue;
            $scope.source_queue = data.source_queue;
        }).finally(function() {
            $scope.content_loader_is_visible = false;
        });
    };

    $scope.loadContent();

    $scope.androidSdkRestart = function() {
        $scope.content_loader_is_visible = true;
        Backoffice.clearCache("android_sdk").success(function (data) {
            $scope.message.setText(data.message)
                .isError(false)
                .show()
            ;
            $scope.content_loader_is_visible = false;
        });
    };

    $scope.restartApk = function (queueId) {
        $scope.content_loader_is_visible = true;
        AdvancedCron
        .restartApk(queueId)
        .success(function (data) {
            $scope.message.setText(data.message)
            .isError(false)
            .show()
            ;
            $scope.content_loader_is_visible = false;
            $scope.loadContent();
        });
    };

    $scope.save = function() {

        $scope.form_loader_is_visible = true;

        AdvancedConfiguration.save($scope.configs).success(function(data) {

            var message = Label.save.error;
            if(angular.isObject(data) && angular.isDefined(data.message)) {
                message = data.message;
                $scope.message.isError(false);
            } else {
                $scope.message.isError(true);
            }
            $scope.message.setText(message)
                .show()
            ;
        }).error(function(data) {
            var message = Label.save.error;
            if(angular.isObject(data) && angular.isDefined(data.message)) {
                message = data.message;
            }

            $scope.message.setText(message)
                .isError(true)
                .show()
            ;
        }).finally(function() {
            $scope.form_loader_is_visible = false;

            $timeout(function() {
                location.reload();
            }, 500);
        });
    };

});
