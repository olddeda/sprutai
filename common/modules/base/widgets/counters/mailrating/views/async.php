<?php
/**
 * @var int   $counterId
 */
?>

<!-- Rating@Mail.ru counter -->
<script type="text/javascript">
    var _tmr = _tmr || [];
    _tmr.push({id: "<?= $counterId ?>", type: "pageView", start: (new Date()).getTime()});
    
    (function (d, w) {
		var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true;
		ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//top-fwz1.mail.ru/js/code.js";
		
		var f = function () {
			var s = d.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(ts, s);
		};
		
		if (w.opera == "[object Opera]") {
			d.addEventListener("DOMContentLoaded", f, false);
		} else {
			f();
		}
	})(document, window);
</script>
<!-- Rating@Mail.ru counter -->
