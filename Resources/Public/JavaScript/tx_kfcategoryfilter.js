/**
 * Created by marcfinnern on 21.01.17.
 */


var $grid,
    $gridDebug = 0;
function initIsoAll(){

    initIso();
    initIsoFunctions();


    if($grid.height() < 10){
        setTimeout(initIso,91);
        if($gridDebug) console.log("init again");
    }
}

function initIso(){
    // init Isotope
    $grid = $('.grid').isotope({
        itemSelector: '.element-item',
        masonry: {
            columnWidth: 0
        },
        getSortData: {
            name: '.name',
            symbol: '.symbol',
            number: '.number parseInt',
            category: '[data-category]',
            weight: function( itemElem ) {
                var weight = $( itemElem ).find('.weight').text();
                return parseFloat( weight.replace( /[\(\)]/g, '') );
            }
        }
    });
}

function initIsoFunctions(){
    // bind filter button click
    $('#filters').on( 'click', 'button', function() {
        var filterValue = $( this ).attr('data-filter');
        // use filterFn if matches value
        filterValue = filterFns[ filterValue ] || filterValue;
        $grid.isotope({ filter: filterValue });
    });


    // filter functions
    var filterFns = {
        // show if number is greater than 50
        numberGreaterThan50: function() {
            var number = $(this).find('.number').text();
            return parseInt( number, 10 ) > 50;
        },
        // show if name ends with -ium
        ium: function() {
            var name = $(this).find('.name').text();
            return name.match( /ium$/ );
        }
    };

    // change is-checked class on buttons
    $('.button-group').each( function( i, buttonGroup ) {
        var $buttonGroup = $( buttonGroup );
        $buttonGroup.on( 'click', 'button', function() {
            $buttonGroup.find('.is-checked').removeClass('is-checked');
            $( this ).addClass('is-checked');
        });
    });
}

(function ($, root, undefined) {

    setTimeout(initIsoAll,91);

})(jQuery, this);
