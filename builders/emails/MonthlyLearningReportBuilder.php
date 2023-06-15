<?php
require_once('EmailTemplateBuilder.php');
require_once(__DIR__ . '/../../repositories/CourseTimeProgressRepository.php');
require_once(__DIR__ . '/../../repositories/CoursesAssignedProgressRepository.php');
require_once(__DIR__ . '/../../repositories/AccountsRepository.php');
require_once(__DIR__ . '/../../repositories/RewardsAssignedRepository.php');
require_once(__DIR__ . '/../../repositories/RewardsRepository.php');

class MonthlyLearningReportBuilder extends EmailTemplateBuilder
{

    public const EMAIL_TEMPLATE_TYPE = 'monthly-learning-reports';

    public const SITE_URL = 'https://newskillsacademy.co.uk';
    public const DASHBOARD_COURSES_URL = self::SITE_URL . '/dashboard/courses';
    public const DASHBOARD_REWARDS_URL = self::SITE_URL . '/dashboard/rewards';
    public const COURSES_URL = self::SITE_URL . '/courses';

    /**
     * @var MonthlyLearningReportBuilder
     */

    protected $emailTemplateBuilder;

    private $blockContainerStyleOptions = [
        'margin-top' => '30px',
        'border-spacing' => '20px 0px'
    ];
    private $defaultStyleOptions = [
        'color' => '#08586c',
        'background-color' => 'transparent',
        'border' => '3px solid #248CAB',
        'border-radius' => '10px',
        '-webkit-border-radius' => '10px',
        '-moz-border-radius' => '10px',
        'padding-top' => '20px',
        'padding-bottom' => '20px',
        'padding-left' => '20px',
        'padding-right' => '20px',
        'font-family' => '\'Poppins, Helvetica\', ' . 'sans-serif',
    ];

    protected $blockHeaderStyleOptions = [
        'color' => '#000000',
        'width' => '100%',
        'font-size' => '24px',
        'font-weight' => '600',
        'margin-top' => '0',
        'margin-bottom' => '20px',
        'text-align' => 'center',
    ];

    private $rewardBlockHeaderStyles = [
        'font-size' => '16px',
        'font-weight' => '600',
        'margin-top' => '0px',
        'margin-bottom' => '10px',
        'width' => '100%',
        'text-align' => 'center',
    ];

    private $coursesRepository;
    private $courseTimeProgressRepository;
    private $accountsRepository;
    private $rewardsAssignedRepository;
    private $rewardsRepository;

    public function __construct()
    {
        parent::__construct();
        $this->emailTemplateBuilder = new EmailTemplateBuilder();
        $this->courseTimeProgressRepository = new CourseTimeProgressRepository();
        $this->coursesRepository = new CoursesRepository();
        $this->accountsRepository = new AccountsRepository();
        $this->rewardsAssignedRepository = new RewardsAssignedRepository();
        $this->rewardsRepository = new RewardsRepository();
    }

    private function renderIntroText()
    {
        return $this->buildBlockGrid(
            1,
            [
                $this->fetchTemplate('sections/text',
                    ['TEXT' => "Hi {$this->account->get('firstname')}, welcome to your learning report for April 2021. Below you will see your key learning stats for this month."]
                )
            ],
            [
                'text-align' => 'left',
                'padding-top' => '0px',
                'padding-bottom' => '0px'
            ],
            $this->blockContainerStyleOptions
        );
    }

    private function renderHeader()
    {
        return $this->fetchTemplate(
            'layouts/header',
            [],
            self::EMAIL_TEMPLATE_TYPE
        );
    }

    private function buildHourlyReportData()
    {
        $progressPastMonth = $this->courseTimeProgressRepository->calculateCourseTimeAccount(
            $this->account->get('id'),
            date('Y-m-d', strtotime('-6 Month')),
            date('Y-m-d', $this->getCurrentTime())
        );
        $progressLastMonth = $this->courseTimeProgressRepository->calculateCourseTimeAccount(
            $this->account->get('id'),
            date('Y-m-d', strtotime('-8 Month')),
            date('Y-m-d', strtotime('-6 Month'))
        );
        $hours = 0;
        if (isset($progressPastMonth['hours'])) {
            $hours = $progressPastMonth['hours'];
        }
        $hoursLastMonth = 0;
        if (isset($progressLastMonth['hours'])) {
            $hoursLastMonth = $progressLastMonth['hours'];
        }
        $textPlaceholder = "This month you studied for a total of %s hours. %s";
        if ($hours > $hoursLastMonth) {
            $hoursDiff = $hours - $hoursLastMonth;
            $text = sprintf($textPlaceholder,
                $hours,
                "That's an increase of {$hoursDiff} hours from last month"
            );
            $arrowImgSource = self::SITE_URL . "/assets/images/arrow-report-up.png";
        } else if ($hours < $hoursLastMonth) {
            $hoursDiff = $hoursLastMonth - $hours;
            $text = sprintf($textPlaceholder,
                $hours,
                "That's an decrease of {$hoursDiff} hours from last month"
            );
            $arrowImgSource = self::SITE_URL . "/assets/images/arrow-report-down.png";
        } else {
            $text = "That's equivalent to the hours from last month";
            $arrowImgSource = false;
        }
        return [
            'hours' => $hours,
            'text' => $text,
            'img_src' => $arrowImgSource
        ];
    }

    private function renderHourlyReportBlock()
    {
        $hourlyReportData = $this->buildHourlyReportData();

        $hourlyReportBlocks = $this->buildBlockGrid(
            1,
            [
                $this->fetchTemplate(
                    'sections/hourly-report/report',
                    [
                        'HOURS' => $hourlyReportData['hours'],
                        'HOURS_IMG_SRC' => self::SITE_URL . "/assets/images/clock-report.png",
                        'HOURS_IMG_HREF' => self::SITE_URL,
                        'HOURS_LABEL' => "hours",
                        'TEXT' => $hourlyReportData['text'],
                        'ARROW_IMG_SRC' => $hourlyReportData['img_src']
                    ],
                    self::EMAIL_TEMPLATE_TYPE
                ),
            ],
            [
                'padding-left' => '2px',
                'padding-right' => '2px',
            ],
            [
                'width' => '100%',
                'padding-left' => '1px',
                'padding-right' => '1px',
            ]
        );
        return $this->buildBlockGrid(
            1,
            [$hourlyReportBlocks],
            array_merge($this->defaultStyleOptions, [
                'padding-top' => '0px',
                'padding-bottom' => '0px'
            ]),
            $this->blockContainerStyleOptions
        );

    }

    private function renderSingleStatBlocks()
    {
        $completedCoursesCount = $this->coursesRepository->fetchCompletedCoursesCount(
            $this->getAccount()->get('id')
        );
        $daysLoggedIn = $this->accountsRepository->fetchDaysLoggedIn(
            $this->getAccount()->get('id')
        );
        $activeCoursesCount = $this->coursesRepository->fetchActiveCoursesCount(
            $this->getAccount()->get('id')
        );
        $data = [
            [
                'STAT_VALUE' => $completedCoursesCount,
                'STAT_TEXT_1' => 'Course',
                'STAT_TEXT_2' => 'Completions',
            ],
            [
                'STAT_VALUE' => $daysLoggedIn,
                'STAT_TEXT_1' => 'Days',
                'STAT_TEXT_2' => 'Logged In'
            ],
            [
                'STAT_VALUE' => $activeCoursesCount,
                'STAT_TEXT_1' => 'Active',
                'STAT_TEXT_2' => 'Courses'
            ]
        ];
        return $this->buildBlockGrid(
            3,
            array_map(function ($config) {
                return $this->fetchTemplate(
                    'sections/single-stat',
                    $config
                );
            }, $data),
            array_merge($this->defaultStyleOptions, ['height' => '122px']),
            $this->blockContainerStyleOptions
        );

    }

    private function getIncompleteCoursesData()
    {
        $this->coursesRepository->setResultFormat(BaseRepository::RESULT_FORMAT_RAW);
        $courseAssignedTbl = (new CoursesAssignedRepository())->getTableName();
        $this->coursesRepository->setOrderBy("{$courseAssignedTbl}.percComplete asc");
        return $this->coursesRepository->fetchInCompletedCourses(
            ['title', 'slug', 'completed', 'percComplete'],
            $this->getAccount()->get('id')
        );
    }

    private function renderIncompleteCoursesSection()
    {
        $incompleteCourses = $this->getIncompleteCoursesData();
        if (!is_array($incompleteCourses) || !count($incompleteCourses)) {
            $listContent =  $this->fetchTemplate(
                'sections/text',
                [
                    'TEXT' => 'You have no incomplete courses',
                    'STYLE' => [
                        'text-align' => 'center',
                        'width' => '100%'
                    ]
                ]
            );
        } else {
            $ctaData = [
                'LINK_TEXT' => 'Continue',
                'LINK_HREF' => self::DASHBOARD_COURSES_URL,
            ];
            $data = array_map(function (ORM $item) use ($ctaData) {
                $percentage = (int)$item->get('percComplete');
                return [
                    'TEXT' => $item->get('title'),
                    'PERCENTAGE' => $percentage,
                    'PERCENTAGE_REMAIN' => (100 - $percentage),
                    'CTA' => $ctaData
                ];
            }, $incompleteCourses);

            $listContent = $this->buildList(
                array_map(function ($config) {
                    return $this->fetchTemplate(
                        'sections/incomplete-courses/list-item',
                        $config,
                        self::EMAIL_TEMPLATE_TYPE
                    );
                }, $data)
            );
        }
        return $this->buildBlockGrid(
            1,
            [
                $this->fetchTemplate(
                    'sections/incomplete-courses/wrapper',
                    [
                        'BLOCK_HEADER' => [
                            'HEADING' => 'Your Incomplete Courses',
                        ],
                        'CONTENT' => $listContent,
                    ],
                    self::EMAIL_TEMPLATE_TYPE
                )
            ],
            $this->defaultStyleOptions,
            $this->blockContainerStyleOptions
        );
    }

    private function buildLeaderBoardData()
    {
        $rewardPoints = [];
        $highestRewardPoints = $this->rewardsAssignedRepository->fetchHighestAccountRewardPoints(
            ['firstname', 'lastname', 'id']
        );
        if ($highestRewardPoints instanceof ORM) {
            $rewardPoints[] = $highestRewardPoints;
        }
        $fetchAccountRewardPoints = $this->rewardsRepository->fetchRewardPointsByAccount(
            $this->getAccountId(),
            ['firstname', 'lastname', 'id']
        );
        $rewardPoints = array_merge($rewardPoints, $fetchAccountRewardPoints);
        $trophyImgSrc = self::SITE_URL . '/assets/images/courseSuccess.png';
        $data = [];
        foreach ($rewardPoints as $index => $item) {

            $itemData = [
                'TROPHY_IMG' => $trophyImgSrc,
                'FIRSTNAME' => $item->get('firstname'),
                'LASTNAME' => $item->get('lastname'),
                'POINTS' => "{$item->get('rewardPoints')}pts",
            ];
            if ($index === 0) {
                $itemData['POSITION'] = '1st';
                $itemData['WINNER_TEXT'] = 'Winner';
            } else {
                $itemData['POSITION'] = (string)($index + 1);
            }
            if ($this->getAccountId() === (int)$item->get('id')) {
                $itemData['IS_CURRENT_ACCOUNT'] = true;
            }
            $data[] = $itemData;
        }
        return $data;
    }

    private function renderLeaderboardPositionSection()
    {
        $data = $this->buildLeaderBoardData();
        $list = $this->buildList(
            array_map(function ($item) {
                return $this->fetchTemplate(
                    'sections/leaderboard-position/list-item',
                    $item,
                    self::EMAIL_TEMPLATE_TYPE
                );
            }, $data)
        );

        $wrapper = $this->fetchTemplate(
            'sections/leaderboard-position/wrapper',
            [
                'BLOCK_HEADER' => [
                    'HEADING' => 'Your Leaderboard Position',
                ],
                'FOOTER_TEXT' => 'The monthly winner receives a £50 Amazon voucher',
                'LIST' => $list,
            ],
            self::EMAIL_TEMPLATE_TYPE
        );
        return $this->buildBlockGrid(
            1,
            [
                $wrapper
            ],
            $this->defaultStyleOptions,
            $this->blockContainerStyleOptions
        );
    }

    private function renderUnclaimedRewardsSection()
    {
        $unclaimedAwards = $this->rewardsAssignedRepository->fetchUnclaimedRewards(
            ["{$this->rewardsRepository->getTableName()}.short as short"],
            $this->getAccountId(),
            3
        );
        if (!is_array($unclaimedAwards) || !count($unclaimedAwards)) {
            $listContent =  $this->fetchTemplate(
                'sections/text',
                [
                    'TEXT' => 0,
                    'STYLE' => [
                        'font-size' => '44px',
                        'text-align' => 'center',
                        'width' => '100%'
                    ]
                ]
            );
        } else {
            $data = array_map(function ($row) {
                return [
                    'TEXT' => $row['name'],
                    'CTA' => [
                        'LINK_TEXT' => 'CLAIM',
                        'LINK_HREF' => self::DASHBOARD_REWARDS_URL,
                    ]
                ];
            }, $unclaimedAwards);
            $listContent = $this->buildList(
                array_map(function ($item) {
                    return $this->fetchTemplate(
                        'sections/rewards/unclaimed-rewards/list-item',
                        $item,
                        self::EMAIL_TEMPLATE_TYPE
                    );
                }, $data),
                [],
                ['width' => 'auto']
            );
        }
        return $this->fetchTemplate(
            'sections/rewards/unclaimed-rewards/unclaimed-rewards',
            [
                'BLOCK_HEADER' => [
                    'HEADING' => 'Unclaimed Rewards',
                    'styles' => $this->rewardBlockHeaderStyles
                ],
                'LIST' => $listContent,
            ],
            self::EMAIL_TEMPLATE_TYPE
        );

    }


    private function renderClaimedRewardsSection()
    {
        $unclaimedAwards = $this->rewardsAssignedRepository->fetchClaimedRewards([], $this->getAccountId());
        if (!is_array($unclaimedAwards) || !count($unclaimedAwards)) {
            $listContent =  $this->fetchTemplate(
                'sections/text',
                [
                    'TEXT' => 0,
                    'STYLE' => [
                        'font-size' => '44px',
                        'text-align' => 'center',
                        'width' => '100%'
                    ]
                ]
            );
        } else {
            $iconImgSrc = self::SITE_URL . '/assets/images/list_check.png';
            $data = array_map(function ($row) use ($iconImgSrc) {
                return [
                    'TEXT' => $row['name'],
                    'IMG_SRC' => $iconImgSrc,
                ];
            }, $unclaimedAwards);
            $listContent = $this->buildList(
                array_map(function ($config) {
                    return $this->fetchTemplate(
                        'sections/rewards/claimed-rewards/list-item',
                        $config,
                        self::EMAIL_TEMPLATE_TYPE
                    );
                }, $data)
            );
        }
        return $this->fetchTemplate(
            'sections/rewards/claimed-rewards/claimed-rewards',
            [
                'BLOCK_HEADER' => [
                    'HEADING' => 'Claimed Rewards',
                    'styles' => $this->rewardBlockHeaderStyles
                ],
                'LIST' => $listContent,
            ],
            self::EMAIL_TEMPLATE_TYPE
        );
    }


    private function renderRewardsPointsTotal()
    {
        $rewardPointsTotal = $this->rewardsAssignedRepository->fetchAccountRewardPoints(
            $this->getAccountId()
        );

        return $this->fetchTemplate(
            'sections/rewards/reward-points',
            [
                'BLOCK_HEADER' => [
                    'HEADING' => 'Reward Points Total',
                    'styles' => $this->rewardBlockHeaderStyles
                ],
                'IMG_SRC' => self::SITE_URL . '/assets/images/courseSuccess.png',
                'POINTS' => $rewardPointsTotal
            ],
            self::EMAIL_TEMPLATE_TYPE
        );
    }

    private function renderRewardsStatBlocks()
    {
        $rewardsPointsTotal = $this->renderRewardsPointsTotal();
        $unclaimedRewards = $this->renderUnclaimedRewardsSection();
        $claimedRewards = $this->renderClaimedRewardsSection();
        return $this->buildBlockGrid(
            3,
            [
                $rewardsPointsTotal,
                $unclaimedRewards,
                $claimedRewards,
            ],
            array_merge($this->defaultStyleOptions, [
                'vertical-align' => 'top',
                'font-size' => '12px',
                'padding-top' => '10px',
                'padding-bottom' => '10px',
                'padding-left' => '10px',
                'padding-right' => '10px',
                'height' => '180px'
            ]),
            $this->blockContainerStyleOptions
        );
    }

    private function renderMostPopularCourseSection()
    {
        $findMostPopularCourses = $this->coursesRepository->fetchMostPopularCourses([], 10);
        $popularCourses = [];
        foreach ($findMostPopularCourses as $index => $row) {
            $popularCourses[] = [
                'pos' => $index + 1,
                'text' => $row['title'],
                'href' => self::SITE_URL . '/course/' . "{$row['slug']}"
            ];
        }

        $popularCoursesListBlockData = [];
        for ($i = 0; $i < count($popularCourses); $i = $i + 5) {
            $popularCoursesListBlockData[] = array_slice($popularCourses, $i, 5);
        }

        $listHtmlData = [];
        foreach ($popularCoursesListBlockData as $listBlockDataItem) {
            $listHtmlData[] = $this->buildList(
                array_map(function ($config) {
                    return $this->fetchTemplate(
                        'sections/most-popular-course/list-item',
                        $config,
                        self::EMAIL_TEMPLATE_TYPE
                    );
                }, $listBlockDataItem)
            );
        }
        $maxBlockRow = 2;
        $listContentData = [];
        for ($i = 0; $i < count($listHtmlData); $i = $i + $maxBlockRow) {
            $sliceData = array_slice($listHtmlData, $i, $maxBlockRow);
            $listContentData[] = $this->buildBlockGrid(
                $maxBlockRow,
                $sliceData,
                [
                    'text-align' => 'left',
                    'font-size' => '12px',
                    'color' => '#000000',
                    'margin-top' => '0',
                ]
            );
        }
        return $this->buildBlockGrid(
            1,
            [
                $this->fetchTemplate(
                    'sections/most-popular-course/wrapper',
                    [
                        'BLOCK_HEADER' => [
                            'HEADING' => 'Most Popular Course April 2021',
                        ],
                        'CTA' => [
                            'LINK_TEXT' => 'SEE ALL COURSES',
                            'LINK_HREF' => self::COURSES_URL,
                        ],
                        'LIST' => implode(' ', $listContentData),
                    ],
                    self::EMAIL_TEMPLATE_TYPE
                )
            ],
            $this->defaultStyleOptions,
            $this->blockContainerStyleOptions
        );
    }

    public function renderReportHtml()
    {
        $content = "";
        $header = $this->renderHeader();
        $content .= $this->renderIntroText();
        $content .= $this->renderHourlyReportBlock();
        $content .= $this->renderSingleStatBlocks();
        $content .= $this->renderIncompleteCoursesSection();
        $content .= $this->renderLeaderboardPositionSection();
        $content .= $this->renderRewardsStatBlocks();
        $content .= $this->renderMostPopularCourseSection();
        return $this->fetchTemplate('layout',
            ['CONTENT' => $content, 'HEADER' => $header],
            self::EMAIL_TEMPLATE_TYPE
        );
    }

}
