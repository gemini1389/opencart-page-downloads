<?php echo $header; ?>

<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
</div>

<?php echo $column_left; ?>
<?php echo $column_right; ?>

<style>
    .category {
        margin-bottom: 50px;
    }
    .category h1 {
        color: #636E75;
        font-size: 32px;
        font-weight: normal;
        margin-bottom: 5px;
        margin-top: 0;
    }
    .category h3 {
        font-weight: normal;
        margin: 0;
    }
    .category-description {
        border-bottom: 1px solid #000000;
        font-style: italic;
        margin-top: 0;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }
    .files-list > div {
        overflow: auto;
        margin-bottom: 20px;
        /*border: 1px solid #000000;*/
    }
    .files-list > div + div {
        padding-top: 20px;
    }
    .files-list .image {
        float: left;
        margin-right: 50px;
    }
    .files-list .image img {
        margin-bottom: -3px;
    }
    .files-list .name {
        margin-bottom: 10px;
    }
    .files-list .name a {
        color: #000000;
        font-weight: bold;
        text-decoration: underline;
        font-size: 16px;
    }
    .files-list .description {
        line-height: 15px;
        margin-bottom: 5px;
        color: #4D4D4D;
    }
    .files-list .download {
        float: right;
        margin-left: 50px;
        text-align: center;
        margin-top: 15px;
    }
    .files-list .download a {
        text-decoration: none;
    }
    .files-list .download img {
        margin-bottom: 10px;
    }
    .files-list .download .filesize {
        font-weight: bold;
    }
    .files-list .image .more {
        color: #FFFFFF;
        margin-bottom: 0;
        text-align: right;
        padding: 3px 5px 3px 3px;
        clear: both;
        background: #ed1c24;
        line-height: 16px;
    }
    .files-list .image .more img {
        margin: 0 0 0 5px;
        vertical-align: middle;
    }
    .files-list .image .more a {
        color: #FFFFFF;
        text-decoration: none;
    }
</style>

<div id="content">
    <?php if ($downloads_cats) { ?>
        <?php foreach ($downloads_cats as $category) { ?>
            <div class="category">
                <?php if ($category) { ?>
                    <h1><?php echo $category['cat_name']; ?></h1>
                    <div class="category-description"><h3><?php echo $category['cat_description']; ?></h3></div>
                    <div class="files-list">
                        <?php foreach ($category['cat_files'] as $file) { ?>
                            <div>
                                <div class="image">
                                    <img src="<?php echo $file['image']; ?>" title="<?php echo $file['name']; ?>" alt="<?php echo $file['name']; ?>" />
                                    <div class="more">
                                        <a href="<?php echo $file['href']; ?>" title="<?php echo $button_download; ?>">
                                            <?php echo $button_download; ?>
                                            <img src="/image/arrow_white.png">
                                        </a>
                                    </div>
                                </div>
                                <div class="download">
                                    <a href="<?php echo $file['href']; ?>" title="<?php echo $button_download; ?>">
                                        <img src="/image/icon_download.png" alt="<?php echo $button_download; ?>" />
                                        <div class="filesize"><?php echo $file['filesize']; ?></div>
                                    </a>
                                </div>
                                <div class="name">
                                    <a href="<?php echo $file['href']; ?>"><?php echo $file['name']; ?></a>
                                </div>
                                <div class="description">
                                    <?php echo $file['description']; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<?php echo $footer; ?>