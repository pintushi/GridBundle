define([
    'oroui/js/mediator',
    './model-action'
], function(mediator, ModelAction) {
    'use strict';

    var FrontendAction;

    /**
     * Action triggers frontend event
     *
     * @export oro/datagrid/action/Frontend-action
     * @class pintushi.grid.action.FrontendAction
     * @extends pintushi.grid.action.ModelAction
     */
    FrontendAction = ModelAction.extend({
        /**
         * @inheritDoc
         */
        constructor: function FrontendAction() {
            FrontendAction.__super__.constructor.apply(this, arguments);
        },

        /**
         * @inheritDoc
         */
        execute: function() {
            mediator.trigger('datagrid:frontend:execute:' + this.datagrid.name, this);
            if (!this.disposed) {
                this.$el.dropdown('toggle');
            }
        }
    });

    return FrontendAction;
});
