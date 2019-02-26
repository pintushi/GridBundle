define([
    './datetime-cell'
], function(DateTimeCell) {
    'use strict';

    var DateCell;

    /**
     * Date column cell
     *
     * @export  oro/datagrid/cell/date-cell
     * @class   pintushi.grid.cell.DateCell
     * @extends pintushi.grid.cell.DateTimeCell
     */
    DateCell = DateTimeCell.extend({
        type: 'date',

        className: 'date-cell',

        /**
         * @inheritDoc
         */
        constructor: function DateCell() {
            DateCell.__super__.constructor.apply(this, arguments);
        }
    });

    return DateCell;
});
