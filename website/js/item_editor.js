// Deleting
async function Post_DeleteItem(itemId) {
    let formData = new FormData();
    formData.append('item_id', itemId);

    if (formData) {
        await fetch(window.location.origin + "/information_system/website/operations/item/delete_item", {
            method: 'POST',
            body: formData
        })
        .then((response) => response.text())
        .then((data) => {
            ClosePopup();
            switch (data) {
                case 'error':
                    MessagePopup('Error', 'Something went wrong when trying to delete Item Id ' + itemId);
                    break;
                case 'cannot perform':
                    MessagePopup('Invalid Access Level', 'You do not have access rights to edit Item Id ' + itemId);
                    break;
                case 'success':
                    UpdateItemsTable();
                    break;
            }
        });
    }
}

// Editing
let currentlyEditingId = -1; // Item ID of current row being edited - only one row can be edited at a time
let defaultEditingValues = {};
function EditItem(itemId, defaultValues) {
    if (currentlyEditingId != -1) {
        ConfirmationPopup(
            'Unsaved Changes',
            'This will revert any changes being made to Item Id ' + currentlyEditingId + '.', 'cancel_edit', {'itemId': currentlyEditingId, 'tryingToEdit_ItemId': itemId});
        return;
    }

    currentlyEditingId = itemId;
    defaultEditingValues = defaultValues;

    let itemRow = document.getElementById('item_row_' + itemId);
    let row_ItemDisplays = itemRow.getElementsByClassName('item-display');
    let row_EditInputs = itemRow.getElementsByClassName('edit-input');

    // Hide value displays
    for (row of row_ItemDisplays) {
        row.classList.add('hidden');
    }

    // Show edit inputs
    for (row of row_EditInputs) {
        row.classList.remove('hidden');
    }
}

function CancelItemEdit(itemId) {
    let itemRow = document.getElementById('item_row_' + itemId);
    let row_ItemDisplays = itemRow.getElementsByClassName('item-display');
    let row_EditInputs = itemRow.getElementsByClassName('edit-input');

    // Show value displays
    for (row of row_ItemDisplays) {
        row.classList.remove('hidden');
    }


    // Hide edit inputs
    for (row of row_EditInputs) {
        row.classList.add('hidden');

        // Reset values
        if (defaultEditingValues) {
            if (row.classList.contains('item-name')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['name'];
            }
            else if (row.classList.contains('rarity')) {
                row.getElementsByTagName('select')[0].value = defaultEditingValues['rarity'];
            }
            else if (row.classList.contains('item-sell-value')) {
                row.getElementsByTagName('input')[0].value = defaultEditingValues['sell_value'];
            }
        }
    }
}

// Perform edit
async function Post_EditItem(itemId) {
    let form = document.getElementById('edit_item_' + itemId);
    if (form) {
        await fetch(window.location.origin + "/information_system/website/operations/item/edit_item", {
            method: 'POST',
            body: new FormData(form)
        })
        .then((response) => response.text())
        .then((data) => {
            ClosePopup();
            switch (data) {
                case 'error':
                    MessagePopup('Error', 'Something went wrong when trying to edit Item Id ' + itemId);
                    break;
                case 'cannot perform':
                    MessagePopup('Invalid Access Level', 'You do not have access rights to edit Item Id ' + itemId);
                    break;
                case 'success':
                    currentlyEditingId = -1;

                    UpdateItemsTable();
                    break;
            }
        });
    }
}

// Add
let addItemForm = document.getElementById('add-item');
let addItemButton = document.getElementById('table-add-item')
let addItemContainer = document.getElementById('add-item-container');

function ShowAddItem() {
    addItemButton.classList.add('hidden');
    addItemContainer.classList.remove('hidden');
    for (let input of addItemForm.getElementsByTagName('input')) {
        input.value = input.dataset.defaultValue;
    }

    for (let select of addItemForm.getElementsByTagName('select')) {
        select.value = select.dataset.defaultValue;
    }

    for (let textArea of addItemForm.getElementsByTagName('textarea')) {
        textArea.value = textArea.dataset.defaultValue;
    }
}

function HideAddItem() {
    addItemButton.classList.remove('hidden');
    addItemContainer.classList.add('hidden');
}