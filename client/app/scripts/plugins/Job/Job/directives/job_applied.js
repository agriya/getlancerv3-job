'use strict';
/**
 * @ngdoc directive
 * @name getlancerApp.directive:Job
 * @description
 * # JOB
 */
angular.module('getlancerApp.Job')
    .directive('jobsAppliedDashboard', function() {
        return {
            templateUrl: 'scripts/plugins/Job/Job/views/default/job_applied_dashboard.html',
            restrict: 'E',
            controller: 'JobsAppliedDashboardController'
        };
    })
    .directive('jobsMyJobDashboard', function() {
        return {
            templateUrl: 'scripts/plugins/Job/Job/views/default/my_jobs_dashboard.html',
            restrict: 'E',
            controller: 'MyJobsDashboardController'
        };
    })
    .directive('jobHomeSkills', function (JobSkillsFactory) {
        return {
            restrict: 'EA',
            replace: true,
            templateUrl: 'scripts/plugins/Job/Job/views/default/job_home_skills.html',
            link: function postLink(scope, element, attrs) {
                var params = {
                    limit: 30,
                    sort: 'name',
                    sortby: 'DSC',
                    field: 'id,name,slug,description'
                };
                JobSkillsFactory.get(params, function(response) {
                  scope.job_skills = response.data;
                });
            }
        }
    })
    .directive('featuredJobsHome', function (JobsFactory, md5) {
        return {
            restrict: 'EA',
            replace: true,
            templateUrl: 'scripts/plugins/Job/Job/views/default/featured_jobs_home_block.html',
            link: function postLink(scope, element, attrs) {
                var params = {
                    limit: 8,
                    sortby: 'DSC',
                    is_featured:'1'
                };
                JobsFactory.get(params, function(response) {
                  scope.featured_jobs = response.data;
                 angular.forEach(scope.featured_jobs, function(value) {
                    if (angular.isDefined(value.attachment) && value.attachment != null) {
                        var hash = md5.createHash(value.attachment.class + value.attachment.foreign_id + 'png' + 'medium_thumb');
                        value.featured_job_image = 'images/medium_thumb/' + value.attachment.class + '/' + value.attachment.foreign_id + '.' + hash + '.png';
                    } else {
                        value.featured_job_image = 'images/no-image.png';
                    }
                });
                });
            }
        }
    });