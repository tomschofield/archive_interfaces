'use strict';
// define(['angular', 'modules/demo/index', 'common/directives'], function(angular) {
//   "use strict";

var angControl  = angular.module('angControl', [
	'ngSanitize',
	'ngAnimate',
	'ngRoute',
  'ngTouch',
  'ngMouseDrag',
	'angControllers']);


angControl.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/grid', {
        templateUrl: 'partials/grid.html',
        controller: 'ListCtrl'
      }).
      when('/grid/:title', {
        templateUrl: 'partials/grid-detail.html',
        controller: 'GridDetailCtrl'
      }).
      otherwise({
        redirectTo: '/grid'
      });
  }]);
// });