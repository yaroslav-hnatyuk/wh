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
                        day: $(this).attr('data-day'),
                        menu_dish_id: $(this).attr('data-menu-id'),
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


        saveUserOrderFromPopup: function() {
            var defer = jQuery.Deferred();

            defer.then(function(){
                var ordersData = [];
                $( ".modal-order-cell" ).each(function(  ) {
                    ordersData.push({
                        day: $(this).attr('data-modal-dish-day'),
                        menu_dish_id: $(this).attr('data-modal-menu-id'),
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
                        for (const index in ordersData) {
                            if (ordersData.hasOwnProperty(index)) {
                                const order = ordersData[index];
                                $('.order-cell[data-menu-id="' + order.menu_dish_id + '"][data-day="' + order.day + '"]').val(order.count);
                            }
                        }
                        $("#myModal").modal('hide');
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
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Не вдалося видалити страву!',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
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
                    $("#modal-dish-description").html(dish.description);
                    $("#modal-dish-reviews-count").html(dish.reviews_count);
                    let rating = "";
                    for (let i = 1; i <= 5; i++) {
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
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
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
                success: function (user) {
                    console.log(user);
                    spop({
                        template: 'Ваші персональні дані успішно збережені!',
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при збереженні :( Перевірте будь-ласка правильність введених даних.',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
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
                    console.log(resp);
                    spop({
                        template: "Ваші відгуки успішно відправлені, ми обов'язково приймемо їх до уваги! Дякуємо за співпрацю! :)",
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при відправленні Ваших відгуків :( Перепрошуємо за тимчасові незручності.',
                        position  : 'bottom-right',
                        style: 'error',
                        autoclose: 3000
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
                success: function (resp) {
                    console.log(resp);
                    spop({
                        template: "Ваша очінка страви збережена! Дякуємо за співпрацю! :)",
                        position  : 'bottom-right',
                        style: 'success',
                        autoclose: 3000
                    });
                },
                error: function (error) {
                    spop({
                        template: 'Сталася помилка при збереженні оцінки страви :( Перепрошуємо за тимчасові незручності.',
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


    // SHOW DISH
    $(".dish-link").click(function(){
        var dishId = $(this).attr('data-link-dish-id');

        $(this).parent().parent().parent().find(".order-cell").each(function(  ) {
            let day = $(this).attr('data-day');
            let menuId = $(this).attr('data-menu-id');
            let count = $(this).val();
            $('input[data-modal-dish-day="' + day + '"]').val(count);
            $('input[data-modal-dish-day="' + day + '"]').attr('data-modal-menu-id', menuId);
        });

        API.getDish(dishId);
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

    $("#save-order-popup").click(function(){
        API.saveUserOrderFromPopup();
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
                '<th width="3%">' +
                '<a href="#" class="btn btn-danger btn-xs remove-dish" style="padding: 3px 10px 3px 10px; text-transform: lowercase">x</a>' +
                '</th>' +
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
        // TODO fixme
        spop({
            template: 'Компанії успішно збережені!',
            position  : 'bottom-right',
            style: 'success',
            autoclose: 3000
        });
        return false;
    });

    $("#save-users").click(function(){
        // TODO fixme
        spop({
            template: 'Користувачі успішно збережені!',
            position  : 'bottom-right',
            style: 'success',
            autoclose: 3000
        });
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

    $(".dish-thumbnail").click(function () {
        $("#cropModal").modal();
    });

    /*===== CROP START =====*/
    var crop_max_width = 400;
    var crop_max_height = 400;
    var jcrop_api;
    var canvas;
    var context;
    var image;

    var prefsize;

    $("#file").change(function() {
      loadImage(this);
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
        allowResize: false,
        allowSelect: false
      }, function() {
        jcrop_api = this;
        jcrop_api.animateTo([0,0,338,338]);
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

    $("#form").submit(function(e) {
      e.preventDefault();
      formData = new FormData($(this)[0]);
      var blob = dataURLtoBlob(canvas.toDataURL('image/jpg'));
      //---Add file blob to the form data
      formData.append("cropped_image", blob);
      $.ajax({
        url: "/api/v1/dishes/upload/1",
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
          alert("Success");
        },
        error: function(data) {
          alert("Error");
        },
        complete: function(data) {}
      });
    });

    /*===== CROP END =====*/

});   