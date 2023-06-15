<?php
$this->setControllers(array("blumeReporting"));

$metaTitle = "Reporting - Dashboard";
include BASE_PATH . 'blume.header.base.php';

function generateLink($name, $date, $action = null): string
{
    $url = $action ? SITE_URL . "ajax?c=blumeReporting&a=downloadCSV&action=$action&" : SITE_URL . "blume/reporting/dashboard?";
//    $url = $action ? SITE_URL . "ajax?c=blumeReporting&a=$action&action=$action&" : SITE_URL . "blume/reporting/dashboard?";

    $allTypesOfFilter = [
        'sv_date',
        's_date',
        'avg_sv_date',
        'visitors',
        'uc_date',
        'cr_date',
        'bsc_date',
        'crs_date',
        'rpc_date',
        'sprc_date',
        'ncpc_date',
        'ncpcat_date',
        'rpacount_date',
        'comRPC_date',
        'ltvbd_date',
        'repeattr_date',
        'apfc_date',
        'ltvbcfp_date',
        'certord_date',
    ];

    foreach ($allTypesOfFilter as $filterType) {
        if ($name === 'all') {
            $url .= $filterType . '=' . $date . '&';
        } else {
            $value = $filterType == $name ? $date ?? $_GET[$name] : $_GET[$filterType];
            $url .= $filterType . '=' . $value . '&';
        }

    }

    return $url;
}

?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel report-page" id="spy2">
            <div class="panel-heading mb30">
                <span class="panel-title">Reporting: Dashboard</span>
            </div>

            <div class="d-flex types-of-filter pv10 mb20">
                <div class="date-filter mr5">
                    <a href="<?= generateLink('all', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                </div>
                <div class="date-filter mr5">
                    <a href="<?= generateLink('all', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                </div>
                <div class="date-filter mr5">
                    <a href="<?= generateLink('all', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                </div>
                <div class="date-filter mr5">
                    <a href="<?= generateLink('all', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                </div>
                <div class="mr5">
                    <input class="custom-date" data-name="all" type="date" />
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Sales (Value)</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value salesReportValue">loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['sv_date'] || !$_GET['sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('sv_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('sv_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('sv_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('sv_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>
                                        <div class="mr5"><input class="custom-date" data-name="sv_date" type="date" value="<?=$_GET['sv_date']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('sv_date', $_GET['sv_date'], 'salesReportValue') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                              class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Sales</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value salesReport">loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['s_date'] || !$_GET['s_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('s_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['s_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('s_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['s_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('s_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['s_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('s_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5">
                                            <input class="custom-date" data-name="s_date" type="date" value="<?=$_GET['s_date']?>"/>
                                        </div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('s_date', $_GET['s_date'], 'salesReport') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">AVG. Sales Value</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value avgSalesValue">loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['avg_sv_date'] || !$_GET['avg_sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('avg_sv_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['avg_sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('avg_sv_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['avg_sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('avg_sv_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['avg_sv_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('avg_sv_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="avg_sv_date" type="date" value="<?=$_GET['avg_sv_date']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('avg_sv_date', $_GET['avg_sv_date'], 'avgSalesValue') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Avg Lifetime value of customer</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value visitors">loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['visitors'] || !$_GET['visitors'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('visitors', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['visitors'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('visitors', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['visitors'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('visitors', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['visitors'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('visitors', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="visitors" type="date" value="<?=$_GET['visitors']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('visitors', $_GET['visitors'], 'visitors') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">LTV by date purchased</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value ltvByDate">loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['ltvbd_date'] || !$_GET['ltvbd_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('ltvbd_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['ltvbd_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('ltvbd_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['ltvbd_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('ltvbd_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['ltvbd_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('ltvbd_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="ltvbd_date" type="date" value="<?=$_GET['ltvbd_date']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('ltvbd_date', $_GET['ltvbd_date'], 'ltvByDate') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Completion Rate</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value completionRate">Loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['cr_date'] || !$_GET['cr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('cr_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['cr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('cr_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['cr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('cr_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['cr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('cr_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5">
                                            <input class="custom-date" data-name="cr_date" type="date" value="<?=$_GET['cr_date']?>"/>
                                        </div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('cr_date', $_GET['cr_date'], 'completionRate') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Unstarted Courses</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value unstartedCourses">Loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['uc_date'] || !$_GET['uc_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('uc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['uc_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('uc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['uc_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('uc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['uc_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('uc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="uc_date" type="date" value="<?=$_GET['uc_date']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('uc_date', $_GET['uc_date'], 'unstartedCourses') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Repeat transaction rate</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value repeatTransactionRate">Loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['repeattr_date'] || !$_GET['repeattr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('repeattr_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['repeattr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('repeattr_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['repeattr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('repeattr_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['repeattr_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('repeattr_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="repeattr_date" type="date" value="<?=$_GET['repeattr_date']?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('repeattr_date', $_GET['repeattr_date'], 'repeatTransactionRate') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xl-3">
                    <div class="panel panel-tile h-204">
                        <div class="panel-body pv5">
                            <div class="row pv10">
                                <div class="col-md-12 text-center">
                                    <h4 class="mt5">Certificate orders</h4>

                                    <h2 class="fs50 pv10 mbn sales-report-value certificateOrders">Loading...</h2>

                                    <div class="d-flex types-of-filter pv10"
                                         data-class="sales-report-value"
                                    >
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['certord_date'] || !$_GET['certord_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('certord_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['certord_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('certord_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['certord_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('certord_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                        </div>
                                        <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['certord_date'] ? 'active' : '' ?>">
                                            <a href="<?= generateLink('certord_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                        </div>

                                        <div class="mr5"><input class="custom-date" data-name="certord_date" type="date" value="<?= $_GET['certord_date'] ?>"/></div>
                                        <div class="mr5">
                                            <a href="<?= generateLink('certord_date', $_GET['certord_date'], 'certificateOrders') ?>" target="_blank">
                                                <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                     class="img-responsive mauto" alt="" width="20" />
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-xl-6">
                    <div>
                        <div class="panel panel-tile h-94">
                            <div class="panel-body pv5 h-94 mb15">
                                <div class="d-flex to-center">
                                    <div class="ml20"><h4 class="mt5">Completion rate vs Spend</h4></div>
                                    <div class="ml20"><h4 class="mt5 completionRateSpend">Loading...</h4></div>
                                    <div class="ml-auto">
                                        <a href="<?= generateLink('no_date', $_GET['no_date'], 'completionRateSpend') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-94 mb15">
                        <div class="panel-body pv5 h-94">
                            <div class="d-flex to-center">
                                <div class="ml20"><h4 class="mt5">Users with unstarted courses</h4></div>
                                <div class="ml20"><h4 class="mt5 usersUnstartedCourses">Loading...</h4></div>
                                <div class="ml-auto">
                                    <a href="<?= generateLink('no_date', $_GET['no_date'], 'usersUnstartedCourses') ?>" target="_blank">
                                        <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                             class="img-responsive mauto" alt="" width="20" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="row">-->
<!--                <div class="col-sm-12 col-xl-12">-->
<!--                    <div class="panel panel-tile h-94 mb15">-->
<!--                        <div class="panel-body pv5 h-94">-->
<!--                            <div class="d-flex to-center">-->
<!--                                <div class="ml20"><h4 class="mt5">Course Flow (select course to download report)</h4></div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="row mt20">
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Best Selling Courses</h4></div>

                                <div class="bestSellingCourses" data-value="title" data-key="count_sales">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['bsc_date'] || !$_GET['bsc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('bsc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['bsc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('bsc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['bsc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('bsc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['bsc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('bsc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5"><input class="custom-date" data-name="bsc_date" type="date" value="<?=$_GET['bsc_date']?>"/></div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('bsc_date', $_GET['bsc_date'], 'bestSellingCourses') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Course Rating</h4></div>

                                <div class="courseRating" data-value="title" data-key="price" data-additional="averageRating">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['crs_date'] || !$_GET['crs_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('crs_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['crs_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('crs_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['crs_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('crs_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['crs_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('crs_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="crs_date" type="date" value="<?=$_GET['crs_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('crs_date', $_GET['crs_date'], 'courseRating') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt20">
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Revenue Per Course</h4></div>

                                <div class="revenuePerCourse" data-value="title" data-key="total">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['rpc_date'] || !$_GET['rpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['rpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['rpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['rpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5"><input class="custom-date" data-name="rpc_date" type="date" value="<?=$_GET['rpc_date']?>"/></div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('rpc_date', $_GET['rpc_date'], 'revenuePerCourse') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">LTV by course first purchased</h4></div>

                                <div class="mh-250 overflow-auto">
                                    <ul>
                                        <?php foreach ($this->blumeReporting->LTVByCourseFirstPurchased() as $data) {?>
                                            <li>
                                                <span class=""><?= ORM::For_Table("courses")->findOne($data['courseID'])->title ?? '' ?> - </span>
                                                <b><?= ORM::For_Table("orders")->where('accountID', $data['accountID'])->sum('total') ?? 0 ?></b>
                                            </li>
                                        <?php }?>
                                    </ul>
                                </div>

                                <?php if (!$this->blumeReporting->LTVByCourseFirstPurchased()) {?>
                                    <div class="text-center">
                                        <h2>Empty</h2>
                                    </div>
                                <?php }?>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['ltvbcfp_date'] || !$_GET['ltvbcfp_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ltvbcfp_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['ltvbcfp_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ltvbcfp_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['ltvbcfp_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ltvbcfp_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['ltvbcfp_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ltvbcfp_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="ltvbcfp_date" type="date" value="<?=$_GET['ltvbcfp_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('ltvbcfp_date', $_GET['ltvbcfp_date'], 'LTVByCourseFirstPurchased') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt20">
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">No customers per course</h4></div>

                                <div class="numberCustomersPerCourse" data-value="title" data-key="count_record">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['ncpc_date'] || !$_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="ncpc_date" type="date" value="<?=$_GET['ncpc_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('ncpc_date', $_GET['ncpc_date'], 'numberCustomersPerCourse') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">No customers per category</h4></div>

                                <div class="numberCustomersPerCategory" data-value="title" data-key="count_record">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['ncpcat_date'] || !$_GET['ncpcat_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpcat_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['ncpcat_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpcat_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['ncpcat_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpcat_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['ncpcat_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpcat_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="ncpcat_date" type="date" value="<?=$_GET['ncpcat_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('ncpcat_date', $_GET['ncpcat_date'], 'numberCustomersPerCategory') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt20">
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Sales per course</h4></div>

                                <div class="mh-250 overflow-auto salesPerCourse" data-value="title" data-key="total">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['ncpc_date'] || !$_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['ncpc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('ncpc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="ncpc_date" type="date" value="<?=$_GET['ncpc_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('ncpc_date', $_GET['ncpc_date'], 'salesPerCourse') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Revenue per customer</h4></div>

                                <div class="mh-250 overflow-auto revenuePerCustomer" data-value="name" data-key="total">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['rpacount_date'] || !$_GET['rpacount_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpacount_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['rpacount_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpacount_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['rpacount_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpacount_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['rpacount_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('rpacount_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="rpacount_date" type="date" value="<?=$_GET['rpacount_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('rpacount_date', $_GET['rpacount_date'], 'revenuePerCustomer') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt20">
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Completion rate per course</h4></div>

                                <div class="mh-250 overflow-auto completionRatePerCourse" data-value="title" data-key="rate">Loading...</div>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['comRPC_date'] || !$_GET['comRPC_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('comRPC_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['comRPC_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('comRPC_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['comRPC_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('comRPC_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['comRPC_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('comRPC_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="comRPC_date" type="date" value="<?=$_GET['comRPC_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('comRPC_date', $_GET['comRPC_date'], 'completionRatePerCourse') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-xl-6">
                    <div class="panel panel-tile h-380 mb15">
                        <div class="panel-body pv5">
                            <div>
                                <div class="ml20 pv10 text-center"><h4 class="mt5">Amount paid for course</h4></div>

                                <div class="mh-250 overflow-auto">
                                    <ul>
                                        <?php foreach ($this->blumeReporting->amountPaidForCourse() as $data) {?>
                                            <li>
                                                <div class="">Course Title: <?= $data['title'] ?? 'N/A' ?></div>
                                                <div class="">Order Total: <?= $data['total'] ?? 'N/A' ?></div>
                                                <div class="">Order Tax: <?= $data['tax'] ?? 'N/A' ?></div>
                                                <div class="">Order Fee: <?= $data['fee'] ?? 'N/A' ?></div>
                                                <hr />
                                            </li>
                                        <?php }?>
                                    </ul>
                                </div>

                                <?php if (!$this->blumeReporting->amountPaidForCourse()) {?>
                                    <div class="text-center">
                                        <h2>Empty</h2>
                                    </div>
                                <?php }?>

                                <div class="d-flex types-of-filter pv10">
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->format('Y-m-d') == $_GET['apfc_date'] || !$_GET['apfc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('apfc_date', \Carbon\Carbon::now()->format('Y-m-d'))?>">Day</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subWeek()->format('Y-m-d') == $_GET['apfc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('apfc_date', \Carbon\Carbon::now()->subWeek()->format('Y-m-d')) ?>">Week</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subMonth()->format('Y-m-d') == $_GET['apfc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('apfc_date', \Carbon\Carbon::now()->subMonth()->format('Y-m-d')) ?>">Month</a>
                                    </div>
                                    <div class="date-filter mr5 <?= \Carbon\Carbon::now()->subYear()->format('Y-m-d') == $_GET['apfc_date'] ? 'active' : '' ?>">
                                        <a href="<?= generateLink('apfc_date', \Carbon\Carbon::now()->subYear()->format('Y-m-d')) ?>">Year</a>
                                    </div>
                                    <div class="mr5">
                                        <input class="custom-date" data-name="apfc_date" type="date" value="<?=$_GET['apfc_date']?>"/>
                                    </div>
                                    <div class="mr5">
                                        <a href="<?= generateLink('apfc_date', $_GET['apfc_date'], 'amountPaidForCourse') ?>" target="_blank">
                                            <img src="<?= SITE_URL ?>assets/images/file-icon.png"
                                                 class="img-responsive mauto" alt="" width="20" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- -------------- DEMO Break -------------- -->
        <div class="mv40"></div>


    </div>
    <!-- -------------- /Column Center -------------- -->

</section>
<script type="text/javascript">
    const generateLink = (name,date,baseUrl = null) => {
        let redirectUrl = baseUrl ? baseUrl : "<?=SITE_URL . 'blume/reporting/dashboard?'?>";

        const url_string = window.location.href
        const url = new URL(url_string);

        const allTypesOfFilter = [
            'sv_date',
            's_date',
            'avg_sv_date',
            'visitors',
            'uc_date',
            'cr_date',
            'bsc_date',
            'crs_date',
            'rpc_date',
            'sprc_date',
            'ncpc_date',
            'ncpcat_date',
            'rpacount_date',
            'comRPC_date',
            'ltvbd_date',
            'repeattr_date',
            'apfc_date',
            'ltvbcfp_date',
            'certord_date',
        ];

        allTypesOfFilter.map((filterType) => {
            if (name === 'all') {
                redirectUrl += filterType + '=' + date + '&';
            } else {
                let param = filterType === name ? (date || url.searchParams.get(name)) : url.searchParams.get(filterType);
                redirectUrl += filterType + '=' + param + '&';
            }
        })

        return redirectUrl;
    }

    function renderAllReports() {
        const reports = [
           'salesReportValue',
           'salesReport',
           'avgSalesValue',
           'visitors',
           'ltvByDate',
           'completionRate',
           'unstartedCourses',
           'repeatTransactionRate',
           'certificateOrders',
           'completionRateSpend',
           'usersUnstartedCourses',
           'bestSellingCourses',
           'courseRating',
           'revenuePerCourse',
           'numberCustomersPerCourse',
           'numberCustomersPerCategory',
           'salesPerCourse',
           'revenuePerCustomer',
           'completionRatePerCourse',
        ];

        const baseUrl = generateLink(null,null,"<?= SITE_URL ?>ajax?c=blumeReporting&a=dashboard&");

        reports.map(report => {
            $.ajax({
                url: baseUrl + "action=" + report,
                type: "GET",
                data: {},
                dataType: 'JSON',
                async: true,
                cache: false,
                success: function (response) {
                    const selector = $('.' + report);

                    if (typeof response.data === 'object') {
                        const data = response.data;
                        const value = selector.data('value');
                        const key = selector.data('key');
                        const additional = selector.data('additional');

                        if (data.length < 1) {
                            selector.html("<div class='text-center'> <h2>Empty</h2></div>");
                            return;
                        }

                        let content = "<ul>"

                        response.data.map(item => {
                            content += "<li><span>" + (item[value] ? item[value] : 'N/A') + "</span>"

                            if (key === 'total' || key === 'price') {
                                content += " - <span><b>£" + (Math.round(item[key] * 100) / 100).toFixed(2) + "</b></span>"
                            } else {
                                content += " - <span>" + item[key] + "</span>"
                            }

                            if (additional) content += "<span>/" + (item[additional] ? item[additional] : 0) + "</span>"

                            content += "</li>"
                        })

                        content += "<ul>"
                        selector.html(content);
                    } else {
                        selector.text(response.data);
                    }
                },
                error: function () {
                    $('.' + report).text('N/A');
                }
            });
        })
    }

    $(document).ready(function(){
        $(document).on('change', '.custom-date', function () {
            window.location.href = generateLink($(this).data('name'), $(this).val())
        })

        renderAllReports();
    })
</script>
<style>
    .report-page .panel ul {
        list-style: decimal;
        color: #000;
    }

    .report-page .panel {
        background: #f3f7fa;
        padding-bottom: 10px;
    }

    .custom-date {
        width: 50px;
        height: 20px;
    }

    .d-flex {
        display: flex;
    }

    .types-of-filter {
        align-items: center;
        justify-content: center;
        color: #000;
        cursor: pointer;
    }

    .types-of-filter a {
        color: #000;
    }

    .types-of-filter .active a {
        color: #67d3e0;
    }

    .h-94 {
        height: 94px;
    }

    .h-204 {
        min-height: 204px;
    }

    .mh-250 {
        max-height: 250px;
    }

    .h-380 {
        height: 380px;
    }

    .to-center {
        align-items: center;
        height: 100%;
    }

    .ml-auto {
        margin-left: auto;
    }

    .overflow-auto {
        overflow: auto;
    }

</style>

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
