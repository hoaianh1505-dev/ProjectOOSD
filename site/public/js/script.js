function openMenuMobile() {
  $(".menu-mb").width("250px");
  $(".btn-menu-mb").hide("slow");
}

function closeMenuMobile() {
  $(".menu-mb").width(0);
  $(".btn-menu-mb").show("slow");
}

// Code trong $(function(){...}) chỉ chạy khi toàn bộ code html đã được load (tải) hoàn tất.
$(function () {
  $(".form-register, .form-reset-password").validate({
    rules: {
      // simple rule, converted to {required:true}
      // required là buộc phải thêm vô
      fullname: {
        required: true,
        maxlength: 50,
        // Biểu thức kính quy
        regex:
          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
      },
      // compound rule
      mobile: {
        required: true,
        regex: /^0([0-9]{9,9})$/,
      },
      email: {
        required: true,
        email: true,
        // Giá trị trả về là true sẽ là không lỗi, ngược lại false là có lỗi
        remote: "?c=customer&a=notExistingEmail",
      },
      password: {
        required: true,
        regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/,
      },
      password_confirmation: {
        required: true,
        equalTo: "[name=password]", //so sanh voi ai do  [selector]
      },
      hiddenRecaptcha: {
        // required: true là chưa check
        // required: false là check rồi
        required: function () {
          // check rồi thì trả về một chuổi ký tự
          // chưa check trả về chuổi rỗng
          // giá trị được xem là false; false, null, 0, undefined, ''
          // giá trị còn lại được xem là true
          if (grecaptcha.getResponse()) {
            return false;
          }
          return true;
        },
      },
    },
    // message nếu phạm rule thì hiển lổi đó ra
    messages: {
      // simple rule, converted to {required:true}
      // required là buộc phải thêm vô
      fullname: {
        required: "Vui lòng họ và tên ",
        maxlength: "Vui lòng không nhập quá 50 ký tự",
        regex: "Vui lòng không nhập số hoặc ký tự đặc biệt",
      },
      // compound rule
      mobile: {
        required: "Vui lòng nhập số điện thoại",
        regex: "Vui lòng nhập đúng số,  vd: 0933345678",
      },

      email: {
        required: "Vui lòng nhập email ",
        email: "Vui lòng nhập đúng định dạng,  vd: abc@gmail.com",
        remote: "Email này đã tồn tại, vui lòng nhập email mới",
      },

      password: {
        required: "Vui lòng nhập password ",
        regex:
          "Vui lòng nhập ít nhất 8 kí tự bao gồm ký tự hoa, ký tự thường, số và ký tự đặc biệt",
      },
      password_confirmation: {
        required: "Vui lòng nhập lại mật khẩu ",
        equalTo: "Mật khẩu không trùng khớp ",
      },
      hiddenRecaptcha: {
        required: "Vui lòng xác nhận tôi không phải là robot",
      },
    },
  });

  $(".form-checkout, .form-shipping-default").validate({
    rules: {
      fullname: {
        required: true,
        maxlength: 50,
        regex:
          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
      },
      mobile: {
        required: true,
        regex: /^0([0-9]{9,9})$/,
      },
      province: {
        required: true,
      },
      district: {
        required: true,
      },
      ward: {
        required: true,
      },
      address: {
        required: true,
      },
    },
    messages: {
      fullname: {
        required: "Vui lòng nhập họ và tên",
        maxlength: "Vui lòng không nhập quá 50 ký tự",
        regex: "Vui lòng không nhập số, hoặc ký tự đặc biệt",
      },

      mobile: {
        required: "Vui lòng nhập số điện thoại",
        regex: "Vui lòng nhập đúng định dạng số điện thoại. vd: 0932538468",
      },

      province: {
        required: "Vui lòng chọn tỉnh/thành phố",
      },
      district: {
        required: "Vui lòng chọn quận/huyện",
      },
      ward: {
        required: "Vui lòng chọn phường/xã",
      },
      address: {
        required: "Vui lòng nhập địa chỉ số nhà, đường",
      },
    },
  });

  // Chọn tỉnh thành, hiển thị quận/huyện tương ứng
  // jqChange
  $("[name=province]").change(function (e) {
    e.preventDefault();
    const province_id = $(this).val();
    //jqAjax
    $.ajax({
      type: "GET",
      url: "?c=address&a=getDistricts",
      data: { province_id: province_id },
      success: function (data) {
        updateSelectBox("[name = district]", data);
        updateSelectBox("[name = ward]", null);
      },
    });

    $.ajax({
      type: "GET",
      url: "?c=address&a=getShippingFee",
      data: { province_id: province_id },
      success: function (shippingFee) {
        const subTotal = $(".payment-total").attr("data");
        const total = Number(subTotal) + Number(shippingFee);
        // Cập nhật
        // // Hiện thị số tiền trong bank
        let amount = total;
        const url_bank = `https://img.vietqr.io/image/vpbank-0965337849-compact2.jpg?amount=${amount}&addInfo=thanh%20toan%20don%20v%20hang&accountName=CTK:%20NGUYEN%20DUC%20THANH%20LONG.`;
        $(".image_bank").attr("src", url_bank);

        $(".shipping-fee").html(formatMoney(shippingFee) + "đ");
        $(".payment-total").html(formatMoney(total) + "đ");
      },
    });
  });

  $("[name=district]").change(function (e) {
    e.preventDefault();
    const district_id = $(this).val();
    //jqAjax
    $.ajax({
      type: "GET",
      url: "?c=address&a=getWards",
      data: { district_id: district_id },
      success: function (data) {
        updateSelectBox("[name = ward]", data);
      },
    });
  });

  displayCart();

  // jqClick
  $(".buy-in-detail").click(function (e) {
    const product_id = $(this).attr("product-id");
    const qty = $(".product-quantity").val();
    // jqAjax
    $.ajax({
      type: "GET",
      url: "?c=cart&a=add",
      data: { product_id: product_id, qty: qty },
      success: function (response) {
        // code này chỉ chạy khi server thực thi request thành công
        // Dữ liệu trên server sẽ gởi về trình duyệt và nằm trong biến response
        displayCart();
      },
    });
  });

  // jqClick
  $(".buy").click(function (e) {
    const product_id = $(this).attr("product-id");
    // jqAjax
    $.ajax({
      type: "GET",
      url: "?c=cart&a=add",
      data: { product_id: product_id, qty: 1 },
      success: function (response) {
        // code này chỉ chạy khi server thực thi request thành công
        // Dữ liệu trên server sẽ gởi về trình duyệt và nằm trong biến response
        displayCart();
      },
    });
  });

  $(".info-account").validate({
    rules: {
      fullname: {
        required: true,
        maxlength: 50,
        regex:
          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
      },
      mobile: {
        required: true,
        regex: /^0([0-9]{9,9})$/,
      },
      password: {
        regex: /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/,
      },
      password_confirmation: {
        equalTo: "[name=password]",
      },
    },
    messages: {
      fullname: {
        required: "Vui lòng nhập họ và tên",
        maxlength: "Vui lòng không nhập quá 50 ký tự",
        regex: "Vui lòng không nhập số, hoặc ký tự đặc biệt",
      },

      mobile: {
        required: "Vui lòng nhập số điện thoại",
        regex: "Vui lòng nhập đúng định dạng số điện thoại. vd: 0932538468",
      },

      password: {
        regex:
          "Vui lòng nhập ít nhất 8 ký tự bao gồm ký tự hoa, ký tự thường, số và ký tự đặc biệt",
      },
      password_confirmation: {
        equalTo: "Mật khẩu phải trùng khớp",
      },
    },
  });

  $(".form-login").validate({
    rules: {
      email: {
        required: true,
        email: true,
      },
      password: {
        required: true,
      },
    },
    messages: {
      email: {
        required: "Vui lòng nhập email",
        email: "Vui lòng nhập đúng định dạng email. vd: abc@gmail.com",
      },
      password: {
        required: "Vui lòng nhập mật khẩu",
      },
    },
  });

  $(".form-comment").validate({
    rules: {
      fullname: {
        required: true,
        maxlength: 50,
        regex:
          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
      },
      email: {
        required: true,
        email: true,
      },
      description: {
        required: true,
      },
    },
    messages: {
      fullname: {
        required: "Vui lòng nhập họ và tên",
        maxlength: "Vui lòng không nhập quá 50 ký tự",
        regex: "Vui lòng không nhập số, hoặc ký tự đặc biệt",
      },

      email: {
        required: "Vui lòng nhập email",
        email: "Vui lòng nhập đúng định dạng email. vd: abc@gmail.com",
      },
      description: {
        required: "Vui lòng nhập nội dung",
      },
    },
    submitHandler: function (form) {
      // cho hiện thông báo
      $(".message").show();
      $(".message").html(
        '<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gởi đánh giá, vui lòng chờ...'
      );
      // Gởi thông tin lên server
      // alert($(form).serialize());
      // jqAjax
      $.ajax({
        type: "POST",
        url: "?c=product&a=storeComment",
        data: $(form).serialize(),
        success: function (response) {
          // dữ liệu trên server gởi về, sẽ nằm trong response
          $(".comment-list").html(response);
          $(".message").hide(); //display:none;

          //chuyển input giá trị 4 => 4 sao
          $(
            "main .product-detail .product-description .answered-rating-input"
          ).rating({
            min: 0,
            max: 5,
            step: 1,
            size: "md",
            stars: "5",
            showClear: false,
            showCaption: false,
            displayOnly: false,
            hoverEnabled: true,
          });
          // reset form
          form.reset();
        },
      });
    },
  });

  $(".form-contact").validate({
    rules: {
      fullname: {
        required: true,
        maxlength: 50,
        regex:
          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂưăạảấầẩẫậắằẳẵặẹẻẽềềểỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ\s]+$/i,
      },
      mobile: {
        required: true,
        regex: /^0([0-9]{9,9})$/,
      },
      email: {
        required: true,
        email: true,
      },
      content: {
        required: true,
      },
    },
    messages: {
      fullname: {
        required: "Vui lòng nhập họ và tên",
        maxlength: "Vui lòng không nhập quá 50 ký tự",
        regex: "Vui lòng không nhập số, hoặc ký tự đặc biệt",
      },
      mobile: {
        required: "Vui lòng nhập số điện thoại",
        regex: "Vui lòng nhập đúng số điện thoại. vd: 0932538468",
      },
      email: {
        required: "Vui lòng nhập email",
        email: "Vui lòng nhập đúng định dạng email. vd: abc@gmail.com",
      },
      content: {
        required: "Vui lòng nhập nội dung",
      },
    },
    submitHandler: function (form) {
      // cho hiện thông báo
      $(".message").show();
      $(".message").html(
        '<i class="fas fa-spinner fa-spin"></i> Hệ thống đang gởi mail, vui lòng chờ...'
      );
      // Gởi thông tin lên server
      // alert($(form).serialize());
      // jqAjax
      $.ajax({
        type: "POST",
        url: "?c=contact&a=sendEmail",
        data: $(form).serialize(),
        success: function (response) {
          // dữ liệu trên server gởi về, sẽ nằm trong response
          $(".message").html(response);
        },
      });
    },
  });

  $.validator.addMethod(
    "regex",
    function (value, element, regexp) {
      var re = new RegExp(regexp);
      return this.optional(element) || re.test(value);
    },
    "Please check your input."
  );

  // jqChange
  $("#sort-select").change(function (e) {
    e.preventDefault();
    const sort = $(this).val();
    const fullUrl = getUpdateParam("sort", sort);
    window.location.href = fullUrl;
  });

  // jqClick
  $(".price-range input").click(function (e) {
    e.preventDefault();
    const priceRange = $(this).val();
    // header('location: ?c=product&....')
    window.location.href = `?c=product&price-range=${priceRange}`;
  });

  $(".product-container").hover(function () {
    $(this).children(".button-product-action").toggle(400);
  });

  // Display or hidden button back to top
  $(window).scroll(function () {
    if ($(this).scrollTop()) {
      $(".back-to-top").fadeIn();
    } else {
      $(".back-to-top").fadeOut();
    }
  });

  // Khi click vào button back to top, sẽ cuộn lên đầu trang web trong vòng 0.8s
  $(".back-to-top").click(function () {
    $("html").animate({ scrollTop: 0 }, 800);
  });

  // Hiển thị form đăng ký
  $(".btn-register").click(function () {
    $("#modal-login").modal("hide");
    $("#modal-register").modal("show");
  });

  // Hiển thị form forgot password
  $(".btn-forgot-password").click(function () {
    $("#modal-login").modal("hide");
    $("#modal-forgot-password").modal("show");
  });

  // Hiển thị form đăng nhập
  $(".btn-login").click(function () {
    $("#modal-login").modal("show");
  });

  // Fix add padding-right 17px to body after close modal
  // Don't rememeber also attach with fix css
  $(".modal").on("hide.bs.modal", function (e) {
    e.stopPropagation();
    $("body").css("padding-right", 0);
  });

  // Hiển thị cart dialog
  $(".btn-cart-detail").click(function () {
    $("#modal-cart-detail").modal("show");
  });

  // Hiển thị aside menu mobile
  $(".btn-aside-mobile").click(function () {
    $("main aside .inner-aside").toggle();
  });

  // Hiển thị carousel for product thumnail
  $(
    "main .product-detail .product-detail-carousel-slider .owl-carousel"
  ).owlCarousel({
    margin: 10,
    nav: true,
  });

  // Cập nhật hình chính khi click vào thumbnail hình ở slider
  $("main .product-detail .product-detail-carousel-slider img").click(function (
    event
  ) {
    /* Act on the event */
    $("main .product-detail .main-image-thumbnail").attr(
      "src",
      $(this).attr("src")
    );
    var image_path = $("main .product-detail .main-image-thumbnail").attr(
      "src"
    );
    $(".zoomWindow").css("background-image", "url('" + image_path + "')");
  });

  $("main .product-detail .product-description .rating-input").rating({
    min: 0,
    max: 5,
    step: 1,
    size: "md",
    stars: "5",
    showClear: false,
    showCaption: false,
  });

  $("main .product-detail .product-description .answered-rating-input").rating({
    min: 0,
    max: 5,
    step: 1,
    size: "md",
    stars: "5",
    showClear: false,
    showCaption: false,
    displayOnly: false,
    hoverEnabled: true,
  });

  $("main .ship-checkout[name=payment_method]").click(function (event) {
    /* Act on the event */
  });

  $("input[name=checkout]").click(function (event) {
    /* Act on the event */
    window.location.href = "?c=payment&a=checkout";
  });

  $("input[name=back-shopping]").click(function (event) {
    /* Act on the event */
    window.location.href = "?c=product";
  });

  // Hiển thị carousel for relative products
  $("main .product-detail .product-related .owl-carousel").owlCarousel({
    // loop: true,
    margin: 10,
    nav: true,
    dots: false,
    responsive: {
      0: {
        items: 2,
      },
      600: {
        items: 4,
      },
      1000: {
        items: 5,
      },
    },
  });
});

// Login in google
function onSignIn(googleUser) {
  var id_token = googleUser.getAuthResponse().id_token;
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "http://study.com/register/google/backend/process.php");
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onload = function () {
    console.log("Signed in as: " + xhr.responseText);
  };
  xhr.send("idtoken=" + id_token);
}

// update param dựa theo key
function getUpdateParam(key, value) {
  // http://godashop.com/site/?c=product&category_id=4
  const url = window.location.href;
  const obj = new URL(url);
  obj.searchParams.set(key, value); //cập nhật value mới cho key
  newUrl = obj.href;
  return newUrl;
}

function goToPage(page) {
  const fullUrl = getUpdateParam("page", page);
  window.location.href = fullUrl;
}

function displayCart() {
  const cartJson = getCookie("cart");
  if (cartJson == "") {
    return; //kết thúc, không cập nhật gì ở giao diện
  }

  // converst json to object
  const cart = JSON.parse(cartJson);
  $(".number-total-product").html(cart.total_product_number);
  $(".price-total").html(formatMoney(cart.total_price) + "đ");
  const items = cart.items;
  // forin
  let rows = "";
  for (const product_id in items) {
    const item = items[product_id];
    // 1 dòng sản phẩm
    console.log(item);
    const row = `<hr>
                    <div class="clearfix text-left">
                        <div class="row">
                            <div class="col-sm-6 col-md-1">
                                <div><img class="img-responsive" src="../upload/${
                                  item.img
                                }" alt="${item.name} "></div>
                            </div>
                            <div class="col-sm-6 col-md-3"><a class="product-name" href="#">${item.name.replace(
                              /\+/g,
                              " "
                            )}</a></div>
                            <div class="col-sm-6 col-md-2"><span class="product-item-discount">${formatMoney(
                              item.unit_price
                            )}₫</span></div>
                            <div class="col-sm-6 col-md-3"><input type="number" onchange="updateProductInCart(this,${
                              item.product_id
                            })" min="1" value="${item.qty}"></div>
                            <div class="col-sm-6 col-md-2"><span>${formatMoney(
                              item.total_price
                            )}₫</span></div>
                            <div class="col-sm-6 col-md-1"><a class="remove-product" href="javascript:void(0)" onclick="deleteProductInCart(${
                              item.product_id
                            })"><span class="glyphicon glyphicon-trash"></span></a></div>
                        </div>
                    </div>`;
    rows += row;
  }
  // cập nhật row vào cart-product
  $(".cart-product").html(rows);
}

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

// 190000 => 190.000
function formatMoney(money) {
  // 7.574,335
  return number_format(money, 0, ",", ".");
}

function updateProductInCart(input, product_id) {
  const qty = $(input).val();
  // jqAjax
  $.ajax({
    type: "GET",
    url: "?c=cart&a=update",
    data: { product_id: product_id, qty: qty },
    success: function (response) {
      displayCart();
    },
  });
}

function deleteProductInCart(product_id) {
  // jqAjax
  $.ajax({
    type: "GET",
    url: "?c=cart&a=delete",
    data: { product_id: product_id },
    success: function (response) {
      displayCart();
    },
  });
}
function updateSelectBox(selector, data) {
  //json to array
  const rows = JSON.parse(data);
  // xoá đi các option hiện tại của thẻ select đi, trừ option đầu tiên
  $(selector).find("option").not(":first").remove();
  if (!data) {
    return null;
  }
  //continue
  //forof
  for (const row of rows) {
    const option = `<option value = "${row.id}">${row.name}</option> `;
    // thêm vào cuối thẻ select
    $(selector).append(option);
  }
}
