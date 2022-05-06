<?php
/** @var WpDigitalDriveCompetitions\Core\Controller $this */ ?>
</div>
<script src="<?php $this->loadJS('bootstrap.bundle.min.js')?>"></script>
<script src="<?php $this->loadJS('boostrap-select.js')?>"></script>
<?php
$this->generateJavaScript();
foreach ($this->extra_jsfiles as $jsfilepath) {
    ?><script type="text/javascript"
src="<?php $this->loadJS($jsfilepath)?>"></script>
<?php } ?>
<script>
<?php print($this->inpage_js); ?>
</script>
