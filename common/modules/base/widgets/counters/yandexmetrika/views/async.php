<?php
/**
 * @var int   $counterId
 * @var array $counterParams
 * @var array $userParams
 */
?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter<?= $counterId ?> = new Ya.Metrika2(<?=\yii\helpers\Json::encode($counterParams)?>);
                <?php if (is_array($userParams) && count($userParams)) { ?>
				w.yaCounter<?= $counterId ?>.userParams(<?=\yii\helpers\Json::encode($userParams)?>)
				<?php } ?>
            } catch (e) {
            }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<!-- /Yandex.Metrika counter -->
