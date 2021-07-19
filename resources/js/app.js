require('./bootstrap');

import Alpine from 'alpinejs'
window.Alpine = Alpine

import focusableDialog from './components/FocusableDialog';
window.focusableDialog = focusableDialog;

import select from './components/Select';
window.select = select;

import searchSelect from './components/SearchSelect'
window.searchSelect = searchSelect;

Alpine.start()
