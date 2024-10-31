function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}

const lictencat_handle_enable_toggle = (e, el, id) => {

	e.preventDefault();

	const countColumn = el.parentNode.parentNode.parentNode.querySelector('.column-listencat_count');
	const timeColumn = el.parentNode.parentNode.parentNode.querySelector('.column-listencat_time');

	const switchEl = el.parentNode;
	switchEl.classList.add('loading');

	const enable = el.checked;

	jQuery(document).ready(function($) {
		var data = {
			'action': 'listencat_convert_post_ajax',
			'enable': enable,
			'id': id
		};

		jQuery.post(ajaxurl, data, function(response) {
			if (response) {

				const { playCount: count, playMinutes: time } = JSON.parse(response);
				const notice = document.querySelector('.listencat-notice--add-first');

				if (enable) {

					const url = window.location.pathname;
					const newUrl = `${url}?listencat_successfuly_converted=1`;
					window.location.replace( newUrl );
					
				} else {

					const url = window.location.pathname;
					window.location.replace( url );

				}

			} else {

				const url = window.location.pathname;
				const newUrl = `${url}?listencat_unable_to_convert=1`;
				window.location.replace( newUrl );
			}
			switchEl.classList.remove('loading');
		});
	});

}

const lictencat_handle_enable_toggle_single_post = (e, el, id) => {

	e.preventDefault();

	const switchEl = el.parentNode;
	switchEl.classList.add('loading');

	const enable = el.checked;
	const box = el.parentNode.parentNode;

	jQuery(document).ready(function($) {
		var data = {
			'action': 'listencat_convert_post_ajax',
			'enable': enable,
			'id': id
		};

		jQuery.post(ajaxurl, data, function(response) {
			if (response) {

				const { playCount: count, playMinutes: time } = JSON.parse(response);

				if (enable) {
					el.checked = true;
					box.classList.add('checked');
				} else {
					el.checked = false;
					box.classList.remove('checked');
				}

			}
			switchEl.classList.remove('loading');
		});
	});

}

const lictencat_handle_bulk_actions = () => {

	const bulkActionForm = document.querySelector('#posts-filter');
	if (bulkActionForm) {
		const applyButton = bulkActionForm.querySelector('#doaction');
		const select = bulkActionForm.querySelector('#bulk-action-selector-top');
		applyButton.addEventListener('click', () => {
			const value = select.value;

			const rowsMarked = [...bulkActionForm.querySelectorAll('#the-list tr')].filter( tr => tr.querySelector('.check-column input').checked );
			const inputs = rowsMarked.map( tr => tr.querySelector('.column-listencat_enable input') );

			if ( value === 'listencat_add_audio' ) {
				[...inputs].filter( input => !input.checked ).forEach( input => input.parentNode.classList.add('loading') );
			} else if ( value === 'listencat_remove_audio' ) {
				[...inputs].filter( input => input.checked ).forEach( input => input.parentNode.classList.add('loading') );
			}
		})
	}

}

const listencat_setup_add_first_post_button = () => {
	const listencatAddFirstAudio = document.querySelector('#listencat_add_first_audio');
	if (listencatAddFirstAudio) {
		listencatAddFirstAudio.addEventListener('click', e => {
			e.preventDefault();
			
			if ( !listencatAddFirstAudio.classList.contains('adding') ) {

				listencatAddFirstAudio.innerText = 'Adding...';
				listencatAddFirstAudio.classList.add('adding');

				const id = listencatAddFirstAudio.dataset.id;

				const data = {
					'action': 'listencat_convert_post_ajax',
					'enable': true,
					'id': id
				};

				jQuery.post(ajaxurl, data, function(response) {
					if (response) {
						const url = window.location.href;
						const newUrl = updateQueryStringParameter(url, 'listencat_successfuly_converted', 1);
						window.location.replace( newUrl );
					} else {
						const url = window.location.href;
						const newUrl = updateQueryStringParameter(url, 'listencat_unable_to_convert', 1);
						window.location.replace( newUrl );
					}
				});

			}

		})
	}
}

window.onload = () => {
  lictencat_handle_bulk_actions();
  listencat_setup_add_first_post_button();
};

