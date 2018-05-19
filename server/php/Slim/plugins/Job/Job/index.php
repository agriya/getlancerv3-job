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
 * POST Jobs Post
 * Summary: Job Post
 * Notes: Post Jobs
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/jobs', function ($request, $response, $args) {
    global $authUser, $_server_domain_url;
    $amount = LISTING_FEE_FOR_JOB;
    $args = $request->getParsedBody();
    $jobs = new Models\Job($args);
    $job = array(
        'payment_gateway_id',
        'gateway_id',
        'buyer_name',
        'buyer_email',
        'buyer_address',
        'buyer_city',
        'buyer_state',
        'buyer_country_iso2',
        'buyer_phone',
        'buyer_zip_code',
        'credit_card_code',
        'credit_card_expire',
        'credit_card_name_on_card',
        'credit_card_number',
        'skills'
    );
    $result = array();
    unset($jobs->location);
    unset($jobs->city_name);
    unset($jobs->state_name);
    unset($jobs->country);
    unset($jobs->image);
    unset($jobs->company_image);
    if (!in_array($authUser->role_id, [\Constants\ConstUserTypes::User, \Constants\ConstUserTypes::Employer]) && $authUser->role_id != \Constants\ConstUserTypes::Admin) {
        return renderWithJson($result, 'Freelancer could not be added the job.', '', 1);
    }
    try {
        if ($args['apply_via'] != 'via_company') {
            unset($args['job_url']);
        }
        $validationErrorFields = $jobs->validate($args);
        if (!empty($validationErrorFields)) {
            $validationErrorFields = $validationErrorFields->toArray();
        }        
        //get country, state and city ids
        if (!empty($args['full_address']) && empty($validationErrorFields)) {
            if (!empty($args['country']['iso_alpha2'])) {
                $jobs->country_id = findCountryIdFromIso2($args['country']['iso_alpha2']);
                if (!empty($args['state']['name'])) {
                    $jobs->state_id = findOrSaveAndGetStateId($args['state']['name'], $jobs->country_id);
                }
                if (!empty($args['city']['name'])) {
                    $jobs->city_id = findOrSaveAndGetCityId($args['city']['name'], $jobs->country_id, $jobs->state_id);
                }
            } else {
                $validationErrorFields['required'] = array();
                array_push($validationErrorFields['required'], 'Country');
            }
        }
        if (empty($validationErrorFields['required'])) {
            unset($validationErrorFields['required']);
        }
        unset($jobs->country);
        unset($jobs->state);
        unset($jobs->city);
        unset($jobs->company);
        if (empty($validationErrorFields)) {
            if (($args['apply_via'] == 'via_company') && !isset($args['job_url'])) {
                $validationErrorFields['job_url'] = 'The job url field is required.';
                return renderWithJson($result, 'Job could not be added. Please, try again.', $validationErrorFields, 1);
            }
            if (!empty($args['is_featured'])) {
                $amount+= FEATURED_FEE_FOR_JOB;
            }
            if (!empty($args['is_urgent'])) {
                $amount+= URGENT_FEE_FOR_JOB;
            }
            $jobs->slug = Inflector::slug(strtolower($jobs->title), '-');
            $jobs->user_id = $authUser['id'];
            if ($authUser['role_id'] == \Constants\ConstUserTypes::Admin && !empty($args['user_id'])) {
                $jobs->user_id = $args['user_id'];
            }
            $jobs->ip_id = saveIp();
            $jobs->total_listing_fee = $amount;
            $jobs->job_status_id = \Constants\JobStatus::Draft;
            if (!empty($args['job_status_id'])) {
                $jobs->job_status_id = $args['job_status_id'];
            }
            $jobs->save();
            if (!empty($args['image'])) {
                saveImage('Job', $args['image'], $jobs->id);
            }  
            if (!empty($args['image_data'])) {
                saveImageData('Job', $args['image_data'], $jobs->id);
            }          
            // Add in the Job Skills table
            if (!empty($args['skills']) && $jobs->id) {
                $skills = explode(',', $args['skills']);
                foreach ($skills as $skill) {
                    $newSkills = Models\Skill::where('name', $skill)->first();
                    if(empty($newSkills)) {
                        $newSkills = new Models\Skill;
                        $newSkills->name = $skill;
                        $newSkills->slug = Inflector::slug(strtolower($skill), '-');
                        $newSkills->save();
                    }
                    $jobsSkills = new Models\JobsSkill;
                    $jobsSkills->skill_id = $newSkills->id ;

                    $jobsSkills->job_id = $jobs->id;
                    $jobsSkills->save();
                }
            }
            if (!empty($args['payment_gateway_id'])) {
                $args['name'] = $args['title'];
                $args['original_price'] = $args['amount'] = $amount;
                $args['id'] = $jobs->id;
                $args['user_id'] = $authUser->id;
                $args['notify_url'] = $_server_domain_url . '/ipn/process_ipn/Job/' . $jobs->id . '/' . md5(SECURITY_SALT . $jobs->id . SITE_NAME);
                $args['success_url'] = $_server_domain_url . '/jobs?error_code=0';
                $args['cancel_url'] = $_server_domain_url . '/jobs?error_code=512';
                $result = Models\Payment::processPayment($jobs->id, $args, 'Job');
            }
            if ($amount <= 0 && $jobs->job_status_id != \Constants\JobStatus::Draft) {
                if (IS_NEED_ADMIN_APPROVAL_FOR_NEW_JOBS) {
                    $jobs->job_status_id = \Constants\JobStatus::PendingApproval;
                } else {
                    $jobs->job_status_id = \Constants\JobStatus::Open;                    
                    $employerDetails = getUserHiddenFields($jobs->user_id);
                    $emailFindReplace = array(
                        '##USERNAME##' => ucfirst($employerDetails->username) ,
                        '##JOB_NAME##' => ucfirst($jobs->title),
                        '##JOB_URL##' => $_server_domain_url . '/jobs/' . $jobs->id . '/' . $jobs->title 
                    );
                    sendMail('Job Published Notification', $emailFindReplace, $employerDetails->email);                    
                }
                $jobs->is_paid = 1;
                $jobs->update();
            } elseif ($jobs->job_status_id != \Constants\JobStatus::Draft) {
                $jobs->job_status_id = \Constants\JobStatus::PaymentPending;
                $jobs->update();
            }
            $followers = Models\Follower::where('foreign_id', $jobs->user_id)->where('class', 'User')->get();
            $employerDetails = getUserHiddenFields($jobs->user_id);
            foreach ($followers as $follower) {
                $userDetails = getUserHiddenFields($follower->user_id);
                $emailFindReplace = array(
                    '##USERNAME##' => ucfirst($userDetails->username) ,
                    '##FAV_USERNAME##' => ucfirst($employerDetails->username) ,
                    '##JOB_NAME##' => ucfirst($jobs->title),
                    '##JOB_LINK##' => $_server_domain_url . '/jobs/' . $jobs->id . '/' . $jobs->title 
                );
                sendMail('New job opened', $emailFindReplace, $userDetails->email);                 
            }                             
            $result['data'] = $jobs->toArray();
            return renderWithJson($result);
        } else {
            return renderWithJson($result, ' Job could not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job could not be added. Please, try again.', '', 1);
    }
})->add(new ACL('canCreateJob'));
/**
 * GET Jobs Get
 * Summary: Fetch all Jobs
 * Notes: Returns all Jobs from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/jobs', '_getJobs');
$app->GET('/api/v1/me/jobs', function ($request, $response, $args) {
    global $authUser;
    if (!empty($authUser)) {
        $result = array();
        $queryParams = $request->getQueryParams();
        $count = PAGE_LIMIT;
        if (!empty($queryParams['limit'])) {
            $count = $queryParams['limit'];
        }
        $enabledIncludes = array(
            'user',
            'job_category',
            'job_type',
            'job_status',
            'job_skill',
            'attachment'
        );
        $jobs = Models\Job::with($enabledIncludes)->where('user_id', $authUser['id']);
        if (!empty($queryParams['job_status_id'])) {
            $jobs = $jobs->where('job_status_id', $queryParams['job_status_id']);
        }
        $jobs = $jobs->Filter($queryParams)->paginate($count)->toArray();
        if (!empty($jobs)) {
            $data = $jobs['data'];
            unset($jobs['data']);
            $result = array(
                'data' => $data,
                '_metadata' => $jobs
            );
            return renderWithJson($result);
        } else {
            return renderWithJson($result, 'No record found', '', 1);
        }
    } else {
        return renderWithJson($result, 'Your not have jobs', '', 1);
    }
})->add(new ACL('canUserViewJob'));
$app->GET('/api/v1/jobs/{jobId}', '_getJobs');
/**
 * DELETE Jobs JobIdDelete
 * Summary: Delete Jobs
 * Notes: Deletes a single Job based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/jobs/{jobId}', function ($request, $response, $args) {
    $jobs = Models\Job::find($request->getAttribute('jobId'));
    $result = array();
    if (empty($jobs)) {
        return renderWithJson($result, 'Jobs not Found.', '', 1);
    }
    try {
        $job_category_id = $jobs->job_category_id;
        $user_id = $jobs->user_id;
        if ($jobs->delete()) {
            Models\JobsSkill::where('job_id', $request->getAttribute('jobId'))->get()->each(function ($jobsSkill) {
                $jobsSkill->delete();
            });
            $result = array(
                'status' => 'success',
            );
            return renderWithJson($result);
        } else {
            return renderWithJson($result, 'Jobs could not be deleted. Access Denied.', '', 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Jobs could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteJob'));
/**
 * PUT Jobs JobIdPut
 * Summary: Update Jobs details
 * Notes: Update Jobsdetails.
 * Output-Formats: [application/json]
 */
$app->PUT('/api/v1/jobs/{jobId}', function ($request, $response, $args) {
    global $authUser, $_server_domain_url;
    $args = $request->getParsedBody();
    $result = array();
    $job_category_id = $job_status_id = '';
    $jobs = Models\Job::find($request->getAttribute('jobId'));
    if (empty($jobs)) {
        return renderWithJson($result, 'Jobs could not be updated. Please, try again', $validationErrorFields, 1);
    }
    if ($args['job_category_id'] != $jobs->job_category_id) {
        $job_category_id = $jobs->job_category_id;
    }
    if ($jobs->job_status_id >= \Constants\JobStatus::Open) {
        $job_status_id = true;
    }
    if ($args['apply_via'] != 'via_company') {
        unset($args['job_url']);
    }
    $oldJobStatus = $jobs->job_status_id;
    $validationErrorFields = $jobs->validate($args);
    if (!empty($validationErrorFields)) {
        $validationErrorFields = $validationErrorFields->toArray();
    }   
    $validationErrorFields['required'] = array();
    //get country, state and city ids
    if (!empty($args['full_address'])) {
        if (!empty($args['country']['iso_alpha2'])) {
            $jobs->country_id = findCountryIdFromIso2($args['country']['iso_alpha2']);
            if (!empty($args['state']['name'])) {
                $jobs->state_id = findOrSaveAndGetStateId($args['state']['name'], $jobs->country_id);
            }
            if (!empty($args['city']['name'])) {
                $jobs->city_id = findOrSaveAndGetCityId($args['city']['name'], $jobs->country_id, $jobs->state_id);
            }
        } else {
            $validationErrorFields['required'] = array();
            array_push($validationErrorFields['required'], 'Country');
        }
    }
    if (empty($validationErrorFields['required'])) {
        unset($validationErrorFields['required']);
    }
    if (empty($validationErrorFields)) {
        if (isset($args['job_status_id'])) {
            if ($authUser['role_id'] != \Constants\ConstUserTypes::Admin && in_array($args['job_status_id'], array(
                \Constants\JobStatus::PendingApproval,
                \Constants\JobStatus::Expired,
                \Constants\JobStatus::CanceledByAdmin
            ))) {
                unset($args['job_status_id']);
            }
        }
        $jobs->fill($args);
        try {
            $jobs->fill($args);
            $jobs->ip_id = saveIp();
            unset($jobs->country);
            unset($jobs->state);
            unset($jobs->city);
            unset($jobs->company);
            unset($jobs->image);
            unset($jobs->company_image);
            unset($jobs->skills);
            if ($authUser['role_id'] == \Constants\ConstUserTypes::Admin && !empty($args['user_id'])) {
                $jobs->user_id = $args['user_id'];
            }
            $newJobStatus = $jobs->job_status_id;
            if (in_array($newJobStatus, [\Constants\JobStatus::Draft, \Constants\JobStatus::PaymentPending]) || in_array($oldJobStatus, [\Constants\JobStatus::Draft, \Constants\JobStatus::PaymentPending])) {
                $amount = LISTING_FEE_FOR_JOB;
                if (!empty($args['is_featured']) || !empty($jobs->is_featured)) {
                    $amount+= FEATURED_FEE_FOR_JOB;
                }
                if (!empty($args['is_urgent']) || !empty($jobs->is_urgent)) {
                    $amount+= URGENT_FEE_FOR_JOB;
                }
                $jobs->total_listing_fee = $amount;
            }
            if ($jobs->save()) {                
                /** Job category count updation**/
                Models\Job::jobCategoryCountUpdation($jobs->id);
                if (!empty($job_category_id)) {
                    /** Job old category count updation**/
                    Models\Job::jobCategoryCountUpdation(0, $job_category_id);
                }
                if (!empty($args['image'])) {
                    saveImage('Job', $args['image'], $jobs->id);
                }
                if (!empty($args['image_data'])) {
                    saveImageData('Job', $args['image_data'], $jobs->id);
                } 
                // Add in the Job Skills table
                if (!empty($args['skills'] && $jobs->id)) {
                    Models\JobsSkill::where('job_id', $jobs->id)->get()->each(function ($jobsSkill) {
                        $jobsSkill->delete();
                    });
                    $skills = explode(',', $args['skills']);
                    foreach ($skills as $skill) {
                        $newSkills = Models\Skill::where('name', $skill)->first();
                        if(empty($newSkills)) {
                            $newSkills = new Models\Skill;
                            $newSkills->name = $skill;
                            $newSkills->slug = Inflector::slug(strtolower($skill), '-');
                            $newSkills->save();
                        }
                        $jobsSkills = new Models\JobsSkill;
                        $jobsSkills->skill_id = $newSkills->id ;

                        $jobsSkills->job_id = $jobs->id;
                        $jobsSkills->save();
                    }
                }
                if ($authUser['role_id'] != \Constants\ConstUserTypes::Admin && in_array($oldJobStatus, [\Constants\JobStatus::Draft, \Constants\JobStatus::PaymentPending]) && $newJobStatus != \Constants\JobStatus::Draft) {
                    if ($jobs->total_listing_fee > 0) {
                        $jobs->job_status_id = \Constants\JobStatus::PaymentPending;
                    } else {
                        $jobs->is_paid = 1;
                        if (IS_NEED_ADMIN_APPROVAL_FOR_NEW_JOBS) {
                            $jobs->job_status_id = \Constants\JobStatus::PendingApproval;
                        } else {                         
                            $jobs->job_open_date = date('Y-m-d h:i:s');   
                            $jobs->job_status_id = \Constants\JobStatus::Open;
                            $employerDetails = getUserHiddenFields($jobs->user_id);
                            $emailFindReplace = array(
                                '##USERNAME##' => ucfirst($employerDetails->username) ,
                                '##JOB_NAME##' => ucfirst($jobs->title),
                                '##JOB_URL##' => $_server_domain_url . '/jobs/' . $jobs->id . '/' . $jobs->title 
                            );
                            sendMail('Job Published Notification', $emailFindReplace, $employerDetails->email);                     
                        }
                    }
                    $jobs->update();
                }
                if (!empty($args['job_status_id']) && ($jobs->job_status_id == \Constants\JobStatus::CanceledByEmployer || $jobs->job_status_id == \Constants\JobStatus::CanceledByAdmin)) {
                    $employerDetails = getUserHiddenFields($jobs->user_id);
                    $emailFindReplace = array(
                        '##USERNAME##' => ucfirst($employerDetails->username) ,
                        '##JOB_NAME##' => ucfirst($jobs->title) 
                    );
                    sendMail('Job Cancelled Alert', $emailFindReplace, $employerDetails->email);                     
                }
                if ($oldJobStatus != $newJobStatus) {
                    $admin = Models\User::where('role_id', \Constants\ConstUserTypes::Admin)->first();
                    if (!empty($args['job_status_id']) && ($args['job_status_id'] == \Constants\JobStatus::CanceledByAdmin || $args['job_status_id'] == \Constants\JobStatus::Open)) {
                        insertActivities($admin['id'], $jobs->user_id, 'Job', $jobs->id, $oldJobStatus, $newJobStatus, \Constants\ActivityType::JobStatusChanged, $jobs->id, 0);
                    }
                    $oldJobStatus = Models\JobStatus::select('name')->where('id', $oldJobStatus)->first();
                    $newJobStatus = Models\JobStatus::select('name')->where('id', $newJobStatus)->first();
                    $emailFindReplace = array(
                        '##OLD_STATUS##' => ucfirst($oldJobStatus->name) ,
                        '##NEW_STATUS##' => ucfirst($newJobStatus->name) ,
                        '##JOB_NAME##' => ucfirst($jobs->title),
                        '##JOB_URL##' => $_server_domain_url . '/jobs/' . $jobs->id . '/' . $jobs->title  
                    );
                    sendMail('Admin Job Status Alert', $emailFindReplace, $admin->email);                   
                }
                $result['data'] = $jobs->toArray();
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Jobs could not be updated. Access Denied.', '', 1);
            }
        } catch (Exception $e) {
            return renderWithJson($result, 'Jobs could not be updated. Please, try again', '', 1);
        }
    } else {
        return renderWithJson($result, 'Jobs could not be updated. Please, try again', $validationErrorFields, 1);
    }
})->add(new ACL('canUpdateJob'));
/**
 * GET employerEmployerIdJobsStatsGet
 * Summary: Fetch job
 * Notes: Returns a job based on a single ID
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/employer/{employerId}/jobs/stats', function ($request, $response, $args) {
    global $authUser;
    $queryParams = $request->getQueryParams();
    $results = array();
    try {
        $jobStatuses = Models\JobStatus::Filter($queryParams)->get()->toArray();
        foreach ($jobStatuses as $key => $jobStatus) {
            $job = Models\Job::where('user_id', $authUser['id'])->where('job_status_id', $jobStatus['id'])->count();
            $jobStatuses[$key]['job_count'] = $job;
        }
        $results = array(
            'data' => $jobStatuses
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobStat'));
/**
 * POST JobApplyClick Post
 * Summary: JobApplyClick Post
 * Notes: Post JobApplyClick
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/job_apply_clicks', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    global $authUser;
    $jobApplyClick = new Models\JobApplyClick($args);
    if (!empty($authUser)) {
        $jobApplyClick->user_id = $authUser['id'];
    }
    $result = array();
    try {
        $validationErrorFields = $jobApplyClick->validate($args);
        if (empty($validationErrorFields)) {
            $jobApplyClick->ip_id = saveIp();
            if ($jobApplyClick->save()) {
                $enabledIncludes = array(
                    'job'
                );
                $jobApplyClick = Models\JobApplyClick::with($enabledIncludes)->find($jobApplyClick->id);
                $result['data'] = $jobApplyClick->toArray();
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Job Apply Click could be added only apply via company.', '', 1);
            }
        } else {
            return renderWithJson($result, 'Job Apply Click could not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Apply Click could not be added. Please, try again.', '', 1);
    }
});
/**
 * GET JobApplyClick JobApplyClickId get
 * Summary: Fetch a JobApplyClick based on a JobApplyClick Id
 * Notes: Returns a JobApplyClick from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_apply_clicks/{jobApplyClickId}', function ($request, $response, $args) {
    $result = array();
    try {
        $enabledIncludes = array(
            'job',
            'user'
        );
        $jobApplyClicks = Models\JobApplyClick::with($enabledIncludes)->find($request->getAttribute('jobApplyClickId'));
        if (!empty($jobApplyClicks)) {
            $result['data'] = $jobApplyClicks->toArray();
            return renderWithJson($result);
        } else {
            return renderWithJson($result, $message = 'No record found', $fields = '', $isError = 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canViewJobApplyClick'));
/**
 * GET JobApplyClick Get
 * Summary: Fetch all JobApplyClick
 * Notes: Returns all JobApplyClick from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_apply_clicks', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    $results = array();
    try {
        $enabledIncludes = array(
            'job',
            'user'
        );
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobApplyClicks['data'] = Models\JobApplyClick::with($enabledIncludes)->get();
        } else {
            $jobApplyClicks = Models\JobApplyClick::with($enabledIncludes)->Filter($queryParams)->paginate($count)->toArray();
        }
        $data = $jobApplyClicks['data'];
        unset($jobApplyClicks['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $jobApplyClicks
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobApplyClick'));
/**
 * DELETE JobApplyClick JobApplyClickId Delete
 * Summary: Delete JobApplyClick
 * Notes: Deletes a single JobApplyClick based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/job_apply_clicks/{jobApplyClickId}', function ($request, $response, $args) {
    $result = array();
    $jobApplyClicks = Models\JobApplyClick::find($request->getAttribute('jobApplyClickId'));
    if (empty($jobApplyClicks)) {
        return renderWithJson($result, $message = 'No record found', $fields = '', $isError = 1);
    }
    try {
        $jobApplyClicks->delete();
        $result = array(
            'status' => 'success',
        );
        return renderWithJson($result);
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Apply Click could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteJobApplyClick'));
/**
 * GET JobApplyStatus Get
 * Summary: Fetch all JobApplyStatus
 * Notes: Returns all JobApplyStatus from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_apply_statuses', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    $results = array();
    try {
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobApplyStatuses['data'] = Models\JobApplyStatus::get();
        } else {
            $jobApplyStatuses = Models\JobApplyStatus::Filter($queryParams)->paginate($count)->toArray();
        }
        $data = $jobApplyStatuses['data'];
        unset($jobApplyStatuses['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $jobApplyStatuses
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobApplyStatus'));
/**
 * POST JobCategory Post
 * Summary: JobCategory Post
 * Notes: Post JoJobCategorybs
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/job_categories', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $jobCategories = new Models\JobCategory($args);
    $result = array();
    try {
        $validationErrorFields = $jobCategories->validate($args);
        if (empty($validationErrorFields)) {
            $jobCategories->slug = Inflector::slug(strtolower($jobCategories->name), '-');
            $jobCategories->save();
            $result['data'] = $jobCategories->toArray();
            return renderWithJson($result);
        } else {
            return renderWithJson($result, ' Job Category could not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Category could not be added. Please, try again.', '', 1);
    }
})->add(new ACL('canCreateJobCategory'));
/**
 * GET JobCategory JobCategoryId get
 * Summary: Fetch a JobCategory based on a JobCategory Id
 * Notes: Returns a JobCategory from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_categories/{jobCategoryId}', function ($request, $response, $args) {
    $result = array();
    $jobCategories = Models\JobCategory::find($request->getAttribute('jobCategoryId'));
    if (empty($jobCategories)) {
        return renderWithJson($result, 'Job Category could not be updated. Please, try again', '', 1);
    } else {
        $result['data'] = $jobCategories->toArray();
        return renderWithJson($result);
    }
});
/**
 * GET JobJobCategory  Get
 * Summary: Fetch all JobCategory
 * Notes: Returns all JobCategory from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_categories', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    $results = array();
    try {
        $jobCategories = Models\JobCategory::where('is_active', 1)->Filter($queryParams)->paginate($count)->toArray();
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobCategories['data'] = Models\JobCategory::get();
        }
        if (!empty($queryParams['filter'])) {
            $jobCategories = Models\JobCategory::Filter($queryParams)->paginate($count)->toArray();
        }
        $data = $jobCategories['data'];
        unset($jobCategories['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $jobCategories
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
});
/**
 * DELETE JobCategory JobCategoryId Delete
 * Summary: Delete JobCategory
 * Notes: Deletes a single JobCategory based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/job_categories/{jobCategoryId}', function ($request, $response, $args) {
    $result = array();
    $jobCategories = Models\JobCategory::find($request->getAttribute('jobCategoryId'));
    try {
        if (!empty($jobCategories)) {
            $jobCategories->delete();
            $result = array(
                'status' => 'success',
            );
            return renderWithJson($result);
        } else {
            return renderWithJson($result, 'Job Category could not be updated. Please, try again', '', 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Category could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteJobCategory'));
/**
 * PUT JobCategory JobCategoryIdPut
 * Summary: Update JobCategory details
 * Notes: Update JobCategory details.
 * Output-Formats: [application/json]
 */
$app->PUT('/api/v1/job_categories/{jobCategoryId}', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $result = array();
    $jobCategories = Models\JobCategory::find($request->getAttribute('jobCategoryId'));
    if (empty($jobCategories)) {
        return renderWithJson($result, 'Job Category could not be updated. Please, try again', '', 1);
    }
    $validationErrorFields = $jobCategories->validate($args);
    if (empty($validationErrorFields)) {
        $jobCategories->fill($args);
        try {
            $jobCategories->slug = Inflector::slug(strtolower($jobCategories->name), '-');
            $jobCategories->save();
            $result['data'] = $jobCategories->toArray();
            return renderWithJson($result);
        } catch (Exception $e) {
            return renderWithJson($result, 'Job Category could not be updated. Please, try again', '', 1);
        }
    } else {
        return renderWithJson($result, 'Job Categorycould could not be updated. Please, try again', $validationErrorFields, 1);
    }
})->add(new ACL('canUpdateJobCategory'));
/**
 * GET jobStatusesGet
 * Summary: Fetch all job statuses
 * Notes: Returns all job statuses from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_statuses', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $results = array();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    try {
        $jobStatuses = Models\JobStatus::Filter($queryParams)->paginate($count)->toArray();
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobStatuses['data'] = Models\JobStatus::get();
        }
        $data = $jobStatuses['data'];
        unset($jobStatuses['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $jobStatuses
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
});
/**
 * POST JobType Post
 * Summary: JobType Post
 * Notes: Post JobType
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/job_types', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $jobTypes = new Models\JobType($args);
    $result = array();
    try {
        $validationErrorFields = $jobTypes->validate($args);
        if (empty($validationErrorFields)) {
            $jobTypes->slug = Inflector::slug(strtolower($jobTypes->name), '-');
            $jobTypes->save();
            $result['data'] = $jobTypes->toArray();
            return renderWithJson($result);
        } else {
            return renderWithJson($result, ' Job Type could not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Type could not be added. Please, try again.', '', 1);
    }
})->add(new ACL('canCreateJobType'));
/**
 * GET JobType JobTypeId get
 * Summary: Fetch a JobType based on a JobType Id
 * Notes: Returns a JobType from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_types/{jobTypeId}', function ($request, $response, $args) {
    $jobTypes = Models\JobType::find($request->getAttribute('jobTypeId'));
    $result['data'] = $jobTypes->toArray();
    return renderWithJson($result);
})->add(new ACL('canViewJobType'));
/**
 * GET JobType  Get
 * Summary: Fetch all JobType
 * Notes: Returns all JobType from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_types', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    $results = array();
    try {
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobTypes = Models\JobType::get()->toArray();
        } else {
            $jobTypes = Models\JobType::Filter($queryParams)->paginate($count)->toArray();
        }
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $results = array(
                'data' => $jobTypes
            );
        } else {
            $data = $jobTypes['data'];
            unset($jobTypes['data']);
            $results = array(
                'data' => $data,
                '_metadata' => $jobTypes
            );
        }
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
});
/**
 * DELETE JobType JobTypeId Delete
 * Summary: Delete JobType
 * Notes: Deletes a single JobType based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/job_types/{jobTypeId}', function ($request, $response, $args) {
    $jobTypes = Models\JobType::find($request->getAttribute('jobTypeId'));
    try {
        $jobTypes->delete();
        $result = array(
            'status' => 'success',
        );
        return renderWithJson($result);
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Type could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteJobType'));
/**
 * PUT JobType JobTypeId update
 * Summary: Update JobType details
 * Notes: Update JobType details.
 * Output-Formats: [application/json]
 */
$app->PUT('/api/v1/job_types/{jobTypeId}', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $result = array();
    $jobTypes = Models\JobType::find($request->getAttribute('jobTypeId'));
    $validationErrorFields = $jobTypes->validate($args);
    if (empty($validationErrorFields)) {
        $jobTypes->fill($args);
        try {
            $jobTypes->slug = Inflector::slug(strtolower($jobTypes->name), '-');
            $jobTypes->save();
            $result['data'] = $jobTypes->toArray();
            return renderWithJson($result);
        } catch (Exception $e) {
            return renderWithJson($result, 'Job Type could not be updated. Please, try again', '', 1);
        }
    } else {
        return renderWithJson($result, 'Job Type not be updated. Please, try again', $validationErrorFields, 1);
    }
})->add(new ACL('canUpdateJobType'));
//get Jobs
function _getJobs($request, $response, $args)
{
    global $authUser;
    $queryParams = $request->getQueryParams();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    $results = array();
    $count = PAGE_LIMIT;
    if (!empty($queryParams['limit'])) {
        $count = $queryParams['limit'];
    }
    try {
        $enabledIncludes = array(
            'user',
            'city',
            'state',
            'country',
            'job_category',
            'attachment',
            'job_type',
            'job_status',
            'job_skill',
            'job_apply'
        );
        (isPluginEnabled('Job/JobFlag')) ? $enabledIncludes[] = 'flag' : '';
        $jobs = Models\Job::with($enabledIncludes)->Filter($queryParams);
        if ((empty($queryParams['filter']) || (!empty($queryParams['filter']) && $queryParams['filter'] != 'all'))) {
            $jobs = $jobs->where('job_status_id', \Constants\JobStatus::Open);
        }
        $jobs = $jobs->paginate($count)->toArray();
        if (!empty($args['jobId'])) {
            $enabledIncludes = array(
                'user',
                'city',
                'state',
                'country',
                'job_category',
                'attachment',
                'job_type',
                'job_status',
                'job_skill',
                'job_apply',
                'salary_type'
            );
            (isPluginEnabled('Job/JobFlag')) ? $enabledIncludes[] = 'flag' : '';
            $jobs['data'] = Models\Job::with($enabledIncludes)->find($args['jobId']);
            if (!empty($_GET['type']) && $_GET['type'] == 'view') {
                insertViews($request->getAttribute('jobId'), 'Job');
            }
        }
        if (!empty($args['jobTypeId'])) {
            $enabledIncludes = array(
                'user',
                'city',
                'state',
                'country',
                'job_category',
                'attachment',
                'job_type',
                'job_status',
                'job_skill'
            );
            $jobs['data'] = Models\Job::with($enabledIncludes)->Filter($queryParams)->where('job_type_id', $args['jobTypeId'])->get()->toArray();
        }
        if (!empty($args['jobCategoryId'])) {
            $enabledIncludes = array(
                'user',
                'city',
                'state',
                'country',
                'job_category',
                'attachment',
                'job_type',
                'job_status',
                'job_skill'
            );
            $jobs['data'] = Models\Job::with($enabledIncludes)->Filter($queryParams)->where('job_category_id', $args['jobCategoryId'])->get()->toArray();
        }
        $data = $jobs['data'];
        unset($jobs['data']);
        if (!empty($args['jobId'])) {
            if (!empty($data)) {
                $results = array(
                    'data' => $data
                );
            } else {
                return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1, 404);
            }
        } else {
            $results = array(
                'data' => $data,
                '_metadata' => $jobs
            );
        }
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1, 404);
    }
}
/**
 * POST JobApply Post
 * Summary: JobApply Post
 * Notes: Post Jobs
 * Output-Formats: [application/json]
 */
$app->POST('/api/v1/job_applies', function ($request, $response, $args) {
    global $authUser;
    $args = $request->getParsedBody();
    $jobApply = new Models\JobApply($args);
    $result = array();
    if (!in_array($authUser->role_id, [\Constants\ConstUserTypes::User, \Constants\ConstUserTypes::Freelancer]) && $authUser->role_id != \Constants\ConstUserTypes::Admin) {
        return renderWithJson($result, 'Employer could not be added the job apply.', '', 1);
    }
    $job = Models\Job::where('id', $args['job_id'])->where('job_status_id', \Constants\JobStatus::Open)->first();
    if (empty($job)) {
        return renderWithJson($result, ' Job Not Open.', '', 1);
    }
    try {
        $validationErrorFields = $jobApply->validate($args);
        if (empty($validationErrorFields) && (!empty($args['file'])) && (file_exists(APP_PATH . '/media/tmp/' . $args['file']))) {
            $jobApply->job_apply_status_id = 1;
            $jobApply->user_id = $authUser['id'];
            $jobApply->ip_id = saveIp();
            $jobApply->save();
            /** Job Appy count updation**/
            jobTableCountUpdation('JobApply', 'job_apply_count', $jobApply->job_id, $jobApply->user_id);
            /** Image save common fucntion **/
            if (!empty($args['file'])) {
                saveImage('JobApply', $args['file'], $jobApply->id);
                $enabledIncludes = array(
                    'attachment'
                );
                $jobApply = Models\JobApply::with($enabledIncludes)->find($jobApply->id);
            }
            if (!empty($args['image_data'])) {
                saveImageData('JobApply', $args['image_data'], $jobApply->id);                
                $enabledIncludes = array(
                    'attachment'
                );
                $jobApply = Models\JobApply::with($enabledIncludes)->find($jobApply->id);
            } 
            if ($jobApply) {
                global $_server_domain_url;
                $getuserData = getUserHiddenFields($job->user_id);
                $getapplyuserName = getUserHiddenFields($jobApply->user_id);
                $emailFindReplace = array(
                    '##USERNAME##' => $getuserData->username,
                    '##APPLY_USERNAME##' => $getapplyuserName->username,
                    '##JOB_TITLE##' =>  $job->title,
                    '##RESUMES_LINK##' => $_server_domain_url . '/job_apply/' . $jobApply->id);     
               sendMail('New Resume Notification', $emailFindReplace, $getuserData->email);
              
            } 
            insertActivities($authUser['id'], $job->user_id, 'JobApply', $jobApply->id, 0, 0, \Constants\ActivityType::JobApply, $jobApply->job_id);
            $result['data'] = $jobApply->toArray();
            return renderWithJson($result);
        } else {
            if (empty($validationErrorFields)) {
                if (empty($args['file']) || ((!empty($args['file'])) && (!file_exists(APP_PATH . '/media/tmp/' . $args['file'])))) {
                    $validationErrorFields['file'] = array(
                        'File Required'
                    );
                }
            }
            return renderWithJson($result, ' Job Apply not be added. Please, try again.', $validationErrorFields, 1);
        }
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Apply could not be added. Please, try again.', '', 1);
    }
})->add(new ACL('canCreateJobApply'));
/**
 * GET JobApply JobApplyId get
 * Summary: Fetch a JobApply based on a JobApply Id
 * Notes: Returns a JobApply from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_applies/{jobApplyId}', function ($request, $response, $args) {
    $result = array();
    $enabledIncludes = array(
        'attachment',
        'user',
        'job_apply_status',
        'job',
        'ip'
    );
    $jobApply = Models\JobApply::with($enabledIncludes)->find($request->getAttribute('jobApplyId'));
    if (!empty($jobApply)) {
        $result['data'] = $jobApply->toArray();
        return renderWithJson($result);
    } else {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canViewJobApply'));
/**
 * GET JobApply Get
 * Summary: Fetch all JobApply
 * Notes: Returns all JobApply from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/job_applies', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $results = array();
    try {
        $count = PAGE_LIMIT;
        if (!empty($queryParams['limit'])) {
            $count = $queryParams['limit'];
        }
        $enabledIncludes = array(
            'attachment',
            'user',
            'job_apply_status',
            'job',
            'ip'
        );
        if (!empty($queryParams['limit']) && $queryParams['limit'] == 'all') {
            $jobApply = Models\JobApply::with($enabledIncludes)->Filter($queryParams)->toArray();
        } else {
            $jobApply = Models\JobApply::with($enabledIncludes)->Filter($queryParams)->paginate($count)->toArray();
        }
        $data = $jobApply['data'];
        unset($jobApply['data']);
        $results = array(
            'data' => $data,
            '_metadata' => $jobApply
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobApply'));
/**
 * GET JobApply Get
 * Summary: Fetch all JobApply
 * Notes: Returns all JobApply from the system
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/me/job_applies', function ($request, $response, $args) {
    global $authUser;
    if (!empty($authUser)) {
        $result = array();
        $queryParams = $request->getQueryParams();
        $count = PAGE_LIMIT;
        if (!empty($queryParams['limit'])) {
            $count = $queryParams['limit'];
        }
        $enabledIncludes = array(
            'job_apply_status',
            'job'
        );
        $jobApply = Models\JobApply::with($enabledIncludes)->where('user_id', $authUser['id'])->Filter($queryParams)->paginate($count)->toArray();
        if (!empty($jobApply)) {
            $data = $jobApply['data'];
            unset($jobApply['data']);
            $result = array(
                'data' => $data,
                '_metadata' => $jobApply
            );
            return renderWithJson($result);
        } else {
            return renderWithJson($result, 'No record found', '', 1);
        }
    } else {
        return renderWithJson($result, 'Your not have jobs', '', 1);
    }
})->add(new ACL('canUserViewJobApply'));
/**
 * DELETE JobApply JobApplyIdDelete
 * Summary: Delete JobApply
 * Notes: Deletes a single JobApply based on the ID supplied
 * Output-Formats: [application/json]
 */
$app->DELETE('/api/v1/job_applies/{jobApplyId}', function ($request, $response, $args) {
    $result = array();
    $jobApply = Models\JobApply::find($request->getAttribute('jobApplyId'));
    if (empty($jobApply)) {
        return renderWithJson($result, 'No record found', '', 1);
    }
    try {
        $jobApply->delete();
        /** Job Appy count updation**/
        jobTableCountUpdation('JobApply', 'job_apply_count', $jobApply->job_id, $jobApply->user_id);
        $result = array(
            'status' => 'success',
        );
        return renderWithJson($result);
    } catch (Exception $e) {
        return renderWithJson($result, 'Job Apply could not be deleted. Please, try again.', '', 1);
    }
})->add(new ACL('canDeleteJobApply'));
/**
 * PUT JobApply JobApplyIdPut
 * Summary: Update JobApply details
 * Notes: Update JobApply details.
 * Output-Formats: [application/json]
 */
$app->PUT('/api/v1/job_applies/{jobApplyId}', function ($request, $response, $args) {
    $args = $request->getParsedBody();
    $result = array();
    $jobApply = Models\JobApply::find($request->getAttribute('jobApplyId'));
    if (empty($jobApply)) {
        return renderWithJson($result, 'No record found', '', 1);
    }
    $validationErrorFields = $jobApply->validate($args);
    if (empty($validationErrorFields)) {
        $jobApply->fill($args);
        try {
            $jobApply->ip_id = saveIp();
            if ($jobApply->save()) {
                /** Job Appy count updation**/
                jobTableCountUpdation('JobApply', 'job_apply_count', $jobApply->job_id, $jobApply->user_id);
                $result['data'] = $jobApply->toArray();
                return renderWithJson($result);
            } else {
                return renderWithJson($result, 'Does`t has access to update this Job Apply', '', 1);
            }
        } catch (Exception $e) {
            return renderWithJson($result, 'Job Apply could not be updated. Please, try again', '', 1);
        }
    } else {
        return renderWithJson($result, 'Job Apply could not be updated. Please, try again', $validationErrorFields, 1);
    }
})->add(new ACL('canUpdateJobApply'));
/**
 * GET employerEmployerIdJobAppliesGet
 * Summary: Fetch job apply
 * Notes: Returns a job apply based on a single ID
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/employer/{employerId}/job_applies', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    global $authUser;
    $job = Models\Job::select('id')->where('user_id', $authUser['id'])->get()->toArray();
    $results = array();
    try {
        $count = PAGE_LIMIT;
        if (!empty($queryParams['limit'])) {
            $count = $queryParams['limit'];
        }
        $jobApplies = Models\JobApply::with('user', 'job', 'job_apply_status', 'attachment')->whereIn('job_id', $job)->Filter($queryParams)->paginate($count)->toArray();
        $data = $jobApplies['data'];
        unset($jobApplies['data']);
        if ((isPluginEnabled('Bidding/Exam'))) {
            foreach ($data as $key => $jobApply) {
                $examsUser = Models\ExamsUser::with('exam')->where('user_id', $jobApply['user_id'])->where('exams_users.exam_status_id', \Constants\ExamStatus::Passed);
                $examsUser = $examsUser->get();
                if (!empty($examsUser)) {
                    $data[$key]['exams_users'] = $examsUser->toArray();
                }
            }
        }
        $results = array(
            'data' => $data,
            '_metadata' => $jobApplies
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListEmployerJobApply'));
/**
 * GET employerEmployerIdJobAppliesStatsGet
 * Summary: Fetch job apply
 * Notes: Returns a job apply based on a single ID
 * Output-Formats: [application/json]
 */
$app->GET('/api/v1/employer/{employerId}/job_applies/stats', function ($request, $response, $args) {
    $queryParams = $request->getQueryParams();
    $results = array();
    global $authUser;
    try {
        $jobApplyStatuses = Models\JobApplyStatus::Filter($queryParams)->get()->toArray();
        foreach ($jobApplyStatuses as $key => $jobApplyStatus) {
            $jobApply = Models\JobApply::where('job_apply_status_id', $jobApplyStatus['id'])->join('jobs', 'job_applies.job_id', '=', 'jobs.id')->where('jobs.user_id', $authUser['id'])->count();
            $jobApplyStatuses[$key]['job_apply_count'] = $jobApply;
        }
        $results = array(
            'data' => $jobApplyStatuses
        );
        return renderWithJson($results);
    } catch (Exception $e) {
        return renderWithJson($results, $message = 'No record found', $fields = '', $isError = 1);
    }
})->add(new ACL('canListJobApplyStat'));
