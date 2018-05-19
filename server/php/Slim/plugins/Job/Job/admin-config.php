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
$menus = array(
    'Jobs' => array(
        'title' => 'Jobs',
        'icon_template' => '<span class="fa fa-tasks"></span>',
        'child_sub_menu' => array(
            'jobs' => array(
                'title' => 'Jobs',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 1
            ) ,
            'job_applies' => array(
                'title' => 'Applies',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 2
            ) ,
            'job_apply_clicks' => array(
                'title' => 'Apply Clicks',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 3
            ) ,
            'job_views' => array(
                'title' => 'Job Views',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 5
            )
        ) ,
        'order' => 7,
    ) ,
    'Master' => array(
        'title' => 'Master',
        'icon_template' => '<span class="glyphicon glyphicon-dashboard"></span>',
        'child_sub_menu' => array(
            'job_categories' => array(
                'title' => 'Job Categories',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 22
            ) ,
            'job_types' => array(
                'title' => 'Job Types',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 23
            ) ,
            'job_apply_statuses' => array(
                'title' => 'Apply Statuses',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 24
            ) ,
            'salary_types' => array(
                'title' => 'Salary Types',
                'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                'suborder' => 25
            ) ,
        ) ,
    ) ,
);
$tables = array(
    'jobs' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'title',
                    'label' => 'Title',
                ) ,
                2 => array(
                    'name' => 'user.username',
                    'label' => 'Employer',
                ) ,
                3 => array(
                    'name' => 'company_name',
                    'label' => 'Company',
                ) ,
                4 => array(
                    'name' => 'job_category.name',
                    'label' => 'Job Category',
                ) ,
                5 => array(
                    'name' => 'job_status.name',
                    'label' => 'Job Status',
                ) ,
                6 => array(
                    'name' => 'job_type.name',
                    'label' => 'Job Type',
                ) ,
                7 => array(
                    'name' => 'no_of_opening',
                    'label' => 'No Of Openings',
                ) ,
                8 => array(
                    'name' => 'full_address',
                    'label' => 'Job Location',
                ) ,
                9 => array(
                    'name' => 'minimum_experience',
                    'label' => 'Minimum Experience',
                ) ,
                10 => array(
                    'name' => 'maximum_experience',
                    'label' => 'Maximum  Experience',
                ) ,
                11 => array(
                    'name' => 'total_listing_fee',
                    'label' => 'Listing Fee',
                ) ,
                12 => array(
                    'name' => 'job_apply_click_count',
                    'label' => 'Job Apply Clicks',
                    'template' => '<a href="#/job_apply_clicks/list?search=%7B%22job_id%22:{{entry.values.id}}%7D">{{entry.values.job_apply_click_count}}</a>',
                ) ,
                13 => array(
                    'name' => 'job_apply_count',
                    'label' => 'Job Applies',
                    'template' => '<a href="#/job_applies/list?search=%7B%22job_id%22:{{entry.values.id}}%7D">{{entry.values.job_apply_count}}</a>',
                ) ,
                14 => array(
                    'name' => 'is_show_salary',
                    'label' => 'Show Salary?',
                    'type' => 'boolean',
                ) ,
                15 => array(
                    'name' => 'is_featured',
                    'label' => 'Featured?',
                    'type' => 'boolean',
                ) ,
                16 => array(
                    'name' => 'is_urgent',
                    'label' => 'Urgent?',
                    'type' => 'boolean',
                ) ,
                17 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Jobs',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => '<project-edit entity="job" id="{{entry.values.id}}"></project-edit>',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
                1 => '<batch-job-open type="open" action="jobs" selection="selection"></batch-job-open>',
                2 => '<batch-job-cancelled type="cancel" action="jobs" selection="selection"></batch-job-cancelled>'
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'user_id',
                    'label' => 'User',
                    'targetEntity' => 'users',
                    'targetField' => 'username',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                    'remoteCompleteAdditionalParams' => array(
                        'role' => 'employer'
                    ) ,
                    'permanentFilters' => array(
                        'role' => 'employer'
                    )
                ) ,
                2 => array(
                    'name' => 'job_status_id',
                    'label' => 'Status',
                    'targetEntity' => 'job_statuses',
                    'targetField' => 'name',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => '<project-create entity="job"></project-create>',
                2 => '<ma-filter-button filters="filters()" enabled-filters="enabledFilters" enable-filter="enableFilter()"></ma-filter-button><ma-export-to-csv-button entry="entry" entity="entity" size="sm" datastore="::datastore"></ma-export-to-csv-button><create-manage-job entry="entry" entity="entity" size="sm" lable=""></create-manage-job>'
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'job_status_id',
                    'label' => 'Job Status',
                    'type' => 'number',
                ) ,
                1 => array(
                    'name' => 'job_type',
                    'label' => 'Job Type',
                    'targetEntity' => 'job_types',
                    'targetField' => 'name',
                    'type' => 'reference',
                ) ,
                2 => array(
                    'name' => 'job_category_id',
                    'label' => 'Job Category',
                    'targetEntity' => 'job_categories',
                    'targetField' => 'name',
                    'type' => 'reference',
                ) ,
                3 => array(
                    'name' => 'title',
                    'label' => 'Title',
                    'type' => 'string',
                ) ,
                4 => array(
                    'name' => 'description',
                    'label' => 'Description',
                    'type' => 'text',
                ) ,
                5 => array(
                    'name' => 'address',
                    'label' => 'Address',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                6 => array(
                    'name' => 'address1',
                    'label' => 'Address1',
                    'type' => 'string',
                ) ,
                7 => array(
                    'name' => 'city_name',
                    'label' => 'City',
                    'targetEntity' => 'cities',
                    'targetField' => 'name',
                    'type' => 'reference',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                8 => array(
                    'name' => 'state_name',
                    'label' => 'State',
                    'targetEntity' => 'states',
                    'targetField' => 'name',
                    'type' => 'reference',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                9 => array(
                    'name' => 'country_iso2',
                    'label' => 'Country',
                    'targetEntity' => 'countries',
                    'targetField' => 'name',
                    'type' => 'reference',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                10 => array(
                    'name' => 'zip_code',
                    'label' => 'Zip Code',
                    'type' => 'string',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                11 => array(
                    'name' => 'latitude',
                    'label' => 'Latitude',
                    'type' => 'number',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                12 => array(
                    'name' => 'longitude',
                    'label' => 'Longitude',
                    'type' => 'number',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                13 => array(
                    'name' => 'salary_from',
                    'label' => 'Salary From',
                    'type' => 'number',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                14 => array(
                    'name' => 'salary_to',
                    'label' => 'Salary To',
                    'type' => 'number',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                15 => array(
                    'name' => 'salary_type_id',
                    'label' => 'Salary Type',
                ) ,
                16 => array(
                    'name' => 'is_show_salary',
                    'label' => 'Show Salary?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => false,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
                17 => array(
                    'name' => 'last_date_to_apply',
                    'label' => 'Last Date To Apply',
                    'type' => 'date',
                    'format' => 'yyyy-MM-dd',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                18 => array(
                    'name' => 'job_listing_date_upto',
                    'label' => 'Job Listing Date Upto',
                ) ,
                19 => array(
                    'name' => 'no_of_opening',
                    'label' => 'No Of Opening',
                    'type' => 'number',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                20 => array(
                    'name' => 'company_name',
                    'label' => 'Company',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                21 => array(
                    'name' => 'apply_via',
                    'label' => 'Apply Via',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                22 => array(
                    'name' => 'job_url',
                    'label' => 'Job Url',
                    'type' => 'string',
                    'validation' => array(
                        'required' => false,
                    ) ,
                ) ,
                23 => array(
                    'name' => 'is_featured',
                    'label' => 'Featured?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
                24 => array(
                    'name' => 'is_urgent',
                    'label' => 'Urgent?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'title',
                    'label' => 'Title',
                ) ,
                2 => array(
                    'name' => 'user.username',
                    'label' => 'Employer',
                ) ,
                3 => array(
                    'name' => 'job_category.name',
                    'label' => 'Job Category',
                ) ,
                4 => array(
                    'name' => 'job_status.name',
                    'label' => 'Job Status',
                ) ,
                5 => array(
                    'name' => 'job_type.name',
                    'label' => 'Job Type',
                ) ,
                6 => array(
                    'name' => 'description',
                    'label' => 'Description',
                ) ,
                7 => array(
                    'name' => 'company_name',
                    'label' => 'Company',
                ) ,
                8 => array(
                    'name' => 'company_website',
                    'label' => 'Company Website',
                ) ,
                9 => array(
                    'name' => 'no_of_opening',
                    'label' => 'No Of Openings',
                ) ,
                10 => array(
                    'name' => 'full_address',
                    'label' => 'Job Location',
                ) ,
                11 => array(
                    'name' => 'salary_from',
                    'label' => 'Salary From',
                ) ,
                12 => array(
                    'name' => 'salary_to',
                    'label' => 'Salary To',
                ) ,
                13 => array(
                    'name' => 'salary_type.name',
                    'label' => 'Salary Type',
                ) ,
                14 => array(
                    'name' => 'minimum_experience',
                    'label' => 'Minimum Experience',
                ) ,
                15 => array(
                    'name' => 'maximum_experience',
                    'label' => 'Maximum  Experience',
                ) ,
                16 => array(
                    'name' => 'apply_via',
                    'label' => 'Apply Via',
                    'template' => '<apply-via entry="entry"  entity="entity"></apply-via>',
                ) ,
                17 => array(
                    'name' => 'job_apply_click_count',
                    'label' => 'Job Apply Clicks',
                    'template' => '<a href="#/job_apply_clicks/list?search=%7B%22job_id%22:{{entry.values.id}}%7D">{{entry.values.job_apply_click_count}}</a>',
                ) ,
                18 => array(
                    'name' => 'job_apply_count',
                    'label' => 'Job Applies',
                    'template' => '<a href="#/job_applies/list?search=%7B%22job_id%22:{{entry.values.id}}%7D">{{entry.values.job_apply_count}}</a>',
                ) ,
                19 => array(
                    'name' => 'flag_count',
                    'label' => 'Flags',
                    'template' => '<a href="#/job_flags/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.flag_count}}</a>',
                ) ,
                20 => array(
                    'name' => 'view_count',
                    'template' => '<a href="#/job_views/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.view_count}}</a>',
                    'label' => 'Views',
                ) ,
                21 => array(
                    'name' => 'is_show_salary',
                    'label' => 'Show Salary?',
                    'type' => 'boolean',
                ) ,
                22 => array(
                    'name' => 'is_featured',
                    'label' => 'Featured?',
                    'type' => 'boolean',
                ) ,
                23 => array(
                    'name' => 'is_urgent',
                    'label' => 'Urgent?',
                    'type' => 'boolean',
                ) ,
                24 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
                25 => array(
                    'name' => 'Applies',
                    'label' => 'Applies',
                    'targetEntity' => 'job_applies',
                    'targetReferenceField' => 'job_id',
                    'targetFields' => array(
                        0 => array(
                            'name' => 'id',
                            'label' => 'ID',
                            'isDetailLink' => true,
                        ) ,
                        1 => array(
                            'name' => '',
                            'label' => 'Image',
                            'template' => '<display-image entry="entry" thumb="normal_thumb" type="JobApply"  entity="entity"></display-image>',
                        ) ,
                        3 => array(
                            'name' => 'user.username',
                            'label' => 'User',
                        ) ,
                        4 => array(
                            'name' => 'job_apply_status.name',
                            'label' => 'Job Apply Status',
                        ) ,
                        5 => array(
                            'name' => 'hire_request.name',
                            'label' => 'Hire Request',
                        ) ,
                        6 => array(
                            'name' => 'cover_letter',
                            'label' => 'Cover Letter',
                        ) ,
                        7 => array(
                            'name' => 'total_resume_rating',
                            'label' => 'Total Resume Rating',
                        ) ,
                        8 => array(
                            'name' => 'resume_rating_count',
                            'template' => '<a href="#/resume_ratings/list?search=%7B%22job_apply_id%22:{{entry.values.id}}%7D">{{entry.values.resume_rating_count}}</a>',
                            'label' => 'Resume Ratings',
                        ) ,
                        9 => array(
                            'name' => 'ip.ip',
                            'label' => 'Ip',
                        ) ,
                        10 => array(
                            'name' => 'created_at',
                            'label' => 'Created On',
                        ) ,
                    ) ,
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'referenced_list',
                    'perPage' => 10
                ) ,
            ) ,
        ) ,
    ) ,
    'job_types' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'slug',
                    'label' => 'Slug',
                ) ,
                3 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
                4 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Types',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'filter',
                    'type' => 'choice',
                    'label' => 'Active?',
                    'choices' => array(
                        0 => array(
                            'label' => 'Active',
                            'value' => 'active',
                        ) ,
                        1 => array(
                            'label' => 'Inactive',
                            'value' => 'inactive',
                        ) ,
                    ) ,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
                2 => 'create',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'slug',
                    'label' => 'Slug',
                ) ,
                3 => array(
                    'name' => 'job_count',
                    'template' => '<a href="#/jobs/list?search=%7B%22job_types%22:{{entry.values.name}}%7D">{{entry.values.job_count}}</a>',
                    'label' => 'Jobs',
                ) ,
                4 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
                5 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
        ) ,
    ) ,
    'job_categories' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'slug',
                    'label' => 'Slug',
                ) ,
                3 => array(
                    'name' => 'job_count',
                    'label' => 'Jobs',
                    'template' => '<a href="#/jobs/list?search=%7B%22job_categories%22:{{entry.values.name}}%7D">{{entry.values.job_count}}</a>',
                ) ,
                4 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
                5 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Categories',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'filter',
                    'type' => 'choice',
                    'label' => 'Active?',
                    'choices' => array(
                        0 => array(
                            'label' => 'Active',
                            'value' => 'active',
                        ) ,
                        1 => array(
                            'label' => 'Inactive',
                            'value' => 'inactive',
                        ) ,
                    ) ,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
                2 => 'create',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
        ) ,
    ) ,
    'job_applies' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'job.title',
                    'label' => 'Job',
                ) ,
                2 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                3 => array(
                    'name' => 'job_apply_status.name',
                    'label' => 'Job Apply Status',
                ) ,
                4 => array(
                    'name' => 'cover_letter',
                    'label' => 'Cover Letter',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                ) ,
                5 => array(
                    'name' => 'resume_rating_count',
                    'label' => 'Rating',
                    'template' => '<star-ratings entry="entry"  entity="entity" ></star-ratings>',
                ) ,
                6 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Applies',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'user_id',
                    'label' => 'User',
                    'targetEntity' => 'users',
                    'targetField' => 'username',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                    'remoteCompleteAdditionalParams' => array(
                        'role' => 'freelancer'
                    ) ,
                    'permanentFilters' => array(
                        'role' => 'freelancer'
                    )
                ) ,
                2 => array(
                    'name' => 'job_id',
                    'label' => 'Jobs',
                    'targetEntity' => 'jobs',
                    'targetField' => 'title',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'job_id',
                    'label' => 'Job',
                    'targetEntity' => 'jobs',
                    'targetField' => 'title',
                    'type' => 'reference',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'hire_request_id',
                    'label' => 'Hire Request',
                    'type' => 'number',
                ) ,
                2 => array(
                    'name' => 'cover_letter',
                    'label' => 'Cover Letter',
                    'type' => 'text',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
            ) ,
        ) ,
        'editionview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'user_id',
                    'label' => 'User',
                    'targetEntity' => 'users',
                    'targetField' => 'username',
                    'type' => 'reference',
                    'editable' => false,
                ) ,
                1 => array(
                    'name' => 'job_id',
                    'label' => 'Job',
                    'targetEntity' => 'jobs',
                    'targetField' => 'title',
                    'type' => 'reference',
                    'editable' => false,
                ) ,
                3 => array(
                    'name' => 'cover_letter',
                    'label' => 'Cover Letter',
                    'type' => 'text',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                2 => array(
                    'name' => 'job_apply_status_id',
                    'label' => 'Status',
                    'targetEntity' => 'job_apply_statuses',
                    'targetField' => 'name',
                    'type' => 'reference',
                    'remoteComplete' => true,
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
            ) ,
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'job.title',
                    'label' => 'Job',
                ) ,
                2 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                3 => array(
                    'name' => 'job_apply_status.name',
                    'label' => 'Job Apply Status',
                ) ,
                4 => array(
                    'name' => 'cover_letter',
                    'label' => 'Cover Letter'
                ) ,
                5 => array(
                    'name' => 'resume_rating_count',
                    'label' => 'Rating',
                    'template' => '<star-ratings entry="entry"  entity="entity" ></star-ratings>',
                ) ,
                6 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
        ) ,
    ) ,
    'job_apply_clicks' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                2 => array(
                    'name' => 'job.title',
                    'label' => 'Job',
                ) ,
                3 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Apply Clicks',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'show',
                1 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'user_id',
                    'label' => 'User',
                    'targetEntity' => 'users',
                    'targetField' => 'username',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                    'remoteCompleteAdditionalParams' => array(
                        'role' => 'freelancer'
                    ) ,
                    'permanentFilters' => array(
                        'role' => 'freelancer'
                    )
                ) ,
                2 => array(
                    'name' => 'job_id',
                    'label' => 'Jobs',
                    'targetEntity' => 'jobs',
                    'targetField' => 'title',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                    'type' => 'reference',
                    'remoteComplete' => true,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'job_id',
                    'label' => 'Job',
                    'targetEntity' => 'jobs',
                    'targetField' => 'title',
                    'type' => 'reference',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
            ) ,
        ) ,
    ) ,
    'job_apply_statuses' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'slug',
                    'label' => 'Slug',
                ) ,
                3 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Apply Statuses',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array() ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
                2 => 'create',
            ) ,
        ) ,
    ) ,
    'salary_types' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'id',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
            ) ,
            'title' => 'Salary Types',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'filter',
                    'type' => 'choice',
                    'label' => 'Active?',
                    'choices' => array(
                        0 => array(
                            'label' => 'Active',
                            'value' => 'active',
                        ) ,
                        1 => array(
                            'label' => 'Inactive',
                            'value' => 'inactive',
                        ) ,
                    ) ,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
                2 => 'create',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
        ) ,
        'editionview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'id',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
            ) ,
        ) ,
    ) ,
    'job_flags' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                2 => array(
                    'name' => 'foreign_flag.title',
                    'label' => 'Job',
                ) ,
                3 => array(
                    'name' => 'flag_category.name',
                    'label' => 'Category',
                ) ,
                4 => array(
                    'name' => 'message',
                    'label' => 'Message',
                    'map' => array(
                        0 => 'truncate',
                    ) ,
                ) ,
                5 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Flags',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
            ) ,
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                2 => array(
                    'name' => 'foreign_flag.name',
                    'label' => 'Contest',
                ) ,
                3 => array(
                    'name' => 'flag_category.name',
                    'label' => 'Category',
                ) ,
                4 => array(
                    'name' => 'message',
                    'label' => 'Message',
                ) ,
                5 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
        ) ,
    ) ,
    'job_flag_categories' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'flag_count',
                    'template' => '<a href="#/user_flags/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.flag_count}}</a>',
                    'label' => 'Flags',
                ) ,
                3 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean',
                ) ,
            ) ,
            'title' => 'Job Flag Categories',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'edit',
                1 => 'show',
                2 => 'delete',
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'filters' => array(
                0 => array(
                    'name' => 'q',
                    'pinned' => true,
                    'label' => 'Search',
                    'type' => 'template',
                    'template' => '',
                ) ,
                1 => array(
                    'name' => 'filter',
                    'type' => 'choice',
                    'label' => 'Active',
                    'choices' => array(
                        0 => array(
                            'label' => 'Active',
                            'value' => 'active',
                        ) ,
                        1 => array(
                            'label' => 'Inactive',
                            'value' => 'inactive',
                        ) ,
                    ) ,
                ) ,
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch',
                1 => 'filter',
                2 => 'create',
            ) ,
        ) ,
        'creationview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
            'prepare' => array(
                'class' => 'Job'
            )
        ) ,
        'editionview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'name',
                    'label' => 'Name',
                    'type' => 'string',
                    'validation' => array(
                        'required' => true,
                    ) ,
                ) ,
                1 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'choice',
                    'defaultValue' => false,
                    'validation' => array(
                        'required' => true,
                    ) ,
                    'choices' => array(
                        0 => array(
                            'label' => 'Yes',
                            'value' => true,
                        ) ,
                        1 => array(
                            'label' => 'No',
                            'value' => false,
                        ) ,
                    ) ,
                ) ,
            ) ,
            'prepare' => array(
                'class' => 'Job'
            )
        ) ,
        'showview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'id',
                    'isDetailLink' => true,
                ) ,
                1 => array(
                    'name' => 'name',
                    'label' => 'Name',
                ) ,
                2 => array(
                    'name' => 'flag_count',
                    'template' => '<a href="#/user_flags/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.flag_count}}</a>',
                    'label' => 'Flags',
                ) ,
                3 => array(
                    'name' => 'is_active',
                    'label' => 'Active?',
                    'type' => 'boolean'
                ) ,
            ) ,
        ) ,
    ) ,
    'job_views' => array(
        'listview' => array(
            'fields' => array(
                0 => array(
                    'name' => 'id',
                    'label' => 'ID',
                ) ,
                1 => array(
                    'name' => 'foreign_view.title',
                    'label' => 'Job',
                ) ,
                2 => array(
                    'name' => 'user.username',
                    'label' => 'User',
                ) ,
                3 => array(
                    'name' => 'created_at',
                    'label' => 'Created On',
                ) ,
            ) ,
            'title' => 'Job Views',
            'perPage' => '10',
            'sortField' => '',
            'sortDir' => '',
            'infinitePagination' => false,
            'listActions' => array(
                0 => 'delete'
            ) ,
            'batchActions' => array(
                0 => 'delete',
            ) ,
            'permanentFilters' => '',
            'actions' => array(
                0 => 'batch'
            ) ,
        ) ,
    ) ,
);
$dashboard = array(
    'jobs' => array(
        'addCollection' => array(
            'fields' => array(
                0 => array(
                    'name' => 'user.username',
                    'label' => 'User'
                ) ,
                1 => array(
                    'name' => 'title',
                    'label' => 'Job'
                ) ,
                2 => array(
                    'name' => 'job_status.name',
                    'label' => 'Status'
                ) ,
                3 => array(
                    'name' => 'no_of_opening',
                    'label' => 'No of Openings'
                ) ,
                4 => array(
                    'name' => 'last_date_to_apply',
                    'label' => 'Job Expired Date'
                ) ,
                5 => array(
                    'name' => 'full_address',
                    'label' => 'Job Location'
                )
            ) ,
            'title' => 'Recent Jobs',
            'name' => 'recent_jobs',
            'perPage' => 5,
            'order' => 5,
            'template' => '<div class="col-lg-6"><div class="panel"><ma-dashboard-panel collection="dashboardController.collections.recent_jobs" entries="dashboardController.entries.recent_jobs" datastore="dashboardController.datastore"></ma-dashboard-panel></div></div>'
        )
    )
);
if (isPluginEnabled('Job/JobFlag')) {
    $portfolio_menu = array(
        'Jobs' => array(
            'title' => 'Jobs',
            'icon_template' => '<span class="fa fa-file-text-o"></span>',
            'child_sub_menu' => array(
                'job_flags' => array(
                    'title' => 'Job Flags',
                    'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                    'suborder' => 4
                )
            )
        )
    );
    $menus = merged_menus($menus, $portfolio_menu);
}
if (isPluginEnabled('Job/JobFlag')) {
    $portfolio_menu = array(
        'Master' => array(
            'title' => 'Master',
            'icon_template' => '<span class="glyphicon glyphicon-dashboard"></span>',
            'child_sub_menu' => array(
                'job_flag_categories' => array(
                    'title' => 'Job Flag Categories',
                    'icon_template' => '<span class="glyphicon glyphicon-log-out"></span>',
                    'suborder' => 30
                )
            )
        )
    );
    $menus = merged_menus($menus, $portfolio_menu);
}
if (isPluginEnabled('Common/Flag')) {
    $job_table = array(
        'jobs' => array(
            'listview' => array(
                'fields' => array(
                    13 => array(
                        'name' => 'flag_count',
                        'label' => 'Flags',
                        'template' => '<a href="#/job_flags/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.flag_count}}</a>',
                    )
                )
            ),
            'showview' => array(
                'fields' => array(
                    23 => array(
                        'name' => 'flag_count',
                        'label' => 'Flags',
                        'template' => '<a href="#/service_flags/list?search=%7B%22class%22:%22Job%22,%22foreign_id%22:{{entry.values.id}}%7D">{{entry.values.flag_count}}</a>'
                    )
                )
            )
        )
    );
    $tables = merge_details($tables, $job_table);
}
