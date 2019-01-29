'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Common.UserFlag
 * @description
 * # JobFlagController
 * Controller of the getlancerApp
 */

angular.module('getlancerApp.Job.JobFlag')
.directive('jobViewFlag', function ($uibModal) {
        return {
            restrict: 'EA',
            replace: true,
            templateUrl: 'scripts/plugins/Job/JobFlag/views/default/job_view_flag.html',
            link: function postLink(scope, element, attr) {
                scope.flag = {};
                scope.FlagModel = function(isvalid) {
                    scope.modalInstance = $uibModal.open({
                        templateUrl: 'scripts/plugins/Job/JobFlag/views/default/job_report.html',
                        backdrop: 'true',
                        controller: 'JobFlagController'
                    });
                };
            }
        };
    });
     