'use strict';
/**
 * @ngdoc  service
 * @name getlancerApp.Job.Factory
 * @description
 * Factory in the  getlancerApp.Job
 */
angular.module('getlancerApp.Job')
    .factory('JobsFactory', ['$resource', function($resource) {
        return $resource('/api/v1/jobs', {}, {
            get: {
                method: 'GET'
            },
            post: {
                method: 'POST'
            }
        });
  }])
    .factory('JobsEdit', ['$resource', function($resource) {
        return $resource('/api/v1/jobs/:id', {
            id: '@id'
        }, {
            get: {
                method: 'GET'
            },
            put: {
                method: 'PUT'
            }
        });
  }])
    .factory('JobTypeFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_types', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobCategoriesFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_categories', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobSalaryTypeFactory', ['$resource', function($resource) {
        return $resource('/api/v1/salary_types', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobSkillsFactory', ['$resource', function($resource) {
        return $resource('/api/v1/skills', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('MyJobsFactory', ['$resource', function($resource) {
        return $resource('/api/v1/me/jobs', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobStatusFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_statuses', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobsAppliesFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_applies', {}, {
            post: {
                method: 'POST'
            }
        });
  }])
    .factory('JobsReportFactory', ['$resource', function($resource) {
        return $resource('/api/v1/flags', {}, {
            post: {
                method: 'POST'
            }
        });
  }])
    .factory('flagCategories', ['$resource', function($resource) {
        return $resource('/api/v1/flag_categories', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('jobAppliedFactory', ['$resource', function($resource) {
        return $resource('/api/v1/me/job_applies', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobAppliesListFactory', ['$resource', function($resource) {
        return $resource('/api/v1/employer/:id/job_applies', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobApplyStatusFactory', ['$resource', function($resource) {
        return $resource('/api/v1/job_apply_statuses', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('JobApplyStatus', ['$resource', function($resource) {
        return $resource('/api/v1/job_applies/:id', {
            id: '@id'
        }, {
            get: {
                method: 'GET'
            },
            put: {
                method: 'PUT'
            }
        });
  }])
    .factory('JobAppliesStatus', ['$resource', function($resource) {
        return $resource('/api/v1/employer/:id/job_applies/stats', {
            id: '@id'
        }, {
            get: {
                method: 'get'
            }
        });
  }])
    .factory('JobStatusCountFactory', ['$resource', function($resource) {
        return $resource('/api/v1/employer/:id/jobs/stats', {
            id: '@id'
        }, {
            get: {
                method: 'get'
            }
        });
  }])
    .factory('TransactionList', ['$resource', function($resource) {
        return $resource('/api/v1/payment_gateways/list', {}, {
            get: {
                method: 'GET'
            }
        });
  }])
    .factory('jobApplyClick', ['$resource', function($resource) {
        return $resource('/api/v1/job_apply_clicks', {}, {
            post: {
                method: 'POST'
            }
        });
  }])
	.factory('JobAutocompleteUsers', ['$resource', function($resource) {
        return $resource('/api/v1/users?type=employer', {}, {
            get: {
                method: 'GET'
            }
        });
  }]);