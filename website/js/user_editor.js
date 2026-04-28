// Deleting
async function Post_DeleteUser(userId) {
    // Cannot change main admin account
    if (userId == 1) {
        MessagePopup('Error', 'The main administrator account cannot be deleted.');
    }
    else {
        let formData = new FormData();
        formData.append('user_id', userId);

        if (formData) {
            await fetch(window.location.origin + "/information_system/website/operations/user/delete_user", {
                method: 'POST',
                body: formData
            })
            .then((response) => response.text())
            .then((data) => {
                ClosePopup();
                switch (data) {
                    case 'error':
                        MessagePopup('Error', 'Something went wrong when trying to delete User Id ' + userId);
                        break;
                    case 'cannot perform':
                        MessagePopup('Invalid Access Level', 'You do not have access rights to delete User Id ' + userId);
                        break;
                    case 'success':
                        UpdateUsersTable();
                        break;
                }
            });
        }
    }
}

// Editing
let currentlyEditingId = -1; // User ID of current row being edited - only one row can be edited at a time
let defaultEditingValues = {};
function EditUser(userId, defaultValues) {
    if (currentlyEditingId != -1) {
        ConfirmationPopup(
            'Unsaved Changes',
            'This will revert any changes being made to User Id ' + currentlyEditingId + '.', 'cancel_edit', {'userId': currentlyEditingId, 'tryingToEdit_UserId': userId});
        return;
    }

    // Cannot change main admin account
    if (userId == 1) {
        MessagePopup('Error', 'The main administrator account cannot be edited.');
    }
    else {
        currentlyEditingId = userId;
        defaultEditingValues = defaultValues;

        let userRow = document.getElementById('user_row_' + userId);
        let row_UserDisplays = userRow.getElementsByClassName('user-display');
        let row_EditInputs = userRow.getElementsByClassName('edit-input');

        // Hide value displays
        for (row of row_UserDisplays) {
            row.classList.add('hidden');
        }

        // Show edit inputs
        for (row of row_EditInputs) {
            row.classList.remove('hidden');
        }
    }
}

function CancelUserEdit(itemId) {
    let userRow = document.getElementById('user_row_' + itemId);
    let row_UserDisplays = userRow.getElementsByClassName('user-display');
    let row_EditInputs = userRow.getElementsByClassName('edit-input');

    // Show value displays
    for (row of row_UserDisplays) {
        row.classList.remove('hidden');
    }

    // Hide edit inputs
    for (row of row_EditInputs) {
        row.classList.add('hidden');

        console.log(defaultEditingValues['access_level']);

        // Reset values
        if (defaultEditingValues) {
            if (row.classList.contains('user-username')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['username'];
            }
            else if (row.classList.contains('user-password')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['password'];
            }
            else if (row.classList.contains('user-display-name')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['display_name'];
            }
            else if (row.classList.contains('user-access-level')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['access_level'];
            }
        }
    }

    currentlyEditingId = -1;
    defaultEditingValues = {};
}

// Perform edit
async function Post_EditUser(userId) {
    let form = document.getElementById('edit_user_' + userId);
    let formData = new FormData(form);
    if (form) {
        await fetch(window.location.origin + "/information_system/website/operations/user/edit_user", {
            method: 'POST',
            body: formData
        })
        .then((response) => response.text())
        .then((data) => {
            console.log(data);
            ClosePopup();
            switch (data) {
                case 'error':
                    MessagePopup('Error', 'Something went wrong when trying to edit User Id ' + userId);
                    break;
                case 'cannot perform':
                    MessagePopup('Invalid Access Level', 'You do not have access rights to edit User Id ' + userId);
                    break;
                case 'duplicate username':
                    MessagePopup('Duplicate Username', "The username '" + formData.get('user_username') + "' already exists!");
                    break;
                case 'success':
                    currentlyEditingId = -1;

                    UpdateUsersTable();
                    break;
            }
        });
    }
}

// Add
let addUserForm = document.getElementById('add-user');
let addUserButton = document.getElementById('table-add-user')
let addUserContainer = document.getElementById('add-user-container');

function ShowAddUser() {
    addUserButton.classList.add('hidden');
    addUserContainer.classList.remove('hidden');
    for (let input of addUserForm.getElementsByTagName('input')) {
        input.value = input.dataset.defaultValue;
    }

    for (let select of addUserForm.getElementsByTagName('select')) {
        select.value = select.dataset.defaultValue;
    }

    for (let textArea of addUserForm.getElementsByTagName('textarea')) {
        textArea.value = textArea.dataset.defaultValue;
    }
}

function HideAddUser() {
    addUserButton.classList.remove('hidden');
    addUserContainer.classList.add('hidden');
}

// Perform edit
async function Post_AddUser() {
    let form = document.getElementById('add-user');
    let formData = new FormData(form);
    if (form) {
        if (form.reportValidity()) {
            await fetch(window.location.origin + "/information_system/website/operations/user/add_user", {
                method: 'POST',
                body: formData
            })
            .then((response) => response.text())
            .then((data) => {
                ClosePopup();
                switch (data) {
                    case 'error':
                        MessagePopup('Error', 'Something went wrong when trying to add an user.');
                        break;
                    case 'cannot perform':
                        MessagePopup('Invalid Access Level', 'You do not have access rights to add an user.');
                        break;
                    case 'duplicate username':
                        MessagePopup('Duplicate Username', "The username '" + formData.get('user_username') + "' already exists!");
                        break;
                    case 'success':
                        MessagePopup('Success', 'The item has been added!');
                        HideAddUser();
                        UpdateUsersTable();
                        break;
                }
            });
        }
    }
}