/**
 * 基础js方法
 */

/**
 * 获取接口地址
 */
function api(api_name) {
	$api_arr = api_name.split('/');
    return api_path + '?c=' + $api_arr[0] + '&m=' + $api_arr[1];
}

/**
 * 获取语言包地址
 */
function lang(api_name) {
    return view_path + 'lang/' + langBase + '/' + api_name + '.json';
}


function console_log(data, title) {
	if (debug) {
		if (title) {
			console.log("=========="+title+"==========");
		}
		console.log(data);
	}
}


function is_int(obj) {
	var int = /^[0-9]*$/;
	return !!int.exec(obj);
}