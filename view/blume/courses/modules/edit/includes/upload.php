<style>
    .module .form-group{
        /*padding: 20px;*/
    }
    .module {
        background: #f5f5f5;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
        border-radius: 15px;
    }
</style>
<div class="row mb10">
    <div class="col-xs-12">
        <button data-type="uploads" type="button" class="btn btn-info btn-small btn-addfile">+ Add File</button>
    </div>
    <div class="col-xs-12 moreuploads">
        <?php
        $files = $this->courseModule->getModuleUploads($module->id);
        if(@$files){
            $i=0;
            foreach ($files as $file){
                ?>
                <div class="row mt5">
                    <div class="col-xs-4">
                        <input class="form-control" type="text" readonly name="old_uploads[<?= $i;?>]['title']" value="<?= $file->title?>">
                    </div>
                    <div class="col-xs-4">
                        <a href="<?= $file->url;?>" target="_blank"><?= $file->fileName?></a>
                    </div>
                    <div class="col-xs-4">
                        <label class="label label-danger deleteItem" data-id="<?= $file->id?>" data-table="media" data-reload="true"><i class="fa fa-trash"></i></label>
                    </div>
                </div>
                <?php
                $i++;
            }
        }
        ?>
    </div>
    <input type="hidden" id="totaluploads" value="<?= count($files);?>">
</div>