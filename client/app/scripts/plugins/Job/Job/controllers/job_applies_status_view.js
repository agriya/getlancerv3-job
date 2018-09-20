'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Job.controller:jobsAppliesStatusController
 * @description
 * # jobsAppliesStatusController
 * Controller of  the getlancerApp.Job
 */
angular.module('getlancerApp.Job')
    .controller('jobsAppliesStatusController', function($scope, $rootScope, $window, $stateParams, $filter, md5, $state, JobsEdit, JobTypeFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, $timeout, $uibModal, $uibModalStack, DateFormat, SalaryType, JobsAppliesFactory, $cookies, JobApplyStatus, StarCount) {
        $scope.DateFormat = DateFormat;
        $rootScope.closemodel = function() {
            $uibModalStack.dismissAll();
        }
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.init
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is used to init the function and variables
         */
        function init() {
            $scope.StarCount = StarCount;
            $scope.DateFormat = DateFormat;
            $scope.SalaryType = SalaryType;
            $scope.auth = JSON.parse($cookies.get("auth"));
            $scope.comment = {};
            $scope.jobAppliesView();
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.jobAppliesView
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is get job applied listing based on id
         */
        $scope.jobAppliesView = function() {
            JobApplyStatus.get({
                id: $state.params.id
            }, function(response) {
                $scope.job_apply_status_view = response.data;
                $scope.jobtitle = $scope.job_apply_status_view.job.title;
                $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")($scope.jobtitle);
                $scope.rating_avg = Math.floor(parseInt(response.data.total_resume_rating) / parseInt(response.data.resume_rating_count));
                if (angular.isDefined($scope.job_apply_status_view.user.attachment)) {
                    if ($scope.job_apply_status_view.user.attachment !== null) {
                        $scope.user.logo_url = 'images/normal_thumb/UserAvatar/' + $scope.job_apply_status_view.user.attachment.foreign_id + '.' + md5.createHash('UserAvatar' + $scope.job_apply_status_view.user.attachment.foreign_id + 'png' + 'normal_thumb') + '.png';
                    } else {
                        $scope.logo_url = 'images/no-image.png';
                    }
                }
            });
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.starCount
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is get star count value for resume rating post function
         */
        $scope.starCount = function(starCountVal) {
            $scope.comment.rating_select = starCountVal;
        };
        init();
    });