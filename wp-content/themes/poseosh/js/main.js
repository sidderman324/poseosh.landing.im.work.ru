$(document).ready(function(){
  var sidebarPos = jQuery('.sidebar').css('left');
  jQuery('.sidebar').hover(
    function() {
      jQuery('.sidebar').css({'left':'0','background-color':'#fff'});

    }, function() {
      jQuery('.sidebar').css({'left':sidebarPos,'background-color':'transparent'});
    }
  );
  jQuery('.block__btn--menu').on('click',function(e){
    e.preventDefault();
    var sidebarPos1 = jQuery('.sidebar').css('left');
    console.log(sidebarPos1);
    if(sidebarPos1 == sidebarPos) {
      jQuery('.sidebar').css({'left':'0','background-color':'#fff'});

    } else {
      jQuery('.sidebar').css({'left':sidebarPos,'background-color':'transparent'});
    }
  });


});
