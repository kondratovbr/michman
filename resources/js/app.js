require('./bootstrap');

import 'tippy.js/dist/tippy.css';

import Alpine from 'alpinejs'
window.Alpine = Alpine

import tooltip from "./tooltip";
Alpine.plugin(tooltip);
import focusableDialog from './components/FocusableDialog';
window.focusableDialog = focusableDialog;

import select from './components/Select';
window.select = select;

import searchSelect from './components/SearchSelect'
window.searchSelect = searchSelect;

Alpine.start()
