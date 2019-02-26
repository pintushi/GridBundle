define([
    './model-action'
], function(ModelAction) {
    'use strict';

    var AjaxAction;

    /**
     * Ajax action, triggers REST AJAX request
     *
     * @export  oro/datagrid/action/ajax-action
     * @class   pintushi.grid.action.AjaxAction
     * @extends pintushi.grid.action.ModelAction
     */
    AjaxAction = ModelAction.extend({
        /**
         * @inheritDoc
         */
        constructor: function AjaxAction() {
            AjaxAction.__super__.constructor.apply(this, arguments);
        }
    });

    return AjaxAction;
});
