require('./bootstrap');

require('alpinejs');

import focusableDialog from './components/FocusableDialog';
window.focusableDialog = focusableDialog;

import searchSelectOld from './components/SearchSelectOld';
window.searchSelectOld = searchSelectOld;

import searchSelect from './components/SearchSelect';
window.searchSelect = searchSelect;

import select from './components/Select'
window.select = select;

import selectProper from './components/SelectProper'
window.selectProper = selectProper;
