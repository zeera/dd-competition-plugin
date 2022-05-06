<?php
/** @var WpDigitalDriveCompetitions\Core\Controller $this */ ?>
<div class="container-fluid mt-2">
    <div class="toperror dd-competition">
        <?php if (count($this->alerts['errors']) > 0) { ?>
            <div class="alert alert-danger">
                <ul class="p-0 mb-0">
                    <?php
                        foreach ($this->alerts['errors'] as $key => $error) {
                            $keyname = str_replace("_", " ", $key);
                            $keyname = ucfirst($keyname);
                            echo "<li><b>$keyname:</b> $error</li>";
                        }
                    ?>
                </ul>
            </div>
        <?php } ?>
    </div>

    <div class="topsuccess dd-competition">
        <?php if (count($this->alerts['successes']) > 0) { ?>
            <div class="alert alert-success">
                <ul class="p-0 mb-0">
                    <?php
                        foreach ($this->alerts['successes'] as $key => $success) {
                            $keyname = str_replace("_", " ", $key);
                            $keyname = ucfirst($keyname);
                            echo "<li><b>$keyname:</b> $success</li>";
                        }
                    ?>
                </ul>
            </div>
        <?php } ?>
    </div>

    <link href="<?php $this->loadCSS('bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $this->loadCSS('boostrap-select.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $this->loadCSS('dataTables.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php $this->loadCSS('custom.css') ?>" rel="stylesheet" type="text/css" />
    <h1><?= $this->title; ?></h1>
