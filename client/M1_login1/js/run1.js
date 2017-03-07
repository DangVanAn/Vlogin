var app = angular.module('myApp', ['dtrw.bcrypt']);
app.controller('myCtrl', function ($scope, bcrypt, $http) {

    var hash = bcrypt.hashSync("dangvanan14", 10);

    $scope.content = 'data';
    $scope.statuscode = 'status';
    $scope.statustext = "status";

    // $scope.submit = function () {
    //     $scope.content = "99999";
    // }

    $scope.login = function () {

        //$urll = 'http://localhost:8080/login11/api/robots/' + $scope.emaill + '/' + $scope.passwordd;

        $urll = 'http://localhost:8080/default/login';
        var cloneemaill1 = $scope.emaill;
        var clonepass1 = $scope.passwordd;
        $gift1 =
            {
                'email': cloneemaill1,
                'password': clonepass1
            };

        $http({
            method: 'POST',
            url: $urll,
            data: $gift1
        }).then(function successCallback(response) {
            $scope.content = response.data;
            $scope.statuscode = response.status;
            $scope.statustext = response.statusText;
        }, function errorCallback(response) {
            $scope.statustext = 'error';
        });

    }

    $scope.register = function () {

        var cloneemaill = $scope.emaill;
        var clonepass = $scope.passwordd;

        hash = bcrypt.hashSync(clonepass, 10);

        $gift =
            {
                'email': cloneemaill,
                'password': hash
            };

        //$scope.content = $gift;

        delete $http.defaults.headers.common.Authorization;
        $urll = 'http://localhost:8080/default/signup';

        $http({
            method: 'POST',
            url: $urll,
            data: $gift,
            contentType: "application/json; charset=utf-8",
            dataType: "json"
        }).then(function successCallback(response) {
            $scope.content = response.data;
            $scope.statuscode = response.status;
            $scope.statustext = response.statusText;
        }, function errorCallback(response) {
            $scope.statustext = 'error';
        });
    }


    $scope.logout = function () {

        var cloneemaill = $scope.emaill;
        var clonepass = $scope.passwordd;

        hash = bcrypt.hashSync(clonepass, 10);

        //$scope.content = $gift;

        delete $http.defaults.headers.common.Authorization;
        $urll = 'http://localhost:8080/default/logout';

        $http({
            method: 'GET',
            url: $urll,
        }).then(function successCallback(response) {
            $scope.content = response.data;
            $scope.statuscode = response.status;
            $scope.statustext = response.statusText;
        }, function errorCallback(response) {
            $scope.statustext = 'error';
        });
    }


});