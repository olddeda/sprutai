initPhoneInputCustom = function(id, options) {
	id.unbind('propertychange change click keyup input paste').bind('propertychange change click keyup input paste', function(event) {
		$(this).val(intlTelInputUtils.formatNumber($(this).intlTelInput("getNumber"), null, intlTelInputUtils.numberFormat.INTERNATIONAL));
	});
}