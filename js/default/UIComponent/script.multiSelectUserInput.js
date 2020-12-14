function addToSelected(item) {
  $('#sr-multi-user-search').value = "";
  $('#sr-multi-select-dropdown').empty();
  if (!$('ul > #' + item.id).length) {
    $('#sr-multi-user-select-input').append(`<option value="${item.id}" selected></option>`);
    $('#sr-multi-user-select').append(`<li id="${item.id}" data-label="${item.dataset.label}">${item.dataset.label}&nbsp;<span onclick="removeFromSelected($(this).parent())">&#10005;</span></li>`);
  }
}

function removeFromSelected(item) {
  console.log(item);
  $('#sr-multi-user-select').removeChild($());
  $('#sr-multi-user-select-input').removeChild($("input[value='" + item.id + "']"));
}

$(document).ready(function() {
  let user_search = $("#sr-multi-user-search");
  user_search.keyup(async function(e) {
    e.preventDefault();
    const data = await fetchData(
      this.dataset.autocomplete_url,
      this.value
    );

    if (data !== "undefined") {
      var list = $('#sr-multi-select-dropdown');
      if (list.length) {
        list.empty();
      } else {
        list = $('<ul id="sr-multi-select-dropdown">');
      }
      for (var i = 0; i < data.length; i++) {
        list.append(`<li id="${data[i]['value']}" data-label="${data[i]['label']}" onclick="addToSelected(this)">${data[i]['label']}</li>`);
      }
      user_search.after(user_search, list);
    }
  })
});

async function fetchData(url, term) {
  return $.ajax({
    type: 'GET',
    url: url,
    data: {
      term: term
    },
    success: (response) => {
      return response;
    },
    async: true
  });
}