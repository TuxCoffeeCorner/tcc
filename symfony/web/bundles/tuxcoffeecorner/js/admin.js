function feedbackHandler(feedback) {
	if ($(".modal").hasClass("in"))
		$(".modal-errors").html(feedback);
	else
		$(".content").prepend(feedback);
}

function getCurrentTimestamp() {
	var dateObj = new Date();

	var month = dateObj.getMonth() +1;
	var day = dateObj.getDate();
	var year = dateObj.getFullYear();
	
	var hours = dateObj.getHours();
	var minutes = dateObj.getMinutes();
	var seconds = dateObj.getSeconds();
	
	var date = year	+ '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day + ' ' + hours + ':' + minutes + ':' + seconds;
	return date;
}


/********************************************
 * Config
 * *****************************************/

// link change value
$("#config").on("click", "a", function(event) {
	event.preventDefault();
	
	var name = $(this).closest("tr").attr("id");
	
	fqn = "/admin/config/get/" + name;
	$.get(fqn, function(data) {
		if (data.success) {
			var value = prompt("Enter value:", data.data['value']);
			
			if (value != null) {
				$.post("/admin/config/set/" + name , {value: value}, function(data) {
					feedbackHandler(data.message);
					$("#config").load("/admin/config/get/");
				}, "json");
			}
		} else {
			feedbackHandler(data.data);
		}
	});
});


/********************************************
 * Customers
 * *****************************************/

// reload active customer table
function sortActiveCustomers() {
	if ($("#active .sort-credit span").hasClass("glyphicon-minus")) {
		if ($("#active .sort-name span").hasClass("glyphicon-chevron-up")) {
			$("#customer-active").load("/admin/customers/get/active/name/asc");
		} else {
			$("#customer-active").load("/admin/customers/get/active/name/desc");
		}
	} else {
		if ($("#active .sort-credit span").hasClass("glyphicon-chevron-up"))
			$("#customer-active").load("/admin/customers/get/active/credit/asc");
		else
			$("#customer-active").load("/admin/customers/get/active/credit/desc");
	}
}

// reload inactive customer table
function sortInactiveCustomers() {
	if ($("#inactive .sort-credit span").hasClass("glyphicon-minus")) {
		if ($("#inactive .sort-name span").hasClass("glyphicon-chevron-up"))
			$("#customer-inactive").load("/admin/customers/get/inactive/name/asc");
		else
			$("#customer-inactive").load("/admin/customers/get/inactive/name/desc");
	} else {
		if ($("#inactive .sort-credit span").hasClass("glyphicon-chevron-up"))
			$("#customer-inactive").load("/admin/customers/get/inactive/credit/asc");
		else
			$("#customer-inactive").load("/admin/customers/get/inactive/credit/desc");
	}
}

// button customer change state
$(".customers").on("click", "button", function() {
	var id = $(this).closest("tr").attr("id");
	$.post("/admin/customers/changestate/" + id, function(data) {
		feedbackHandler(data.message);
		sortActiveCustomers();
		sortInactiveCustomers();
	}, "json");
});

// link customer charge
$(".customers").on("click", ".btn-customer-charge", function(event) {
	event.preventDefault();
	var charge = prompt("Enter a value that should be charged:", "0");
	
	if (charge != null) {
		var id = $(this).closest("tr").attr("id");
		var type = $(this).closest("tbody").attr("id");
		
		$.post("/admin/customers/charge/" + id + "/" + charge, function(data) {
			feedbackHandler(data.message);
			sortActiveCustomers();
			sortInactiveCustomers();
		}, "json");
	}
});

// tablecell customer sort by name
$(".sort-name").click(function() {
	$("span", this).removeClass("glyphicon-minus");
	
	if ($("span", this).hasClass("glyphicon-chevron-up") || $("span", this).hasClass("glyphicon-chevron-down"))
		$("span", this).toggleClass("glyphicon-chevron-up glyphicon-chevron-down");
	else
		$("span", this).addClass("glyphicon-chevron-up");
	
	var order = "desc";
	if ($("span", this).hasClass("glyphicon-chevron-up"))
		order = "asc";

	var active = $(this).closest(".tab-pane").attr("id");
	
	$("#" + active + " .sort-credit span").removeClass("glyphicon-chevron-down glyphicon-chevron-up").addClass("glyphicon-minus");
	
	$("#customer-" + active).load("/admin/customers/get/" + active + "/name/" + order);
});

// tablecell customer sort by credit
$(".sort-credit").click(function(event) {
	$("span", this).removeClass("glyphicon-minus");
	
	if ($("span", this).hasClass("glyphicon-chevron-up") || $("span", this).hasClass("glyphicon-chevron-down"))
		$("span", this).toggleClass("glyphicon-chevron-up glyphicon-chevron-down");
	else
		$("span", this).addClass("glyphicon-chevron-up");
	
	var order = "desc";
	if ($("span", this).hasClass("glyphicon-chevron-up"))
		order = "asc";

	var active = $(this).closest(".tab-pane").attr("id");
	
	$("#" + active + " .sort-name span").removeClass("glyphicon-chevron-down glyphicon-chevron-up").addClass("glyphicon-minus");
	
	$("#customer-" + active).load("/admin/customers/get/" + active + "/credit/" + order);
});


/********************************************
 * Products
 * *****************************************/

// trigger the real file input element when fake input is clicked
$("#image-fake-input").click(function() {
	$("#image-hidden-input").click();
});

// display filename in fake input
$("#image-hidden-input").change(function() {
	$("#image-fake-input").val($("#image-hidden-input").val());
});

// button image upload
$("#image-form").submit(function(event) {
	event.preventDefault();
	var formData = new FormData($(this)[0]);
	
    $.ajax({
        url: "/admin/products/image/add/",
        type: "POST",
        success: function(data) {
        	$("#image-input").val("");
        	$("#image-hidden-input").val("");
        	feedbackHandler(data.message);
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
});

// button product add
$("#btn-product-add").click(function() {
	$(".modal-errors").html("");
	
	var fqn = "/admin/products/image/get/";
	$.get(fqn, function(data) {
		$("#product-image").html(data);
		$("#product-image").prepend($('<option></option>').val("no_image.jpg").html("no_image.jpg"));
	});
	
	$("#product-header").text("Add Product");
	$("#product-barcode").val("");
	$("#product-name").val("");
	$("#product-price").val("");
	$("#product-form").attr("action","/admin/products/add/");
	
	$("#product-image").val($("#product-image option:first").val());
});

// button product edit
$("#products").on("click", ".btn-product-edit", function() {
	$(".modal-errors").html("");
	
	var id = $(this).closest("tr").attr("id");
	$("#product-form").attr("action", "/admin/products/edit/" + id);
	
	var fqn = "/admin/products/image/get/";
	$.get(fqn, function(data) {
		$("#product-image").html(data);
		$("#product-image").prepend($('<option></option>').val("no_image.jpg").html("no_image.jpg"));
		
	});
	
	fqn = "/admin/products/get/" + id;
	$.get(fqn, function(data) {
		if (data.success) {
			$("#product-header").text("Edit: " + data.data['name']);
			$("#product-barcode").val(data.data['barcode']);
			$("#product-name").val(data.data['name']);
			$("#product-price").val(data.data['price']);
			$("#product-image").val(data.data['image']);
		} else {
			$("#btn-product-add").click();
			feedbackHandler(data.data);
		}

	});
});

// button product delete
$("#products").on("click", ".btn-product-del", function(event) {
	event.preventDefault();
	if (confirm("Are you sure?")) {
		var id = $(this).closest("tr").attr("id");
		$.post("/admin/products/delete/" + id, function(data) {
			feedbackHandler(data.message);
			$("#products").load("/admin/products/get/");
		}, "json");
	}
});

// button product save
$("#btn-product-submit").click(function() {
	$.post($("#product-form").attr("action"), $("#product-form").serialize(), function(data) {
		if (data.success)
			$('#product-modal').modal('hide');
		feedbackHandler(data.message);
		$("#products").load("/admin/products/get/");
	}, "json");
});

// initialize popover for default image information
if ($(".image-information").length) {
	$(".image-information").popover();
}


/********************************************
 * Charitys 
 * *****************************************/

// trigger the real file input element when fake input is clicked
$("#image-fake-input").click(function() {
	$("#image-hidden-input").click();
});

// display filename in fake input
$("#image-hidden-input").change(function() {
	$("#image-fake-input").val($("#image-hidden-input").val());
});

// button image upload
$("#image-form").submit(function(event) {
	event.preventDefault();
	var formData = new FormData($(this)[0]);
	
    $.ajax({
        url: "/admin/charitys/image/add/",
        type: "POST",
        success: function(data) {
        	$("#image-input").val("");
        	$("#image-hidden-input").val("");
        	feedbackHandler(data.message);
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
});

// button charity add
$("#btn-charity-add").click(function() {
	$(".modal-errors").html("");
	
	var fqn = "/admin/charitys/image/get/";
	$.get(fqn, function(data) {
		$("#charity-image").html(data);
		$("#charity-image").prepend($('<option></option>').val("no_image.jpg").html("no_image.jpg"));
	});
	
	$("#charity-header").text("Add Charity");
	$("#charity-barcode").val("");
	$("#charity-organisation").val("");
	$("#charity-beginn").val("");
	$("#charity-ende").val("");
	$("#charity-form").attr("action","/admin/charitys/add/");
	$("#charity-image").val($("#charity-image option:first").val());
});

// button charity edit
$("#charitys").on("click", ".btn-charity-edit", function() {
	$(".modal-errors").html("");
	
	var id = $(this).closest("tr").attr("id");
	$("#charity-form").attr("action", "/admin/charitys/edit/" + id);
	
	var fqn = "/admin/charitys/image/get/";
	$.get(fqn, function(data) {
		$("#charity-image").html(data);
		$("#charity-image").prepend($('<option></option>').val("no_image.jpg").html("no_image.jpg"));
		
	});
	
	fqn = "/admin/charitys/get/" + id;
	$.get(fqn, function(data) {
		if (data.success) {
			$("#charity-header").text("Edit: " + data.data['name']);
			$("#charity-barcode").val(data.data['barcode']);
			$("#charity-organisation").val(data.data['organisation']);
			$("#charity-beginn").val(data.data['beginn']);
			$("#charity-ende").val(data.data['ende']);
			$("#charity-image").val(data.data['image']);
		} else {
			$("#btn-charity-add").click();
			feedbackHandler(data.data);
		}

	});
});

// button charity delete
$("#charitys").on("click", ".btn-charity-del", function(event) {
	event.preventDefault();
	if (confirm("Are you sure?")) {
		var id = $(this).closest("tr").attr("id");
		$.post("/admin/charitys/delete/" + id, function(data) {
			feedbackHandler(data.message);
			$("#charitys").load("/admin/charitys/get/");
		}, "json");
	}
});


// button charity reset
$("#charitys").on("click", ".btn-charity-reset", function(event) {
	event.preventDefault();
	if (confirm("Reset Spendenstand?")) {
		var id = $(this).closest("tr").attr("id");
		$.post("/admin/charitys/reset/" + id, function(data) {
			feedbackHandler(data.message);
			$("#charitys").load("/admin/charitys/get/");
		}, "json");
	}
});


// button charity save
$("#btn-charity-submit").click(function() {
	$.post($("#charity-form").attr("action"), $("#charity-form").serialize(), function(data) {
		if (data.success)
			$('#charity-modal').modal('hide');
		feedbackHandler(data.message);
		$("#charitys").load("/admin/charitys/get/");
	}, "json");
});

// initialize popover for default image information
if ($(".image-information").length) {
	$(".image-information").popover();
}

/********************************************
 * Mails
 * *****************************************/

// button mail add
$("#btn-mail-add").click(function() {
	$(".modal-errors").html("");
	
	$("#mail-header").text("Add Mail");
	$("#mail-identifier").val("");
	$("#mail-subject").val("");
	$("#mail-body").val("");
	$("#mail-to").val("");
	$("#mail-cc").val("");
	$("#mail-from").val("");
	$("#mail-form").attr("action", "/admin/mails/add/");
});

// button mail edit
$("#mails").on("click", ".btn-mail-edit", function() {
	$(".modal-errors").html("");
	
	var id = $(this).closest("tr").attr("id");
	var fqn = "/admin/mails/get/" + id;
	$("#mail-form").attr("action", "/admin/mails/edit/" + id);
	
	$.get(fqn, function(data) {
		if (data.success) {
			$("#mail-header").text("Edit entry of " + data.data['identifier']);
			$("#mail-identifier").val(data.data['identifier']);
			$("#mail-subject").val(data.data['subject']);
			$("#mail-body").val(data.data['body']);
			$("#mail-to").val(data.data['to']);
			$("#mail-cc").val(data.data['cc']);
			$("#mail-from").val(data.data['from']);
		} else {
			$("#btn-mail-add").click();
			feedbackHandler(data.data);
		}
	});
});

// button mail delete
$("#mails").on("click", ".btn-mail-delete", function() {
	if (confirm("Are you sure?")) {
		var id = $(this).closest("tr").attr("id");
		$.post("/admin/mails/delete/" + id, function(data) {
			feedbackHandler(data.message);
			$("#mails").load("/admin/mails/get/");
		}, "json"); 
	}
});

// button mail send
$("#mails").on("click", ".btn-mail-send", function() {
	var recipient = prompt("Enter the recipient:", "");
	if (recipient != "" && recipient != null) {
		var id = $(this).closest("tr").attr("id");
		var fqn = "/admin/mails/send/" + id + "/" + recipient;
		$.post(fqn, function(data) {
			feedbackHandler(data.message);
		}, "json");
	}
});

//button mail submit
$("#btn-mail-submit").click(function() {
	$.post($("#mail-form").attr("action"), $("#mail-form").serialize(), function(data) {
		if (data.success)
			$('#mail-modal').modal('hide');
		feedbackHandler(data.message);
		$("#mails").load("/admin/mails/get/");
	}, "json");
});


/********************************************
 * Vault
 * *****************************************/

// button vault add
$("#btn-vault-add").click(function() {
	$(".modal-errors").html("");
	
	$("#vault-header").text("Add Vault entry");
	$("#vault-timestamp").val(getCurrentTimestamp());
	$("#vault-input").val("");
	$("#vault-outtake").val("");
	$("#vault-comment").val("");
	$("#vault-cashier").val($("#remoteUser").text());
	$("#vault-form").attr("action","/admin/vault/add/");
});

// button vault edit
$("#vault").on("click", ".btn-vault-edit", function() {
	$(".modal-errors").html("");
	
	var id = $(this).closest("tr").attr("id");
	var fqn = "/admin/vault/get/" + id;
	$("#vault-form").attr("action", "/admin/vault/edit/" + id);
	
	$.get(fqn, function(data) {
		if (data.success) {
			$("#vault-header").text("Edit entry of " + data.data['timestamp']);
			$("#vault-timestamp").val(data.data['timestamp']);
			$("#vault-input").val(data.data['input']);
			$("#vault-outtake").val(data.data['outtake']);
			$("#vault-comment").val(data.data['comment']);
			$("#vault-cashier").val(data.data['cashier']);
		} else {
			$("#btn-vault-add").click();
			feedbackHandler(data.data);
		}
	});
});

// button vault delete
$("#vault").on("click", ".btn-vault-del", function() {
	if (confirm("Are you sure?")) {
		var id = $(this).closest("tr").attr("id");
		$.post("/admin/vault/delete/" + id, function(data) {
			feedbackHandler(data.message);
			$("#vault").load("/admin/vault/get/");
		}, "json");
	}
});

// button vault submit
$("#btn-vault-submit").click(function() {
	$.post($("#vault-form").attr("action"), $("#vault-form").serialize(), function(data) {
		if (data.success)
			$('#vault-modal').modal('hide');
		feedbackHandler(data.message);
		$("#vault").load("/admin/vault/get/");
	}, "json");
});

$("#vault-comment-list li a").click(function(event) {
	event.preventDefault();
	$("#vault-comment").val($(this).text());
	$("#vault-comment").focus();
});

$("#vault-cashier-list li a").click(function(event) {
	event.preventDefault();
	$("#vault-cashier").val($(this).text());
	$("#vault-cashier").focus();
});
