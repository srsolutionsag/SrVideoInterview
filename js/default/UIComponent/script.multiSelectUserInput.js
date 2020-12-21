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
			results_list,
			selected_list,
			user_template,
			running = false,
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
			results_list = element.find('.sr-results-users-list');
			selected_list = element.find('.sr-selected-users-list');
			hidden_wrapper = element.find('.sr-multi-user-search-container');
			user_template = element.find('.sr-multi-user-template');

			// search_field
			search_field.keyup(async function (e) {
				if (running) {
					return;
				}
				e.preventDefault();
				const data = await fetchData(
					this.dataset.autocomplete_url,
					this.value
				);

				if (data !== "undefined") {
					if (results_list.length) {
						results_list.empty();
					}
					for (let i = 0; i < data.length; i++) {
						let user_id = data[i]['value'];
						let label = data[i]['label'];
						let user = user_template.clone();
						user.removeAttr('class');
						user.attr('data-item-id', user_id);
						user.attr('data-label', label);
						user.html(label);
						user.on('click', function () {
							let user = $(this);
							user.unbind('click');
							// append hidden input
							let input = $(`<input type='hidden' name='selected[]' value='${user.attr('data-item-id')}'/>`);
							user.append(input);
							// append close glyph
							let close = $(`<span>&nbsp; &#10005;</span>`);
							close.on('click', function () {
								$(this).parent().remove();
							});
							user.append(close);
							// move to the selected list
							user.detach();
							selected_list.append(user);
							// clear list
							search_field.value = '';
							results_list.empty();
						});
						results_list.append(user);
					}
				}
			});

		};


		let addToSelected = function (item) {
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
			running = true;
			return $.ajax({
				type:    'GET',
				url:     url,
				data:    {
					term: term
				},
				success: (response) => {
					running = false;
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
