
var filterBy = {
    priceIntervale: {
        min: 10,
        max: 1000,
    },
    datetime: {today: false, thisWeek: false, thisMonth: false},
    category: {food: false, sports: false, entertainment: false, realEstate: false, vehicle: false},
    price: {allPrices: true, asc: false, desc: false}
}


function setPriceIntervale(min, max) {
    filterBy.priceIntervale = {
        min: min,
        max: max,
    }
}