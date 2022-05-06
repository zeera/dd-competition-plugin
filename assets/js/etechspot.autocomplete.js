// Generic autocomplete for non ajaxsearch items
jQuery(function() {
	// Initialize ajax autocompletes:
	if (typeof autocompletearray != 'undefined') {
		for ( var k in autocompletearray) {
			// use hasOwnProperty to filter out keys from the Object.prototype
			if (autocompletearray.hasOwnProperty(k)) {
				jQuery("input[id='" + k + "']").autocomplete( {
					serviceUrl : autocompletearray[k]['url'],
					params : {
						additional : autocompletearray[k]['additional']
					},
					onSelect : function(suggestion) {
						var localid = jQuery(this).attr('id');

						//If there is a 
						if (typeof validateAddress == 'function') {

							jQuery(this).val(suggestion.value);

							validateAddress(jQuery(this));
						}
						if (typeof autocompletearray[localid]['onSelect'] == 'function'){
							jQuery(this).val(suggestion.value);
							autocompletearray[k]['onSelect']();
						}
					}
				});
			}
		}
	}

});
