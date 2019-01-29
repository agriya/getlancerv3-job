'use strict';
/**
 * @ngdoc  service
 * @name getlancerApp.Job.Factory
 * @description
 * Factory in the  getlancerApp.Job
 */
angular.module('getlancerApp.Job.ResumeRating')
    .factory('ResumeRatingGetFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_apply/:id/resume_ratings', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('ResumeRatingAddFactory', ['$resource', function($resource) {
        return $resource('/api/v1/resume_ratings', {}, {
            post: {
                method: 'POST'
            }
        });
  }])
    .factory('ResumeRatingFactory', ['$resource', function($resource) {
        return $resource('/api/v1/resume_ratings/:id', {
            id: '@id'
        }, {
            get: {
                method: 'GET'
            },
            delete: {
                method: 'DELETE'
            },
            put: {
                method: 'PUT'
            }
        });
  }]);