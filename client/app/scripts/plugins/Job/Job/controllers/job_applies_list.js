'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Job.controller:JobAppliesListController
 * @description
 * # JobAppliesListController
 * Controller of  the getlancerApp.Job
 */
angular.module('getlancerApp.Job')
    .controller('JobAppliesListController', ['$scope', '$rootScope', '$window', '$filter', '$state', 'JobAppliesListFactory', 'JobApplyStatusFactory', 'JobCategoriesFactory', 'JobApplyStatus', 'JobSalaryTypeFactory', 'Upload', '$timeout', 'DateFormat', 'flash', 'StarCount', 'JobAppliesStatus', '$cookies', 'md5', '$location', function($scope, $rootScope, $window, $filter, $state, JobAppliesListFactory, JobApplyStatusFactory, JobCategoriesFactory, JobApplyStatus, JobSalaryTypeFactory, Upload, $timeout, DateFormat, flash, StarCount, JobAppliesStatus, $cookies, md5, $location) {
        var params = [];
        $scope.DateFormat = DateFormat;
        $scope.sortby = params.sortby = 'desc';
        $scope.parseInt = parseInt;
        $scope.myjobs = null;
        params.jobId = $state.params.job_id;
        // $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("MyJobs");
        /**
         * @ngdoc method
         * @name JobAppliesListController.init
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to init the function and variables
         */
        function init() {
            $scope.StarCount = StarCount;
            $scope.auth = JSON.parse($cookies.get("auth"));
            getAppliesJobData(params);
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.getAppliesJobData
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get the job resume applies listing
         */
        function getAppliesJobData(params) {
            $scope.loader = true;
            if ($state.params.job_id == null) {
                params.page = ($scope.currentPage !== undefined) ? $scope.currentPage : 1;
                params.id = $scope.auth.id;
                JobAppliesListFactory.get(params, function(response) {
                    angular.forEach(response.data, function(user) {
                     $scope.exam_users = user.exams_users;
                     angular.forEach($scope.exam_users, function(exams) {
                         $scope.total_mark = Number(exams.total_mark || 0);
                         $scope.total_question_count = Number(exams.total_question_count || 0);
                         $scope.average = $scope.total_mark / $scope.total_question_count;
                         exams.exam_user_per = parseInt($scope.average * 100);
                         if (angular.isDefined(exams.exam.attachment) && exams.exam.attachment !== null) {
                             exams.exam_image = 'images/small_normal_thumb/Exam/' + exams.exam.attachment.foreign_id + '.' + md5.createHash('Exam' + exams.exam.attachment.foreign_id + 'png' + 'small_normal_thumb') + '.png';
                         } else {
                             exams.exam_image = 'images/no-image.png';
                         }
                     });
                 });
                    $scope.loader = false;
                    $scope.jobApplyStatus();
                    if (angular.isDefined(response._metadata)) {
                        $scope.currentPage = response._metadata.current_page;
                        $scope.totalItems = response._metadata.total;
                        $scope.itemsPerPage = response._metadata.per_page;
                        $scope.noOfPages = response._metadata.last_page;
                    }
                     return $scope.appliesjobs = response.data;

                     
                });
            } else {
                params.job_id = $state.params.job_id;
                params.page = ($scope.currentPage !== undefined) ? $scope.currentPage : 1;
                params.id = $scope.auth.id;
                JobAppliesListFactory.get(params, function(response) {
                     angular.forEach(response.data, function(user) {
                     $scope.exam_users = user.exams_users;
                     angular.forEach($scope.exam_users, function(exams) {
                         $scope.total_mark = Number(exams.total_mark || 0);
                         $scope.total_question_count = Number(exams.total_question_count || 0);
                         $scope.average = $scope.total_mark / $scope.total_question_count;
                         exams.exam_user_per = parseInt($scope.average * 100);
                         if (angular.isDefined(exams.exam.attachment) && exams.exam.attachment !== null) {
                             exams.exam_image = 'images/small_normal_thumb/Exam/' + exams.exam.attachment.foreign_id + '.' + md5.createHash('Exam' + exams.exam.attachment.foreign_id + 'png' + 'small_normal_thumb') + '.png';
                         } else {
                             exams.exam_image = 'images/no-image.png';
                         }
                     });
                 });
                    $scope.jobApplyStatus();
                    if (angular.isDefined(response._metadata)) {
                        $scope.currentPage = response._metadata.current_page;
                        $scope.totalItems = response._metadata.total;
                        $scope.itemsPerPage = response._metadata.per_page;
                        $scope.noOfPages = response._metadata.last_page;
                    }
                    return $scope.appliesjobs = response.data;
                });
            }
        }
         $scope.showhideSkills = function(id, is_show) {
             var skillId = 'skills-' + id;
             if (parseInt(is_show) === 1) {
                 $('#' + skillId) //jshint ignore:line
                     .attr('style', 'display:block');
             } else {
                 $('.user-certificate-skills') //jshint ignore:line
                     .attr('style', 'display:none');
             }
         };
        /**
         * @ngdoc method
         * @name JobAppliesListController.jobApplyStatus
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get the job apply status listing(eg: new, selected, rejected etc)
         */
        $scope.jobApplyStatus = function() {
            JobAppliesStatus.get({
                id: $scope.auth.id
            }, function(response) {
                $scope.jobStatsCount = response.data;
                 $scope.jobStatusCount = $filter('orderBy')(response.data, 'id');
                $scope.all = 0;
                angular.forEach($scope.jobStatsCount, function(value) {
                    $scope.all += parseInt(value.job_apply_count);
                });
            });
        }
        /**
         * @ngdoc method
         * @name JobAppliesListController.jobStatusApply
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to update the job apply status listing(eg: new, selected, rejected etc)
         */
        $scope.jobStatusApply = function(statusId, jobId) {
            JobApplyStatus.put({
                id: jobId,
                job_apply_status_id: statusId
            }, function(response) {
                var flashMessage = "";
                if (parseInt(response.error.code) === 0) {
                    flashMessage = $filter("translate")("Job status changed");
                    flash.set(flashMessage, 'success', false);
                    $scope.jobApplyStatus();
                } else {
                    flashMessage = $filter("translate")(response.error.message);
                    flash.set(flashMessage, 'error', false);
                }
            });
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.orderByJob
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get job applies listing orderBy based on job title
         */
        $scope.orderByJob = function(sortby) {
            params.sort = 'job.title';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.no_of_opening = 'down';
            }
            $scope.myjobs = getAppliesJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.title = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.orderByApplicant
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get job applies listing orderBy based on username
         */
        $scope.orderByApplicant = function(sortby) {
            params.sort = 'user.username';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.username = 'down';
            }
            $scope.myjobs = getAppliesJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.username = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.orderByRatings
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get job applies listing orderBy based on total resume rating
         */
        $scope.orderByRatings = function(sortby) {
            params.sort = 'total_resume_rating';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.total_resume_rating = 'down';
            }
            $scope.myjobs = getAppliesJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.total_resume_rating = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.orderByFeedbacks
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get job applies listing orderBy based on resume rating count
         */
        $scope.orderByFeedbacks = function(sortby) {
            params.sort = 'resume_rating_count';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.resume_rating_count = 'down';
            }
            $scope.myjobs = getAppliesJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.resume_rating_count = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.orderByAppliedOn
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used to get job applies listing orderBy based on created date
         */
        $scope.orderByAppliedOn = function(sortby) {
            params.sort = 'created_at';
            params.sortby = sortby;
            if (sortby === 'asc') {
                $scope.created_at = 'down';
            }
            $scope.myjobs = getAppliesJobData(params);
            $scope.sortby = 'desc';
            if (sortby === 'desc') {
                $scope.sortby = 'asc';
                $scope.created_at = 'up';
            }
        };
        /**
         * @ngdoc method
         * @name JobAppliesListController.paginate
         * @methodOf module.JobAppliesListController
         * @description
         * This method is used for pagination process
         */
        $scope.paginate = function() {
            $scope.currentPage = parseInt($scope.currentPage);
            getAppliesJobData(params);
        };
        $scope.downloadResume = function(attachment) {
            var docFile = attachment.filename;
            var ext = docFile.substring(docFile.lastIndexOf('.') + 1)
                .toLowerCase();
            var download_file = md5.createHash(attachment.class_name + attachment.foreign_id + 'docdownload') + '.doc';
            var downloadurl = $location.protocol() + '://' + $location.host() + '/download/' + attachment.class_name + '/' + attachment.foreign_id + '/' + download_file;
        };
        $scope.filterByStatus = function(id, name) {
            $scope.auth = JSON.parse($cookies.get("auth"));
            $scope.job_stats = id;
            $scope.status_name = name;
            params.job_apply_status_id = id;
            params.id = $scope.auth.id;
            getAppliesJobData(params);
            $state.go('user_dashboard', {
                status: name,
                type: 'applied_resumes'
            }, {
                notify: false
            });
        };
          if ($state.params.status) {
            if($state.params.status === 'all'){
                $scope.filterByStatus(null, 'all');
            }
           else if ($state.params.status === 'New') {
                $scope.filterByStatus(1, 'New');
            }
            else if ($state.params.status === 'Inprocess') {
                $scope.filterByStatus(2, 'Inprocess');
            } else if ($state.params.status === 'Selected') {
                $scope.filterByStatus(3,'Selected');
            } else if ($state.params.status === 'Rejected') {
                $scope.filterByStatus(4, 'Rejected');
            }
        }
       
        init();
  }]);