<?php use yii\helpers\Json; ?>
<script>
<?php foreach ($dataLayerPushItems as $item) : ?>
  dataLayer.push(<?= Json::encode($item) ?>);
<?php endforeach; ?>
</script>
