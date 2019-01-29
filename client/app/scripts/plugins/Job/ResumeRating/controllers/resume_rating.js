'use strict';
/**
 * @ngdoc function
 * @name getlancerApp.Job.controller:ResumeRatingController
 * @description
 * # ResumeRatingController
 * Controller of  the getlancerApp.Job
 */
angular.module('getlancerApp.Job.ResumeRating')
    .controller('ResumeRatingController', function($scope, $rootScope, $filter, md5, $state, DateFormat, ResumeRatingGetFactory, ResumeRatingAddFactory, ResumeRatingFactory, $cookies, StarCount, flash, $location) {
        $scope.DateFormat = DateFormat;

        function init() {
            $scope.StarCount = StarCount;
            $scope.DateFormat = DateFormat;
            $scope.comment = {};
            $scope.resumeRatingGet();
            $scope.is_rating_req = false;
            $scope.isEdit = false;
        }
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.resumeRatingDelete
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is delete Resume rating comment
         */
        $scope.resumeRatingDelete = function(id) {
            var flashMessage = "";
            /* [ Checks the user_id and login in auth user id ] */
            if ($rootScope.user !== null && $rootScope.user !== undefined) {
                swal({ //jshint ignore:line
                    title: $filter("translate")("Are you sure you want to delete?"),
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    closeOnConfirm: true,
                    animation:false,
                }).then(function (isConfirm) {
                    if (isConfirm) {
                        var params = {};
                        params.id = id;
                        ResumeRatingFactory.delete(params, function(response) {
                            if (response.error.code === 0) {
                                flashMessage = $filter("translate")("Comment deleted successfully.");
                                flash.set(flashMessage, 'success', false);
                            } else {
                                flashMessage = $filter("translate")(response.error.message);
                                flash.set(flashMessage, 'error', false);
                            }
                            $state.go('applies_jobs_view', {}, {
                                reload: true
                            });
                        });
                    }
                });
            }
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.resumeRatingGet
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is get Resume rating listing
         */
        $scope.resumeRatingGet = function() {
            ResumeRatingGetFactory.get({
                id: $state.params.id
            }, function(response) {
                $scope.resumeRatings = response.data;
                angular.forEach($scope.resumeRatings, function(value) {
                    if (angular.isDefined(value.user.attachment)) {
                        if (value.user.attachment !== null) {
                            $scope.resumeRatings.logo_url = 'images/normal_thumb/UserAvatar/' + value.user.attachment.foreign_id + '.' + md5.createHash('UserAvatar' + value.user.attachment.foreign_id + 'png' + 'normal_thumb') + '.png';
                        } else {
                            $scope.resumeRatings.logo_url = 'images/default.png';
                        }
                    } else {
                        $scope.resumeRatings.logo_url ='images/default.png';
                    }
                });
            });
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.commentAdd
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is post resume rating comment
         */
        $scope.save_btn = false;
        $scope.commentAdd = function($valid) {
            if ($valid) {
                $scope.save_btn = true;
                if (parseInt($scope.comment.rating_select) > 0) {
                    $scope.is_rating_req = false;
                    var post_params = {
                        job_id: $scope.job_apply_status_view.job_id,
                        job_apply_id: $state.params.id,
                        rating: $scope.comment.rating_select,
                        comment: $scope.comment.resumeComment
                    };
                    var flashMessage = "";
                    if (!$scope.isEdit) {
                        ResumeRatingAddFactory.post(post_params, function(response) {
                            $scope.response = response;
                            if ($scope.response.error.code === 0) {
                                $scope.comment = {};
                                $scope.applyComment.$setPristine();
                                $scope.applyComment.$setUntouched();
                                flashMessage = $filter("translate")("Comment added successfully.");
                                flash.set(flashMessage, 'success', false);
                                $state.reload();
                            } else {
                                flashMessage = $filter("translate")($scope.response.error.message);
                                flash.set(flashMessage, 'error', false);
                                $scope.save_btn = false;
                            }
                        });
                    } else {
                        post_params.id = $scope.reviewId;
                        ResumeRatingFactory.put(post_params, function(response) {
                            $scope.response = response;
                            if ($scope.response.error.code === 0) {
                                $scope.isEdit = false;
                                $scope.comment = {};
                                $scope.applyComment.$setPristine();
                                $scope.applyComment.$setUntouched();
                                flashMessage = $filter("translate")("Comment updated successfully.");
                                flash.set(flashMessage, 'success', false);
                                $state.reload();
                            } else {
                                flashMessage = $filter("translate")($scope.response.error.message);
                                flash.set(flashMessage, 'error', false);
                            }
                        });
                    }
                } else {
                    $scope.is_rating_req = true;
                }
            }
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.starCount
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is get star count value for resume rating post function
         */
        $scope.starCount = function(starCountVal) {
            $scope.comment.rating_select = starCountVal;
        };
        /**
         * @ngdoc method
         * @name jobsAppliesStatusController.editReview
         * @methodOf module.jobsAppliesStatusController
         * @description
         * This method is update resume rating comment
         */
        $scope.editReview = function(reviewId) {
            $scope.isEdit = true;
            $scope.reviewId = reviewId;
            ResumeRatingFactory.get({
                id: $scope.reviewId
            }, function(resp) {
                $scope.comment = {};
                $scope.comment.rating_select = resp.data.rating;
                $scope.comment.resumeComment = resp.data.comment;
            });
        };
        init();
    });