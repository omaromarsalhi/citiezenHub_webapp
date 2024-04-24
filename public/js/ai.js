


function showModelUnverfied(idProduct){
    console.log('response')

    $.ajax({
        url: "/ai/result/check4verification",
        type: "POST",
        data: {
            idProduct: idProduct,
        },
        async: true,
        success: function (response) {
            console.log(response)
            if(response.doesItExists===false)
                $('#loadingModel').modal("show")
            else {
                str2Add='';
                for (let i = 0; i < response.data.length; i++) {
                    str2Add+='<details>\n' +
                        '<summary>Image N° '+(i+1)+'</summary>\n'
                    if(response.data[i].title===false) {
                        str1=response.data[i].titleData.replace(' No,', ' Title : ')
                        str2Add += '<div>' + str1.replace('paragraph', ' image ')+'</div>\n'
                    }
                    if(response.data[i].category===false) {
                        str2 = response.data[i].categoryData.replace(' No,', ' Category : ');
                        str2Add += '<div>' + str2.replace('paragraph', ' image ') + '</div>\n'
                    }
                    str2Add+='</details>'
                }
                $('#sectionData').html(str2Add);
                $('#aiModel').modal("show")
            }

        },
        error:function (resp){
            console.log('omar')
        },
    });
}