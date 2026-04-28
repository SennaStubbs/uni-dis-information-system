// Submitting a log in form
var errorText = document.getElementById('error-message');
async function LogIn(event) {
    let form = event.currentTarget.form;
    let formData = new FormData(form);

    let formValidity = form.reportValidity();
    if (formValidity) {
        await fetch(window.location.origin + "/information_system/website/operations/user/log-in", {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.text())
        .then((data) => {
            let errors = data.match(/error:/g) ? data.replace(/error:/g, '\n').trim().replace('\n', '<br>') : "";
            if (errors != "") {
                errorText.innerHTML = errors;
                errorText.classList.remove('hidden');
            }
            else if(data == "success" || data == "logged in") {
                errorText.classList.add('hidden');
                window.location.href = window.location.origin + "/information_system/website/user";
            }
        });
    } else {
        // Focus on the entered input, if submitted with the 'ENTER' key
        if (event.target.tagName.toLowerCase() == "input") {
            event.target.focus();
        }
    }
}

// Checking for 'ENTER' key inputs
var inputs = document.getElementsByTagName('main')[0].getElementsByTagName('input');
for (let input of inputs) {
    input.addEventListener('keypress', function(event) {
        if (event.key == "Enter") {
            event.preventDefault();
            LogIn(event);
        }
    });
}