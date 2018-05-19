'use strict';
angular.module('getlancerApp.Job')
    /**
     * @ngdoc getlancerApp.Job.controller
     * @name jobs.controller:jobsController
     * @description
     * This controller is only list the jobs and filter jobs functionality.
     **/
    .controller('jobsController', ['$scope', '$rootScope', '$window', '$filter', 'md5', '$state', 'JobsFactory', 'JobTypeFactory', 'JobCategoriesFactory', 'JobSkillsFactory', 'JobSalaryTypeFactory', 'Upload', '$timeout', '$location', '$stateParams', '$cookies', function($scope, $rootScope, $window, $filter, md5, $state, JobsFactory, JobTypeFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, $timeout, $location, $stateParams, $cookies) {
        /**
         * @ngdoc method
         * @name jobsController.init
         * @methodOf module.jobsController
         * @description
         * This method is used to init the function and variables
         */
        $scope.data = [];
        $scope.jobtype_select = [];

        function init() {
            $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("Browse Jobs");
            $scope.selectOptions = {
                enableSearch: true,
                scrollableHeight: '280px',
                scrollable: true,
            };
            $scope.params = [];
            $scope.job();
            $scope.maxSize = 5;
        };
        if ($state.params.job_types !== undefined) {
            angular.forEach($state.params.job_types.split(','), function(value) {
                $scope.jobtype_select.push(parseInt(value));
            });
        }
         if ($stateParams.q != undefined) {
                 $scope.data = {
                     q: $state.params.q
                 }
            }
        /**
         * @ngdoc method
         * @name jobsController.job
         * @methodOf module.jobsController
         * @description
         * This method is used to get the jobs listings
         */
        $scope.job = function() {
            var params = {};
            $scope.loader = true;
            if($state.params.page === undefined)
            {
                params.page = 1;
            }else{
                params.page = $state.params.page;
            }  
            if ($stateParams.q != undefined) {
                params.q = $stateParams.q;
            }
            if ($stateParams.job_categories != undefined) {
                params.job_categories = $stateParams.job_categories;
            }
            if ($stateParams.skills != undefined) {
                params.skills = $stateParams.skills;
            }
            if ($stateParams.job_types != undefined) {
                params.job_types = $stateParams.job_types;
            }
            JobsFactory.get(params, function(response) {
                if (angular.isDefined(response._metadata)) {
                    $scope.currentPage = response._metadata.current_page;
                    $scope.totalItems = response._metadata.total;
                    $scope.itemsPerPage = response._metadata.per_page;
                    $scope.noOfPages = response._metadata.last_page;
                }
                if (angular.isDefined(response.data)) {
                    $scope.jobs = response.data;
                    /* Here need to check the attachment single or multiple concept */
                    angular.forEach($scope.jobs, function(value) {
                        if (angular.isDefined(value.attachment)) {
                            if (value.attachment !== null) {
                                value.logo_url = 'images/small_normal_thumb/Job/' + value.id + '.' + md5.createHash('Job' + value.id + 'png' + 'small_normal_thumb') + '.png';
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
                }
            });
        };
        JobTypeFactory.get(function(response) {
            $scope.types = response.data;
        });
        /**
         * @ngdoc method
         * @name jobsController.jobSkills
         * @methodOf module.jobsController
         * @description
         * This method is used to get the jobs skills list
         */
        var params = {};
        params.limit = 'all';
        JobSkillsFactory.get(params, function(response) {
            if (parseInt(response.error.code) === 0) {
                $scope.jobSkill = [];
                $scope.jobSkills = response.data;
                $scope.data.skill_select = [];
                var selectedSkill = "";
                if (angular.isDefined($location.search()
                        .skills)) {
                    selectedSkill = $location.search()
                        .skills.split(',');
                }
                angular.forEach($scope.jobSkills, function(value) {
                    $scope.jobSkill.push({
                        id: value.id,
                        text: value.name
                    });
                    if (selectedSkill !== "" && selectedSkill.indexOf(value.id.toString()) != -1) {
                        $scope.data.skill_select.push({
                            id: value.id,
                            text: value.name
                        });
                    }
                });
            } else {
                console.log('Skills Error');
            }
        }, function(error) {
            console.log('Skills Error', error);
        });
        /**
         * @ngdoc method
         * @name jobsController.jobCategories
         * @methodOf module.jobsController
         * @description
         * This method is used to get the jobs categories list
         */
        var params = {};
        params.limit = 'all';
        JobCategoriesFactory.get(params, function(response) {
            if (parseInt(response.error.code) === 0) {
                $scope.jobCategories = response.data;
                $scope.jobCat = [];
                $scope.data.category_select = [];
                var selectedjobCat = "";
                if (angular.isDefined($location.search()
                        .job_categories)) {
                    selectedjobCat = $location.search()
                        .job_categories.split(',');
                }
                angular.forEach($scope.jobCategories, function(value) {
                    $scope.jobCat.push({
                        id: value.id,
                        text: value.name
                    });
                    if (selectedjobCat !== "" && selectedjobCat.indexOf(value.id.toString()) != -1) {
                        $scope.data.category_select.push({
                            id: value.id,
                            text: value.name
                        });
                    }
                });
            } else {
                console.log('Categories Error');
            }
        }, function(error) {
            console.log('jobCategory Error', error);
        });
        init();
        $scope.loader = false;
        /**
         * @ngdoc method
         * @name jobsController.job
         * @methodOf module.jobsController
         * @description
         * This method is used to get the jobs listings
         */
        $scope.paginate = function() {
            $scope.currentPage = parseInt($scope.currentPage);
             $state.go('jobs', {
                    'page': $scope.currentPage,
                });
            $scope.job();
        };
        $scope.jobCategories = function() {
            $scope.category = [];
            if ($rootScope.category !== undefined) {
                $scope.skill = $rootScope.category;
                delete $rootScope.category;
            }
            $scope.categories = [];
            JobCategoriesFactory.get(function(response) {
                angular.forEach(response.data, function(value) {
                    $scope.categories.push({
                        id: value.id,
                        label: $filter("capitalize")(value.name)
                    });
                });
            });
        };
        $scope.loadCategories = function(qstr) {
            qstr = qstr.toLowerCase();
            var items = [];
            angular.forEach($scope.jobCat, function(value) {
                name = value.text.toLowerCase();
                if (name.indexOf(qstr) >= 0) {
                    items.push({
                        id: value.id,
                        text: value.text
                    });
                }
            });
            return items;
        };
        $scope.loadSkills = function(qstr) {
            qstr = qstr.toLowerCase();
            var items = [];
            angular.forEach($scope.jobSkill, function(value) {
                name = value.text.toLowerCase();
                if (name.indexOf(qstr) >= 0) {
                    items.push({
                        id: value.id,
                        text: value.text
                    });
                }
            });
            return items;
        };
        /**
         * @ngdoc method
         * @name jobsController.jobTypes
         * @methodOf module.jobsController
         * @description
         * This method is used to get the jobs typs list
         */
        $scope.jobTypes = function() {
            $scope.jobtype = [];
            $scope.types = [];
            JobTypeFactory.get(function(response) {
                angular.forEach(response.data, function(value) {
                    $scope.types.push({
                        id: value.id,
                        label: $filter("capitalize")(value.name)
                    });
                });
            });
        };
        $scope.check = function(value, checked) {
            var idx = $scope.jobtype_select.indexOf(value);
            if (idx > -1) {
                $scope.jobtype_select.splice(idx, 1);
            } else {
                $scope.jobtype_select.push(value);
            }
        };
        /**
         * @ngdoc method
         * @name jobsController.job
         * @methodOf module.jobsController
         * @description
         * This method is used to get filter the jobs
         */
        /**
         * Refine Search Job Listing
         */
        $scope.refinesearch = function(data) {
            if (angular.isDefined(data.skill_select) && Object.keys(data.skill_select)
                .length > 0) {
                var skills = [];
                angular.forEach(data.skill_select, function(value) {
                    skills.push(value.id);
                });
                data.skills = skills.toString();
            } else {
                data.skills = undefined;
            }
            if (angular.isDefined(data.category_select) && Object.keys(data.category_select)
                .length > 0) {
                var categories = [];
                angular.forEach(data.category_select, function(value) {
                    categories.push(value.id);
                });
                data.categories = categories.toString();
            } else {
                data.categories = undefined;
            }
            $scope.params = {
                q: data.q,
                job_categories: data.categories,
                skills: data.skills,
                job_types: ($scope.jobtype_select.length > 0) ? $scope.jobtype_select.toString() : ""
            };
            $state.go('jobs', $scope.params);
        };
  }]);