$(document).ready(function (){

    $(".show-add-dish-group").click(function () {
        $(".add-new-group-popup").css('display', 'block');
    });

    $(".close-new-group-popup").click(function () {
        $(".add-new-group-popup").css('display', 'none');
    });

    $(".group-header").click(function(){
        var groupId = $(this).attr('data-group-id');
        $(".group-" + groupId).toggle();
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

    $(".order-cell").focus(function (e) {
        if (parseInt($(this).val()) === 0) {
            $(this).val('');
        }
    });

    $(".order-cell").blur(function (e) {
        num = parseInt($(this).val());

        if (isNaN(num) || num < 0) {
            $(this).val('0');
        } else {
            $(this).val(num);
        }
    });

    $(".modal-order-cell").focus(function (e) {
        if (parseInt($(this).val()) === 0) {
            $(this).val('');
        }
    });

    $(".modal-order-cell").blur(function (e) {
        num = parseInt($(this).val());

        if (isNaN(num) || num < 0) {
            $(this).val('0');
        } else {
            $(this).val(num);
        }
    });

    $( ".dish-link" ).hover(
        function() {
            $(this).find(".dish-hint").css("display", "block");
        }, function() {
            $(this).find(".dish-hint").css("display", "none");
        }
    );

    $(".nav-link").click(function() {
        $(location).attr('href', $(this).attr('href'));
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

    $(".show-add-new-company").click(function () {
        $(".add-new-group-popup").css('display', 'block');
    });

    $(".users-tabs li").click(function () {
        var tab = $(this).attr('data-tab');

        if (tab === 'users') {
            $('li[data-tab="stuff"]').removeClass('active');
            $('li[data-tab="users"]').removeClass('active').addClass('active');
            $('#users').css('display', 'table');
            $('#stuff').css('display', 'none');
        } else {
            $('li[data-tab="users"]').removeClass('active');
            $('li[data-tab="stuff"]').removeClass('active').addClass('active');
            $('#users').css('display', 'none');
            $('#stuff').css('display', 'table');
        }
    });

    $(".user-name-input").change(function () {
        $(this).parent().find(".user-email-actions").css('display', 'block');
    });

    $(".user-email-actions-cancel").click(function () {
        var input = $(this).parent().parent().find(".user-name-input");
        input.val(input.attr('value'));
        $(this).parent().parent().find(".user-email-actions").css('display', 'none');
    });
    
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

    var API = {
        login: function(data) {
            $.ajax({
                url: "/login",
                method: "POST",
                data: data,
                dataType: "json",
                success: function (result) {
                    if (result.status === 'OK') {
                        setCookie('X-AUTH-TOKEN', result.token, 365 * 5);
                        $(location).attr('href', '/order');
                    } else {
                        spop({
                            template: 'Помилка :( Перевірте будь-ласка ваш email та пароль.',
                            position  : 'top-left',
                            style: 'error',
                            autoclose: 6000
                        });
                    }
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Перевірте будь-ласка ваш email та пароль.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

                
        register: function(data) {
            $.ajax({
                url: "/register",
                method: "POST",
                data: data,
                dataType: "json",
                success: function (result) {
                    if (result.status === 'OK') {
                        setCookie('X-AUTH-TOKEN', result.token, 365 * 5);
                        $(location).attr('href', '/order');
                    } else {
                        spop({
                            template: 'Сталася помилка :( Перевірте будь-ласка введені дані.',
                            position  : 'top-left',
                            style: 'error',
                            autoclose: 6000
                        });
                    }
                },
                error: function (error) {
                    if (error.responseJSON && error.responseJSON.status === 'ERROR' && error.responseJSON.message) {
                        spop({
                            template: error.responseJSON.message,
                            position  : 'top-left',
                            style: 'error',
                            autoclose: 6000
                        });
                    } else {
                        spop({
                            template: 'Помилка :( Перевірте будь-ласка введені дані.',
                            position  : 'top-left',
                            style: 'error',
                            autoclose: 6000
                        });
                    }
                }
            });
        },
     
        restoreEmail: function(email) {
            $.ajax({
                url: "/restore",
                method: "POST",
                data: {email: email},
                dataType: "json",
                success: function (result) {
                    spop({
                        template: 'На Ваш email було відправлено новий пароль для входу в систему.',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Перевірте будь-ласка введений email.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveUserOrder: function() {
            var defer = jQuery.Deferred();

            defer.then(function(){
                var ordersData = [],
                    currentDate = new Date(),
                    dayDate,
                    day;

                currentDate.setDate(currentDate.getDate() - 1);

                $( ".order-cell" ).each(function(  ) {
                    day = $(this).attr('data-day');
                    dayDate = new Date(day);
                    if (dayDate.getTime() >= currentDate.getTime()) {
                        ordersData.push({
                            day: day,
                            group_id: $(this).attr('data-group-id'),
                            dessert: $(this).attr('data-dessert'),
                            count: $(this).val()
                        });
                    }
                });

                return ordersData;
            }).then(function(ordersData) {
                if (ordersData.length) {
                    $.ajax({
                        url: "/api/v1/orders",
                        method: "POST",
                        data: JSON.stringify(ordersData),
                        dataType: "json",
                        success: function (result) {
                            $(location).attr('href', location.href);
                        },
                        error: function (error) {
                            spop({
                                template: 'Помилка :( Перевірте будь-ласка ваше замовлення.',
                                position  : 'top-left',
                                style: 'error',
                                autoclose: 6000
                            });
                        }
                    });
                }
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
                        calories = $(this).children( "th" ).children("input[name='calories']").val(),
                        weight = parseFloat($(this).children( "th" ).children("input[name='weight']").val()),
                        groupId = $(this).attr('data-group-id'),
                        tmpId = $(this).attr('data-tmp-id'),
                        dishId = $(this).attr('data-dish-id');

                    dishesData.push({
                        id: dishId || null,
                        name: name,
                        price: isNaN(price) ? 0 : price,
                        description: description,
                        ingredients: ingredients,
                        calories: calories,
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
                    $(location).attr('href', '/dishes');
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося зберегти зміни! Перевірте будь-ласка правильність даних в змінених і доданих стравах.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
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
                        template: 'Не вдалося видалити страву з меню, хтось вже замовив цю страву.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
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
                    var menuRow = $('tr[data-dish-id="' + dish.dish_id + '"]');
                    menuRow.attr('data-menu-id', dish.id);
                    callback();
                    
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося додати страву в меню!',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        removeDish: function(dishId) {
            $.ajax({
                url: "/api/v1/dishes/" + dishId,
                method: "DELETE",
                contentType:'application/json',
                success: function (dish) {
                    $('tr[data-dish-id="' + dishId + '"]').remove();
                    spop({
                        template: 'Страва успішно видалена!',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося видалити страву, тому що вона додана в меню.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        getDish: function(dishId) {
            $.ajax({
                url: "/api/v1/dishes/" + dishId + "?included[]=reviews_count&included[]=rating",
                method: "GET",
                contentType:'application/json',
                success: function (dish) {
                    $("#modal-dish-name").html(dish.name);
                    $("#modal-dish-image").attr('src', '/views/assets/dishes/' + dishId + '.jpg');
                    $("#modal-dish-description").html(dish.description);
                    $("#modal-dish-reviews-count").html(dish.reviews_count);
                    var rating = "";
                    for (var i = 1; i <= 5; i++) {
                        if (dish.rating >= i) {
                            rating = '<span style="color: #FFA33E;">&#9733;</span>' + rating;
                        } else {
                            rating = '<span>&#9734;</span>' + rating;
                        }
                    }
                    $("#modal-dish-rating").html(rating);
                    $("#myModal").modal();
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Не вдалося знайти вибрану страву.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        getDishFeedback: function(dishId) {
            $.ajax({
                url: "/api/v1/feedback/dish/" + dishId,
                method: "GET",
                contentType:'application/json',
                success: function (feedback) {
                    var feedbackHtml = "";
                    for (var item in feedback) {
                        if (feedback.hasOwnProperty(item)) {
                            var data = feedback[item];
                            var created = data.created.split(' ');
                            feedbackHtml += 
                                '<div class="col-md-12 text-left" style="padding-top: 10px; border: 1px solid #eee; background-color: #f9d1ae1a; margin-top: 10px">' +
                                    '<b>' + data.user_name + '</b> | <span>' + created[0] + '</span>' +
                                    '<p id="modal-dish-description" style="font-size: 12px; margin: 0 0 10px 0; line-height: 16px">'
                                    + data.text +
                                    '</p>' + 
                                '</div>';
                        }
                    }
                    if (feedbackHtml) {
                        $("#modal-dish-feedback").html(feedbackHtml);
                    } else {
                        $("#modal-dish-feedback").html('Відгуків не знайдено.');
                    }
                    $("#myModal").modal();
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Не вдалося знайти відгуки для вибраної страви.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        getDishRatings: function(dishId) {
            $.ajax({
                url: "/api/v1/rating/dish/" + dishId,
                method: "GET",
                contentType:'application/json',
                success: function (ratings) {
                    var ratingHtml = "";
                    for (var mark in ratings) {
                        if (ratings.hasOwnProperty(mark)) {
                            var votes = ratings[mark];
                            console.log(votes);
                            console.log(mark);
                            ratingHtml += votes + ' - ';
                            for (var i = 0; i < mark; i++) {
                                ratingHtml += '<span style="color: #FFA33E;">★</span>';
                            }
                            ratingHtml += '<br/>';
                        }
                    }
                    if (ratingHtml) {
                        $("#modal-dish-feedback").html(
                            '<div class="col-md-12 text-left" style="padding-top: 10px; border: 1px solid #eee; background-color: #f9d1ae1a; margin-top: 10px">' +
                                '<p id="modal-dish-description" style="font-size: 12px; margin: 0 0 10px 0; line-height: 16px">' +
                                    ratingHtml + 
                                '</p>' +
                            '</div>'
                        );
                    } else {
                        $("#modal-dish-feedback").html('Оцінок не знайдено.');
                    }
                    $("#myModal").modal();
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Не вдалося знайти відгуки для вибраної страви.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });

        },

        saveUserPersonalData: function (userData) {
            $.ajax({
                url: "/api/v1/users/current",
                method: "POST",
                data: JSON.stringify(userData),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    if (resp.token) {
                        setCookie('X-AUTH-TOKEN', resp.token, 365 * 5);   
                    }
                    spop({
                        template: 'Ваші персональні дані успішно збережені!',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при збереженні :( Перевірте будь-ласка правильність введених даних.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveUser: function (user) {
            $.ajax({
                url: "/api/v1/users",
                method: "POST",
                data: JSON.stringify(user),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    spop({
                        template: 'Дані успішно збережені!',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                    setTimeout(function() { $(location).attr('href', '/users'); }, 1000);
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при збереженні :( Перевірте будь-ласка правильність введених даних.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveFeedback: function (feedback) {
            $.ajax({
                url: "/api/v1/feedback",
                method: "POST",
                data: JSON.stringify(feedback),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    $(location).attr('href', '/profile/feedback');
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при відправленні Ваших відгуків :( Перепрошуємо за тимчасові незручності.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveRating: function (rating) {
            $.ajax({
                url: "/api/v1/ratings",
                method: "POST",
                data: JSON.stringify(rating),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {},
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при збереженні оцінки страви :( Перепрошуємо за тимчасові незручності.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveDishGroup: function (dishGroup) {
            $.ajax({
                url: "/api/v1/dishgroups",
                method: "POST",
                data: JSON.stringify(dishGroup),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    $(location).attr('href', '/dishes');
                },
                error: function (error) {
                    spop({
                        template: 'Помилка при збереженні :( Превірте будь-ласка назву групи.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        updateDishGroup: function (dishGroup) {
            $.ajax({
                url: "/api/v1/dishgroups/" + dishGroup.id,
                method: "PUT",
                data: JSON.stringify(dishGroup),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {},
                error: function (error) {
                    spop({
                        template: 'Помилка при збереженні :( Превірте будь-ласка назву групи страв.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        saveCompany: function (company) {
            $.ajax({
                url: "/api/v1/companies",
                method: "POST",
                data: JSON.stringify(company),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    $(location).attr('href', '/companies');
                },
                error: function (error) {
                    spop({
                        template: 'Помилка при збереженні :( Превірте будь-ласка назву компанії.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        updateCompany: function (company) {
            $.ajax({
                url: "/api/v1/companies/" + company.id,
                method: "PUT",
                data: JSON.stringify({name: company.name}),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {},
                error: function (error) {
                    spop({
                        template: 'Помилка :( Превірте будь-ласка введені дані.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        updateOffices: function (offices) {
            $.ajax({
                url: "/api/v1/offices",
                method: "POST",
                data: JSON.stringify(offices),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    $(location).attr('href', '/companies');
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Превірте будь-ласка введені дані.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        updateUserEmail: function (id, email) {
            $.ajax({
                url: "/api/v1/users/" + id,
                method: "PUT",
                data: JSON.stringify({email: email}),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    var actions = $(".user-actions-" + id);
                    actions.css('display', 'none');
                    actions.find(".user-email-actions-confirm").attr('value', email);
                    spop({
                        template: 'Email користувача успішно оновлений!',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Превірте будь-ласка введені дані.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });
        },

        deactivateUser: function(id) {
            $.ajax({
                url: "/api/v1/users/" + id,
                method: "DELETE",
                contentType:'application/json',
                success: function (dish) {},
                error: function (error) {}
            });
        },

        saveSettings: function(settings) {
            $.ajax({
                url: "/api/v1/settings",
                method: "POST",
                data: JSON.stringify(settings),
                dataType: "json",
                contentType:'application/json',
                success: function (resp) {
                    spop({
                        template: 'Налаштування успішно збережені!',
                        position  : 'top-left',
                        style: 'success',
                        autoclose: 6000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Помилка :( Превірте будь-ласка введені дані.',
                        position  : 'top-left',
                        style: 'error',
                        autoclose: 6000
                    });
                }
            });  
        }
    }

    // SHOW DISH
    $(".user .dish-link").click(function(){
        var dishId = $(this).attr('data-link-dish-id');
        API.getDish(dishId);
    });
    
    //LOGIN

    $('.login-form input[type="text"], .login-form input[type="password"], .login-form input[type="first_name"], .login-form input[type="last_name"]').on('focus', function() {
        $(this).removeClass('input-error');
    });

    $("#login-btn").click(function(){

        var isError = false;
        $('.login-form').find('input[type="text"], input[type="password"]').each(function(){
            if ( $(this).val() == "" ) {
                isError = true;
                $(this).addClass('input-error');
            }
            else {
                $(this).removeClass('input-error');
            }
        });

        if (!isError) {
            API.login({
                email: $("#form-username").val(),
                password: $("#form-password").val()
            });
        }

        return false;
    });
    
    // REGISTER
    $("#register-btn").click(function(){
        
        var isError = false;
        $('.login-form').find('input[type="text"], input[type="password"], input[type="first_name"], input[type="last_name"]').each(function(){
            if ( $(this).val() == "" ) {
                isError = true;
                $(this).addClass('input-error');
            }
            else {
                $(this).removeClass('input-error');
            }
        });

        if (!isError) {
            API.register({
                email: $("#form-username").val(),
                password: $("#form-password").val(),
                first_name: $("#form-first-name").val(),
                last_name: $("#form-last-name").val(),
                cid: $("#form-cid").val(),
                ipn: $("#form-ipn").val()
            });
        }

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
        var newGroupHtml = '<tr class="group-' + groupId + '" data-dish-id="" data-group-id="' + groupId + '" data-tmp-id="' + Math.floor(Math.random() * 1000001) +'">' +
                '<th class="wh-name">' + 
                //   '<img data-dish-image="" data-dish-image-name="sdasdas" src="" class="dish-thumbnail">' +
                  '<input type="text" name="name" value="" placeholder="Введіть ім\'я" />' + 
                '</th>' +
                '<th><input name="description" type="text" value="" placeholder="Опис.."/></th>' +
                '<th><input name="ingredients" type="text" value="" placeholder="Інгредієнти.."/></th>' +
                '<th width="5%"><input name="calories" type="number" value="" placeholder="Калорійність.."/></th>' +
                '<th width="5%"><input name="weight" type="number" value="" placeholder="Вага.."/></th>' +
                '<th width="5%"><input name="price" type="number" value="" placeholder="Ціна.."/></th>' +
                '<th width="3%">' +
                '<a href="#" class="btn btn-danger btn-xs remove-dish" style="padding: 3px 10px 3px 10px; text-transform: lowercase">x</a>' +
                '</th>' +
            '</tr>';

        
        if ($(".group-" + groupId).length) {
            $(".group-" + groupId).last().after(newGroupHtml);
        } else {
            $(this).parent().parent().after(newGroupHtml);
        }

        return false;
    });

    $(".add-office").click(function () {
        var companyId = $(this).attr('data-company-id');
        var newOfficeHtml = '<tr data-office-id="" data-office-company-id="' + companyId + '">' +
            '<th class="wh-name"><input class="office-address" value="" placeholder="Введіть адресу офісу.." type="text"></th>' +
            '<th style="opacity: 0.45">Посилання на реєстрацію буде згенероване автоматично після збереження змін</th>' +
        '</tr>';

        if ($(".company-" + companyId).length) {
            $(".company-" + companyId).last().after(newOfficeHtml);
        } else {
            $(this).parent().parent().after(newOfficeHtml);
        }

        return false;
    });

    //SAVE DISHES
    $("#save-dishes").click(function(){
        API.saveDishes();
        return false;
    });

    $(".menu-action .switch").click(function(event) {
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
                start: $(".date-range").attr('data-start'),
                end: $(".date-range").attr('data-end'),
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

    $("body").on("click", ".remove-dish", function(){
        var dishId = $(this).parent().parent().attr('data-dish-id');
        if (dishId) {
            API.removeDish(dishId);
        } else {
            $(this).parent().parent().remove();
        }
        return false;
    });      

    $("#save-companies").click(function(){
        var offices = [];
        $(".office-address").each(function () {
            offices.push({
                id: $(this).parent().parent().attr('data-office-id'),
                address: $(this).val(),
                company_id: $(this).parent().parent().attr('data-office-company-id')
            });
        });

        API.updateOffices(offices);
        return false;
    });

    $("#save-user-personal-data").click(function () {
        API.saveUserPersonalData({
            first_name: $("#user-first-name").val(),
            last_name: $("#user-last-name").val(),
            email: $("#user-email").val()
        });
        return false;
    });

    $("#save-feedbacks").click(function () {
        var feedback = [];
        $( ".dish-feedback-text" ).each(function() {
            feedback.push({
                dish_id: $(this).attr('data-feedback-dish-id'),
                text: $(this).val()
            });
        });

        API.saveFeedback(feedback);
    });

    $(".rating-feedback").click(function () {
        var currentMark, 
            parent = $(this).parent(),
            ratingMark = parseInt($(this).attr('data-rating-mark')),
            defer = jQuery.Deferred();

        defer.then(function(){
            API.saveRating({
                mark: ratingMark,
                dish_id: parent.attr('data-rating-dish-id')
            });
        }).then(function() {
            parent.find(".rating-feedback").each(function () {
                currentMark = parseInt($(this).attr('data-rating-mark'));
                if (currentMark > ratingMark) {
                    $(this).html('&#9734;');
                    $(this).css('color', '#8b8e94');
                } else {
                    $(this).html('&#9733;');
                    $(this).css('color', '#FFA33E');
                }
            });
        });
        
        defer.resolve();
    });

    $("body").on("click", ".dish-thumbnail", function(){
        var dishId = $(this).attr('data-dish-image');
        $("#dish-image-crop-header").attr('data-crop-popup-dish-id', dishId);
        $("#dish-image-crop-header").html($(this).attr('data-dish-image-name'));
        $("#views").css("display", "none");
        $(".crop-image-placeholder").css("display", "block");
        $("#cropModal").modal();
    });

    /*===== CROP START =====*/
    var crop_max_width = 500;
    var crop_max_height = 500;
    var jcrop_api;
    var canvas;
    var context;
    var image;
    var prefsize;

    $("#file").change(function() {
      $("#views").css("display", "block");
      loadImage(this);
    });

    $("#choose-file").click(function() {
      $("#file").click();
      $(".crop-image-placeholder").hide(0);
    });

    function loadImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        canvas = null;
        reader.onload = function(e) {
          image = new Image();
          image.onload = validateImage;
          image.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
      }
    }

    function dataURLtoBlob(dataURL) {
      var BASE64_MARKER = ';base64,';
      if (dataURL.indexOf(BASE64_MARKER) == -1) {
        var parts = dataURL.split(',');
        var contentType = parts[0].split(':')[1];
        var raw = decodeURIComponent(parts[1]);

        return new Blob([raw], {
          type: contentType
        });
      }
      var parts = dataURL.split(BASE64_MARKER);
      var contentType = parts[0].split(':')[1];
      var raw = window.atob(parts[1]);
      var rawLength = raw.length;
      var uInt8Array = new Uint8Array(rawLength);
      for (var i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
      }

      return new Blob([uInt8Array], {
        type: contentType
      });
    }

    function validateImage() {
      if (canvas != null) {
        image = new Image();
        image.onload = restartJcrop;
        image.src = canvas.toDataURL('image/png');
      } else restartJcrop();
    }

    function restartJcrop() {
      if (jcrop_api != null) {
        jcrop_api.destroy();
      }
      $("#views").empty();
      $("#views").append("<canvas id=\"canvas\">");
      canvas = $("#canvas")[0];
      context = canvas.getContext("2d");
      canvas.width = image.width;
      canvas.height = image.height;
      context.drawImage(image, 0, 0);
      $("#canvas").Jcrop({
        onSelect: selectcanvas,
        onRelease: clearcanvas,
        boxWidth: crop_max_width,
        boxHeight: crop_max_height,
        aspectRatio: 1
        // allowResize: false,
        // allowSelect: false
      }, function() {
        jcrop_api = this;
        // jcrop_api.animateTo([0,0,338,338]);
      });
      clearcanvas();
    }

    function clearcanvas() {
      prefsize = {
        x: 0,
        y: 0,
        w: canvas.width,
        h: canvas.height,
      };
    }

    function selectcanvas(coords) {
      prefsize = {
        x: Math.round(coords.x),
        y: Math.round(coords.y),
        w: Math.round(coords.w),
        h: Math.round(coords.h)
      };
    }

    function applyCrop() {
        canvas.width = prefsize.w;
        canvas.height = prefsize.h;
        context.drawImage(image, prefsize.x, prefsize.y, prefsize.w, prefsize.h, 0, 0, canvas.width, canvas.height);
        validateImage();
    }

    $("#cropbutton").click(function(e) {
      applyCrop();
    });

    $("#crop-dish-image-form").submit(function(e) {
      e.preventDefault();
      formData = new FormData($(this)[0]);
      var blob = dataURLtoBlob(canvas.toDataURL('image/jpg'));
      var dishId = $("#dish-image-crop-header").attr('data-crop-popup-dish-id');
      //---Add file blob to the form data
      formData.append("cropped_image", blob);
      $.ajax({
        url: "/api/v1/dishes/upload/" + dishId,
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            var dishImage = $('img[data-dish-image="' + dishId + '"]');
            var imageSrc = dishImage.attr('src');
            var d = new Date();
            dishImage.removeAttr('src').attr('src', imageSrc + '?' + d.getTime());
            
            spop({
                template: 'Зображення страви успішно оновлене!',
                position  : 'top-left',
                style: 'success',
                autoclose: 6000
            });
        },
        error: function(data) {
            spop({
                template: 'Не вдалося оновити зображення страви :( Перевірте будь-ласка формат зображення.',
                position  : 'top-left',
                style: 'error',
                autoclose: 6000
            });
        },
        complete: function(data) {}
      });
    });

    $("#save-cropped-image").click(function() {
        $("#crop-dish-image-form").submit();
    });

    /*===== CROP END =====*/

    $(".infolink").hover(
        function () {
            $(this).parent().parent().find(".infolink-description").css('display', 'block');
        },
        function () {
            $(this).parent().parent().find(".infolink-description").css('display', 'none');
        }
    );

    $(".infolink-total-price").hover(
        function () {
            $(this).parent().parent().find(".total-price-infolink-description").css('display', 'block');
        },
        function () {
            $(this).parent().parent().find(".total-price-infolink-description").css('display', 'none');
        }
    );

    $("#save-new-dish-group").click(function () {
        var dishGroup = $(".new-group-popup-input").val();
        API.saveDishGroup({
            name: dishGroup
        });
    });

    $("#save-new-company").click(function () {
        var company = $(".new-group-popup-input").val();
        API.saveCompany({
            name: company
        });
    });

    $(document).on('change','.dish-group-name', function() {
        var groupId = $(this).attr('data-dish-group-name-id');
        var groupName = $(this).val();
        API.updateDishGroup({
            id: groupId,
            name: groupName
        });
   }).change();


   $(document).on('change','.company-group-name', function() {
        var companyId = $(this).attr('data-company-group-name-id');
        var companyName = $(this).val();
        API.updateCompany({
            id: companyId,
            name: companyName
        });
    }).change();

    $(".user-email-actions-confirm").click(function () {
        var input = $(this).parent().parent().find(".user-name-input");
        API.updateUserEmail($(this).attr('data-confirm-user-id'), input.val());
    });

    $(".users-action-switch .switch").click(function(event) {
        var checkbox = $(this).children('input');
        var userId = $(this).attr('data-deactivate-user-id');
        
        if (checkbox.is(':checked')) {
            checkbox.prop('checked', false);
        } else {
            checkbox.prop('checked', true);
        }

        API.deactivateUser(userId);

        return false;
    });

    $('.disabled-cell').on('mousemove', function (e) {
        var tooltip = $(this).find('.orders-not-allowed-tooltip');
        for (var i=tooltip.length; i--;) {
            tooltip[i].style.left = e.pageX + 'px';
            tooltip[i].style.top = e.pageY + 'px';
        }
    });

    $('#save-system-settings').click(function () {
        var settings = {};
        $('.settings-input').each(function () {
            var name = $(this).attr('name');
            settings[name] = $(this).val();
        });
        API.saveSettings(settings);
    });

    $('.feedback-link').click(function () {
        var dishId = $(this).attr('data-dish-id'),
            dishImage = $(this).attr('data-dish-image'),
            dishName = $(this).attr('data-dish-name');

        $('#modal-dish-image').attr('src', dishImage);
        $('#modal-dish-name').html(dishName);

        API.getDishFeedback(dishId);
    });

    $('.rating-link').click(function () {
        var dishId = $(this).attr('data-dish-id'),
            dishImage = $(this).attr('data-dish-image'),
            dishName = $(this).attr('data-dish-name');

        $('#modal-dish-image').attr('src', dishImage);
        $('#modal-dish-name').html(dishName);

        API.getDishRatings(dishId);
    });

    $('#add-new-admin-moder').click(function () {
        $('#users-stuff-tab').click();
        $('.new-moder-admin-row').css('display', 'table-row');
    });

    $(".moder-admin-actions-confirm").click(function () {
        var user = {
            first_name: $('.moder-admin-first-name').val(),
            last_name: $('.moder-admin-last-name').val(),
            email: $('.moder-admin-email').val(),
            role: $('.moder-admin-role').val(),
            pass: $('.moder-admin-pass').val()
        };

        API.saveUser(user);

    });

    $("#select-export-type").change(function() {
        var selectedType = $(this).val();
        if (selectedType === 'period') {
            $("#export-selected-period-button").css('display', 'block');
            $("#export-nextday-button").css('display', 'none');
        } else {
            $("#export-selected-period-button").css('display', 'none');
            $("#export-nextday-button").css('display', 'block');
        }
    });

    $("#restore-pass-btn").click(function () {
        var email = $("#restore-email-input").val();
        API.restoreEmail(email);
        return false;
    });

    $(".order-cell").focus(function () {
        var groupId = $(this).attr('data-group-id'),
            groupDay = $(this).attr('data-day');

        $('.dessert-hint').css('display', 'none');
        $('.hint-' + groupId + '-' + groupDay).css('display', 'block');

        return false;
    });

    $(".dessert-hint-option").click(function () {
        var self = this;
        var dessert = parseInt($(this).val());

        $(this).parent().parent().find('.order-cell').attr('data-dessert', dessert);

        setTimeout(function () {
            $(self).parent().css('display', 'none');
        }, 150);
    });

    $(".dessert-option-label").click(function () {
        var self = this;
        var radioBtnId = $(this).attr('data-radio-id');

        $(this).parent().find('.' + radioBtnId +'-radio').click();
    });

    $("#export-option").click(function () {
        var option = $(this).val();
        
        $('#export-selected-period-button').css('display', 'none');
        $('#export-weekly-button').css('display', 'none');
        $('#export-monthly-button').css('display', 'none');
        $('#export-month-block').css('display', 'none');

        $('#' + option + '-button').css('display', 'block');

        if (option === 'export-monthly') {
            $('#export-month-block').css('display', 'block');
        }
    });

    $("#export-month").change(function () {
        console.log($(this).val());
        $('#export-monthly-button-input').attr('href', '/export/monthly?month=' + $(this).val());
    });

});   