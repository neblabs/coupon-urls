export default class RowsComponentsFinder
{
    rowsCopy = [];

    constructor(rowsCopy: array) 
    {
        this.rowsCopy = rowsCopy;
    }

    find(temporaryID) : any
    {
        for (let row of this.rowsCopy) { 
            if (row.temporaryID === temporaryID) {
                return row;
            }

            for (let column of row.columns) {
                if (column.temporaryID === temporaryID) {
                    return column;
                }

                for (let defaultOffer of column.defaultOffers) {
                    if (defaultOffer.temporaryID === temporaryID) {
                        return defaultOffer;
                    }
                }

                for (let context of column.contexts) {
                    if (context.temporaryID === temporaryID) {
                        return context;
                    }

                    for (let conditionOrFilter of context.conditionsOrFilters) {
                        if (conditionOrFilter.temporaryID === temporaryID) {
                            return conditionOrFilter;
                        }
                    }

                    for (let offer of context.offers) {
                        if (offer.temporaryID === temporaryID) {
                            return offer;
                        }
                    }  
                }   
            }
        }
    }
}