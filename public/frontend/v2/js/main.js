const CART_URL = '/cart';
const USER_REGISTER_URL = '/register';
const USER_LOGIN_URL = '/login';
const GET_INFO = '/get';
const CHECKOUT_URL = '/checkout';
const USER_URL = '/user';
const SEARCH_URL = '/search';
const QUERY_URL = '/query';

// to make thisgs hidden from dom
function closeThings() {
  let s = document.getElementById('search_result_box');
  s.classList.add("hide");
}
// body on onclick listner
document.getElementsByTagName('body')[0].onclick = closeThings;



// submit product cart/buy form
function addToCart(product_id, btn) {
  // get dom elements to pull values
  let qty = document.getElementById('qty_input');
  let attribute_key = document.getElementsByClassName('attribute_key');

  // product
  let product = {
    'product_id': product_id,
    'qty': qty.value,
    attribute_value_ids: []
  };

  // pull attributes ids & values
  for (let item of attribute_key) {
    product.attribute_value_ids.push(item.value);
  }

  // send to server
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: CART_URL,
    method: "POST",
    data: product,
  }).done(function(res) {
    if (btn == 'buy_now') {
      window.location.href = CART_URL;
      return;
    }
    // show toast
    myToast("<i class='far fa-check-circle'></i> Item has been added to cart.", 'alert alert-success');
    document.getElementById('cart_size_notify').innerText = res.cart_size;
    document.getElementById('cart_list_container').innerHTML = res.cart;
  })
  .fail(function(xhttp, txtStatus, err) {
    // show toast
    myToast("<i class='far fa-times-circle'></i> Somthing Wrong! Please try again later.");
  });

}

function removeFromCart(product_id) {
  $.ajax({
    url: CART_URL,
    method: "GET",
    data: {'delete' : product_id}
  }).done(function(res) {
    window.location.href = window.location.href;
  })
  .fail(function(xhttp, txtStatus, err) {
    myToast("<i class='far fa-times-circle'></i> Somthing Wrong! Please try again later.");
  });
}


function checkLoginStatus() {

  $.ajax({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: GET_INFO,
    method: "POST",
    data: {'for': "is_user_logged_in"}
  }).done(function(res) {
    if (res.logged_in) {
      // if login success, then check shipping address is empty or not, err if empty
      // check if shipping details if empty, err
      let full_name = document.getElementById('full_name').value;
      let phone_number = document.getElementById('phone_number').value;
      let address = document.getElementById('address').value;
      let division = document.getElementById('division').value;
      let city = document.getElementById('city').value;
      let postal_code = document.getElementById('postal_code').value;
      let country = document.getElementById('country').value;
      if (!full_name || !phone_number || !address || !division || !city || !postal_code || !country) {
        myToast('Please check shipping address.', 'alert alert-danger');
        return;
      }

      // submit, place order
      document.getElementById('place_order_btn').submit();

    }else {
      $("#login_registration_model").modal('show');
    }
  })
  .fail(function(xhttp, txtStatus, err) {
    console.log(err);
  });
}


// get search results
function query(q) {
  let query_result_box = document.getElementById('search_result_box');
  query_result_box.classList.remove('hide');
  $.ajax({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: QUERY_URL,
    method: "GET",
    data: {'query': q},
  }).done(function(res) {
    query_result_box.innerHTML = res.query;
  })
  .fail(function(xhttp, txtStatus, err) {
    query_result_box.innerHTML = '';
  });
}


// ajax user login
function loginUser() {
  let form = document.querySelector('#user_popup_login');
  let formData = {
    email: form.querySelector('#email').value,
    password: form.querySelector('#password').value
  }

  // login request
  $.ajax({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    url: USER_LOGIN_URL,
    method: "POST",
    data: formData,
  }).done(function(res) {
    if (!res.error) {
      location.href = CART_URL;
    }
  })
  .fail(function(xhttp, txtStatus, err) {
    document.querySelector('#login_error_container').innerHTML = '<div class="alert alert-danger" role="alert">Please check your credentials.</div>';
  });
}

// ajax user register
function registerUser() {
  // get the form
  let form = document.querySelector('#cart_user_registration');
  let formData = {
    first_name: form.querySelector('#first_name').value,
    last_name: form.querySelector('#last_name').value,
    email: form.querySelector('#email').value,
    password: form.querySelector('#password').value,
    password_confirmation: form.querySelector('#password-confirm').value
  };


  $.ajax({
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
      url: USER_REGISTER_URL,
      type: "post",
      data: formData,
      success: function (data) {
          //redirect to checkout page
          location.href = CHECKOUT_URL;
      },
      error: function (data) {
          var messages = '';
          var errors = $.parseJSON(data.responseText);
          $.each(errors.errors, function (key, value) {
            messages += value[0]+'<br>';
          });
          $('#registration_error_container').html('<div class="alert alert-danger" role="alert">'+messages+'</div>');
      }
  });

}

let shipping_address = {
  'address_id': '',
  'full_name': '',
  'phone_number': '',
  'address': '',
  'division': '',
  'city': '',
  'postal_code': '',
  'country': '',
  'shipping_address': true,
};
function saveShippingAddress(btn = '') {
  let full_name = document.getElementById('full_name');
  let phone_number = document.getElementById('phone_number');
  let address = document.getElementById('address');
  let division = document.getElementById('division');
  let city = document.getElementById('city');
  let postal_code = document.getElementById('postal_code');
  let country = document.getElementById('country');

  let choosen_address_id = document.getElementById('saved_addresses');

  if (btn == 'change_address') {
    // get address id
    let choosen_address = '';
    for (var i = 0; i < addresses.length; i++) {
      if (addresses[i].id == choosen_address_id.value) {
        shipping_address.address_id = addresses[i].id;
        choosen_address = addresses[i];
        break;
      }
    }

    // fill address fields
    if (choosen_address_id.value == 'new') {
      shipping_address.address_id = '';
      document.getElementById('shipping_address_form').reset();
    }else {
      full_name.value = (choosen_address.full_name.length > 0) ? choosen_address.full_name : choosen_address.first_name+' '+choosen_address.last_name;
      full_name.value = full_name.value.replace('null', '');
      phone_number.value = choosen_address.mobile;
      address.value = choosen_address.address_1;
      division.value = choosen_address.state;
      city.value = choosen_address.city;
      postal_code.value = choosen_address.zip;
      country.value = choosen_address.country;
    }
  }

  shipping_address.full_name = full_name.value;
  shipping_address.phone_number = phone_number.value;
  shipping_address.address = address.value;
  shipping_address.division = division.value;
  shipping_address.city = city.value;
  shipping_address.postal_code = postal_code.value;
  shipping_address.country = country.value;

  if (btn == 'save') {
    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url: CART_URL,
        type: "GET",
        data: shipping_address,
        success: function (data) {
          if (data.status) {
            myToast("<i class='far fa-check-circle'></i> Shipping address has been saved.", 'alert alert-success');
          }
        },
        error: function (data) {
            var messages = '';
            var errors = $.parseJSON(data.responseText);
            $.each(errors.errors, function (key, value) {
              messages += value[0]+'<br>';
            });
            myToast(messages, 'alert alert-danger text-left');
        }
    });
  }

}

// toast message
function myToast(msg, class_names) {
  // Get the snackbar DIV
  var x = document.getElementById("snackbar");
  x.innerHTML = msg;
  // Add the "show" class to DIV
  x.className = "show "+class_names;
  // After 3 seconds, remove the show class from DIV
  setTimeout(function(){ x.className = x.className.replace("show", "") }, 3000);
}

// show popup_banner
$(document).ready(function(){
    $("#popup_banner").modal('show');

    // for filter search page=search
    let inputs = document.getElementsByClassName('filter_input');
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].addEventListener("click", filterURL);
    }

    // larave pagination modification, to get page number & will add manually by filterURL function
    let paginate_links = document.getElementsByClassName('page-link');
    for (var i = 0; i < paginate_links.length; i++) {
      paginate_links[i].addEventListener("click", function (event) {
        event.preventDefault();
        var x = new URL(event.target.href);
        filterURL(x.searchParams.get('page'));
      });
    }
});


// search url generator for filter

function filterURL(page_no = '') {
    let filter_values = {
      price: {min: 0, max: 0},
      sort_by: '',
      categories: [],
      brands: []
    }

    // get all the values
    filter_values.price.min = document.getElementsByClassName('filter_price_min')[0].value;
    filter_values.price.max = document.getElementsByClassName('filter_price_max')[0].value;

    let cats = document.getElementsByClassName('filter_category');
    for (var i = 0; i < cats.length; i++) {
      if (cats[i].checked) {
        filter_values.categories.push(cats[i].value);
      }
    }

    let brands = document.getElementsByClassName('filter_brand');
    for (var i = 0; i < brands.length; i++) {
      if (brands[i].checked) {
        filter_values.brands.push(brands[i].value);
      }
    }

    filter_values.sort_by = document.getElementsByClassName('filter_sort_by')[0].value;
    let url = new URL(window.location.href);

    url.searchParams.delete('min');
    url.searchParams.delete('max');
    if (filter_values.price.min && filter_values.price.max) {
      url.searchParams.append('min', filter_values.price.min);
      url.searchParams.append('max', filter_values.price.max);
    }

    if (filter_values.sort_by == 'relevance') {
      url.searchParams.delete('sort_by');
    }

    url.searchParams.delete('categories');
    if (filter_values.categories.length > 0) {
      let x = '';
      for (var i = 0; i < filter_values.categories.length; i++) {
        x = x + filter_values.categories[i] +',';
      }
      url.searchParams.append('categories', x.substring(0, x.length - 1));
    }

    url.searchParams.delete('brands');
    if (filter_values.brands.length > 0) {
      let x = '';
      for (var i = 0; i < filter_values.brands.length; i++) {
        x = x + filter_values.brands[i] +',';
      }
      url.searchParams.append('brands', x.substring(0, x.length - 1));
    }

    url.searchParams.delete('sort_by');
    if (filter_values.sort_by) {
      if (filter_values.sort_by == 'relevance') {

      }else {
        url.searchParams.append('sort_by', filter_values.sort_by);
      }
    }
    url.searchParams.delete('page');
    if (page_no) {
      url.searchParams.append('page', page_no);
    }

    window.location.href = url.href;
}


// // facebook login
// window.fbAsyncInit = function() {
//     FB.init({
//         appId   : 'YOUR_APP_ID',
//         oauth   : true,
//         status  : true, // check login status
//         cookie  : true, // enable cookies to allow the server to access the session
//         xfbml   : true // parse XFBML
//     });
//
//   };
//
// function fb_login(){
//     FB.login(function(response) {
//
//         if (response.authResponse) {
//             console.log('Welcome!  Fetching your information.... ');
//             //console.log(response); // dump complete info
//             access_token = response.authResponse.accessToken; //get access token
//             user_id = response.authResponse.userID; //get FB UID
//
//             FB.api('/me', function(response) {
//                 user_email = response.email; //get user email
//           // you can store this data into your database
//             });
//
//         } else {
//             //user hit cancel button
//             console.log('User cancelled login or did not fully authorize.');
//
//         }
//     }, {
//         scope: 'publish_stream,email'
//     });
// }
// (function() {
//     var e = document.createElement('script');
//     e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
//     e.async = true;
//     document.getElementById('fb-root').appendChild(e);
// }());
