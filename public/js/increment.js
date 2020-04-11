const buttonClick = (event) => {
    event.preventDefault();

    const button = event.currentTarget;
    const id = button.dataset.id;

    if (typeof id !== 'string') {
        // TODO add error
        return;
    }

    const encodedId = encodeURIComponent(id);
    const formBody = [];

    formBody.push(`id=${encodedId}`);

    const formData = formBody.join('&');

    const options = {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
        },
        body: formData,
    };

    const handleJson = (json) => {
        if (!Object.prototype.hasOwnProperty.call(json, 'data')) {
            // TODO add error
            return;
        }

        if (json.code !== 'ok') {
            // TODO add error
            return;
        }

        const responseWrap = button.parentNode.querySelector('.increment-response');

        if (responseWrap === null) {
            // TODO add error
            return;
        }

        const responseElement = responseWrap.querySelector('.increment-response-value');

        if (responseElement === null) {
            // TODO add error
            return;
        }

        responseElement.textContent = json.data.value;
        responseWrap.style.display = 'initial';
    };

    const handleError = () => {
        // TODO add error
    };

    fetch(incrementLoc.url, options)
        .then((response) => response.json())
        .then(handleJson)
        .catch(handleError);
};

const bindEvent = (button) => {
   button.addEventListener('click', buttonClick);
   console.log(button);
};

const documentReady = () => {
    document.querySelectorAll('.increment-button').forEach(bindEvent);
};

document.addEventListener('DOMContentLoaded', documentReady);