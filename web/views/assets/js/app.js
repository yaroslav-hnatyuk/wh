$(document).ready(function(){var e=function(t){$.ajax({url:"/login",method:"POST",data:t,dataType:"json",success:function(t){"OK"===t.status?(b("X-AUTH-TOKEN",t.token,1825),$(location).attr("href","/order")):spop({template:"Помилка :( Перевірте будь-ласка ваш email та пароль.",position:"top-left",style:"error",autoclose:6e3})},error:function(t){spop({template:"Помилка :( Перевірте будь-ласка ваш email та пароль.",position:"top-left",style:"error",autoclose:6e3})}})},a=function(t){$.ajax({url:"/register",method:"POST",data:t,dataType:"json",success:function(t){"OK"===t.status?(b("X-AUTH-TOKEN",t.token,1825),$(location).attr("href","/order")):spop({template:"Сталася помилка :( Перевірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})},error:function(t){t.responseJSON&&"ERROR"===t.responseJSON.status&&t.responseJSON.message?spop({template:t.responseJSON.message,position:"top-left",style:"error",autoclose:6e3}):spop({template:"Помилка :( Перевірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})}})},t=function(){var t=jQuery.Deferred();t.then(function(){var t,e=[],a=new Date;return a.setDate(a.getDate()-1),$(".order-cell").each(function(){t=$(this).attr("data-day"),new Date(t).getTime()>=a.getTime()&&e.push({day:t,menu_dish_id:$(this).attr("data-menu-id"),count:$(this).val()})}),e}).then(function(t){t.length&&$.ajax({url:"/api/v1/orders",method:"POST",data:JSON.stringify(t),dataType:"json",success:function(t){$(location).attr("href",location.href)},error:function(t){spop({template:"Помилка :( Перевірте будь-ласка ваше замовлення.",position:"top-left",style:"error",autoclose:6e3})}})}),t.resolve()},i=function(){var t=jQuery.Deferred();t.then(function(){var t,e=[],a=new Date;return a.setDate(a.getDate()-1),$(".modal-order-cell").each(function(){t=$(this).attr("data-modal-dish-day"),new Date(t).getTime()>=a.getTime()&&e.push({day:t,menu_dish_id:$(this).attr("data-modal-menu-id"),count:$(this).val()})}),e}).then(function(t){$.ajax({url:"/api/v1/orders",method:"POST",data:JSON.stringify(t),dataType:"json",success:function(t){$("#myModal").modal("hide"),$(location).attr("href",location.href)},error:function(t){spop({template:"Помилка :( Перевірте будь-ласка ваше замовлення.",position:"top-left",style:"error",autoclose:6e3})}})}),t.resolve()},n=function(){var l=[];$(".wh-table tr").each(function(){var t=$(this).attr("class");if(void 0!==t&&t.startsWith("group")){var e=$(this).children("th").children("input[name='name']").val(),a=parseFloat($(this).children("th").children("input[name='price']").val()),i=$(this).children("th").children("input[name='description']").val(),n=$(this).children("th").children("input[name='ingredients']").val(),o=$(this).children("th").children("input[name='calories']").val(),s=parseFloat($(this).children("th").children("input[name='weight']").val()),r=$(this).attr("data-group-id"),c=$(this).attr("data-tmp-id"),p=$(this).attr("data-dish-id");l.push({id:p||null,name:e,price:isNaN(a)?0:a,description:i,ingredients:n,calories:o,weight:isNaN(s)?0:s,dish_group_id:r,tmp_id:c||null})}}),$.ajax({url:"/api/v1/dishes",method:"POST",data:JSON.stringify(l),dataType:"json",success:function(t){$(location).attr("href","/dishes")},error:function(t){spop({template:"Не вдалося зберегти зміни! Перевірте будь-ласка правильність даних в змінених і доданих стравах.",position:"top-left",style:"error",autoclose:6e3})}})},o=function(t,a){$.ajax({url:"/api/v1/menudishes/"+t,method:"DELETE",contentType:"application/json",success:function(t){var e=$('tr[data-dish-id="'+t.dish_id+'"]');e.attr("data-menu-id",t.id),e.attr("data-menu-id",""),a()},error:function(t){spop({template:"Не вдалося видалити страву з меню, хтось вже замовив цю страву.",position:"top-left",style:"error",autoclose:6e3})}})},s=function(t,e){$.ajax({url:"/api/v1/menudishes",method:"POST",data:JSON.stringify(t),dataType:"json",success:function(t){$('tr[data-dish-id="'+t.dish_id+'"]').attr("data-menu-id",t.id),e()},error:function(t){spop({template:"Не вдалося додати страву в меню!",position:"top-left",style:"error",autoclose:6e3})}})},r=function(e){$.ajax({url:"/api/v1/dishes/"+e,method:"DELETE",contentType:"application/json",success:function(t){$('tr[data-dish-id="'+e+'"]').remove(),spop({template:"Страва успішно видалена!",position:"top-left",style:"success",autoclose:6e3})},error:function(t){spop({template:"Не вдалося видалити страву, тому що вона додана в меню.",position:"top-left",style:"error",autoclose:6e3})}})},c=function(i){$.ajax({url:"/api/v1/dishes/"+i+"?included[]=reviews_count&included[]=rating",method:"GET",contentType:"application/json",success:function(t){$("#modal-dish-name").html(t.name),$("#modal-dish-image").attr("src","/views/assets/dishes/"+i+".jpg"),$("#modal-dish-description").html(t.description),$("#modal-dish-reviews-count").html(t.reviews_count);for(var e="",a=1;a<=5;a++)e=t.rating>=a?'<span style="color: #FFA33E;">&#9733;</span>'+e:"<span>&#9734;</span>"+e;$("#modal-dish-rating").html(e),$("#myModal").modal()},error:function(t){spop({template:"Помилка :( Не вдалося знайти вибрану страву.",position:"top-left",style:"error",autoclose:6e3})}})},p=function(t){$.ajax({url:"/api/v1/feedback/dish/"+t,method:"GET",contentType:"application/json",success:function(t){var e="";for(var a in t)if(t.hasOwnProperty(a)){var i=t[a],n=i.created.split(" ");e+='<div class="col-md-12 text-left" style="padding-top: 10px; border: 1px solid #eee; background-color: #f9d1ae1a; margin-top: 10px"><b>'+i.user_name+"</b> | <span>"+n[0]+'</span><p id="modal-dish-description" style="font-size: 12px; margin: 0 0 10px 0; line-height: 16px">'+i.text+"</p></div>"}e?$("#modal-dish-feedback").html(e):$("#modal-dish-feedback").html("Фідбеків не знайдено."),$("#myModal").modal()},error:function(t){spop({template:"Помилка :( Не вдалося знайти відгуки для вибраної страви.",position:"top-left",style:"error",autoclose:6e3})}})},l=function(t){$.ajax({url:"/api/v1/users/current",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){t.token&&b("X-AUTH-TOKEN",t.token,1825),spop({template:"Ваші персональні дані успішно збережені!",position:"top-left",style:"success",autoclose:6e3})},error:function(t){spop({template:"Сталася помилка при збереженні :( Перевірте будь-ласка правильність введених даних.",position:"top-left",style:"error",autoclose:6e3})}})},d=function(t){$.ajax({url:"/api/v1/feedback",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){$(location).attr("href","/profile/feedback")},error:function(t){spop({template:"Сталася помилка при відправленні Ваших відгуків :( Перепрошуємо за тимчасові незручності.",position:"top-left",style:"error",autoclose:6e3})}})},u=function(t){$.ajax({url:"/api/v1/ratings",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){},error:function(t){spop({template:"Сталася помилка при збереженні оцінки страви :( Перепрошуємо за тимчасові незручності.",position:"top-left",style:"error",autoclose:6e3})}})},h=function(t){$.ajax({url:"/api/v1/dishgroups",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){$(location).attr("href","/dishes")},error:function(t){spop({template:"Помилка при збереженні :( Превірте будь-ласка назву групи.",position:"top-left",style:"error",autoclose:6e3})}})},f=function(t){$.ajax({url:"/api/v1/dishgroups/"+t.id,method:"PUT",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){},error:function(t){spop({template:"Помилка при збереженні :( Превірте будь-ласка назву групи страв.",position:"top-left",style:"error",autoclose:6e3})}})},m=function(t){$.ajax({url:"/api/v1/companies",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){$(location).attr("href","/companies")},error:function(t){spop({template:"Помилка при збереженні :( Превірте будь-ласка назву компанії.",position:"top-left",style:"error",autoclose:6e3})}})},y=function(t){$.ajax({url:"/api/v1/companies/"+t.id,method:"PUT",data:JSON.stringify({name:t.name}),dataType:"json",contentType:"application/json",success:function(t){},error:function(t){spop({template:"Помилка :( Превірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})}})},v=function(t){$.ajax({url:"/api/v1/offices",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){$(location).attr("href","/companies")},error:function(t){spop({template:"Помилка :( Превірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})}})},g=function(a,i){$.ajax({url:"/api/v1/users/"+a,method:"PUT",data:JSON.stringify({email:i}),dataType:"json",contentType:"application/json",success:function(t){var e=$(".user-actions-"+a);e.css("display","none"),e.find(".user-email-actions-confirm").attr("value",i),spop({template:"Email користувача успішно оновлений!",position:"top-left",style:"success",autoclose:6e3})},error:function(t){spop({template:"Помилка :( Превірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})}})},k=function(t){$.ajax({url:"/api/v1/users/"+t,method:"DELETE",contentType:"application/json",success:function(t){},error:function(t){}})},w=function(t){$.ajax({url:"/api/v1/settings",method:"POST",data:JSON.stringify(t),dataType:"json",contentType:"application/json",success:function(t){spop({template:"Налаштування успішно збережені!",position:"top-left",style:"success",autoclose:6e3})},error:function(t){spop({template:"Помилка :( Превірте будь-ласка введені дані.",position:"top-left",style:"error",autoclose:6e3})}})};function b(t,e,a){var i=new Date;i.setTime(i.getTime()+24*a*60*60*1e3);var n="expires="+i.toGMTString();document.cookie=t+"="+e+";"+n+";path=/"}$(".group-header").click(function(){var t=$(this).attr("data-group-id");$(".group-"+t).toggle()}),$(".user .dish-link").click(function(){var t=$(this).attr("data-link-dish-id"),i=0,n=0;$(this).parent().parent().parent().find(".order-cell").each(function(){var t=$(this).attr("data-day"),e=$(this).attr("data-menu-id"),a=$(this).val();$('input[data-modal-dish-day="'+t+'"]').val(a),$('input[data-modal-dish-day="'+t+'"]').attr("data-modal-menu-id",e),i++,$(this).attr("disabled")&&n++}),n===i?$("#save-order-popup").css("display","none"):$("#save-order-popup").css("display","inline-block"),c(t)}),$(".dish-list tr th input").keydown(function(t){-1!==$.inArray(t.keyCode,[46,8,9,27,13,110])||65===t.keyCode&&(!0===t.ctrlKey||!0===t.metaKey)||25<=t.keyCode&&t.keyCode<=40||(t.shiftKey||t.keyCode<48||57<t.keyCode)&&(t.keyCode<96||105<t.keyCode)&&t.preventDefault()}),$(".order-cell").focus(function(t){0===parseInt($(this).val())&&$(this).val("")}),$(".order-cell").blur(function(t){num=parseInt($(this).val()),isNaN(num)||num<0?$(this).val("0"):$(this).val(num)}),$(".modal-order-cell").focus(function(t){0===parseInt($(this).val())&&$(this).val("")}),$(".modal-order-cell").blur(function(t){num=parseInt($(this).val()),isNaN(num)||num<0?$(this).val("0"):$(this).val(num)}),$(".dish-link").hover(function(){$(this).find(".dish-hint").css("display","block")},function(){$(this).find(".dish-hint").css("display","none")}),$(".nav-link").click(function(){$(location).attr("href",$(this).attr("href"))}),$('.login-form input[type="text"], .login-form input[type="password"], .login-form input[type="first_name"], .login-form input[type="last_name"]').on("focus",function(){$(this).removeClass("input-error")}),$("#login-btn").click(function(){var t=!1;return $(".login-form").find('input[type="text"], input[type="password"]').each(function(){""==$(this).val()?(t=!0,$(this).addClass("input-error")):$(this).removeClass("input-error")}),t||e({email:$("#form-username").val(),password:$("#form-password").val()}),!1}),$("#register-btn").click(function(){var t=!1;return $(".login-form").find('input[type="text"], input[type="password"], input[type="first_name"], input[type="last_name"]').each(function(){""==$(this).val()?(t=!0,$(this).addClass("input-error")):$(this).removeClass("input-error")}),t||a({email:$("#form-username").val(),password:$("#form-password").val(),first_name:$("#form-first-name").val(),last_name:$("#form-last-name").val(),cid:$("#form-cid").val(),ipn:$("#form-ipn").val()}),!1}),$("#save-order").click(function(){return t(),!1}),$("#save-order-popup").click(function(){return i(),!1}),$(".add-dish").click(function(){var t=$(this).attr("data-group-id"),e='<tr class="group-'+t+'" data-dish-id="" data-group-id="'+t+'" data-tmp-id="'+Math.floor(1000001*Math.random())+'"><th class="wh-name"><input type="text" name="name" value="" placeholder="Введіть ім\'я" /></th><th><input name="description" type="text" value="" placeholder="Опис.."/></th><th><input name="ingredients" type="text" value="" placeholder="Інгредієнти.."/></th><th width="5%"><input name="calories" type="number" value="" placeholder="Калорійність.."/></th><th width="5%"><input name="weight" type="number" value="" placeholder="Вага.."/></th><th width="5%"><input name="price" type="number" value="" placeholder="Ціна.."/></th><th width="3%"><a href="#" class="btn btn-danger btn-xs remove-dish" style="padding: 3px 10px 3px 10px; text-transform: lowercase">x</a></th></tr>';return $(".group-"+t).length?$(".group-"+t).last().after(e):$(this).parent().parent().after(e),!1}),$(".add-office").click(function(){var t=$(this).attr("data-company-id"),e='<tr data-office-id="" data-office-company-id="'+t+'"><th class="wh-name"><input class="office-address" value="" placeholder="Введіть адресу офісу.." type="text"></th><th style="opacity: 0.45">Посилання на реєстрацію буде згенероване автоматично після збереження змін</th></tr>';return $(".company-"+t).length?$(".company-"+t).last().after(e):$(this).parent().parent().after(e),!1}),$("#save-dishes").click(function(){return n(),!1}),$("#edit-menu").click(function(){$("#menu-header").css("display","none"),$("#editable-menu-header").css("display","block"),$(".wh-actions").css("display","table-cell"),$(".wh-table .menu-item").css("display","table-row")}),$("#save-menu").click(function(){$("#menu-header").css("display","block"),$("#editable-menu-header").css("display","none"),$(".wh-actions").css("display","none"),$(".wh-table .menu-item .wh-actions input").each(function(){$(this).is(":checked")?$(this).parent().parent().parent().css("display","table-row"):$(this).parent().parent().parent().css("display","none")})}),$(".menu-action .switch").click(function(t){var e=$(this).children("input");if(e.is(":checked")){var a=$(this).parent().parent().attr("data-menu-id");a?o(a,function(){e.prop("checked",!1)}):e.prop("checked",!1)}else{var i={start:$(".date-range").attr("data-start"),end:$(".date-range").attr("data-end"),dish_id:$(this).parent().parent().attr("data-dish-id")};s(i,function(){e.prop("checked",!0)})}return!1}),$(".order-filter").change(function(){$("#order-filters").submit()}),$("body").on("click",".remove-dish",function(){var t=$(this).parent().parent().attr("data-dish-id");return t?r(t):$(this).parent().parent().remove(),!1}),$("#save-companies").click(function(){var t=[];return $(".office-address").each(function(){t.push({id:$(this).parent().parent().attr("data-office-id"),address:$(this).val(),company_id:$(this).parent().parent().attr("data-office-company-id")})}),v(t),!1}),$("#save-users").click(function(){return spop({template:"Користувачі успішно збережені!",position:"top-left",style:"success",autoclose:6e3}),!1}),$("#save-user-personal-data").click(function(){return l({first_name:$("#user-first-name").val(),last_name:$("#user-last-name").val(),email:$("#user-email").val()}),!1}),$("#save-feedbacks").click(function(){var t=[];$(".dish-feedback-text").each(function(){t.push({dish_id:$(this).attr("data-feedback-dish-id"),text:$(this).val()})}),d(t)}),$(".rating-feedback").click(function(){var t,e=$(this).parent(),a=parseInt($(this).attr("data-rating-mark")),i=jQuery.Deferred();i.then(function(){u({mark:a,dish_id:e.attr("data-rating-dish-id")})}).then(function(){e.find(".rating-feedback").each(function(){t=parseInt($(this).attr("data-rating-mark")),a<t?($(this).html("&#9734;"),$(this).css("color","#8b8e94")):($(this).html("&#9733;"),$(this).css("color","#FFA33E"))})}),i.resolve()}),$("body").on("click",".dish-thumbnail",function(){var t=$(this).attr("data-dish-image");$("#dish-image-crop-header").attr("data-crop-popup-dish-id",t),$("#dish-image-crop-header").html($(this).attr("data-dish-image-name")),$("#views").css("display","none"),$(".crop-image-placeholder").css("display","block"),$("#cropModal").modal()});var T,j,x,O,S,N=500,D=500;function _(){null!=j?((O=new Image).onload=C,O.src=j.toDataURL("image/png")):C()}function C(){null!=T&&T.destroy(),$("#views").empty(),$("#views").append('<canvas id="canvas">'),j=$("#canvas")[0],x=j.getContext("2d"),j.width=O.width,j.height=O.height,x.drawImage(O,0,0),$("#canvas").Jcrop({onSelect:E,onRelease:J,boxWidth:N,boxHeight:D,aspectRatio:1},function(){T=this}),J()}function J(){S={x:0,y:0,w:j.width,h:j.height}}function E(t){S={x:Math.round(t.x),y:Math.round(t.y),w:Math.round(t.w),h:Math.round(t.h)}}$("#file").change(function(){$("#views").css("display","block"),function(t){if(t.files&&t.files[0]){var e=new FileReader;j=null,e.onload=function(t){(O=new Image).onload=_,O.src=t.target.result},e.readAsDataURL(t.files[0])}}(this)}),$("#choose-file").click(function(){$("#file").click(),$(".crop-image-placeholder").hide(0)}),$("#cropbutton").click(function(t){j.width=S.w,j.height=S.h,x.drawImage(O,S.x,S.y,S.w,S.h,0,0,j.width,j.height),_()}),$("#crop-dish-image-form").submit(function(t){t.preventDefault(),formData=new FormData($(this)[0]);var e=function(t){var e=";base64,";if(-1==t.indexOf(e)){var a=(n=t.split(","))[0].split(":")[1],i=decodeURIComponent(n[1]);return new Blob([i],{type:a})}a=(n=t.split(e))[0].split(":")[1];for(var n,o=(i=window.atob(n[1])).length,s=new Uint8Array(o),r=0;r<o;++r)s[r]=i.charCodeAt(r);return new Blob([s],{type:a})}(j.toDataURL("image/jpg")),n=$("#dish-image-crop-header").attr("data-crop-popup-dish-id");formData.append("cropped_image",e),$.ajax({url:"/api/v1/dishes/upload/"+n,type:"POST",data:formData,contentType:!1,cache:!1,processData:!1,success:function(t){var e=$('img[data-dish-image="'+n+'"]'),a=e.attr("src"),i=new Date;e.removeAttr("src").attr("src",a+"?"+i.getTime()),spop({template:"Зображення страви успішно оновлене!",position:"top-left",style:"success",autoclose:6e3})},error:function(t){spop({template:"Не вдалося оновити зображення страви :( Перевірте будь-ласка формат зображення.",position:"top-left",style:"error",autoclose:6e3})},complete:function(t){}})}),$("#save-cropped-image").click(function(){$("#crop-dish-image-form").submit()}),$(".infolink").hover(function(){$(this).parent().parent().find(".infolink-description").css("display","block")},function(){$(this).parent().parent().find(".infolink-description").css("display","none")}),$(".infolink-total-price").hover(function(){$(this).parent().parent().find(".total-price-infolink-description").css("display","block")},function(){$(this).parent().parent().find(".total-price-infolink-description").css("display","none")}),$(".show-add-dish-group").click(function(){$(".add-new-group-popup").css("display","block")}),$(".close-new-group-popup").click(function(){$(".add-new-group-popup").css("display","none")}),$("#save-new-dish-group").click(function(){var t=$(".new-group-popup-input").val();h({name:t})}),$("#save-new-company").click(function(){var t=$(".new-group-popup-input").val();m({name:t})}),$(document).on("change",".dish-group-name",function(){var t=$(this).attr("data-dish-group-name-id"),e=$(this).val();f({id:t,name:e})}).change(),$(document).on("change",".company-group-name",function(){var t=$(this).attr("data-company-group-name-id"),e=$(this).val();y({id:t,name:e})}).change(),$(".show-add-new-company").click(function(){$(".add-new-group-popup").css("display","block")}),$(".users-tabs li").click(function(){"users"===$(this).attr("data-tab")?($('li[data-tab="stuff"]').removeClass("active"),$('li[data-tab="users"]').removeClass("active").addClass("active"),$("#users").css("display","table"),$("#stuff").css("display","none")):($('li[data-tab="users"]').removeClass("active"),$('li[data-tab="stuff"]').removeClass("active").addClass("active"),$("#users").css("display","none"),$("#stuff").css("display","table"))}),$(".user-name-input").change(function(){$(this).parent().find(".user-email-actions").css("display","block")}),$(".user-email-actions-cancel").click(function(){var t=$(this).parent().parent().find(".user-name-input");t.val(t.attr("value")),$(this).parent().parent().find(".user-email-actions").css("display","none")}),$(".user-email-actions-confirm").click(function(){var t=$(this).parent().parent().find(".user-name-input");g($(this).attr("data-confirm-user-id"),t.val())}),$(".users-action-switch .switch").click(function(t){var e=$(this).children("input"),a=$(this).attr("data-deactivate-user-id");return e.is(":checked")?e.prop("checked",!1):e.prop("checked",!0),k(a),!1}),$(".disabled-cell").on("mousemove",function(t){for(var e=$(this).find(".orders-not-allowed-tooltip"),a=e.length;a--;)e[a].style.left=t.pageX+"px",e[a].style.top=t.pageY+"px"}),$("#save-system-settings").click(function(){var e={};$(".settings-input").each(function(){var t=$(this).attr("name");e[t]=$(this).val()}),w(e)}),$(".feedback-link").click(function(){var t=$(this).attr("data-dish-id"),e=$(this).attr("data-dish-image"),a=$(this).attr("data-dish-name");$("#modal-dish-image").attr("src",e),$("#modal-dish-name").html(a),p(t)})});