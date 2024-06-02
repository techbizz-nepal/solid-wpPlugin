jQuery(document).ready(function ($) {
    const form = document.getElementById('ajax-form');
    const errorDiv = document.getElementById('error');

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const url = form.dataset.url
        const params = new URLSearchParams(new FormData(form));

        fetch(url, {
            method: 'POST',
            body: params,
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        const data = JSON.parse(text).data
                        throw new Error(data.message)
                    })
                } else {
                    return response.json();
                }
            })
            .then(({data}) => {
                errorDiv.style.color = 'green'
                errorDiv.innerHTML = data.result
            })
            .catch(error => {
                errorDiv.style.color = 'red'
                errorDiv.innerHTML = error.message
            })
    })
});