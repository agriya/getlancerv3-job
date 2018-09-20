/**
 * getlancerApp.Job - Angular Framework
 * Angula Version 1.5.3
 * @category   Js
 * @package    REST
 * @Framework  Angular
 * @author     Mugundhan Asokan
 * @email      a.mugundhan@agriya.in
 * @copyright  2016 Agriya
 * @license    http://www.agriya.com/ Agriya Licence
 * @link       http://www.agriya.com
 * @since      2016-12-22
 */
/*globals $:false */
'use strict';
/**
 * @ngdoc overview
 * @name getlancerApp.Job
 * @description
 * # getlancerApp.Job
 *
 * Main module of the application.
 */
angular.module('getlancerApp.Job', [
    'ngResource',
    'ngSanitize',
    'satellizer',
    'ngAnimate',
    'ui.bootstrap',
    'ui.bootstrap.datetimepicker',
    'ui.router',
    'angular-growl',
    'google.places',
    'angular.filter',
    'ngCookies',
    'angular-md5',
    'http-auth-interceptor',
    'angulartics',
    '720kb.socialshare',
    'pascalprecht.translate',
    'angulartics.google.analytics',
    'tmh.dynamicLocale',
    'ngFileUpload',
    'angular-loading-bar',
    'ngAnimate',
    'checklist-model',
    'angularjs-dropdown-multiselect',
    'ngTagsInput',
    'textAngular',
    'getlancerApp.Job.JobFlag'
  ])
  /**
   * @ngdoc constant
   * @name getlancerApp.Job.constant
   * @description
   * APP Constant
   */
  .constant('DateFormat', {
    view: 'MMM dd',
    created_12: 'yyyy-MM-dd HH:mm',
    created_24: 'yyyy-MM-dd hh:mma',
    created: 'yyyy-MM-dd',
    title: 'MMMM dd, yyyy hh:mma (EEEE)'
  })
  .constant('SalaryType', {
    '1': 'Per Annum',
    '2': 'Per Month',
    '3': 'Per Week',
    '4': 'Per Day',
    '5': 'Per Hour',
    '6': 'Per Job'
  })
  .constant('StarCount', {
    Jobs: 5,
    JobsResume: 5,
  })
  .constant('FileFormat', {
    image: ['jpg', 'gif', 'png', 'jpeg'],
    resume: ['doc', 'docx', 'pdf', 'rtf', 'odt', 'docm', 'dot', 'txt']
  })
  /**
   * @ngdoc function
   * @name getStorgae
   * @methodOf global getStorgae
   * @description
   * @param {string, string} type, val
   * This funciton is used to get the localstorage.
   * @returns {string} local stored string
   */
  .config(function ($stateProvider, $urlRouterProvider) {
    var getToken = {
      'TokenServiceData': function (TokenService, $q) {
        return $q.all({
          AuthServiceData: TokenService.promise,
          SettingServiceData: TokenService.promiseSettings
        });
      }
    };
    $urlRouterProvider.otherwise('/');
    $stateProvider.state('jobs_add', {
        url: '/jobs/add',
        templateUrl: 'scripts/plugins/Job/Job/views/default/jobs_add.html',
        resolve: getToken
      })
      .state('jobs_edit', {
        url: '/jobs/edit/:id',
        templateUrl: 'scripts/plugins/Job/Job/views/default/jobs_edit.html',
        resolve: getToken
      })
      .state('jobs_view', {
        url: '/jobs/view/:id',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_view.html',
        resolve: getToken
      })
      .state('job_payment', {
        url: '/jobs/order/:id',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_payment.html',
        resolve: getToken
      })
      .state('jobs', {
        url: '/jobs?q&page&job_categories&job_types&skills',
        templateUrl: 'scripts/plugins/Job/Job/views/default/jobs.html',
        resolve: getToken
      })
      .state('my_jobs', {
        url: '/jobs/list',
        templateUrl: 'scripts/plugins/Job/Job/views/default/my_jobs.html',
        resolve: getToken,
      })
      .state('applied_jobs', {
        url: '/job_applies/type/myappliedjobs',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_applied.html',
        resolve: getToken,
      })
      .state('applies_jobs', {
        url: '/jobs/applied/resumes?job_id=:id',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_applies_list.html',
        resolve: getToken,
      })
      .state('applies_jobs_view', {
        url: '/job_apply/:id',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_applies_status_view.html',
        resolve: getToken,
      })
      .state('download_resume', {
        url: '/download/:class/:id/:file_name',
        //   templateUrl: 'scripts/plugins/Job/Job/views/default/job_applies_list.html',
        resolve: getToken,
      })
      .state('applies_resume', {
        url: '/jobs/job_applies/:id/:slug',
        templateUrl: 'scripts/plugins/Job/Job/views/default/job_resume_applies.html',
        controller: 'jobsViewController',
        resolve: getToken,
      });
  })
  /**
   * @ngdoc filter
   * @name getlancerApp.Job.date_format
   * @param {date, string} date, format
   * @description
   * For change the date format in html view page.
   */
  .filter('date_format', function ($filter) {
    return function (input, format) {
      return $filter('date')(new Date(input), format);
    };
  })
  /**
   * @ngdoc directive
   * @name getlancerApp.Job.downloadFile
   * @param {object} value
   * @description
   * For download the files process (Resumes, Image). 
   * @author mugundhan_352at15
   *<span download-file attachment={{attachment}} downloadlable="Dowload"> </span>
   */
  .directive('downloadFile', function (md5, $location) {
    var directive = {
      restrict: 'EA',
      replace: true,
      template: '<a href="{{downloadUrl}}" class="cursor" target="_blank" title="download"> <i class="fa fa-download fa-fw"> </i> <span>{{"Download"|translate}}</span> </a>',
      scope: {
        attachment: '@',
        downloadlable: '@'
      },
      link: function (scope, element, attrs) {
        scope.attachment = JSON.parse(scope.attachment);
        var download_file = md5.createHash(scope.attachment.class + scope.attachment.foreign_id + 'docdownload') + '.doc';
        scope.downloadUrl = $location.protocol() + '://' + $location.host() + '/download/' + scope.attachment.class + '/' + scope.attachment.foreign_id + '/' + download_file;
        /* For check the download label is undeifed or not to fill the default text */
        if (scope.downloadlable === undefined) {
          scope.downloadlable = "Download";
        }
      },
    };
    return directive;
  })
  .directive('jobsHomeBlock', function () {
    return {
      restrict: 'E',
      replace: true,
      templateUrl: 'scripts/plugins/Job/Job/views/default/job_home_block.html',
      controller: function ($scope, $rootScope, md5, JobsFactory) {
        $scope.job = function () {
          $scope.loader = true;
          JobsFactory.get({
            limit: 4,
            sortby: 'desc'
          }, function (response) {
            if (angular.isDefined(response.data)) {
              $scope.jobs = response.data;
              /* Here need to check the attachment single or multiple concept */
              angular.forEach($scope.jobs, function (value) {
                if (angular.isDefined(value.attachment)) {
                  if (value.attachment !== null) {
                    value.logo_url = 'images/medium_thumb/Job/' + value.id + '.' + md5.createHash('Job' + value.id + 'png' + 'medium_thumb') + '.png';
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
          $scope.loader = false;
        };
        $scope.job();
      }
    }
  })
  /**
   * @ngdoc directive
   * @name getlancerApp.Job.inputStars
   * @param {object} value
   * @description
   * For using the star rating.  
   */

  .filter('trustedhtml', function ($sce) {
    return function (html) {
      return $sce.trustAsHtml(html);
    };
  });
/**
 * @ngdoc function
 * @name checkFileFormat
 * @methodOf global checkFileFormat
 * @description
 * @param {object, array} type, val
 * This funciton is used to check the upload file validation.
 * @returns {boolean}
 */
//jshint unused:false
function checkFileFormat(file, validFormats) {
  if (file) {
    var value = file.name;
    var ext = value.substring(value.lastIndexOf('.') + 1)
      .toLowerCase();
    return validFormats.indexOf(ext) !== -1;
  } else {
    return false;
  }
}
