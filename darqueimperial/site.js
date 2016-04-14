$(function(){
  $(".editButtons input").each(function(){
    $(this).addClass("btn");
  });
});

$(function(){
  $(":submit").each(function(){
    $(this).addClass("btn");
  });
});

$(function(){
  $("table").each(function(){
    $(this).addClass("table table-striped table-bordered table-condensed");
  });
});

$(function(){
  $(".span4 .sectionLinks").hide();
});

function loginPDuser(PDdata, PD_Auth_Timestamp, PD_Auth_Signature){
  var myurl = window.location.href;
  PDdata.PDuser = true;
  PDdata.timestamp = PD_Auth_Timestamp;
  PDdata.wikisignature = PD_Auth_Signature;

  $.ajax({
    url: myurl,
    data: PDdata,
    success: function(data){
	//$('body').append(data);
      //console.log(jQuery.parseJSON(data));
    },
	error: function(){
		//window.alert("login error");
	}
  });
}

function testforlogin(PD_Auth_URL, PD_Auth_Timestamp, PD_Auth_Signature){
  var loginurl = "http://www.profounddecisions.co.uk?returnto=/empireplotwiki";

  PD_Auth_URL+= "&timestamp=" + PD_Auth_Timestamp;
  PD_Auth_URL+= "&signature=" + PD_Auth_Signature;
console.log(1);
  $.ajax({
    url: PD_Auth_URL,
    dataType: "jsonp",
    success: function(data){
	  //console.log(data);
	  //window.alert(data.username);
      loginPDuser(data, PD_Auth_Timestamp, PD_Auth_Signature);
	  
    },
    error: function(a,b,c){
      var r=confirm("Please login to your Profound Decisions account");
      if (r){
         window.location.replace(loginurl);
      }
    }
  });
}

$(document).ready(function() {
  var PD_Auth_URL = $("#PD_Auth_URL").val(),
      PD_Auth_Timestamp = $("#PD_Auth_Timestamp").val(),
      PD_Auth_Signature = $("#PD_Auth_Signature").val();

  if(PD_Auth_URL){
    //testforlogin(PD_Auth_URL, PD_Auth_Timestamp, PD_Auth_Signature);
  }
});