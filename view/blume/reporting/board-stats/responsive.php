<?php

$metaTitle = "Board Stats";
include BASE_PATH . 'blume.header.base.php';


?>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    div.dt-buttons {
        float: right;
        margin-left: 20px;
    }
</style>
<!-- -------------- Content -------------- -->

<section id="content" class="table-layout animated fadeIn">
    <!-- -------------- /Column Left -------------- -->

    <div id="boardStats">


        <!-- -------------- Column Center -------------- -->
        <div class="chute chute-center">


            <!-- -------------- Data Filter -------------- -->
            <div class="panel" id="spy2">
                <div class="panel-heading">

                <a id="link2" onclick="csvdata()" style="margin-left:5px;"class="btn btn-info pull-right">
                        Export Data (CSV)
                    </a>
                    <span class="panel-title">Board Statistics</span>
                    <p>An overview of reporting data.</p>

                    <input type="text" name="datefilter" value="" autocomplete="off" placeholder="Select Date" />
                </div>

                <div class="row">
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Monthly Subs Up For Renewal</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="renewals"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Cancelled Monthly Subs</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="cancelled"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Elapsed Monthly Subs</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="elapsed"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Churn Rate</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="churn"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Dynamic Churn Rate (Monthly)</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="monthlyDynamicChurnRate"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="panel panel-tile">
                            <div class="panel-body">
                                <div class="row pv10">
                                    <div class="col-xs-12 pl5 text-center">
                                        <h6 class="text-muted">Dynamic Churn Rate (Annual)</h6>

                                        <h2 class="fs50 mt5 mbn" style="font-size:26px !important;" id="annualDynamicChurnRate"></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= SITE_URL ?>assets/vendor/axios/dist/axios.min.js"></script>


<script type="text/javascript">

    var startDate = "";
    var endDate = "";


        
        $('input[name="datefilter"]').daterangepicker({
            autoUpdateInput: false,

        });
        $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            startDate = $(this).data('daterangepicker').startDate.format('DD/MM/YYYY');
            endDate = $(this).data('daterangepicker').endDate.format('DD/MM/YYYY');

                    var dateFrom = startDate;
                    var dateTo =  endDate;
                    var renewals = 0;
                    var cancelled = 0;
                    var elapsed = 0;
                    var churn = 0.00;
                    var monthlyDynamicChurnRate = 0.00;
                    var annualDynamicChurnRate = 0.00;
             


                        const url1 = "<?= SITE_URL ?>ajax?c=blumeNew&a=get-board-stats&dateFrom=" + dateFrom + "&dateTo=" + dateTo;
                        $.ajax({
                            type: "GET",
                            url: url1,
                            success: function(response) {
                                response = JSON.parse(response);
                                document.getElementById("renewals").innerHTML = response.renewals;
                                document.getElementById("cancelled").innerHTML = response.cancelled;
                                document.getElementById("elapsed").innerHTML = response.elapsed;
                                document.getElementById("churn").innerHTML = response.churn;
                                document.getElementById("monthlyDynamicChurnRate").innerHTML = response.monthlyDynamicChurnRate;
                                document.getElementById("annualDynamicChurnRate").innerHTML = response.annualDynamicChurnRate;
                            },
                        });


                    });


                    function csvdata(){

                        dateFrom2 = startDate;
                        dateTo2 =  endDate;
                        renewals =  document.getElementById("renewals").innerHTML;
                        cancelled =  document.getElementById("cancelled").innerHTML;
                        elapsed =  document.getElementById("elapsed").innerHTML;
                        churn =  document.getElementById("churn").innerHTML;
                        monthlyDynamicChurnRate =  document.getElementById("monthlyDynamicChurnRate").innerHTML;
                        annualDynamicChurnRate =  document.getElementById("annualDynamicChurnRate").innerHTML;
                        const url1 = "<?= SITE_URL ?>ajax?c=blumeNew&a=CSV-data&renewals=" + renewals + "&cancelled=" + cancelled + "&elapsed=" + elapsed + "&churn=" + churn + "&monthlyDynamicChurnRate=" + monthlyDynamicChurnRate + "&annualDynamicChurnRate=" + annualDynamicChurnRate + "&dateFrom2=" + dateFrom2 + "&dateTo2=" + dateTo2;
                        document.getElementById("link2").setAttribute("href",url1);


                        }

                    
   

    
             

</script>


<?php include BASE_PATH . 'blume.footer.base.php'; ?>