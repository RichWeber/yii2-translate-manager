/** 
 * Created on : 2014.08.24., 5:26:26
 * Author     : Lajos Molnar <lajax.m@gmail.com>
 * since 1.0
 */

$(document).ready(function () {
    translate.init();
});

var translate = (function () {

    /**
     * @type string
     */
    var _originalMessage;

    /**
     * @param {object} $this
     */
    function _translateLanguage($this) {
        var data = {
            id: $this.data('id'),
            language_id: $('#language_id').val(),
            translation: $.trim($this.closest('tr').find('.translation').val())
        };

        helpers.post($('#language_id').data('url'), data);
    }

    /**
    * @param {object} $this
    */
    function _translateGoogle($this) {
        var data = {
            id: $this.data('id'),
            language_id: $('#google_lang_id').val(),
            source: $.trim($this.closest('tr').find('.source').val())
        };

        helpers.post($('#google_lang_id').data('url'), data);
    }

    /**
     * @param {object} $this
     */
    function _copySourceToTranslation($this) {
        if ($.trim($this.closest('tr').find('.translation').val()).length === 0) {
            $this.closest('tr').find('.translation').val($.trim($this.val()));
        }

        _translateGoogle($this.closest('tr').find('button'));
    }

    return {
        init: function () {
            $('.google-btn').on('click', 'button', function () {
                alert('Google');
                //_translateGoogle($(this));
            });
            $('#translates').on('click', '.source', function () {
                _copySourceToTranslation($(this));
            });
            $('#translates').on('click', 'button', function () {
                _translateLanguage($(this));
            });
            $('#translates').on('focus', '.translation', function () {
                _originalMessage = $.trim($(this).val());
            });
            $('#translates').on('blur', '.translation', function () {
                if ($.trim($(this).val()) !== _originalMessage) {
                    _translateLanguage($(this).closest('tr').find('button'));
                }
            });
        }
    };
})();