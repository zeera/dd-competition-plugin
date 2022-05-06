//Build By Nicolas Wendell, Etechspot.
//Updated 20/7/2018 - Bootstrap 4 pagination
//Version 5 

function etech_ajaxSearch(identity) {
	this.ajaxurl = "";
	this.requestarray = {};
	this.sort_by = "";
	this.sort_order = "";
	this.page = 1;
	this.resultsperpage = 15;
	this.numberofresults = 0;
	this.filterlistclassid = "filterlist";
	this.searchfieldnames = {};
	this.boxarray = [];
	this.selfidentity = identity;
	this.progressbarid = identity + 'progressbar';
	this.progressboxid = identity + 'progressbarbox';
	this.ajaxpaginateid = identity + 'paginate';
	this.returntabledata = identity + 'data';
	this.autocompleteids = {};

	// this is the ajax request
	this.ajaxreq = "";

	// On pressing back, we reset some options
	jQuery(window).bind("pageshow", function() {
        jQuery('select option').prop('selected', function() {
            return this.defaultSelected;
        });
    });
}

etech_ajaxSearch.prototype.addAutocomplete = function(autoarray) {
	var self = this;
	// Create autocomplete for the three top fields

	// Initialize ajax autocompletes:
	if (typeof autoarray != 'undefined') {
		for ( var k in autoarray) {
			// use hasOwnProperty to filter out keys from the Object.prototype
			if (autoarray.hasOwnProperty(k)) {
				jQuery("input[id='" + k + "']").autocomplete( {
					serviceUrl : autoarray[k]['url'],
					params : {
						additional : autoarray[k]['additional']
					},
					onSelect : function(suggestion) {
						self['showUser']();
						// if (jQuery.fn.validateAddress){
						// validateAddress(jQuery(this).attr('id'));
						// }
					}
				});
			}
		}
	}

};

etech_ajaxSearch.prototype.toTitleCase = function(str) {
	return str.replace(/\w\S*/g, function(txt) {
		return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
	});
};

// This etech_ajaxSearch.prototype.calls all the items currently in the request
// array, parses them
// and creates a toggleable list of current filters being applied. It handes
// objects starting with box_
// differently stripping the tag etc.
etech_ajaxSearch.prototype.updateFilters = function() {
	if (document.getElementById(this.filterlistclassid) !== null) {
		// Clearing the Filters
		var myNode = document.getElementById(this.filterlistclassid);
		while (myNode.firstChild) {
			myNode.removeChild(myNode.firstChild);
		}
		// Creating the new Filter
		var keys = [];
		for ( var key in this.requestarray) {
			if (this.requestarray.hasOwnProperty(key))
				keys.push(key);
		}
		var newcontent = '';
		for ( var i = 0; i < keys.length; i++) {
			if (this.requestarray[keys[i]] != "") {
				var id = keys[i];
				var idcheck = id.split("box_");
				if (idcheck.length > 1) {

					id = idcheck[idcheck.length - 1];
					id = id.replace('_', ' ');
					id = this.toTitleCase(id);
					newcontent = newcontent
							+ "<div class='activefilterbox' id='filter:"
							+ keys[i] + "' onclick='" + this.selfidentity
							+ ".removeFilter(this)'> <a class='btn btn-success btn-lg' href='#' >" + id
							+ ":<br /> ";
					for ( var m = 0; m < this.requestarray[keys[i]].length; m++) {
						// console.log(m);
						var tempvalue = this.requestarray[keys[i]][m];
						tempvalue = tempvalue.split('#');
						newcontent = newcontent + tempvalue[0] + "<br />";
					}

					newcontent = newcontent + "</a></div>&nbsp;&nbsp";
				} else {
					id = id.replace('_', ' ');
					id = this.toTitleCase(id);

					newcontent = newcontent
							+ "<div class='activefilterbox' id='filter:"
							+ keys[i] + "' onclick='" + this.selfidentity
							+ ".removeFilter(this)'> <a class='btn btn-success btn-lg' href='#' >" + id + ": "
							+ this.requestarray[keys[i]]
							+ "&nbsp;&nbsp;&nbsp;<b>X</b> </a></div>&nbsp;&nbsp";
				}
			}

		}

		if (newcontent == '') {
			newcontent = '&nbsp;';
			this.ajaxPaginate(1, false);
		}
		myNode.innerHTML = newcontent;
	}
};

etech_ajaxSearch.prototype.sortBy = function(name) {
	if (this.sort_by.length > 0)
		jQuery("#" + this.sort_by + ".result-table-header").removeClass("desc asc");
	if (this.sort_by === name) {
		if (this.sort_order == 'ASC')
			this.sort_order = 'DESC';
		else
			this.sort_order = 'ASC';
	} else {
		this.sort_order = 'ASC';
	}

	if (this.sort_order == 'ASC') {
		jQuery("#" + name + ".result-table-header").addClass("asc");
	} else {
		jQuery("#" + name + ".result-table-header").addClass("desc");
	}
	this.sort_by = name;
	this.showUser(false);
};

etech_ajaxSearch.prototype.ajaxPaginate = function(number, show) {
	show = typeof show !== 'undefined' ? show : true;

	if (show == true) {
		this.page = number;
		this.showUser(false);
		// console.log("Showing Paginate");
	} else {
		// this.page = number + 1;

		if (jQuery('#' + this.ajaxpaginateid).length > 0) {
			jQuery('#' + this.ajaxpaginateid).empty();
			var newnumber = this.page + 1;

			// So we need to calculate how many tabs based on the number of
			// results vs the results per page
			console.log(this.numberofresults);
			if (this.numberofresults > 0) {
				var tabs = Math
						.ceil(this.numberofresults / this.resultsperpage);

				if (tabs > 10) {
					// three conditions, if page is < 5 then... if page is >
					// tabs-5 otherwise.
					if (number < 6) {
						for ( var i = 1; i <= number + 4; i++) {
							if (i == number) {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item active' onclick='"
												+ this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							} else {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item' onclick='" + this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							}
						}
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item'><a href='javascript:;'>...</a></li>");
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item' onclick='" + this.selfidentity
										+ ".ajaxPaginate(" + tabs
										+ ")'><a class='page-link' href='javascript:;'>" + tabs
										+ "</a></li>");

					} else if (number > (tabs - 5)) {
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item' onclick='" + this.selfidentity
										+ ".ajaxPaginate(" + 1
										+ ")'><a class='page-link' href='javascript:;'>" + 1 + "</a></li>");
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item'><a href='javascript:;'>...</a></li>");
						for ( var i = number - 4; i <= tabs; i++) {
							if (i == number) {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item active' onclick='"
												+ this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							} else {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item' onclick='" + this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							}
						}
					} else {
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item' onclick='" + this.selfidentity
										+ ".ajaxPaginate(" + 1
										+ ")'><a class='page-link' href='javascript:;'>" + 1 + "</a></li>");
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item'><a href='javascript:;'>...</a></li>");
						for ( var i = number - 4; i <= number + 4; i++) {
							if (i == number) {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item active' onclick='"
												+ this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							} else {
								jQuery('#' + this.ajaxpaginateid).append(
										"<li class='page-item' onclick='" + this.selfidentity
												+ ".ajaxPaginate(" + i
												+ ")'><a class='page-link' href='javascript:;'>" + i
												+ "</a></li>");
							}
						}
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item'><a href='javascript:;'>...</a></li>");
						jQuery('#' + this.ajaxpaginateid).append(
								"<li class='page-item' onclick='" + this.selfidentity
										+ ".ajaxPaginate(" + tabs
										+ ")'><a class='page-link' href='javascript:;'>" + tabs
										+ "</a></li>");

					}
				} else {
					for ( var i = 1; i <= tabs; i++) {
						if (i == number) {
							jQuery('#' + this.ajaxpaginateid).append(
									"<li class='page-item active' onclick='"
											+ this.selfidentity
											+ ".ajaxPaginate(" + i
											+ ")'><a class='page-link' href='javascript:;'>" + i
											+ "</a></li>");
						} else {
							jQuery('#' + this.ajaxpaginateid).append(
									"<li class='page-item' onclick='" + this.selfidentity
											+ ".ajaxPaginate(" + i
											+ ")'><a class='page-link' href='javascript:;'>" + i
											+ "</a></li>");
						}
					}
				}
				jQuery('#' + this.ajaxpaginateid).append(
						"<li style='margin-left:20px'>" + this.numberofresults
								+ " Results</li>");

			} else {
				for ( var i = 1; i < number; i++) {
					jQuery('#' + this.ajaxpaginateid).append(
							"<li class='page-item' onclick='" + this.selfidentity
									+ ".ajaxPaginate(" + i + ")'><a class='page-link' href='javascript:;'>"
									+ i + "</a></li>");
				}
				jQuery('#' + this.ajaxpaginateid).append(
						"<li class='page-item active' onclick='" + this.selfidentity
								+ ".ajaxPaginate(" + number + ")'><a class='page-link' href='javascript:;'>"
								+ number + "</a></li>");
				jQuery('#' + this.ajaxpaginateid)
						.append(
								"<li class='page-item' onclick='" + this.selfidentity
										+ ".ajaxPaginate(" + newnumber
										+ ")'><a class='page-link' href='javascript:;'>&raquo;</a></li>");
			}

			// this.page = number;
		}
		// if (show == true)
		// this.showUser();
		// showUser();
	}
};

// Update the id of the input to match the id of the passed value.
etech_ajaxSearch.prototype.alterId = function(data) {
	// this.page = 1; //resetting the page pagination purposes
	// jQuery("#ajax_last_name").remove()
	var curid = document.getElementsByName(data.id)[0];
	var object = jQuery("input[id='" + curid.id + "']");
	if (typeof (object.autocomplete) === 'function') {
		//TODO: this errors under the most recent jquery
		//object.autocomplete("disable"); 
	}

	document.getElementsByName(data.id)[0].id = data.value;

	if (data.value in this.requestarray) {
		document.getElementById(data.value).value = this.requestarray[data.value];
	} else {
		document.getElementById(data.value).value = '';
	}

	// checking if altered item should be an autocompleted item if so activate
	// and alter the autocomplete.

	if (jQuery.inArray(data.value, this.autocompleteids) > -1) {
		jQuery("input[id='" + data.value + "']").autocomplete('setOptions', {
			params : {
				additional : data.value
			}
		});
		jQuery("input[id='" + data.value + "']").autocomplete("clearCache");
		jQuery("input[id='" + data.value + "']").autocomplete("enable");
	}
	document.getElementsByName(data.id)[0].id = data.value;
};

etech_ajaxSearch.prototype.ajaxSuccess = function(data) {
	document.getElementById(this.progressbarid).style.width = "80%";

	var IS_JSON = true;
	try {
		var json = jQuery.parseJSON(data);
	} catch (err) {
		IS_JSON = false;
		console.log("NOT JSON"); 
		console.log(err);
	}

	if (IS_JSON == true) {
		var json = jQuery.parseJSON(data);
		//console.log(data);
		document.getElementById(this.returntabledata).innerHTML = json['tabledata'];
		this.numberofresults = json['result_count'];
	} else {
		document.getElementById(this.returntabledata).innerHTML = data;
	}

	// Hide Progress Bar
	document.getElementById(this.progressboxid).style.display = "none";
	this.ajaxreq = "";
};

// Shows the main tables with currently calcuated values
etech_ajaxSearch.prototype.showUser = function(paginatereset) {
	paginatereset = typeof paginatereset !== 'undefined' ? paginatereset : true;
	this.numberofresults = 0;
	if (paginatereset == true) {
		this.page = 1;
	}

	// Testing to see if we need to do this based of the ajaxurl variable
	if (typeof this.ajaxurl != 'undefined') {

		// Starting the progress bar
		document.getElementById(this.progressboxid).style.display = "block";
		document.getElementById(this.progressbarid).style.width = "20%";

		// killing any previous ajax request
		if (this.ajaxreq != "") {
			this.ajaxreq.abort();
			this.ajaxreq = "";
		}

		// updating the result list data clearing first
		// list_result_list.clear();

		// Getting the most recent search results
		for ( var k in this.searchfieldnames) {
			// use hasOwnProperty to filter out keys from the Object.prototype
			if (this.searchfieldnames.hasOwnProperty(k)) {
				var userdata = jQuery('[name="' + this.searchfieldnames[k] + '"]');
				this.requestarray[userdata.attr('id')] = userdata.val();
				// console.log(userdata.attr('id'));
			}
		}

		var keys = [];
		for ( var key in this.requestarray) {
			if (this.requestarray.hasOwnProperty(key))
				keys.push(key);
		}

		var passdata = {};

		for ( var i = 0; i < keys.length; i++) {

			if (this.requestarray[keys[i]] != "") {
				var testid = keys[i].split("box_");

				if (testid.length > 1) {
					passdata[testid[testid.length - 1]] = new Array();
					for ( var x = 0; x < this.requestarray[keys[i]].length; x++) {
						// console.log(this.requestarray[keys[i]][x]);
						passdata[testid[testid.length - 1]]
								.push(this.requestarray[keys[i]][x]);
					}

				} else {
					var id = keys[i].split("auto_");
					passdata[id[id.length - 1]] = this.requestarray[keys[i]];
				}
			}
		}
		if (this.sort_by.length > 0)
			passdata['sort_by'] = this.sort_by;
		passdata['sort_order'] = this.sort_order;
		passdata['page_number'] = this.page;

		document.getElementById(this.progressbarid).style.width = "40%";

		console.log(this.ajaxurl);

		this.ajaxreq = jQuery.ajax( {
			cache : false,
			type : 'POST',
			context : this,
			url : this.ajaxurl,
			data : passdata,
			dataType : 'text',
			success : function(data) {
				this.ajaxSuccess(data);
				this.ajaxPaginate(this.page, false);
			},
			complete : this.updateFilters()
		});
	}
};
// Removes currently active filters (incomplete function)
etech_ajaxSearch.prototype.removeFilter = function(data) {
	// console.log(data.id);
	// this.page = 1; //resetting the page pagination purposes
	var id = data.id.split(":");
	this.requestarray[id[1]] = "";
	var item = document.getElementById(id[1]);
	var idcheck = id[1].split("box_");
	if (idcheck.length > 1) {

		for ( var y = 0; y < item.options.length; y++) {
			item.options[y].selected = false;
		}
	} else {
		if (item != null) {
			item.value = "";
		}
	}
	this.showUser();
};

// Updates the filters for the additional filters.
etech_ajaxSearch.prototype.updateAdditionalFilters = function() {
	for ( var x = 0; x < this.boxarray.length; x++) {
		var boxitem = document.getElementById(this.boxarray[x]);
		this.requestarray[boxitem.id] = new Array();

		// console.log(boxitem.options);
		for ( var y = 0; y < boxitem.options.length; y++) {
			if (boxitem.options[y].selected) {
				this.requestarray[boxitem.id].push(boxitem.options[y].value);
				// boxitem.options[y].selected = false;
			}
		}
	}
	this.showUser();
};

