$(function() {

    $('#city').click(function(event)
    {
        event.preventDefault();

        $('.select_city').toggle();
    });


    // поиск в базе городов по названию
    $('#city_name').keyup(function()
    {
        var city_name = $('#city_name').val();

        if (city_name.length > 1) {

            $.ajax({
                url: '/site/findcity',

                data : {
                    'city_name': city_name

                },
                success: function(result){
                    if (result) {
                        var response = JSON.parse(result);

                        $('#city_ul').empty();
                        for (var i = 0; i < response.length; i++) {
                            $('#city_ul').append('<li><a class="city_li" href="#" data-id="' + response[i]['id'] + '">' + response[i]['name'] + '</a></li>');
                        }

                        $('.city_li').click(setCity);

                    }
                },
                error: function(result) {

                },
                type: "POST"
            });

        }

    });
});


// выбор города и установка куки
function setCity(event)
{
    event.preventDefault();
    var id = $(event.target).data('id');

    $.ajax({
        url: '/site/setcity',

        data : {
            'id': id

        },
        success: function(result){

            window.location.href = 'http://servergoods.ru';
        },
        error: function(result) {

        },
        type: "POST"
    });

}