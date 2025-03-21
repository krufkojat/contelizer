import { $ } from '../app';

$(document).ready(function() {
    let currentPage = 1;
    let searchParams = {};

    function fetchUsers() {
        const params = { ...searchParams, page: currentPage };
        const queryString = Object.keys(params)
            .filter(key => params[key] !== '')
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');

        $.ajax({
            url: `/api/users${queryString ? '?' + queryString : ''}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderUsers(response);

                    updatePagination(response.pagination);
                } else {
                    $.toast({
                        text: 'Błąd podczas pobierania użytkowników',
                        icon: 'error'
                    })
                }
            },
            error: function() {
                $.toast({
                    text: 'Nie udało się połączyć z serwerem',
                    icon: 'error'
                })

                $('#users-table-body').html(`
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-red-500">
                            Wystąpił błąd podczas ładowania danych. Spróbuj ponownie później.
                        </td>
                    </tr>
                `);
            }
        });
    }

    function renderUsers(data) {
        const users = data.data;
        const { total } = data.pagination;

        if (!users || users.length === 0) {
            $('#users-table-body').html(`
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-slate-500">
                        Brak użytkowników spełniających kryteria wyszukiwania.
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        users.forEach(user => {
            html += `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${user.id}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">${user.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${user.email}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">${user.gender === 'male' ? 'Mężczyzna' : 'Kobieta'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${user.status === 'active' ? 'Aktywny' : 'Nieaktywny'}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="edit-user-button text-primary hover:text-blue-700 cursor-pointer" data-id="${user.id}">Edytuj</button>
                    </td>
                </tr>
            `;
        });

        $('#users-table-body').html(html);

        const from = (currentPage - 1) * 10 + 1;
        const to = from + users.length - 1;

        $('#showing-from').text(from);
        $('#showing-to').text(to);
        $('#total-users').text(total);
    }

    function updatePagination(pagination) {
        $('#prev-page').prop('disabled', currentPage === 1);
        $('#next-page').prop('disabled', currentPage >= parseInt(pagination.pages, 10));
    }

    $('#search-button').on('click', function() {
        searchParams = {
            name: $('#search-name').val(),
            email: $('#search-email').val(),
            gender: $('#search-gender').val(),
            status: $('#search-status').val()
        };
        currentPage = 1;

        fetchUsers();
    });

    $('#reset-button').on('click', function() {
        $('#search-name').val('');
        $('#search-email').val('');
        $('#search-gender').val('');
        $('#search-status').val('');
        searchParams = {};
        currentPage = 1;

        fetchUsers();
    });

    $('#prev-page').on('click', function() {
        if (currentPage > 1) {
            currentPage--;

            fetchUsers();
        }
    });

    $('#next-page').on('click', function() {
        currentPage++;

        fetchUsers();
    });

    $(document).on('click', '.edit-user-button', function() {
        const userId = $(this).data('id');
        window.location.href = `/user/edit/${userId}`;
    });

    fetchUsers();
});
