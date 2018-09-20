'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Job.controller:JobsAddController
 * @description
 * # JobsAddController
 * Controller of the getlancerApp.Job
 */
angular.module('getlancerApp.Job')
    .controller('JobsAddController', ['$scope', '$rootScope', '$window', '$filter', 'flash', '$state', 'JobsFactory', 'JobTypeFactory', 'JobCategoriesFactory', 'JobSkillsFactory', 'JobSalaryTypeFactory', 'Upload', '$timeout', 'FileFormat', 'md5', 'JobAutocompleteUsers', 'ConstUserRole', function($scope, $rootScope, $window, $filter, flash, $state, JobsFactory, JobTypeFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, $timeout, FileFormat, md5, JobAutocompleteUsers, ConstUserRole) {
        /**
         * @ngdoc method
         * @name JobsAddController.init
         * @methodOf module.JobsAddController
         * @description
         * This method is used to init the function and variables
         */
        function init() {
            $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("Post a Job");
            // $scope.FileFormat = FileFormat;
            $scope.jobTypes();
            $scope.jobCategories();
            $scope.jobSkills();
            $scope.jobSalaryTypes();
            $scope.regex = 'http://';
            // $scope.is_image_error = false;
        };
      
       $scope.exp_years = [];
        for (var i = 0; i <= 30; i++) {
           $scope.exp_years.push(i);
        }
        $scope.JOB_TOTAL_FEE = $rootScope.settings.LISTING_FEE_FOR_JOB;
        $scope.job = {};
        $scope.job.apply_via = "via_our_site";
        $scope.job.job_type_id = 1;
        $scope.job.is_show_salary = false;
        $scope.skill_select = [];
        $scope.check = function(value, checked) {
            var idx = $scope.skill_select.indexOf(value);
            if (idx >= 0 && !checked) {
                $scope.skill_select.splice(idx, 1);
            }
            if (idx < 0 && checked) {
                $scope.skill_select.push(value);
            }
        };
        $scope.amount_find = true;
       $scope.projectFeatureFeeAdd = function(value) {
            if ($scope.job.is_featured) {
                $scope.amount_find = false;
                 $timeout(function () {
                    $scope.amount_find = true;
                    $scope.JOB_TOTAL_FEE = parseInt($scope.JOB_TOTAL_FEE) + parseInt(value);
                }, 100);
                
            } else {
                 $scope.amount_find = false;
                 $timeout(function () {
                    $scope.amount_find = true;
                    $scope.JOB_TOTAL_FEE = parseInt($scope.JOB_TOTAL_FEE) - parseInt(value);
                }, 100);
                
            }
        };
        $scope.projectUrgentFeeAdd = function(value) {
            if ($scope.job.is_urgent) {
                 $scope.amount_find = false;
                  $timeout(function () {
                    $scope.amount_find = true;
                     $scope.JOB_TOTAL_FEE = parseInt($scope.JOB_TOTAL_FEE) + parseInt(value);
                }, 100);
              
            } else {
                 $scope.amount_find = false;
                  $timeout(function () {
                    $scope.amount_find = true;
                     $scope.JOB_TOTAL_FEE = parseInt($scope.JOB_TOTAL_FEE) - parseInt(value);
                }, 100);
            }
        };
        /**
         * @ngdoc method
         * @name JobsAddController.selectType
         * @methodOf module.JobsAddController
         * @description
         * This method is used For select the job type for eg: Full time , part time like that.
         */
        $scope.selectType = function(id) {
            $scope.job.job_type_id = id;
        }
        /**
         * @ngdoc method
         * @name JobsAddController.location
         * @methodOf module.JobsAddController
         * @description
         * This method is used For google address auto complete 
         */
        $scope.location = function() {
            $scope.job.city = {};
            $scope.job.state = {};
            $scope.job.country = {};
            var k = 0;
            if ($scope.place !== undefined) {
                angular.forEach($scope.place.address_components, function(value, key) {
                    if (value.types[0] === 'locality' || value.types[0] === 'administrative_area_level_2') {
                        if (value.types[0] === 'locality') {
                            k = 1;
                        }
                    }
                    if (value.types[0] === 'administrative_area_level_1') {
                        $scope.job.city.name = value.long_name;
                        //   $scope.disable_state = true;
                    }
                    if (value.types[0] === 'administrative_area_level_1') {
                        $scope.job.state.name = value.long_name;
                        //  $scope.disable_state = true;
                    }
                    if (value.types[0] === 'country') {
                        $scope.job.country.iso_alpha2 = value.short_name;
                        //   $scope.disable_country = true;
                    }
                    if (value.types[0] === 'postal_code') {
                        $scope.disable_zip = 'true';
                        $scope.job.zip_code = parseInt(value.long_name);
                    } else {
                        $scope.disable_zip = 'false';
                    }
                    $scope.job.latitude = $scope.place.geometry.location.lat();
                    $scope.disable_latitude = true;
                    $scope.job.longitude = $scope.place.geometry.location.lng();
                    $scope.disable_longitude = true;
                    $scope.job.address = $scope.place.formatted_address;
                    $scope.job.full_address = $scope.place.formatted_address;                                    
                }); 
            }
        };
		
        /**
         * @ngdoc method
         * @name JobsAddController.submit
         * @methodOf module.JobsAddController
         * @description
         * This method is used to post the jobs
         */
        $scope.save_btn = false;
        $scope.submit = function(type, $valid) {
            if ($valid && !$scope.error_message) {
                $scope.save_btn = true;
                /* type: 1 -> Submit, type:2 -> Draft */
                $scope.job.job_status_id = (parseInt(type) === 1) ? 2 : 1;
                $scope.tmp_skills = [];
                /* Due to webservice team issue doing this like they told did not change the api flow */
                // angular.forEach($scope.skill_select, function(id, key) {
                //     $scope.tmp_skills.push({
                //         'skill_id': id
                //     });
                // });
                // $scope.job.skills = $scope.tmp_skills;
                if (angular.isDefined($scope.skill_select)) {
                    $scope.seperate_skills = [];
                    angular.forEach($scope.skill_select, function (value) {
                       $scope.seperate_skills.push(value.text);
                    });
                   $scope.job.skills = $scope.seperate_skills.toString();
                }
                var flashMessage;
                $scope.location();
                $scope.job.last_date_to_apply = $filter('date')($scope.last_date_to_apply, "yyyy-MM-dd");
				if (angular.isUndefined($scope.job.user_id)) {
             		  $scope.job.user_id = ConstUserRole.Admin;
            	}               
                //$scope.job.job_listing_date_upto = $filter('date')($scope.last_date_to_apply, "dd-MM-yyyy");
				delete $scope.job.job_listing_date_upto;
                delete $scope.job.logo_url;
                JobsFactory.post($scope.job, function(response) {
                    $scope.save_btn = false;
                    $scope.response = response;
                    if ($scope.response.error.code === 0) {
                        flashMessage = $filter("translate")("Job added successfully.");
                        if (type === 2) {
                            $state.reload();
                            $state.go('user_dashboard', {
                                status: 'draft',
                                type: 'my_jobs'
                            });
                        } else {
                             if (response.data.total_listing_fee > 0) {
                            $state.go('job_payment', {
                                id: $scope.response.data.id,
                                slug: response.slug
                            });
                        } else {
                            $state.go('jobs_view', {
                                id: $scope.response.data.id,
                                slug: response.slug
                            });
                        }
                    }
                        flash.set(flashMessage, 'success', false);
                    } else {
                        if (type === 2) {
                            flashMessage = $filter("translate")("Job stored in draft failed.");
                        } else {
                            flashMessage = $filter("translate")("Job added failed.");
                        }
                        flash.set(flashMessage, 'error', false);
                    }
                }, function(error) {
                    $scope.save_btn = false;
                });
            } else {
                $timeout(function() {
                    $('.error')
                        .each(function() {
                            if (!$(this)
                                .hasClass('ng-hide')) {
                                $scope.scrollvalidate($(this)
                                   .offset().top-140);
                                return false;
                            }
                        });
                }, 100);
            }
        };
        $scope.scrollvalidate = function(topvalue) {
            $('html, body')
                .animate({
                    'scrollTop': topvalue
                });
        };
        /**
         * @ngdoc method
         * @name JobsAddController.jobTypes
         * @methodOf module.JobsAddController
         * @description
         * This method is used For select the job type for eg: Full time , part time like that.
         */
        $scope.jobTypes = function() {
            JobTypeFactory.get(function(response) {
                $scope.types = response.data;
            });
        };
        /**
         * @ngdoc method
         * @name JobsAddController.jobCategories
         * @methodOf module.JobsAddController
         * @description
         * This method is used For select the job categories.
         */
        $scope.jobCategories = function() {
            JobCategoriesFactory.get(function(response) {
                $scope.categories = response.data;
            });
        };
        /**
         * @ngdoc method
         * @name JobsAddController.jobSkills
         * @methodOf module.JobsAddController
         * @description
         * This method is used For select the job skills.
         */
        var params = {};
        params.limit = 'all';
        $scope.jobSkills = function() {
            JobSkillsFactory.get(params, function(response) {
                $scope.skilles = response.data;
                   $scope.skills = [];
                 angular.forEach($scope.skilles, function (value) {
                         $scope.skills.push({
                            id: value.id,
                            text: value.name
                        });
                        /* here for select skill default */
                        if ($scope.skills !== undefined) {
                            if ($scope.skills.indexOf(value.id) != -1) {
                                $scope.skill_select.push({
                                    id: value.id,
                                    text: value.name
                                }); 
                            }
                        }
                    });
            });
        };

         $scope.loadSkills = function (qstr) {
            qstr = qstr.toLowerCase();
            var items = [];
            angular.forEach($scope.skills, function (value) {
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
         * @name JobsAddController.jobSalaryTypes
         * @methodOf module.JobsAddController
         * @description
         * This method is used For select the salary type.
         */
        $scope.jobSalaryTypes = function() {
            JobSalaryTypeFactory.get(function(response) {
                $scope.salarytypes = response.data;
            });
        };
        /**
         * @ngdoc method
         * @name JobsAddController.upload
         * @methodOf module.JobsAddController
         * @description
         * This method is used to upload the company logo image.
         */
        $scope.upload = function(file) {
            // if (checkFileFormat(file, $scope.FileFormat.image)) {
                // $scope.is_image_error = false;
                Upload.upload({
                        url: '/api/v1/attachments?class=Job',
                        data: {
                            file: file,
                        }
                    })
                    .then(function(response) {
                          if (response.data.error.code === 0) {
                        $scope.job.image = response.data.attachment;
                        $scope.job.logo_url = '/images/small_thumb/Job/' + $scope.job.id + '.' + md5.createHash('Job' + $scope.job.id + 'png' + 'small_thumb') + '.png';
                         $scope.error_message = '';
                          } else {
                               $scope.error_message = response.data.error.message;
                          }
                    });
            // }
            //  else {
            //     $scope.is_image_error = true;
            };
        // };
        /**
         * @ngdoc method
         * @name JobsAddController.today
         * @methodOf module.JobsAddController
         * @description
         * This method is used for get today date
         */
        $scope.today = function() {
            $scope.dt = new Date();
        };
        $scope.today();
        /**
         * @ngdoc method
         * @name JobsAddController.clear
         * @methodOf module.JobsAddController
         * @description
         * This method is used for clear the date
         */
        $scope.clear = function() {
            $scope.dt = null;
        };
        $scope.inlineOptions = {
            customClass: getDayClass,
            minDate: new Date(),
            showWeeks: true
        };
        $scope.dateOptions = {
            formatYear: 'yy',
            maxDate: new Date(2020, 12, 31),
            minDate: new Date(),
            startingDay: 1
        };

        function toggleMin() {
            $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
            $scope.dateOptions.minDate = new Date();
        };
        toggleMin();
        $scope.open1 = function() {
            $scope.popup1.opened = true;
        };
        $scope.open2 = function() {
            $scope.popup2.opened = true;
        };
        /**
         * @ngdoc method
         * @name JobsAddController.formats
         * @methodOf module.JobsAddController
         * @description
         * This method is used for format the date.
         */
        $scope.formats = ['yyyy-MM-dd', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
        $scope.format = $scope.formats[1];
        $scope.altInputFormats = $scope.formats[1];
        $scope.popup1 = {
            opened: false
        };
        $scope.popup2 = {
            opened: false
        };
        /**
         * @ngdoc method
         * @name JobsAddController.getDayClass
         * @methodOf module.JobsAddController
         * @description
         * This method is used for datepicker plugin.
         */
        function getDayClass(data) {
            var date = data.date,
                mode = data.mode;
            if (mode === 'day') {
                var dayToCheck = new Date(date)
                    .setHours(0, 0, 0, 0);
                for (var i = 0; i < $scope.events.length; i++) {
                    var currentDay = new Date($scope.events[i].date)
                        .setHours(0, 0, 0, 0);
                    if (dayToCheck === currentDay) {
                        return $scope.events[i].status;
                    }
                }
            }
            return '';
        }
        init();
    }]);