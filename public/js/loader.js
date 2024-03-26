
function loader_start(){
    document.getElementById('container').classList.add('body');
    document.getElementById('loading-div').classList.add('loader');
}

function loader_stop(time){
    setTimeout(function () {
        document.getElementById('container').classList.remove('body');
        document.getElementById('loading-div').classList.remove('loader');
    }, time)
}

function stop_scroll(){
    // scroll_timerId = setTimeout(jQuery(window).on('scroll', function() {
    //     jQuery('html, body').animate({scrollTop: jQuery(this).scrollTop()}, 550);
    // }), 0);
    // $('html, body').css('overflow-y', 'hidden');
}
function resume_scroll(){
    // console.log(scroll_timerId)
    // clearTimeout(scroll_timerId);
    // console.log('waaa')
    // $('html, body').css('overflow-y', 'auto');
}