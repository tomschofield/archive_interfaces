$( document ).ready(function() {
    image_directory  = 'BloodAxeBooks_thumbs/';
    large_image_directory  = '/BloodAxeBooks/';
    var image_index = 1;
    var image_increment =250;
    var sort_type = "bydate";
    var books = new Array();
    var width = window.innerWidth;
    var height = window.innerHeight;

    //a nice book object
    function book(title,date,url,id)
      {
        this.title=title;
        this.date=date;
        this.url=url;
        this.id=id;
      }

    function getListOfYears(){

    }
    var currentDate = 0;
    
    var image_width =30;
    var currentWidth = image_width +200;
    function addImages(startPoint, endPoint){

      
      var i=startPoint;
      //for(var i=startPoint;i<endPoint;i++){
        $.post( "get_record.php", { id: i, sort: sort_type } , function( data ) {

          if(data.date!=currentDate){
            currentDate=data.date;
            
            // var new_element = "<div id='year_"+ data.date+"' class='row' style='width:300px;' > "+data.date +" </div>" ;

            var new_element = "<div id='year_"+ data.date+"' class='row' style='width:"+currentWidth+"px;' > "+data.date +" </div>" ;
            $("#container").add(new_element).appendTo( "#container");
            currentWidth = image_width +200;
          }
          //create the new elements   
          var new_element = "<div id='book_"+ data.id+"' class='book' > <img src='"+image_directory+data.fname +"' width = '"+image_width+"'>" + "</div>" ;
          var content = data.title+ '<br>'+ data.date+ '<br>'+data.creators;
          var new_secret_element = "<div id='secret_"+ data.id+"' class='secret'><p>"+ content + " </p></div>" ;

          $("#year_"+data.date).css("width",currentWidth );

          currentWidth += image_width;

          $("#year_"+data.date).add(new_element).appendTo("#year_"+data.date);
          
          $("#book_"+data.id).add(new_secret_element).appendTo( "#book_"+data.id);
          
          var newBook=new book(data.title,data.date,image_directory+data.fname,data.id);
          books.push(newBook);
            
          //bind events
           $(document).on( "click", "#book_"+i, function(event){
              var src = $(this).find('img').attr('src');

              //var id = $(this).attr('id');
              var temp_file_name = src.split("/");
              console.log(temp_file_name[1]);
              temp_file_name = large_image_directory+temp_file_name[1];
              $("#hidden_image").find('img')
                .attr("src", temp_file_name)
                .css("width",300);
              
              $("#hidden_image")
                .hide()
                  .fadeIn(2000)
                    .dimBackground()
                      .css("left",(width/2)-150)
                        .css("top",100+ $("body").scrollTop());

              $("#hidden_text").html(content)
                .css("left",350)
                        .css("top",50);

           });//end of binding
           $(document).on( "mouseenter", "#book_"+i, function(event){
              var src = $(this).find('img').attr('src');
              
              $(this)
                .find('img')
                  .css("border","2px solid")
                    .css("border-color","white");
           });//end of binding
           $(document).on( "mouseleave", "#book_"+i, function(event){
              var src = $(this).find('img').attr('src');
              $(this)
                .find('img')
                  .css("border","0px solid");
           });//end of binding

          //make function recursive
          i++;
          if(i<endPoint){
            addImages(i, i+image_increment);
          }

        }, "json");
        /////end of post
  };
  $("body").click(function(){
    $("#hidden_image").fadeOut(1000,function(){
    $.undim();
    });
  });
  $("#hidden_image").click(function(){
    $(this).fadeOut(1000,function(){
    $.undim();
    });
  });

  addImages(image_index, image_index+image_increment);
  
});