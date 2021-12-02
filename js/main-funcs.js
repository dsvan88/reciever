async function useFetchApi({ url = '/switcher.php', type = "json", data = '' } = {}) {
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
    commonActionHandler: async function (event) {
        const target = event.target.closest('[data-action]');
        const action = camelize(target.dataset.action);

        if (typeof this[action] != 'undefined')
            this[action](event);
        else {
            const formData = new FormData();
            for (let [key, value] of Object.entries(target.dataset)) {
                if (key === 'action') {
                    if (value.endsWith('-form'))
                        formData.append('need', `form_${value.replace(/-form$/, '')}`);
                    else if (value.startsWith('get-'))
                        formData.append('need', value.replace(/-/, '_'));
                    else
                        formData.append('need', `do_${value}`);
                }
                else {
                    formData.append(key, value);
                }
            }
            if (target.dataset.action.endsWith('-form')) {
                const modal = this.commonFormEventStart();
                const data = await useFetchApi({ 'data': formDataToJson(formData) });
                this.commonFormEventEnd({ modal, data, formSubmitAction: action+'Submit' });
            }
            else {
                const modal = this.commonFormEventStart();
                const data = await useFetchApi({ data: formDataToJson(formData)});
                this.commonFormEventEnd({modal, data});
            }
        }
    },
    userLogOut: async function (event) {
        const data = await useFetchApi({ 'data': '{"need":"do_user-log-out"}' });
        window.location = window.location.href;
    },
    commonFormEventStart: function (event) {
        return new ModalWindow();
    },
    commonFormEventEnd: function ({modal, data, formSubmitAction, ...args}) {
        let modalWindow;
        if (data['error'] === 0)
            modalWindow = modal.fillModalContent(data);
        else
            modalWindow = modal.fillModalContent({ html: data['html'], title: 'Error!', buttons: [{ 'text': 'Okay', 'className': 'positive' }] });
        modalWindow.querySelectorAll('*[data-action]').forEach(block => block.addEventListener('click', (event) => this[camelize(block.dataset.action)](event)));
        const form = modalWindow.querySelector('form');
        if (form !== null && formSubmitAction) {
            console.log(formSubmitAction);
            form.addEventListener('submit', (event) => this[formSubmitAction](event, args))
        }
    },
    addFormField: function (event) {
        const oldIntput = event.target.closest('div').querySelector('input');
        const newIntput = oldIntput.cloneNode(true);
        newIntput.value = '';
        
        if (event.target.parentElement.tagName !== 'div')
            event.target.parentElement.before(newIntput);
        else
            event.target.before(newIntput);
    },
    dashboardEvent: async function ([event, need]) {
        if (event.target.closest('*[data-action].dashboard__item').classList.contains('active'))
            return false;
        const data = await useFetchApi({ data: `{"need":"${need}"}` });
        let parentBlock = document.body.querySelector('main');
        parentBlock.outerHTML = data['html'];
        parentBlock = document.body.querySelector('main');

        const blocksWithAction = parentBlock.querySelectorAll('*[data-action]');
        blocksWithAction.forEach(block => block.addEventListener('click', (event) => mainFunc[camelize(block.dataset.action)](event)));

        const blocksWithActionChange = parentBlock.querySelectorAll('*[data-action-change]');
        blocksWithActionChange.forEach(block => block.addEventListener('change', (event) => mainFunc[camelize(block.dataset.actionChange)](event)));

        document.body.querySelector('*[data-action].dashboard__item.active').classList.remove('active');
        event.target.closest('*[data-action].dashboard__item').classList.add('active');
    },
    checkUserChange: function (event) {
        if (event.target.value === 'all') {
            const checkUsers = document.body.querySelectorAll('input[name="check-user"]');
            checkUsers.forEach(checkbox => checkbox.checked = event.target.checked);
        }
        if (document.body.querySelectorAll('input[name="check-user"]:checked').length > 0){
            document.body.querySelector('[data-action="delete-users-array"].action-text-blur').classList.remove('action-text-blur');
        }
        else {
            document.body.querySelector('[data-action="delete-users-array"]').classList.add('action-text-blur');
        }
    },
    dashboardRedirect: function (event) {
        if (event.target.tagName !== 'A') {
            window.location = event.target.closest('li').querySelector('a').href;
        }
    },
    contactDelete: async function (event) {
        if (!confirm(`Are you really wanna to delete contact with id: ${event.target.dataset.cid}`))
            return false;
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data: `{"need":"do_contact-delete","cid":"${event.target.dataset.cid}"}`});
        this.commonFormEventEnd({modal, data});
    },
    messageArchive: async function (event) {
        event.preventDefault();
        const messageId = event.target.closest('div[data-message-id]').dataset.messageId;
        const result = await useFetchApi({ 'data': `{"need":"do_message-archive","mid":"${messageId}"}` });
        alert(result['text']);
        if (result['error'] == '0')
            window.location = window.location.href;
    },
    messageDelete: async function (event) {
        event.preventDefault();
        const messageId = event.target.closest('div[data-message-id]').dataset.messageId;
        const result = await useFetchApi({ 'data': `{"need":"do_message-delete","mid":"${messageId}"}` });
        alert(result['text']);
        if (result['error'] == '0')
            window.location = window.location.href;
    },
    messageEditForm: async function (event) {
        const messageId = event.target.closest('div[data-message-id]').dataset.messageId;
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data: `{"need":"form_message-edit","mid":"${messageId}"}`});
        this.commonFormEventEnd({modal, data, formSubmitAction: 'messageEditFormSubmit'});
    },
    messageEditFormSubmit: async function (event) {
        event.preventDefault();
        let formData = new FormData(event.target);
        formData.append('need', 'do_message-edit');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
        if (result['error'] == '0')
            window.location = window.location.href;
    },
    reSetMainTechData: async function (event) {
        event.preventDefault();
        const form = event.target.closest('form');
        let formData = new FormData(form);
        formData.append('need', 'do_re-set-main-tech-data');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
    },
    notesBlockToggle: async function (event) {
        const notesBlock = event.target.parentElement.querySelector('.messages__notes');
        let savedNotes = event.target.parentElement.querySelector('div.messages__notes-saved');
        let html = '';

        if (!savedNotes) {
            const messageId = event.target.closest('[data-message-id]').dataset.messageId;
            const result = await useFetchApi({ 'data': `{"need":"get_notes-data","mid":"${messageId}"}` });
            
            savedNotes = document.createElement('div');
            savedNotes.className = 'messages__notes-saved';
            notesBlock.after(savedNotes);
            html = result['html'];
        }
        else {
            html = savedNotes.innerHTML;
            savedNotes.innerHTML = notesBlock.innerHTML;
        }
        event.target.innerText = event.target.innerText === '< Show notes >' ? '< Hide notes >' : '< Show notes >'
        notesBlock.innerHTML = html;
    },
    userAddFormSubmit: async function (event, args) {
        event.preventDefault();
        let formData = new FormData(event.target);
        formData.append('need', 'do_user-add');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
        if (result['error'] == '0')
            window.location = window.location.href;
    },
    userPasswordChangeForm: async function (event) {
        event.preventDefault();
        const modal = this.commonFormEventStart();
        const userId = event.target.closest('form').querySelector('input[name=uid]').value;
        const data = await useFetchApi({ 'data': `{"need":"form_user-password-change","uid":"${userId}"}` });
        this.commonFormEventEnd({modal, data, formSubmitAction: 'userPasswordChangeFormSubmit'});
    },
    userPasswordChangeFormSubmit: async function (event) {
        event.preventDefault();
        
        if (!confirm(`Are you really wanna to change user password?`))
            return false;
        
        let formData = new FormData(event.target);
        formData.append('need', 'do_user-password-change');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
    },
    userPasswordReset: async function (event) {
        event.preventDefault();
        
        if (!confirm(`Are you really wanna to reset user password?
(A new temporary password will be sent to the user on the first email)`))
            return false;
        const form = event.target.closest('form');
        const userId = form.querySelector('input[name=uid]').value;
        const result = await useFetchApi({ 'data': `{"need":"do_user-password-reset","uid":"${userId}"}` });
        alert(result['text']);
    },
    userDelete: async function (event) {
        const userId = event.target.closest('tr').dataset.uid;
        if (!confirm(`Are you really wanna to delete user with id: ${userId}`))
            return false;
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data: `{"need":"do_user-delete","uid":"${userId}"}`});
        this.commonFormEventEnd({modal, data});
    },
    usersDeleteArray: async function (event) {
        const checkboxes = document.body.querySelectorAll('input[name="check-user"]:checked');
        if (checkboxes.length === 0) return;
        const values = [];
        checkboxes.forEach(checkbox => values.push(checkbox.value));

        if (!confirm(`Are you really wanna to delete user with ids: ` + values.join(', ')))
            return false;
        
        const formData = new FormData();
        formData.append('need', 'do_users-array-delete');
        formData.append('ids', values);
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data: formDataToJson(formData) });
        this.commonFormEventEnd({modal, data});
    },
    userEditForm: async function (event) {
        const userId = event.target.closest('*[data-uid]').dataset.uid;
        const modal = this.commonFormEventStart();
        const data = await useFetchApi({ data: `{"need":"form_user-edit","uid":"${userId}"}`});
        this.commonFormEventEnd({modal, data, formSubmitAction: 'userEditFormSubmit'});
    },
    userEditFormSubmit: async function (event) {
        event.preventDefault();
        let formData = new FormData(event.target);
        formData.append('need', 'do_user-edit');
        const result = await useFetchApi({ 'data': formDataToJson(formData) });
        alert(result['text']);
        if (result['error'] == '0')
            window.location = window.location.href;
    },
};

function formDataToJson(data) {
    const object = {};
    data.forEach((value, key) => {
        value = value.replace("'", '’');
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