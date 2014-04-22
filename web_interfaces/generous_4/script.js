$( document ).ready(function() {
  var image_index = 1;
  var image_increment =23;
  var sort_type = "bydate";

  //basic book object
  function book(title,date,url,id)
  {
    this.title=title;
    this.date=date;
    this.url=url;
    this.id=id;
  }
  var clickcount=0;
  ///////////////menu dropdown////////////////////
  $("h1").mouseenter(function() {
    console.log("enter");
    $(".sort").css('visibility','visible');
    $(".sort").hide().fadeIn( "slow", function() {
        


        setTimeout(function() 
        {
          $(".sort").fadeOut( "slow");
        }, 5000);
    });
  });

        $("#bydate").click(function(){
          $("#container").empty();
  
          sort_type = "bydate";
          image_index = 1;
          addImages(image_index,image_index+image_increment);
          console.log("clickcount "+clickcount);
          clickcount++;

        });
        $("#bytitle").click(function(){
          $("#container").empty();
          sort_type = "bytitle";
          image_index = 1;
          addImages(image_index,image_index+image_increment);
          console.log("clickcount "+clickcount);
          clickcount++;
          
        });
  var books = new Array();

  function addImages(startPoint, endPoint){
    image_directory  = 'BloodAxeBooks_300/';
    for(var i=startPoint;i<endPoint;i++){

      
      //entry = String(Math.floor(Math.random()*300));

      $.post( "get_record.php", { id: i, sort: sort_type } , function( data ) {

        $("#title").text("Title: "+data.title ) ;
        $("#date").text("Date: "+data.date ) ;

        var new_element = "<div id='new"+ data.id+"' class='book'><p> <img src='"+image_directory+data.fname +"' width = '200'>" + "</p></div>" ;
        var new_secret_element = "<div id='secret"+ data.id+"' class='secret'><p> </p></div>" ;
        $("#container").add(new_element).appendTo( "#container");
        $("#new"+data.id).add(new_secret_element).appendTo( "#new"+data.id);
        //<div class="secret"> <p>  </p> </div>
        
        var newBook=new book(data.title,data.date,image_directory+data.fname,data.id);
        books.push(newBook);

      }, "json");
      /////end of post


      //console.log("new"+i);
      //bind it
      $(document).on( "mouseenter", "#new"+i, function(event){
        var this_id = $(this).attr('id');
        var index =  this_id.substring(3,this_id.length);

        $.post( "get_record.php", { id: index, sort: sort_type } , function( data ) {
          //console.log(data.title);
          var p = $( "#new"+index);
          var position = p.position();

          var str = data.creators;
          var res = str.split("\r");

          var content = data.title+ '<br>'+ data.date+ '<br>'+data.creators;
          $("#secret"+index).css('visibility','visible')
            .html( content);

            $("#secret"+index).hide().fadeIn( "slow", function() {
            });
                   // .css("left", position.left)
                   //     .css("top", position.top)
        }, "json");
        //$('#dvLoading').hide();
      });


      $(document).on( "mouseleave", "#new"+i, function(event){
        var this_id = $(this).attr('id');
        var index =  this_id.substring(3,this_id.length);

        $.post( "get_record.php", { id: index, sort: sort_type } , function( data ) {

          $("#secret"+index).css('visibility','hidden')
            .html( "");
        }, "json");
      });
    }//end of for loop

  }

  addImages(image_index,image_index+image_increment);

  //on scroll to the bottom
  $(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {                     
      image_index+=image_increment;
      var stop =image_index+image_increment;
      
      addImages(image_index,image_index+image_increment);
    }
  });

  
});