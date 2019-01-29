'use strict';
angular.module('getlancerApp.Job.JobFlag')
    /**
     * @ngdoc getlancerApp.Job.controller
     * @name jobs.controller:JobFlagController
     * @description
     * This controller is used to detail view of job.
     **/
    .controller('JobFlagController', function($scope, $rootScope, $window, $stateParams, $filter, md5, $state, $timeout, $uibModal, $uibModalStack, flagCategories, $cookies, flash) {
        /**
         * @ngdoc method
         * @name JobFlagController.closemodel
         * @methodOf module.jobsViewController
         * @description
         * This method is used to close the popup model
         */
        $rootScope.closemodel = function() {
            $state.go('jobs_view', {
                id: $state.params.id,
            });
            $uibModalStack.dismissAll();
        }
        /**
         * @ngdoc method
         * @name JobFlagController.jobReport
         * @methodOf module.JobFlagController
         * @description
         * This method is used to report the job. which is open the popup model window to report the jobs.
         */
        $scope.jobReport = function() {
            $scope.modalInstance = $uibModal.open({
                templateUrl: 'scripts/plugins/Job/JobFlag/views/default/job_report.html',
                animation: false,
                controller: function($scope, $rootScope, $window, $stateParams, $filter, md5, $state, Upload, $timeout, $uibModal, $uibModalStack, flagCategories, JobsReportFactory, flash) {
                    $scope.flag = $scope.flags = [];
                    $scope.flagCategory = function() {
                        var params ={};
                        params.class='Job',
                        flagCategories.get(params, function(response) {
                            $scope.flag = response.data;
                        });
                    };
                    $scope.flagCategory();
                    /**
                     * @ngdoc method
                     * @name jobsViewController.jobReport.submit
                     * @methodOf module.jobsViewController
                     * @description
                     * This method is post the apply job details.
                     */
                    $scope.flag_button = false;
                    $scope.submit = function($valid) {
                        if ($valid) {
                            $scope.flag_button = true;
                            var post_params = {
                                user_id: $rootScope.user.id,
                                foreign_id: $state.params.id,
                                class: "Job",
                                flag_category_id: $scope.flags.flag_category_id,
                                message: $scope.flags.message
                            };
                            var flashMessage = "";
                            JobsReportFactory.post(post_params, function(response) {
                                $scope.response = response;
                                if ($scope.response.error.code === 0) {
                                    $scope.flags = {};
                                    $rootScope.closemodel();
                                    flashMessage = $filter("translate")("Report posted successfully.");
                                    flash.set(flashMessage, 'success', false);
                                    job();
                                } else {
                                    flashMessage = $filter("translate")($scope.response.error.message);
                                    flash.set(flashMessage, 'error', false);
                                    $scope.flag_button = false;
                                }
                            });
                        }
                    };
                },
                size: 'lg'
            });
        };
    });