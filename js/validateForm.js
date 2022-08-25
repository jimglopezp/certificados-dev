jQuery(document).ready(function () {
  var ajaxform = true;
  var metodo = 'post';
  // binds form submission and fields to the validation engine
  jQuery("#myform").validationEngine({showArrowOnRadioAndCheckbox: true});

  jQuery("#myformajax").validationEngine({
    ajaxFormValidation: true,
    ajaxFormValidationMethod: 'post',
    onAjaxFormComplete: ajaxValidationCallback,
    onBeforeAjaxFormValidation: preload,
    showArrowOnRadioAndCheckbox: true
  });
});

function preload() {
  jQuery(".preload, .load").show();
}

function bloquearFestivos(date) {
  var d = date.getDate(), m = date.getMonth(), y = date.getFullYear();
  //console.log('Checking (raw): ' + m + '-' + d + '-' + y);
  for (i = 0; i < diaInhabil.length; i++) {
    //if($.inArray((m+1) + '-' + d + '-' + y,disabledDays) != -1 || new Date() > date) {
    if ($.inArray(y + '-' + (m + 1) + '-' + d, diaInhabil) != -1 || new Date() > date) {
      //console.log('bad:  ' + (m+1) + '-' + d + '-' + y + ' / ' + diaInhabil[i]);
      return [false];
    }
  }
  //console.log('good:  ' + (m+1) + '-' + d + '-' + y);
  return [true];
}

