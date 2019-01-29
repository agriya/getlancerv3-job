<?php
/**
 * Plugin
 *
 * PHP version 5
 *
 * @category   PHP
 * @package    GetlancerV3
 * @subpackage Core
 * @author     Agriya <info@agriya.com>
 * @copyright  2018 Agriya Infoway Private Ltd
 * @license    http://www.agriya.com/ Agriya Infoway Licence
 * @link       http://www.agriya.com
 */
/**
 * GET resumeRatingsGet
 * Summary: Fetch all resume ratings
 * Notes: Returns all resume ratings from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/resume_ratings', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $results = array();
    try {
        $enabledIncludes = array(
            'user',
            'job'
        );
        $resumeRatings = Models\ResumeRating::with($enabledIncludes)->Filter($queryParams)->paginate(PAGE_LIMIT)->toArray();
        $data = $resumeRatings['data'];
        unset($resumeRatings['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $resumeRatings
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListResumeRating'));
/**
 * POST resumeRatingsPost
 * Summary: Creates a new resume rating
 * Notes: Creates a new resume rating
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/resume_ratings', function ($request, $response, $args) {
    global $authUser;
    $args = $request->getParsedBody();
    $resumeRating = new Models\ResumeRating($args);
    $result = array();
    try {
        $validationErrorFields = $resumeRating->validate($args);
        if (empty($validationErrorFields)) {
            $resumeRating->user_id = $authUser['id'];
            if ($resumeRating->save()) {
                $enabledIncludes = array(
                    'user',
                    'job'
                );
                $resumeRating = Models\ResumeRating::with($enabledIncludes)->find($resumeRating->id);
                $result = $resumeRating->toArray();
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Resume rating could not be added. Access Denied.', '', 1);
            }
        } else {
            return renderWithJson($result, 'Resume rating could not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Resume rating could not be added. Please, try again.', '', 1);
    }
})->add(new ACL('canCreateResumeRating'));
/**
 * DELETE resumeRatingsResumeRatingIdDelete
 * Summary: Delete resume rating
 * Notes: Deletes a single resume rating based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/resume_ratings/{resumeRatingId}', function ($request, $response, $args) {
    $resumeRating = Models\ResumeRating::find($request->getAttribute('resumeRatingId'));
    $result = array();
    try {
        if (!empty($resumeRating)) {
            if ($resumeRating->delete()) {
                $result = array(
                    'status' => 'success',
                );
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Resume rating could not be deleted. Access Denied.', '', 1);
            }
        } else {
            return renderWithJson($result, 'No record found', '', 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Resume rating could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteResumeRating'));
/**
 * GET resumeRatingsResumeRatingIdGet
 * Summary: Fetch resume rating
 * Notes: Returns a resume rating based on a single ID
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/resume_ratings/{resumeRatingId}', function ($request, $response, $args) {
    $result = array();
    global $authUser;
    $enabledIncludes = array(
        'user',
        'job'
    );
    $resumeRating = Models\ResumeRating::with($enabledIncludes)->find($request->getAttribute('resumeRatingId'));
    if (!empty($resumeRating)) {
        if (Models\ResumeRating::resumeRatingPermission($resumeRating, $authUser)) {
            $result['data'] = $resumeRating;
            return renderWithJson($result);
        } else {
            return renderWithJson($result, 'No record found. Access Denied', '', 1);
        }
    } else {
        return renderWithJson($result, 'No record found', '', 1);
    }
})->add(new ACL('canViewResumeRating'));
/**
 * PUT resumeRatingsResumeRatingIdPut
 * Summary: Update resume rating by its id
 * Notes: Update resume rating by its id
 * Output-Formats: [application/json]
 */
$app->PUT('/api/v1/resume_ratings/{resumeRatingId}', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $resumeRating = Models\ResumeRating::find($request->getAttribute('resumeRatingId'));
    $resumeRating->fill($args);
    $result = array();
    try {
        $validationErrorFields = $resumeRating->validate($args);
        if (empty($validationErrorFields)) {
            if ($resumeRating->save()) {
                $enabledIncludes = array(
                    'user',
                    'job'
                );
                $resumeRating = Models\ResumeRating::with($enabledIncludes)->find($resumeRating->id);
                $result = $resumeRating->toArray();
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Resume rating could not be updated. Access Denied.', '', 1);
            }
        } else {
            return renderWithJson($result, 'Resume rating could not be updated. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Resume rating could not be updated. Please, try again.', '', 1);
    }
})->add(new ACL('canUpdateResumeRating'));
/**
 * GET jobApplyJobApplyIdResumeRatingsGet
 * Summary: Fetch all resume ratings
 * Notes: Returns all resume ratings from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_apply/{jobApplyId}/resume_ratings', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $results = array();
    try {
        $count = PAGE_LIMIT;
        if (!empty($queryParams['limit'])) {
            $count = $queryParams['limit'];
        }
        $enabledIncludes = array(
            'user'
        );
        $resumeRatings = Models\ResumeRating::with($enabledIncludes)->where('job_apply_id', $request->getAttribute('jobApplyId'))->Filter($queryParams)->paginate($count)->toArray();
        $data = $resumeRatings['data'];
        unset($resumeRatings['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $resumeRatings
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobApplyResumeRating'));
