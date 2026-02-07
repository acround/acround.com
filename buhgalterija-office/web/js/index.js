
let companyListModal;
let companyList = [];
let editReminderModal;

function setUser(user) {
  localStorage.setItem('user', JSON.stringify(user));
}

function getUser() {
  const user = localStorage.getItem('user');
  return (user && (user !== 'undefined')) ? JSON.parse(user) : null;
}

function clearUser() {
  localStorage.removeItem('user');
  localStorage.removeItem('pageHistory');
}

function showError(title, message) {
  $('#error-tab ol').append(
    `<li class="mb-2 animate-in slide-in-from-top-2 duration-300">
      <div class="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg ring-1 ring-black/5">
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-1">
              <p class="font-medium text-gray-900">${title}</p>
              <p class="mt-1 text-sm text-gray-700">${message}</p>
            </div>
            <button class="ml-4 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-white text-gray-400 hover:bg-gray-50 hover:text-gray-500" onclick="this.closest('li').remove();">
              <span class="sr-only">Close</span>
              &times;
            </button>
          </div>
        </div>
      </div>
    </li>`
  );
  let lastError = $('#error-tab ol li').last();
  setTimeout(() => {
    $(lastError).remove();
  }, 5000);
}

function menu(el) {
  if ($(el).hasClass('active')) {
    alert('active')
  } else {
    alert('not active')
  }
  return false;
}

function putPtm() {
  user = getUser();
  $('.ptm-name').text(user.firstname + ' ' + user.lastname);
  $('.ptm-email').text(user.email);
  $('.ptm-position').text(user.rule);
}

function loadContent() {
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: '/site/load',
    type: 'POST',
    data: {
      token: token
    },
    success: function (response) {
      $('#root').html(response.data);
      userMenuResize();
    },
    error: function (e) {
      showError('Load error', e);
    },
    type: 'json'
  })
}

function showTiff() {
  if ($('#tiffCanvas').length) {
    let docId = $('#tiffCanvas').data('doc-id');
    var xhr = new XMLHttpRequest();
    xhr.responseType = 'arraybuffer';
    xhr.open('GET', "/document/file/" + docId, true);
    xhr.onload = function (e) {
      var tiff = new Tiff({
        buffer: xhr.response
      });
      var canvas = tiff.toCanvas();
      document.getElementById('tiffCanvas').appendChild(canvas);
    };
    xhr.send();
  }

}

function loadPage(url, data = {}, saveHistory = false, success) {
  let user = getUser();
  let token = user ? user?.token : '';
  if (!data) {
    data = {};
  }
  data.token = token;
  let itemHistory = { url: url, data: data };
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (saveHistory === true) {
    if (pageHistory.length > 0) {
      let lastPage = pageHistory[pageHistory.length - 1];
      if (lastPage.url !== itemHistory.url || JSON.stringify(lastPage.data) !== JSON.stringify(itemHistory.data)) {
        pageHistory.push(itemHistory);
      }
    } else {
      pageHistory.push(itemHistory);
    }
  } else if (saveHistory === false) {
    pageHistory = [itemHistory];
  } else if (typeof saveHistory === 'number') {
    while (saveHistory > 0) {
      saveHistory--;
      pageHistory.pop();
    }
  }
  localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
  $.ajax({
    url: url,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        $('main').html(response.data);
        userMenuResize();
        showTiff();
        if (success && (typeof (success) === 'function')) {
          success();
        }
      }
    },
    error: function (e) {
      if (e.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Load error', e.message);
      }
    },
    dataType: 'json'
  })
}

function goBack() {
  let pageHistory = localStorage.getItem('pageHistory');
  pageHistory = pageHistory ? JSON.parse(pageHistory) : [];
  if (pageHistory.length > 1) {
    pageHistory.pop();
    let lastPage = pageHistory[pageHistory.length - 1];
    localStorage.setItem('pageHistory', JSON.stringify(pageHistory));
    let data = lastPage.data;
    data.token = getUser().token;
    $.ajax({
      url: lastPage.url,
      type: 'POST',
      data: data,
      success: function (response) {
        if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          $('main').html(response.data);
          userMenuResize();
          showTiff();
        }
      },
      error: function (e) {
        if (e.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError('Load error', e.message);
        }
      },
      dataType: 'json'
    })
  }
}

function putLangDependentWords() {
  let user = getUser();
  let lang = user ? user?.lang : 'rs';
  $('.edit-calendar-btn').attr('title', dictionaryLookup('edit', lang));
  $('.delete-calendar-btn').attr('title', dictionaryLookup('delete', lang));
  $('.btn-cancel').text(dictionaryLookup('cancel', lang));
  $('.btn-save').text(dictionaryLookup('save', lang));
}

function loadCompanyListModal(id, url, trFunction) {
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: url,
    type: 'POST',
    data: {
      token: token,
      id: id
    },
    success: function (response) {
      if (response.status === 'success') {
        $(companyListModal.modal).find('.modal-body ul').empty();
        let ul = $(companyListModal.modal).find('.modal-body tbody');
        for (let i = 0; i < response.data.list.length; i++) {
          let tr = trFunction(response.data.list[i]);
          ul.append(tr);
        }
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Load error', response.message);
      }
    },
    error: function (e) {
      showError('Load error', e);
    },
    type: 'json'
  })
}

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

function makeCompanyListTr(listItem) {
  return '<tr><td><label for="company_ps_reminder_' + listItem.id + '">' + listItem.name + '</label></td>' +
    '<td><input id="company_ps_reminder_' + listItem.id + '" type="checkbox" value="' + listItem.id + '"' + (listItem.count > 0 ? ' checked' : '') + ' /></td></tr>';
}

function makeCompanyListRegReminderTr(listItem) {
  return '<tr><td><label for="company_reg_reminder_' + listItem.id + '">' + listItem.name + '</label></td>' +
    '<td><input id="company_reg_reminder_' + listItem.id + '" type="checkbox" value="' + listItem.id + '"' + (listItem.count > 0 ? ' checked' : '') + ' /></td></tr>';
}

//------------------------------------------------------------------
//                Handlers
//------------------------------------------------------------------

$(document).on('load', function () {
  putLangDependentWords();
});

document.addEventListener('DOMContentLoaded', function () {
  loadContent();
});

$(document).on('click', '#login-button', function (e) {
  const email = $('#login-email').val();
  const password = $('#login-password').val();
  $.ajax({
    url: '/auth/login',
    type: 'POST',
    data: {
      username: email,
      password: password
    },
    success: function (response) {
      if (response.status === 'success') {
        setUser(response.user);
        loadContent();
        putLangDependentWords();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Ошибка входа', response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при попытке входа.');
    }
  });
});

$(document).on('click', '#close-menu-button', function (e) {
  $('#sidebar').toggleClass('w-16 w-64');
  $('main').toggleClass('ml-4 ml-64');
  if ($('#sidebar').hasClass('w-16')) {
    $(this).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4"><path d="m9 18 6-6-6-6"></path></svg>');
    $('#sidebar span.blink').hide();
  } else {
    $(this).html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-4 w-4"><path d="m15 18-6-6 6-6"></path></svg>');
    $('#sidebar span.blink').show();
  }
});

$(document).on('click', 'a.menuitem', function (e) {
  e.preventDefault();
  $('a.menuitem').removeClass('active');
  $('a.menuitem').removeClass('bg-sidebar-primary');
  $('a.menuitem').removeClass('text-sidebar-primary-foreground');
  $('a.menuitem').addClass('text-sidebar-foreground');
  $(this).addClass('active');
  $(this).addClass('bg-sidebar-primary');
  $(this).addClass('text-sidebar-primary-foreground');
  let url = $(this).attr('href');
  loadPage(url);
  return false;
});

$(document).on('click', 'div span.ptm-logout', function (e) {
  let user = getUser();
  $.ajax({
    url: '/auth/logout',
    type: 'POST',
    data: {
      token: user.token
    },
    success: function (response) {
      if (response.status === 'success') {
        clearUser();
        loadContent();
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка');
    }
  });
});

$(document).on('click', '#user-card-mini, #user-menu .user-menu-item', function () {
  $('#user-menu').toggle();
  userMenuResize();
})

$(document).on('click', '#lang-card-mini', function () {
  $('#lang-menu').toggle();
  userMenuResize();
})

$(document).on('click', '.company_open_profile', function (e) {
  e.preventDefault();
  let id = $(this).data('id');
  let data = {
    id: id
  }
  loadPage('/company/profile', data, true);
  return false;
});

$(document).on('click', '#company-edit-button, #company-add-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let id = $(this).data('id') ?? null;
  data = {
    token: token,
    id: id
  };
  loadPage('/company/edit', data, true);
});

$(document).on('click', '#task-edit-button, #task-add-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let id = $(this).data('id') ?? null;
  data = {
    token: token,
    id: id
  };
  loadPage('/task/edit', data, true, function () {
    if (id) {
      $('main h1').text(dictionaryLookup('taskEditing', user.lang));
    } else {
      $('main h1').text(dictionaryLookup('taskCreation', user.lang));
    }
  });
});

$(document).on('click', '#task-save-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let taskId = $('#task-edit-table input[name=id]').val();
  $.ajax({
    url: '/task/save',
    type: 'POST',
    data: {
      token: token,
      id: taskId,
      category: $('#task-edit-table input[name=category]').val(),
      request: $('#task-edit-table textarea[name=request]').val(),
      status: getSelectWidgetValue('status'),
      priority: getSelectWidgetValue('priority'),
      due_date: $('#task-edit-table input[name=due_date]').val(),
      company_id: getSelectWidgetValue('company'),
      accountant_id: getSelectWidgetValue('accountant'),
    },
    success: function (response) {
      if (response.status === 'success') {
        let data = {
          id: response.id
        }
        if (taskId) {
          loadPage('/task/view', data, 1);
        } else {
          loadPage('/task/view', data, 0);
        }
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang));
    }
  });
  return false;
});

$(document).on('click', '#company-save-button', function (e) {
  let user = getUser();
  let token = user ? user?.token : '';
  let data = {
    token: token,
    id: $('#company-edit-table input[name=id]').val(),
    name: $('#company-edit-table input[name=name]').val(),
    name_tg: $('#company-edit-table input[name=name_tg]').val(),
    type_id: getSelectWidgetValue('type'),
    pib: $('#company-edit-table input[name=pib]').val(),
    status: getSelectWidgetValue('status'),
    is_pdv: $('#company-edit-table input[name=is_pdv]').prop('checked') ? 1 : 0,
    activity_id: getSelectWidgetValue('activity'),
    accountant_id: getSelectWidgetValue('accountant'),
  };
  if (!data.pib) {
    showError(dictionaryLookup('pibIsRequired', user.lang), '');
    $('#company-edit-table input[name=pib]').focus();
    return false;
  }
  $.ajax({
    url: '/company/save',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let data = {
          id: response.id
        }
        loadPage('/company/profile', data, 1);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang));
    }
  });
  return false;
});

$(document).on('click', '.company_open_tasks', function (e) {
  e.preventDefault();
  let id = $(this).find('input.company').val();
  let data = {
    id: id
  }
  loadPage('/company/tasks', data);
  return false;
});

$(document).on('click', '.company_open_docs', function (e) {
  e.preventDefault();
  let id = $(this).find('input.company').val();
  let data = {
    id: id
  }
  loadPage('/company/docs', data);
  return false;
});

$(document).on('click', 'div[role=tablist] button[role=tab]', function (e) {
  let tabId = $(this).attr('id');
  let tablist = $(this).closest('div[role=tablist]');
  tablist.find('button[role=tab]').attr('data-state', 'inactive');
  $(this).attr('data-state', 'active');
  let parentTablist = tablist.parent();
  let selector = "div[role='tabpanel'][aria-labelledby='" + tabId + "']";
  let targetTab = parentTablist.find(selector);
  if (targetTab.length) {
    parentTablist.find('div[role=tabpanel]').attr('data-state', 'inactive').attr('hidden', '');
    targetTab.attr('data-state', 'active').removeAttr('hidden');
  }
});

$(document).on('click', '#add_note_button', function (e) {
  let companyId = $(this).data('company-id');
  let noteText = $('#note_textarea').val();
  let user = getUser();
  let token = user ? user?.token : '';
  $.ajax({
    url: '/company/add-note',
    type: 'POST',
    data: {
      token: token,
      company_id: companyId,
      note_text: noteText
    },
    success: function (response) {
      if (response.status === 'success') {
        $('#company-content-notes').html(response.data);
        $('#note_textarea').val('');
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении заметки.');
    }
  });
});

$(document).on('click', 'tr.task-row, div.task-row', function (e) {
  let taskId = $(this).data('task-id');
  let data = {
    id: taskId
  }
  loadPage('/task/view', data, true);
});

$(document).on('click', '.go-to-link', function (e) {
  const link = $(this).data('link');
  const count = $(this).data('count');
  if ((typeof count === 'number' && count > 0) || (typeof count === 'undefined')) {
    loadPage(link, {}, true);
  } else {
    const user = getUser();
    showError(dictionaryLookup('information', user.lang), dictionaryLookup('noItemsToShow', user.lang));
  }
  return false;
});

$(document).on('click', '.load-by-link', function (e) {
  const link = $(this).data('link');
  const target = $(this).data('target');
  const user = getUser();
  const data = {
    token: user.token
  };
  $.ajax({
    url: link,
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#' + target).html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', '#accountant-edit-button', function (e) {
  const id = $(this).data('id');
  const user = getUser();
  const data = {
    token: user.token,
    id: id
  };
  $.ajax({
    url: '/accountant/change',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#accountant-map').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', '#accountant-cancel-button', function (e) {
  const id = $(this).data('id');
  if (id) {
    const user = getUser();
    const data = {
      token: user.token,
    };
    $.ajax({
      url: '/accountant/page/' + id,
      type: 'POST',
      data: data,
      success: function (response) {
        if (response.status === 'success') {
          $('#accountant-map').html(response.data);
        } else if (response.status === 'logout') {
          clearUser();
          loadContent();
        } else {
          showError(dictionaryLookup('error', user.lang), response.message);
        }
      },
      error: function () {
        showError(dictionaryLookup('error', user.lang), 'Unknown error');
      }
    });
  } else {
    $('#accountant-map').empty();
  }
  return false;
});

$(document).on('click', '#accountant-save-button', function (e) {
  const table = $('#accountant-map table.accountant-page');
  const id = $(this).data('id');
  const email = $(table).find('input[name=accountant-email]').val();
  const firstname = $(table).find('input[name=accountant-firstname]').val();
  const lastname = $(table).find('input[name=accountant-lastname]').val();
  const role = getSelectWidgetValue('accountant-role');
  const lang = getSelectWidgetValue('accountant-lang');
  const user = getUser();
  const data = {
    token: user.token,
    id: id,
    email: email,
    firstname: firstname,
    lastname: lastname,
    role: role,
    lang: lang
  };
  if (!id) {
    data.password = $(table).find('input[name=accountant-password]').val();
  }
  $.ajax({
    url: '/accountant/write/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#accountant-map').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', 'button.back', function (e) {
  goBack();
});

$(document).on('click', 'tr.doc-row', function (e) {
  let docId = $(this).data('doc-id');
  let data = {
    id: docId
  }
  loadPage('/document/view', data, true);
});

$(document).on('click', 'a.document-view', function (e) {
  const id = $(this).data('doc-id');
  const data = { id: id };
  loadPage('/document/view', data, true);
  return false;
});

$(document).on('click', '#finish-task', function (e) {
  const taskId = $(this).data('task-id');
  const user = getUser();
  const data = {
    token: user.token,
    id: taskId
  };
  $.ajax({
    url: '/task/finish',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        loadPage('/task/view', { id: taskId }, 0);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      showError(dictionaryLookup('error', user.lang), 'Unknown error');
    }
  });
  return false;
});

$(document).on('click', '#sendComment', function (e) {
  const taskId = $(this).data('task-id');
  const commentText = $('#commentInput').val();
  const user = getUser();
  const data = {
    token: user.token,
    id: taskId,
    comment_text: commentText
  };
  $.ajax({
    url: '/task/comment',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#commentInput').val('');
        $('#task-comment-list').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении комментария.');
    }
  });
  return false;
});

$(document).on('click', '#doc-change-status', function (e) {
  $('#doc-change-status').hide();
  $('#doc-status-select').removeClass('hidden');
});

$(document).on('change', '#doc-status-select', function (e) {
  $('#doc-change-status').show();
  $('#doc-status-select').addClass('hidden');
  let user = getUser();
  let token = user ? user?.token : '';
  let docId = $(this).data('doc-id');
  let newStatus = $(this).val();
  $.ajax({
    url: '/document/change-status',
    type: 'POST',
    data: {
      token: token,
      id: docId,
      status: newStatus
    },
    success: function (response) {
      if (response.status === 'success') {
        $('#doc-status-block').html(response.data);
        $('#doc-activity-block').replaceWith(response.activity);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при изменении статуса документа.');
    }
  });
});

$(document).on('click', '#doc-send-comment', function (e) {
  const docId = $(this).data('document-id');
  const commentText = $('#doc-comment-input').val();
  const user = getUser();
  const data = {
    token: user.token,
    id: docId,
    comment_text: commentText
  };
  $.ajax({
    url: '/document/comment',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#commentInput').val('');
        $('#doc-comment-block').replaceWith(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при добавлении комментария.');
    }
  });
  return false;
});

async function uploadFile(file, taskId) {
  let formData = new FormData();
  formData.append('document', file);
  formData.append('task_id', taskId);
  const user = getUser();
  formData.append('token', user.token);

  try {
    const response = await fetch('/document/upload/', {
      method: 'POST',
      body: formData,
    });

    const result = await response.json();

    if (result.status == 'success') {
      formData = new FormData();
      formData.append('token', user.token);
      formData.append('id', taskId);
      const responseList = await fetch('/task/documents', {
        method: 'POST',
        body: formData,
      });
      return await responseList.json();
    } else if (result.status === 'logout') {
      clearUser();
      loadContent();
    } else {
      showError(dictionaryLookup('error', user.lang), response.message);
    }
  } catch (error) {
    console.error('Сетевая ошибка:', error);
    return { success: false, error: error.message };
  }
}

$(document).on('change', '#document-to-upload', async (e) => {
  const file = e.target.files[0];
  if (file) {
    const taskId = $(e.target).data('task-id');
    const result = await uploadFile(file, taskId);
    e.target.value = '';
    if (result.status === 'success') {
      $('#task-documents-list').html(result.data);
    } else {
      showError(dictionaryLookup('error', user.lang), result.message);
    }
  }
});

let isDialogOpen = false;
$(document).on('click', '#dropArea', () => {
  if (isDialogOpen) return;
  isDialogOpen = true;
  setTimeout(() => isDialogOpen = false, 300);
  $('#document-to-upload').click()
});
$(document).on('dragover', '#dropArea', (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ff7700');
});
$(document).on('dragleave', '#dropArea', (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ccc');
});
$(document).on('drop', '#dropArea', async (e) => {
  e.preventDefault();
  $('#dropArea').css('border-color', '#ccc');
  const originalEvent = e.originalEvent;
  const files = originalEvent.dataTransfer.files;
  if (files.length > 0) {
    const taskId = $('#dropArea').data('task-id');
    for (let i = 0; i < files.length; i++) {
      const result = await uploadFile(files[i], taskId);
      if (result.status === 'success') {
        $('#task-documents-list').html(result.data);
      } else {
        showError(dictionaryLookup('error', user.lang), result.message);
      }
    }
  }
});

$(document).on('click', '#save-user', function (e) {
  const form = $(this).closest('form');
  const formData = form.serializeArray();
  let data = {};
  formData.forEach(item => {
    data[item.name] = item.value;
  });
  let user = getUser();
  data.token = user.token;
  $.ajax({
    url: form.attr('action'),
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        loadPage('/accountant/profile', {}, false);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError(dictionaryLookup('error', user.lang), response.message);
      }
    },
    error: function () {
      alert('Произошла ошибка при сохранении данных пользователя.');
    }
  });
  return false;
});

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
$(document).on('click', '#reg-reminder-create', function (e) {
  let user = getUser();
  let data = {
    token: user.token,
  };
  $.ajax({
    url: '/reminder/reg-reminder-create/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let title = dictionaryLookup('createReminder', user.lang);
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

$(document).on('click', '.edit-reg-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let data = {
    token: user.token,
    id: id
  };
  $.ajax({
    url: '/reminder/reg-reminder-update/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        let title = dictionaryLookup('createReminder', user.lang);
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

/*
function fieldTranslate(fieldFrom, fieldTo, langFrom, langTo) {
  let textOriginal = $(fieldFrom).val();
  let user = getUser();
  let data = {
    token: user.token,
    text: textOriginal,
    from: langFrom,
    to: langTo
  };
  $.ajax({
    url: '/util/translate/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $(fieldTo).val(response.data.translation);
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
}
*/

// $(document).on('change', '#modal-create-reg-reminder input[name=type_ru]', function (e) {
//   return fieldTranslate('#modal-create-reg-reminder input[name=type_ru]', '#modal-create-reg-reminder input[name=type_rs]', 'ru', 'rs');
// });

// $(document).on('change', '#modal-create-reg-reminder input[name=type_rs]', function (e) {
//   return fieldTranslate('#modal-create-reg-reminder input[name=type_rs]', '#modal-create-reg-reminder input[name=type_ru]', 'rs', 'ru');
// });

// $(document).on('change', '#modal-create-reg-reminder input[name=text_ru]', function (e) {
//   return fieldTranslate('#modal-create-reg-reminder input[name=text_ru]', '#modal-create-reg-reminder input[name=text_rs]', 'ru', 'rs');
// });

// $(document).on('change', '#modal-create-reg-reminder input[name=text_rs]', function (e) {
//   return fieldTranslate('#modal-create-reg-reminder input[name=text_rs]', '#modal-create-reg-reminder input[name=text_ru]', 'rs', 'ru');
// });

$(document).on('click', '#save-reg-reminder', function (e) {
  let user = getUser();
  let modalBody = $(this).closest('.modal-window').find('.modal-body');
  let data = {
    token: user.token,
    id: $(modalBody).find('input[name="reminderId"]').val(),
    lang: $(modalBody).find('input[name="reminder-lang"]').val(),
    deadLine: $(modalBody).find('input[name="deadlineDay"]').val(),
    topic: $(modalBody).find('input[name="topic"]').val(),
    text: $(modalBody).find('input[name="text"]').val(),
  };
  $.ajax({
    url: '/reminder/reg-reminder-save/',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        if (response.action === 'created') {
          $('#reg-reminder-table-body').append(response.data);
        } else if (response.action === 'updated') {
          let row = $('#reg-reminder-row-' + response.reminder.id);
          $(row).replaceWith(response.data);
        }
        regReminderModal.close();
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

$(document).on('click', '.cancel-reg-reminder-btn', function (e) {
  if (confirm('Are you sure you want to delete this reminder?')) {
    let user = getUser();
    let reminderId = $(this).data('item-id');
    let data = {
      token: user.token,
      id: reminderId
    }
    let row = $(this).closest('tr');
    $.ajax({
      url: '/reminder/cancel-regular',
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
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-calendar-reminders/');
  loadCompanyListModal(id, '/company/list-to-calendar', makeCompanyListTr);
  companyListModal.open(this);
});

$(document).on('click', 'button.company-reg-reminder-btn', function (e) {
  let user = getUser();
  let id = $(this).data('item-id');
  let title = dictionaryLookup('regularReminders', user.lang);
  companyListModal = new Modal('modal-overlay', title);
  companyListModal.setDoUrl('/company/update-list-to-regular/');
  loadCompanyListModal(id, '/company/list-to-regular', makeCompanyListRegReminderTr);
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
  let doActionUrl = $(companyListModal.modal).find('#do-action-url').val();
  if (doActionUrl) {
    $.ajax({
      url: doActionUrl,
      type: 'POST',
      data: {
        token: token,
        reminder_id: reminder_id,
        checked_companies: checkedCompanies,
        uncheked_companies: unchekedCompanies
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
  const reminderId = $(this).attr('data-rm-id');
  const scheduleId = $(this).attr('data-sc-id');
  const companyId = $(this).attr('data-co-id');
  const token = user ? user?.token : '';
  const data = {
    token: token,
    reminder_id: reminderId,
    schedule_id: scheduleId,
    company_id: companyId
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
  const reminderId = $(this).data('rm-id');
  const scheduleId = $(this).data('sc-id');
  const companyId = $(this).data('co-id');
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

$(document).on('click', 'button.filters-on-off-button', function (e) {
  if ($('div.filter-box').hasClass('hidden')) {
    $('div.filter-box').removeClass('hidden');
  } else {
    $('div.filter-box').addClass('hidden');
  }
});

$(document).on('click', 'button.reset-filters-button', function (e) {
  $(this).closest('div.filter-box').find('select').val('');
});

$(document).on('click', '#company-find-button', function (e) {
  const user = getUser();
  const token = user ? user?.token : '';
  const data = {
    token: token,
  };
  let name = $('#search').val();
  let status = $('#status-filters-select').val();
  let accountant = $('#responsible-filters-select').val();
  let sort = $('#sorting-filters-select').val();
  if (name) {
    data.name = name;
  }
  if (status) {
    data.status = status;
  }
  if (accountant) {
    data.accountant = accountant;
  }
  if (sort) {
    data.sort = sort;
  }
  $.ajax({
    url: '/company/filter',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#company-list').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Select error', response.message ?? '');
      }
    },
    error: function (e) {
      showError('Select error', e);
    },
    type: 'json'
  });
});

$(document).on('click', '#task-find-button', function (e) {
  const user = getUser();
  const token = user ? user?.token : '';
  const data = {
    token: token,
  };
  let name = $('#search').val();
  let status = $('#status-filters-select').val();
  let priority = $('#priority-filters-select').val();
  let assignedTo = $('#assignedTo-filters-select').length > 0 ? $('#assignedTo-filters-select').val() : '';
  let company = $('#companyName-filters-select').val();
  if (name) {
    data.name = name;
  }
  if (status) {
    data.status = status;
  }
  if (priority) {
    data.priority = priority;
  }
  if (assignedTo) {
    data.assignedTo = assignedTo;
  }
  if (company) {
    data.company = company;
  }
  $.ajax({
    url: '/task/filter',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#task-list').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Select error', response.message ?? '');
      }
    },
    error: function (e) {
      showError('Select error', e);
    },
    type: 'json'
  });
});

$(document).on('click', '#doc-find-button', function (e) {
  const user = getUser();
  const token = user ? user?.token : '';
  const data = {
    token: token,
  };
  let name = $('#search').val();
  let status = $('#status-filters-select').val();
  let type = $('#documentType-filters-select').val();
  let company = $('#companyName-filters-select').val();
  if (name) {
    data.name = name;
  }
  if (status) {
    data.status = status;
  }
  if (type) {
    data.type = type;
  }
  if (company) {
    data.company = company;
  }
  $.ajax({
    url: '/document/filter',
    type: 'POST',
    data: data,
    success: function (response) {
      if (response.status === 'success') {
        $('#doc-list').html(response.data);
      } else if (response.status === 'logout') {
        clearUser();
        loadContent();
      } else {
        showError('Select error', response.message ?? '');
      }
    },
    error: function (e) {
      showError('Select error', e);
    },
    type: 'json'
  });
});


// ----------------------------------------------------
//                Resize Debounce
//----------------------------------------------------

function debounce(func, delay = 250) {
  let timeoutId;
  return function () {
    const context = this;
    const args = arguments;

    // Сбросить предыдущий таймер
    clearTimeout(timeoutId);

    // Установить новый таймер
    timeoutId = setTimeout(() => {
      func.apply(context, args);
    }, delay);
  };
}

function userMenuResize() {
  if ($('#user-menu').length) {
    let docWidth = document.documentElement.clientWidth;
    let menuWidth = $('#user-menu').get(0).offsetWidth;
    let transformUser = docWidth - menuWidth - 24;
    let transformLang = transformUser - 314;
    $('#user-menu').css('transform', 'translate(' + transformUser + 'px, 62px)');
    $('#lang-menu').css('transform', 'translate(' + transformLang + 'px, 54px)');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  window.addEventListener('resize', debounce(userMenuResize, 50));
  userMenuResize();
});

// ----------------------------------------------------
//                Document Ready
//----------------------------------------------------

$(document).ready(function () {
});


