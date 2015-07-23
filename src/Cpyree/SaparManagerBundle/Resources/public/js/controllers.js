/* Controllers */

var saparManagerControllers = angular.module('saparManagerControllers', []);

saparManagerControllers.controller('dirListCtl', ['$scope', '$http', function ($scope, $http) {

    $scope.listAction = function() {
        $http.get(Routing.generate('cpyree_saparmanager_manager_templisting'))
            .success(function (response) {
                $scope.dirs = response.data;
            });
    }
    $scope.detailAction = function(dir){
        $http.get(Routing.generate('cpyree_saparmanager_manager_dirdetail', {'name':dir.id}))
            .success(function(response) {
                $scope.files = response.data.files;
                $scope.cover = response.data.coverThumb ? response.data.coverThumb : "none.jpg";
                $scope.curDir = dir.fullName;

            });
    }

    $scope.getPlayer = function(file){
        return '<audio controls  preload="none"><source  src="' + $scope.getStreamUrl(file.id) + '" type="audio/mpeg"></audio>';
    }

    $scope.getStreamUrl = function(file){
        return Routing.generate('cpyree_saparmanager_manager_stream', {'file':file});
    }
    $scope.listAction();

}]);

