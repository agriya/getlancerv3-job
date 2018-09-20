'use strict';
/**
 * @ngdoc getlancerApp.Job.controller
 * @name my_jobs.controller:MyJobsController
 * @description
 *
 * This is my jobs controller having the methods init. and function getJobData. It is used for controlling the my jobs listing functionalities.
 **/
angular.module('getlancerApp.Job')
    .controller('MyJobsController', ['$scope', '$rootScope', '$window', '$filter', '$state', 'MyJobsFactory', 'JobStatusCountFactory', 'JobCategoriesFactory', 'JobSkillsFactory', 'JobSalaryTypeFactory', 'Upload', '$timeout', 'DateFormat', '$cookies', 'JobsEdit', 'flash', function($scope, $rootScope, $window, $filter, $state, MyJobsFactory, JobStatusCountFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, $timeout, DateFormat, $cookies, JobsEdit, flash) {
        var params = [];
        $scope.DateFormat = DateFormat;
        $scope.sortby = params.sortby = 'desc';
        $scope.parseInt = parseInt;
        $scope.myjobs = null;
        /**
         * @ngdoc method
         * @name MyJobsController.init
         * @methodOf module.MyJobsController
         * @description
         * This method is used for init the functions.
         */
        $scope.init = function() {
            $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("My Jobs");
            $scope.auth = JSON.parse($cookies.get("auth"));
            $scope.jobStatus();
        };
        // get My job lists
        getJobData(params);
        /**
         * @ngdoc function
         * @name MyJobsController.getJobData
         * @functionof module.MyJobsController
         * @description
         * This function is used for My Jobs details.
         */
        function getJobData(params) {
            params.page = ($scope.currentPage !== undefined) ? $scope.currentPage : 1;
            MyJobsFactory.get(params, function(response) {
                if (angular.isDefined(response._metadata)) {
                    $scope.currentPage = response._metadata.current_page;
                    $scope.totalItems = response._metadata.total;
                    $scope.itemsPerPage = response._metadata.per_page;
                    $scope.noOfPages = response._metadata.last_page;
                }
                return $scope.myjobs = response.data;
            });
        }
        /**
         * @ngdoc method
         * @name MyJobsController.jobStatus
         * @methodOf module.MyJobsController
         * @description
         * This method is used for get the job status listing in backend webservice.
         */
        $scope.jobStatus = function() {
            JobStatusCountFactory.get({
                id: $scope.auth.id
            }, function(response) {
                $scope.status = response.data;
                $scope.all = 0;
                angular.forEach($scope.status, function(value, key) {
                    $scope.all += parseInt(value.job_count);
                });
            });
        };
        $scope.order = function(field_name, sortby) {
            params.sort = field_name;
            params.sortby = sortby;
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
            }
        };
        $scope.orderByNoOfOpening = function(sortby) {
            params.sort = 'no_of_opening';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.no_of_opening = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.no_of_opening = 'up';
            }
        };
        $scope.orderByLastDateToApply = function(sortby) {
            params.sort = 'last_date_to_apply';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.last_date_to_apply = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.last_date_to_apply = 'up';
            }
        };
        $scope.orderJobApplyCount = function(sortby) {
            params.sort = 'job_apply_count';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.job_apply_count = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.job_apply_count = 'up';
            }
        };
        $scope.orderJobApplyClickCount = function(sortby) {
            params.sort = 'job_apply_click_count';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.job_apply_click_count = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.job_apply_click_count = 'up';
            }
        };
        $scope.orderView = function(sortby) {
            params.sort = 'view_count';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.view_count = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.view_count = 'up';
            }
        };
        $scope.orderPosted = function(sortby) {
            params.sort = 'created_at';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.created_at = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.created_at = 'up';
            }
        };
        $scope.orderJob = function(sortby) {
            params.sort = 'title';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.title = 'down';
            }
            $scope.myjobs = getJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.title = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name MyJobsController.paginate
         * @methodOf module.MyJobsController
         * @description
         * This method is used for handling the pagination.
         */
        $scope.paginate = function() {
            $scope.currentPage = parseInt($scope.currentPage);
            getJobData(params);
        };
        $scope.archived = function(id) {
            swal({ //jshint ignore:line
                title: $filter("translate")("Are you sure you want to archived this job?"),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "OK",
                cancelButtonText: "Cancel",
                closeOnConfirm: true,
                animation:false,
            }).then(function (isConfirm) {
                if (isConfirm) {
                    var flashMessage;
                    $scope.job = {};
                    $scope.job.id = id;
                    $scope.job.job_status_id = 7;
                    JobsEdit.put($scope.job, function(response) {
                        $scope.response = response;
                        if (response.error.code === 0) {
                            flashMessage = $filter("translate")("Your job is archieved");
                            flash.set(flashMessage, 'success', false);
                            getJobData(params);
                        } else {
                            flashMessage = $filter("translate")(response.error.message);
                            flash.set(flashMessage, 'error', false);
                        }
                    });
                }
            });
        }
        $scope.filterByStatus = function(id) {
            params.job_status_id = id;
            getJobData(params);
        };
        $scope.form_value = false;
        $scope.buttonName = "Resume Applied";
        $scope.form = function() {
            if ($scope.form_value == false) {
                $scope.form_value = true;
                $scope.buttonName = 'My Jobs';
            } else {
                $scope.form_value = false;
                $scope.buttonName = "Applied Resume";
            }
        }
        $scope.init();
  }]);