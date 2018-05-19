'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.controller:JobsEditController
 * @description
 * # JobsEditController
 * Controller of the getlancerApp
 */
angular.module('getlancerApp.Job')
    .controller('JobsEditController', ['$scope', '$rootScope', '$filter', 'flash', '$state', 'JobsEdit', 'JobTypeFactory', 'JobCategoriesFactory', 'JobSkillsFactory', 'JobSalaryTypeFactory', 'Upload', 'md5', '$timeout', 'JobAutocompleteUsers', 'ConstUserRole', function($scope, $rootScope, $filter, flash, $state, JobsEdit, JobTypeFactory, JobCategoriesFactory, JobSkillsFactory, JobSalaryTypeFactory, Upload, md5, $timeout, JobAutocompleteUsers, ConstUserRole) {
        /**
         * @ngdoc method
         * @name JobsEditController.init
         * @methodOf module.JobsEditController
         * @description
         * This method is used to init the function and variables
         */
        $scope.init = function() {
            $rootScope.header = $rootScope.settings.SITE_NAME + ' | ' + $filter("translate")("Edit Job");
            $scope.jobTypes();
            $scope.jobCategories();
            $scope.jobSkills();
            $scope.jobSalaryTypes();
            $scope.regex = 'http://';
            $scope.jobEdit();
        };
        $scope.exp_years = [];
        for (var i = 0; i <= 30; i++) {
           $scope.exp_years.push(i);
        }
        
        $scope.disable_zip = 'true';
        
        $scope.job = {};
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
                $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE) + parseInt(value);
                  },100);
            } else {
                 $scope.amount_find = false;
                  $timeout(function () {
                    $scope.amount_find = true;
                $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE) - parseInt(value);
                  },100);
            }
        };
        $scope.projectUrgentFeeAdd = function(value) {
            if ($scope.job.is_urgent) {
                 $scope.amount_find = false;
                  $timeout(function () {
                    $scope.amount_find = true;
                $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE) + parseInt(value);
                  },100);
            } else {
                 $scope.amount_find = false;
                  $timeout(function () {
                    $scope.amount_find = true;
                $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE) - parseInt(value);
                  },100);
            }
        };
        /**
         * @ngdoc method
         * @name JobsEditController.selectType
         * @methodOf module.JobsEditController
         * @description
         * This method is used For select the job type for eg: Full time , part time like that.
         */
        $scope.selectType = function(id) {
            $scope.job.job_type_id = id;
        }
        /**
         * @ngdoc method
         * @name JobsEditController.location
         * @methodOf module.JobsEditController
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
                        // $scope.disable_state = true;
                    }
                    if (value.types[0] === 'administrative_area_level_1') {
                        $scope.job.state.name = value.long_name;
                        // $scope.disable_state = true;
                    }
                    if (value.types[0] === 'country') {
                        $scope.job.country.iso_alpha2 = value.short_name;
                        //  $scope.disable_country = true;
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
                    $scope.job.address = $scope.place.name+" "+$scope.place.vicinity;
                    $scope.job.full_address = $scope.place.formatted_address;
                });
            }
        };
        /**
         * @ngdoc method
         * @name JobsEditController.submit
         * @methodOf module.JobsEditController
         * @description
         * This method is used to post the jobs
         */
        $scope.save_btn = false;
        $scope.submit = function(type, $valid) {
            if ($valid && !$scope.error_message) {
                $scope.save_btn = true;
                /* type: 1 -> Submit, type:2 -> Draft */
                if($scope.job_status != 4) {
                $scope.job.job_status_id = (parseInt(type) === 1) ? 2 : 1;
                }
                // $scope.tmp_skills = [];
                /* Due to webservice team issue doing this like they told did not change the api flow */
                if (angular.isDefined($scope.skill_select)) {
                    $scope.seperate_skills = [];
                    angular.forEach($scope.skill_select, function (value) {
                       $scope.seperate_skills.push(value.text);
                    });
                   $scope.job.skills = $scope.seperate_skills.toString();
                }
                // $scope.job.skills = $scope.tmp_skills;
                var flashMessage;
                if ($scope.place !== $scope.job.address) {
                    $scope.location();
                }
				if (angular.isUndefined($scope.job.user_id)) {
             		  $scope.job.user_id = ConstUserRole.Admin;
            	}
                $scope.job.last_date_to_apply = $filter('date')($scope.last_date_to_apply, "yyyy-MM-dd");
                delete $scope.job.job_listing_date_upto;
                delete $scope.job.logo_url;
                 if ($scope.job.company_website === null) {
                      delete $scope.job.company_website;
                 }
                JobsEdit.put($scope.job, function(response) {
                    $scope.save_btn = false;
                    $scope.response = response;
                    if ($scope.response.error.code === 0) {
                        flashMessage = $filter("translate")("Job updated successfully.");
                        if (type === 2) {
                        flash.set(flashMessage, 'success', false);
                        $state.reload();
                        $state.go('user_dashboard', {
                            status: 'draft',
                            type: 'my_jobs'
                        });
                    } else {
                             if (response.data.total_listing_fee > 0 && $scope.job_status !=4) {
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
                } else {
                        flashMessage = $filter("translate")($scope.response.error.message);
                        flash.set(flashMessage, 'error', false);
                         $scope.save_btn = false;
                    }
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
         * @name JobsEditController.jobTypes
         * @methodOf module.JobsEditController
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
         * @name JobsEditController.jobCategories
         * @methodOf module.JobsEditController
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
         * @name JobsEditController.jobSkills
         * @methodOf module.JobsEditController
         * @description
         * This method is used For select the job skills.
         */
        var params = {};
        params.limit = 'all';
        params.job_id = $state.params.id;
        $scope.jobSkills = function() {
            JobSkillsFactory.get(params,function(response) {
               $scope.skilles = response.data;
                   $scope.skills = [];
                   $scope.job.skill_select = [];
                 angular.forEach($scope.skilles, function (value) {
                         $scope.skills.push({
                            id: value.id,
                            text: value.name
                        });
                        /* here for select skill default */
                        if (selectedSkill !== "") {
                                $scope.job.skill_select.push({
                                    id: value.id,
                                    text: value.name
                                });
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
         * @name JobsEditController.jobSalaryTypes
         * @methodOf module.JobsEditController
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
         * @name JobsEditController.jobEdit
         * @methodOf module.JobsEditController
         * @description
         * This method is used For edit the jobs and re post the jobs.
         */
    var selectedSkill = [];
        $scope.jobEdit = function() {
			JobsEdit.get({
                id: $state.params.id
            }, function(response) {
                $scope.amount_find = true;
                $scope.data = response.data;
                $scope.job_status = response.data.job_status_id;
                $scope.skill_select = [];
				//delete response.data.user;
				$scope.job.user_id = response.data.user_id;
				$scope.job.username = response.data.user.username;
                $scope.job = response.data;
                $scope.place = response.data.address;
                $scope.job.salary_from = parseInt(response.data.salary_from);
                $scope.job.salary_to = parseInt(response.data.salary_to);
                $scope.job.salary_type_id = parseInt(response.data.salary_type_id);
                $scope.setDate(response.data.last_date_to_apply);
                $scope.selected = $scope.job.job_type_id;
                angular.forEach(response.data.job_skill, function (value) {
                            selectedSkill.push(value.skill.id);
                        });
                if ($rootScope.settings.LISTING_FEE_FOR_JOB){
                $rootScope.settings.JOB_TOTAL_FEE = $rootScope.settings.LISTING_FEE_FOR_JOB;
                }
                if ($scope.data.is_featured) {
                    $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE  ||0) + parseInt($rootScope.settings.FEATURED_FEE_FOR_JOB || 0);
                }
                if ($scope.data.is_urgent) {
                    $rootScope.settings.JOB_TOTAL_FEE = parseInt($rootScope.settings.JOB_TOTAL_FEE  ||0) + parseInt($rootScope.settings.URGENT_FEE_FOR_JOB || 0);
                }
                $scope.job.skill_select = [];
                        angular.forEach(response.data.job_skill, function(value) {
                            $scope.job.skill_select.push({
                                'text': value.skill.name
                            });
                        });
				angular.element(document.getElementsByClassName('btn dropdown-toggle')).prop('title', $scope.job.username);
				angular.element('.filter-option').text($scope.job.username);
                if (angular.isDefined($scope.job.attachment)) {
                    if ($scope.job.attachment !== null) {
                        $scope.job.logo_url = 'images/medium_thumb/Job/' + $scope.job.id + '.' + md5.createHash('Job' + $scope.job.id + 'png' + 'medium_thumb') + '.png';
                    } else {
                        $scope.job.logo_url = 'images/no-image.png';
                    }
                } else {
                    $scope.job.logo_url = 'images/no-image.png';
                }
				JobAutocompleteUsers.get(function(response) {
					if (parseInt(response.error.code) === 0) {
						$scope.employerUser = [];
						$scope.employerUsers = response.data;
						$scope.job.user_select = [];
						angular.forEach($scope.employerUsers, function(value) {
							$scope.employerUser.push({
								id: value.id,
								text: value.username
							});
							});
						} else {
							console.log('User Error');
						}
					}, function(error) {
						console.log('Users Error', error);
				});
				
                $scope.job.job_category_id = [];
                $scope.job.job_category_id = $scope.job.job_category.id;
                delete $scope.job.job_category;
                $scope.job.job_status_id = $scope.job.job_status.id;
                delete $scope.job.job_status;
                $scope.job.job_type_id = $scope.job.job_type.id;
                delete $scope.job.job_type;
                delete $scope.job.attachment;
                delete $scope.job.job_apply;
                delete $scope.job.flag;
                delete $scope.job.job_skill;
                delete $scope.job.salary_type;
            });
        };
        /**
         * @ngdoc method
         * @name JobsEditController.upload
         * @methodOf module.JobsEditController
         * @description
         * This method is used to upload the company logo image.
         */
        $scope.upload = function(file) {
            $scope.is_image_error = false;
            Upload.upload({
                    url: '/api/v1/attachments?class=Job',
                    data: {
                        file: file,
                    }
                })
                .then(function(response) {
                    if (response.data.error.code === 0) {
                    $scope.job.image = response.data.attachment;
                    $scope.job.logo_url = 'images/small_thumb/Job/' + $scope.job.id + '.' + md5.createHash('Job' + $scope.job.id + 'png' + 'small_thumb') + '.png';
                    $scope.error_message = '';
                          } else {
                               $scope.error_message = response.data.error.message;
                          }
                });
        };
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
        $scope.toggleMin = function() {
            $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
            $scope.dateOptions.minDate = new Date();
        };
        $scope.toggleMin();
        $scope.open1 = function() {
            $scope.popup1.opened = true;
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
        $scope.setDate = function(date) {
            $scope.last_date_to_apply = new Date(date);
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
        $scope.init();
  }]);