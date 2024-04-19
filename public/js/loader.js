window.onload = function () {
    loader_start_unverified();
}





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

function loader_start_desc(){
    document.getElementById('description').disabled=true;
    $('#description').addClass('description_style');
}
function loader_stop_desc(time){
    setTimeout(function () {
        document.getElementById('description').disabled=false;
        $('#description').removeClass('description_style');
    }, time)
}


function loader_start_unverified(){
    document.getElementById('container-ai').classList.add('body');
    document.getElementById('loading-ai').classList.add('loader-ai');
}
