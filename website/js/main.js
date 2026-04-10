// Prevent resubmission page
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}

//// Popup
let popupTemplate =
`<div class="confirmation-popup" id="popup">
    <div class="popup-box frutiger-glossy">
        <p class="question">Are you sure?</p>
        <p class="message">This will delete item ID 0 (item name) permanently.</p>
        <div class="buttons">
            <button class="frutiger-tile continue">Continue</button>
            <button class="frutiger-tile cancel" onclick="ClosePopup()">Cancel</button>
        </div>
    </div>
</div>`;

function ConfirmationPopup(question, message, action, actionValues) {
    document.getElementById('popup').classList.remove('hidden');

    document.getElementById('popup').getElementsByClassName('question')[0].innerHTML = question;
    document.getElementById('popup').getElementsByClassName('message')[0].innerHTML = message;

    let continueButton = document.getElementById('popup').getElementsByClassName('continue')[0];

    let cancelButton = document.getElementById('popup').getElementsByClassName('cancel')[0];
    cancelButton.style = "block";

    if (action == 'delete_item' && actionValues['itemId']) {
        function _() {
            document.getElementById('delete_item_' + actionValues['itemId']).submit();
        }
        continueButton.addEventListener('click', _);

        cancelButton.addEventListener('click', function() {
            ClosePopup();
            continueButton.removeEventListener('click', _);
        });
    }
    else if (action == 'edit_item' && actionValues['itemId']) {
        function _() {
            document.getElementById('edit_item_' + actionValues['itemId']).submit();
        }
        continueButton.addEventListener('click', _);

        cancelButton.addEventListener('click', function() {
            ClosePopup();
            continueButton.removeEventListener('click', _);
        });
    }
    else if (action == 'cancel_edit' && actionValues['itemId']) {
        function _() {
            CancelItemEdit(actionValues['itemId']);
            ClosePopup();
            currentlyEditingId = -1;
            defaultEditingValues = {};

            // If trying to edit an item while another item is currently being edited
            if (actionValues['tryingToEdit_ItemId']) {
                EditItem(actionValues['tryingToEdit_ItemId']);
            }
        }
        continueButton.addEventListener('click', _);

        cancelButton.addEventListener('click', function() {
            ClosePopup();
            continueButton.removeEventListener('click', _);
        });
    }
        
}

function MessagePopup(title, message) {
    document.getElementById('popup').classList.remove('hidden');

    document.getElementById('popup').getElementsByClassName('question')[0].innerHTML = question;
    document.getElementById('popup').getElementsByClassName('message')[0].innerHTML = message;

    let continueButton = document.getElementById('popup').getElementsByClassName('continue')[0];

    let cancelButton = document.getElementById('popup').getElementsByClassName('cancel')[0];
    cancelButton.style = "none";

    continueButton.addEventListener('click', function() {
        ClosePopup();
        continueButton.removeEventListener('click');
    });
}

function ClosePopup() {
    document.getElementById('popup').classList.add('hidden');
}


//// Page styles
var currentPageStyle = localStorage['page-style'] || "green"; // Initial value is 'green' if no value is cached
function UpdatePageStyle() {
    document.body.classList = currentPageStyle;
    document.getElementById('theme').value = currentPageStyle;
}
UpdatePageStyle()

//// Input listener
document.addEventListener('input', function(event)
{
    // Page style changed
    if (event.target.id == 'theme')
    {
        currentPageStyle = event.target.value

        // Cache selected page style
        localStorage['page-style'] = currentPageStyle;

        // Update page with new page style
        UpdatePageStyle()
    }
    else return;
});