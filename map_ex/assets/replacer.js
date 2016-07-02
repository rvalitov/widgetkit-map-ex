/*
This code is used for replacements of data in string, usually after $translate service
*/
"use strict";

function replaceTransAll(data,expression){
	for (var key in expression)
		data=data.replace('%'+key+'%',expression[key]);
	return data;
}