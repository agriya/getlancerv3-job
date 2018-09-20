'use strict';
angular.module('getlancerApp.Job')
    /**
     * @ngdoc getlancerApp.Job.controller
     * @name jobs.controller:jobsViewController
     * @description
     * This controller is used to detail view of job.
     **/
    .controller('jobsViewController', function($scope, $rootScope, $window, $stateParams, $filter, md5, $state, JobsEdit, JobTypeFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, $timeout, $uibModal, $uibModalStack, DateFormat, SalaryType, JobsAppliesFactory, flagCategories, $cookies, FileFormat, flash, jobApplyClick) {
        /**
         * @ngdoc method
         * @name jobsViewController.closemodel
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
         * @name jobsViewController.init
         * @methodOf module.jobsViewController
         * @description
         * This method is used to init the function and variables
         */
        function init() {
            job();
            $scope.DateFormat = DateFormat;
            $scope.SalaryType = SalaryType;
            $scope.is_image_error = false;
        };
        /**
         * @ngdoc function
         * @name jobsViewController.job
         * @functionOf module.jobsViewController
         * @description
         * This function is used to get the job details
         */
        function job() {
            JobsEdit.get({
                id: $state.params.id,
                type: 'view'
            }, function(response) {
                if (angular.isDefined(response)) {
                  if (parseInt(response.error.code) === 0) {
                    $scope.show_response_page = true;  
                    $scope.job_view = response.data;
                    $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("Job" + " " + "-" + " " + $scope.job_view.title);
                    if (angular.isDefined($scope.job_view.job_apply)) {
                        if (Object.keys($scope.job_view.job_apply)
                            .length > 0) {
                            $scope.is_job_apply = true;
                        }
                    }
                    if (angular.isDefined($scope.job_view.flag)) {
                        if (Object.keys($scope.job_view.flag)
                            .length > 0) {
                            $rootScope.is_flag = true;
                        }
                    }
                    if (angular.isDefined($scope.job_view.attachment)) {
                        if ($scope.job_view.attachment !== null) {
                            $scope.job_view.logo_url = 'images/normal_thumb/Job/' + $scope.job_view.id + '.' + md5.createHash('Job' + $scope.job_view.id + 'png' + 'normal_thumb') + '.png';
                        } else {
                            $scope.job_view.logo_url = 'images/no-image.png';
                        }
                    } else {
                        $scope.job_view.logo_url = 'images/no-image.png';
                    }
                }
            }
            });
        };
        /**
         * @ngdoc method
         * @name jobsViewController.jobApply
         * @methodOf module.jobsViewController
         * @description
         * This method is used to applied the job. Which is open the model popup window for apply the job.
         */
        $scope.jobApply = function(id, slug) {
            $state.go('applies_resume', {
                id: id,
                slug: slug,
            }, {
                notify: false
            });
            $scope.modalInstance = $uibModal.open({
                templateUrl: 'scripts/plugins/Job/Job/views/default/job_resume_applies.html',
                animation: false,
                controller: function($scope, $rootScope, $window, $stateParams, $filter, md5, $state, Upload, $timeout, $uibModal, $uibModalStack, JobsAppliesFactory, flash, FileFormat) {
                    $scope.job_apply = {};
                    // $scope.FileFormat = FileFormat;
                    // $scope.is_image_error = false;
                    $scope.upload = function(file) {
                        // if (checkFileFormat(file, $scope.FileFormat.resume)) {
                            // $scope.is_image_error = false;
                            Upload.upload({
                                    url: '/api/v1/attachments?class=JobApply',
                                    data: {
                                        file: file,
                                    }
                                })
                                .then(function(response) {
                                    if (response.data.error.code === 0) {
                                      $scope.job_apply.file = response.data.attachment;
                                      $scope.error_message = '';
                                     } 
                                       else {
                                            $scope.error_message = response.data.error.message;
                                       }
                                });
                        //}
                        //  else {
                        //     $scope.is_image_error = true;
                        // }
                    };
                    /**
                     * @ngdoc method
                     * @name jobsViewController.jobApply.submit
                     * @methodOf module.jobsViewController
                     * @description
                     * This method is post the apply job details.
                     */
                    $scope.submit = function($valid, value) {
                        if ($valid && !$scope.error_messages) {
                            var post_params = {
                                job_id: $state.params.id,
                                cover_letter: $scope.job_apply.cover_letter,
                                file: $scope.job_apply.file
                            };
                            var flashMessage = "";
                            JobsAppliesFactory.post(post_params, function(response) {
                                $scope.response = response;
                                if ($scope.response.error.code === 0) {
                                    $rootScope.closemodel();
                                    flashMessage = $filter("translate")("Resume posted Successfully.");
                                    flash.set(flashMessage, 'success', false);
                                    job();
                                } else {
                                    flashMessage = $filter("translate")($scope.response.error.message);
                                    flash.set(flashMessage, 'error', false);
                                }
                            });
                        }
                    };
                },
                size: 'lg'
            });
        };
        /**
         * @ngdoc method
         * @name jobsViewController.jobFilter 
         * @methodOf module.jobsViewController
         * @description
         * This method is used to store the category and skill id values in rootScope based the user submit in the veiw page for job filters and redirect the jobs listing page with the filter. 
         */
        $scope.jobFilter = function(id, type) {
            if (type === 1) {
                 $state.go('jobs', {
                        'skills': id,
                    });
                $rootScope.skill = id;
            } else {
                $state.go('jobs', {
                        'job_categories': id,
                    });
                $rootScope.category = id;
            }
            // $state.go('jobs');
        }
        init();
        $scope.job_apply = {};
        $scope.FileFormat = FileFormat;
        $scope.is_image_error = false;
        $scope.upload = function(file) {
            if (checkFileFormat(file, $scope.FileFormat.resume)) {
                $scope.is_image_error = false;
                Upload.upload({
                        url: '/api/v1/attachments?class=JobApply',
                        data: {
                            file: file,
                        }
                    })
                    .then(function(response) {
                        $scope.job_apply.file = response.data.attachment;
                    });
            } else {
                $scope.is_image_error = true;
            }
        };
        /**
         * @ngdoc method
         * @name jobsViewController.jobApply.submit
         * @methodOf module.jobsViewController
         * @description
         * This method is post the apply job details.
         */
        $scope.job_resume = false;
        $scope.submit = function($valid, value) {
            if ($valid && !$scope.is_image_error) {
                $scope.job_resume = true;
                var post_params = {
                    job_id: $state.params.id,
                    cover_letter: $scope.job_apply.cover_letter,
                    file: $scope.job_apply.file
                };
                var flashMessage = "";
                JobsAppliesFactory.post(post_params, function(response) {
                    $scope.response = response;
                    if ($scope.response.error.code === 0) {
                        $rootScope.closemodel();
                        flashMessage = $filter("translate")("Resume posted Successfully.");
                        flash.set(flashMessage, 'success', false);
                        job();
                        $state.go('jobs_view', {
                            id: $state.params.id,
                        });
                    } else {
                        $scope.job_resume = false;
                        flashMessage = $filter("translate")($scope.response.error.message);
                        flash.set(flashMessage, 'error', false);
                    }
                });
            };
        };
        $scope.jobApplyClick = function(id) {
            jobApplyClick.post({
                job_id: id
            }, function(response) {
                $scope.url = response.data;
                $window.open($scope.url.job.job_url, '_blank');
            })
        }
    });