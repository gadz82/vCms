// IIFE - Immediately Invoked Function Expression
(function (yourcode) {
    // The global jQuery object is passed as a parameter
    yourcode(window.jQuery, window, document);

}(function ($, window, document) {
    // The $ is now locally scoped
    // Listen for the jQuery ready event on the document
    $(function () {
        // The DOM is ready!

        var $form = $('form[name="form-edit"]');
        var jrr = (json_render_required != '') ? JSON.parse(json_render_required) : [];

        $('select[name="id_tipologia_stato').on('change', function () {

            required = get_required($(this).val());

            $form.find('label:not(:has(input))').each(function () {
                var $this = $(this);

                $this.html($this.html().replace(' *', ''));
                if (required.indexOf($this.attr('for')) !== -1) {
                    $this.append(' *');
                }
            });

            $form.find('input, select, textarea').each(function () {
                var $this = $(this);

                $this.removeAttr('required');
                if (required.indexOf($this.attr('name')) !== -1) {
                    $this.attr('required', true);
                }
            });

        });

        $('.selectpicker').on('change', function () {
            if ($(this).val() != 0) {
                $(this).closest('.form-group').removeClass('has-error');
            } else {
                $(this).closest('.form-group').addClass('has-error');
            }
        });

        var form_confirm = false;
        $form.on('submit', function (ev) {

            $.validator.setDefaults({
                ignore: []
            });

            $form.validate({
                highlight: function (element) {
                    $(element).closest('.form-group').addClass('has-error');
                },
                unhighlight: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                success: function (element) {
                    $(element).closest('.form-group').removeClass('has-error');
                },
                invalidHandler: function (event, validator) {
                },
                errorPlacement: function (error, element) {
                }
            });

            if (!$form.valid()) {
                ev.preventDefault();

                var $first_error = $form.find('.form-group.has-error:first');

                if ($first_error.length > 0) {
                    $('html,body').animate({
                        scrollTop: $first_error.offset().top - 60
                    }, 'fast');
                }

                return false;
            }

        });

        /**
         * Preselezione Select2
         */
        $('select[name="filtri_group_post_types[]"]').selectpicker("val", selected);


        $('select[name="id_tipologia_stato').trigger('change');

        function get_required(id_tipologia_stato) {

            var required = [];
            var id_item = 'default';

            for (var item in jrr) {

                var arr_item_elem = [];
                if (item.indexOf('|') !== -1) arr_item_elem = item.split('|');

                if (id_tipologia_stato == item || arr_item_elem.indexOf(id_tipologia_stato) !== -1) {
                    id_item = item;
                    break;
                }

            }

            required = [];
            for (var val in jrr[id_item]) {
                required.push(jrr[id_item][val]);
            }

            return required;

        }

    });

}));