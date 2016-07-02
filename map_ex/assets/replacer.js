/*
This code is used for replacements of data in string, usually after $translate service
*/
"use strict";
/*
data - original string
expression - associative array, key => value.
The function replaces all occurances of %key% in data into value.
Returns the string with all replacements done.
*/
function replaceTransAll(data,expression){
	for (var key in expression)
		data=data.replace('%'+key+'%',expression[key]);
	return data;
}