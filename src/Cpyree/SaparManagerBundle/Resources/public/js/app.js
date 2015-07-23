/* App Module */
var saparManagerApp = angular.module('saparManagerApp', [
    'ngRoute',
    'saparManagerControllers'

]);
saparManagerApp.config(['$httpProvider', function($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}])
.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider.
            when('/dirs', {
                templateUrl: Routing.generate('cpyree_saparmanager_manager_partial',{'name':'list.html'}),
                controller: 'dirListCtl'
            }).
            otherwise({
                redirectTo: '/dirs'
            });
}]);
