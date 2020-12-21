il = il || {};
il.Plugins = il.Plugins || {};
il.Plugins.SrMultiUserSearchInputGUI = il.Plugins.SrMultiUserSearchInputGUI || {};
(function ($, il) {
	/**
	 * @TODO: improve performance by using the same instance when retaking a record.
	 * @TODO: fix preview error, that doesn't work when retaking an existing recording.
	 *
	 * @type {{init: init}}
	 */
	il.Plugins.SrMultiUserSearchInputGUI = (function ($) {
		var element,
			search_field,
			results,
			hidden_wrapper;
		/**
		 * VideoRecorderInput
		 *
		 * @param {string} id
		 * @param {string} settings
		 */
		let init = function (id) {
			console.log(id);
			element = $('#' + id);
			search_field = element.find('.sr-multi-user-searchfield');
			results = element.find('.sr-selected-users-list');
			hidden_wrapper = element.find('.sr-multi-user-search-container');

			// search_field
			search_field.keyup(async function (e) {
				e.preventDefault();
				const data = await fetchData(
					this.dataset.autocomplete_url,
					this.value
				);

				if (data !== "undefined") {
					console.log(data)

					if (results.length) {
						results.empty();
					}
					for (let i = 0; i < data.length; i++) {
						results.append(`<li id="${data[i]['value']}" data-label="${data[i]['label']}" onclick="addToSelected(this)">${data[i]['label']}</li>`);
					}
					// user_search.after(user_search, list);
				}
			});

		};


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
			// $('#sr-multi-user-select').removeChild($());
			$('#sr-multi-user-select-input').removeChild($("input[value='" + item.id + "']"));
		}


		async function fetchData(url, term) {
			return $.ajax({
				type:    'GET',
				url:     url,
				data:    {
					term: term
				},
				success: (response) => {
					return response;
				},
				async:   true
			});
		}


		return {
			init: init,
		};
	})($);
})($, il);
