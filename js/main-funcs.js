async function useFetchApi({ url = '', type = "json", data = ''}) {
    if (!url || !type) return false
    let options = {};
    if (data !== '') {
        options.body = data;
        options.headers = {
            // 'Content-Type': 'multipart/form-data'
            'Content-Type': 'application/json;charset=utf-8',
        };
        options.mode = 'cors';
        options.method = 'POST';
    }
    let response = await fetch(url, options);
    if (response.ok)
        return await response[type]();
    return false;
}

const mainFunc = {

};

function formDataToJson(data) {
    const object = {};
    data.forEach((value, key) => object[key] = value );
    return JSON.stringify(object);
}
function camelize(str) {
	return str
		.split("-") // разбивает 'my-long-word' на массив ['my', 'long', 'word']
		.map((word, index) => (index == 0 ? word : word[0].toUpperCase() + word.slice(1)))
		.join(""); // соединяет ['my', 'Long', 'Word'] в 'myLongWord'
}