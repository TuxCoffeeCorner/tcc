var timespan = 15000;
var timeout = 0;
var timer;

function exit () {
	item = $(".warning").attr("id");
	
	if (item)
		$.post("/shop/buy/" + item, function() {window.location = "/shop/exit/";});
	else
		window.location = "/shop/exit/";
	
};

var fct_timer = function () {
	var time = timeout - $.now();
	$('#timeout').text(Math.floor(time / 1000));
	if (time < 1000) {
		clearInterval(timer);
		exit();
	} 
};

function partialRender () {
	$.when($.get("/shop/get/ltt/"), $.get("/shop/get/image/")).then(function (lttTable, imgSource) {
		lttTable = lttTable[0];
		imgSource = imgSource[0];
		
		$("#img-img").attr("src", imgSource);
		$("#table-ltt").html(lttTable);
		$("input").val("");
		$("input").focus();
		timeout = $.now() + timespan;
	});
}

$(function() {
	if (window.location.pathname != "/shop/") { // only start timer when location is not /shop/ (entrance)
		timer = setInterval(fct_timer, 1000);
		timeout = $.now() + timespan;
	}
	
	$("input").val("");
	$("input").focus();
});

$(document).unbind().on('keyup','#shop-input', function(event) {

	value = $(this).val();
	event.preventDefault();

	if ( event.which == 13 ) {
		
		$.when($.get("/shop/customer/exists/" + value), $.get("/shop/product/exists/" + value), $.get("/shop/charity/exists/" + value)).then(function (customerExists, productExists, charityExists) {
			customerExists = customerExists[0];
			productExists = productExists[0];
			charityExists = charityExists[0];
			
			item = $(".warning").attr("id");
			
			if (charityExists == "true"){ // charity if

				if ($("#table-ltt").html()){ // customer is logged in
					$.post("/shop/donate/" + value, function(){partialRender();});				
				} else{ // nobody is logged in
					// Charity-Info, Spendenstand
				}
			} else{

				if (customerExists == "true" && productExists == "false") { // customer exists but product does not
					if ($("#table-ltt").html() && item)
						$.post("/shop/buy/" + item, function() {window.location = "/shop/enter/" + value;});
					else
						window.location = "/shop/enter/" + value;
					
				} else if (customerExists == "false" && productExists == "true") { // product exists but customer does not
					if ($("#table-ltt").html())
						$.post("/shop/buy/" + value, function(){partialRender();});
					else
						window.location = "/shop/product/info/" + value;

				} else if (customerExists == "false" && productExists == "false") { // none exists, must be code or invalid barcode
					if ($("#table-ltt").html()) {
						switch(value){
					    case "221":
							$.post("/shop/annulate/", { item: item }, function(){partialRender();});
							break;
							
					    case "222":
							exit();
							break;

						default:
							partialRender();
						}
					} else {
						window.location = "/shop/error/";
					}

				} else if (customerExists == "true" && productExists == "true") { // both exist
					// Houston, we have a problem...
				} else {
					window.location = "/shop/error/";
				}
			} // end charity_if
		});
	}
});
