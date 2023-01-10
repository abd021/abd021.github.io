function goto(e,url) {
    e.preventDefault();
    window.location.href = url;
}


function getRegisterStatus() {

  let values="tag=getRegisterStatus";
  $("#overlay").show();
  $.ajax({
      url : 'php/functions.php',
      type : 'POST',
      data : values,
      dataType:'json',
      success : function(data) {    
                      
          if(data.mess=="success")
          {   
            if(+data.register==0)
            {
              $("#register_container").hide();
              $("#register_warning").show();
            }
          }
          $("#overlay").hide();
      },
      error : function(request,error)
      {
          alert("Request: "+JSON.stringify(request));
          $("#overlay").hide();
      }
  }); 

}


function register()
{
  
    let name = document.getElementById("name").value;
    let email = document.getElementById("email").value;
    let password = document.getElementById("password").value;
    let passwordc = document.getElementById("passwordc").value;

    let pass = 1;
    let mess = "";

    if(name.length<3)
    {
        pass = 0;
        mess = mess+" -  يجب ان يكون الاسم على الاقل 3 احرف "
    }

    if(email.length<=0)
    {
        pass = 0;
        mess = mess+" -  الايميل مطلوب"
    }
    else
    {
      if(!checkEmail(email))
      {
        pass = 0;
        mess = mess+" -   يوجد مشكلة في هيئة الايميل" 
      }
    }


    if(password.length<6)
    {
        pass = 0;
        mess = mess+" -  يجب ان تكون كلمة السر على الاقل 6 احرف"
    }

    if(passwordc.length<6)
    {
        pass = 0;
        mess = mess+" -  يجب ان تكون تأكيد كلمة السر على الاقل 6 احرف"
    }


    if(password!=passwordc)
    {
      pass = 0;
      mess = mess+" -  كلمة السر و تأكيدها غير متطابقان"
    }



    if(pass == 1)
    {
        $("#overlay").show();
        let values = $("#register_form").serialize();


values = values+"&tag=registeruser";


$.ajax({

    url : 'php/functions.php',
    type : 'POST',
    data : values,
    dataType:'json',
    success : function(data) {              
        if(data.mess=="success")
        {

          Swal.fire(
            'تمت العملية بنجاح',
            'لقم تم تسجيلك بالموقع بنجاح الرجاء تفعيل حسابك عن طريق الضغط على رابط التفعيل في الايميل الذي ارسلناه اليك',
            'success'
          ).then(function (result) {
            if (true) {
              window.location = "login.html";
            }
          })
        }
        else
        {
          Swal.fire(
            'هناك مشكلة',
            ' يوجد مشكلة في عملية التسجيل  '+data.info,
            'warning'
          )
        }
        $("#overlay").hide();
    },
    error : function(request,error)
    {
        $("#overlay").hide();
        alert("Request: "+JSON.stringify(request));
    }
});
    }
    else
    {

      Swal.fire(
        'هناك مشكلة',
        ' يوجد مشكلة في عملية التسجيل  '+mess,
        'warning'
      )
    }
    

}



function loginuser() {

  let email = document.getElementById("email").value;
  let password = document.getElementById("password").value;

  let pass = 1;
  let mess = "";


  if(email.length<=0)
  {
      pass = 0;
      mess = mess+" -  الايميل مطلوب"
  }
  else
  {
    if(!checkEmail(email))
    {
      pass = 0;
      mess = mess+" -   يوجد مشكلة في هيئة الايميل" 
    }
  }


  if(password.length<6)
  {
      pass = 0;
      mess = mess+" -  يجب ان تكون كلمة السر على الاقل 6 احرف"
  }



  if(pass == 1)
  {
      $("#overlay").show();
      let values = $("#login_form").serialize();


values = values+"&tag=loginuser";


$.ajax({

  url : 'php/functions.php',
  type : 'POST',
  data : values,
  dataType:'json',
  success : function(data) {              
      if(data.mess=="success")
      {

        Swal.fire(
          'تمت العملية بنجاح',
          ' لقد تم تسجيل الدخول بنجاح سيتم تحويلك للصفحة الرئيسية بعض الضغط على موافقة ',
          'success'
        ).then(function (result) {
          if (true) {
            window.location = "index.html";
          }
        })
      }
      else
      {
        Swal.fire(
          'هناك مشكلة',
          ' يوجد مشكلة في عملية الدخول  '+data.info,
          'warning'
        )
      }
      $("#overlay").hide();
  },
  error : function(request,error)
  {
      $("#overlay").hide();
      alert("Request: "+JSON.stringify(request));
  }
});
  }
  else
  {

    Swal.fire(
      'هناك مشكلة',
      ' يوجد مشكلة في عملية التسجيل  '+mess,
      'warning'
    )
  }

}


$(document).ready(function() {

  $.ajax({
      url: "includes/header.txt",
      cache: false,
      dataType: "html",
      success: function(data) {
          $("#header").html(data);
          checklogin();
      },
      error : function(request,error)
      {
          alert("Request: "+JSON.stringify(request));
          checklogin();
      }
  });


  $.ajax({
      url: "includes/footer.txt",
      cache: false,
      dataType: "html",
      success: function(data) {
          $("#footer_all").html(data);
              d = new Date();
    year = d.getFullYear();
    $("#footer_year").text(year);
    myMap();
      },
      error : function(request,error)
      {
          alert("Request: "+JSON.stringify(request));
      }
  });
  
})

function myMap() {
mapProp= {
  center:new google.maps.LatLng(32.012612,35.935645),
  zoom:17,
};
map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
TestMarker();
}

    // Function for adding a marker to the page.
    function addMarker(location) {
        marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
    
        function TestMarker() {
           university = new google.maps.LatLng(32.012612, 35.935645);
           addMarker(university);
    }
    


function checklogin()
{

   
    let values="tag=checklogin";
    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {              
            if(data.mess=="success")
            {
                if(currpage=="login")
                {
                window.location.href = "index.html"
                }

                 $("#username_login").text(data.name);
                  //  اسم المستخدم
                $(".login_in_div_logged_in").show();
                $(".login_in_div_logged_out").hide();
            }
            else
            {
                $(".login_in_div_logged_in").hide();
                $(".login_in_div_logged_out").show();
            }
        },
        error : function(request,error)
        {
            alert("Request: "+JSON.stringify(request));
        }
    });  
}


function logout(event)
{
    event.preventDefault();
    let values="tag=logout";
    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {              
            if(data.mess=="success")
            {
                window.location.href = "login.html"
            }

        },
        error : function(request,error)
        {
            alert("Request: "+JSON.stringify(request));
        }
    });  
}



$(document).ready(function() {
  $("#keyword").keydown(function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      
      let type = $("#search_type").val();
      let keyword = $("#keyword").val();

      let pass = 1;
      let mess = "";

      if(type.length<=0)
      {
        pass = 0;
        mess = mess+" -  عليك اختيار طريقة البحث باستخدام العنوان او المؤلف "
      }

      if(keyword.trim().length<3) {
        pass = 0;
        mess = mess+" - كلمة البحث يجب ان تكون على الاقل 3 احرف "
      }

      if(pass == 1) {

        $("#overlay").show();

        let values = $("#search_form").serialize();
        values=values+"&tag=searchbooks";
        $.ajax({
            url : 'php/functions.php',
            type : 'POST',
            data : values,
            dataType:'json',
            success : function(data) {              
                if(data.mess=="success")
                {

                  


                  bookrows = [];

                data.data.forEach(function(obj) { 
                    
                  let p_id = obj.id;
                  let p_title = obj.title;
                  let p_author = obj.author;
                  let p_bookid = obj.bookid;
                  let p_pdate = obj.publish_date;
                  let p_pagesno = obj.pagesno
                  let p_copyno = obj.copyno;
                  let p_copynov = obj.copynov;
                  let p_c_date = obj.create_date;
                  let p_u_date = obj.update_date;
                  let p_status = obj.status;

                  p_register_abook = '<a onclick="open_register_book(event,\''+p_author+'\',\''+p_title+'\','+p_id+')" class="tableicon acceptcolor" href="#"><ion-icon name="thumbs-up-outline"></ion-icon></a>';

                  p_titlefull = '<label class="tablerow" id="tablerow'+p_id+'">'+p_title+'</label>'


                  let obj1 = { 'autonum' : autonum,'bookid' : p_bookid,'title' :p_titlefull,'author' : p_author,'copyno' : p_copyno,'copynov' : p_copynov,'pagesno' : p_pagesno,'status' : p_register_abook }


                  bookrows.push(obj1);
                  autonum++;
                  
              
              });

              //console.log(bookrows)

              search_table.clear().draw();
              search_table.rows.add( bookrows ).draw();


                }
                else
                {

                }
                $("#overlay").hide();
            },
            error : function(request,error)
            {
                alert("Request: "+JSON.stringify(request));
                $("#overlay").hide();
            }
        });  

      }
      else
      {

        Swal.fire(
          'هناك مشكلة',
          ' يوجد مشكلة في عملية البحث  '+mess,
          'warning'
        )

      }



    }
});
})


function open_register_book(event,author,title,id) {
  event.preventDefault();

  Swal.fire({
    title: 'تأكيد استلاف كتاب',
    text: " هل أنت متأكد من استلاف الكتاب ذو العنوان "+ title + "  و المؤلف  "+ author,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'نعم'
  }).then((result) => {
    if (result.isConfirmed) {

        let values="tag=borrowbook&bookid="+id;
        $("#overlay").show();
        $.ajax({
            url : 'php/functions.php',
            type : 'POST',
            data : values,
            dataType:'json',
            success : function(data) {    
                            
                if(data.mess=="success")
                {
    
                    Swal.fire(
                        'تم الاستلاف',
                        data.info,
                        'success'
                      )
                    
                    
                }
                else
                {
                  Swal.fire(
                    'حدث مشكلة اثناء عملية الاستلاف',
                    data.info,
                    'warning'
                  )
                }
                $("#overlay").hide();
            },
            error : function(request,error)
            {
                alert("Request: "+JSON.stringify(request));
                $("#overlay").hide();
            }
        }); 

    }
  }) 


}

function checkEmail(emailAddress) {
  var sQtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
  var sDtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
  var sAtom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
  var sQuotedPair = '\\x5c[\\x00-\\x7f]';
  var sDomainLiteral = '\\x5b(' + sDtext + '|' + sQuotedPair + ')*\\x5d';
  var sQuotedString = '\\x22(' + sQtext + '|' + sQuotedPair + ')*\\x22';
  var sDomain_ref = sAtom;
  var sSubDomain = '(' + sDomain_ref + '|' + sDomainLiteral + ')';
  var sWord = '(' + sAtom + '|' + sQuotedString + ')';
  var sDomain = sSubDomain + '(\\x2e' + sSubDomain + ')*';
  var sLocalPart = sWord + '(\\x2e' + sWord + ')*';
  var sAddrSpec = sLocalPart + '\\x40' + sDomain; // complete RFC822 email address spec
  var sValidEmail = '^' + sAddrSpec + '$'; // as whole string

  var reValidEmail = new RegExp(sValidEmail);

  return reValidEmail.test(emailAddress);
}