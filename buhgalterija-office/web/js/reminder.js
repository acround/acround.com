function removeReminder(id) {
  let user = getUser();
  let token = user ? user?.token : '';
  if (confirm('Are you sure you want to delete this calendar row?')) {
    $.ajax({
      url: '/company/delete-calendar-reminder',
      type: 'POST',
      data: {
        token: token,
        id: id
      },
      success: function (response) {
        if (response.status === 'success') {
          $('#tax-calendar-table').find('tr[data-item-id="' + id + '"]').remove();
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError('Delete error', response.message);
        }
      },
      error: function (e) {
        showError('Delete error', e);
      },
      type: 'json'
    })
  }
}

$(document).on('change', '#tax-calendar-month', function (e) {
  let month = $(this).val();
  let user = getUser();
  let data = {
    token: user.token,
  };
  $.ajax({
    url: '/reminder/tax-calendar-table/' + month,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#tax-calendar-table').replaceWith(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

$(document).on('click', '#tax-calendar-month-load', function (e) {
  let month = $('#tax-calendar-month_to_load').val();
  let user = getUser();
  let data = {
    token: user.token,
    month: month
  };
  $.ajax({
    url: '/reminder/tax-calendar-month-load/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#tax-calendar-load-message').show();
        setTimeout(() => {
          $('#tax-calendar-load-message').hide();
        }, 2000);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

let regReminderModal = null;
$(document).on('click', 'button.reminder-create', function (e) {
  let user = getUser();
  let reminderType;
  let titleName;
  let url = '/reminder/reminder-create/';
  let buttonId = $(this).attr('id');
  switch (buttonId) {
    case 'reg-reminder-create':
      reminderType = 'regular';
      titleName = 'createRegReminder';
      break;
    case 'yearly-reminder-create':
      reminderType = 'yearly';
      titleName = 'createYearlyReminder';
      break;
    case 'one-time-reminder-create':
      reminderType = 'one-time';
      titleName = 'createOneTimeReminder';
      break;
  }
  let data = {
    token: user.token,
    reminderType: reminderType
  };
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let title = dictionaryLookup(titleName, user.lang);
        regReminderModal = new Modal('modal-create-reg-reminder', title, 'reminder');
        $(regReminderModal.modal).find('input.reminder-type').val(reminderType);
        regReminderModal.setContent(response.data);
        regReminderModal.open();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

$(document).on('click', '.edit-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let type = $(this).data('reminder-type');
  let data = {
    token: user.token,
    id: id,
    reminderType: type,
  };
  $.ajax({
    url: '/reminder/reminder-update/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let title = dictionaryLookup('createRegReminder', user.lang);
        regReminderModal = new Modal('modal-create-reg-reminder', title, 'reminder');
        regReminderModal.setContent(response.data);
        regReminderModal.open();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

$(document).on('click', '#save-reminder', function (e) {
  let user = getUser();
  let button = this;
  let modalBody = $(this).closest('.modal-window').find('.modal-body');
  const reminderType = $(modalBody).find('input[name="reminder-type"]').val();
  let data = {
    token: user.token,
    id: $(modalBody).find('input[name="reminderId"]').val(),
    lang: $(modalBody).find('input[name="reminder-lang"]').val(),
    reminderType: reminderType,
    deadLine: $(modalBody).find('input[name="deadlineDay"]').val(),
    topic: $(modalBody).find('input[name="topic"]').val(),
    text: $(modalBody).find('input[name="text"]').val(),
  };
  button.disabled = true;
  $.ajax({
    url: '/reminder/reminder-save/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        if (response.action === 'created') {
          $('#' + reminderType + '-reminder-table-body').append(response.data);
        } else if (response.action === 'updated') {
          let row = $('#' + reminderType + '-reminder-row-' + response.reminder.id);
          $(row).replaceWith(response.data);
        }
        regReminderModal.close();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
      button.disabled = false;
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
      button.disabled = false;
    }
  });
  return false;
});

$(document).on('click', '#reminders-button-list button', function (e) {
  let user = getUser();
  let divId = $(this).data('controls');
  let div = $('#' + divId);
  let url = $(this).data('link');
  $('#reminders-button-list > button').attr('data-state', 'inactive');
  $(this).attr('data-state', 'active');
  $('#reminders-div-list > div').attr('data-state', 'inactive').hide();
  div.attr('data-state', 'active').show();
  let data = {
    token: user.token,
  };
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        div.html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function (e) {
      showError(dictionaryLookup('error', user.lang), e.message);
    }
  });
  return false;
});

$(document).on('click', '.cancel-reminder-btn', function (e) {
  if (confirm('Are you sure you want to delete this reminder?')) {
    let user = getUser();
    let reminderId = $(this).data('item-id');
    let type = $(this).data('reminder-type');
    let data = {
      token: user.token,
      id: reminderId,
      type: type,
    }
    let row = $(this).closest('tr');
    $.ajax({
      url: '/reminder/cancel-reminder',
      type: 'POST',
      data: data,
      success: function (response) {
        if (response.status === 'success') {
          $(row).remove();
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError(dictionaryLookup('error', user.lang), response.message);
        }
      },
      error: function (e) {
        showError(dictionaryLookup('error', user.lang), e.message);
      }
    });
  }
});

$(document).on('click', 'button.company-tax-reminder-btn', function (e) {
  let id = $(this).data('item-id');
  const title = $(this).closest('tr').find('.calendar-text').text();
  const type = $(this).data('type');
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-calendar-reminders/');
  loadCompanyListModal(id, '/company/list-to-calendar', makeCompanyListTr, type);
  companyListModal.open(this);
});

$(document).on('click', 'button.company-reg-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let title = dictionaryLookup('regularReminders', user.lang);
  const type = $(this).data('type');
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-reminders-list/');
  companyListModal.setReminderType('regular');
  loadCompanyListModal(id, '/company/list-to-regular', makeCompanyReminderList, type);
  companyListModal.open(this);
});

$(document).on('click', 'button.company-yearly-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let title = dictionaryLookup('yearlyReminders', user.lang);
  const type = $(this).data('type');
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-reminders-list/');
  companyListModal.setReminderType('yearly');
  loadCompanyListModal(id, '/company/list-to-regular', makeCompanyReminderList, type);
  companyListModal.open(this);
});

$(document).on('click', 'button.company-onetime-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let title = dictionaryLookup('oneTimeReminders', user.lang);
  const type = $(this).data('type');
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-reminders-list/');
  companyListModal.setReminderType('one-time');
  loadCompanyListModal(id, '/company/list-to-regular', makeCompanyReminderList, type);
  companyListModal.open(this);
});

$(document).on('click', 'button.edit-calendar-btn', function (e) {
  let id = $(this).data('item-id');
  const user = getUser();
  let title = dictionaryLookup('editReminder', user?.lang || 'ru');
  editReminderModal = new Modal('modal-edit-reminder', title, 'edit-reminder');
  let text = $('#tax-calendar-table').find('tr[data-item-id="' + id + '"] td.calendar-text').text();
  $(editReminderModal.modal).find('.modal-body').empty().append('<textarea class="edit-reminder-text" style="height: 100%;width: 100%;">' + text + '</textarea>');
  editReminderModal.open(id);
});

$(document).on('click', 'button.delete-calendar-btn', function (e) {
  removeReminder($(this).data('item-id'));
});

$(document).on('click', '#do-edit-reminder', function (e) {
  const reminder_id = editReminderModal?.currentRow;
  if (reminder_id) {
    let user = getUser();
    let token = user ? user?.token : '';
    let text = $(editReminderModal.modal).find('.edit-reminder-text').val();
    $.ajax({
      url: '/company/update-reminder-details',
      type: 'POST',
      data: {
        token: token,
        id: reminder_id,
        text: text
      },
      success: function (response) {
        if (response.status === 'success') {
          $('#tax-calendar-table').find('tr[data-item-id="' + reminder_id + '"] td.calendar-text').text(text);
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError('Update error', response.message);
        }
      },
      error: function (e) {
        showError('Update error', e);
      },
      type: 'json'
    });
  }
  editReminderModal.close();
});

$(document).on('click', '#do-action-btn', function (e) {
  let reminder_id = $(this).data('item-id');
  if (!reminder_id) {
    const row = $(this).closest('tr');
    if (row) {
      reminder_id = $(row).data('item-id');
    }
  }
  if (!reminder_id) {
    const row = companyListModal.currentRow;
    if (row) {
      reminder_id = $(row).data('item-id');
    }
  }
  if (!reminder_id) {
    return;
  }
  const checkedCompanies = [];
  $(companyListModal.modal).find('tbody input[type="checkbox"]:checked').each(function () {
    checkedCompanies.push($(this).val());
  });
  const unchekedCompanies = [];
  $(companyListModal.modal).find('tbody input[type="checkbox"]').not(':checked').each(function () {
    unchekedCompanies.push($(this).val());
  });
  let user = getUser();
  let token = user ? user?.token : '';
  let doActionUrl = $(companyListModal.modal).find('input.do-action-url').val();
  let reminderType = $(companyListModal.modal).find('input.reminder-table-type').val();
  if (doActionUrl) {
    $.ajax({
      url: doActionUrl,
      type: 'POST',
      data: {
        token: token,
        reminder_id: reminder_id,
        checked_companies: checkedCompanies,
        uncheked_companies: unchekedCompanies,
        reminder_type: reminderType
      },
      success: function (response) {
        if (response.status === 'success') {
          // Обновление прошло успешно
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError('Update error', response.message);
        }
      },
      error: function (e) {
        showError('Update error', e);
      },
      type: 'json'
    });
  }
  companyListModal.close();
});

$(document).on('click', '.accordeon-item', function (e) {
  const table = $(this).next('.accordeon-table');
  table.fadeToggle();
});

$(document).on('click', 'button.stop-reminder-btn', function (e) {
  const button = $(this);
  const user = getUser();
  const type = $(this).data('reminder-type');
  const reminderId = $(this).attr('data-rm-id');
  const scheduleId = $(this).attr('data-sc-id');
  const companyId = $(this).attr('data-co-id');
  const token = user ? user?.token : '';
  const data = {
    token: token,
    reminder_id: reminderId,
    schedule_id: scheduleId,
    company_id: companyId,
    type: type,
  };
  $.ajax({
    url: '/reminder/stop-reminder',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $(button).closest('tr').find('div.stopped-reminder').removeClass('hidden');
        $(button).addClass('hidden');
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Update error', response.message);
      }
    },
    error: function (e) {
      showError('Update error', e);
    },
    type: 'json'
  });
});

$(document).on('click', 'input.reminder-activity', function (e) {
  const input = $(this);
  const user = getUser();
  const type = $(this).data('reminder-type');
  const reminderId = $(this).data('rm-id');
  const scheduleId = $(this).data('sc-id');
  const companyId = $(this).data('co-id');
  const isActive = $(this).is(':checked') ? 1 : 0;
  const token = user ? user?.token : '';
  const data = {
    token: token,
    type: type,
    reminder_id: reminderId,
    schedule_id: scheduleId,
    company_id: companyId,
    is_active: isActive
  };
  $.ajax({
    url: '/reminder/toggle-reminder-activity',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        const id = response.id ?? '';
        $(input).attr('data-sc-id', id);
        const td = $(input).closest('tr').find('td').last();
        $(td).children().addClass('hidden');
        if (id) {
          $(td).find('button.stop-reminder-btn').attr('data-sc-id', id).removeClass('hidden');
        } else {
          $(td).find('div.not-assigned-reminder').removeClass('hidden');
        }
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Update error', response.message);
      }
    },
    error: function (e) {
      showError('Update error', e);
    },
    type: 'json'
  });
});

$(document).on('click', 'input.tax-activity', function (e) {
  const input = $(this);
  const user = getUser();
  const reminderId = $(this).attr('data-rm-id');
  const scheduleId = $(this).attr('data-sc-id');
  const companyId = $(this).attr('data-co-id');
  const isActive = $(this).is(':checked') ? 1 : 0;
  const token = user ? user?.token : '';
  const data = {
    token: token,
    reminder_id: reminderId,
    schedule_id: scheduleId,
    company_id: companyId,
    is_active: isActive
  };
  $.ajax({
    url: '/reminder/toggle-tax-activity',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        const id = response.id ?? '';
        $(input).attr('data-sc-id', id);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Update error', response.message);
      }
    },
    error: function (e) {
      showError('Update error', e);
    },
    type: 'json'
  });
});
