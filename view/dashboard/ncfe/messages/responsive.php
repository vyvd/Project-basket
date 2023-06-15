<?php
$pageTitle = "Messages";
include BASE_PATH . 'account.header.php';
?>
    <section class="page-title" style="padding-bottom:0;">
        <div class="container">
            <a href="javascript:;" data-toggle="modal" data-target="#newMessage" class="mainBtn">New Message</a>
            <h1><?= $pageTitle ?></h1>
        </div>
    </section>

    <form name="selectedMessages" id="selectedMessages">
        <section class="page-content messages">
            <div class="container">

                <div class="row">
                    <div class="col-12">
                        <div class="tab-content container">
                            <div id="all" class="tab-pane active show">
                                <div class="row">
                                    <div class="col-12" id="accordioninbox">
                                        <?php
                                        $items = ORM::for_table('messages')
                                            ->where("recipientID", CUR_ID_FRONT)
                                            ->order_by_desc('whenSent')
                                            ->limit(50)
                                            ->find_many();

                                        foreach($items as $item) {
                                            ?>
                                            <div class="col-12 regular-full message-outer" id="message<?= $item->id ?>">
                                                <div class="row align-items-center">
                                                    <div class="message-box white-rounded">
                                                        <div class="message-head" id="heading<?= $item->id ?>" aria-expanded="false" aria-controls="message<?= $item->id ?>">
                                                            <a class="btn btn-primary"><?= $this->ago($item->whenSent) ?></a>
                                                            <h3><?= $item->subject ?></h3>
                                                            <i class="fas fa-reply-all" onclick="reply('<?= $item->subject ?>');" style="position: absolute;right: 64px;color: #c2c2c2;font-size: 29px;"></i>
                                                        </div>
                                                        <div class="collapse show" aria-labelledby="heading<?= $item->id ?>" id="message<?= $item->id ?>" data-parent="#accordioninbox">
                                                            <div class="card card-body">
                                                                <?php echo $item->message; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $update = ORM::For_table("messages")->find_one($item->id);
                                            $update->seen = "1";
                                            $update->save();

                                        }

                                        if(count($items) == 0) {
                                            ?>
                                            <div class="col-12 text-center">
                                                <p style="margin-top:50px;margin-bottom:50px;">
                                                    You currently have no messages in your inbox. Any messages your tutor replies to will appear here.
                                                </p>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </form>

    <div class="modal fade" id="newMessage" tabindex="-1" role="dialog" aria-labelledby="basketTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <a class="btn-close" data-dismiss="modal">X</a>
                    <p class="popup-title text-center">New Message</p>
                </div>
                <div class="modal-body coupon">


                    <form name="newMessage">
                        <div class="form-group">
                            <label>I need help from...</label>
                            <select class="form-control" name="type" id="type">
                                <option value="cs">Customer Services</option>
                                <option value="tutor">My Tutor</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" style="padding:20px;background:#fff;" />
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" rows="5" class="form-control" style="padding:20px;"></textarea>
                        </div>

                        <div class="totals">
                            <button type="submit" class="btn btn-primary extra-radius">Send Message</button>
                        </div>
                    </form>

                    <?php
                    $this->renderFormAjax("message", "send-ncfe", "newMessage");
                    ?>

                </div>
            </div>
        </div>
    </div>

    <script>
        function reply(subject) {

            $("#subject").val(subject);
            $("#type").val("tutor");

            $("#newMessage").modal("toggle");

        }
    </script>

<?php include BASE_PATH . 'account.footer.php';?>