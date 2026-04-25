function collectDataForSearch(withPage = false) {
    const user = getUser();
    const token = user ? user?.token : '';
    let filterBox = $('div.all-filter-box');
    const entity = $(filterBox).data('entity');
    let data = {
        entity: entity,
        token: token,
    };
    data.name = $('#search').val();
    $(filterBox).find('select').each(function () {
        let key = $(this).data('field');
        let value = $(this).val();
        if (value != '') {
            data[key] = value;
        }
    });

    let sorting = [];
    $(filterBox).find('input.sorting[value!=none]').each(function () {
        let field = $(this).data('field');
        let value = $(this).val();
        let sortObj = {
            field: field,
            value: value
        }
        sorting.push(sortObj);
    });
    data.sorting = sorting;

    if (withPage) {
        if (withPage === true) {
            withPage = $('div.pagination button.page-btn.active').data('page') || 1;
        }
        data.page = withPage;
    }

    return data;
}

function loadEntities(withPage = false) {
    let data = collectDataForSearch(withPage);
    let url = '/' + data.entity + '/filter';
    delete data.entity;
    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function (response) {
            if (response.status === 'success') {
                $('#entity-list').html(response.data);
                $('#entity-count').text(response.count);
            } else if (response.status === 'logout') {
                clearUser();
                loadContent();
            } else {
                showError('Error', response.message ?? '');
            }
        },
        error: function (e) {
            showError('Error', e);
        },
        type: 'json'
    });
}

function setSearchHistory() {
    let data = collectDataForSearch(true);
    let entity = data.entity;
    let url;
    let pageHistory = localStorage.getItem('pageHistory');
    pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
    if (entity) {
        if (pageHistory.length > 1) {
            url = '/' + entity + '/filter';
        } else {
            url = pageHistory[pageHistory.length - 1].url;
        }
        delete data.entity;
    } else {
        if (pageHistory.length > 0) {
            url = pageHistory[pageHistory.length - 1].url;
            data = pageHistory[pageHistory.length - 1].data;
        }
    }
    let itemHistory = {
        url: url,
        data: data
    }
    clearPageHistory(1);
    addPageHistory(itemHistory);
}

//=========================================================================
//  Handlers
//=========================================================================

$(document).ready(function () {

    $(document).on('click', 'button.reset-filters-button', function (e) {
        $('#search').val('');
        $(this).closest('div.filter-box').find('select').val('');
    });

    $(document).on('click', 'button.find-button', function (e) {
        loadEntities(false);
    })

    $(document).on('click', 'div.pagination button.page-btn', function (e) {
        const page = $(this).data('page');
        loadEntities(page);
    });

    $(document).on('change', 'div.all-filter-box select', function (e) {
        $('div.all-filter-box button.find-button').click();
    });

    $(document).on('click', 'button.sorting', function (e) {
        let sort = $(this).data('sort');
        let field = $(this).data('field');
        // let entity = $(this).data('entity');
        let input = $('div.all-filter-box input.sorting[data-field=' + field + ']');
        switch (sort) {
            case 'asc':
                $(this).data('sort', 'desc');
                $(input).val('desc');
                $(this).find('span').html('↑');
                break;
            case 'desc':
                $(this).data('sort', 'none');
                $(input).val('none');
                $(this).find('span').html('●');
                break;
            default:
                $(this).data('sort', 'asc');
                $(input).val('asc');
                $(this).find('span').html('↓');
        }
        const page = $('div.pagination button.page-btn.active').data('page');
        loadEntities(page);
    });

});
