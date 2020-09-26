/*!
 * 
 * cdebiTheme
 * 
 * @author 
 * @version 0.1.0
 * @link UNLICENSED
 * @license UNLICENSED
 * 
 * Copyright (c) 2020 
 * 
 * This software is released under the UNLICENSED License
 * https://opensource.org/licenses/UNLICENSED
 * 
 * Compiled with the help of https://wpack.io
 * A zero setup Webpack Bundler Script for WordPress
 */
(window.wpackiocdebiThemeappJsonp=window.wpackiocdebiThemeappJsonp||[]).push([[9],{206:function(e,i,s){s(4),e.exports=s(207)},207:function(e,i,s){"use strict";s.r(i);s(208),s(209),s(210)},208:function(e,i){var s,t;s=window.jQuery,(t={el:{fieldsRows:s("[data-row-span]"),fieldsContainers:s("[data-field-span]"),focusableFields:s("input, textarea, select","[data-field-span]"),window:s(window)},init:function(){this.focusField(this.el.focusableFields.filter(":focus")),this.equalizeFieldHeights(),this.events()},focusField:function(e){e.closest("[data-field-span]").addClass("focus")},removeFieldFocus:function(){this.el.fieldsContainers.removeClass("focus")},events:function(){var e=this;e.el.fieldsContainers.click((function(i){var t=e.el.focusableFields.selector;s(i.target).is(t)||s(this).find('input[type="text"], textarea, select').first().focus()})),e.el.focusableFields.focus((function(){e.focusField(s(this))})),e.el.focusableFields.blur((function(){e.removeFieldFocus()})),e.el.window.resize((function(){e.equalizeFieldHeights()}))},equalizeFieldHeights:function(){this.el.fieldsContainers.css("height","auto");var e=this.el.fieldsRows,i=this.el.fieldsContainers;this.areFieldsStacked()||e.each((function(){var e=s(this),t=e.css("height"),n=e.children(),o=n.children("textarea");1===n.length&&1===o.length||e.find(i).css("height",t)}))},areFieldsStacked:function(){var e=this.el.fieldsRows.not('[data-row-span="1"]').first(),i=0;return e.children().each((function(){i+=s(this).width()})),e.width()<=i}}).init(),window.GridForms=t},209:function(e,i,s){},210:function(e,i,s){},4:function(e,i,s){var t="cdebiThemedist".replace(/[^a-zA-Z0-9_-]/g,"");s.p=window["__wpackIo".concat(t)]}},[[206,0]]]);
//# sourceMappingURL=shortcode_mc4wp_gridform-55e22721.js.map