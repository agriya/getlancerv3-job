'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Job.controller:JobsAppliedController
 * @description
 * # JobsAppliedController
 * Controller of  the getlancerApp.Job
 */
angular.module('getlancerApp.Job')
    .controller('JobsAppliedController', ['$scope', '$rootScope', '$window', '$filter', '$state', 'jobAppliedFactory', 'JobSalaryTypeFactory', '$timeout', 'SalaryType', 'DateFormat', 'md5', function($scope, $rootScope, $window, $filter, $state, jobAppliedFactory, JobSalaryTypeFactory, $timeout, SalaryType, DateFormat, md5) {
        /**
         * @ngdoc method
         * @name JobsAppliedController.init
         * @methodOf module.JobsAppliedController
         * @description
         * This method is used to init the function and variables
         */
        var params = [];

        function init() {
            var params = [];
            $scope.DateFormat = DateFormat;
            $scope.SalaryType = SalaryType;
            $scope.myjobs = null;
            // $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("Applied_Jobs");
            $scope.getApplyJobData(params);
        };
        /**
         * @ngdoc method
         * @name JobsAppliedController.getApplyJobData
         * @methodOf module.JobsAppliedController
         * @description
         * This method is used to get the jobs applied listings
         */
        $scope.getApplyJobData = function(params) {
            params.page = ($scope.currentPage !== undefined) ? $scope.currentPage : 1;
            jobAppliedFactory.get(params, function(response) {
                if (angular.isDefined(response._metadata)) {
                    $scope.currentPage = response._metadata.current_page;
                    $scope.totalItems = response._metadata.total;
                    $scope.itemsPerPage = response._metadata.per_page;
                    $scope.noOfPages = response._metadata.last_page;
                }
                $scope.applyjobs = response.data;
                /* Here need to check the attachment single or multiple concept */
                angular.forEach($scope.applyjobs, function(value) {
                    if (angular.isDefined(value.job.attachment)) {
                        if (value.attachment !== null) {
                            value.logo_url = 'images/small_thumb/Job/' + value.job.id + '.' + md5.createHash('Job' + value.job.id + 'png' + 'small_thumb') + '.png';
                        } else {
                            value.logo_url = 'images/no-image.png';
                        }
                    } else {
                        value.logo_url = 'images/no-image.png';
                    }
                    /* For check the job is already applied */
                    if (angular.isDefined(value.job_apply)) {
                        if (Object.keys(value.job_apply)
                            .length > 0) {
                            value.is_apply = true;
                        } else {
                            value.is_apply = false;
                        }
                    }
                });
            });
        }
        /**
         * @ngdoc method
         * @name JobsAppliedController.paginate
         * @methodOf module.JobsAppliedController
         * @description
         * This method is used for pagination
         */
        $scope.paginate = function() {
            $scope.currentPage = parseInt($scope.currentPage);
            $scope.getApplyJobData(params);
        };
        init();
  }]);