function validateEmail() {
    jQuery("#email_address").change(function(){
        var value = jQuery("#email_address").val();
        jQuery.validator.addMethod(
            'email_zerobounce',
            function (value) {
                var isValid = true,
                    validator = this;

                jQuery.ajax({
                    url: 'zerobounce/index/index',
                    data: {email: value, isAjax: true},
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    cache: false,
                    success: function (errors) {
                    console.log(errors);
                    isValid = errors.length === 0;
                    validator.errorMessage = errors.length === 0 ? null : errors[0].message;
                },
            });

                return isValid;
            },
            function () {
                return this.errorMessage ? this.errorMessage : jQuery.mage.__('Email is invalid.');
            }
        ),
            jQuery("#form-validate").validate({
                rules: {
                    email_address:{
                        email_zerobounce : true
                    }
                },
            })


    })
}

document.observe("dom:loaded",validateEmail);