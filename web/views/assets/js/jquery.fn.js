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
                        $(location).attr('href', '/order');
                    } else {
                        spop({
                            template: 'Помилка :( Перевірте будь-ласка ваш email та пароль.',
                            position  : 'bottom-right',
                            style: 'error',
                            autoclose: 3000
                        });
                    }
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Перевірте будь-ласка ваш email та пароль.',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
                    });
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
                        menu_dish_id: $(this).parent().parent().attr('data-menu-id'),
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
                        spop({
                            template: 'Замовлення збережене! Дякуємо :)',
                            position  : 'bottom-right',
                            style: 'success',
                            autoclose: 3000
                        });
                    },
                    error: function (error) {
                        spop({
                            template: 'Помилка :( Перевірте будь-ласка ваше замовлення.',
                            position  : 'bottom-right',
                            style: 'error',
                            autoclose: 3000
                        });
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
                    var name = $(this).children( "th" ).children("input[name='name']").val(),
                        price = parseFloat($(this).children( "th" ).children("input[name='price']").val()),
                        description = $(this).children( "th" ).children("input[name='description']").val(),
                        ingredients = $(this).children( "th" ).children("input[name='ingredients']").val(),
                        weight = parseFloat($(this).children( "th" ).children("input[name='weight']").val()),
                        groupId = $(this).data('group-id'),
                        tmpId = $(this).data('tmp-id'),
                        dishId = $(this).data('dish-id');

                    dishesData.push({
                        id: dishId || null,
                        name: name,
                        price: isNaN(price) ? 0 : price,
                        description: description,
                        ingredients: ingredients,
                        weight: isNaN(weight) ? 0 : weight,
                        dish_group_id: groupId,
                        tmp_id: tmpId || null
                    });
                }
            });

            $.ajax({
                url: "/api/v1/dishes",
                method: "POST",
                data: JSON.stringify(dishesData),
                dataType: "json",
                success: function (dishes) {
                    for (const index in dishes) {
                        if (dishes.hasOwnProperty(index)) {
                            var dish = dishes[index];
                            if (dish.tmp_id) {
                                var dishTableRow = $('tr[data-tmp-id="' + dish.tmp_id + '"]');
                                dishTableRow.removeAttr('data-tmp-id');
                                dishTableRow. attr('data-dish-id', dish.id);
                            }
                        }
                    }
                    spop({
                        template: 'Зміни успішно збережені!',
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося зберегти зміни! Перевірте будь-ласка правильність введених даних!',
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                }
            });
        },

        removeDishFromMenu: function(menuId, callback) {
            $.ajax({
                url: "/api/v1/menudishes/" + menuId,
                method: "DELETE",
                contentType:'application/json',
                success: function (dish) {
                    var menuRow = $('tr[data-dish-id="' + dish.dish_id + '"]');
                    menuRow.attr('data-menu-id', dish.id);
                    menuRow.attr('data-menu-id', '');
                    callback();
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося видалити страву з меню!',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
                    });
                }
            });
        },

        addDishToMenu: function(data, callback) {
            $.ajax({
                url: "/api/v1/menudishes",
                method: "POST",
                data: JSON.stringify(data),
                dataType: "json",
                success: function (dish) {
                    console.log(dish);
                    var menuRow = $('tr[data-dish-id="' + dish.dish_id + '"]');
                    menuRow.attr('data-menu-id', dish.id);
                    callback();
                    
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося додати страву в меню!',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
                    });
                }
            });
        }
    }

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
    $(".add-dish").click(function() {
        var groupId = $(this).attr('data-group-id');
        $(".group-" + groupId).last().after( 
            '<tr class="group-' + groupId + '" data-dish-id="" data-group-id="' + groupId + '" data-tmp-id="' + Math.floor(Math.random() * 1000001) +'">' +
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

    $("#edit-menu").click(function() {
        $("#menu-header").css('display', 'none');
        $("#editable-menu-header").css('display', 'block');
        $(".wh-actions").css('display', 'table-cell');
        $(".wh-table .menu-item").css('display', 'table-row');
    });

    $("#save-menu").click(function() {
        $("#menu-header").css('display', 'block');
        $("#editable-menu-header").css('display', 'none');
        $(".wh-actions").css('display', 'none');
        
        $( ".wh-table .menu-item .wh-actions input" ).each(function() {
            if($(this).is(':checked')) {
                $(this).parent().parent().parent().css('display', 'table-row');
            } else {
                $(this).parent().parent().parent().css('display', 'none');
            }
        });
    });

    $(".wh-table .switch").click(function(event) {
        var checkbox = $(this).children('input');
        
        if (checkbox.is(':checked')) {
            var menuId = $(this).parent().parent().attr('data-menu-id');
            if (menuId) {
                API.removeDishFromMenu(menuId, function(){
                    checkbox.prop('checked', false);
                });
            } else {
                checkbox.prop('checked', false);
            }
        } else {
            var dishData = {
                start: $(".date-range").data('start'),
                end: $(".date-range").data('end'),
                dish_id: $(this).parent().parent().attr('data-dish-id')
            };
            API.addDishToMenu(dishData, function(){
                checkbox.prop('checked', true);
            });
        }

        return false;
    });

    $(".order-filter").change(function() {
        $("#order-filters").submit();
    });

});   