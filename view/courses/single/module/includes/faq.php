<div class="row">
    <h3>Frequently Asked Questions</h3>
    <div id="accordion" class="col-12">
        <?php
        foreach(ORM::for_table("courseModuleFaqs")->order_by_asc("id")->find_many() as $faq) {
            ?>
            <div class="card">
                <div class="card-header" id="heading<?= $faq->id ?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#question<?= $faq->id ?>" aria-expanded="false" aria-controls="question<?= $faq->id ?>">
                            <?= $faq->question ?>
                        </button>
                    </h5>
                </div>
                <div id="question<?= $faq->id ?>" class="collapse" aria-labelledby="heading<?= $faq->id ?>" data-parent="#accordion">
                    <div class="card-body">
                        <?= $faq->answer ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>