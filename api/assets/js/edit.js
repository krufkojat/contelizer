import { $ } from '../app';

$(document).ready(function() {
    const path = window.location.pathname;
    const segments = path.split('/');
    const userId = segments.pop();

    function showValidationErrors(errors) {
        $('.validation-error').addClass('hidden');

        for (const field in errors) {
            $(`#edit-${field}-error`).text(errors[field]).removeClass('hidden');
        }
    }

    function clearValidationErrors() {
        $('.text-red-600').addClass('hidden');
    }

    $('#edit-user-form').on('submit', function(e) {
        e.preventDefault();

        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            gender: $('#gender').val(),
            status: $('#status').val(),
            _csrf_token: $('#_csrf_token').val()
        };

        const $saveButton = $('#save-button');
        $saveButton.prop('disabled', true).text('Zapisywanie...');

        $.ajax({
            url: '/api/users/' + userId,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(result) {
                $saveButton.prop('disabled', false).text('Zapisz zmiany');

                if (result.success) {
                    $.toast({
                        text: 'Dane użytkownika zostały zaktualizowane',
                        icon: 'success'
                    })
                } else {
                    $.toast({
                        text: 'Nie udało się zaktualizować danych użytkownika',
                        icon: 'error'
                    })
                }
            },
            error: function(response) {
                console.log(response.responseJSON.errors);


                showValidationErrors(response.responseJSON.errors);

                $saveButton.prop('disabled', false).text('Zapisz zmiany');

                $.toast({
                    text: 'Nie udało się zaktualizować danych użytkownika',
                    icon: 'error'
                })
            }
        });
    });
;

    $('#cancel-button').on('click', function() {
        window.location.href = '/';
    });
});
