import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
	connect() {
		this.closeDropdownsOnOutsideClick = this.closeDropdownsOnOutsideClick.bind(this);
		document.addEventListener('click', this.closeDropdownsOnOutsideClick);
	}

	disconnect() {
		document.removeEventListener('click', this.closeDropdownsOnOutsideClick);
	}

	toggle(event) {
		if (window.matchMedia('(min-width: 992px)').matches) {
			return;
		}

		event.preventDefault();

		const currentDropdown = event.currentTarget.closest('.dropdown');

		if (!currentDropdown) {
			return;
		}

		this.element.querySelectorAll('.dropdown.open').forEach((dropdown) => {
			if (dropdown !== currentDropdown) {
				dropdown.classList.remove('open');
			}
		});

		currentDropdown.classList.toggle('open');
	}

	closeDropdownsOnOutsideClick(event) {
		if (this.element.contains(event.target)) {
			return;
		}

		this.element.querySelectorAll('.dropdown.open').forEach((dropdown) => {
			dropdown.classList.remove('open');
		});
	}
}