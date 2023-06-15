<?php
$this->setControllers(array("blumeReporting"));

// now we can access functions via $this->blumeReporting->functionName(); OR we can use ajax to query data like rebuild.newskillsacademy.co.uk/ajax?c=blumeReporting&a=function-name

$metaTitle = "Sales Reporting";
include BASE_PATH . 'blume.header.base.php';
?>
<!-- -------------- Content -------------- -->
<section id="content" class="table-layout animated fadeIn">

    <style>
        .module {
            background:#F3F7FA;
            color:#000;
            text-align:center;
            font-weight:bold;
            padding:10px;
            border: 1px solid #efefef;
        }
        .module .title {
            font-size:18px;
        }
        .module .figure {
            font-size: 24px;
            margin-top: 10px;
        }
        .module .options {
            font-size:10px;
            margin-top:13px;
        }
        .module .options a {
            padding:0px 3px;
            color:#333;
        }
    </style>


    <!-- -------------- /Column Left -------------- -->

    <!-- -------------- Column Center -------------- -->
    <div class="chute chute-center">


        <!-- -------------- Data Filter -------------- -->
        <div class="panel" id="spy2">
            <div class="panel-heading">
                <span class="panel-title">Sales Reporting</span>

            </div>
            <div class="panel-body pn">

                <div class="row">

                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-3">
                                <div class="module">
                                    <div class="title">
                                        Sales (Value)
                                    </div>

                                    <div class="figure">
                                        £10,091
                                    </div>

                                    <p class="options">
                                        <a href="javascript:;">
                                            Day
                                        </a>
                                        <a href="javascript:;">
                                            Week
                                        </a>
                                        <a href="javascript:;">
                                            Month
                                        </a>
                                        <a href="javascript:;">
                                            Year
                                        </a>
                                        <a href="javascript:;">
                                            Custom
                                        </a>
                                        <a href="javascript:;">
                                            CSV
                                        </a>
                                    </p>

                                </div>
                            </div>

                            <div class="col-xs-3">
                                <div class="module">
                                    <div class="title">
                                        Sales
                                    </div>

                                    <div class="figure">
                                        309
                                    </div>

                                    <p class="options">
                                        <a href="javascript:;">
                                            Day
                                        </a>
                                        <a href="javascript:;">
                                            Week
                                        </a>
                                        <a href="javascript:;">
                                            Month
                                        </a>
                                        <a href="javascript:;">
                                            Year
                                        </a>
                                        <a href="javascript:;">
                                            Custom
                                        </a>
                                        <a href="javascript:;">
                                            CSV
                                        </a>
                                    </p>

                                </div>
                            </div>

                            <div class="col-xs-3">
                                <div class="module">
                                    <div class="title">
                                        Avg. Sales Value
                                    </div>

                                    <div class="figure">
                                        £29.01
                                    </div>

                                    <p class="options">
                                        <a href="javascript:;">
                                            Day
                                        </a>
                                        <a href="javascript:;">
                                            Week
                                        </a>
                                        <a href="javascript:;">
                                            Month
                                        </a>
                                        <a href="javascript:;">
                                            Year
                                        </a>
                                        <a href="javascript:;">
                                            Custom
                                        </a>
                                        <a href="javascript:;">
                                            CSV
                                        </a>
                                    </p>

                                </div>
                            </div>

                            <div class="col-xs-3">
                                <div class="module">
                                    <div class="title">
                                        Revenue Per Trans.
                                    </div>

                                    <div class="figure">
                                        £25.62
                                    </div>

                                    <p class="options">
                                        <a href="javascript:;">
                                            Day
                                        </a>
                                        <a href="javascript:;">
                                            Week
                                        </a>
                                        <a href="javascript:;">
                                            Month
                                        </a>
                                        <a href="javascript:;">
                                            Year
                                        </a>
                                        <a href="javascript:;">
                                            Custom
                                        </a>
                                        <a href="javascript:;">
                                            CSV
                                        </a>
                                    </p>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <br />

                <div class="row">
                    <div class="col-xs-6">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="module">
                                    <div class="title">
                                        Revenue Per Trans.
                                    </div>

                                    <div class="figure">
                                        £25.62
                                    </div>

                                    <p class="options">
                                        <a href="javascript:;">
                                            Day
                                        </a>
                                        <a href="javascript:;">
                                            Week
                                        </a>
                                        <a href="javascript:;">
                                            Month
                                        </a>
                                        <a href="javascript:;">
                                            Year
                                        </a>
                                        <a href="javascript:;">
                                            Custom
                                        </a>
                                        <a href="javascript:;">
                                            CSV
                                        </a>
                                    </p>

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

<!-- -------------- /Content -------------- -->
<?php include BASE_PATH . 'blume.footer.base.php'; ?>
