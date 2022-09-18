function toastNotifications(message) {
    var $showToast, $clearToasts;

      var options = {
        type :  "success",
        positionClass : "toast-top-right",
        title : "",
        message : message,
        closeButton : false,
        debug : false,
        newestOnTop : false,
        progressBar : false,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000", 
        extendedTimeOut : "1000",
        showEasing   : "swing",
        hideEasing   : "linear",
        showMethod   : "fadeIn",
        hideMethod   : "fadeOut"
      }

      var title = options.title || '', message = options.message;

      toastr[options.type](message, title, options);

    $clearToasts = $('#demo-clear-toasts');
    $clearToasts.click(function (evt) {
      toastr.clear();
    });
  }

  function toastDanger(message) {
    var $showToast, $clearToasts;

      var options = {
        type :  "error",
        positionClass : "toast-top-right",
        title : "",
        message : message,
        closeButton : false,
        debug : false,
        newestOnTop : false,
        progressBar : false,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000", 
        extendedTimeOut : "1000",
        showEasing   : "swing",
        hideEasing   : "linear",
        showMethod   : "fadeIn",
        hideMethod   : "fadeOut"
      }

      var title = options.title || '', message = options.message;

      toastr[options.type](message, title, options);



    $clearToasts = $('#demo-clear-toasts');
    $clearToasts.click(function (evt) {
      toastr.clear();
    });
  }
  