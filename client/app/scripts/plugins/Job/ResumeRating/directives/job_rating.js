'use strict';
/**
 * @ngdoc directive
 * @name getlancerApp.directive:QuoteServiceFaqs
 * @description
 * # QuoteServiceFaqs
 */
angular.module('getlancerApp.Job.ResumeRating')
    .directive('resumeRatingView', function() {
        return {
            templateUrl: 'scripts/plugins/Job/ResumeRating/views/default/job_resume_rating_view.html',
            restrict: 'E',
            controller: 'ResumeRatingController'
        };
    });