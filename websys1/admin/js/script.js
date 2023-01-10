
    
    $(document).ready(function() {

        $.ajax({
            url: "includes/header.txt",
            cache: false,
            dataType: "html",
            success: function(data) {
                $("#header").html(data);
                init();
                checklogin();
            },
            error : function(request,error)
            {
                alert("Request: "+JSON.stringify(request));
                init();
                checklogin();
            }
        });

    })
    
    let cantoggle = 1;


 function togglesidebar() {
    if(cantoggle==1)
    {
        cantoggle = 0;
    if(document.getElementById("adminsidebarinner").style.right == "0px")
    {
    document.getElementById("adminsidebarinner").style.right = "-100%";
    document.getElementById("adminpanel").classList.add("makefullwidth");
    cantoggle = 1;
    }
    else if(document.getElementById("adminsidebarinner").style.right == "") {
        document.getElementById("adminsidebarinner").style.right = "-100%";
       document.getElementById("adminpanel").classList.add("makefullwidth");
       cantoggle = 1;
    }
    else
    {
    document.getElementById("adminsidebarinner").style.right = "0px";
    setTimeout(function() {
        document.getElementById("adminpanel").classList.remove("makefullwidth");
        cantoggle = 1;
    }, 500)
    }
}
}






function init() {
    let w = window.innerWidth    || document.documentElement.clientWidth    || document.body.clientWidth;
    if(w<=768)
    {
        togglesidebar();   
    }
}


function checklogin()
{

    $("#overlay").show();

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
                else
                {
                    setTimeout(function() {
                        $("#user_full_name_span").text(data.user_full_name);


                        if(currpage=="bookhome")
                        {
                        getbooks();
                        }
                        else if(currpage=="index") {
                            getstat();
                        }
                        else if(currpage=="userhome") {
                            getUsers();
                        }
                        else if(currpage=="regsitredbooks") {
                            getAllRegistredBorrowings();
                        }
                        else if(currpage=="takenbooks") {
                            getalltakenborrow();
                        }
                        else if(currpage=="borrowarchive") {
                            loadBorrowArchive();
                        }
                        else if(currpage=="settings") {
                            getSettings();
                        }
                        else if(currpage="changepassword") {
                            $("#overlay").hide();
                        }




                       // $("#overlay").hide();
                    },60)
                    
                    
                }
            }
            else
            {
                if(currpage!="login")
                {
                window.location.href = "login.html"
                }
                else
                {
                    $("#overlay").hide();
                }
            }
        },
        error : function(request,error)
        {
            $("#overlay").hide();
            alert("Request: "+JSON.stringify(request));
        }
    });  
}


function login() {

    $("#overlay").show();

let username = document.getElementById("username").value;
let password = document.getElementById("password").value;

let pass = 1;
let mess = "";


if(username.length>3)
{

if(!checkEmail(username))
{
pass = 0;
mess = mess+" - اسم المستخدم يجب ان يكون على هيئة بريد الكتروني صحيح";
}

}
else
{
    pass = 0;
    mess = mess+" - اسم المستخدم على الاقل 4 حروف";
}


if(password.length<=5)
{
pass = 0;
mess = mess+" - كلمة السر على الاقل 6 حروف"
}


if(pass == 1)
{
let values = $("#login_form").serialize();
values = values+"&tag=login";


$.ajax({

    url : 'php/functions.php',
    type : 'POST',
    data : values,
    dataType:'json',
    success : function(data) {              
        if(data.mess=="success")
        {
            window.location.href = "index.html"
        }
        else
        {
            $("#overlay").hide();
            alert(data.error)
        }
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
    $("#overlay").hide();
    alert(mess);
}

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



function addBook()
{
  
    let title = document.getElementById("title").value;
    let author = document.getElementById("author").value;
    let serial = document.getElementById("serial").value;

    let dayselect = document.getElementById("dayselect").value;
    let monthselect = document.getElementById("monthselect").value;
    let yearselect = document.getElementById("yearselect").value;

    let pagesno = document.getElementById("pages").value;
    let copyno = document.getElementById("copynum").value;
    let type = document.getElementById("type").value;
    let bookprimaryid = document.getElementById("bookprimaryid").value;


    let pass = 1;
    let mess = "";

    if(title.length<4)
    {
        pass = 0;
        mess = mess+" -  يجب ان يكون العنوان على الاقل 4 احرف "
    }

    if(author.length<4)
    {
        pass = 0;
        mess = mess+" -  يجب ان يكون اسم المؤلف على الاقل 4 احرف "
    }


    if(serial.length<6)
    {
        pass = 0;
        mess = mess+" -  يجب ان يكون الرقم التسلسلي للكتاب على الاقل 6 احرف "
    }


    if(dayselect.length<1 || monthselect.length<1 || yearselect.length<1)
    {
        pass = 0;
        mess = mess+" -  تاريخ نشر الكتاب مطلوب"
    }
    else
    {

        let pdate = new Date(yearselect, monthselect-1, dayselect, 12, 0, 0, 0);
        pdatet = pdate.getTime()/1000;

    }


    if(pagesno.length<1)
    {
        pass = 0;
        mess = mess+" -  عدد صفحات الكتاب مطلوب"
    }
    else
    {
        if(isNaN(pagesno))
        {
            pass = 0;
            mess = mess+" -  عدد صفحات الكتاب يجب ان يكون رقم"   
        }
        else
        {
            if(pagesno<1)
            {
                pass = 0;
                mess = mess+" -  عدد صفحات الكتاب يجب ان يكون رقم موجب على الاقل 1"   
            }
        }
    }



    if(copyno.length<1)
    {
        pass = 0;
        mess = mess+" -  عدد نسخ الكتاب مطلوب"
    }
    else
    {
        if(isNaN(copyno))
        {
            pass = 0;
            mess = mess+" -  عدد نسخ الكتاب يجب ان يكون رقم"   
        }
        else
        {
            if(copyno<1)
            {
                pass = 0;
                mess = mess+" -  عدد نسخ الكتاب يجب ان يكون رقم موجب على الاقل 1"  
            }
        }
    }


    if(pass == 1)
    {
        $("#overlay").show();
        let values = $("#addbookform").serialize();

        let tag="";
        if(type=="add")
        {
            tag="addbook";
            pid = 0;
        }
        else if(type=="edit") {
            tag="editbook";
            pid = bookprimaryid;
        }
        else
        {
            tag="editbook";
            pid = bookprimaryid; 
        }


values = values+"&tag="+tag+"&pdate="+pdatet+"&pid="+pid;


$.ajax({

    url : 'php/functions.php',
    type : 'POST',
    data : values,
    dataType:'json',
    success : function(data) {              
        if(data.mess=="success")
        {
            $('#myModal').modal('hide');

            let p_id = data.id; 

            let p_edit = '<a data-id="'+p_id+'"  onclick="openeditbook(event,'+p_id+')" class="tableicon  acceptcolor" href="#"><ion-icon name="create-outline"></ion-icon></a>';
            let p_delete = '<a data-id="'+p_id+'" onclick="opendeletebook(event,'+p_id+')" class="tableicon  deletecolor" href="#"><ion-icon name="close-circle-outline"></ion-icon></a>'
            p_titlefull = '<label class="tablerow" id="tablerow'+p_id+'">'+title+'</label>'

            if(type=="add")
            {
                table1.rows.add( [ { "autonum": autonum,"title": p_titlefull,"author":author,"pagesno":pagesno,"copyno": copyno,"status" : copyno,"edit": p_edit,"delete": p_delete } ] ).draw();
            }
            else
            {
                let td = $("#tablerow"+p_id).parent("td");
                let rowindex = table1.cell(td).index().row;
                table1.cell(rowindex, 1).data(p_titlefull);
                table1.cell(rowindex, 2).data(author);
                table1.cell(rowindex, 3).data(pagesno);
                table1.cell(rowindex, 4).data(copyno);
                table1.cell(rowindex, 5).data(data.newcopynov);
            }
        }
        else
        {
            alert(data.error)
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
        alert(mess);
    }
    

}



function getbooks()
{
    let values="tag=getbooks";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
            
            let bookrows = [];
            
            if(data.mess=="success")
            {
                //     
                

                data.data.forEach(function(obj) { 
                    
                    let p_id = obj.id;
                    let p_title = obj.title;
                    let p_author = obj.author;
                    let p_bookid = obj.bookid;
                    let p_pdate = obj.publish_date;
                    let p_pagesno = obj.pagesno
                    let p_copyno = obj.copyno;
                    let p_c_date = obj.create_date;
                    let p_u_date = obj.update_date;
                    let p_status = obj.copynov;

                    p_edit = '<a data-id="'+p_id+'" onclick="openeditbook(event,'+p_id+')" class="tableicon acceptcolor" href="#"><ion-icon name="create-outline"></ion-icon></a>';
                    p_delete = '<a data-id="'+p_id+'" onclick="opendeletebook(event,'+p_id+')" class="tableicon  deletecolor" href="#"><ion-icon name="close-circle-outline"></ion-icon></a>'
                    p_titlefull = '<label class="tablerow" id="tablerow'+p_id+'">'+p_title+'</label>'


                    let obj1 = { 'autonum' : autonum,'title' :p_titlefull,'author' : p_author,'pagesno' : p_pagesno,'copyno' : p_copyno,'status' : p_status,'edit' : p_edit, 'delete' : p_delete }


                    bookrows.push(obj1);
                    autonum++;
                    
                
                });

                //console.log(bookrows)

                  table1.rows.add( bookrows ).draw();

               
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



function openaddbook()
{
    $("#title").val(""); 
    $("#author").val(""); 
    $("#serial").val(""); 
    $("#dayselect").val(""); 
    $("#monthselect").val(""); 
    $("#yearselect").val(""); 
    $("#pages").val(""); 
    $("#copynum").val(""); 
    $("#bookprimaryid").val("");
    $("#type").val("add");
    $("#bookformtitle").text("اضافة كتاب")


    $('#myModal').modal('show');
}

function openeditbook(e,id)
{
    e.preventDefault();

    let values="tag=getbook&id="+id;
    $("#overlay").show();
    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
                        
            if(data.mess=="success")
            {

                let ts = +data.data.publish_date * 1000;
                pdate = new Date(ts);
                days = pdate.getDate();
                months = pdate.getMonth()+1;
                years = pdate.getFullYear();

                //    
                $("#title").val(data.data.title); 
                $("#author").val(data.data.author); 
                $("#serial").val(data.data.bookid); 
                $("#dayselect").val(days); 
                $("#monthselect").val(months); 
                $("#yearselect").val(years); 
                $("#pages").val(data.data.pagesno); 
                $("#copynum").val(data.data.copyno); 
                $("#bookprimaryid").val(data.data.id);
                $("#type").val("edit");
                $("#bookformtitle").text("تعديل كتاب رقم" + data.data.id);


                $('#myModal').modal('show');
                
                
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


function opendeletebook(e,id) {
    e.preventDefault();
    Swal.fire({
        title: 'تأكيد حذف كتاب',
        text: "هل أنت متأكد من حذف الكتاب ذو الرقم "+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {



            let values="tag=deletebook&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        let tr = $("#tablerow"+id).closest("tr");
                        table1.row(tr).remove().draw();

                        Swal.fire(
                            'تم الحذف!',
                            'لقد تم حذف الكتاب ذو الرقم'+id,
                            'success'
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



$("document").ready(function() {
    $("#registercheck").click(function() {
       if($("#registercheck").is(":checked"))
       {
        
        let values="tag=enablereg";
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
                        'تمت العملية بنجاح',
                        'تم تفعيل التسجيل بالموقع بنجاح',
                        'success'
                      );
                      $("#registernotelabel").text("التسجيل مفعل اضغط لوقف التسجيل بالموقع");
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
        let values="tag=disablereg";
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
                        'تمت العملية بنجاح',
                        'تم تعطيل التسجيل بالموقع بنجاح',
                        'warning'
                      )
                      $("#registernotelabel").text("التسجيل معطل اضغط لتفعيل التسجيل بالموقع");
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





    ////////////////////////////////




    $("#borrowcheck").click(function() {
        if($("#borrowcheck").is(":checked"))
        {
         
         let values="tag=enableborrow";
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
                         'تمت العملية بنجاح',
                         'تم تفعيل الاستلاف من الموقع بنجاح',
                         'success'
                       );
                       $("#borrownotelabel").text("الاستلاف مفعل اضغط لوقف الاستلاف من الموقع");
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
         let values="tag=disableborrow";
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
                         'تمت العملية بنجاح',
                         'تم تعطيل الاستلاف من الموقع بنجاح',
                         'warning'
                       )
                       $("#borrownotelabel").text("الاستلاف معطل اضغط لتفعيل الاستلاف من الموقع");
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
})


function getSettings() {

    let values="tag=getsettings";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
                        
            if(data.mess=="success")
            {   
                if(+data.register==1)
                {
                    $("#registercheck").prop("checked",true);
                    $("#registernotelabel").text("التسجيل مفعل اضغط لوقف التسجيل بالموقع");
                }
                else
                {
                    $("#registercheck").prop("checked",false);
                    $("#registernotelabel").text("التسجيل معطل اضغط لتفعيل التسجيل بالموقع");
                }


                if(+data.borrow==1)
                {
                    $("#borrowcheck").prop("checked",true);
                    $("#borrownotelabel").text("الاستلاف مفعل اضغط لوقف الاستلاف من الموقع");
                }
                else
                {
                    $("#borrowcheck").prop("checked",false);
                    $("#borrownotelabel").text("الاستلاف معطل اضغط لتفعيل الاستلاف من الموقع");
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


function getUsers() {
 
    let values="tag=getusers";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
            
            let userrows = [];
            
            if(data.mess=="success")
            {
                //     
                

                data.data.forEach(function(obj) { 
                    
                    let p_id = obj.id;
                    let p_name = obj.name;
                    let p_email = obj.email;
                    let p_status = obj.status;

                    p_delete = '<a data-id="'+p_id+'" onclick="opendeleteuser(event,'+p_id+')" class="tableicon  deletecolor" href="#"><ion-icon name="close-circle-outline"></ion-icon></a>'
                    p_namefull = '<label class="tablerow" id="tablerow'+p_id+'">'+p_name+'</label>'

                    p_statusfull = "";
                    if(p_status==0) {
                        p_statusfull = "<label>مستخدم جديد لم يفعل حسابه بعد</label>";
                    }
                    else if(p_status==1) {
                        p_statusfull = "<label>مفعل</label><a onclick='openuserban(event,"+p_id+")' class='tableicon deletecolor' href='#'><ion-icon name='ban-outline'></ion-icon></a>";
                    }
                    else if(p_status==2)
                    {
                        p_statusfull = "<label>محظور</label><a onclick='openuseractivate(event,"+p_id+")' class='tableicon acceptcolor' href='#'><ion-icon name='checkmark-outline'></ion-icon></a>";
                    }
                    else
                    {
                        p_statusfull = "<label>هناك خطأ ما</label>"; 
                    }


                    let obj1 = { 'autonum' : autonum,'name' :p_namefull,'email' : p_email,'status' : p_statusfull, 'delete' : p_delete }


                    userrows.push(obj1);
                    autonum++;
                    
                
                });

                //console.log(bookrows)

                  usertable.rows.add( userrows ).draw();

               
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


function openuserban(event,id) {
    event.preventDefault();

    Swal.fire({
        title: 'تأكيد حظر مستخدم',
        text: " هل أنت متأكد من حظر المستخدم رقم "+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {
            let values="tag=banuser&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        let p_statusfull = "<label>محظور</label><a onclick='openuseractivate(event,"+id+")' class='tableicon acceptcolor' href='#'><ion-icon name='checkmark-outline'></ion-icon></a>";

                        let td = $("#tablerow"+id).parent("td");
                        let rowindex =  usertable.cell(td).index().row;

                          usertable.cell(rowindex, 3).data(p_statusfull);


                        Swal.fire(
                            'تم حظر المستخدم',
                            ' لقد تم حظر المستخدم ذو الرقم'+id,
                            'success'
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




function openuseractivate(event,id) {
    event.preventDefault();

    Swal.fire({
        title: 'تأكيد تفعيل مستخدم',
        text: " هل أنت متأكد من تفعيل المستخدم رقم "+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {
            let values="tag=activateuser&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        p_statusfull = "<label>مفعل</label><a onclick='openuserban(event,"+id+")' class='tableicon deletecolor' href='#'><ion-icon name='ban-outline'></ion-icon></a>";
              
                        let td = $("#tablerow"+id).parent("td");
                        let rowindex =  usertable.cell(td).index().row;

                          usertable.cell(rowindex, 3).data(p_statusfull);


                        Swal.fire(
                            'تم تفعيل المستخدم',
                            ' لقد تم تفعيل المستخدم ذو الرقم'+id,
                            'success'
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





function opendeleteuser(e,id) {
    e.preventDefault();
    Swal.fire({
        title: 'تأكيد حذف مستخدم',
        text: " هل أنت متأكد من حذف المستخدم ذو الرقم "+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {



            let values="tag=deleteuser&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        let tr = $("#tablerow"+id).closest("tr");
                        usertable.row(tr).remove().draw();

                        Swal.fire(
                            'تم الحذف!',
                            ' لقد تم حذف المستخدم ذو الرقم'+id,
                            'success'
                          )
                        
                        
                    }
                    else
                    {
                            Swal.fire(
                            'هناك مشكلة',
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





function getAllRegistredBorrowings()
{
    let values="tag=getallregistredborrowings";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
            
            let register_borrow = [];
            
            if(data.mess=="success")
            {
                //     
                

                data.data.forEach(function(obj) { 
                    
                    let borrow_id = obj.bid;
                    let register_date = +obj.register_date;
                    let user_name = obj.name;
                    let user_email = obj.email;
                    let book_title = obj.title;
                    let book_bookid = obj.bookidg;
                    let borrow_secret = obj.secret;

                    reg_date = new Date(register_date*1000);
                    reg_date_day = reg_date.getDate();
                    reg_date_month = reg_date.getMonth()+1;
                    reg_date_year = reg_date.getFullYear();
                    reg_date_hour = reg_date.getHours();
                    reg_date_minutes = reg_date.getMinutes();

                    reg_date_str = reg_date_day+"/"+reg_date_month+"/"+reg_date_year+" "+reg_date_hour+":"+reg_date_minutes;


                    p_confirm = '<a data-id="'+borrow_id+'" onclick="openconfirmborrow(event,'+borrow_id+')" class="tableicon acceptcolor" href="#"><ion-icon name="thumbs-up-outline"></ion-icon></a>';
                    p_delete = '<a data-id="'+borrow_id+'" onclick="opendeleteborrow(event,'+borrow_id+')" class="tableicon  deletecolor" href="#"><ion-icon name="close-circle-outline"></ion-icon></a>'
                    p_borrowid_full = '<label class="tablerow" id="tablerow'+borrow_id+'">'+borrow_id+'</label>'


                    let obj1 = { 'autonum' : autonum,'borrowid' :p_borrowid_full,'name' : user_name,'email' : user_email,'title' : book_title,'bookid' : book_bookid,'regdate' : reg_date_str, 'secret' : borrow_secret,'confirmborrowing' : p_confirm,'delete' : p_delete }


                    register_borrow.push(obj1);
                    autonum++;
                    
                
                });

                //console.log(bookrows)

                booksregistertable.rows.add( register_borrow ).draw();

               
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

function openconfirmborrow(event,id) {
    event.preventDefault();
    Swal.fire({
        title: 'تأكيد استلاف كتاب',
        text: " هل انت متأكد من اتمام عملية الاستلاف ذات الرقم"+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {



            let values="tag=confirmborrowing&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        let tr = $("#tablerow"+id).closest("tr");
                        booksregistertable.row(tr).remove().draw();

                        Swal.fire(
                            'تمت عملية الاستلاف', 
                            ' لقد تمت عملية الاستلاف بنجاح و لدى المستخدم 14 يوما لاعادة الكتاب منذ هذه اللحظة',
                            'success'
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


function getalltakenborrow() {
    let values="tag=getalltakenborrowings";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
            
            let taken_borrow = [];
            
            if(data.mess=="success")
            {
                //     
                

                data.data.forEach(function(obj) { 
                    
                    let borrow_id = obj.bid;
                    let taken_date_db = +obj.taken_date;
                    let user_name = obj.name;
                    let user_email = obj.email;
                    let book_title = obj.title;
                    let book_bookid = obj.bookidg;
                    let borrow_secret = obj.secret;

                    taken_date = new Date(taken_date_db*1000);
                    taken_date_day = taken_date.getDate();
                    taken_date_month = taken_date.getMonth()+1;
                    taken_date_year = taken_date.getFullYear();
                    taken_date_hour = taken_date.getHours();
                    taken_date_minutes = taken_date.getMinutes();

                    taken_date_str = taken_date_day+"/"+taken_date_month+"/"+taken_date_year+" "+taken_date_hour+":"+taken_date_minutes;


                    return_date = new Date((taken_date_db*1000)+1209600000);
                    return_date_day = return_date.getDate();
                    return_date_month = return_date.getMonth()+1;
                    return_date_year = return_date.getFullYear();
                    return_date_hour = return_date.getHours();
                    return_date_minutes = return_date.getMinutes();

                    return_date_str = return_date_day+"/"+return_date_month+"/"+return_date_year+" "+return_date_hour+":"+return_date_minutes;




                    p_confirm_return = '<a data-id="'+borrow_id+'" onclick="openconfirmreturn(event,'+borrow_id+')" class="tableicon acceptcolor" href="#"><ion-icon name="thumbs-up-outline"></ion-icon></a>';
                    p_borrowid_full = '<label class="tablerow" id="tablerow'+borrow_id+'">'+borrow_id+'</label>'


                    let obj1 = { 'autonum' : autonum,'borrowid' :p_borrowid_full,'name' : user_name,'email' : user_email,'title' : book_title,'bookid' : book_bookid,'regdate' : taken_date_str,'returndate' : return_date_str,'confirmreturn' : p_confirm_return }


                    taken_borrow.push(obj1);
                    autonum++;
                    
                
                });

                //console.log(bookrows)

                takenbookstable.rows.add( taken_borrow ).draw();

               
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



function openconfirmreturn(event,id) {
    event.preventDefault();
    Swal.fire({
        title: 'تأكيد ترجيع كتاب',
        text: " هل انت متأكد من اتمام عملية ترجيع الكتاب بعملية الاستلاف ذات الرقم"+ id,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم'
      }).then((result) => {
        if (result.isConfirmed) {



            let values="tag=confirmreturnbookborrowing&id="+id;
            $("#overlay").show();
            $.ajax({
                url : 'php/functions.php',
                type : 'POST',
                data : values,
                dataType:'json',
                success : function(data) {    
                                
                    if(data.mess=="success")
                    {
        
                        let tr = $("#tablerow"+id).closest("tr");
                        takenbookstable.row(tr).remove().draw();

                        Swal.fire(
                            'تمت عملية ارجاع الكتاب', 
                            ' لقد تمت عملية ارجاع الكتاب بنجاح ',
                            'success'
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



function loadBorrowArchive() {
    let values="tag=getallarchiveborrowings";

    $.ajax({
        url : 'php/functions.php',
        type : 'POST',
        data : values,
        dataType:'json',
        success : function(data) {    
            
            let archive_borrow = [];
            
            if(data.mess=="success")
            {
                //     
                

                data.data.forEach(function(obj) { 
                    
                    let borrow_id = obj.bid;
                    let taken_date_db = +obj.taken_date;
                    let return_date_db = +obj.return_date;
                    let user_name = obj.name;
                    let user_email = obj.email;
                    let book_title = obj.title;
                    let book_bookid = obj.bookidg;
                    let borrow_secret = obj.secret;

                    taken_date = new Date(taken_date_db*1000);
                    taken_date_day = taken_date.getDate();
                    taken_date_month = taken_date.getMonth()+1;
                    taken_date_year = taken_date.getFullYear();
                    taken_date_hour = taken_date.getHours();
                    taken_date_minutes = taken_date.getMinutes();

                    taken_date_str = taken_date_day+"/"+taken_date_month+"/"+taken_date_year+" "+taken_date_hour+":"+taken_date_minutes;


                    return_date = new Date(return_date_db*1000);
                    return_date_day = return_date.getDate();
                    return_date_month = return_date.getMonth()+1;
                    return_date_year = return_date.getFullYear();
                    return_date_hour = return_date.getHours();
                    return_date_minutes = return_date.getMinutes();

                    return_date_str = return_date_day+"/"+return_date_month+"/"+return_date_year+" "+return_date_hour+":"+return_date_minutes;



                    p_borrowid_full = '<label class="tablerow" id="tablerow'+borrow_id+'">'+borrow_id+'</label>'


                    let obj1 = { 'autonum' : autonum,'borrowid' :p_borrowid_full,'name' : user_name,'email' : user_email,'title' : book_title,'bookid' : book_bookid,'regdate' : taken_date_str,'realreturndate' : return_date_str }


                    archive_borrow.push(obj1);
                    autonum++;
                    
                
                });

                //console.log(bookrows)

                archivetable.rows.add( archive_borrow ).draw();

               
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



function changePassword() {

    let cpassword = $("#cpassword").val();
    let newpassword = $("#newpassword").val();
    let newpasswordc = $("#newpasswordc").val();

    let pass = 1;
    let mess = "";

    if(cpassword.length<6) {
        pass = 0;
        mess = mess+" - كلمة السر الحالية يجب ان تكون على الاقل 6 احرف ";
    }

    if(newpassword.length<6) {
        pass = 0;
        mess = mess+" - كلمة السر الجديدة يجب ان تكون على الاقل 6 احرف ";    
    }


    if(newpasswordc.length<6) {
        pass = 0;
        mess = mess+" -  تأكيد كلمة السر الجديدة يجب ان تكون على الاقل 6 احرف";    
    }


    if(newpassword!=newpasswordc) {
        pass = 0;
        mess = mess+" -  كلمة السر الجديدة و تأكيدها غير متطابقان ";    
    }


    if(pass == 1)
    {
        $("#overlay").show();
        let values = $("#changepasswordform").serialize();


values = values+"&tag=changepassword";


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
            ' لقد تم تغيير كلمة السر بنجاح ',
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
            ' يوجد مشكلة في عملية تغيير كلمة السر  '+data.info,
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
        ' يوجد مشكلة في عملية تغيير كلمة السر  '+mess,
        'warning'
      )
    }
}



function getstat() {

    values = "tag=getstat";


$.ajax({

    url : 'php/functions.php',
    type : 'POST',
    data : values,
    dataType:'json',
    success : function(data) {              
        if(data.mess=="success")
        {
            $("#bookc").text(data.bookc);
            $("#userc").text(data.userc);
            $("#takenc").text(data.takenc);
            $("#archivec").text(data.archivec);

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



