async function useFetchApi({ url = '/switcher.php?', type = "json", data = '' } = {}) {
    const options = {};
    if (data !== '') {
        options.body = data;
        options.headers = {
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
    commonFormEventStart: function (event) {
        return new ModalWindow();
    },
    commonFormEventEnd: function ({modal, data, formSubmitAction}) {
        let modalWindow;
        if (data['error'] === 0)
            modalWindow = modal.fillModalContent(data);
        else
            modalWindow = modal.fillModalContent({ html: data['html'], title: 'Error!', buttons: [{ 'text': 'Okay', 'className': 'positive' }] });
        modalWindow.querySelectorAll('*[data-action]').forEach(block => block.addEventListener('click', (event) => this[camelize(block.dataset.action)](event)));
        const form = modalWindow.querySelector('form');
        if (form !== null && formSubmitAction) {
            form.addEventListener('submit', (event) => this[formSubmitAction](event))
        }
    },
    addUserForm: async function (event) {
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ 'data': `{"need":"form_add-user"}` });
        this.commonFormEventEnd({modal, data, formSubmitAction: 'addUserFormSubmit'});
    },
    addUserFormSubmit: async function (event) {
        event.preventDefault();
        let formData = new FormData(event.target);
        formData.append('need', 'do_add-user');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
    },
    settingsForm: async function (event) {
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data:'need=form_settings' });
        this.commonFormEventEnd({modal, data});
    },
    addFormField: function (event) {
        const oldIntput = event.target.closest('div').querySelector('input');
        const newIntput = oldIntput.cloneNode(true);
        newIntput.value = '';
        oldIntput.after(newIntput);
    }
};

function formDataToJson(data) {
    const object = {};
    data.forEach((value, key) => {
        if (key.includes('[')) {
			key = key.substr(0, key.indexOf('['));
			if (!object[key])
				object[key] = [];
			object[key][object[key].length] = value;
			return;
        }
        else {
            object[key] = value;
        }
    });
    return JSON.stringify(object);
}
function camelize(str) {
	return str
		.split("-") // разбивает 'my-long-word' на массив ['my', 'long', 'word']
		.map((word, index) => (index == 0 ? word : word[0].toUpperCase() + word.slice(1)))
		.join(""); // соединяет ['my', 'Long', 'Word'] в 'myLongWord'
}