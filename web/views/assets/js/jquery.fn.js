$(document).ready(function (){
    var API = {
        
        login: function(data) {
            $.ajax({
                url: "/login",
                method: "POST",
                data: data,
                dataType: "json",
                success: function (result) {
                    if (result.status === 'OK') {
                        localStorage.setItem('token', result.token);
                        setCookie('X-AUTH-TOKEN', result.token, 365 * 5);
                        $(location).attr('href', 'http://localhost:9001/order');
                    } else {
                        $('#alert-error').html(result.message).fadeIn(0).fadeOut(5000);
                    }
                },
                error: function (error) {
                    $('#alert-error').html("Authentication failed! Please check your email and password.").fadeIn(0).fadeOut(1000);
                }
            });
        },

        saveUserOrder: function() {

            var defer = jQuery.Deferred();

            defer.then(function(){
                var ordersData = [];
                $( ".order-cell" ).each(function(  ) {
                    ordersData.push({
                        day: $(this).data('day'),
                        menu_dish_id: $(this).parent().parent().data('menu-id'),
                        count: $(this).val()
                    });
                });

                return ordersData;
            }).then(function(ordersData) {
                $.ajax({
                    url: "/api/v1/orders",
                    method: "POST",
                    data: JSON.stringify(ordersData),
                    dataType: "json",
                    success: function (result) {
                        console.log(result);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });

            defer.resolve();
        },

        saveDishes: function() {

            var dishesData = [];
            $( ".wh-table tr" ).each(function() {
                var className = $(this).attr('class');
                if (className !== undefined && className.startsWith('group')) {

                    dishesData.push({
                        id: $(this).data('dish-id'),
                        name: $(this).children( "th" ).children("input[name='name']").val(),
                        price: parseFloat($(this).children( "th" ).children("input[name='price']").val()),
                        description: $(this).children( "th" ).children("input[name='description']").val(),
                        ingredients: $(this).children( "th" ).children("input[name='ingredients']").val(),
                        weight: parseFloat($(this).children( "th" ).children("input[name='weight']").val()),
                        dish_group_id: $(this).data('group-id'),
                    });
                }
            });

            console.log(dishesData);

            // var defer = jQuery.Deferred();

            // defer.then(function(){
            //     var dishesData = [];
            //     $( ".order-cell" ).each(function(  ) {
            //         ordersData.push({
            //             day: $(this).data('day'),
            //             menu_dish_id: $(this).parent().parent().data('menu-id'),
            //             count: $(this).val()
            //         });
            //     });

            //     return dishesData;
            // }).then(function(dishesData) {
            //     $.ajax({
            //         url: "/api/v1/orders",
            //         method: "POST",
            //         data: JSON.stringify(dishesData),
            //         dataType: "json",
            //         success: function (result) {
            //             console.log(result);
            //         },
            //         error: function (error) {
            //             console.log(error);
            //         }
            //     });
            // });

            // defer.resolve();
        }
    };

    function setCookie(cname,cvalue,exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    $("#salads-header").click(function(){
        $( "#salads-list" ).toggle();
    });

    $("#soups-header").click(function(){
        $( "#soups-list" ).toggle();
    }); 

    $("#main-dish-header").click(function(){
        $( "#main-dish-list" ).toggle();
    }); 

    $("#dessert-header").click(function(){
        $( "#dessert-list" ).toggle();
    }); 


    // MODAL
    $(".dish-link").click(function(){
        $("#myModal").modal();
    });


    // ORDER
    $(".dish-list tr th input").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
            (e.keyCode >= 25 && e.keyCode <= 40)) {
                return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $(".dish-list tr th input").focus(function (e) {
        if (parseInt($(this).val()) === 0) {
            $(this).val('');
        }
    });

    $(".dish-list tr th input").blur(function (e) {
        num = parseInt($(this).val());

        if (isNaN(num) || num < 0) {
            $(this).val('0');
        } else {
            $(this).val(num);
        }
    });

    $( ".dish-link" ).hover(
        function() {
            $(".dish-hint").css("display", "block");
        }, function() {
            $(".dish-hint").css("display", "none")
        }
    );

    $(".nav-link").click(function() {
        $(location).attr('href', $(this).attr('href'));
    });

    
    //LOGIN
    $("#login-btn").click(function(){
        API.login({
            email: $("#form-username").val(),
            password: $("#form-password").val()
        });
        return false;
    });

    //SAVE ORDER
    $("#save-order").click(function(){
        API.saveUserOrder();
        return false;
    });

    //ADD dish
    $(".add-dish").click(function(){
        var groupId = $(this).data('group-id');
        $(".group-" + groupId).last().after( 
            '<tr class="group-' + groupId + '" data-dish-id="">' +
                '<th class="wh-name"><input type="text" name="name" value="" placeholder="Введіть ім\'я" /></th>' +
                '<th><input name="description" type="text" value="" placeholder="Опис.."/></th>' +
                '<th><input name="ingredients" type="text" value="" placeholder="Інгредієнти.."/></th>' +
                '<th width="5%"><input name="weight" type="number" value="" placeholder="Вага.."/></th>' +
                '<th width="5%"><input name="price" type="number" value="" placeholder="Ціна.."/></th>' +
            '</tr>' 
        );
        return false;
    });

    //SAVE DISHES
    $("#save-dishes").click(function(){
        API.saveDishes();
        return false;
    });

});   