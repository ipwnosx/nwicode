App.factory('Advanced', function ($http, Url) {
    var factory = {};

    factory.filename = null;

    factory.loadData = function () {
        return $http({
            method: 'GET',
            url: Url.get('backoffice/advanced_module/load'),
            cache: true,
            responseType: 'json'
        });
    };
    
    factory.deleteModule = function(module_name,module_version) {
        if(!module_name) return;
        search_keyword = encodeURIComponent(module_name);
        version = encodeURIComponent(module_version);
        return $http({
            method: 'GET',
            url: Url.get("backoffice/advanced_module/moduledelete", {modulename: search_keyword,moduleversion: version}),
            cache: false,
            responseType:'json'
        });
    };
    
    factory.clearCache = function () {
        return $http({
            method: 'GET',
            url: Url.get('backoffice/advanced_module/clearcache'),
            cache: true,
            responseType: 'json'
        });
    };
    
    factory.checkForUpdatesmodule = function() {

        return $http({
            method: 'GET',
            url: Url.get("installer/backoffice_module/checkforupdatesmodule"),
            cache: false,
            responseType:'json'
        });

    };

    factory.checkPermissions = function() {

        return $http({
            method: 'GET',
            url: Url.get("installer/backoffice_module/checkpermissions", {file: factory.filename}),
            cache: false,
            responseType:'json'
        });

    };

    factory.copy = function() {

        return $http({
            method: 'GET',
            url: Url.get("installer/backoffice_module/copy", {file: factory.filename}),
            cache: false,
            responseType:'json'
        });
    };

    factory.downloadUpdatemodule = function(moduleUpdatepathurl, moduleVersion, moduleName) {

        return $http({
            method: 'POST',
            url: Url.get("installer/backoffice_module/downloadupdatemodule"),
            cache: false,
            data: {
                moduleupdatepathurl: moduleUpdatepathurl,
                moduleversion: moduleVersion,
                modulename: moduleName
            },
            responseType:'json'
        });

    };

    factory.findAll = function () {
        return $http({
            method: 'GET',
            url: Url.get('backoffice/advanced_module/findall'),
            cache: false,
            responseType: 'json'
        });
    };

    factory.moduleAction = function (module, action) {
        return $http({
            method: 'POST',
            url: Url.get('backoffice/advanced_module/execute'),
            data: {
                module: module,
                action: action
            },
            cache: false,
            responseType: 'json'
        });
    };

    /**
     * Toggle a feature enable state
     *
     * @param featureId
     * @param isEnabled
     * @returns {*}
     */
    factory.toggleFeature = function (featureId, isEnabled) {
        return $http({
            method: 'POST',
            url: Url.get('backoffice/advanced_module/togglefeature'),
            data: {
                featureId: featureId,
                isEnabled: isEnabled
            },
            cache: false,
            responseType: 'json'
        });
    };

    factory.install = function() {

        return $http({
            method: 'GET',
            url: Url.get("installer/backoffice_module/install"),
            cache: false,
            responseType:'json'
        });
    };

    return factory;
});
